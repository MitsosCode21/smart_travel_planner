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

    // --- ΕΠΕΞΕΡΓΑΣΙΑ (EDIT) ΑΥΤΟΚΙΝΗΤΟΥ ---
    if (isset($_POST['edit_car'])) {
        $res_id = intval($_POST['reservation_id']);
        $new_type = trim($_POST['car_type']);
        $new_license = trim($_POST['car_license']);
        $new_days = intval($_POST['car_days']);

        // Φέρνουμε τα τωρινά δεδομένα της κράτησης
        $stmt_fetch = $pdo->prepare("SELECT transport_method, passenger_details FROM reservations WHERE id = ?");
        $stmt_fetch->execute([$res_id]);
        $row = $stmt_fetch->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $tm = $row['transport_method'];
            $pd = $row['passenger_details'];

            // 1. Ενημέρωση Ημερών (στο transport_method)
            $tm = preg_replace('/Ενοικίαση Οχήματος\s*\(\d+\s*Ημέρες\)/ui', "Ενοικίαση Οχήματος ($new_days Ημέρες)", $tm);

            // 2. Ενημέρωση Τύπου και Διπλώματος (στο passenger_details)
            $pd = preg_replace('/ΙΧ:\s*.*?\s*\(Δίπλωμα:\s*.*?\)/ui', "ΙΧ: $new_type (Δίπλωμα: $new_license)", $pd);

            // Αποθήκευση στη βάση
            $upd = $pdo->prepare("UPDATE reservations SET transport_method = ?, passenger_details = ? WHERE id = ?");
            $upd->execute([$tm, $pd, $res_id]);

            header("Location: cars.php?msg=updated");
            exit();
        }
    }

    // --- ΔΙΑΓΡΑΦΗ ΚΡΑΤΗΣΗΣ (ΟΛΙΚΗ) ---
    if (isset($_POST['delete_id'])) {
        $del_id = intval($_POST['delete_id']);
        $del_stmt = $pdo->prepare("DELETE FROM reservations WHERE id = ?");
        $del_stmt->execute([$del_id]);
        header("Location: cars.php?msg=deleted");
        exit();
    }

    // --- ΑΝΤΛΗΣΗ ΜΟΝΟ ΤΩΝ ΚΡΑΤΗΣΕΩΝ ΠΟΥ ΕΧΟΥΝ ΑΥΤΟΚΙΝΗΤΟ ---
    // Ψάχνουμε τη λέξη 'Ενοικίαση Οχήματος' μέσα στη στήλη transport_method
    $stmt = $pdo->query("
        SELECT r.*, u.fullname, u.email 
        FROM reservations r 
        JOIN users u ON r.user_id = u.id 
        WHERE r.transport_method LIKE '%Ενοικίαση Οχήματος%'
        ORDER BY r.id DESC
    ");
    $car_bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    die("Σφάλμα σύνδεσης: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="gr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ενοικιάσεις Οχημάτων | Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #0f172a; --secondary: #3b82f6; --accent: #0ea5e9; --bg: #f1f5f9; --text: #334155; --border: #e2e8f0; }
        body { margin: 0; font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); display: flex; min-height: 100vh;}
        
        /* Sidebar */
        .sidebar { width: 260px; background: var(--primary); color: white; display: flex; flex-direction: column; padding: 20px 0; flex-shrink: 0;}
        .sidebar-brand { padding: 0 20px 20px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 20px; }
        .sidebar-brand h2 { margin: 0; font-size: 18px; font-weight: 900; color: white; line-height: 1.2; letter-spacing: -0.5px;} /* Διορθωμένο για το Smart Travel Planner */
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
        .search-bar:focus { border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,0.1);}
        .search-icon { position: absolute; left: 12px; top: 12px; color: #94a3b8; }

        .msg-box { padding: 15px 20px; border-radius: 10px; margin-bottom: 20px; font-weight: 700; background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0;}
        .msg-deleted { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

        /* Table */
        .table-container { background: white; padding: 25px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border-top: 4px solid #f59e0b;}
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { padding: 12px 15px; border-bottom: 2px solid var(--border); color: var(--text-muted); font-size: 13px; text-transform: uppercase; font-weight: 800;}
        td { padding: 15px; border-bottom: 1px solid var(--border); font-size: 14px; font-weight: 500;}
        tr:last-child td { border-bottom: none;}
        tr:hover td { background: #fffbeb; }
        
        .badge { padding: 5px 10px; border-radius: 6px; font-size: 12px; font-weight: 800;}
        .badge-voucher { background: #fef3c7; color: #b45309; border: 1px dashed #f59e0b; }
        
        .btn-action { border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 12px; transition: 0.2s; display: inline-flex; align-items: center; gap: 5px;}
        .btn-edit { background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd; }
        .btn-edit:hover { background: #bae6fd; color: #0369a1; }
        .btn-delete { background: #fee2e2; color: #ef4444; border: 1px solid #fecaca; margin-left: 5px;}
        .btn-delete:hover { background: #fca5a5; color: #b91c1c; }

        .hidden-row { display: none !important; }

        /* Modal Styling */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15,23,42,0.8); backdrop-filter: blur(5px); z-index: 1000; display: flex; align-items: center; justify-content: center; opacity: 0; pointer-events: none; transition: 0.3s; }
        .modal-overlay.active { opacity: 1; pointer-events: auto; }
        .modal-content { background: white; width: 100%; max-width: 450px; border-radius: 20px; padding: 30px; box-shadow: 0 25px 50px rgba(0,0,0,0.2); transform: translateY(-20px); transition: 0.3s; }
        .modal-overlay.active .modal-content { transform: translateY(0); }
        
        .modal-title { font-size: 20px; font-weight: 900; color: #d97706; margin-top: 0; margin-bottom: 20px; border-bottom: 2px solid var(--border); padding-bottom: 10px;}
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-size: 12px; font-weight: 800; color: var(--text-muted); margin-bottom: 5px; text-transform: uppercase;}
        .form-group input { width: 100%; padding: 12px 15px; border-radius: 10px; border: 1px solid var(--border); font-family: inherit; font-size: 14px; box-sizing: border-box; outline: none; transition: 0.2s;}
        .form-group input:focus { border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245,158,11,0.1); }
        
        .modal-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 25px;}
        .btn-save { background: #f59e0b; color: white; border: none; padding: 10px 20px; border-radius: 10px; font-weight: bold; cursor: pointer; transition: 0.2s;}
        .btn-save:hover { background: #d97706; }
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
            <a href="cars.php" class="nav-link active">🚗 Ενοικιάσεις Οχημάτων</a>
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
                <h1>Ενοικιάσεις Οχημάτων</h1>
                <p style="color: var(--text-muted); margin-top: 5px; font-size: 14px;">Συνολικά: <strong><?php echo count($car_bookings); ?></strong> ενοικιάσεις ενεργές</p>
            </div>
            
            <div class="search-container">
                <span class="search-icon">🔍</span>
                <input type="text" id="searchInput" class="search-bar" placeholder="Αναζήτηση με Όνομα ή Voucher..." onkeyup="filterTable()">
            </div>
        </div>

        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'updated'): ?>
            <div class="msg-box">✔️ Τα στοιχεία της ενοικίασης ενημερώθηκαν επιτυχώς!</div>
        <?php elseif(isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
            <div class="msg-box msg-deleted">✔️ Η κράτηση (και η ενοικίαση) διαγράφηκε οριστικά.</div>
        <?php endif; ?>

        <div class="table-container">
            <table id="carsTable">
                <thead>
                    <tr>
                        <th>ID Κράτησης</th>
                        <th>Πελάτης</th>
                        <th>Προορισμός</th>
                        <th>Λεπτομέρειες Οχήματος</th>
                        <th>Voucher Code</th>
                        <th style="text-align: right;">Ενέργειες</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($car_bookings) > 0): ?>
                        <?php foreach ($car_bookings as $bk): 
                            
                            $car_days_num = 1;
                            $car_type = "Άγνωστο Όχημα";
                            $car_license = "Μη διαθέσιμο";
                            $car_voucher = "CAR-" . strtoupper(substr(md5($bk['id'] . 'rental'), 0, 6));

                            // 1. Βρίσκουμε τις ημέρες
                            if (preg_match('/Ενοικίαση Οχήματος\s*\((\d+)\s*Ημέρες\)/ui', $bk['transport_method'], $carMatch)) {
                                $car_days_num = $carMatch[1];
                            }

                            // 2. Βρίσκουμε τον τύπο και το δίπλωμα
                            if (preg_match('/\[EXTRAS:.*?(ΙΧ:.*?)\s*(?:\||\])/ui', $bk['passenger_details'], $exMatch)) {
                                $car_info_raw = $exMatch[1]; 
                                if (preg_match('/ΙΧ:\s*(.*?)\s*\(Δίπλωμα:\s*(.*?)\)/ui', $car_info_raw, $typeMatch)) {
                                    $car_type = trim($typeMatch[1]);
                                    $car_license = trim($typeMatch[2]);
                                } else {
                                    $car_type = str_replace('ΙΧ:', '', $car_info_raw);
                                }
                            }
                        ?>
                            <tr>
                                <td>#<?php echo $bk['id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($bk['fullname']); ?></strong><br>
                                    <span style="color:#64748b; font-size:12px;"><?php echo htmlspecialchars($bk['email']); ?></span>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($bk['destination_name']); ?></strong><br>
                                    <span style="color:#64748b; font-size:12px;"><?php echo date('d/m/Y', strtotime($bk['check_in'])); ?></span>
                                </td>
                                <td>
                                    <span style="color: #d97706; font-weight: 800;">🚗 <?php echo htmlspecialchars($car_type); ?></span><br>
                                    <span style="color:#64748b; font-size:12px;">Αρ. Διπλ.: <?php echo htmlspecialchars($car_license); ?> (<?php echo $car_days_num; ?> Ημέρες)</span>
                                </td>
                                <td>
                                    <span class="badge badge-voucher"><?php echo $car_voucher; ?></span>
                                </td>
                                <td style="text-align: right; white-space: nowrap;">
                                    
                                    <button type="button" class="btn-action btn-edit" onclick="openEditModal(
                                        '<?php echo $bk['id']; ?>', 
                                        '<?php echo addslashes($car_type); ?>', 
                                        '<?php echo addslashes($car_license); ?>', 
                                        '<?php echo $car_days_num; ?>'
                                    )">✏️ Επεξεργασία</button>

                                    <form method="POST" style="display:inline;" onsubmit="return confirm('⚠️ ΠΡΟΣΟΧΗ: Η διαγραφή της ενοικίασης θα διαγράψει και ολόκληρη την κράτηση του ταξιδιού (#<?php echo $bk['id']; ?>). Είστε σίγουροι;');">
                                        <input type="hidden" name="delete_id" value="<?php echo $bk['id']; ?>">
                                        <button type="submit" class="btn-action btn-delete">❌</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" style="text-align:center; padding: 40px; color:#64748b;">Δεν υπάρχουν ενοικιάσεις οχημάτων.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal-overlay" id="editModal">
        <div class="modal-content">
            <h2 class="modal-title">🚗 Επεξεργασία Ενοικίασης</h2>
            <form method="POST" action="cars.php">
                <input type="hidden" name="reservation_id" id="modal_res_id">
                
                <div class="form-group">
                    <label>Τύπος Οχήματος (π.χ. Economy, SUV)</label>
                    <input type="text" name="car_type" id="modal_car_type" required>
                </div>
                
                <div class="form-group">
                    <label>Αριθμός Διπλώματος Οδήγησης</label>
                    <input type="text" name="car_license" id="modal_car_license" required>
                </div>
                
                <div class="form-group">
                    <label>Ημέρες Ενοικίασης</label>
                    <input type="number" name="car_days" id="modal_car_days" min="1" required>
                </div>

                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeEditModal()">Ακύρωση</button>
                    <button type="submit" name="edit_car" class="btn-save">Αποθήκευση</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Live Αναζήτηση
        function filterTable() {
            let input = document.getElementById("searchInput");
            let filter = input.value.toLowerCase();
            let table = document.getElementById("carsTable");
            let tr = table.getElementsByTagName("tr");

            for (let i = 1; i < tr.length; i++) { 
                let tdClient = tr[i].getElementsByTagName("td")[1];
                let tdVoucher = tr[i].getElementsByTagName("td")[4];
                
                if (tdClient || tdVoucher) {
                    let txtValue1 = tdClient.textContent || tdClient.innerText;
                    let txtValue2 = tdVoucher.textContent || tdVoucher.innerText;
                    
                    if (txtValue1.toLowerCase().indexOf(filter) > -1 || txtValue2.toLowerCase().indexOf(filter) > -1) {
                        tr[i].classList.remove('hidden-row');
                    } else {
                        tr[i].classList.add('hidden-row');
                    }
                }       
            }
        }

        // Λειτουργίες Modal Επεξεργασίας
        const modal = document.getElementById('editModal');
        
        function openEditModal(id, type, license, days) {
            document.getElementById('modal_res_id').value = id;
            document.getElementById('modal_car_type').value = type;
            document.getElementById('modal_car_license').value = license;
            document.getElementById('modal_car_days').value = days;
            
            modal.classList.add('active');
        }

        function closeEditModal() {
            modal.classList.remove('active');
        }
    </script>
</body>
</html>