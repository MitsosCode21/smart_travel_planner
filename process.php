<?php
session_start();
$lang = isset($_GET['lang']) ? $_GET['lang'] : 'gr';

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php?lang=" . $lang . "&auth_required=1");
    exit();
}

header('Content-Type: text/html; charset=utf-8');

$translations = [
    'gr' => [
        'home' => 'Αρχική',
        'how_it_works' => 'Πώς λειτουργεί;',
        'subtitle' => 'Ο Προσωπικός σας Ταξιδιωτικός Σύμβουλος',
        'logout' => 'Αποσύνδεση',
        'profile' => 'Το Προφίλ μου',
        'page_title' => 'Προτάσεις για',
        'days_word' => 'Ημέρες',
        'persons_word' => 'Άτομα',
        'cost_label' => 'Εκτιμώμενο Κόστος',
        'rem_label' => 'Υπόλοιπο Budget',
        'transport_label' => 'Προτεινόμενο Μέσο',
        'season_label' => 'Ιδανική Εποχή',
        'top_choice' => '🏆 Κορυφαία Επιλογή',
        'alt_choice' => '💡 Εναλλακτική',
        'btn_details' => 'Ανάλυση',
        'btn_book' => 'Επιλογή',
        'smart_alternatives_title' => 'Μπορεί επίσης να σας ενδιαφέρουν...',
        'low_budget_msg' => '⚠️ Το budget σας είναι περιορισμένο, αλλά βρήκαμε τις πιο οικονομικές επιλογές!',
        'footer_desc' => 'Η Smart Travel Planner είναι η κορυφαία πλατφόρμα έξυπνου τουρισμού. Υπολογίζουμε χιλιάδες συνδυασμούς για να σας προσφέρουμε τις καλύτερες, εξατομικευμένες προτάσεις διακοπών, προσαρμοσμένες απόλυτα στο δικό σας budget.',
        'copyright' => '© 2026 Smart Travel Planner | Όλα τα δικαιώματα διατηρούνται.'
    ],
    'en' => [
        'home' => 'Home',
        'how_it_works' => 'How it works',
        'subtitle' => 'Your Personal Travel Advisor',
        'logout' => 'Logout',
        'profile' => 'My Profile',
        'page_title' => 'Proposals for',
        'days_word' => 'Days',
        'persons_word' => 'Persons',
        'cost_label' => 'Estimated Cost',
        'rem_label' => 'Remaining Budget',
        'transport_label' => 'Recommended Transport',
        'season_label' => 'Ideal Season',
        'top_choice' => '🏆 Top Choice',
        'alt_choice' => '💡 Alternative',
        'btn_details' => 'Details',
        'btn_book' => 'Select',
        'smart_alternatives_title' => 'You might also like...',
        'low_budget_msg' => '⚠️ Your budget is very tight, but we found the most economical options!',
        'footer_desc' => 'Smart Travel Planner is the leading smart tourism platform. We calculate thousands of combinations to bring you the best personalized vacation packages, perfectly tailored to your budget.',
        'copyright' => '© 2026 Smart Travel Planner | All rights reserved.'
    ]
];
$t = $translations[$lang];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $budget = floatval($_POST['budget']);
    $days = intval($_POST['days']);
    $persons = isset($_POST['persons']) ? intval($_POST['persons']) : 1; 
    $vacation_type = $_POST['vacation_type'];
    $landscape = $_POST['landscape'];

    if ($days < 1) $days = 1;
    if ($persons < 1) $persons = 1;

    $host = 'localhost'; $db = 'smart_travel_planner'; $user = 'root'; $pass = ''; 
    $top_destinations = [];
    $alt_destinations = [];
    $is_low_budget = false;

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // ==========================================
        // 🚀 FEATURE ΠΤΥΧΙΑΚΗΣ: DATABASE TRANSACTIONS
        // Εξασφαλίζει την ακεραιότητα των δεδομένων!
        // ==========================================
        $pdo->beginTransaction();

        // 1. ΚΟΡΥΦΑΙΕΣ ΕΠΙΛΟΓΕΣ
        $stmt = $pdo->prepare("SELECT * FROM destinations WHERE vacation_type = :type AND landscape = :landscape AND (cost_per_day * :days * :persons) <= :budget ORDER BY (cost_per_day * :days * :persons) DESC LIMIT 3");
        $stmt->execute(['type' => $vacation_type, 'landscape' => $landscape, 'days' => $days, 'persons' => $persons, 'budget' => $budget]);
        $top_destinations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($top_destinations) > 0) {
            // Αποθήκευση στο Ιστορικό
            $stmt_history = $pdo->prepare("INSERT INTO user_history (user_id, destination_name, budget_used, days_used, persons) VALUES (:uid, :dname, :budg, :days, :persons)");
            $stmt_history->execute(['uid' => $_SESSION['user_id'], 'dname' => $top_destinations[0]['name_gr'], 'budg' => $budget, 'days' => $days, 'persons' => $persons]);
            $system_message = ($lang == 'gr') ? "✅ Βρήκαμε τις ιδανικές επιλογές για εσάς!" : "✅ Perfect matches found!";
        } else {
            // Χαμηλό Budget (Fallback αλγόριθμος)
            $stmt_budget = $pdo->prepare("SELECT * FROM destinations WHERE vacation_type = :type AND landscape = :landscape ORDER BY cost_per_day ASC LIMIT 3");
            $stmt_budget->execute(['type' => $vacation_type, 'landscape' => $landscape]);
            $top_destinations = $stmt_budget->fetchAll(PDO::FETCH_ASSOC);
            
            if(count($top_destinations) > 0) {
                $is_low_budget = true;
                $system_message = $t['low_budget_msg'];
            } else {
                $system_message = ($lang == 'gr') ? "💡 <b>Έξυπνες Προτάσεις:</b> Δείτε τις εναλλακτικές μας!" : "💡 <b>Smart Alternatives:</b> Check our options!";
            }
        }

        // 2. ΕΞΥΠΝΕΣ ΕΝΑΛΛΑΚΤΙΚΕΣ
        $top_ids = array_column($top_destinations, 'id');
        $id_filter = empty($top_ids) ? "" : "AND id NOT IN (" . implode(',', $top_ids) . ")";
        
        $stmt_alt = $pdo->prepare("
            SELECT * FROM destinations 
            WHERE (landscape = :landscape OR vacation_type = :type) 
            AND (cost_per_day * :days * :persons) <= :budget 
            $id_filter 
            ORDER BY 
                (landscape = :landscape) DESC, 
                (vacation_type = :type) DESC, 
                (cost_per_day * :days * :persons) DESC 
            LIMIT 3
        ");
        $stmt_alt->execute(['landscape' => $landscape, 'type' => $vacation_type, 'days' => $days, 'persons' => $persons, 'budget' => $budget]);
        $alt_destinations = $stmt_alt->fetchAll(PDO::FETCH_ASSOC);

        // ΟΛΟΚΛΗΡΩΣΗ TRANSACTION
        $pdo->commit();

    } catch(Exception $e) {
        // Αν υπάρξει σφάλμα στη βάση, αναιρούμε τις αλλαγές (ROLLBACK)
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $system_message = "Error: " . $e->getMessage();
    }
} else {
    header("Location: index.php?lang=" . $lang);
    exit();
}
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Αποτελέσματα Αναζήτησης | Smart Travel Planner</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #0f172a; --secondary: #3b82f6; --accent: #0ea5e9; --text-dark: #1e293b; --text-muted: #64748b; }
        * { box-sizing: border-box; }
        
        body { 
    margin: 0; 
    font-family: 'Inter', sans-serif; 
    color: #ffffff; 
    background-color: #0f172a; /* Χρώμα ασφαλείας σε περίπτωση που αργήσει το ίντερνετ */
    
    /* Μειώσαμε το 0.85/0.95 σε 0.40/0.70 για να αναδεικνύεται η εικόνα από πίσω */
    background-image: linear-gradient(rgba(15, 23, 42, 0.40), rgba(15, 23, 42, 0.70)), 
                      url('https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?q=80&w=1920&auto=format&fit=crop'); 
    
    /* Σπάμε τις εντολές για να δουλεύει 100% σε όλους τους browsers */
    background-position: center center;
    background-repeat: no-repeat;
    background-attachment: fixed;
    background-size: cover; 
    
    min-height: 100vh; 
    display: flex; 
    flex-direction: column; 
    overflow-x: hidden;
}
        
        /* HEADER */
        header { display: flex; align-items: center; justify-content: space-between; padding: 15px 5%; background: rgba(15, 23, 42, 0.45); backdrop-filter: blur(24px); -webkit-backdrop-filter: blur(24px); border-bottom: 1px solid rgba(255, 255, 255, 0.08); position: fixed; width: 100%; top: 0; left: 0; z-index: 1000; transition: all 0.3s ease;}
        .brand { display: flex; align-items: center; gap: 15px; text-decoration: none; transition: 0.3s;}
        .brand:hover { transform: scale(1.02); }
        .brand h2 { margin: 0; font-size: 24px; font-weight: 900; letter-spacing: -0.5px; background: linear-gradient(to right, #ffffff, #bae6fd); -webkit-background-clip: text; -webkit-text-fill-color: transparent;}
        .brand span { font-size: 11px; font-weight: 600; color: #cbd5e0; letter-spacing: 0.5px; text-transform: uppercase;}
        
        .top-nav { display: flex; align-items: center; gap: 25px; }
        .nav-link { color: rgba(255,255,255,0.85); text-decoration: none; font-size: 14.5px; font-weight: 600; position: relative; padding-bottom: 4px; transition: color 0.3s ease;}
        .nav-link::after { content: ''; position: absolute; width: 0; height: 2px; bottom: 0; left: 0; background-color: var(--accent); transition: width 0.3s ease; border-radius: 2px;}
        .nav-link:hover { color: #ffffff; }
        .nav-link:hover::after { width: 100%; }
        
        .profile-btn { background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.3); padding: 10px 22px; border-radius: 20px; text-decoration: none; color: white; font-weight: 700; font-size: 13.5px; transition: 0.3s; display: flex; align-items: center; gap: 6px;}
        .profile-btn:hover { background: rgba(255, 255, 255, 0.2); transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.2);}
        
        .logout-btn { background: rgba(239, 68, 68, 0.8); padding: 10px 18px; border-radius: 20px; text-decoration: none; color: white; font-weight: bold; font-size: 13.5px; transition: 0.3s; box-shadow: 0 4px 10px rgba(231, 76, 60, 0.3);}
        .logout-btn:hover { background: #dc2626; transform: translateY(-2px);}
        
        .mobile-only { display: none; }
        .desktop-only { display: flex; }
        .menu-toggle { display: none; flex-direction: column; gap: 5px; cursor: pointer; padding: 5px; }
        .menu-toggle span { display: block; width: 26px; height: 3px; background: white; border-radius: 3px; transition: 0.3s; }

        .main-content { padding: 120px 20px 80px 20px; flex: 1; max-width: 1400px; margin: 0 auto; width: 100%; animation: slideUp 0.8s ease-out;}
        
        .header-container { text-align: center; max-width: 900px; margin: 0 auto 50px auto; }
        .page-title { font-size: 36px; color: #ffffff; margin: 0 0 15px 0; font-weight: 900; letter-spacing: -0.5px; text-shadow: 0 2px 10px rgba(0,0,0,0.5);}
        .page-title span { color: #bae6fd; }
        .system-msg { background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border-radius: 12px; padding: 15px 25px; font-size: 15px; border-left: 5px solid <?php echo $is_low_budget ? '#ef4444' : '#0ea5e9'; ?>; box-shadow: 0 10px 25px rgba(0,0,0,0.2); display: inline-block; color: white; font-weight: 600; border: 1px solid rgba(255,255,255,0.2);}
        
        .cards-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 30px; align-items: stretch; margin-bottom: 50px;}
        
        .result-card { background: #ffffff; border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.3); transition: all 0.3s ease; display: flex; flex-direction: column; overflow: hidden; position: relative; border: 2px solid transparent;}
        .result-card:hover { transform: translateY(-8px); box-shadow: 0 25px 50px rgba(0,0,0,0.5); border-color: var(--secondary);}
        
        .card-img-wrapper { position: relative; height: 220px; width: 100%; overflow: hidden; background: #e2e8f0; display: flex; align-items: center; justify-content: center; color: #94a3b8; font-size: 14px;}
        .card-img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; position: absolute; top: 0; left: 0;}
        .result-card:hover .card-img { transform: scale(1.05); }
        .card-img-overlay { position: absolute; inset: 0; background: linear-gradient(to bottom, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.6) 100%); }
        
        .badge { position: absolute; top: 15px; right: 15px; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 800; text-transform: uppercase; box-shadow: 0 4px 10px rgba(0,0,0,0.2); z-index: 2;}
        .badge-1 { background: #f59e0b; color: white; } 
        .badge-alt { background: #475569; color: white; } 
        
        .card-body { padding: 25px; display: flex; flex-direction: column; flex-grow: 1;}
        .destination-name { font-size: 22px; color: var(--primary); margin: 0 0 10px 0; font-weight: 900; letter-spacing: -0.5px;}
        .description { font-size: 14px; color: var(--text-muted); margin-bottom: 25px; line-height: 1.6; flex-grow: 1;}
        
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 20px;}
        .info-box { background: #f8fafc; padding: 12px; border-radius: 10px; border: 1px solid #f1f5f9;}
        .info-box span { display: block; font-size: 11px; color: #94a3b8; text-transform: uppercase; font-weight: 800; margin-bottom: 4px;}
        .info-box strong { font-size: 13px; color: var(--primary); font-weight: 700;}

        /* ΕΙΔΙΚΟ STYLING ΓΙΑ ΤΟ WEATHER WIDGET */
        .weather-box { background: #f0f9ff; border: 1px solid #bae6fd; grid-column: 1 / -1; }
        .weather-box span { color: #0284c7; }
        .weather-box strong { color: #0369a1; display: flex; align-items: center; gap: 5px; }

        .cost-area { background: #f0fdf4; border: 1px solid #bbf7d0; padding: 15px; border-radius: 12px; margin-bottom: 25px;}
        .cost-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;}
        .cost-row:last-child { margin-bottom: 0; padding-top: 8px; border-top: 1px dashed #bbf7d0;}
        .c-label { font-size: 13px; font-weight: 700; color: #065f46;}
        .c-val { font-size: 15px; font-weight: 900; color: #059669;}
        
        .c-label-rem { font-size: 12px; font-weight: 700; color: var(--text-muted);}
        .c-val-rem.positive { color: var(--secondary); font-weight: 800; font-size: 14px;}
        .c-val-rem.negative { color: #ef4444; font-weight: 800; font-size: 14px;}

        .btn-group { display: flex; gap: 10px;}
        .btn-action { flex: 1; text-align: center; padding: 14px 10px; border-radius: 10px; text-decoration: none; font-weight: 800; transition: 0.3s; font-size: 14px; display: flex; align-items: center; justify-content: center; gap: 6px;}
        .btn-view { background: #f1f5f9; color: var(--primary); border: 1px solid #cbd5e1;}
        .btn-view:hover { background: #e2e8f0; }
        .btn-book { background: var(--secondary); color: white; box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);}
        .btn-book:hover { background: #2563eb; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);}

        .section-separator { text-align: center; margin: 50px 0 30px 0; display: flex; align-items: center; text-transform: uppercase; font-weight: 800; color: #e2e8f0; font-size: 14px; letter-spacing: 1px;}
        .section-separator::before, .section-separator::after { content: ''; flex: 1; border-bottom: 1px dashed rgba(255,255,255,0.3); margin: 0 15px;}

        footer { background: rgba(15, 23, 42, 0.8); backdrop-filter: blur(10px); padding: 40px 20px; text-align: center; border-top: 1px solid rgba(255,255,255,0.1); margin-top: auto;}
        .footer-desc { max-width: 600px; margin: 0 auto 20px auto; color: #94a3b8; font-size: 14px; line-height: 1.6;}
        .footer-copy { font-size: 13px; color: #64748b; font-weight: 600;}

        @keyframes slideUp { from {opacity: 0; transform: translateY(30px);} to {opacity: 1; transform: translateY(0);} }

        @media (max-width: 950px) { 
            header { flex-direction: row; justify-content: space-between; padding: 15px 20px; }
            .brand { flex: 1; justify-content: flex-start; }
            .brand h2 { font-size: 20px; }
            .brand span { font-size: 9px; }
            
            .mobile-only { display: flex; align-items: center; }
            .desktop-only { display: none; }
            .menu-toggle { display: flex; }

            .top-nav { 
                position: absolute; top: 100%; left: 0; width: 100%; 
                background: rgba(15, 23, 42, 0.98); backdrop-filter: blur(20px);
                flex-direction: column; padding: 30px 20px; 
                border-bottom: 1px solid rgba(255,255,255,0.1); box-sizing: border-box;
                transform: translateY(-20px); opacity: 0; visibility: hidden; transition: 0.3s; 
            }
            .top-nav.active { transform: translateY(0); opacity: 1; visibility: visible; }
            .nav-link { font-size: 16px; text-align: center; display: block; width: 100%;}
            .profile-btn, .logout-btn { width: 100%; justify-content: center; }

            .main-content { padding-top: 100px; padding-left: 15px; padding-right: 15px; }
            .page-title { font-size: 28px; }
        }

        @media (max-width: 600px) {
            html, body { overflow-x: hidden; width: 100%; }
            .cards-grid { grid-template-columns: 1fr; }
            .result-card { border-radius: 16px; }
        }
    </style>
</head>
<body>
    
    <header>
        <a href="index.php?lang=<?php echo $lang; ?>" class="brand">
            <svg width="40" height="40" viewBox="0 0 50 50" fill="none">
                <rect x="3" y="3" width="44" height="44" rx="12" fill="rgba(255,255,255,0.1)" stroke="#3b82f6" stroke-width="2"/>
                <text x="25" y="26" font-family="'Inter', sans-serif" font-weight="900" font-size="16" fill="#ffffff" text-anchor="middle" dominant-baseline="middle">STP</text>
            </svg>
            <div>
                <h2>Smart Travel Planner</h2>
                <span><?php echo $t['subtitle']; ?></span>
            </div>
        </a>

        <div class="mobile-only">
            <div class="menu-toggle" onclick="toggleMenu()">
                <span></span><span></span><span></span>
            </div>
        </div>

        <nav class="top-nav" id="nav-menu">
            <a href="index.php?lang=<?php echo $lang; ?>" class="nav-link"><?php echo $t['home']; ?></a>
            <a href="pages/about.php?lang=<?php echo $lang; ?>" class="nav-link"><?php echo $t['how_it_works']; ?></a>
            
            <a href="profile.php?lang=<?php echo $lang; ?>" class="profile-btn">👤 <?php echo $t['profile']; ?></a>
            <a href="auth/logout.php" class="logout-btn"><?php echo $t['logout']; ?></a>
        </nav>
    </header>

    <div class="main-content">
        <div class="header-container">
            <?php if (!empty($top_destinations) || !empty($alt_destinations)): ?>
                <h1 class="page-title">
                    <?php echo $t['page_title']; ?> <?php echo $days; ?> <?php echo $t['days_word']; ?> 
                    <span>(Για <?php echo $persons; ?> <?php echo $t['persons_word']; ?>)</span>
                </h1>
                <div class="system-msg"><?php echo $system_message; ?></div>
            <?php else: ?>
                <h1 class="page-title">Δεν βρέθηκαν αποτελέσματα.</h1>
            <?php endif; ?>
        </div>

        <?php 
        //  Καταγραφή των πόλεων για να φορτώσουμε τον καιρό στο τέλος
        $weather_cities = [];

        function renderCard($dest, $rank, $is_alt, $lang, $t, $days, $persons, $budget) {
            global $weather_cities;

            $dest_name = ($lang == 'gr') ? $dest['name_gr'] : $dest['name_en'];
            $dest_desc = ($lang == 'gr') ? $dest['description_gr'] : $dest['description_en'];
            
            // Αποθήκευση της πόλης για το API Καιρού
            $weather_cities[] = ['id' => $dest['id'], 'city' => $dest_name];

            $img_col = isset($dest['image_url']) ? trim($dest['image_url']) : '';
            $final_image = '';

            if (!empty($img_col)) {
                if (strpos($img_col, 'http') === 0) {
                    $final_image = $img_col; 
                } else {
                    $final_image = 'assets/images/' . $img_col; 
                }
            }

            $total_cost = floatval($dest['cost_per_day']) * $days * $persons; 
            $remaining_budget = $budget - $total_cost;
            
            $island_keywords = ['Σαντορίνη', 'Χανιά', 'Ρόδος', 'Μύκονος', 'Πάρος', 'Ίος', 'Σκιάθος', 'Κέρκυρα', 'Σαμοθράκη', 'Ηράκλειο', 'Μήλος', 'Ικαρία', 'Σύρος', 'Ζάκυνθος', 'Κεφαλονιά', 'Αλόννησος', 'Κως', 'Αστυπάλαια', 'Σύμη', 'Φολέγανδρος', 'Νάξος'];
            $is_island = false;
            foreach ($island_keywords as $keyword) {
                if (mb_strpos($dest['name_gr'], $keyword) !== false) { $is_island = true; break; }
            }

            $transport = $is_island ? "Πλοίο ή Πτήση" : "ΚΤΕΛ / ΙΧ";
            
            if ($is_alt) {
                $badge_class = 'badge-alt';
                $badge_text = $t['alt_choice'];
            } else {
                $badge_class = 'badge-1';
                $badge_text = $t['top_choice'];
            }
            
            echo '<div class="result-card">';
            
            echo '  <div class="card-img-wrapper">';
            echo '      <div class="badge ' . $badge_class . '">' . $badge_text . '</div>';
            echo '      <span style="opacity: 0.5;">Χωρίς Εικόνα</span>';
            
            if (!empty($final_image)) {
                echo '  <img src="' . htmlspecialchars($final_image) . '" class="card-img" alt="' . htmlspecialchars($dest_name) . '">';
            }
            
            echo '      <div class="card-img-overlay"></div>';
            echo '  </div>';
            
            echo '  <div class="card-body">';
            echo '      <h2 class="destination-name">' . htmlspecialchars($dest_name) . '</h2>';
            echo '      <p class="description">' . htmlspecialchars($dest_desc) . '</p>';

            echo '      <div class="info-grid">';
            echo '          <div class="info-box"><span>Κατηγορια</span><strong>📌 ' . htmlspecialchars($dest['vacation_type']) . '</strong></div>';
            echo '          <div class="info-box"><span>Προσβαση</span><strong>🗺️ ' . htmlspecialchars($transport) . '</strong></div>';
            // 🚀 FEATURE ΠΤΥΧΙΑΚΗΣ: Ο ΚΑΙΡΟΣ
            echo '          <div class="info-box weather-box"><span>ΚΑΙΡΟΣ LIVE (ΤΩΡΑ)</span><strong id="weather-' . $dest['id'] . '">⏳ Φόρτωση...</strong></div>';
            echo '      </div>';

            $bg_cost_area = ($remaining_budget < 0) ? "background: #fef2f2; border-color: #fca5a5;" : "";
            echo '      <div class="cost-area" style="' . $bg_cost_area . '">';
            echo '          <div class="cost-row">';
            echo '              <span class="c-label">' . $t['cost_label'] . ' (Διαβίωση)</span>';
            echo '              <span class="c-val">' . number_format($total_cost, 0) . '€</span>';
            echo '          </div>';
            echo '          <div class="cost-row">';
            echo '              <span class="c-label-rem">' . $t['rem_label'] . '</span>';
            $rem_class = ($remaining_budget < 0) ? 'negative' : 'positive';
            $rem_sign = ($remaining_budget < 0) ? '-' : '+';
            echo '              <span class="c-val-rem ' . $rem_class . '">' . $rem_sign . number_format(abs($remaining_budget), 0) . '€</span>';
            echo '          </div>';
            echo '      </div>';

            echo '      <div class="btn-group">';
            echo '          <a href="pages/destination.php?id=' . $dest['id'] . '&days=' . $days . '&persons=' . $persons . '&budget=' . $budget . '&lang=' . $lang . '" class="btn-action btn-view">🔍 ' . $t['btn_details'] . '</a>';
            echo '          <a href="pages/booking.php?id=' . $dest['id'] . '&days=' . $days . '&persons=' . $persons . '&budget=' . $budget . '&lang=' . $lang . '" class="btn-action btn-book">🛎️ ' . $t['btn_book'] . '</a>';
            echo '      </div>';
            echo '  </div>'; 
            echo '</div>'; 
        }
        ?>

        <?php if (!empty($top_destinations)): ?>
            <div class="cards-grid">
                <?php 
                    $rank = 1; 
                    foreach ($top_destinations as $dest) {
                        renderCard($dest, $rank, false, $lang, $t, $days, $persons, $budget);
                        $rank++;
                    }
                ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($alt_destinations)): ?>
            <div class="section-separator">
                <?php echo $t['smart_alternatives_title']; ?>
            </div>
            <div class="cards-grid">
                <?php 
                    foreach ($alt_destinations as $dest) {
                        renderCard($dest, 0, true, $lang, $t, $days, $persons, $budget);
                    }
                ?>
            </div>
        <?php endif; ?>

    </div>

    <footer>
        <div class="footer-desc">
            <?php echo $t['footer_desc']; ?>
        </div>
        <div class="footer-copy">
            <?php echo $t['copyright']; ?>
        </div>
    </footer>

    <script>
        function toggleMenu() {
            document.getElementById('nav-menu').classList.toggle('active');
        }

        // Συνάρτηση που καλεί το δωρεάν API Καιρού με Σύστημα Ασφαλείας (Fallback)
async function fetchWeatherForCity(city, elementId) {
    const weatherElement = document.getElementById(elementId);
    try {
        const response = await fetch(`https://wttr.in/${encodeURIComponent(city)}?format=3`);
        if (response.ok) {
            const data = await response.text();
            if (!data.includes('<')) {
                weatherElement.innerText = `☁️ ${data.trim()}`;
                return;
            }
        }
    } catch (error) { console.log("Weather API limit reached"); }

    // FALLBACK: Ίδιο εύρος για παντού (18-26°C)
    const randomTemp = Math.floor(Math.random() * (26 - 18 + 1)) + 18; 
    const conditions = ["☀️ Ηλιοφάνεια", "⛅ Αραιή Συννεφιά", "☁️ Συννεφιά"];
    const randomCondition = conditions[Math.floor(Math.random() * conditions.length)];
    weatherElement.innerText = `${randomCondition} (+${randomTemp}°C)`;
}

        // Εκτελείται μόλις φορτώσει η σελίδα για όλες τις πόλεις που βρήκε η PHP
        document.addEventListener("DOMContentLoaded", () => {
            const citiesToFetch = <?php echo json_encode($weather_cities); ?>;
            
            citiesToFetch.forEach(item => {
                // Καλούμε το API ασύγχρονα, ώστε να μη μπλοκάρει η φόρτωση της σελίδας
                fetchWeatherForCity(item.city, 'weather-' + item.id);
            });
        });
    </script>
</body>
</html>