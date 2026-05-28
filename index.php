<?php
session_start();

$lang = isset($_GET['lang']) ? $_GET['lang'] : 'gr';

$translations = [
    'gr' => [
        'home' => 'Αρχική',
        'subtitle' => 'Ο Προσωπικός σας Ταξιδιωτικός Σύμβουλος',
        'how_it_works' => 'Πώς λειτουργεί;',
        'login' => 'Σύνδεση / Εγγραφή',
        'logout' => 'Αποσύνδεση',
        'profile' => 'Το Προφίλ μου',
        'title' => 'Ανακαλύψτε τον Ιδανικό Προορισμό',
        'desc' => 'Μην χάνετε ώρες ψάχνοντας. Εισάγετε το budget, τις ημέρες και το ιδανικό τοπίο, και εμείς οργανώνουμε το τέλειο ταξίδι για εσάς.',

        'tag1' => 'Προτάσεις Εποχής',
        'tag2' => 'Οργάνωση Μεταφορικών',
        'tag3' => 'Βελτιστοποίηση Budget',

        'form_title' => 'ΠΑΡΑΜΕΤΡΟΙ ΑΝΑΖΗΤΗΣΗΣ',
        'persons_label' => 'ΑΤΟΜΑ',
        'days_label' => 'ΗΜΕΡΕΣ',
        'budget_label' => 'ΣΥΝΟΛΙΚΟ BUDGET (€)',
        'type_label' => 'ΤΥΠΟΣ ΔΙΑΚΟΠΩΝ',
        'landscape_label' => 'ΤΟΠΙΟ',
        'opt_placeholder' => '-- ΕΠΙΛΕΞΤΕ --',

        'opt_hist' => 'Ιστορικός',
        'opt_rom' => 'Ρομαντικός',
        'opt_fam' => 'Οικογενειακός',
        'opt_fun' => 'Διασκέδαση',
        'opt_nat' => 'Φύση',
        'opt_sea' => 'Θάλασσα',
        'opt_mountain' => 'Βουνό',
        'opt_city' => 'Πόλη',

        'btn' => 'ΕΥΡΕΣΗ ΤΑΞΙΔΙΟΥ',
        'btn_login_req' => 'ΕΓΓΡΑΦΗ ΓΙΑ ΕΥΡΕΣΗ',
        'note' => 'Προτείνουμε ιδανική εποχή, μέσο & διαμονή.',
        'secure_payments' => 'Όλες οι συναλλαγές είναι 100% Ασφαλείς (SSL 256-bit)',

        'features_title' => 'Γιατί να μας επιλέξετε;',
        'feat_1' => 'Απόλυτη Εξατομίκευση',
        'feat_1_desc' => 'Αναλύουμε τις προτιμήσεις και το budget σας για να δημιουργήσουμε το ιδανικό ταξιδιωτικό πακέτο, κομμένο και ραμμένο στα μέτρα σας.',
        'feat_2' => 'Άμεσα E-Tickets',
        'feat_2_desc' => 'Ξεχάστε τις εκτυπώσεις. Όλα τα εισιτήρια και οι κρατήσεις σας αποθηκεύονται ψηφιακά στο προφίλ σας με δυναμικά QR Codes.',
        'feat_3' => 'Ασφάλεια & Ευελιξία',
        'feat_3_desc' => 'Απολαύστε δωρεάν ακύρωση, απόλυτα ασφαλείς συναλλαγές και δυνατότητα πληρωμής του ξενοδοχείου απευθείας στο κατάλυμα.',

        'pop_title' => 'Κορυφαίοι Προορισμοί',
        'pop_desc' => 'Εξερευνήστε τις πιο δημοφιλείς επιλογές των ταξιδιωτών μας.',
        'btn_summer' => 'Καλοκαίρι',
        'btn_winter' => 'Χειμώνας',
        'explore' => 'Εξερεύνηση',

        'cookie_msg' => 'Χρησιμοποιούμε cookies για να βελτιώσουμε την εμπειρία σας.',
        'cookie_btn' => 'Εντάξει, το κατάλαβα!',
        'copyright' => '© 2026 Smart Travel Planner | Σχεδιασμένο για μοναδικές εμπειρίες',

        'f_company' => 'ΕΤΑΙΡΕΙΑ',
        'f_about' => 'Σχετικά με εμάς',
        'f_support' => 'Υποστήριξη',
        'f_contact' => 'Επικοινωνία',
        'f_legal' => 'ΝΟΜΙΚΑ',
        'f_terms' => 'Όροι Χρήσης',
        'f_privacy' => 'Πολιτική Απορρήτου',
        'f_cookies' => 'Πολιτική Cookies'
    ],
    'en' => [
        'home' => 'Home',
        'subtitle' => 'Your Personal Travel Advisor',
        'how_it_works' => 'How it works',
        'login' => 'Login / Register',
        'logout' => 'Logout',
        'profile' => 'My Profile',
        'title' => 'Discover Your Ideal Destination',
        'desc' => 'Stop wasting hours searching. Enter your budget, available days, and preferred landscape, and let us organize the perfect trip.',
        'tag1' => 'Seasonal Suggestions',
        'tag2' => 'Transport Organized',
        'tag3' => 'Budget Optimized',

        'form_title' => 'SEARCH PARAMETERS',
        'persons_label' => 'PERSONS',
        'days_label' => 'DAYS',
        'budget_label' => 'TOTAL BUDGET (€)',
        'type_label' => 'VACATION TYPE',
        'landscape_label' => 'LANDSCAPE',
        'opt_placeholder' => '-- SELECT --',

        'opt_hist' => 'Historical',
        'opt_rom' => 'Romantic',
        'opt_fam' => 'Family',
        'opt_fun' => 'Nightlife',
        'opt_nat' => 'Nature',
        'opt_sea' => 'Sea',
        'opt_mountain' => 'Mountain',
        'opt_city' => 'City',

        'btn' => 'FIND MY TRIP',
        'btn_login_req' => 'SIGN UP TO SEARCH',
        'note' => 'We recommend the ideal season and transport.',
        'secure_payments' => 'All transactions are 100% Secure (SSL 256-bit)',

        'features_title' => 'Why choose us?',
        'feat_1' => 'Absolute Personalization',
        'feat_1_desc' => 'We analyze your preferences and budget to create the ideal travel package, tailored specifically for you.',
        'feat_2' => 'Instant E-Tickets',
        'feat_2_desc' => 'Forget printing. All your tickets and bookings are stored digitally in your profile with dynamic QR Codes.',
        'feat_3' => 'Security & Flexibility',
        'feat_3_desc' => 'Enjoy free cancellation, fully secure transactions, and the ability to pay for your hotel directly at the property.',

        'pop_title' => 'Top Destinations',
        'pop_desc' => 'Explore our travelers\' most popular choices.',
        'btn_summer' => 'Summer',
        'btn_winter' => 'Winter',
        'explore' => 'Explore',

        'cookie_msg' => 'We use cookies to improve your experience on our platform.',
        'cookie_btn' => 'Got it!',
        'copyright' => '© 2026 Smart Travel Planner | Designed for unique experiences',

        'f_company' => 'COMPANY',
        'f_about' => 'About Us',
        'f_support' => 'Support',
        'f_contact' => 'Contact',
        'f_legal' => 'LEGAL',
        'f_terms' => 'Terms of Use',
        'f_privacy' => 'Privacy Policy',
        'f_cookies' => 'Cookie Policy'
    ]
];
$t = $translations[$lang];

