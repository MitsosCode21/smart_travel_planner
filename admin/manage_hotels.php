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

    // --- ΑΝΤΛΗΣΗ ΠΡΟΟΡΙΣΜΩΝ (Για το dropdown menu) ---
    $stmt_dest = $pdo->query("SELECT id, name_gr FROM destinations ORDER BY name_gr ASC");
    $all_destinations = $stmt_dest->fetchAll(PDO::FETCH_ASSOC);

    // --- 1. ΠΡΟΣΘΗΚΗ (ADD) ΝΕΟΥ ΞΕΝΟΔΟΧΕΙΟΥ ---
    if (isset($_POST['add_hotel'])) {
        $dest_id = intval($_POST['destination_id']);
        $category = trim($_POST['category']);
        $h_name = trim($_POST['hotel_name']);
        $img = trim($_POST['image_url']);
        $loc = trim($_POST['location']);
        $stars = intval($_POST['stars']);
        $price = floatval($_POST['price_per_night']);
        $amenities = trim($_POST['amenities']);
        $desc = trim($_POST['description']);
        $lat = trim($_POST['latitude']);
        $lng = trim($_POST['longitude']);
        $phone = trim($_POST['phone']);
        $web = trim($_POST['website']);

        $ins = $pdo->prepare("INSERT INTO hotels (destination_id, category, hotel_name, image_url, location, stars, price_per_night, amenities, description, latitude, longitude, phone, website) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $ins->execute([$dest_id, $category, $h_name, $img, $loc, $stars, $price, $amenities, $desc, $lat, $lng, $phone, $web]);
        
        header("Location: manage_hotels.php?msg=added");
        exit();
    }

    // --- 2. ΕΠΕΞΕΡΓΑΣΙΑ (EDIT) ΞΕΝΟΔΟΧΕΙΟΥ ---
    if (isset($_POST['edit_hotel'])) {
        $h_id = intval($_POST['hotel_id']);
        $dest_id = intval($_POST['destination_id']);
        $category = trim($_POST['category']);
        $h_name = trim($_POST['hotel_name']);
        $img = trim($_POST['image_url']);
        $loc = trim($_POST['location']);
        $stars = intval($_POST['stars']);
        $price = floatval($_POST['price_per_night']);
        $amenities = trim($_POST['amenities']);
        $desc = trim($_POST['description']);
        $lat = trim($_POST['latitude']);
        $lng = trim($_POST['longitude']);
        $phone = trim($_POST['phone']);
        $web = trim($_POST['website']);

        $upd = $pdo->prepare("UPDATE hotels SET destination_id=?, category=?, hotel_name=?, image_url=?, location=?, stars=?, price_per_night=?, amenities=?, description=?, latitude=?, longitude=?, phone=?, website=? WHERE id=?");
        $upd->execute([$dest_id, $category, $h_name, $img, $loc, $stars, $price, $amenities, $desc, $lat, $lng, $phone, $web, $h_id]);
        
        header("Location: manage_hotels.php?msg=updated");
        exit();
    }

    // --- 3. ΔΙΑΓΡΑΦΗ (DELETE) ΞΕΝΟΔΟΧΕΙΟΥ ---
    if (isset($_POST['delete_hotel_id'])) {
        $del_id = intval($_POST['delete_hotel_id']);
        $del = $pdo->prepare("DELETE FROM hotels WHERE id = ?");
        $del->execute([$del_id]);
        header("Location: manage_hotels.php?msg=deleted");
        exit();
    }

    // --- ΑΝΤΛΗΣΗ ΟΛΩΝ ΤΩΝ ΞΕΝΟΔΟΧΕΙΩΝ ΜΕ ΤΟΝ ΠΡΟΟΡΙΣΜΟ ΤΟΥΣ ---
    $stmt = $pdo->query("
        SELECT h.*, d.name_gr as dest_name 
        FROM hotels h 
        LEFT JOIN destinations d ON h.destination_id = d.id 
        ORDER BY h.id DESC
    ");
    $hotels = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    die("Σφάλμα σύνδεσης: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="gr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Διαχείριση Ξενοδοχείων | Admin Panel</title>
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
        .search-bar { padding: 12px 15px 12px 35px; border-radius: 10px; border: 1px solid var(--border); outline: none; width: 300px; font-family: inherit; font-size: 14px;}
        .search-bar:focus { border-color: var(--secondary); box-shadow: 0 0 0 3px rgba(59,130,246,0.1);}
        .search-icon { position: absolute; left: 12px; top: 12px; color: #94a3b8; }
        
        .btn-add { background: #3b82f6; color: white; border: none; padding: 12px 20px; border-radius: 10px; font-weight: 800; cursor: pointer; transition: 0.2s; display: flex; align-items: center; gap: 8px;}
        .btn-add:hover { background: #2563eb; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(59,130,246,0.3);}

        .msg-box { padding: 15px 20px; border-radius: 10px; margin-bottom: 20px; font-weight: 700; background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .msg-deleted { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

        /* Table */
        .table-container { background: white; padding: 25px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); border-top: 4px solid var(--secondary);}
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { padding: 12px 15px; border-bottom: 2px solid var(--border); color: var(--text-muted); font-size: 13px; text-transform: uppercase; font-weight: 800;}
        td { padding: 15px; border-bottom: 1px solid var(--border); font-size: 14px; font-weight: 500; vertical-align: middle;}
        tr:hover td { background: #f8fafc; }
        
        .hotel-img { width: 50px; height: 50px; border-radius: 8px; object-fit: cover; }
        .stars { color: #f59e0b; font-size: 12px; letter-spacing: 2px;}
        
        .btn-action { border: none; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 12px; transition: 0.2s; display: inline-flex; align-items: center; gap: 5px;}
        .btn-edit { background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd; }
        .btn-edit:hover { background: #bae6fd; color: #0369a1; }
        .btn-delete { background: #fee2e2; color: #ef4444; border: 1px solid #fecaca; margin-left: 5px;}
        .btn-delete:hover { background: #fca5a5; color: #b91c1c; }

        .hidden-row { display: none !important; }

        /* Modal Styling (Τώρα πιο πλατύ με 2 στήλες) */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15,23,42,0.8); backdrop-filter: blur(5px); z-index: 1000; display: flex; align-items: center; justify-content: center; opacity: 0; pointer-events: none; transition: 0.3s; padding: 20px;}
        .modal-overlay.active { opacity: 1; pointer-events: auto; }
        .modal-content { background: white; width: 100%; max-width: 800px; border-radius: 20px; padding: 30px; box-shadow: 0 25px 50px rgba(0,0,0,0.2); transform: translateY(-20px); transition: 0.3s; max-height: 90vh; overflow-y: auto;}
        .modal-overlay.active .modal-content { transform: translateY(0); }
        
        .modal-title { font-size: 22px; font-weight: 900; color: var(--primary); margin-top: 0; margin-bottom: 20px; border-bottom: 2px solid var(--border); padding-bottom: 10px;}
        
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px;}
        .form-group { margin-bottom: 10px; }
        .form-group.full-width { grid-column: 1 / -1; }
        .form-group label { display: block; font-size: 12px; font-weight: 800; color: var(--text-muted); margin-bottom: 5px; text-transform: uppercase;}
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px 15px; border-radius: 10px; border: 1px solid var(--border); font-family: inherit; font-size: 14px; box-sizing: border-box; outline: none; transition: 0.2s;}
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color: var(--secondary); box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
        .form-group textarea { resize: vertical; min-height: 80px; }

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
            <a href="manage_hotels.php" class="nav-link active">🏢 Ξενοδοχεία</a>
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
                <h1>Διαχείριση Ξενοδοχείων</h1>
                <p style="color: var(--text-muted); margin-top: 5px; font-size: 14px;">Σύνολο: <strong><?php echo count($hotels); ?></strong> καταλύματα στη βάση.</p>
            </div>
            
            <div class="search-container">
                <div style="position: relative;">
                    <span class="search-icon">🔍</span>
                    <input type="text" id="searchInput" class="search-bar" placeholder="Αναζήτηση Ονόματος..." onkeyup="filterTable()">
                </div>
                <button class="btn-add" onclick="openAddModal()">➕ Νέο Ξενοδοχείο</button>
            </div>
        </div>

        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'added'): ?>
            <div class="msg-box">✔️ Το νέο ξενοδοχείο προστέθηκε επιτυχώς!</div>
        <?php elseif(isset($_GET['msg']) && $_GET['msg'] == 'updated'): ?>
            <div class="msg-box">✔️ Οι πληροφορίες του ξενοδοχείου ενημερώθηκαν!</div>
        <?php elseif(isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
            <div class="msg-box msg-deleted">✔️ Το ξενοδοχείο διαγράφηκε από τη βάση.</div>
        <?php endif; ?>

        <div class="table-container">
            <table id="hotelsTable">
                <thead>
                    <tr>
                        <th>Εικόνα</th>
                        <th>Όνομα Ξενοδοχείου</th>
                        <th>Προορισμός</th>
                        <th>Κατηγορία</th>
                        <th>Τιμή / Βράδυ</th>
                        <th style="text-align: right;">Ενέργειες</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($hotels) > 0): ?>
                        <?php foreach ($hotels as $h): ?>
                            <tr>
                                <td><img src="<?php echo htmlspecialchars($h['image_url']); ?>" alt="Hotel" class="hotel-img"></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($h['hotel_name']); ?></strong><br>
                                    <span class="stars"><?php echo str_repeat('★', $h['stars']); ?></span>
                                </td>
                                <td><?php echo htmlspecialchars($h['dest_name'] ?? 'Άγνωστος'); ?></td>
                                <td><span style="background:#f1f5f9; padding:4px 8px; border-radius:6px; font-size:12px; font-weight:600; color:#475569;"><?php echo htmlspecialchars($h['category']); ?></span></td>
                                <td><strong style="color:var(--primary);"><?php echo $h['price_per_night']; ?> €</strong></td>
                                <td style="text-align: right; white-space: nowrap;">
                                    
                                    <button type="button" class="btn-action btn-edit" 
                                        data-id="<?php echo $h['id']; ?>"
                                        data-dest="<?php echo $h['destination_id']; ?>"
                                        data-cat="<?php echo htmlspecialchars($h['category']); ?>"
                                        data-name="<?php echo htmlspecialchars($h['hotel_name']); ?>"
                                        data-img="<?php echo htmlspecialchars($h['image_url']); ?>"
                                        data-loc="<?php echo htmlspecialchars($h['location']); ?>"
                                        data-stars="<?php echo $h['stars']; ?>"
                                        data-price="<?php echo $h['price_per_night']; ?>"
                                        data-amenities="<?php echo htmlspecialchars($h['amenities']); ?>"
                                        data-lat="<?php echo htmlspecialchars($h['latitude']); ?>"
                                        data-lng="<?php echo htmlspecialchars($h['longitude']); ?>"
                                        data-phone="<?php echo htmlspecialchars($h['phone']); ?>"
                                        data-web="<?php echo htmlspecialchars($h['website']); ?>"
                                        data-desc="<?php echo htmlspecialchars($h['description']); ?>"
                                        onclick="openEditModal(this)">✏️ Επεξεργασία</button>
                                    
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('⚠️ Είστε σίγουροι ότι θέλετε να διαγράψετε αυτό το ξενοδοχείο;');">
                                        <input type="hidden" name="delete_hotel_id" value="<?php echo $h['id']; ?>">
                                        <button type="submit" class="btn-action btn-delete">❌</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" style="text-align:center; padding: 40px; color:#64748b;">Δεν υπάρχουν ξενοδοχεία.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal-overlay" id="hotelModal">
        <div class="modal-content">
            <h2 class="modal-title" id="modalTitle">🏢 Προσθήκη / Επεξεργασία</h2>
            <form method="POST" action="manage_hotels.php">
                <input type="hidden" name="hotel_id" id="modal_hotel_id">
                
                <div class="form-grid">
                    
                    <div class="form-group">
                        <label>Όνομα Ξενοδοχείου</label>
                        <input type="text" name="hotel_name" id="modal_hotel_name" required>
                    </div>

                    <div class="form-group">
                        <label>Προορισμός (Destination)</label>
                        <select name="destination_id" id="modal_dest_id" required>
                            <option value="">Επιλέξτε Προορισμό...</option>
                            <?php foreach($all_destinations as $d): ?>
                                <option value="<?php echo $d['id']; ?>"><?php echo htmlspecialchars($d['name_gr']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Τιμή ανά Βράδυ (€)</label>
                        <input type="number" step="0.01" name="price_per_night" id="modal_price" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Αστέρια (1-5)</label>
                        <select name="stars" id="modal_stars" required>
                            <option value="1">1 Αστέρι</option>
                            <option value="2">2 Αστέρια</option>
                            <option value="3">3 Αστέρια</option>
                            <option value="4">4 Αστέρια</option>
                            <option value="5">5 Αστέρια</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Κατηγορία</label>
                        <select name="category" id="modal_category" required>
                            <option value="Budget">Budget</option>
                            <option value="Standard">Standard</option>
                            <option value="Luxury">Luxury</option>
                            <option value="Boutique">Boutique</option>
                            <option value="Resort">Resort</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Link Εικόνας (URL)</label>
                        <input type="text" name="image_url" id="modal_image_url">
                    </div>

                    <div class="form-group full-width">
                        <label>Τοποθεσία (Οδός / Περιοχή)</label>
                        <input type="text" name="location" id="modal_location">
                    </div>

                    <div class="form-group">
                        <label>Γεωγρ. Πλάτος (Latitude)</label>
                        <input type="text" name="latitude" id="modal_latitude">
                    </div>

                    <div class="form-group">
                        <label>Γεωγρ. Μήκος (Longitude)</label>
                        <input type="text" name="longitude" id="modal_longitude">
                    </div>

                    <div class="form-group">
                        <label>Τηλέφωνο</label>
                        <input type="text" name="phone" id="modal_phone">
                    </div>

                    <div class="form-group">
                        <label>Website (URL)</label>
                        <input type="text" name="website" id="modal_website">
                    </div>

                    <div class="form-group full-width">
                        <label>Παροχές (π.χ. Free WiFi, Pool)</label>
                        <input type="text" name="amenities" id="modal_amenities">
                    </div>

                    <div class="form-group full-width">
                        <label>Περιγραφή</label>
                        <textarea name="description" id="modal_description"></textarea>
                    </div>

                </div> <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Ακύρωση</button>
                    <button type="submit" id="submitBtn" name="add_hotel" class="btn-save">Αποθήκευση</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Λειτουργία Αναζήτησης
        function filterTable() {
            let input = document.getElementById("searchInput");
            let filter = input.value.toLowerCase();
            let table = document.getElementById("hotelsTable");
            let tr = table.getElementsByTagName("tr");

            for (let i = 1; i < tr.length; i++) { 
                let tdName = tr[i].getElementsByTagName("td")[1];
                let tdDest = tr[i].getElementsByTagName("td")[2];
                
                if (tdName || tdDest) {
                    let txtValue1 = tdName.textContent || tdName.innerText;
                    let txtValue2 = tdDest.textContent || tdDest.innerText;
                    
                    if (txtValue1.toLowerCase().indexOf(filter) > -1 || txtValue2.toLowerCase().indexOf(filter) > -1) {
                        tr[i].classList.remove('hidden-row');
                    } else {
                        tr[i].classList.add('hidden-row');
                    }
                }       
            }
        }

        const modal = document.getElementById('hotelModal');

        // Άνοιγμα για ΝΕΟ
        function openAddModal() {
            document.getElementById('modalTitle').innerHTML = '➕ Νέο Ξενοδοχείο';
            document.getElementById('submitBtn').name = 'add_hotel';
            document.getElementById('submitBtn').innerText = 'Προσθήκη';
            
            // Καθαρισμός πεδίων
            document.getElementById('modal_hotel_id').value = '';
            document.getElementById('modal_hotel_name').value = '';
            document.getElementById('modal_dest_id').value = '';
            document.getElementById('modal_category').value = 'Standard';
            document.getElementById('modal_image_url').value = '';
            document.getElementById('modal_location').value = '';
            document.getElementById('modal_stars').value = '3';
            document.getElementById('modal_price').value = '';
            document.getElementById('modal_amenities').value = '';
            document.getElementById('modal_latitude').value = '';
            document.getElementById('modal_longitude').value = '';
            document.getElementById('modal_phone').value = '';
            document.getElementById('modal_website').value = '';
            document.getElementById('modal_description').value = '';

            modal.classList.add('active');
        }
        
        // Άνοιγμα για ΕΠΕΞΕΡΓΑΣΙΑ
        function openEditModal(btn) {
            document.getElementById('modalTitle').innerHTML = '✏️ Επεξεργασία Ξενοδοχείου';
            document.getElementById('submitBtn').name = 'edit_hotel';
            document.getElementById('submitBtn').innerText = 'Αποθήκευση Αλλαγών';

            // Ανάκτηση από τα data attributes
            document.getElementById('modal_hotel_id').value = btn.getAttribute('data-id');
            document.getElementById('modal_hotel_name').value = btn.getAttribute('data-name');
            document.getElementById('modal_dest_id').value = btn.getAttribute('data-dest');
            document.getElementById('modal_category').value = btn.getAttribute('data-cat');
            document.getElementById('modal_image_url').value = btn.getAttribute('data-img');
            document.getElementById('modal_location').value = btn.getAttribute('data-loc');
            document.getElementById('modal_stars').value = btn.getAttribute('data-stars');
            document.getElementById('modal_price').value = btn.getAttribute('data-price');
            document.getElementById('modal_amenities').value = btn.getAttribute('data-amenities');
            document.getElementById('modal_latitude').value = btn.getAttribute('data-lat');
            document.getElementById('modal_longitude').value = btn.getAttribute('data-lng');
            document.getElementById('modal_phone').value = btn.getAttribute('data-phone');
            document.getElementById('modal_website').value = btn.getAttribute('data-web');
            document.getElementById('modal_description').value = btn.getAttribute('data-desc');
            
            modal.classList.add('active');
        }

        function closeModal() {
            modal.classList.remove('active');
        }
    </script>
</body>
</html>