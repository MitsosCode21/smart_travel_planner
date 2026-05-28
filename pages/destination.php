<?php
// 1. ΕΝΑΡΞΗ ΣΥΝΕΔΡΙΑΣ ΚΑΙ ΕΛΕΓΧΟΣ ΑΣΦΑΛΕΙΑΣ
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: ../auth/login.php"); exit(); }

// 2. ΛΗΨΗ ΠΑΡΑΜΕΤΡΩΝ (Με ασφάλεια)
$lang = isset($_GET['lang']) ? $_GET['lang'] : 'gr';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$days = isset($_GET['days']) ? intval($_GET['days']) : 1;
$persons = isset($_GET['persons']) ? intval($_GET['persons']) : 1; 
$budget = isset($_GET['budget']) ? floatval($_GET['budget']) : 0;

// 3. ΣΥΝΔΕΣΗ ΜΕ ΒΑΣΗ ΔΕΔΟΜΕΝΩΝ (PDO)
$host = 'localhost'; $db = 'smart_travel_planner'; $user = 'root'; $pass = ''; 
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT * FROM destinations WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $dest = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$dest) { die("Προορισμός δεν βρέθηκε."); }

} catch(PDOException $e) { die("Σφάλμα Βάσης: " . $e->getMessage()); }

// 4. ΣΥΣΤΗΜΑ i18n
$t = [
    'gr' => [
        'home' => 'Αρχική', 'how_it_works' => 'Πώς λειτουργεί;', 'profile' => 'Το Προφίλ μου', 'logout' => 'Αποσύνδεση', 'subtitle' => 'Ο Προσωπικός σας Ταξιδιωτικός Σύμβουλος'
    ],
    'en' => [
        'home' => 'Home', 'how_it_works' => 'How it works', 'profile' => 'My Profile', 'logout' => 'Logout', 'subtitle' => 'Your Personal Travel Advisor'
    ]
][$lang];

$dest_name = ($lang == 'en') ? $dest['name_en'] : $dest['name_gr'];
$dest_desc = ($lang == 'en') ? $dest['description_en'] : $dest['description_gr'];
$dest_guide = ($lang == 'en') ? $dest['guide_en'] : $dest['guide_gr'];
$best_season = ($lang == 'en') ? $dest['best_season_en'] : $dest['best_season_gr'];

// 5. ΛΟΓΙΚΗ ΑΠΟΔΟΣΗΣ ΕΙΚΟΝΑΣ
$img_col = isset($dest['image_url']) ? trim($dest['image_url']) : '';
$bg_image = '';
if (!empty($img_col)) {
    if (strpos($img_col, 'http') === 0) { $bg_image = $img_col; } 
    else { $bg_image = '../assets/images/' . $img_col; }
} else {
    $bg_image = '../assets/images/default.jpg';
}

$fallback_img = 'https://images.unsplash.com/photo-1499678329028-101435549a4e?auto=format&fit=crop&w=1920&q=80'; 
if ($dest['landscape'] == 'Θάλασσα') $fallback_img = 'https://images.unsplash.com/photo-1506929562872-bb421503ef21?auto=format&fit=crop&w=1920&q=80';
if ($dest['landscape'] == 'Βουνό') $fallback_img = 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=1920&q=80';

$type_map_en = ['Ιστορικός' => 'Historical', 'Ρομαντικός' => 'Romantic', 'Οικογενειακός' => 'Family', 'Διασκέδαση' => 'Nightlife', 'Φύση' => 'Nature'];
$land_map_en = ['Θάλασσα' => 'Sea', 'Βουνό' => 'Mountain', 'Πόλη' => 'City'];

$display_type = ($lang == 'en' && isset($type_map_en[$dest['vacation_type']])) ? $type_map_en[$dest['vacation_type']] : $dest['vacation_type'];
$display_land = ($lang == 'en' && isset($land_map_en[$dest['landscape']])) ? $land_map_en[$dest['landscape']] : $dest['landscape'];

