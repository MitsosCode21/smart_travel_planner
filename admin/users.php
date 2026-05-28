<?php
session_start();

// --- ΦΡΟΥΡΟΣ ΑΣΦΑΛΕΙΑΣ ---
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$host = 'localhost'; $db = 'smart_travel_planner'; $user = 'root'; $pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // --- ΕΠΕΞΕΡΓΑΣΙΑ ΧΡΗΣΤΗ (EDIT) ---
    if (isset($_POST['edit_user'])) {
        $e_id = intval($_POST['user_id']);
        $e_name = trim($_POST['fullname']);
        $e_email = trim($_POST['email']);
        $e_role = trim($_POST['role']);

        // Προστασία: Αποτροπή του Admin από το να κάνει "υποβιβασμό" (demote) στον εαυτό του κατά λάθος!
        if ($e_id === $_SESSION['user_id'] && $e_role !== 'admin') {
            header("Location: users.php?msg=error_role");
            exit();
        } else {
            $upd = $pdo->prepare("UPDATE users SET fullname = ?, email = ?, role = ? WHERE id = ?");
            $upd->execute([$e_name, $e_email, $e_role, $e_id]);
            header("Location: users.php?msg=updated");
            exit();
        }
    }

    // --- ΔΙΑΓΡΑΦΗ ΧΡΗΣΤΗ ---
    if (isset($_POST['delete_user_id'])) {
        $del_id = intval($_POST['delete_user_id']);
        
        // Προστασία: Μην αφήσεις τον Admin να διαγράψει τον εαυτό του!
        if ($del_id !== $_SESSION['user_id']) {
            // 1. Πρώτα διαγράφουμε τις κρατήσεις του χρήστη
            $del_res = $pdo->prepare("DELETE FROM reservations WHERE user_id = ?");
            $del_res->execute([$del_id]);
            
            // 2. Μετά διαγράφουμε τον ίδιο τον χρήστη
            $del_usr = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $del_usr->execute([$del_id]);
            
            header("Location: users.php?msg=deleted");
            exit();
        } else {
            header("Location: users.php?msg=error_self");
            exit();
        }
    }

    // --- ΑΝΤΛΗΣΗ ΟΛΩΝ ΤΩΝ ΧΡΗΣΤΩΝ ---
    $stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC");
    $all_users = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    die("Σφάλμα σύνδεσης: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="gr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Χρήστες | Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #0f172a; --secondary: #3b82f6; --accent: #0ea5e9; --bg: #f1f5f9; --text: #334155; --border: #e2e8f0; }
        body { margin: 0; font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); display: flex; min-height: 100vh;}
        
        /* Sidebar */
        .sidebar { width: 260px; background: var(--primary); color: white; display: flex; flex-direction: column; padding: 20px 0; flex-shrink: 0;}
        .sidebar-brand { padding: 0 20px 20px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 20px; }
        .sidebar-brand h2 { margin: 0; font-size: 18px; font-weight: 900; color: white; line-height: 1.2; letter-spacing: -0.5px;} 
        .sidebar-brand span { color: var(--accent); font-size: 13px; text-transform: uppercase; letter-spacing: 1px;}
        
        .nav-links { display: flex; flex-direction: column; gap: 5px; padding: 0 15px;}
        .nav-link { padding: 12px 15px; color: #cbd5e1; text-decoration: none; border-radius: 10px; font-weight: 600; transition: 0.3s; display: flex; align-items: center; gap: 10px; }
        .nav-link:hover, .nav-link.active { background: var(--secondary); color: white; }
        
        .logout-btn { margin-top: auto; margin-bottom: 20px; padding: 0 15px;}
        .logout-btn a { display: block; padding: 12px; background: rgba(239, 68, 68, 0.15); color: #fca5a5; text-align: center; border-radius: 10px; text-decoration: none; font-weight: 700; transition: 0.3s;}
        .logout-btn a:hover { background: #ef4444; color: white; }

        /* Main Content */
        .main-content { flex: 1; padding: 40px; overflow-y: auto;}
        .top-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;}
        .top-header h1 { margin: 0; font-size: 28px; font-weight: 900; color: var(--primary);}
        
        .search-container { position: relative; }
        .search-bar { padding: 12px 15px 12px 35px; border-radius: 10px; border: 1px solid var(--border); outline: none; width: 300px; font-family: inherit; font-size: 14px; box-shadow: 0 4px 6px rgba(0,0,0,0.02);}
        .search-bar:focus { border-color: var(--secondary); box-shadow: 0 0 0 3px rgba(59,130,246,0.1);}
        .search-icon { position: absolute; left: 12px; top: 12px; color: #94a3b8; }

        .msg-box { padding: 15px 20px; border-radius: 10px; margin-bottom: 20px; font-weight: 700; }
        .msg-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .msg-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

        /* Table */
        .table-container { background: white; padding: 25px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.05);}
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { padding: 12px 15px; border-bottom: 2px solid var(--border); color: var(--text-muted); font-size: 13px; text-transform: uppercase; font-weight: 800;}
        td { padding: 15px; border-bottom: 1px solid var(--border); font-size: 14px; font-weight: 500; vertical-align: middle;}
        tr:last-child td { border-bottom: none;}
        tr:hover td { background: #f8fafc; }
        
        .badge { padding: 5px 10px; border-radius: 6px; font-size: 12px; font-weight: 800;}
        .badge-admin { background: #fee2e2; color: #ef4444; }
        .badge-user { background: #e0f2fe; color: #0284c7; }
        
        .btn-action { border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 12px; transition: 0.2s; display: inline-flex; align-items: center; gap: 5px;}
        .btn-edit { background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd; }
        .btn-edit:hover { background: #bae6fd; color: #0369a1; }
        .btn-delete { background: #fee2e2; color: #ef4444; border: 1px solid #fecaca; margin-left: 5px;}
        .btn-delete:hover { background: #fca5a5; color: #b91c1c; }
        .btn-disabled { background: #f1f5f9; color: #94a3b8; cursor: not-allowed; border: 1px solid #e2e8f0; margin-left: 5px;}

        .hidden-row { display: none !important; }

        /* Modal Styling */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15,23,42,0.8); backdrop-filter: blur(5px); z-index: 1000; display: flex; align-items: center; justify-content: center; opacity: 0; pointer-events: none; transition: 0.3s; }
        .modal-overlay.active { opacity: 1; pointer-events: auto; }
        .modal-content { background: white; width: 100%; max-width: 450px; border-radius: 20px; padding: 30px; box-shadow: 0 25px 50px rgba(0,0,0,0.2); transform: translateY(-20px); transition: 0.3s; }
        .modal-overlay.active .modal-content { transform: translateY(0); }
        
        .modal-title { font-size: 20px; font-weight: 900; color: var(--primary); margin-top: 0; margin-bottom: 20px; border-bottom: 2px solid var(--border); padding-bottom: 10px;}
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-size: 12px; font-weight: 800; color: var(--text-muted); margin-bottom: 5px; text-transform: uppercase;}
        .form-group input, .form-group select { width: 100%; padding: 12px 15px; border-radius: 10px; border: 1px solid var(--border); font-family: inherit; font-size: 14px; box-sizing: border-box; outline: none; transition: 0.2s;}
        .form-group input:focus, .form-group select:focus { border-color: var(--secondary); box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
        
        .modal-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 25px;}
        .btn-save { background: var(--secondary); color: white; border: none; padding: 10px 20px; border-radius: 10px; font-weight: bold; cursor: pointer; transition: 0.2s;}
        .btn-save:hover { background: #2563eb; }
        .btn-cancel { background: #f1f5f9; color: var(--text-muted); border: none; padding: 10px 20px; border-radius: 10px; font-weight: bold; cursor: pointer; transition: 0.2s;}
        .btn-cancel:hover { background: #e2e8f0; color: var(--text); }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-brand">
            <h2>Smart Travel Planner</h2>
            <span>Admin Panel</span>
        </div>
        <div class="nav-links">
            <a href="dashboard.php" class="nav-link">📊 Dashboard</a>
            <a href="users.php" class="nav-link active">👥 Χρήστες</a>
            <a href="bookings.php" class="nav-link">🏨 Κρατήσεις Ταξιδιών</a>
            <a href="transports.php" class="nav-link">✈️ Εισιτήρια & Μέσα</a>
            <a href="cars.php" class="nav-link">🚗 Ενοικιάσεις Οχημάτων</a>
            <a href="manage_hotels.php" class="nav-link">🏢 Ξενοδοχεία</a>
            <a href="manage_destinations.php" class="nav-link">🏝️ Προορισμοί</a>
            <a href="messages.php" class="nav-link">✉️ Μηνύματα</a> </div>
        <div class="logout-btn">
            <a href="../index.php">← Πίσω στο Site</a>
            <a href="../auth/logout.php" style="margin-top:10px;">Αποσύνδεση</a>
        </div>
    </div>

    <div class="main-content">
        <div class="top-header">
            <div>
                <h1>Διαχείριση Χρηστών</h1>
                <p style="color: var(--text-muted); margin-top: 5px; font-size: 14px;">Εγγεγραμμένα Μέλη: <strong><?php echo count($all_users); ?></strong></p>
            </div>
            
            <div class="search-container">
                <span class="search-icon">🔍</span>
                <input type="text" id="searchInput" class="search-bar" placeholder="Αναζήτηση με Όνομα ή Email..." onkeyup="filterTable()">
            </div>
        </div>

        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'updated'): ?>
            <div class="msg-box msg-success">✔️ Τα στοιχεία του χρήστη ενημερώθηκαν επιτυχώς!</div>
        <?php elseif(isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
            <div class="msg-box msg-success">✔️ Ο χρήστης και οι κρατήσεις του διαγράφηκαν επιτυχώς!</div>
        <?php elseif(isset($_GET['msg']) && $_GET['msg'] == 'error_self'): ?>
            <div class="msg-box msg-error">⚠️ Δεν μπορείτε να διαγράψετε τον δικό σας λογαριασμό!</div>
        <?php elseif(isset($_GET['msg']) && $_GET['msg'] == 'error_role'): ?>
            <div class="msg-box msg-error">⚠️ Σφάλμα: Δεν μπορείτε να αφαιρέσετε τα δικαιώματα Admin από τον εαυτό σας!</div>
        <?php endif; ?>

        <div class="table-container">
            <table id="usersTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ονοματεπώνυμο</th>
                        <th>Email</th>
                        <th>Ρόλος</th>
                        <th style="text-align: right;">Ενέργειες</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($all_users) > 0): ?>
                        <?php foreach ($all_users as $usr): ?>
                            <tr>
                                <td>#<?php echo $usr['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($usr['fullname']); ?></strong></td>
                                <td><?php echo htmlspecialchars($usr['email']); ?></td>
                                <td>
                                    <?php if ($usr['role'] == 'admin'): ?>
                                        <span class="badge badge-admin">Admin</span>
                                    <?php else: ?>
                                        <span class="badge badge-user">Πελάτης</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: right; white-space: nowrap;">
                                    
                                    <button type="button" class="btn-action btn-edit" onclick="openEditModal(
                                        '<?php echo $usr['id']; ?>', 
                                        '<?php echo addslashes($usr['fullname']); ?>', 
                                        '<?php echo addslashes($usr['email']); ?>', 
                                        '<?php echo $usr['role']; ?>'
                                    )">✏️ Επεξεργασία</button>

                                    <?php if ($usr['id'] == $_SESSION['user_id']): ?>
                                        <button class="btn-action btn-disabled" disabled>❌</button>
                                    <?php else: ?>
                                        <form method="POST" style="display:inline;" onsubmit="return confirm('⚠️ Είστε σίγουροι ότι θέλετε να διαγράψετε τον χρήστη <?php echo addslashes($usr['fullname']); ?>; Θα διαγραφούν και ΟΛΕΣ οι κρατήσεις του!');">
                                            <input type="hidden" name="delete_user_id" value="<?php echo $usr['id']; ?>">
                                            <button type="submit" class="btn-action btn-delete">❌</button>
                                        </form>
                                    <?php endif; ?>

                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="text-align:center; padding: 30px; color:#64748b;">Δεν υπάρχουν χρήστες.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal-overlay" id="editModal">
        <div class="modal-content">
            <h2 class="modal-title">✏️ Επεξεργασία Χρήστη</h2>
            <form method="POST" action="users.php">
                <input type="hidden" name="user_id" id="modal_user_id">
                
                <div class="form-group">
                    <label>Ονοματεπώνυμο</label>
                    <input type="text" name="fullname" id="modal_fullname" required>
                </div>
                
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" id="modal_email" required>
                </div>
                
                <div class="form-group">
                    <label>Ρόλος Συστήματος</label>
                    <select name="role" id="modal_role" required>
                        <option value="user">Πελάτης (User)</option>
                        <option value="admin">Διαχειριστής (Admin)</option>
                    </select>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeEditModal()">Ακύρωση</button>
                    <button type="submit" name="edit_user" class="btn-save">Αποθήκευση</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Live Αναζήτηση Χρηστών
        function filterTable() {
            let input = document.getElementById("searchInput");
            let filter = input.value.toLowerCase();
            let table = document.getElementById("usersTable");
            let tr = table.getElementsByTagName("tr");

            for (let i = 1; i < tr.length; i++) { 
                let tdName = tr[i].getElementsByTagName("td")[1];
                let tdEmail = tr[i].getElementsByTagName("td")[2];
                
                if (tdName || tdEmail) {
                    let txtValue1 = tdName.textContent || tdName.innerText;
                    let txtValue2 = tdEmail.textContent || tdEmail.innerText;
                    
                    if (txtValue1.toLowerCase().indexOf(filter) > -1 || txtValue2.toLowerCase().indexOf(filter) > -1) {
                        tr[i].classList.remove('hidden-row');
                    } else {
                        tr[i].classList.add('hidden-row');
                    }
                }       
            }
        }

        // Λειτουργίες Modal (Άνοιγμα και Γέμισμα Δεδομένων)
        const modal = document.getElementById('editModal');
        
        function openEditModal(id, name, email, role) {
            document.getElementById('modal_user_id').value = id;
            document.getElementById('modal_fullname').value = name;
            document.getElementById('modal_email').value = email;
            document.getElementById('modal_role').value = role;
            
            modal.classList.add('active');
        }

        function closeEditModal() {
            modal.classList.remove('active');
        }
    </script>
</body>
</html>