// -------------------------------------------------------------------------
// ΣΥΝΔΕΣΗ ΜΕ ΒΑΣΗ ΔΕΔΟΜΕΝΩΝ ΓΙΑ ΤΡΑΒΗΓΜΑ TOP ΠΡΟΟΡΙΣΜΩΝ
// -------------------------------------------------------------------------
$host = 'localhost';
$db = 'smart_travel_planner';
$user = 'root';
$pass = '';
$summer_dests = [];
$winter_dests = [];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Τραβάμε τους 4 Κορυφαίους Καλοκαιρινούς
    $stmt_s = $pdo->query("SELECT * FROM destinations WHERE id IN (6, 16, 11, 5) ORDER BY FIELD(id, 6, 16, 11, 5)");
    $summer_dests = $stmt_s->fetchAll(PDO::FETCH_ASSOC);

    // Τραβάμε τους 4 Κορυφαίους Χειμερινούς
    $stmt_w = $pdo->query("SELECT * FROM destinations WHERE id IN (8, 14, 35, 13) ORDER BY FIELD(id, 8, 14, 35, 13)");
    $winter_dests = $stmt_w->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Αν υπάρχει πρόβλημα με τη βάση, αφήνουμε τους πίνακες κενούς
}

// -------------------------------------------------------------------------
// ΣΥΝΑΡΤΗΣΗ ΓΙΑ ΤΟ RENDER ΤΩΝ ΚΑΡΤΩΝ
// -------------------------------------------------------------------------
function renderIndexCard($dest, $t, $lang)
{
    $dest_name = ($lang == 'gr') ? $dest['name_gr'] : $dest['name_en'];
    $landscape = $dest['landscape'];
    $type = $dest['vacation_type'];

    // ΑΠΟΛΥΤΑ ΚΑΘΑΡΗ ΛΟΓΙΚΗ ΕΙΚΟΝΑΣ ΑΠΟ ΤΗ ΒΑΣΗ
    $img_col = isset($dest['image_url']) ? trim($dest['image_url']) : '';
    $final_image = '';

    if (!empty($img_col)) {
        if (strpos($img_col, 'unsplash.com/photos/white-and-blue-concrete') !== false) {
            $final_image = 'https://images.unsplash.com/photo-1499856871958-5b9627545d1a?auto=format&fit=crop&w=800&q=80';
        } else if (strpos($img_col, 'http') === 0) {
            $final_image = $img_col;
        } else {
            $final_image = 'assets/images/' . $img_col;
        }
    } else {
        $final_image = 'assets/images/default.jpg';
    }

    // Όταν κάνεις κλικ, συμπληρώνει τη φόρμα και κάνει scroll up
    $js_click = "document.getElementById('landscape').value='" . htmlspecialchars($landscape) . "'; " .
        "document.getElementById('type').value='" . htmlspecialchars($type) . "'; " .
        "window.scrollTo({top: 0, behavior: 'smooth'});";

    echo '<div class="dest-card" onclick="' . $js_click . '">';
    echo '  <div class="dest-img-wrapper">';
    echo '      <img src="' . htmlspecialchars($final_image) . '" class="dest-img" alt="' . htmlspecialchars($dest_name) . '">';
    echo '  </div>';
    echo '  <div class="dest-info">';
    echo '      <h4>' . htmlspecialchars($dest_name) . '</h4>';
    echo '      <p>' . htmlspecialchars($landscape) . ' &bull; ' . htmlspecialchars($type) . '</p>';
    echo '      <div class="dest-btn">' . $t['explore'] . '</div>';
    echo '  </div>';
    echo '</div>';
}
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Travel Planner | Home</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --primary: #0f172a;
            --secondary: #3b82f6;
            --accent: #0ea5e9;
            --text-dark: #1e293b;
            --text-muted: #64748b;
            --radius: 28px;
            --bg-color: #f8fafc;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-dark);
            overflow-x: hidden;
        }

        /* HEADER */
        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 5%;
            background: rgba(15, 23, 42, 0.45);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 15px;
            cursor: pointer;
            text-decoration: none;
            transition: 0.3s;
        }

        .brand:hover {
            transform: scale(1.02);
        }

        .brand h2 {
            margin: 0;
            font-size: 24px;
            font-weight: 900;
            letter-spacing: -0.5px;
            background: linear-gradient(to right, #ffffff, #bae6fd);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .brand span {
            font-size: 11px;
            font-weight: 600;
            color: #94a3b8;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .top-nav {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        /* Animated Nav Links */
        .nav-link {
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            font-size: 14.5px;
            font-weight: 600;
            position: relative;
            padding-bottom: 4px;
            transition: color 0.3s ease;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 0;
            background-color: var(--accent);
            transition: width 0.3s ease;
            border-radius: 2px;
        }

        .nav-link:hover {
            color: #ffffff;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .login-btn {
            background: rgba(255, 255, 255, 0.1);
            padding: 10px 24px;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: 0.3s;
            color: white;
            text-decoration: none;
            font-weight: 700;
            font-size: 13.5px;
        }

        .login-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            transform: translateY(-2px);
        }

        .profile-btn {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 10px 22px;
            border-radius: 20px;
            text-decoration: none;
            color: white;
            font-weight: 700;
            font-size: 13.5px;
            transition: 0.3s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .profile-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .logout-btn {
            background: rgba(239, 68, 68, 0.8);
            padding: 10px 18px;
            border-radius: 20px;
            text-decoration: none;
            color: white;
            font-weight: bold;
            font-size: 13.5px;
            transition: 0.3s;
            box-shadow: 0 4px 10px rgba(231, 76, 60, 0.3);
        }

        .logout-btn:hover {
            background: #dc2626;
            transform: translateY(-2px);
        }

        /* --- ΚΟΥΜΠΙ ADMIN DASHBOARD (ΟΡΑΤΟ ΜΟΝΟ ΣΕ ADMIN) --- */
        .admin-btn {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            padding: 10px 20px;
            border-radius: 20px;
            text-decoration: none;
            color: white;
            font-weight: 700;
            font-size: 13.5px;
            transition: 0.3s;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.35);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .admin-btn:hover {
            background: linear-gradient(135deg, #d97706, #b45309);
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(245, 158, 11, 0.45);
        }

        .lang-switch {
            display: flex;
            gap: 8px;
            font-size: 13px;
            font-weight: 800;
            border-left: 1px solid rgba(255, 255, 255, 0.15);
            padding-left: 20px;
        }

        .lang-switch a {
            color: rgba(255, 255, 255, 0.5);
            text-decoration: none;
            transition: 0.3s;
            padding: 6px 12px;
            border-radius: 10px;
        }

        .lang-switch a.active {
            color: var(--primary);
            background: #ffffff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
        }

        .lang-switch a:hover:not(.active) {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.15);
        }

        /* CLASSES ΕΛΕΓΧΟΥ ΟΡΑΤΟΤΗΤΑΣ */
        .mobile-only {
            display: none;
        }

        .desktop-only {
            display: flex;
        }

        /* HAMBURGER MENU ICON (3 Dots) */
        .menu-toggle {
            display: none;
            flex-direction: column;
            gap: 5px;
            cursor: pointer;
            padding: 5px;
        }

        .menu-toggle span {
            display: block;
            width: 26px;
            height: 3px;
            background: white;
            border-radius: 3px;
            transition: 0.3s;
        }

        /* HERO SECTION */
        .hero-container {
            background: linear-gradient(rgba(10, 20, 30, 0.65), rgba(15, 30, 45, 0.95)), url('https://images.unsplash.com/photo-1533105079780-92b9be482077?q=80&w=1920&auto=format&fit=crop') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            color: white;
            padding-top: 100px;
            padding-bottom: 50px;
        }

        .wrapper {
            max-width: 1400px;
            margin: 0 auto;
            padding: 30px 40px;
            flex: 1;
            display: flex;
            flex-direction: column;
            width: 100%;
            justify-content: center;
        }

        .hero {
            display: grid;
            grid-template-columns: 1.15fr 0.85fr;
            gap: 40px;
            align-items: center;
            flex: 1;
        }

        .info-section {
            animation: fadeInLeft 1s cubic-bezier(0.2, 0.8, 0.2, 1);
        }

        .info-section h1 {
            font-size: 54px;
            margin-top: 0;
            margin-bottom: 20px;
            font-weight: 900;
            letter-spacing: -1.5px;
            line-height: 1.15;
            text-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            background: linear-gradient(135deg, #ffffff 0%, #bae6fd 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .info-section p {
            font-size: 18.5px;
            line-height: 1.7;
            color: #e2e8f0;
            margin-bottom: 40px;
            font-weight: 400;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
            max-width: 95%;
        }

        .features-tags {
            display: flex;
            flex-direction: row;
            gap: 12px;
            flex-wrap: nowrap;
            align-items: center;
        }

        .tag {
            background: rgba(255, 255, 255, 0.1);
            padding: 12px 18px;
            border-radius: 30px;
            font-size: 13.5px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(15px);
            font-weight: 600;
            white-space: nowrap;
            color: #ffffff;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            animation: float 4s ease-in-out infinite;
        }

        .tag:nth-child(1) {
            animation-delay: 0s;
        }

        .tag:nth-child(2) {
            animation-delay: 0.5s;
        }

        .tag:nth-child(3) {
            animation-delay: 1s;
        }

        @keyframes float {
            0% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-6px);
            }

            100% {
                transform: translateY(0px);
            }
        }

        /* ΚΑΡΤΑ ΦΟΡΜΑΣ */
        .card {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(40px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            border-radius: 32px;
            padding: 45px;
            box-shadow: 0 40px 80px -20px rgba(0, 0, 0, 0.6), 0 0 60px rgba(56, 189, 248, 0.25);
            color: var(--text-dark);
            position: relative;
            z-index: 10;
            animation: slideUp 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
            max-width: 520px;
            width: 100%;
            margin-left: auto;
        }

        .card h3 {
            margin-top: 0;
            margin-bottom: 25px;
            font-size: 24px;
            text-align: center;
            color: var(--primary);
            font-weight: 900;
            letter-spacing: -0.5px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
            width: 100%;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .field-full {
            grid-column: 1 / -1;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        label {
            font-size: 12.5px;
            font-weight: 800;
            color: var(--primary);
            letter-spacing: 0.5px;
            white-space: nowrap;
        }

        input,
        select {
            width: 100%;
            padding: 15px 18px;
            border: 1px solid rgba(255, 255, 255, 0.6);
            border-radius: 16px;
            font-size: 14.5px;
            background: rgba(241, 245, 249, 0.8);
            outline: none;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            color: var(--text-dark);
            font-weight: 700;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.03);
        }

        input:hover,
        select:hover {
            background: rgba(226, 232, 240, 0.9);
        }

        input:focus,
        select:focus {
            background: #ffffff;
            border-color: var(--secondary);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15), inset 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        .btn {
            width: 100%;
            background: linear-gradient(135deg, #2563eb, #0ea5e9);
            color: white;
            padding: 18px;
            border-radius: 16px;
            border: none;
            cursor: pointer;
            font-weight: 900;
            font-size: 16px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 25px rgba(37, 99, 235, 0.3);
            letter-spacing: 1px;
            margin-top: 5px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        .btn:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 15px 35px rgba(37, 99, 235, 0.4);
            background: linear-gradient(135deg, #1d4ed8, #0284c7);
        }

        .system-note {
            font-size: 12.5px;
            color: var(--text-muted);
            text-align: center;
            margin-top: 2px;
            font-weight: 600;
        }

        .trust-badge {
            background: rgba(240, 253, 244, 0.8);
            border: 1px solid #bbf7d0;
            color: #166534;
            padding: 12px;
            border-radius: 12px;
            font-size: 12.5px;
            font-weight: 700;
            text-align: center;
            margin-top: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        /* ΓΙΑΤΙ ΕΜΑΣ */
        .features-parallax {
            background: linear-gradient(rgba(15, 23, 42, 0.8), rgba(30, 58, 138, 0.85)), url('https://images.unsplash.com/photo-1499678329028-101435549a4e?q=80&w=1920&auto=format&fit=crop') no-repeat center center fixed;
            background-size: cover;
            padding: 100px 20px;
        }

        .features-parallax .section-header h2 {
            color: white;
        }

        .features-parallax .section-header p {
            color: #bae6fd;
        }

        .features-wrapper {
            max-width: 1400px;
            margin: 0 auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .feat-box {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            padding: 40px 30px;
            border-radius: 24px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            transition: 0.3s;
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
        }

        .feat-box:hover {
            transform: translateY(-10px);
            background: rgba(255, 255, 255, 0.15);
            border-color: #bae6fd;
        }

        .feat-icon {
            font-size: 40px;
            margin-bottom: 20px;
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            width: 80px;
            height: 80px;
            line-height: 80px;
            border-radius: 50%;
            color: #bae6fd;
        }

        .feat-box h4 {
            font-size: 20px;
            margin-bottom: 15px;
            font-weight: 800;
            color: white;
        }

        .feat-box p {
            font-size: 14.5px;
            color: #cbd5e1;
            line-height: 1.6;
        }

        /* ΕΝΟΤΗΤΑ ΠΡΟΟΡΙΣΜΩΝ ΜΕ TOGGLE */
        .destinations-section {
            background: white;
            padding: 100px 20px;
            border-bottom: 1px solid #e2e8f0;
        }

        .dest-wrapper {
            max-width: 1400px;
            margin: 0 auto;
        }

        .section-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .section-header h2 {
            font-size: 36px;
            color: var(--primary);
            font-weight: 900;
            margin-bottom: 15px;
            letter-spacing: -0.5px;
        }

        .section-header p {
            font-size: 18px;
            color: var(--text-muted);
            max-width: 600px;
            margin: 0 auto;
        }

        /* TOGGLE ΚΟΥΜΠΙΑ */
        .season-toggle {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin: 0 auto 50px auto;
            background: #f1f5f9;
            padding: 6px;
            border-radius: 40px;
            max-width: 340px;
            box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .toggle-btn {
            flex: 1;
            padding: 14px 20px;
            border: none;
            background: transparent;
            border-radius: 30px;
            font-weight: 800;
            font-size: 15px;
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .toggle-btn.active {
            background: white;
            color: var(--primary);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .dest-grid {
            display: none;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            animation: slideUp 0.5s ease-out forwards;
        }

        .dest-grid.active-grid {
            display: grid;
        }

        .dest-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: 0.3s;
            cursor: pointer;
            border: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .dest-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            border-color: var(--secondary);
        }

        .dest-img-wrapper {
            height: 220px;
            overflow: hidden;
            flex-shrink: 0;
            background: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            font-size: 14px;
            position: relative;
        }

        .dest-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
            position: absolute;
            top: 0;
            left: 0;
        }

        .dest-card:hover .dest-img {
            transform: scale(1.05);
        }

        .dest-info {
            padding: 25px;
            background: white;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .dest-info h4 {
            margin: 0 0 5px 0;
            font-size: 20px;
            color: var(--primary);
            font-weight: 800;
        }

        .dest-info p {
            margin: 0 0 20px 0;
            color: var(--text-muted);
            font-size: 14px;
            font-weight: 600;
        }

        .dest-btn {
            margin-top: auto;
            align-self: flex-start;
            background: #eff6ff;
            color: var(--secondary);
            padding: 10px 20px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 800;
            transition: 0.3s;
        }

        .dest-card:hover .dest-btn {
            background: var(--secondary);
            color: white;
        }

        /* FOOTER */
        footer {
            background: var(--primary);
            color: white;
            padding: 60px 40px 30px 40px;
        }

        .footer-content {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 40px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 40px;
            margin-bottom: 30px;
        }

        .f-brand h2 {
            margin: 0 0 10px 0;
            font-weight: 900;
            color: white;
        }

        .f-brand p {
            color: #94a3b8;
            font-size: 14.5px;
            max-width: 300px;
            line-height: 1.6;
        }

        .f-links {
            display: flex;
            gap: 60px;
        }

        .f-col h4 {
            margin: 0 0 20px 0;
            font-size: 16px;
            color: #cbd5e1;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .f-col a {
            display: block;
            color: #94a3b8;
            text-decoration: none;
            margin-bottom: 12px;
            font-size: 14.5px;
            transition: 0.2s;
        }

        .f-col a:hover {
            color: white;
            transform: translateX(5px);
        }

        .copyright {
            text-align: center;
            color: #64748b;
            font-size: 14px;
        }

        /* COOKIE BANNER */
        .cookie-banner {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%) translateY(200%);
            background: white;
            padding: 20px 30px;
            border-radius: 16px;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            gap: 25px;
            z-index: 9999;
            transition: transform 0.5s cubic-bezier(0.2, 0.8, 0.2, 1);
            width: 90%;
            max-width: 700px;
            border: 1px solid #e2e8f0;
            visibility: hidden;
        }

        .cookie-banner.show {
            transform: translateX(-50%) translateY(0);
            visibility: visible;
        }

        .cookie-text {
            flex: 1;
            font-size: 14px;
            color: var(--text-dark);
            line-height: 1.5;
            font-weight: 500;
        }

        .cookie-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 800;
            cursor: pointer;
            transition: 0.2s;
            white-space: nowrap;
        }

        .cookie-btn:hover {
            background: var(--secondary);
        }

        /* ANIMATIONS */
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-40px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }


        /* =========================================================
           📱 ΕΞΕΙΔΙΚΕΥΜΕΝΟ RESPONSIVE (ΓΙΑ TABLETS & ΚΙΝΗΤΑ)
           ========================================================= */

        /* --- 1. ΜΙΚΡΑ LAPTOPS & ΜΕΓΑΛΑ TABLETS (Κάτω από 1100px) --- */
        @media (max-width: 1100px) {
            .hero {
                grid-template-columns: 1fr;
                gap: 40px;
                text-align: center;
            }

            .info-section h1 {
                font-size: 46px;
            }

            .features-tags {
                justify-content: center;
            }

            /* Η φόρμα κρατάει τις 2 στήλες, αλλά κεντράρεται όμορφα */
            .card {
                padding: 40px;
                max-width: 650px;
                margin: 0 auto;
            }

            .cookie-banner {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }

            /* Μικραίνουμε λίγο το κενό στο μενού για να χωράει */
            .top-nav {
                gap: 15px;
            }

            .nav-link {
                font-size: 13px;
            }
        }

        /* --- 2. ΚΑΝΟΝΙΚΑ TABLETS (iPad) & ΜΕΓΑΛΑ ΚΙΝΗΤΑ (Κάτω από 950px) --- */
        @media (max-width: 950px) {

            /* Ενεργοποιούμε το Hamburger Menu (γραμμούλες) από εδώ για να μη στριμώχνονται τα κουμπιά */
            header {
                flex-direction: row;
                justify-content: space-between;
                padding: 15px 25px;
            }

            .brand {
                gap: 10px;
                flex: 1;
                justify-content: flex-start;
            }

            .mobile-only {
                display: flex;
                align-items: center;
                gap: 15px;
            }

            .desktop-only {
                display: none;
            }

            .lang-switch.mobile-only {
                border-left: none;
                padding-left: 0;
            }

            .lang-switch a {
                padding: 6px 10px;
                font-size: 12px;
            }

            .menu-toggle {
                display: flex;
            }

            .top-nav {
                position: absolute;
                top: 100%;
                left: 0;
                width: 100%;
                background: rgba(15, 23, 42, 0.98);
                backdrop-filter: blur(20px);
                flex-direction: column;
                gap: 20px;
                padding: 30px 20px;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
                box-sizing: border-box;
                transform: translateY(-20px);
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s ease;
            }

            .top-nav.active {
                transform: translateY(0);
                opacity: 1;
                visibility: visible;
            }

            .nav-link {
                font-size: 16px;
                text-align: center;
                display: block;
            }

            .login-btn,
            .profile-btn,
            .logout-btn {
                width: 100%;
                text-align: center;
                justify-content: center;
            }

            .features-parallax,
            .destinations-section {
                padding: 80px 20px;
            }
        }

        /* --- 3. ΚΙΝΗΤΑ ΤΗΛΕΦΩΝΑ (Κάτω από 600px) --- */
        @media (max-width: 600px) {

            html,
            body {
                overflow-x: hidden;
                width: 100%;
            }

            header {
                padding: 12px 15px;
            }

            .brand h2 {
                font-size: 18px;
            }

            .brand span {
                font-size: 9px;
            }

            .brand svg {
                width: 30px;
                height: 30px;
            }

            .hero-container {
                padding-top: 80px;
            }

            .wrapper {
                padding: 20px 15px;
            }

            .info-section h1 {
                font-size: 34px;
                letter-spacing: -0.5px;
            }

            .info-section p {
                font-size: 15px;
                margin: 0 auto 30px auto;
                max-width: 100%;
            }

            .features-tags {
                flex-wrap: wrap;
                justify-content: center;
                gap: 8px;
            }

            .tag {
                padding: 8px 14px;
                font-size: 12px;
            }

            /* Η Φόρμα γίνεται 1 στήλη στο κινητό */
            .card {
                padding: 25px 20px;
                border-radius: 20px;
                width: 100%;
                max-width: 100%;
                margin: 0;
            }

            .card h3 {
                font-size: 20px;
            }

            .form-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            input,
            select {
                padding: 12px 15px;
                font-size: 14px;
            }

            .features-parallax,
            .destinations-section {
                padding: 50px 15px;
            }

            .section-header h2 {
                font-size: 26px;
            }

            /* Διόρθωση των toggle κουμπιών (Χειμώνας/Καλοκαίρι) για κινητά */
            .season-toggle {
                flex-wrap: wrap;
                border-radius: 20px;
            }

            .toggle-btn {
                border-radius: 15px;
                width: 100%;
                justify-content: center;
            }

            /* Footer */
            .f-links {
                flex-direction: column;
                gap: 30px;
                align-items: center;
                text-align: center;
            }

            .f-col {
                text-align: center;
            }

            .footer-content {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
        }
    </style>
</head>

<body>

    <header>
        <a href="index.php?lang=<?php echo $lang; ?>" class="brand">
            <svg width="44" height="44" viewBox="0 0 50 50" fill="none"
                style="filter: drop-shadow(0px 4px 6px rgba(0,0,0,0.3));">
                <defs>
                    <linearGradient id="logo-bg" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#00d2ff" />
                        <stop offset="100%" stop-color="#3b82f6" />
                    </linearGradient>
                </defs>
                <rect x="3" y="3" width="44" height="44" rx="14" fill="url(#logo-bg)" fill-opacity="0.15"
                    stroke="url(#logo-bg)" stroke-width="2" />
                <text x="25" y="26" font-family="'Inter', sans-serif" font-weight="900" font-size="17" fill="#ffffff"
                    text-anchor="middle" dominant-baseline="middle" letter-spacing="1">STP</text>
                <path d="M 14 34 Q 25 40 36 34" stroke="#00d2ff" stroke-width="2.5" stroke-linecap="round" />
                <circle cx="36" cy="34" r="2.5" fill="#ffffff" />
            </svg>

            <div>
                <h2>Smart Travel Planner</h2>
                <span><?php echo $t['subtitle']; ?></span>
            </div>
        </a>

        <div class="mobile-only" style="align-items: center; gap: 15px;">
            <div class="lang-switch" style="border: none; padding: 0;">
                <a href="?lang=gr" class="<?php echo $lang == 'gr' ? 'active' : ''; ?>">GR</a>
                <a href="?lang=en" class="<?php echo $lang == 'en' ? 'active' : ''; ?>">EN</a>
            </div>

            <div class="menu-toggle" onclick="toggleMenu()">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>

        <nav class="top-nav" id="nav-menu">
            <a href="index.php?lang=<?php echo $lang; ?>" class="nav-link"><?php echo $t['home']; ?></a>
            <a href="pages/about.php?lang=<?php echo $lang; ?>" class="nav-link"><?php echo $t['how_it_works']; ?></a>
            <a href="pages/about_us.php?lang=<?php echo $lang; ?>" class="nav-link"><?php echo $t['f_about']; ?></a>
            <a href="pages/contact.php?lang=<?php echo $lang; ?>" class="nav-link"><?php echo $t['f_contact']; ?></a>

            <?php if (isset($_SESSION['user_id'])): ?>
                <?php // --- ΚΟΥΜΠΙ ADMIN DASHBOARD (ΟΡΑΤΟ ΜΟΝΟ ΣΕ ΔΙΑΧΕΙΡΙΣΤΕΣ) --- ?>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <a href="admin/dashboard.php" class="admin-btn">
                        ⚙️ Admin Dashboard
                    </a>
                <?php endif; ?>
                <a href="profile.php" class="profile-btn">
                    👤 <?php echo $t['profile']; ?>
                </a>
                <a href="auth/logout.php" class="logout-btn"><?php echo $t['logout']; ?></a>
            <?php else: ?>
                <a href="auth/login.php?lang=<?php echo $lang; ?>" class="login-btn"><?php echo $t['login']; ?></a>
            <?php endif; ?>

            <div class="lang-switch desktop-only">
                <a href="?lang=gr" class="<?php echo $lang == 'gr' ? 'active' : ''; ?>">GR</a>
                <a href="?lang=en" class="<?php echo $lang == 'en' ? 'active' : ''; ?>">EN</a>
            </div>
        </nav>
    </header>

    <div class="hero-container">
        <div class="wrapper">
            <div class="hero">
                <div class="info-section">
                    <h1><?php echo $t['title']; ?></h1>
                    <p><?php echo $t['desc']; ?></p>

                    <div class="features-tags">
                        <div class="tag">📅 <?php echo $t['tag1']; ?></div>
                        <div class="tag">✈️ <?php echo $t['tag2']; ?></div>
                        <div class="tag">💰 <?php echo $t['tag3']; ?></div>
                    </div>
                </div>

                <div class="card" id="search-form">
                    <h3>✨ <?php echo $t['form_title']; ?></h3>

                    <form action="process.php?lang=<?php echo $lang; ?>" method="POST">
                        <div class="form-grid">
                            <div class="field">
                                <label for="persons">👥 <?php echo $t['persons_label']; ?> (MAX 8)</label>
                                <input type="number" id="persons" name="persons" placeholder="π.χ. 2" required min="1"
                                    max="8">
                            </div>
                            <div class="field">
                                <label for="days">📅 <?php echo $t['days_label']; ?> (MAX 15)</label>
                                <input type="number" id="days" name="days" placeholder="π.χ. 5" required min="1"
                                    max="15">
                            </div>

                            <div class="field">
                                <label for="type">🏖️ <?php echo $t['type_label']; ?></label>
                                <select id="type" name="vacation_type" required>
                                    <option value="" disabled selected><?php echo $t['opt_placeholder']; ?></option>
                                    <option value="Ιστορικός"><?php echo $t['opt_hist']; ?></option>
                                    <option value="Ρομαντικός"><?php echo $t['opt_rom']; ?></option>
                                    <option value="Οικογενειακός"><?php echo $t['opt_fam']; ?></option>
                                    <option value="Διασκέδαση"><?php echo $t['opt_fun']; ?></option>
                                    <option value="Φύση"><?php echo $t['opt_nat']; ?></option>
                                </select>
                            </div>
                            <div class="field">
                                <label for="landscape">⛰️ <?php echo $t['landscape_label']; ?></label>
                                <select id="landscape" name="landscape" required>
                                    <option value="" disabled selected><?php echo $t['opt_placeholder']; ?></option>
                                    <option value="Θάλασσα"><?php echo $t['opt_sea']; ?></option>
                                    <option value="Βουνό"><?php echo $t['opt_mountain']; ?></option>
                                    <option value="Πόλη"><?php echo $t['opt_city']; ?></option>
                                </select>
                            </div>

                            <div class="field-full">
                                <label for="budget">💰 <?php echo $t['budget_label']; ?></label>
                                <input type="number" id="budget" name="budget" placeholder="π.χ. 800" required min="50"
                                    step="5">
                            </div>
                        </div>

                        <?php if (isset($_SESSION['user_id'])): ?>
                            <button class="btn" type="submit">🔍 <?php echo $t['btn']; ?></button>
                        <?php else: ?>
                            <button class="btn" type="submit"
                                style="background: linear-gradient(135deg, #475569, #334155); box-shadow: none;">🔒
                                <?php echo $t['btn_login_req']; ?></button>
                        <?php endif; ?>

                        <div class="system-note"><?php echo $t['note']; ?></div>

                        <div class="trust-badge">
                            🔒 <?php echo $t['secure_payments']; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="features-parallax" id="about">
        <div class="features-wrapper">
            <div class="section-header">
                <h2><?php echo $t['features_title']; ?></h2>
            </div>

            <div class="features-grid">
                <div class="feat-box">
                    <div class="feat-icon">🎯</div>
                    <h4><?php echo $t['feat_1']; ?></h4>
                    <p><?php echo $t['feat_1_desc']; ?></p>
                </div>
                <div class="feat-box">
                    <div class="feat-icon">📱</div>
                    <h4><?php echo $t['feat_2']; ?></h4>
                    <p><?php echo $t['feat_2_desc']; ?></p>
                </div>
                <div class="feat-box">
                    <div class="feat-icon">🛡️</div>
                    <h4><?php echo $t['feat_3']; ?></h4>
                    <p><?php echo $t['feat_3_desc']; ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="destinations-section">
        <div class="dest-wrapper">
            <div class="section-header">
                <h2><?php echo $t['pop_title']; ?></h2>
                <p><?php echo $t['pop_desc']; ?></p>
            </div>

            <div class="season-toggle">
                <button class="toggle-btn active" id="btn-summer" onclick="switchSeason('summer', true)">☀️
                    <?php echo $t['btn_summer']; ?></button>
                <button class="toggle-btn" id="btn-winter" onclick="switchSeason('winter', true)">❄️
                    <?php echo $t['btn_winter']; ?></button>
            </div>

            <div class="dest-grid active-grid" id="grid-summer">
                <?php
                if (!empty($summer_dests)) {
                    foreach ($summer_dests as $dest) {
                        renderIndexCard($dest, $t, $lang);
                    }
                }
                ?>
            </div>

            <div class="dest-grid" id="grid-winter">
                <?php
                if (!empty($winter_dests)) {
                    foreach ($winter_dests as $dest) {
                        renderIndexCard($dest, $t, $lang);
                    }
                }
                ?>
            </div>

        </div>
    </div>

    <footer>
        <div class="footer-content">
            <div class="f-brand">
                <h2>Smart Travel Planner</h2>
                <p><?php echo $t['desc']; ?></p>
            </div>
            <div class="f-links">
                <div class="f-col">
                    <h4><?php echo $t['f_company']; ?></h4>
                    <a href="pages/about_us.php?lang=<?php echo $lang; ?>"><?php echo $t['f_about']; ?></a>
                    <a href="pages/support.php?lang=<?php echo $lang; ?>"><?php echo $t['f_support']; ?></a>
                    <a href="pages/contact.php?lang=<?php echo $lang; ?>"><?php echo $t['f_contact']; ?></a>
                </div>
                <div class="f-col">
                    <h4><?php echo $t['f_legal']; ?></h4>
                    <a href="pages/terms.php?lang=<?php echo $lang; ?>"><?php echo $t['f_terms']; ?></a>
                    <a href="pages/privacy.php?lang=<?php echo $lang; ?>"><?php echo $t['f_privacy']; ?></a>
                    <a href="pages/cookies.php?lang=<?php echo $lang; ?>"><?php echo $t['f_cookies']; ?></a>
                </div>
            </div>
        </div>
        <div class="copyright">
            <?php echo $t['copyright']; ?>
        </div>
    </footer>

    <div class="cookie-banner" id="cookieBanner">
        <div class="cookie-text">
            🍪 <strong>Πολιτική Cookies:</strong> <?php echo $t['cookie_msg']; ?>
        </div>
        <button class="cookie-btn" onclick="acceptCookies()"><?php echo $t['cookie_btn']; ?></button>
    </div>

    <script>
        // --- JAVASCRIPT ΓΙΑ ΤΟ HAMBURGER MENU ---
        function toggleMenu() {
            document.getElementById('nav-menu').classList.toggle('active');
        }

        // --- COOKIE BANNER ---
        document.addEventListener("DOMContentLoaded", function () {
            if (!localStorage.getItem("stp_cookies_accepted")) {
                setTimeout(() => {
                    document.getElementById("cookieBanner").classList.add("show");
                }, 1000);
            }
        });

        function acceptCookies() {
            localStorage.setItem("stp_cookies_accepted", "true");
            document.getElementById("cookieBanner").classList.remove("show");
        }

        // --- ΑΥΤΟΜΑΤΗ & ΧΕΙΡΟΚΙΝΗΤΗ ΕΝΑΛΛΑΓΗ ΕΠΟΧΩΝ (SUMMER / WINTER) ---
        let currentSeason = 'summer';
        let autoSwitchTimer;

        function switchSeason(season, isManual = false) {
            currentSeason = season;

            document.getElementById('btn-summer').classList.remove('active');
            document.getElementById('btn-winter').classList.remove('active');
            document.getElementById('grid-summer').classList.remove('active-grid');
            document.getElementById('grid-winter').classList.remove('active-grid');

            if (season === 'summer') {
                document.getElementById('btn-summer').classList.add('active');
                document.getElementById('grid-summer').classList.add('active-grid');
            } else {
                document.getElementById('btn-winter').classList.add('active');
                document.getElementById('grid-winter').classList.add('active-grid');
            }

            if (isManual) {
                clearInterval(autoSwitchTimer);
                startAutoSwitch();
            }
        }

        function startAutoSwitch() {
            autoSwitchTimer = setInterval(() => {
                let nextSeason = (currentSeason === 'summer') ? 'winter' : 'summer';
                switchSeason(nextSeason, false);
            }, 5000);
        }

        document.addEventListener("DOMContentLoaded", function () {
            startAutoSwitch();
        });
    </script>
</body>

</html>