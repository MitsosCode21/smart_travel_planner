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

    // --- ΔΙΑΓΡΑΦΗ ΜΗΝΥΜΑΤΟΣ ---
    if (isset($_POST['delete_msg_id'])) {
        $del_id = intval($_POST['delete_msg_id']);
        $del = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
        $del->execute([$del_id]);
        header("Location: messages.php?msg=deleted");
        exit();
    }

    // --- ΑΝΤΛΗΣΗ ΟΛΩΝ ΤΩΝ ΜΗΝΥΜΑΤΩΝ ---
    $stmt = $pdo->query("SELECT * FROM contact_messages ORDER BY submitted_at DESC");
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    $db_error = "Σφάλμα: " . $e->getMessage();
    $messages = [];
}
?>

<!DOCTYPE html>
<html lang="gr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Μηνύματα | Admin Panel</title>
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

        .msg-box { padding: 15px 20px; border-radius: 10px; margin-bottom: 20px; font-weight: 700; background: #fee2e2; color: #991b1b; border: 1px solid #fecaca;}

        /* Table */
        .table-container { background: white; padding: 25px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border-top: 4px solid #8b5cf6;}
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { padding: 12px 15px; border-bottom: 2px solid var(--border); color: var(--text-muted); font-size: 13px; text-transform: uppercase; font-weight: 800;}
        td { padding: 15px; border-bottom: 1px solid var(--border); font-size: 14px; font-weight: 500;}
        tr:last-child td { border-bottom: none;}
        tr:hover td { background: #f8fafc; }
        
        .btn-action { border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 12px; transition: 0.2s; display: inline-flex; align-items: center; gap: 5px;}
        .btn-view { background: #ede9fe; color: #7c3aed; border: 1px solid #ddd6fe; }
        .btn-view:hover { background: #ddd6fe; color: #6d28d9; }
        .btn-delete { background: #fee2e2; color: #ef4444; border: 1px solid #fecaca; margin-left: 5px;}
        .btn-delete:hover { background: #fca5a5; color: #b91c1c; }

        .hidden-row { display: none !important; }

        /* Modal Προβολής */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15,23,42,0.8); backdrop-filter: blur(5px); z-index: 1000; display: flex; align-items: center; justify-content: center; opacity: 0; pointer-events: none; transition: 0.3s; }
        .modal-overlay.active { opacity: 1; pointer-events: auto; }
        .modal-content { background: white; width: 100%; max-width: 500px; border-radius: 20px; padding: 30px; box-shadow: 0 25px 50px rgba(0,0,0,0.2); transform: translateY(-20px); transition: 0.3s;}
        .modal-overlay.active .modal-content { transform: translateY(0); }
        
        .modal-title { font-size: 20px; font-weight: 900; color: #7c3aed; margin-top: 0; margin-bottom: 20px; border-bottom: 2px solid var(--border); padding-bottom: 10px; display: flex; justify-content: space-between; align-items: center;}
        .modal-close { cursor: pointer; color: var(--text-muted); font-size: 20px; background: none; border: none;}
        .modal-close:hover { color: #ef4444; }
        
        .msg-info { font-size: 13px; color: var(--text-muted); margin-bottom: 15px; background: #f8fafc; padding: 10px; border-radius: 8px; border: 1px solid var(--border);}
        .msg-info strong { color: var(--primary); }
        .msg-text { background: #fff; border: 1px solid #e2e8f0; padding: 15px; border-radius: 10px; font-size: 14px; line-height: 1.6; color: #334155; min-height: 100px; white-space: pre-wrap;}
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
            <a href="users.php" class="nav-link">👥 Χρήστες</a>
            <a href="bookings.php" class="nav-link">🏨 Κρατήσεις Ταξιδιών</a>
            <a href="transports.php" class="nav-link">✈️ Εισιτήρια & Μέσα</a>
            <a href="cars.php" class="nav-link">🚗 Ενοικιάσεις Οχημάτων</a>
            <a href="manage_hotels.php" class="nav-link">🏢 Ξενοδοχεία</a>
            <a href="manage_destinations.php" class="nav-link">🏝️ Προορισμοί</a>
            <a href="messages.php" class="nav-link active">✉️ Μηνύματα</a>
        </div>
        <div class="logout-btn">
            <a href="../index.php">← Πίσω στο Site</a>
            <a href="../auth/logout.php" style="margin-top:10px;">Αποσύνδεση</a>
        </div>
    </div>

    <div class="main-content">
        <div class="top-header">
            <div>
                <h1>Εισερχόμενα Μηνύματα</h1>
                <p style="color: var(--text-muted); margin-top: 5px; font-size: 14px;">Συνολικά: <strong><?php echo isset($messages) ? count($messages) : 0; ?></strong> μηνύματα από τη φόρμα επικοινωνίας</p>
            </div>
            
            <div class="search-container">
                <span class="search-icon">🔍</span>
                <input type="text" id="searchInput" class="search-bar" placeholder="Αναζήτηση με Όνομα ή Email..." onkeyup="filterTable()">
            </div>
        </div>

        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
            <div class="msg-box" style="background: #d1fae5; color: #065f46; border-color: #a7f3d0;">✔️ Το μήνυμα διαγράφηκε επιτυχώς!</div>
        <?php elseif(isset($db_error)): ?>
            <div class="msg-box">⚠️ <?php echo $db_error; ?></div>
        <?php endif; ?>

        <div class="table-container">
            <table id="msgTable">
                <thead>
                    <tr>
                        <th>Ημερομηνία</th>
                        <th>Αποστολέας</th>
                        <th>Μήνυμα (Προεπισκόπηση)</th>
                        <th style="text-align: right;">Ενέργειες</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($messages) && count($messages) > 0): ?>
                        <?php foreach ($messages as $m): ?>
                            <tr>
                                <td><?php echo date('d/m/Y H:i', strtotime($m['submitted_at'])); ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($m['full_name']); ?></strong><br>
                                    <span style="color:#64748b; font-size:12px;"><?php echo htmlspecialchars($m['email']); ?></span>
                                </td>
                                <td>
                                    <span style="color: #475569; font-size: 13px;">
                                        <?php 
                                            // Προεπισκόπηση 50 χαρακτήρων
                                            echo htmlspecialchars(mb_substr($m['message'], 0, 50)) . (mb_strlen($m['message']) > 50 ? '...' : ''); 
                                        ?>
                                    </span>
                                </td>
                                <td style="text-align: right; white-space: nowrap;">
                                    
                                    <button type="button" class="btn-action btn-view" onclick="openMsgModal(
                                        '<?php echo addslashes($m['full_name']); ?>', 
                                        '<?php echo addslashes($m['email']); ?>', 
                                        '<?php echo date('d/m/Y H:i', strtotime($m['submitted_at'])); ?>', 
                                        '<?php echo htmlspecialchars(addslashes(str_replace(["\r", "\n"], [" ", "\\n"], $m['message']))); ?>'
                                    )">👁️ Προβολή</button>
                                    
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('⚠️ Είστε σίγουροι ότι θέλετε να διαγράψετε αυτό το μήνυμα;');">
                                        <input type="hidden" name="delete_msg_id" value="<?php echo $m['id']; ?>">
                                        <button type="submit" class="btn-action btn-delete">❌</button>
                                    </form>

                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" style="text-align:center; padding: 40px; color:#64748b;">Δεν υπάρχουν μηνύματα.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal-overlay" id="msgModal">
        <div class="modal-content">
            <h2 class="modal-title">
                ✉️ Προβολή Μηνύματος
                <button class="modal-close" onclick="closeMsgModal()">✖</button>
            </h2>
            
            <div class="msg-info">
                <div><strong>Από:</strong> <span id="modal_sender"></span></div>
                <div style="margin-top: 5px;"><strong>Email:</strong> <span id="modal_email"></span></div>
                <div style="margin-top: 5px;"><strong>Ημερομηνία:</strong> <span id="modal_date"></span></div>
            </div>

            <div class="msg-text" id="modal_text">
                </div>

            <div style="display: flex; justify-content: space-between; margin-top: 20px; align-items: center;">
                <a href="#" id="replyBtn" class="btn-action" style="background: var(--secondary); color: white; text-decoration: none; font-size: 14px; padding: 8px 16px; transition: 0.2s;">✉️ Απάντηση μέσω Email</a>
                
                <button class="btn-action" style="background: #f1f5f9; color: var(--text-muted); font-size: 14px; padding: 8px 16px;" onclick="closeMsgModal()">Κλείσιμο</button>
            </div>
        </div>
    </div>

    <script>
        // Live Αναζήτηση
        function filterTable() {
            let input = document.getElementById("searchInput");
            let filter = input.value.toLowerCase();
            let table = document.getElementById("msgTable");
            let tr = table.getElementsByTagName("tr");

            for (let i = 1; i < tr.length; i++) {
                let tdName = tr[i].getElementsByTagName("td")[1];
                let tdMsg = tr[i].getElementsByTagName("td")[2];
                
                if (tdName || tdMsg) {
                    let txt1 = tdName.textContent || tdName.innerText;
                    let txt2 = tdMsg.textContent || tdMsg.innerText;
                    
                    if (txt1.toLowerCase().indexOf(filter) > -1 || txt2.toLowerCase().indexOf(filter) > -1) {
                        tr[i].classList.remove('hidden-row');
                    } else {
                        tr[i].classList.add('hidden-row');
                    }
                }       
            }
        }

        // Modal Προβολής
        const modal = document.getElementById('msgModal');
        
        function openMsgModal(name, email, date, msg) {
            document.getElementById('modal_sender').innerText = name;
            document.getElementById('modal_email').innerText = email;
            document.getElementById('modal_date').innerText = date;
            document.getElementById('modal_text').innerText = msg;
            
            // Δυναμική δημιουργία του Mailto link
            let mailtoLink = "mailto:" + email + "?subject=Απάντηση από Smart Travel Planner";
            document.getElementById('replyBtn').href = mailtoLink;
            
            modal.classList.add('active');
        }

        function closeMsgModal() {
            modal.classList.remove('active');
        }
    </script>
</body>
</html>