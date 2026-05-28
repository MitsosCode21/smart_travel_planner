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

    // --- 1. ΠΡΟΣΘΗΚΗ (ADD) ΝΕΟΥ ΠΡΟΟΡΙΣΜΟΥ ---
    if (isset($_POST['add_dest'])) {
        $name_gr = trim($_POST['name_gr']);
        $name_en = trim($_POST['name_en']);
        $vacation_type = trim($_POST['vacation_type']);
        $landscape = trim($_POST['landscape']);
        $best_season_gr = trim($_POST['best_season_gr']);
        $best_season_en = trim($_POST['best_season_en']);
        $cost_per_day = floatval($_POST['cost_per_day']);
        $description_gr = trim($_POST['description_gr']);
        $description_en = trim($_POST['description_en']);
        $image_url = trim($_POST['image_url']);
        
        $guide_gr = trim($_POST['guide_gr']);
        $guide_en = trim($_POST['guide_en']);
        
        // --- ΤΟ ΕΞΥΠΝΟ TRICK: Κρύβουμε το μεταφορικό μέσο μέσα στον οδηγό ---
        $transport_mode = trim($_POST['transport_mode']);
        if (!empty($transport_mode)) {
            $guide_gr = "\n" . $guide_gr;
        }

        $ins = $pdo->prepare("INSERT INTO destinations (name_gr, name_en, vacation_type, landscape, best_season_gr, best_season_en, cost_per_day, description_gr, description_en, guide_gr, guide_en, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $ins->execute([$name_gr, $name_en, $vacation_type, $landscape, $best_season_gr, $best_season_en, $cost_per_day, $description_gr, $description_en, $guide_gr, $guide_en, $image_url]);
        
        header("Location: manage_destinations.php?msg=added");
        exit();
    }

    // --- 2. ΕΠΕΞΕΡΓΑΣΙΑ (EDIT) ΠΡΟΟΡΙΣΜΟΥ ---
    if (isset($_POST['edit_dest'])) {
        $d_id = intval($_POST['dest_id']);
        $name_gr = trim($_POST['name_gr']);
        $name_en = trim($_POST['name_en']);
        $vacation_type = trim($_POST['vacation_type']);
        $landscape = trim($_POST['landscape']);
        $best_season_gr = trim($_POST['best_season_gr']);
        $best_season_en = trim($_POST['best_season_en']);
        $cost_per_day = floatval($_POST['cost_per_day']);
        $description_gr = trim($_POST['description_gr']);
        $description_en = trim($_POST['description_en']);
        $image_url = trim($_POST['image_url']);
        
        $guide_gr = trim($_POST['guide_gr']);
        $guide_en = trim($_POST['guide_en']);
        
        // --- ΤΟ ΕΞΥΠΝΟ TRICK ΣΤΟ EDIT ---
        $transport_mode = trim($_POST['transport_mode']);
        if (!empty($transport_mode)) {
            $guide_gr = "\n" . $guide_gr;
        }

        $upd = $pdo->prepare("UPDATE destinations SET name_gr=?, name_en=?, vacation_type=?, landscape=?, best_season_gr=?, best_season_en=?, cost_per_day=?, description_gr=?, description_en=?, guide_gr=?, guide_en=?, image_url=? WHERE id=?");
        $upd->execute([$name_gr, $name_en, $vacation_type, $landscape, $best_season_gr, $best_season_en, $cost_per_day, $description_gr, $description_en, $guide_gr, $guide_en, $image_url, $d_id]);
        
        header("Location: manage_destinations.php?msg=updated");
        exit();
    }

    // --- 3. ΔΙΑΓΡΑΦΗ (DELETE) ΠΡΟΟΡΙΣΜΟΥ ---
    if (isset($_POST['delete_dest_id'])) {
        $del_id = intval($_POST['delete_dest_id']);
        
        $del_hotels = $pdo->prepare("DELETE FROM hotels WHERE destination_id = ?");
        $del_hotels->execute([$del_id]);

        $del = $pdo->prepare("DELETE FROM destinations WHERE id = ?");
        $del->execute([$del_id]);
        
        header("Location: manage_destinations.php?msg=deleted");
        exit();
    }

    // --- ΑΝΤΛΗΣΗ ΟΛΩΝ ΤΩΝ ΠΡΟΟΡΙΣΜΩΝ ---
    $stmt = $pdo->query("SELECT * FROM destinations ORDER BY id DESC");
    $destinations = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    die("Σφάλμα σύνδεσης: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="gr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Διαχείριση Προορισμών | Admin Panel</title>
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
        
        .search-container { position: relative; display: flex; gap: 15px;}
        .search-bar { padding: 12px 15px 12px 35px; border-radius: 10px; border: 1px solid var(--border); outline: none; width: 250px; font-family: inherit; font-size: 14px;}
        .search-bar:focus { border-color: var(--secondary); box-shadow: 0 0 0 3px rgba(59,130,246,0.1);}
        .search-icon { position: absolute; left: 12px; top: 12px; color: #94a3b8; }
        
        .btn-add { background: #10b981; color: white; border: none; padding: 12px 20px; border-radius: 10px; font-weight: 800; cursor: pointer; transition: 0.2s; display: flex; align-items: center; gap: 8px;}
        .btn-add:hover { background: #059669; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(16,185,129,0.3);}

        .msg-box { padding: 15px 20px; border-radius: 10px; margin-bottom: 20px; font-weight: 700; background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .msg-deleted { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

        /* Table */
        .table-container { background: white; padding: 25px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border-top: 4px solid #10b981;}
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { padding: 12px 15px; border-bottom: 2px solid var(--border); color: var(--text-muted); font-size: 13px; text-transform: uppercase; font-weight: 800;}
        td { padding: 15px; border-bottom: 1px solid var(--border); font-size: 14px; font-weight: 500; vertical-align: middle;}
        tr:hover td { background: #f8fafc; }
        
        .dest-img { width: 60px; height: 40px; border-radius: 8px; object-fit: cover; }
        
        .btn-action { border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 12px; transition: 0.2s; display: inline-flex; align-items: center; gap: 5px;}
        .btn-edit { background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd; }
        .btn-edit:hover { background: #bae6fd; color: #0369a1; }
        .btn-delete { background: #fee2e2; color: #ef4444; border: 1px solid #fecaca; margin-left: 5px;}
        .btn-delete:hover { background: #fca5a5; color: #b91c1c; }

        .hidden-row { display: none !important; }

        /* Modal Styling */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15,23,42,0.8); backdrop-filter: blur(5px); z-index: 1000; display: flex; align-items: center; justify-content: center; opacity: 0; pointer-events: none; transition: 0.3s; padding: 20px;}
        .modal-overlay.active { opacity: 1; pointer-events: auto; }
        .modal-content { background: white; width: 100%; max-width: 800px; border-radius: 20px; padding: 30px; box-shadow: 0 25px 50px rgba(0,0,0,0.2); transform: translateY(-20px); transition: 0.3s; max-height: 90vh; overflow-y: auto;}
        .modal-overlay.active .modal-content { transform: translateY(0); }
        
        .modal-title { font-size: 22px; font-weight: 900; color: var(--primary); margin-top: 0; margin-bottom: 20px; border-bottom: 2px solid var(--border); padding-bottom: 10px;}
        
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px;}
        .form-group { margin-bottom: 10px; }
        .form-group.full-width { grid-column: 1 / -1; }
        .form-group label { display: block; font-size: 12px; font-weight: 800; color: var(--text-muted); margin-bottom: 5px; text-transform: uppercase;}
        .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 10px 15px; border-radius: 10px; border: 1px solid var(--border); font-family: inherit; font-size: 14px; box-sizing: border-box; outline: none; transition: 0.2s;}
        .form-group input:focus, .form-group textarea:focus, .form-group select:focus { border-color: var(--secondary); box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
        .form-group textarea { resize: vertical; min-height: 60px; }
        
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
            <a href="users.php" class="nav-link">👥 Χρήστες</a>
            <a href="bookings.php" class="nav-link">🏨 Κρατήσεις Ταξιδιών</a>
            <a href="transports.php" class="nav-link">✈️ Εισιτήρια & Μέσα</a>
            <a href="cars.php" class="nav-link">🚗 Ενοικιάσεις Οχημάτων</a>
            <a href="manage_hotels.php" class="nav-link">🏢 Ξενοδοχεία</a>
            <a href="manage_destinations.php" class="nav-link active">🏝️ Προορισμοί</a>
            <a href="messages.php" class="nav-link">✉️ Μηνύματα</a>
        </div>
        <div class="logout-btn">
            <a href="../index.php">← Πίσω στο Site</a>
            <a href="../auth/logout.php" style="margin-top:10px;">Αποσύνδεση</a>
        </div>
    </div>

    <div class="main-content">
        <div class="top-header">
            <div>
                <h1>Διαχείριση Προορισμών</h1>
                <p style="color: var(--text-muted); margin-top: 5px; font-size: 14px;">Σύνολο: <strong><?php echo count($destinations); ?></strong> προορισμοί διαθέσιμοι.</p>
            </div>
            
            <div class="search-container">
                <div style="position: relative;">
                    <span class="search-icon">🔍</span>
                    <input type="text" id="searchInput" class="search-bar" placeholder="Αναζήτηση Προορισμού..." onkeyup="filterTable()">
                </div>
                <button class="btn-add" onclick="openAddModal()">➕ Νέος Προορισμός</button>
            </div>
        </div>

        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'added'): ?>
            <div class="msg-box">✔️ Ο νέος προορισμός προστέθηκε επιτυχώς!</div>
        <?php elseif(isset($_GET['msg']) && $_GET['msg'] == 'updated'): ?>
            <div class="msg-box">✔️ Οι πληροφορίες του προορισμού ενημερώθηκαν!</div>
        <?php elseif(isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
            <div class="msg-box msg-deleted">✔️ Ο προορισμός (και τα ξενοδοχεία του) διαγράφηκαν οριστικά.</div>
        <?php endif; ?>

        <div class="table-container">
            <table id="destTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Εικόνα</th>
                        <th>Όνομα (GR)</th>
                        <th>Τύπος / Τοπίο</th>
                        <th>Μέσο</th>
                        <th style="text-align: right;">Ενέργειες</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($destinations) > 0): ?>
                        <?php foreach ($destinations as $d): 
                            
                            // ΑΣΦΑΛΗΣ ΕΞΑΓΩΓΗ ΤΟΥ ΜΕΣΟΥ ΜΕΤΑΦΟΡΑΣ
                            $transport_mode = "Μη ορισμένο";
                            $pure_guide_gr = $d['guide_gr']; 
                            
                            if (preg_match('/\/u(.+)/', $pure_guide_gr, $m)) {
                                $transport_mode = trim($m[1]);
                                $pure_guide_gr = trim(preg_replace('/\/u.+/', '', $pure_guide_gr));
                            }
                        ?>
                            <tr>
                                <td style="color: #64748b; font-weight: 600;">#<?php echo $d['id']; ?></td>
                                <td>
                                    <?php if(!empty($d['image_url'])): ?>
                                        <img src="<?php echo htmlspecialchars($d['image_url']); ?>" alt="Dest" class="dest-img">
                                    <?php else: ?>
                                        <span style="font-size: 12px; color: #94a3b8;">Χωρίς εικόνα</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($d['name_gr']); ?></strong><br>
                                    <span style="font-size:12px; color:#64748b;"><?php echo htmlspecialchars($d['name_en'] ?? ''); ?></span>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($d['vacation_type'] ?? '-'); ?><br>
                                    <span style="font-size:12px; color:#64748b;"><?php echo htmlspecialchars($d['landscape'] ?? '-'); ?></span>
                                </td>
                                <td>
                                    <span style="background:#f1f5f9; padding:4px 8px; border-radius:6px; font-size:12px; font-weight:700; color:#0ea5e9;">
                                        <?php echo htmlspecialchars($transport_mode); ?>
                                    </span>
                                </td>
                                <td style="text-align: right; white-space: nowrap;">
                                    
                                    <button type="button" class="btn-action btn-edit" 
                                        data-id="<?php echo $d['id']; ?>"
                                        data-namegr="<?php echo htmlspecialchars($d['name_gr']); ?>"
                                        data-nameen="<?php echo htmlspecialchars($d['name_en'] ?? ''); ?>"
                                        data-vactype="<?php echo htmlspecialchars($d['vacation_type'] ?? ''); ?>"
                                        data-land="<?php echo htmlspecialchars($d['landscape'] ?? ''); ?>"
                                        data-seasongr="<?php echo htmlspecialchars($d['best_season_gr'] ?? ''); ?>"
                                        data-seasonen="<?php echo htmlspecialchars($d['best_season_en'] ?? ''); ?>"
                                        data-cost="<?php echo $d['cost_per_day'] ?? 0; ?>"
                                        data-descgr="<?php echo htmlspecialchars($d['description_gr'] ?? ''); ?>"
                                        data-descen="<?php echo htmlspecialchars($d['description_en'] ?? ''); ?>"
                                        data-guidegr="<?php echo htmlspecialchars($pure_guide_gr); ?>"
                                        data-guideen="<?php echo htmlspecialchars($d['guide_en'] ?? ''); ?>"
                                        data-img="<?php echo htmlspecialchars($d['image_url'] ?? ''); ?>"
                                        data-transport="<?php echo htmlspecialchars($transport_mode === 'Μη ορισμένο' ? '' : $transport_mode); ?>"
                                        onclick="openEditModal(this)">✏️ Επεξεργασία</button>
                                    
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('⚠️ ΠΡΟΣΟΧΗ: Αν διαγράψετε τον προορισμό, θα διαγραφούν ΚΑΙ ΟΛΑ τα ξενοδοχεία του! Είστε σίγουροι;');">
                                        <input type="hidden" name="delete_dest_id" value="<?php echo $d['id']; ?>">
                                        <button type="submit" class="btn-action btn-delete">❌</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" style="text-align:center; padding: 40px; color:#64748b;">Δεν υπάρχουν προορισμοί στη βάση.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal-overlay" id="destModal">
        <div class="modal-content">
            <h2 class="modal-title" id="modalTitle">🏝️ Προσθήκη Προορισμού</h2>
            <form method="POST" action="manage_destinations.php">
                
                <input type="hidden" name="dest_id" id="modal_dest_id">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label>Όνομα (GR)</label>
                        <input type="text" name="name_gr" id="modal_name_gr" placeholder="π.χ. Μύκονος" required>
                    </div>
                    <div class="form-group">
                        <label>Όνομα (EN)</label>
                        <input type="text" name="name_en" id="modal_name_en" placeholder="π.χ. Mykonos" required>
                    </div>

                    <div class="form-group">
                        <label>Τύπος Διακοπών</label>
                        <select name="vacation_type" id="modal_vacation_type" required>
                            <option value="">-- ΕΠΙΛΕΞΤΕ --</option>
                            <option value="Ιστορικός">Ιστορικός</option>
                            <option value="Ρομαντικός">Ρομαντικός</option>
                            <option value="Οικογενειακός">Οικογενειακός</option>
                            <option value="Διασκέδαση">Διασκέδαση</option>
                            <option value="Φύση">Φύση</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Τοπίο</label>
                        <select name="landscape" id="modal_landscape" required>
                            <option value="">-- ΕΠΙΛΕΞΤΕ --</option>
                            <option value="Θάλασσα">Θάλασσα</option>
                            <option value="Βουνό">Βουνό</option>
                            <option value="Πόλη">Πόλη</option>
                        </select>
                    </div>

                    <div class="form-group full-width">
                        <label>Προτεινόμενο Μέσο Μεταφοράς</label>
                        <select name="transport_mode" id="modal_transport_mode" required>
                            <option value="">-- ΕΠΙΛΕΞΤΕ ΜΕΣΟ --</option>
                            <option value="✈️ Αεροπλάνο">✈️ Αεροπλάνο</option>
                            <option value="⛴️ Πλοίο">⛴️ Πλοίο</option>
                            <option value="🚌 Λεωφορείο">🚌 Λεωφορείο / ΚΤΕΛ</option>
                            <option value="🚆 Τρένο">🚆 Τρένο</option>
                            <option value="🚗 Οδικώς (ΙΧ)">🚗 Οδικώς (ΙΧ)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Καλύτερη Εποχή (GR)</label>
                        <input type="text" name="best_season_gr" id="modal_best_season_gr" placeholder="π.χ. Παντός Καιρού">
                    </div>
                    <div class="form-group">
                        <label>Καλύτερη Εποχή (EN)</label>
                        <input type="text" name="best_season_en" id="modal_best_season_en" placeholder="π.χ. All Year">
                    </div>

                    <div class="form-group">
                        <label>Κόστος / Ημέρα (€)</label>
                        <input type="number" step="0.01" name="cost_per_day" id="modal_cost_per_day" required>
                    </div>
                    <div class="form-group">
                        <label>Link Εικόνας (URL)</label>
                        <input type="text" name="image_url" id="modal_image_url">
                    </div>

                    <div class="form-group full-width">
                        <label>Σύντομη Περιγραφή (GR)</label>
                        <textarea name="description_gr" id="modal_description_gr"></textarea>
                    </div>
                    <div class="form-group full-width">
                        <label>Σύντομη Περιγραφή (EN)</label>
                        <textarea name="description_en" id="modal_description_en"></textarea>
                    </div>

                    <div class="form-group full-width">
                        <label>Οδηγός (HTML Guide) (GR)</label>
                        <textarea name="guide_gr" id="modal_guide_gr"></textarea>
                    </div>
                    <div class="form-group full-width">
                        <label>Οδηγός (HTML Guide) (EN)</label>
                        <textarea name="guide_en" id="modal_guide_en"></textarea>
                    </div>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Ακύρωση</button>
                    <button type="submit" id="submitBtn" name="add_dest" class="btn-save">Αποθήκευση</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function filterTable() {
            let input = document.getElementById("searchInput");
            let filter = input.value.toLowerCase();
            let table = document.getElementById("destTable");
            let tr = table.getElementsByTagName("tr");

            for (let i = 1; i < tr.length; i++) { 
                let tdNameGr = tr[i].getElementsByTagName("td")[2];
                if (tdNameGr) {
                    let txt1 = tdNameGr.textContent || tdNameGr.innerText;
                    if (txt1.toLowerCase().indexOf(filter) > -1) {
                        tr[i].classList.remove('hidden-row');
                    } else {
                        tr[i].classList.add('hidden-row');
                    }
                }       
            }
        }

        const modal = document.getElementById('destModal');
        
        function openAddModal() {
            document.getElementById('modalTitle').innerHTML = '🏝️ Προσθήκη Προορισμού';
            document.getElementById('submitBtn').name = 'add_dest';
            document.getElementById('submitBtn').innerText = 'Προσθήκη';
            
            document.getElementById('modal_dest_id').value = '';
            document.getElementById('modal_name_gr').value = '';
            document.getElementById('modal_name_en').value = '';
            document.getElementById('modal_vacation_type').value = ''; 
            document.getElementById('modal_landscape').value = ''; 
            document.getElementById('modal_transport_mode').value = ''; 
            document.getElementById('modal_best_season_gr').value = '';
            document.getElementById('modal_best_season_en').value = '';
            document.getElementById('modal_cost_per_day').value = '';
            document.getElementById('modal_image_url').value = '';
            document.getElementById('modal_description_gr').value = '';
            document.getElementById('modal_description_en').value = '';
            document.getElementById('modal_guide_gr').value = '';
            document.getElementById('modal_guide_en').value = '';
            
            modal.classList.add('active');
        }

        function openEditModal(btn) {
            document.getElementById('modalTitle').innerHTML = '✏️ Επεξεργασία Προορισμού';
            document.getElementById('submitBtn').name = 'edit_dest';
            document.getElementById('submitBtn').innerText = 'Αποθήκευση Αλλαγών';

            document.getElementById('modal_dest_id').value = btn.getAttribute('data-id');
            document.getElementById('modal_name_gr').value = btn.getAttribute('data-namegr');
            document.getElementById('modal_name_en').value = btn.getAttribute('data-nameen');
            document.getElementById('modal_vacation_type').value = btn.getAttribute('data-vactype');
            document.getElementById('modal_landscape').value = btn.getAttribute('data-land');
            document.getElementById('modal_transport_mode').value = btn.getAttribute('data-transport'); 
            document.getElementById('modal_best_season_gr').value = btn.getAttribute('data-seasongr');
            document.getElementById('modal_best_season_en').value = btn.getAttribute('data-seasonen');
            document.getElementById('modal_cost_per_day').value = btn.getAttribute('data-cost');
            document.getElementById('modal_image_url').value = btn.getAttribute('data-img');
            document.getElementById('modal_description_gr').value = btn.getAttribute('data-descgr');
            document.getElementById('modal_description_en').value = btn.getAttribute('data-descen');
            document.getElementById('modal_guide_gr').value = btn.getAttribute('data-guidegr');
            document.getElementById('modal_guide_en').value = btn.getAttribute('data-guideen');
            
            modal.classList.add('active');
        }

        function closeModal() {
            modal.classList.remove('active');
        }
    </script>
</body>
</html>