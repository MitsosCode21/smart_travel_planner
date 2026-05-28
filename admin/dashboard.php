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

    $error = "";
    $success = "";

    // --- 0. ΑΛΛΑΓΗ ΚΩΔΙΚΟΥ ΠΡΟΣΒΑΣΗΣ (ADMIN PROFILE) ---
    if (isset($_POST['change_password'])) {
        $current_pass = $_POST['current_password'];
        $new_pass = $_POST['new_password'];
        $confirm_pass = $_POST['confirm_password'];
        $admin_id = $_SESSION['user_id'];

        $stmt_pass = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt_pass->execute([$admin_id]);
        $admin_data = $stmt_pass->fetch(PDO::FETCH_ASSOC);

        if (!password_verify($current_pass, $admin_data['password'])) {
            $error = "Ο τρέχων κωδικός είναι λανθασμένος.";
        } elseif ($new_pass !== $confirm_pass) {
            $error = "Οι νέοι κωδικοί δεν ταιριάζουν μεταξύ τους.";
        } elseif (strlen($new_pass) < 6) {
            $error = "Ο νέος κωδικός πρέπει να έχει τουλάχιστον 6 χαρακτήρες.";
        } else {
            $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);
            $upd = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $upd->execute([$hashed_pass, $admin_id]);
            $success = "Ο κωδικός πρόσβασης άλλαξε επιτυχώς! 🔒";
        }
    }

    // --- 1. ΣΤΑΤΙΣΤΙΚΑ: Συνολικοί Χρήστες ---
    $stmt_users = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'");
    $total_users = $stmt_users->fetchColumn();

    // --- 2. ΣΤΑΤΙΣΤΙΚΑ: Συνολικές Κρατήσεις ---
    $stmt_res = $pdo->query("SELECT COUNT(*) FROM reservations");
    $total_reservations = $stmt_res->fetchColumn();

    // --- 3. ΣΤΑΤΙΣΤΙΚΑ: Συνολικά Έσοδα (Μόνο τα Επιβεβαιωμένα) ---
    $stmt_rev = $pdo->query("SELECT SUM(total_price) FROM reservations WHERE status != 'Ακυρώθηκε'");
    $total_revenue = $stmt_rev->fetchColumn() ?: 0;

    // --- 4. ΣΤΑΤΙΣΤΙΚΑ: Συνολικά Ξενοδοχεία ---
    $stmt_hotels = $pdo->query("SELECT COUNT(*) FROM hotels");
    $total_hotels = $stmt_hotels->fetchColumn();

    // --- 5. ΤΕΛΕΥΤΑΙΕΣ 5 ΚΡΑΤΗΣΕΙΣ ---
    $stmt_latest = $pdo->query("
        SELECT r.*, u.fullname, u.email 
        FROM reservations r 
        JOIN users u ON r.user_id = u.id 
        ORDER BY r.id DESC LIMIT 5
    ");
    $latest_bookings = $stmt_latest->fetchAll(PDO::FETCH_ASSOC);

    // --- 6. ΔΕΔΟΜΕΝΑ ΓΡΑΦΗΜΑΤΟΣ (Έσοδα ανά μήνα) ---
    $stmt_chart = $pdo->query("
        SELECT MONTH(check_in) as m_num, SUM(total_price) as rev 
        FROM reservations 
        WHERE status != 'Ακυρώθηκε'
        GROUP BY m_num 
        ORDER BY m_num ASC 
        LIMIT 12
    ");
    $chart_db_data = $stmt_chart->fetchAll(PDO::FETCH_ASSOC);
    
    $greek_months = [1=>'Ιαν', 2=>'Φεβ', 3=>'Μάρ', 4=>'Απρ', 5=>'Μάι', 6=>'Ιούν', 7=>'Ιούλ', 8=>'Αύγ', 9=>'Σεπ', 10=>'Οκτ', 11=>'Νοέ', 12=>'Δεκ'];
    $chart_labels = [];
    $chart_values = [];
    
    foreach ($chart_db_data as $d) {
        $chart_labels[] = $greek_months[$d['m_num']];
        $chart_values[] = (float)$d['rev'];
    }
    if (empty($chart_labels)) {
        $chart_labels = ['Ιαν', 'Φεβ', 'Μαρ', 'Απρ', 'Μαϊ', 'Ιουν'];
        $chart_values = [0, 0, 0, 0, 0, 0];
    }

} catch(PDOException $e) {
    die("Σφάλμα σύνδεσης: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="gr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Smart Travel Planner</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root { --primary: #0f172a; --secondary: #3b82f6; --accent: #0ea5e9; --bg: #f1f5f9; --text: #334155; --border: #e2e8f0; }
        body { margin: 0; font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); display: flex; min-height: 100vh;}
        
        /* Sidebar */
        .sidebar { width: 260px; background: var(--primary); color: white; display: flex; flex-direction: column; padding: 20px 0; }
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
        .admin-profile { display: flex; align-items: center; gap: 10px; font-weight: 700; background: white; padding: 8px 16px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);}

        .msg-box { padding: 15px 20px; border-radius: 10px; margin-bottom: 20px; font-weight: 700; }
        .msg-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0;}
        .msg-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca;}

        /* Stats Cards */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;}
        .stat-card { background: white; padding: 25px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); display: flex; flex-direction: column; gap: 10px; position: relative; overflow: hidden;}
        .stat-card::before { content:''; position:absolute; top:0; left:0; width:4px; height:100%; background: var(--secondary); }
        .stat-card.revenue::before { background: #10b981; }
        .stat-card.users::before { background: #f59e0b; }
        .stat-card.hotels::before { background: #8b5cf6; } 
        
        .stat-title { font-size: 13px; color: var(--text-muted); font-weight: 800; text-transform: uppercase;}
        .stat-value { font-size: 32px; font-weight: 900; color: var(--primary); margin: 0;}

        /* Content Boxes */
        .content-box { background: white; padding: 25px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); margin-bottom: 30px;}
        .content-box h3 { margin: 0 0 20px 0; font-size: 18px; font-weight: 800; color: var(--primary); border-bottom: 2px solid var(--border); padding-bottom: 10px;}
        
        /* 2-Column Grid for Bottom part */
        .dashboard-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; }

        /* Table */
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { padding: 12px 15px; border-bottom: 2px solid var(--border); color: var(--text-muted); font-size: 13px; text-transform: uppercase; font-weight: 800;}
        td { padding: 15px; border-bottom: 1px solid var(--border); font-size: 14px; font-weight: 500;}
        tr:last-child td { border-bottom: none;}
        
        .badge { padding: 5px 10px; border-radius: 6px; font-size: 12px; font-weight: 800;}
        .badge-confirmed { background: #d1fae5; color: #065f46; }
        .badge-pending { background: #fef3c7; color: #b45309; }
        .badge-cancelled { background: #fee2e2; color: #b91c1c; }

        /* Form Profile */
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-size: 12px; font-weight: 800; color: var(--text-muted); margin-bottom: 8px; text-transform: uppercase;}
        .form-group input { width: 100%; padding: 12px 15px; border-radius: 10px; border: 1px solid var(--border); font-family: inherit; font-size: 14px; box-sizing: border-box; outline: none; transition: 0.2s;}
        .form-group input:focus { border-color: var(--secondary); box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }
        .btn-submit { background: var(--primary); color: white; border: none; padding: 12px 20px; border-radius: 10px; font-weight: 800; font-size: 14px; cursor: pointer; transition: 0.2s; width: 100%;}
        .btn-submit:hover { background: #1e293b; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(15,23,42,0.2);}
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-brand">
            <h2>Smart Travel Planner</h2>
            <span>Admin Panel</span>
        </div>
        <div class="nav-links">
            <a href="dashboard.php" class="nav-link active">📊 Dashboard</a>
            <a href="users.php" class="nav-link">👥 Χρήστες</a>
            <a href="bookings.php" class="nav-link">🏨 Κρατήσεις Ταξιδιών</a>
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
                <h1>Επισκόπηση Συστήματος</h1>
                <p style="color: var(--text-muted); margin-top: 5px; font-size: 14px;">Διαχειριστείτε τις κρατήσεις και τα έσοδά σας.</p>
            </div>
            <div class="admin-profile">
                <span>👋 <?php echo htmlspecialchars($_SESSION['fullname']); ?></span>
            </div>
        </div>

        <?php if($error): ?>
            <div class="msg-box msg-error">⚠️ <?php echo $error; ?></div>
        <?php endif; ?>
        <?php if($success): ?>
            <div class="msg-box msg-success">✔️ <?php echo $success; ?></div>
        <?php endif; ?>

        <div class="stats-grid">
            <div class="stat-card users">
                <span class="stat-title">Συνολικοι Πελατες</span>
                <h2 class="stat-value"><?php echo number_format($total_users); ?></h2>
            </div>
            <div class="stat-card">
                <span class="stat-title">Κρατησεις Ταξιδιων</span>
                <h2 class="stat-value"><?php echo number_format($total_reservations); ?></h2>
            </div>
            <div class="stat-card hotels">
                <span class="stat-title">Διαθεσιμα Ξενοδοχεια</span>
                <h2 class="stat-value"><?php echo number_format($total_hotels); ?></h2>
            </div>
            <div class="stat-card revenue">
                <span class="stat-title">Συνολικα Εσοδα</span>
                <h2 class="stat-value"><?php echo number_format($total_revenue, 2); ?> €</h2>
            </div>
        </div>

        <div class="content-box">
            <h3>Έσοδα ανά Μήνα Ταξιδιού</h3>
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <div class="dashboard-grid">
            <div class="content-box" style="margin-bottom: 0;">
                <h3>Τελευταίες 5 Κρατήσεις</h3>
                <table style="font-size: 13px;">
                    <thead>
                        <tr>
                            <th>Πελάτης</th>
                            <th>Προορισμός</th>
                            <th>Ποσό</th>
                            <th>Κατάσταση</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($latest_bookings) > 0): ?>
                            <?php foreach ($latest_bookings as $bk): 
                                $status_class = 'badge-confirmed';
                                if ($bk['status'] == 'Εκκρεμεί') $status_class = 'badge-pending';
                                if ($bk['status'] == 'Ακυρώθηκε') $status_class = 'badge-cancelled';
                            ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($bk['fullname']); ?></strong><br>
                                        <span style="color:#64748b; font-size:11px;">#<?php echo $bk['id']; ?></span>
                                    </td>
                                    <td><?php echo htmlspecialchars($bk['destination_name']); ?></td>
                                    <td><strong><?php echo number_format($bk['total_price'], 2); ?> €</strong></td>
                                    <td><span class="badge <?php echo $status_class; ?>"><?php echo htmlspecialchars($bk['status']); ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" style="text-align:center; padding: 20px; color:#64748b;">Δεν υπάρχουν κρατήσεις ακόμα.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <div style="text-align: center; margin-top: 15px;">
                    <a href="bookings.php" style="color: var(--secondary); text-decoration: none; font-weight: 700; font-size: 13px;">Δείτε όλες τις κρατήσεις ➔</a>
                </div>
            </div>

            <div class="content-box" style="margin-bottom: 0; border-top: 4px solid var(--primary);">
                <h3>⚙️ Αλλαγή Κωδικού</h3>
                <form method="POST" action="dashboard.php">
                    <div class="form-group">
                        <label>Τρέχων Κωδικός</label>
                        <input type="password" name="current_password" required>
                    </div>
                    <div class="form-group">
                        <label>Νέος Κωδικός</label>
                        <input type="password" name="new_password" required placeholder="Τουλάχ. 6 χαρακτήρες">
                    </div>
                    <div class="form-group">
                        <label>Επιβεβαίωση Νέου Κωδικού</label>
                        <input type="password" name="confirm_password" required>
                    </div>
                    <button type="submit" name="change_password" class="btn-submit">💾 Αποθήκευση</button>
                </form>
            </div>
        </div>

    </div>

    <script>
        const ctx = document.getElementById('revenueChart').getContext('2d');
        
        let gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(59, 130, 246, 0.4)');
        gradient.addColorStop(1, 'rgba(59, 130, 246, 0.0)');
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($chart_labels); ?>,
                datasets: [{
                    label: 'Έσοδα (€)',
                    data: <?php echo json_encode($chart_values); ?>,
                    borderColor: '#3b82f6',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#3b82f6',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return ' ' + context.parsed.y + ' €';
                            }
                        }
                    }
                },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        grid: { borderDash: [5, 5], color: '#e2e8f0' },
                        ticks: { callback: function(value) { return value + '€'; } }
                    },
                    x: { 
                        grid: { display: false } 
                    }
                }
            }
        });
    </script>
</body>
</html>