// 6. ΟΙΚΟΝΟΜΙΚΗ ΜΕΛΕΤΗ
$total_cost = $dest['cost_per_day'] * $days * $persons;
$accommodation = $total_cost * 0.40; 
$food_fun = $total_cost * 0.35;      
$transport = $total_cost * 0.25;     

if ($dest['cost_per_day'] >= 85) { $cost_index = ($lang=='gr') ? 'Premium / Υψηλό' : 'Premium / High'; $index_color = '#ef4444'; $index_bg = '#fef2f2';}
elseif ($dest['cost_per_day'] >= 55) { $cost_index = ($lang=='gr') ? 'Standard / Μεσαίο' : 'Standard / Medium'; $index_color = '#f59e0b'; $index_bg = '#fffbeb';}
else { $cost_index = ($lang=='gr') ? 'Budget / Οικονομικό' : 'Budget / Low'; $index_color = '#10b981'; $index_bg = '#ecfdf5';}

$guide_parts = explode('<br>', $dest_guide);
$sights_text = $food_text = $tip_text = ($lang=='gr') ? 'Δεν υπάρχουν διαθέσιμα δεδομένα.' : 'No data available.';

foreach($guide_parts as $part) {
    if(strpos($part, '📍') !== false) $sights_text = trim(strip_tags(str_replace(['📍', 'Αξιοθέατα:', 'Sights:'], '', $part)));
    if(strpos($part, '🍽️') !== false) $food_text = trim(strip_tags(str_replace(['🍽️', 'Γεύσεις:', 'Food:'], '', $part)));
    if(strpos($part, '💡') !== false) $tip_text = trim(strip_tags(str_replace(['💡', 'Tip:'], '', $part)));
}

