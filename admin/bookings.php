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

    // --- ΔΙΑΓΡΑΦΗ ΚΡΑΤΗΣΗΣ ---
    if (isset($_POST['delete_id'])) {
        $del_id = intval($_POST['delete_id']);
        $del_stmt = $pdo->prepare("DELETE FROM reservations WHERE id = ?");
        $del_stmt->execute([$del_id]);
        header("Location: bookings.php?msg=deleted");
        exit();
    }

    // --- ΠΛΗΡΗΣ ΕΠΕΞΕΡΓΑΣΙΑ ΚΡΑΤΗΣΗΣ ---
    if (isset($_POST['update_booking'])) {
        $bk_id       = intval($_POST['reservation_id']);
        $new_checkin  = trim($_POST['check_in']);
        $new_checkout = trim($_POST['check_out']);
        $new_persons  = intval($_POST['persons']);
        $new_price    = floatval($_POST['total_price']);
        $new_method   = trim($_POST['payment_method']);
        $new_status   = trim($_POST['status']);

        $upd_stmt = $pdo->prepare("
            UPDATE reservations 
            SET check_in = ?, check_out = ?, persons = ?, 
                total_price = ?, payment_method = ?, status = ? 
            WHERE id = ?
        ");
        $upd_stmt->execute([$new_checkin, $new_checkout, $new_persons, $new_price, $new_method, $new_status, $bk_id]);
        header("Location: bookings.php?msg=booking_updated");
        exit();
    }

    // --- ΣΕΛΙΔΟΠΟΙΗΣΗ (Pagination) ---
    $limit = 10; 
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($page < 1) $page = 1;
    $offset = ($page - 1) * $limit;

    $total_stmt = $pdo->query("SELECT COUNT(*) FROM reservations");
    $total_rows = $total_stmt->fetchColumn();
    $total_pages = ceil($total_rows / $limit);

    // --- ΑΝΤΛΗΣΗ ΚΡΑΤΗΣΕΩΝ ---
    $stmt = $pdo->query("
        SELECT r.*, u.fullname, u.email 
        FROM reservations r 
        JOIN users u ON r.user_id = u.id 
        ORDER BY r.id DESC 
        LIMIT $limit OFFSET $offset
    ");
    $all_bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    die("Σφάλμα σύνδεσης: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="gr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Κρατήσεις | Admin Panel</title>
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

        .msg-box { padding: 15px 20px; border-radius: 10px; margin-bottom: 20px; font-weight: 700; background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0;}
        .msg-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca;}
        .msg-info { background: #dbeafe; color: #1e40af; border: 1px solid #93c5fd;}

        /* Table */
        .table-container { background: white; padding: 25px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border-top: 4px solid var(--secondary);}
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { padding: 12px 15px; border-bottom: 2px solid var(--border); color: var(--text-muted); font-size: 13px; text-transform: uppercase; font-weight: 800;}
        td { padding: 15px; border-bottom: 1px solid var(--border); font-size: 14px; font-weight: 500;}
        tr:last-child td { border-bottom: none;}
        tr:hover td { background: #f8fafc; }
        
        .badge { background: #d1fae5; color: #065f46; padding: 5px 10px; border-radius: 6px; font-size: 12px; font-weight: 800;}
        .badge-cancelled { background: #fee2e2; color: #b91c1c; }
        
        .btn-action { border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 12px; transition: 0.2s; display: inline-flex; align-items: center; gap: 5px;}
        .btn-view { background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd; }
        .btn-view:hover { background: #bae6fd; color: #0369a1; }
        .btn-status { background: #f3f4f6; color: #475569; border: 1px solid #cbd5e1; margin-left: 5px;}
        .btn-status:hover { background: #e2e8f0; color: #1e293b; }
        .btn-delete { background: #fee2e2; color: #ef4444; border: 1px solid #fecaca; margin-left: 5px;}
        .btn-delete:hover { background: #fca5a5; color: #b91c1c; }

        /* Pagination */
        .pagination { display: flex; justify-content: center; gap: 8px; margin-top: 25px; }
        .page-link { padding: 8px 14px; background: #fff; border: 1px solid var(--border); border-radius: 8px; text-decoration: none; color: var(--text-muted); font-weight: 700; transition: 0.2s; font-size: 13px;}
        .page-link:hover { border-color: var(--secondary); color: var(--secondary); }
        .page-link.active { background: var(--secondary); color: white; border-color: var(--secondary); pointer-events: none;}

        .hidden-row { display: none !important; }

        /* Modal Προβολής (View Modal) */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15,23,42,0.8); backdrop-filter: blur(5px); z-index: 1000; display: flex; align-items: center; justify-content: center; opacity: 0; pointer-events: none; transition: 0.3s; }
        .modal-overlay.active { opacity: 1; pointer-events: auto; }
        .modal-content { background: white; width: 100%; max-width: 600px; border-radius: 20px; padding: 30px; box-shadow: 0 25px 50px rgba(0,0,0,0.2); transform: translateY(-20px); transition: 0.3s; max-height: 90vh; overflow-y: auto;}
        .modal-overlay.active .modal-content { transform: translateY(0); }
        
        .modal-title { font-size: 22px; font-weight: 900; color: var(--primary); margin-top: 0; margin-bottom: 20px; border-bottom: 2px solid var(--border); padding-bottom: 10px; display: flex; justify-content: space-between; align-items: center;}
        .modal-close { cursor: pointer; color: var(--text-muted); font-size: 20px; background: none; border: none;}
        .modal-close:hover { color: #ef4444; }
        
        .detail-box { background: #f8fafc; border: 1px solid var(--border); padding: 15px; border-radius: 12px; margin-bottom: 15px; font-size: 14px;}
        .detail-box h4 { margin: 0 0 10px 0; color: var(--secondary); font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;}
        .detail-row { display: flex; justify-content: space-between; margin-bottom: 8px; border-bottom: 1px dashed #e2e8f0; padding-bottom: 4px;}
        .detail-row:last-child { margin-bottom: 0; border-bottom: none; padding-bottom: 0;}
        .detail-row span { color: var(--text-muted); font-weight: 600;}
        .detail-row strong { color: var(--primary); font-weight: 800;}

        /* Status Modal */
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-size: 12px; font-weight: 800; color: var(--text-muted); margin-bottom: 5px; text-transform: uppercase;}
        .form-group select, .form-group input { width: 100%; padding: 12px 15px; border-radius: 10px; border: 1px solid var(--border); font-family: inherit; font-size: 14px; outline: none; box-sizing: border-box; transition: 0.2s;}
        .form-group select:focus, .form-group input:focus { border-color: var(--secondary); box-shadow: 0 0 0 3px rgba(59,130,246,0.1);}
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .form-divider { border: none; border-top: 1px dashed var(--border); margin: 20px 0; }
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
            <a href="bookings.php" class="nav-link active">🏨 Κρατήσεις Ταξιδιών</a>
            <a href="transports.php" class="nav-link">✈️ Εισιτήρια & Μέσα</a>
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
                <h1>Διαχείριση Κρατήσεων</h1>
                <p style="color: var(--text-muted); margin-top: 5px; font-size: 14px;">Συνολικά: <strong><?php echo $total_rows; ?></strong> κρατήσεις (Σελίδα <?php echo $page; ?> από <?php echo $total_pages ?: 1; ?>)</p>
            </div>
            
            <div class="search-container">
                <span class="search-icon">🔍</span>
                <input type="text" id="searchInput" class="search-bar" placeholder="Αναζήτηση στην τρέχουσα σελίδα..." onkeyup="filterTable()">
            </div>
        </div>

        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
            <div class="msg-box msg-error">✔️ Η κράτηση διαγράφηκε οριστικά!</div>
        <?php elseif(isset($_GET['msg']) && $_GET['msg'] == 'status_updated'): ?>
            <div class="msg-box">✔️ Η κατάσταση της κράτησης ενημερώθηκε!</div>
        <?php elseif(isset($_GET['msg']) && $_GET['msg'] == 'booking_updated'): ?>
            <div class="msg-box msg-info">✔️ Η κράτηση ενημερώθηκε επιτυχώς!</div>
        <?php endif; ?>

        <div class="table-container">
            <table id="bookingsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Πελάτης</th>
                        <th>Προορισμός / Ξενοδοχείο</th>
                        <th>Ημερομηνίες / Άτομα</th>
                        <th>Πληρωμή & Σύνολο</th>
                        <th>Κατάσταση</th>
                        <th style="text-align: right;">Ενέργειες</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($all_bookings) > 0): ?>
                        <?php foreach ($all_bookings as $bk): ?>
                            <tr>
                                <td>#<?php echo $bk['id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($bk['fullname']); ?></strong><br>
                                    <span style="color:#64748b; font-size:12px;"><?php echo htmlspecialchars($bk['email']); ?></span>
                                </td>
                                <td>
                                    <strong><?php echo htmlspecialchars($bk['destination_name']); ?></strong><br>
                                    <span style="color:#64748b; font-size:12px;">🏨 <?php echo htmlspecialchars($bk['hotel_name']); ?></span>
                                </td>
                                <td>
                                    <?php echo date('d/m/Y', strtotime($bk['check_in'])) . ' - ' . date('d/m/Y', strtotime($bk['check_out'])); ?><br>
                                    <span style="color:#64748b; font-size:12px;">👤 <?php echo $bk['persons']; ?> Άτομα</span>
                                </td>
                                <td>
                                    <strong style="color:var(--primary); font-size: 16px;"><?php echo number_format($bk['total_price'], 2); ?> €</strong><br>
                                    <span style="color:#0ea5e9; font-size:11px; font-weight: 700;"><?php echo htmlspecialchars($bk['payment_method'] ?? 'Μη διαθέσιμο'); ?></span>
                                </td>
                                <td>
                                    <?php if($bk['status'] === 'Ακυρώθηκε'): ?>
                                        <span class="badge badge-cancelled">Ακυρώθηκε</span>
                                    <?php else: ?>
                                        <span class="badge">Επιβεβαιώθηκε</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: right; white-space: nowrap;">
                                    
                                    <div id="details_<?php echo $bk['id']; ?>" style="display:none;">
                                        <div class="detail-box">
                                            <h4>👤 Πελάτης & Ταξίδι</h4>
                                            <div class="detail-row"><span>Όνομα:</span> <strong><?php echo htmlspecialchars($bk['fullname']); ?></strong></div>
                                            <div class="detail-row"><span>Προορισμός:</span> <strong><?php echo htmlspecialchars($bk['destination_name']); ?></strong></div>
                                            <div class="detail-row"><span>Ημερομηνίες:</span> <strong><?php echo date('d/m/Y', strtotime($bk['check_in'])) . ' έως ' . date('d/m/Y', strtotime($bk['check_out'])); ?></strong></div>
                                            <div class="detail-row"><span>Άτομα:</span> <strong><?php echo $bk['persons']; ?></strong></div>
                                            <div class="detail-row"><span>Συνολικό Ποσό:</span> <strong style="color: #10b981; font-size:16px;"><?php echo number_format($bk['total_price'], 2); ?> €</strong></div>
                                            <div class="detail-row"><span>Τρόπος Πληρωμής:</span> <strong><?php echo htmlspecialchars($bk['payment_method'] ?? '-'); ?></strong></div>
                                        </div>
                                        <div class="detail-box">
                                            <h4>🏨 Κατάλυμα</h4>
                                            <div class="detail-row"><span>Ξενοδοχείο:</span> <strong><?php echo htmlspecialchars($bk['hotel_name']); ?></strong></div>
                                            <div class="detail-row"><span>Τύπος Δωματίου:</span> <strong><?php echo htmlspecialchars($bk['room_type']); ?></strong></div>
                                        </div>
                                        <div class="detail-box" style="border-color: #bae6fd; background: #e0f2fe;">
                                            <h4>🚢 Μεταφορικά & Λεπτομέρειες (PNR)</h4>
                                            <p style="margin: 0 0 10px 0; font-weight: 800; color: #0284c7;"><?php echo htmlspecialchars($bk['transport_method']); ?></p>
                                            <div style="background: white; padding: 10px; border-radius: 8px; border: 1px dashed #7dd3fc; font-family: monospace; font-size: 13px; line-height: 1.5; color: #334155;">
                                                <?php echo nl2br(htmlspecialchars($bk['passenger_details'])); ?>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="button" class="btn-action btn-view" onclick="openViewModal('<?php echo $bk['id']; ?>')">👁️</button>
                                    
                                    <button type="button" class="btn-action btn-status" onclick="openEditModal('<?php echo $bk['id']; ?>', '<?php echo $bk['check_in']; ?>', '<?php echo $bk['check_out']; ?>', '<?php echo $bk['persons']; ?>', '<?php echo $bk['total_price']; ?>', '<?php echo htmlspecialchars($bk['payment_method'] ?? ''); ?>', '<?php echo htmlspecialchars($bk['status']); ?>')">✏️ Επεξεργασία</button>

                                    <form method="POST" style="display:inline;" onsubmit="return confirm('⚠️ Είστε σίγουροι ότι θέλετε να διαγράψετε οριστικά την κράτηση #<?php echo $bk['id']; ?>;');">
                                        <input type="hidden" name="delete_id" value="<?php echo $bk['id']; ?>">
                                        <button type="submit" class="btn-action btn-delete">❌</button>
                                    </form>

                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" style="text-align:center; padding: 30px; color:#64748b;">Δεν υπάρχουν κρατήσεις.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <?php if($total_pages > 1): ?>
                <div class="pagination">
                    <?php for($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="bookings.php?page=<?php echo $i; ?>" class="page-link <?php echo ($i == $page) ? 'active' : ''; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <div class="modal-overlay" id="viewModal">
        <div class="modal-content">
            <h2 class="modal-title">
                Λεπτομέρειες Κράτησης <span id="modal_header_id" style="color: var(--secondary);"></span>
                <button class="modal-close" onclick="closeViewModal()">✖</button>
            </h2>
            <div id="modal_content_area"></div>
            <div style="text-align: right; margin-top: 15px;">
                <button class="btn-action" style="background: #f1f5f9; color: var(--text-muted); font-size: 14px; padding: 8px 16px;" onclick="closeViewModal()">Κλείσιμο</button>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="editModal">
        <div class="modal-content" style="max-width: 550px;">
            <h2 class="modal-title">
                Επεξεργασία Κράτησης <span id="edit_modal_id" style="color: var(--secondary);"></span>
                <button class="modal-close" onclick="closeEditModal()">✖</button>
            </h2>
            <form method="POST" action="bookings.php">
                <input type="hidden" name="reservation_id" id="edit_res_id">
                
                <div class="form-row">
                    <div class="form-group">
                        <label>📅 Check-in</label>
                        <input type="date" name="check_in" id="edit_checkin" required>
                    </div>
                    <div class="form-group">
                        <label>📅 Check-out</label>
                        <input type="date" name="check_out" id="edit_checkout" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>👤 Άτομα</label>
                        <input type="number" name="persons" id="edit_persons" min="1" max="10" required>
                    </div>
                    <div class="form-group">
                        <label>💰 Συνολικό Ποσό (€)</label>
                        <input type="number" name="total_price" id="edit_price" step="0.01" min="0" required>
                    </div>
                </div>

                <hr class="form-divider">

                <div class="form-row">
                    <div class="form-group">
                        <label>💳 Τρόπος Πληρωμής</label>
                        <select name="payment_method" id="edit_method">
                            <option value="Άμεση Πληρωμή (Κάρτα)">Άμεση Πληρωμή (Κάρτα)</option>
                            <option value="Πληρωμή 3 μέρες πριν">Πληρωμή 3 μέρες πριν</option>
                            <option value="Πληρωμή στο Κατάλυμα">Πληρωμή στο Κατάλυμα</option>
                            <option value="Digital Pay (Apple/PayPal)">Digital Pay (Apple/PayPal)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>📋 Κατάσταση</label>
                        <select name="status" id="edit_status" required>
                            <option value="Επιβεβαιώθηκε">Επιβεβαιώθηκε</option>
                            <option value="Ακυρώθηκε">Ακυρώθηκε</option>
                            <option value="Σε Εκκρεμότητα">Σε Εκκρεμότητα</option>
                        </select>
                    </div>
                </div>

                <div style="text-align: right; margin-top: 20px; display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" class="btn-action btn-status" onclick="closeEditModal()" style="padding: 10px 20px;">Ακύρωση</button>
                    <button type="submit" name="update_booking" class="btn-action btn-view" style="padding: 10px 20px; background: #3b82f6; color: white; border-color: #2563eb;">💾 Αποθήκευση</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Live Αναζήτηση
        function filterTable() {
            let input = document.getElementById("searchInput");
            let filter = input.value.toLowerCase();
            let table = document.getElementById("bookingsTable");
            let tr = table.getElementsByTagName("tr");

            for (let i = 1; i < tr.length; i++) {
                let tdClient = tr[i].getElementsByTagName("td")[1];
                let tdDest = tr[i].getElementsByTagName("td")[2];
                
                if (tdClient || tdDest) {
                    let txtValue1 = tdClient.textContent || tdClient.innerText;
                    let txtValue2 = tdDest.textContent || tdDest.innerText;
                    
                    if (txtValue1.toLowerCase().indexOf(filter) > -1 || txtValue2.toLowerCase().indexOf(filter) > -1) {
                        tr[i].classList.remove('hidden-row');
                    } else {
                        tr[i].classList.add('hidden-row');
                    }
                }       
            }
        }

        // Modal Προβολής Λεπτομερειών
        const viewModal = document.getElementById('viewModal');
        function openViewModal(id) {
            document.getElementById('modal_header_id').innerText = '#' + id;
            const detailsHtml = document.getElementById('details_' + id).innerHTML;
            document.getElementById('modal_content_area').innerHTML = detailsHtml;
            viewModal.classList.add('active');
        }
        function closeViewModal() {
            viewModal.classList.remove('active');
        }

        // Modal Επεξεργασίας
        const editModal = document.getElementById('editModal');
        function openEditModal(id, checkin, checkout, persons, price, method, status) {
            document.getElementById('edit_res_id').value = id;
            document.getElementById('edit_modal_id').innerText = '#' + id;
            document.getElementById('edit_checkin').value = checkin;
            document.getElementById('edit_checkout').value = checkout;
            document.getElementById('edit_persons').value = persons;
            document.getElementById('edit_price').value = parseFloat(price).toFixed(2);
            document.getElementById('edit_method').value = method;
            document.getElementById('edit_status').value = status;
            editModal.classList.add('active');
        }
        function closeEditModal() {
            editModal.classList.remove('active');
        }
    </script>
</body>
</html>