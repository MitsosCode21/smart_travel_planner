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

    // --- ΕΠΕΞΕΡΓΑΣΙΑ (EDIT) ΕΙΣΙΤΗΡΙΩΝ (Ημερομηνίες & Ώρες) ---
    if (isset($_POST['edit_transport'])) {
        $res_id = intval($_POST['reservation_id']);
        $new_pnr = trim($_POST['pnr']);
        
        $new_date_out = trim($_POST['date_out']);
        $new_time_out = trim($_POST['time_out']);
        
        $new_date_ret = trim($_POST['date_ret']);
        $new_time_ret = trim($_POST['time_ret']);

        // Φέρνουμε τα τωρινά δεδομένα
        $stmt_fetch = $pdo->prepare("SELECT passenger_details FROM reservations WHERE id = ?");
        $stmt_fetch->execute([$res_id]);
        $row = $stmt_fetch->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $pd = $row['passenger_details'];

            // 1. Ενημέρωση PNR
            if (!empty($new_pnr)) {
                $pd = preg_replace('/\(PNR\):\s*([A-Z0-9-]+)/i', "(PNR): $new_pnr", $pd);
            }
            
            // 2. Ενημέρωση Ώρας Αναχώρησης (στο κρυφό tag του e-ticket)
            if (!empty($new_time_out)) {
                if (strpos($pd, '[TIME_OUT:') !== false) {
                    $pd = preg_replace('/\[TIME_OUT:\s*([^\]]+)\]/i', "[TIME_OUT: $new_time_out]", $pd);
                } else {
                    $pd .= "\n[TIME_OUT: $new_time_out]";
                }
            }

            // 3. Ενημέρωση Ώρας Επιστροφής (στο κρυφό tag του e-ticket)
            if (!empty($new_time_ret)) {
                if (strpos($pd, '[TIME_RET:') !== false) {
                    $pd = preg_replace('/\[TIME_RET:\s*([^\]]+)\]/i', "[TIME_RET: $new_time_ret]", $pd);
                } else {
                    $pd .= "\n[TIME_RET: $new_time_ret]";
                }
            }

            // 4. Ενημέρωση των Ημερομηνιών και των Λεπτομερειών στη βάση
            $upd = $pdo->prepare("UPDATE reservations SET check_in = ?, check_out = ?, passenger_details = ? WHERE id = ?");
            $upd->execute([$new_date_out, $new_date_ret, $pd, $res_id]);

            header("Location: transports.php?msg=updated");
            exit();
        }
    }

    // --- ΑΝΤΛΗΣΗ ΟΛΩΝ ΤΩΝ ΚΡΑΤΗΣΕΩΝ ΠΟΥ ΕΧΟΥΝ ΜΕΤΑΦΟΡΙΚΑ ---
    $stmt = $pdo->query("
        SELECT r.*, u.fullname, u.email 
        FROM reservations r 
        JOIN users u ON r.user_id = u.id 
        WHERE r.transport_method NOT LIKE '%χωρίς μεταφορικά%' 
        AND r.transport_method NOT LIKE '%δικό μου όχημα%'
        ORDER BY r.id DESC
    ");
    $transport_bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    die("Σφάλμα σύνδεσης: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="gr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Μεταφορές & Εισιτήρια | Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #0f172a; --secondary: #3b82f6; --accent: #0ea5e9; --bg: #f1f5f9; --text: #334155; --border: #e2e8f0; }
        body { margin: 0; font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); display: flex; min-height: 100vh;}
        
        /* Sidebar */
        .sidebar { width: 260px; background: var(--primary); color: white; display: flex; flex-direction: column; padding: 20px 0; flex-shrink: 0;}
        .sidebar-brand { padding: 0 20px 20px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 20px; }
        .sidebar-brand h2 { margin: 0; font-size: 18px; font-weight: 900; color: white; line-height: 1.2; letter-spacing: -0.5px;} /* Διορθώθηκε ο τίτλος */
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
        .search-bar:focus { border-color: #0ea5e9; box-shadow: 0 0 0 3px rgba(14,165,233,0.1);}
        .search-icon { position: absolute; left: 12px; top: 12px; color: #94a3b8; }

        .msg-box { padding: 15px 20px; border-radius: 10px; margin-bottom: 20px; font-weight: 700; background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0;}

        /* Table */
        .table-container { background: white; padding: 25px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border-top: 4px solid #0ea5e9;}
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { padding: 12px 15px; border-bottom: 2px solid var(--border); color: var(--text-muted); font-size: 13px; text-transform: uppercase; font-weight: 800;}
        td { padding: 15px; border-bottom: 1px solid var(--border); font-size: 14px; font-weight: 500;}
        tr:last-child td { border-bottom: none;}
        tr:hover td { background: #f0f9ff; }
        
        .badge { padding: 5px 10px; border-radius: 6px; font-size: 12px; font-weight: 800;}
        .badge-pnr { background: #e0f2fe; color: #0284c7; border: 1px dashed #0ea5e9; font-family: monospace; font-size: 14px;}
        
        .btn-action { border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 12px; transition: 0.2s; display: inline-flex; align-items: center; gap: 5px;}
        .btn-edit { background: #0f172a; color: white; border: 1px solid #1e293b; }
        .btn-edit:hover { background: #334155; }

        .hidden-row { display: none !important; }

        /* Modal Styling */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15,23,42,0.8); backdrop-filter: blur(5px); z-index: 1000; display: flex; align-items: center; justify-content: center; opacity: 0; pointer-events: none; transition: 0.3s; }
        .modal-overlay.active { opacity: 1; pointer-events: auto; }
        .modal-content { background: white; width: 100%; max-width: 500px; border-radius: 20px; padding: 30px; box-shadow: 0 25px 50px rgba(0,0,0,0.2); transform: translateY(-20px); transition: 0.3s; }
        .modal-overlay.active .modal-content { transform: translateY(0); }
        
        .modal-title { font-size: 20px; font-weight: 900; color: #0ea5e9; margin-top: 0; margin-bottom: 20px; border-bottom: 2px solid var(--border); padding-bottom: 10px;}
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-size: 12px; font-weight: 800; color: var(--text-muted); margin-bottom: 5px; text-transform: uppercase;}
        .form-group input { width: 100%; padding: 12px 15px; border-radius: 10px; border: 1px solid var(--border); font-family: inherit; font-size: 14px; box-sizing: border-box; outline: none; transition: 0.2s;}
        .form-group input:focus { border-color: #0ea5e9; box-shadow: 0 0 0 3px rgba(14,165,233,0.1); }
        
        .modal-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 25px;}
        .btn-save { background: #0ea5e9; color: white; border: none; padding: 10px 20px; border-radius: 10px; font-weight: bold; cursor: pointer; transition: 0.2s;}
        .btn-save:hover { background: #0284c7; }
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
            <a href="transports.php" class="nav-link active">✈️ Εισιτήρια & Μέσα</a>
            <a href="cars.php" class="nav-link">🚗 Ενοικιάσεις Οχημάτων</a>
            <a href="manage_hotels.php" class="nav-link">🏢 Ξενοδοχεία</a>
            <a href="manage_destinations.php" class="nav-link">🏝️ Προορισμοί</a>
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
                <h1>Μεταφορές & Εισιτήρια</h1>
                <p style="color: var(--text-muted); margin-top: 5px; font-size: 14px;">Συνολικά: <strong><?php echo count($transport_bookings); ?></strong> ταξίδια με εισιτήρια</p>
            </div>
            
            <div class="search-container">
                <span class="search-icon">🔍</span>
                <input type="text" id="searchInput" class="search-bar" placeholder="Αναζήτηση με Όνομα, PNR ή Εταιρεία..." onkeyup="filterTable()">
            </div>
        </div>

        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'updated'): ?>
            <div class="msg-box">✔️ Οι Ημερομηνίες και οι πληροφορίες του εισιτηρίου ενημερώθηκαν επιτυχώς!</div>
        <?php endif; ?>

        <div class="table-container">
            <table id="transTable">
                <thead>
                    <tr>
                        <th>Κράτηση</th>
                        <th>Πελάτης</th>
                        <th>Μέσο Μεταφοράς</th>
                        <th>Κωδικός PNR</th>
                        <th>Ημερομηνίες & Ώρες</th>
                        <th style="text-align: right;">Ενέργειες</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($transport_bookings) > 0): ?>
                        <?php foreach ($transport_bookings as $bk): 
                            
                            $method_raw = mb_strtolower($bk['transport_method'], 'UTF-8');
                            $details_raw = $bk['passenger_details'];
                            $pnr = "ΕΚΚΡΕΜΕΙ";
                            $time_out = "--:--";
                            $time_ret = "--:--";

                            // Ανίχνευση Μεταφορέα 
                            $carrier = explode('|', $bk['transport_method'])[0];
                            $carrier = str_replace('Αναχώρηση:', '', $carrier);
                            $carrier = trim($carrier);

                            // Εύρεση του κατάλληλου εικονιδίου 
                            $icon = '⛴️'; 
                            if (strpos($method_raw, 'aegean') !== false || strpos($method_raw, 'sky express') !== false || strpos($method_raw, 'ryanair') !== false || strpos($method_raw, 'πτήση') !== false) {
                                $icon = '✈️';
                            } elseif (strpos($method_raw, 'κτελ') !== false || strpos($method_raw, 'bus') !== false) {
                                $icon = '🚌';
                            }

                            // Ανάγνωση PNR
                            if (preg_match('/\(PNR\):\s*([A-Z0-9-]+)/i', $details_raw, $m)) $pnr = $m[1];
                            
                            // Ανάγνωση Ωρών
                            if (preg_match('/\[TIME_OUT:\s*([^\]]+)\]/i', $details_raw, $m)) $time_out = $m[1];
                            if (preg_match('/\[TIME_RET:\s*([^\]]+)\]/i', $details_raw, $m)) $time_ret = $m[1];
                            if ($time_ret === 'none') $time_ret = "One Way";
                        ?>
                            <tr>
                                <td style="color: #64748b; font-weight: 600;">#<?php echo $bk['id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($bk['fullname']); ?></strong><br>
                                    <span style="color:#64748b; font-size:12px;"><?php echo htmlspecialchars($bk['destination_name']); ?></span>
                                </td>
                                <td>
                                    <span style="font-size: 16px;"><?php echo $icon; ?></span> 
                                    <strong style="color: #0f172a;"><?php echo htmlspecialchars($carrier); ?></strong>
                                </td>
                                <td>
                                    <span class="badge badge-pnr"><?php echo htmlspecialchars($pnr); ?></span>
                                </td>
                                <td>
                                    <span style="color: #10b981; font-weight:700;">🛫 <?php echo date('d/m/Y', strtotime($bk['check_in'])); ?> (<?php echo htmlspecialchars($time_out); ?>)</span><br>
                                    <span style="color: #f59e0b; font-weight:700;">🛬 <?php echo date('d/m/Y', strtotime($bk['check_out'])); ?> (<?php echo htmlspecialchars($time_ret); ?>)</span>
                                </td>
                                <td style="text-align: right;">
                                    <button type="button" class="btn-action btn-edit" onclick="openEditModal(
                                        '<?php echo $bk['id']; ?>', 
                                        '<?php echo addslashes($pnr); ?>', 
                                        '<?php echo $bk['check_in']; ?>',
                                        '<?php echo addslashes($time_out); ?>', 
                                        '<?php echo $bk['check_out']; ?>',
                                        '<?php echo addslashes($time_ret); ?>'
                                    )">✏️ Επεξεργασία</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" style="text-align:center; padding: 40px; color:#64748b;">Δεν υπάρχουν κρατήσεις με μεταφορικά.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal-overlay" id="editModal">
        <div class="modal-content">
            <h2 class="modal-title">✈️ Επεξεργασία Εισιτηρίου</h2>
            <form method="POST" action="transports.php">
                <input type="hidden" name="reservation_id" id="modal_res_id">
                
                <div class="form-group">
                    <label>Κωδικός Κράτησης (PNR)</label>
                    <input type="text" name="pnr" id="modal_pnr" required style="font-family: monospace; font-size: 16px; font-weight: bold; color: #0284c7;">
                </div>
                
                <div style="display: flex; gap: 15px; margin-bottom: 5px;">
                    <div class="form-group" style="flex: 2;">
                        <label>Ημερομηνία Αναχώρησης</label>
                        <input type="date" name="date_out" id="modal_date_out" required>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Ώρα (OUT)</label>
                        <input type="text" name="time_out" id="modal_time_out" placeholder="π.χ. 08:30" required>
                    </div>
                </div>

                <div style="display: flex; gap: 15px;">
                    <div class="form-group" style="flex: 2;">
                        <label>Ημερομηνία Επιστροφής</label>
                        <input type="date" name="date_ret" id="modal_date_ret" required>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Ώρα (RET)</label>
                        <input type="text" name="time_ret" id="modal_time_ret" placeholder="π.χ. 18:45 ή none">
                    </div>
                </div>

                <p style="font-size: 11px; color: #94a3b8; margin-top: 0; margin-bottom: 20px;">* Η αλλαγή των στοιχείων θα ενημερώσει αυτόματα το ψηφιακό e-ticket του πελάτη.</p>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeEditModal()">Ακύρωση</button>
                    <button type="submit" name="edit_transport" class="btn-save">Αποθήκευση</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Live Αναζήτηση
        function filterTable() {
            let input = document.getElementById("searchInput");
            let filter = input.value.toLowerCase();
            let table = document.getElementById("transTable");
            let tr = table.getElementsByTagName("tr");

            for (let i = 1; i < tr.length; i++) { 
                let tdClient = tr[i].getElementsByTagName("td")[1];
                let tdCarrier = tr[i].getElementsByTagName("td")[2];
                let tdPnr = tr[i].getElementsByTagName("td")[3];
                
                if (tdClient || tdCarrier || tdPnr) {
                    let txt1 = tdClient.textContent || tdClient.innerText;
                    let txt2 = tdCarrier.textContent || tdCarrier.innerText;
                    let txt3 = tdPnr.textContent || tdPnr.innerText;
                    
                    if (txt1.toLowerCase().indexOf(filter) > -1 || txt2.toLowerCase().indexOf(filter) > -1 || txt3.toLowerCase().indexOf(filter) > -1) {
                        tr[i].classList.remove('hidden-row');
                    } else {
                        tr[i].classList.add('hidden-row');
                    }
                }       
            }
        }

        // Λειτουργίες Modal Επεξεργασίας με Ημερομηνίες!
        const modal = document.getElementById('editModal');
        
        function openEditModal(id, pnr, date_out, time_out, date_ret, time_ret) {
            document.getElementById('modal_res_id').value = id;
            document.getElementById('modal_pnr').value = pnr === 'ΕΚΚΡΕΜΕΙ' ? '' : pnr;
            
            document.getElementById('modal_date_out').value = date_out;
            document.getElementById('modal_time_out').value = time_out;
            
            document.getElementById('modal_date_ret').value = date_ret;
            document.getElementById('modal_time_ret').value = time_ret;
            
            modal.classList.add('active');
        }

        function closeEditModal() {
            modal.classList.remove('active');
        }
    </script>
</body>
</html>