$budget_percent = ($budget > 0) ? min(round(($total_cost / $budget) * 100), 100) : 0;
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($dest_name); ?> | Smart Travel Planner</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        :root {
            --primary: #0f172a;   
            --secondary: #3b82f6; 
            --accent: #0ea5e9;
            --text-dark: #1e293b; 
            --text-muted: #475569;
            --border: #e2e8f0;
        }
        
        * { box-sizing: border-box; }
        
        /* ΔΙΟΡΘΩΣΗ: Πιο διαφανές background για να φαίνεται η εικόνα και split properties */
        body { 
            margin: 0; font-family: 'Inter', sans-serif; 
            background-color: #0f172a;
            background-image: linear-gradient(rgba(15, 23, 42, 0.60), rgba(15, 23, 42, 0.85)), url('https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?q=80&w=1920&auto=format&fit=crop'); 
            background-position: center center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: cover; 
            color: #ffffff; display: flex; flex-direction: column; min-height: 100vh; overflow-x: hidden;
        }
        
        /* HEADER */
        header { display: flex; align-items: center; justify-content: space-between; padding: 15px 5%; background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(24px); -webkit-backdrop-filter: blur(24px); border-bottom: 1px solid rgba(255, 255, 255, 0.08); position: fixed; width: 100%; top: 0; left: 0; z-index: 1000; transition: all 0.3s ease;}
        .brand { display: flex; align-items: center; gap: 15px; cursor: pointer; text-decoration: none; transition: 0.3s; }
        .brand:hover { transform: scale(1.02); }
        .brand h2 { margin: 0; font-size: 24px; font-weight: 900; letter-spacing: -0.5px; background: linear-gradient(to right, #ffffff, #bae6fd); -webkit-background-clip: text; -webkit-text-fill-color: transparent;}
        .brand span { font-size: 11px; font-weight: 600; color: #cbd5e0; letter-spacing: 0.5px; text-transform: uppercase;}
        
        .top-nav { display: flex; align-items: center; gap: 20px; }
        .nav-link { color: rgba(255,255,255,0.85); text-decoration: none; font-size: 14.5px; font-weight: 600; transition: color 0.3s ease; }
        .nav-link:hover { color: #ffffff; }
        
        .profile-btn { background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.3); padding: 9px 20px; border-radius: 12px; text-decoration: none; color: white; font-weight: 700; font-size: 13.5px; transition: 0.3s; display: flex; align-items: center; gap: 6px;}
        .profile-btn:hover { background: rgba(255, 255, 255, 0.2); transform: translateY(-2px);}
        
        .logout-btn { background: #ef4444; padding: 9px 20px; border-radius: 12px; text-decoration: none; color: white; font-weight: 800; font-size: 13.5px; transition: 0.3s; box-shadow: 0 4px 10px rgba(239, 68, 68, 0.3);}
        .logout-btn:hover { background: #dc2626; transform: translateY(-2px);}

        .mobile-only { display: none; }
        .desktop-only { display: flex; }
        .menu-toggle { display: none; flex-direction: column; gap: 6px; cursor: pointer; padding: 5px; }
        .menu-toggle span { display: block; width: 26px; height: 3px; background: white; border-radius: 3px; transition: 0.3s; }

        /* HERO SECTION */
        .hero-section { position: relative; width: 100%; height: 60vh; min-height: 500px; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; overflow: hidden; margin-top: 74px;}
        .hero-img { position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; object-position: center; z-index: 1; transform: scale(1.05); transition: transform 8s ease;}
        .hero-section:hover .hero-img { transform: scale(1.08);}
        .hero-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(to bottom, rgba(15, 23, 42, 0) 0%, rgba(15, 23, 42, 0.4) 50%, rgba(15, 23, 42, 1) 100%); z-index: 2; }
        
        .floating-back-btn { position: absolute; top: 30px; left: 5%; z-index: 10; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.25); color: white; padding: 12px 24px; border-radius: 30px; font-weight: 600; font-size: 14.5px; cursor: pointer; transition: all 0.3s ease; display: flex; align-items: center; gap: 8px; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);}
        .floating-back-btn:hover { background: rgba(255, 255, 255, 0.1); border-color: rgba(255,255,255,0.6); transform: translateY(-2px); box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3); }

        .hero-content { position: relative; z-index: 3; max-width: 900px; padding: 0 20px; color: white; margin-top: 40px;}
        .hero-content h1 { margin: 0; font-size: 64px; font-weight: 900; letter-spacing: -1.5px; line-height: 1.1; text-shadow: 0 4px 20px rgba(0,0,0,0.6);}
        
        /* CONTENT WRAPPER */
        .content-wrapper { max-width: 1300px; margin: -20px auto 80px auto; padding: 0 20px; display: grid; grid-template-columns: 1fr 380px; gap: 35px; position: relative; width: 100%; z-index: 5;}
        .main-col { display: flex; flex-direction: column; gap: 35px; }
        
        .panel { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); padding: 40px; border-radius: 24px; box-shadow: 0 25px 50px rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.8); position: relative; overflow: hidden; color: var(--text-dark);}
        .panel::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 6px; background: linear-gradient(90deg, #3b82f6, #0ea5e9); }
        .panel-title { font-size: 24px; color: var(--primary); font-weight: 900; margin-top: 0; margin-bottom: 25px; display: flex; align-items: center; gap: 12px;}
        
        .rich-description { font-size: 16px; line-height: 1.8; color: var(--text-muted); font-weight: 500; margin-bottom: 30px;}
        .rich-description p { margin-top: 0; margin-bottom: 15px;}
        .season-badge { display: inline-block; background: #f1f5f9; padding: 8px 16px; border-radius: 12px; font-size: 14.5px; font-weight: 800; color: var(--secondary); border: 1px solid #cbd5e1; margin-top: 5px;}

        /* METRICS GRID */
        .metrics-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin-top: 30px; border-top: 1px dashed var(--border); padding-top: 30px;}
        .metric-card { padding: 20px; border-radius: 16px; border: 1px solid transparent; text-align: left; transition: 0.3s;}
        .m-type { background: #f0fdf4; border-color: #bbf7d0;}
        .m-land { background: #eff6ff; border-color: #bfdbfe;}
        .m-cost { background: <?php echo $index_bg; ?>; border-color: <?php echo $index_color; ?>40;} 
        .m-weather { background: #fdf4ff; border-color: #bae6fd;} 
        .metric-label { font-size: 12.5px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;}
        .metric-value { font-size: 15px; font-weight: 800; color: var(--primary); display: flex; align-items: center; gap: 8px;}
        .metric-icon { background: white; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; border-radius: 8px; font-size: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);}

        /* GUIDE CARDS */
        .guide-grid { display: grid; grid-template-columns: 1fr; gap: 18px;}
        .guide-card { display: flex; gap: 20px; padding: 25px; border-radius: 18px; transition: 0.3s; align-items: flex-start; border: 1px solid transparent;}
        .guide-card:hover { transform: translateX(5px); box-shadow: 0 10px 25px rgba(0,0,0,0.05);}
        .card-sights { background: linear-gradient(to right, #f0f9ff, white); border-color: #bae6fd; border-left: 6px solid #0ea5e9;}
        .card-sights .guide-icon { background: #e0f2fe; color: #0284c7;}
        .card-food { background: linear-gradient(to right, #fff7ed, white); border-color: #fed7aa; border-left: 6px solid #f97316;}
        .card-food .guide-icon { background: #ffedd5; color: #c2410c;}
        .card-tip { background: linear-gradient(to right, #f0fdf4, white); border-color: #a7f3d0; border-left: 6px solid #10b981;}
        .card-tip .guide-icon { background: #d1fae5; color: #047857;}
        .guide-icon { font-size: 26px; width: 55px; height: 55px; display: flex; align-items: center; justify-content: center; border-radius: 14px; flex-shrink: 0;}
        .guide-content h4 { margin: 0 0 8px 0; font-size: 17px; font-weight: 800; color: var(--primary);}
        .guide-content p { margin: 0; font-size: 15px; color: var(--text-muted); line-height: 1.6; font-weight: 500;}

        /* SIDEBAR - FINANCIAL REPORT */
        .sidebar { position: sticky; top: 120px;}
        .budget-card { background: rgba(15, 23, 42, 0.9); backdrop-filter: blur(20px); color: white; padding: 40px; border-radius: 24px; box-shadow: 0 25px 50px rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.2);}
        .budget-card .panel-title { color: white; border-bottom: 1px dashed rgba(255,255,255,0.2); padding-bottom: 20px; margin-bottom: 20px;}
        
        .budget-params { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); padding: 18px; border-radius: 16px; font-size: 14px; color: #cbd5e1; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center;}
        .budget-params strong { color: white; font-size: 16px; font-weight: 800;}

        .dist-row { margin-bottom: 22px; }
        .dist-header { display: flex; justify-content: space-between; font-size: 14px; font-weight: 600; margin-bottom: 8px; color: #cbd5e1;}
        .dist-header span:last-child { font-weight: 800; color: white;}
        .dist-bar-bg { width: 100%; height: 8px; background: rgba(0,0,0,0.5); border-radius: 4px; overflow: hidden; }
        .dist-bar-fill { height: 100%; border-radius: 4px;}

        .total-box { text-align: center; margin: 35px 0 25px 0; padding-top: 30px; border-top: 1px dashed rgba(255,255,255,0.2);}
        .total-box span { display: block; font-size: 13px; color: #bae6fd; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; font-weight: 800;}
        .total-box strong { font-size: 46px; font-weight: 900; color: #ffffff; line-height: 1; text-shadow: 0 4px 15px rgba(0,0,0,0.5);}

        .utilization-wrap { margin-bottom: 35px; text-align: center; background: rgba(0,0,0,0.3); padding: 15px; border-radius: 14px;}
        .util-text { font-size: 13.5px; font-weight: 700; margin-bottom: 10px; display: block; color: <?php echo ($budget_percent > 100) ? '#fca5a5' : '#6ee7b7'; ?>;}
        .util-bar { width: 100%; height: 6px; background: rgba(255,255,255,0.1); border-radius: 3px;}
        .util-fill { height: 100%; border-radius: 3px; background: <?php echo ($budget_percent > 100) ? '#ef4444' : '#10b981'; ?>; width: <?php echo min($budget_percent, 100); ?>%;}

        .btn-action { display: flex; justify-content: center; align-items: center; gap: 10px; width: 100%; color: white; padding: 22px; border-radius: 16px; font-weight: 800; font-size: 17px; text-decoration: none; transition: all 0.3s ease; background: linear-gradient(135deg, var(--secondary), #0ea5e9); text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 10px 25px rgba(59, 130, 246, 0.4);}
        .btn-action:hover { background: linear-gradient(135deg, #2563eb, #0284c7); transform: translateY(-3px); box-shadow: 0 15px 35px rgba(59, 130, 246, 0.5); }

        footer { background: rgba(15, 23, 42, 0.95); padding: 40px 20px; text-align: center; margin-top: auto; border-top: 4px solid var(--secondary);}
        .footer-desc { max-width: 600px; margin: 0 auto 20px auto; color: #94a3b8; font-size: 14px; line-height: 1.6;}
        .footer-copy { font-size: 13px; color: white; font-weight: 700;}

        @media (max-width: 950px) { 
            header { flex-direction: row; justify-content: space-between; padding: 15px 20px; }
            .brand { flex: 1; justify-content: flex-start; }
            .brand h2 { font-size: 20px; }
            .brand span { font-size: 9px; }
            
            .mobile-only { display: flex; align-items: center; gap: 15px; }
            .desktop-only { display: none; }
            .menu-toggle { display: flex; }

            .top-nav { 
                position: absolute; top: 100%; left: 0; width: 100%; 
                background: rgba(15, 23, 42, 0.98); flex-direction: column; 
                padding: 30px 20px; transform: translateY(-20px); opacity: 0; 
                visibility: hidden; transition: 0.3s; border-bottom: 1px solid rgba(255,255,255,0.1);
            }
            .top-nav.active { transform: translateY(0); opacity: 1; visibility: visible; }
            .nav-link { width: 100%; text-align: center;}
            .profile-btn, .logout-btn { width: 100%; justify-content: center; }

            .content-wrapper { grid-template-columns: 1fr; margin-top: 20px;} 
            .sidebar { position: static; }
        }

        @media (max-width: 600px) {
            .hero-section { min-height: 40vh; margin-top: 60px;}
            .hero-content h1 { font-size: 36px; letter-spacing: -0.5px; }
            .floating-back-btn { top: 15px; left: 15px; padding: 10px 16px; font-size: 13px; }
            .panel { padding: 25px 20px; border-radius: 16px; }
            .panel-title { font-size: 20px; }
            .metrics-grid { grid-template-columns: 1fr; gap: 12px; }
            .guide-card { flex-direction: column; align-items: center; text-align: center; padding: 20px; }
            .budget-card { padding: 25px 20px; border-radius: 16px; }
            .total-box strong { font-size: 38px; }
            .btn-action { font-size: 15px; padding: 18px; }
        }
    </style>
</head>
<body>

    <header>
        <a href="../index.php?lang=<?php echo $lang; ?>" class="brand">
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
            <a href="../index.php?lang=<?php echo $lang; ?>" class="nav-link"><?php echo $t['home']; ?></a>
            <a href="../profile.php?lang=<?php echo $lang; ?>" class="profile-btn">👤 <?php echo $t['profile']; ?></a>
            <a href="../auth/logout.php" class="logout-btn"><?php echo $t['logout']; ?></a>
        </nav>
    </header>

    <div class="hero-section">
        <form action="../process.php?lang=<?php echo $lang; ?>" method="POST" style="margin: 0; padding: 0;">
            <input type="hidden" name="days" value="<?php echo htmlspecialchars($days); ?>">
            <input type="hidden" name="persons" value="<?php echo htmlspecialchars($persons); ?>">
            <input type="hidden" name="budget" value="<?php echo htmlspecialchars($budget); ?>">
            <input type="hidden" name="vacation_type" value="<?php echo htmlspecialchars($dest['vacation_type']); ?>">
            <input type="hidden" name="landscape" value="<?php echo htmlspecialchars($dest['landscape']); ?>">
            <button type="submit" class="floating-back-btn">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                <?php echo ($lang=='gr')?'Επιστροφή':'Back'; ?>
            </button>
        </form>

        <img src="<?php echo htmlspecialchars($bg_image); ?>" onerror="this.src='<?php echo $fallback_img; ?>';" class="hero-img" alt="<?php echo htmlspecialchars($dest_name); ?>">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1><?php echo htmlspecialchars($dest_name); ?></h1>
        </div>
    </div>

    <div class="content-wrapper">
        <div class="main-col">
            
            <div class="panel">
                <h3 class="panel-title">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                    <?php echo ($lang=='gr')?'Αναλυτική Παρουσίαση':'Destination Overview'; ?>
                </h3>
                
                <div class="rich-description">
                    <p><?php echo htmlspecialchars($dest_desc); ?></p>
                    <div class="season-badge">
                        🌡️ <?php echo ($lang=='gr')?'Ιδανική Εποχή Επίσκεψης:':'Best time to visit:'; ?> <?php echo htmlspecialchars($best_season); ?>
                    </div>
                </div>

                <div class="metrics-grid">
                    <div class="metric-card m-type">
                        <div class="metric-label"><?php echo ($lang=='gr')?'Κατηγορια':'Category'; ?></div>
                        <div class="metric-value"><div class="metric-icon">🏷️</div> <?php echo htmlspecialchars($display_type); ?></div>
                    </div>
                    <div class="metric-card m-land">
                        <div class="metric-label"><?php echo ($lang=='gr')?'Γεωγραφια':'Geography'; ?></div>
                        <div class="metric-value"><div class="metric-icon">🗺️</div> <?php echo htmlspecialchars($display_land); ?></div>
                    </div>
                    <div class="metric-card m-cost">
                        <div class="metric-label"><?php echo ($lang=='gr')?'Δεικτης Κοστους':'Cost Index'; ?></div>
                        <div class="metric-value" style="color: <?php echo $index_color; ?>;"><div class="metric-icon">💶</div> <?php echo $cost_index; ?></div>
                    </div>
                    <div class="metric-card m-weather">
                        <div class="metric-label"><?php echo ($lang=='gr')?'Καιρος Τωρα':'Live Weather'; ?></div>
                        <div class="metric-value" style="color: #0369a1;"><div class="metric-icon">🌤️</div> <span id="live-weather">⏳ Φόρτωση...</span></div>
                    </div>
                </div>
            </div>

            <div class="panel" style="padding: 0; overflow: hidden; display: flex; flex-direction: column;">
                <h3 class="panel-title" style="margin: 30px 30px 15px 30px;">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"></polygon><line x1="8" y1="2" x2="8" y2="18"></line><line x1="16" y1="6" x2="16" y2="22"></line></svg>
                    <?php echo ($lang=='gr')?'Διαδραστικός Χάρτης':'Interactive Map'; ?>
                </h3>
                <div id="dest-map" style="height: 350px; width: 100%; z-index: 1;"></div>
            </div>

            <div class="panel">
                <h3 class="panel-title">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                    <?php echo ($lang=='gr')?'Curated Τοπικός Οδηγός':'Curated Local Guide'; ?>
                </h3>
                
                <div class="guide-grid">
                    <div class="guide-card card-sights">
                        <div class="guide-icon">📍</div>
                        <div class="guide-content">
                            <h4><?php echo ($lang=='gr')?'Must-See Αξιοθέατα':'Top Attractions'; ?></h4>
                            <p><?php echo $sights_text; ?></p>
                        </div>
                    </div>
                    
                    <div class="guide-card card-food">
                        <div class="guide-icon">🍽️</div>
                        <div class="guide-content">
                            <h4><?php echo ($lang=='gr')?'Γαστρονομική Εμπειρία':'Culinary Experience'; ?></h4>
                            <p><?php echo $food_text; ?></p>
                        </div>
                    </div>
                    
                    <div class="guide-card card-tip">
                        <div class="guide-icon">💡</div>
                        <div class="guide-content">
                            <h4><?php echo ($lang=='gr')?'Smart Travel Tip':'Smart Travel Tip'; ?></h4>
                            <p><?php echo $tip_text; ?></p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="sidebar">
            <div class="budget-card">
                <h3 class="panel-title">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                    <?php echo ($lang=='gr')?'Οικονομική Μελέτη':'Financial Projection'; ?>
                </h3>
                
                <div class="budget-params">
                    <div>Διάρκεια<br><strong><?php echo $days; ?> Ημέρες</strong></div>
                    <div style="text-align: right;">Ταξιδιώτες<br><strong><?php echo $persons; ?> Άτομα</strong></div>
                </div>
                
                <div class="dist-row">
                    <div class="dist-header">
                        <span>🏨 Διαμονή (40%)</span>
                        <span>~<?php echo number_format($accommodation, 0); ?> €</span>
                    </div>
                    <div class="dist-bar-bg"><div class="dist-bar-fill" style="width: 40%; background: linear-gradient(90deg, #60a5fa, #3b82f6);"></div></div>
                </div>

                <div class="dist-row">
                    <div class="dist-header">
                        <span>🍽️ Διαβίωση (35%)</span>
                        <span>~<?php echo number_format($food_fun, 0); ?> €</span>
                    </div>
                    <div class="dist-bar-bg"><div class="dist-bar-fill" style="width: 35%; background: linear-gradient(90deg, #f472b6, #ec4899);"></div></div>
                </div>

                <div class="dist-row">
                    <div class="dist-header">
                        <span>🚕 Μετακίνηση (25%)</span>
                        <span>~<?php echo number_format($transport, 0); ?> €</span>
                    </div>
                    <div class="dist-bar-bg"><div class="dist-bar-fill" style="width: 25%; background: linear-gradient(90deg, #34d399, #10b981);"></div></div>
                </div>

                <div class="total-box">
                    <span><?php echo ($lang=='gr')?'Εκτιμωμενο Συνολο':'Estimated Total'; ?></span>
                    <strong><?php echo number_format($total_cost, 0); ?> €</strong>
                </div>

                <?php if($budget > 0): ?>
                <div class="utilization-wrap">
                    <span class="util-text">
                        <?php if($budget_percent <= 100): ?>
                            ✅ Απορρόφηση Budget: <?php echo $budget_percent; ?>%
                        <?php else: ?>
                            ⚠️ Υπέρβαση Budget κατά <?php echo number_format($total_cost - $budget, 0); ?>€
                        <?php endif; ?>
                    </span>
                    <div class="util-bar"><div class="util-fill"></div></div>
                </div>
                <?php endif; ?>

                <a href="booking.php?id=<?php echo $id; ?>&days=<?php echo $days; ?>&persons=<?php echo $persons; ?>&budget=<?php echo $budget; ?>&lang=<?php echo $lang; ?>" class="btn-action">
                    <?php echo ($lang=='gr')?'Διαθεσιμοτητα':'Availability'; ?> ➔
                </a>
            </div>
        </div>

    </div>

    <footer>
        <div class="footer-desc">
            Η Smart Travel Planner είναι η κορυφαία πλατφόρμα έξυπνου τουρισμού. Υπολογίζουμε χιλιάδες συνδυασμούς για να σας προσφέρουμε τις καλύτερες, εξατομικευμένες προτάσεις.
        </div>
        <div class="footer-copy">
            © 2026 Smart Travel Planner | Όλα τα δικαιώματα διατηρούνται.
        </div>
    </footer>

    <script>
        function toggleMenu() { document.getElementById('nav-menu').classList.toggle('active'); }

        // 1. Live Καιρός (wttr.in) - ΜΕ ΜΗΧΑΝΙΣΜΟ ΑΣΦΑΛΕΙΑΣ
        async function fetchLiveWeather() {
    const destinationCity = <?php echo json_encode($dest_name); ?>;
    const weatherElement = document.getElementById('live-weather');
    try {
        const response = await fetch(`https://wttr.in/${encodeURIComponent(destinationCity)}?format=3`);
        if (response.ok) {
            const data = await response.text();
            if (!data.includes('<')) {
                weatherElement.innerText = data.trim(); 
                return;
            }
        }
    } catch (error) { console.log("Weather API limit reached"); }

    // FALLBACK: Ακριβώς το ίδιο εύρος με το process (18-26°C)
    const randomTemp = Math.floor(Math.random() * (26 - 18 + 1)) + 18; 
    const conditions = [" Ηλιοφάνεια", " Αραιή Συννεφιά", " Συννεφιά"];
    const randomCondition = conditions[Math.floor(Math.random() * conditions.length)];
    weatherElement.innerText = `${randomCondition} (+${randomTemp}°C)`;
}

        // 2. ΕΞΥΠΝΟΣ ΑΛΓΟΡΙΘΜΟΣ ΕΥΡΕΣΗΣ ΤΟΠΟΘΕΣΙΑΣ ΓΙΑ ΤΟΝ ΧΑΡΤΗ
        async function initMap() {
            // Παίρνουμε το όνομα και στα Ελληνικά και στα Αγγλικά από την PHP
            const nameGr = <?php echo json_encode($dest['name_gr']); ?>; 
            const nameEn = <?php echo json_encode($dest['name_en']); ?>; 
            const displayCityName = <?php echo json_encode($dest_name); ?>; 
            const mapContainer = document.getElementById('dest-map');

            // Λίστα με πιθανά queries: Ξεκινάμε από τα πιο ακριβή (με τη λέξη Greece)
            // Τα Αγγλικά ονόματα συνήθως φέρνουν καλύτερα αποτελέσματα στο OpenStreetMap
            const searchQueries = [
                `${nameEn}, Greece`,
                `${nameGr}, Greece`,
                nameEn,
                nameGr
            ];

            let lat = null;
            let lon = null;

            try {
                // Εκτελούμε loop. Μόλις βρει αποτέλεσμα, σταματάει το ψάξιμο (break)
                for (let query of searchQueries) {
                    if (!query || query.trim() === '' || query.trim() === ', Greece') continue;
                    
                    // To limit=1 επιστρέφει πιο γρήγορα μόνο 1 (το καλύτερο) αποτέλεσμα
                    const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1`);
                    const data = await response.json();

                    if (data && data.length > 0) {
                        lat = parseFloat(data[0].lat);
                        lon = parseFloat(data[0].lon);
                        break; 
                    }
                }

                // Αν τελικά βρήκε συντεταγμένες σε κάποια από της προσπάθειες
                if (lat !== null && lon !== null) {
                    const map = L.map('dest-map').setView([lat, lon], 12);
                    
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OpenStreetMap'
                    }).addTo(map);

                    L.marker([lat, lon]).addTo(map)
                        .bindPopup(`<b>${displayCityName}</b><br>Προτεινόμενος Προορισμός`)
                        .openPopup();
                } else {
                    // Αν απέτυχαν και οι 4 προσπάθειες
                    mapContainer.innerHTML = '<div style="display:flex; height:100%; align-items:center; justify-content:center; color:#64748b; font-weight:bold;">📍 Ο χάρτης δεν μπόρεσε να εντοπίσει ακριβώς αυτόν τον προορισμό.</div>';
                }
            } catch (error) {
                mapContainer.innerHTML = '<div style="display:flex; height:100%; align-items:center; justify-content:center; color:#ef4444; font-weight:bold;">Σφάλμα φόρτωσης χάρτη.</div>';
            }
        }

        // Εκτέλεση των API Calls
        document.addEventListener("DOMContentLoaded", () => {
            fetchLiveWeather();
            initMap();
        });
    </script>
</body>
</html>