<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: ../auth/login.php"); exit(); }

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$days = isset($_GET['days']) ? intval($_GET['days']) : 1;
$persons = isset($_GET['persons']) ? intval($_GET['persons']) : 1; 
$budget = isset($_GET['budget']) ? floatval($_GET['budget']) : 0;

// --- ΣΩΣΤΗ ΔΙΑΧΕΙΡΙΣΗ ΔΙΓΛΩΣΣΙΑΣ ΜΕΣΩ SESSION ---
if (isset($_GET['lang']) && in_array($_GET['lang'], ['gr', 'en'])) {
    $_SESSION['lang'] = $_GET['lang'];
}
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'gr';

$translations = [
    'gr' => [
        'page_title' => 'Βήμα 1: Διαμονή | Smart Travel Planner',
        'cancel' => '✕ ΑΚΥΡΩΣΗ',
        'step1' => 'Διαμονη',
        'step2' => 'Μεταβαση',
        'step3' => 'Πληρωμη',
        'booking_title' => 'Κρατηση: ',
        'section1' => 'Επιλογη Δωματιων',
        'room_label' => 'Επιλέξτε διάταξη κρεβατιών για',
        'persons' => 'άτομα:',
        'section2' => 'Διαθεσιμες Ημερομηνιες',
        'days_label' => 'ΗΜΕΡΕΣ',
        'section3' => 'Επιλογη Ξενοδοχειου',
        'options_label' => 'επιλογές',
        'view_map' => '📍 Προβολη Χαρτη',
        'more_info' => 'Περισσοτερες Πληροφοριες',
        'website' => '🌐 Ιστοσελιδα Καταλυματος',
        'nights_label' => 'ΔΙΑΝΥΚΤΕΡΕΥΣΕΙΣ',
        'final_price' => 'ΤΕΛΙΚΗ ΤΙΜΗ ΓΙΑ',
        'rooms_label' => 'ΔΩΜΑΤΙΑ',
        'continue_btn' => 'ΣΥΝΕΧΕΙΑ ΣΤΑ ΜΕΤΑΦΟΡΙΚΑ ➔',
        'no_hotels' => 'Δεν βρέθηκαν καταλύματα στη βάση δεδομένων για τον προορισμό: ',
        'add_hotels' => 'Παρακαλώ προσθέστε ξενοδοχεία στον πίνακα hotels στο phpMyAdmin.',
        'return' => 'Επιστροφή',
        'js_sold_out' => 'SOLD OUT',
        'js_unavailable_date' => 'ΜΗ ΔΙΑΘΕΣΙΜΟ ΣΕ ΑΥΤΗ ΤΗΝ ΗΜΕΡΟΜΗΝΙΑ',
        'js_no_availability' => 'ΔΕΝ ΥΠΑΡΧΕΙ ΔΙΑΘΕΣΙΜΟΤΗΤΑ'
    ],
    'en' => [
        'page_title' => 'Step 1: Accommodation | Smart Travel Planner',
        'cancel' => '✕ CANCEL',
        'step1' => 'Accommodation',
        'step2' => 'Transport',
        'step3' => 'Payment',
        'booking_title' => 'Booking: ',
        'section1' => 'Room Selection',
        'room_label' => 'Select bed arrangement for',
        'persons' => 'persons:',
        'section2' => 'Available Dates',
        'days_label' => 'DAYS',
        'section3' => 'Hotel Selection',
        'options_label' => 'options',
        'view_map' => '📍 View Map',
        'more_info' => 'More Information',
        'website' => 'Property Website',
        'nights_label' => 'NIGHTS',
        'final_price' => 'FINAL PRICE FOR',
        'rooms_label' => 'ROOMS',
        'continue_btn' => 'CONTINUE TO TRANSPORT ➔',
        'no_hotels' => 'No properties found in the database for destination: ',
        'add_hotels' => 'Please add hotels to the hotels table in phpMyAdmin.',
        'return' => 'Return',
        'js_sold_out' => 'SOLD OUT',
        'js_unavailable_date' => 'UNAVAILABLE FOR THIS DATE',
        'js_no_availability' => 'NO AVAILABILITY'
    ]
];
$t = $translations[$lang];

$host = 'localhost'; $db = 'smart_travel_planner'; $user = 'root'; $pass = ''; 
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT * FROM destinations WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $dest = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$dest) { die("Προορισμός δεν βρέθηκε."); }

    $dest_name_gr = $dest['name_gr'];
    $dest_name = ($lang == 'en') ? $dest['name_en'] : $dest['name_gr']; 

    // --- ΞΕΝΟΔΟΧΕΙΑ (Διαβάζει ΑΠΟΚΛΕΙΣΤΙΚΑ από τη βάση σου) ---
    $stmt_h = $pdo->prepare("SELECT * FROM hotels WHERE destination_id = :id ORDER BY price_per_night ASC");
    $stmt_h->execute(['id' => $id]);
    $hotels = $stmt_h->fetchAll(PDO::FETCH_ASSOC);

    // Αν δεν έχεις περάσει ακόμα ξενοδοχεία για αυτόν τον προορισμό στη βάση, βγάζει μήνυμα αντί να βάλει λάθος εικόνες!
    if (empty($hotels)) {
        die("<div style='text-align:center; margin-top:100px; font-family:sans-serif;'><h2>" . $t['no_hotels'] . htmlspecialchars($dest_name) . "</h2><p>" . $t['add_hotels'] . "</p><a href='javascript:history.back()' style='display:inline-block; margin-top:20px; padding:10px 20px; background:#3b82f6; color:white; text-decoration:none; border-radius:8px;'>" . $t['return'] . "</a></div>");
    }

} catch(PDOException $e) { die("Σφάλμα: " . $e->getMessage()); }

$total_hotels = count($hotels);
$rooms_needed = ceil($persons / 2);
$room_options = [];
if ($lang == 'gr') {
    switch ($persons) {
        case 1: $room_options = ["1 Μονό Δωμάτιο"]; break;
        case 2: $room_options = ["1 Διπλό Κρεβάτι", "2 Ανεξάρτητα Μονά Κρεβάτια"]; break;
        case 3: $room_options = ["1 Τρίκλινο (Διπλό + Μονό)", "1 Δίκλινο + 1 Μονό (2 Δωμάτια)"]; break;
        case 4: $room_options = ["1 Τετράκλινο (Οικογενειακό)", "2 Δίκλινα Δωμάτια"]; break;
        case 5: $room_options = ["1 Τρίκλινο + 1 Δίκλινο", "1 Οικογενειακό + 1 Μονό"]; break;
        case 6: $room_options = ["2 Τρίκλινα Δωμάτια", "3 Δίκλινα Δωμάτια"]; break;
        case 7: $room_options = ["2 Τρίκλινα + 1 Μονό", "1 Τετράκλινο + 1 Τρίκλινο"]; break;
        case 8: $room_options = ["2 Τετράκλινα Δωμάτια", "4 Δίκλινα Δωμάτια"]; break;
        default: $room_options = ["Επιλογή βάσει διαθεσιμότητας"];
    }
} else {
    switch ($persons) {
        case 1: $room_options = ["1 Single Room"]; break;
        case 2: $room_options = ["1 Double Bed", "2 Twin Beds"]; break;
        case 3: $room_options = ["1 Triple (Double + Single)", "1 Double + 1 Single (2 Rooms)"]; break;
        case 4: $room_options = ["1 Quadruple (Family)", "2 Double Rooms"]; break;
        case 5: $room_options = ["1 Triple + 1 Double", "1 Family + 1 Single"]; break;
        case 6: $room_options = ["2 Triple Rooms", "3 Double Rooms"]; break;
        case 7: $room_options = ["2 Triple + 1 Single", "1 Quadruple + 1 Triple"]; break;
        case 8: $room_options = ["2 Quadruple Rooms", "4 Double Rooms"]; break;
        default: $room_options = ["Subject to availability"];
    }
}

function format_date_parts($date_str, $lang) {
    $ts = strtotime($date_str);
    if ($lang == 'gr') {
        $months = ['Jan'=>'ΙΑΝ', 'Feb'=>'ΦΕΒ', 'Mar'=>'ΜΑΡ', 'Apr'=>'ΑΠΡ', 'May'=>'ΜΑΙ', 'Jun'=>'ΙΟΥΝ', 'Jul'=>'ΙΟΥΛ', 'Aug'=>'ΑΥΓ', 'Sep'=>'ΣΕΠ', 'Oct'=>'ΟΚΤ', 'Nov'=>'ΝΟΕ', 'Dec'=>'ΔΕΚ'];
        $days = ['Sunday'=>'Κυριακη', 'Monday'=>'Δευτερα', 'Tuesday'=>'Τριτη', 'Wednesday'=>'Τεταρτη', 'Thursday'=>'Πεμπτη', 'Friday'=>'Παρασκευη', 'Saturday'=>'Σαββατο'];
    } else {
        $months = ['Jan'=>'JAN', 'Feb'=>'FEB', 'Mar'=>'MAR', 'Apr'=>'APR', 'May'=>'MAY', 'Jun'=>'JUN', 'Jul'=>'JUL', 'Aug'=>'AUG', 'Sep'=>'SEP', 'Oct'=>'OCT', 'Nov'=>'NOV', 'Dec'=>'DEC'];
        $days = ['Sunday'=>'Sunday', 'Monday'=>'Monday', 'Tuesday'=>'Tuesday', 'Wednesday'=>'Wednesday', 'Thursday'=>'Thursday', 'Friday'=>'Friday', 'Saturday'=>'Saturday'];
    }
    return ['day' => date('d', $ts), 'month' => $months[date('M', $ts)], 'day_name' => $days[date('l', $ts)]];
}

$season = $dest['best_season_gr']; 
$preset_dates = [];

if ($season == 'Καλοκαίρι') { 
    $preset_dates = [
        ["date" => "2026-05-20", "name_gr" => "🌼 EARLY SEASON (ΜΑΙΟΣ)", "name_en" => "🌼 EARLY SEASON (MAY)", "mult" => 0.8],
        ["date" => "2026-06-15", "name_gr" => "☀️ ΙΟΥΝΙΟΣ (ΙΔΑΝΙΚΑ)", "name_en" => "☀️ JUNE (IDEAL)", "mult" => 1.0],
        ["date" => "2026-07-10", "name_gr" => "🏖️ ΜΕΣΑ ΙΟΥΛΙΟΥ", "name_en" => "🏖️ MID JULY", "mult" => 1.3],
        ["date" => "2026-07-25", "name_gr" => "🔥 ΚΑΛΟΚΑΙΡΙΝΗ ΑΙΧΜΗ", "name_en" => "🔥 SUMMER PEAK", "mult" => 1.5],
        ["date" => "2026-08-10", "name_gr" => "⛪ ΕΒΔΟΜΑΔΑ 15ΑΥΓΟΥΣΤΟΥ", "name_en" => "⛪ AUGUST 15TH WEEK", "mult" => 1.6],
        ["date" => "2026-08-22", "name_gr" => "🌊 LATE AUGUST", "name_en" => "🌊 LATE AUGUST", "mult" => 1.4],
        ["date" => "2026-09-05", "name_gr" => "😎 ΑΡΧΕΣ ΣΕΠΤΕΜΒΡΗ", "name_en" => "😎 EARLY SEPTEMBER", "mult" => 1.0],
        ["date" => "2026-09-20", "name_gr" => "🍂 ΦΘΙΝΟΠΩΡΙΝΗ ΔΡΟΣΙΑ", "name_en" => "🍂 AUTUMN BREEZE", "mult" => 0.85],
        ["date" => "2026-10-10", "name_gr" => "💸 OFF-SEASON ΕΥΚΑΙΡΙΑ", "name_en" => "💸 OFF-SEASON DEAL", "mult" => 0.75]
    ];
} elseif ($season == 'Χειμώνας') { 
    $preset_dates = [
        ["date" => "2026-11-10", "name_gr" => "🍂 ΦΘΙΝΟΠΩΡΙΝΗ ΕΞΟΡΜΗΣΗ", "name_en" => "🍂 AUTUMN GETAWAY", "mult" => 0.8],
        ["date" => "2026-12-05", "name_gr" => "🏔️ EARLY WINTER", "name_en" => "🏔️ EARLY WINTER", "mult" => 1.0],
        ["date" => "2026-12-18", "name_gr" => "⛷️ PRE-CHRISTMAS", "name_en" => "⛷️ PRE-CHRISTMAS", "mult" => 1.2],
        ["date" => "2026-12-24", "name_gr" => "🎄 ΗΜΕΡΕΣ ΧΡΙΣΤΟΥΓΕΝΝΩΝ", "name_en" => "🎄 CHRISTMAS DAYS", "mult" => 1.6],
        ["date" => "2026-12-28", "name_gr" => "✨ ΜΕΤΑΞΥ ΕΟΡΤΩΝ", "name_en" => "✨ HOLIDAY SEASON", "mult" => 1.5],
        ["date" => "2027-01-03", "name_gr" => "🎆 ΑΡΧΕΣ ΙΑΝΟΥΑΡΙΟΥ", "name_en" => "🎆 EARLY JANUARY", "mult" => 1.3],
        ["date" => "2027-01-20", "name_gr" => "☕ ΜΕΣΑ ΧΕΙΜΩΝΑ", "name_en" => "☕ MID WINTER", "mult" => 1.0],
        ["date" => "2027-02-15", "name_gr" => "❄️ LATE WINTER", "name_en" => "❄️ LATE WINTER", "mult" => 0.85],
        ["date" => "2027-03-10", "name_gr" => "💸 OFF-SEASON ΕΥΚΑΙΡΙΑ", "name_en" => "💸 OFF-SEASON DEAL", "mult" => 0.75]
    ];
} else { 
    $preset_dates = [
        ["date" => "2026-04-15", "name_gr" => "🌼 ΑΝΟΙΞΙΑΤΙΚΗ ΑΠΟΔΡΑΣΗ", "name_en" => "🌼 SPRING GETAWAY", "mult" => 1.0],
        ["date" => "2026-05-25", "name_gr" => "☀️ EARLY SUMMER", "name_en" => "☀️ EARLY SUMMER", "mult" => 1.0],
        ["date" => "2026-07-20", "name_gr" => "🔥 ΚΑΛΟΚΑΙΡΙΝΗ ΑΙΧΜΗ", "name_en" => "🔥 SUMMER PEAK", "mult" => 1.4],
        ["date" => "2026-09-10", "name_gr" => "🍂 ΦΘΙΝΟΠΩΡΙΝΗ ΕΞΟΡΜΗΣΗ", "name_en" => "🍂 AUTUMN GETAWAY", "mult" => 1.0],
        ["date" => "2026-10-25", "name_gr" => "🇬🇷 ΕΘΝΙΚΗ ΕΟΡΤΗ", "name_en" => "🇬🇷 NATIONAL HOLIDAY", "mult" => 1.3],
        ["date" => "2026-11-20", "name_gr" => "🏔️ EARLY WINTER", "name_en" => "🏔️ EARLY WINTER", "mult" => 0.85],
        ["date" => "2026-12-28", "name_gr" => "✨ ΕΟΡΤΑΣΤΙΚΗ ΠΕΡΙΟΔΟΣ", "name_en" => "✨ HOLIDAY SEASON", "mult" => 1.6],
        ["date" => "2027-02-10", "name_gr" => "❄️ ΜΕΣΑ ΧΕΙΜΩΝΑ", "name_en" => "❄️ MID WINTER", "mult" => 0.8],
        ["date" => "2027-03-15", "name_gr" => "💸 OFF-SEASON ΕΥΚΑΙΡΙΑ", "name_en" => "💸 OFF-SEASON DEAL", "mult" => 0.75]
    ];
}

$dates_list = [];
foreach ($preset_dates as $pd) {
    $offset_days = ($id % 5) - 2; 
    $start_ts = strtotime($pd['date'] . " $offset_days days");
    $start = date('Y-m-d', $start_ts);
    $end = date('Y-m-d', strtotime($start . ' + ' . $days . ' days'));
    
    $multiplier = $pd['mult'];
    if ($multiplier >= 1.5) { $tag_color = '#ef4444'; } 
    elseif ($multiplier >= 1.2) { $tag_color = '#f59e0b'; } 
    elseif ($multiplier >= 1.0) { $tag_color = '#0284c7'; } 
    else { $tag_color = '#10b981'; } 

    $sold_out_indices = [];
    $max_out = ($multiplier >= 1.5) ? 5 : (($multiplier >= 1.3) ? 3 : (($multiplier >= 1.2) ? 1 : 0));
    $max_out = min($max_out, $total_hotels - 1); 
    
    $hash = crc32($start . $dest_name_gr);
    $av_idx = range(0, $total_hotels - 1);
    
    for ($i = count($av_idx) - 1; $i > 0; $i--) {
        $j = ($hash + $i) % ($i + 1);
        $temp = $av_idx[$i]; $av_idx[$i] = $av_idx[$j]; $av_idx[$j] = $temp;
    }
    
    $num_out = ($max_out > 0) ? ($hash % ($max_out + 1)) : 0;
    $sold_out_indices = array_slice($av_idx, 0, $num_out);

    $dates_list[] = [
        'value' => $start . '|' . $end, 
        'in' => format_date_parts($start, $lang), 
        'out' => format_date_parts($end, $lang), 
        'multiplier' => $multiplier, 
        'season_tag' => ($lang == 'en') ? $pd['name_en'] : $pd['name_gr'], 
        'tag_color' => $tag_color,
        'sold_out' => implode(',', $sold_out_indices)
    ];
}
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $t['page_title']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #0f172a; --secondary: #2563eb; --bg-color: #f8fafc; --text-main: #334155; --text-muted: #64748b; --success: #10b981; --border: #cbd5e1;}
        body { margin: 0; font-family: 'Inter', sans-serif; background-color: var(--bg-color); color: var(--text-main); min-height: 100vh;}
        
        /* CHECKOUT HEADER (Minimal, χωρίς μενού, μόνο Ακύρωση) */
        header { display: flex; align-items: center; justify-content: space-between; padding: 15px 5%; background: var(--primary); border-bottom: 1px solid rgba(255, 255, 255, 0.08); position: sticky; width: 100%; top: 0; z-index: 1000; box-sizing: border-box;}
        .brand { display: flex; align-items: center; gap: 15px; text-decoration: none;}
        .brand h2 { margin: 0; font-size: 20px; font-weight: 900; background: linear-gradient(to right, #ffffff, #bae6fd); -webkit-background-clip: text; -webkit-text-fill-color: transparent;}
        
        .btn-cancel { color: #e2e8f0; text-decoration: none; font-size: 13px; font-weight: 700; border: 1px solid rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 8px; transition: 0.3s; display: flex; align-items: center; gap: 5px;}
        .btn-cancel:hover { background: rgba(239, 68, 68, 0.15); border-color: rgba(239, 68, 68, 0.4); color: #fca5a5;}

        .wrapper { max-width: 1050px; margin: 40px auto; background: white; padding: 45px; border-radius: 20px; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08); border-top: 5px solid var(--secondary);}
        
        .wizard-nav { display: flex; align-items: center; justify-content: center; gap: 15px; margin-bottom: 40px; background: #f1f5f9; padding: 15px; border-radius: 12px;}
        .wizard-step { display: flex; align-items: center; gap: 10px; font-weight: 700; font-size: 15px;}
        .step-active { color: var(--secondary); }
        .step-active .w-num { background: var(--secondary); color: white; border: 2px solid var(--secondary);}
        .step-pending { color: var(--text-muted); opacity: 0.6;}
        .step-pending .w-num { border: 2px solid var(--text-muted); color: var(--text-muted);}
        .w-num { width: 26px; height: 26px; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-size: 13px;}
        .w-line { width: 50px; height: 2px; background: #cbd5e1; }

        .page-title { color: var(--primary); margin-top: 0; font-size: 28px; font-weight: 800; border-bottom: 2px solid #e2e8f0; padding-bottom: 15px; margin-bottom: 30px;}
        .section-title { font-size: 18px; font-weight: 800; color: var(--primary); margin-bottom: 20px; margin-top: 45px; display: flex; align-items: center; gap: 10px;}
        .section-title span { background: var(--secondary); color: white; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; border-radius: 8px; font-size: 14px;}

        input[type="radio"] { transform: scale(1.4); accent-color: var(--secondary); cursor: pointer;}

        .room-config { background: #f8fafc; border: 1px solid var(--border); padding: 25px; border-radius: 12px; margin-bottom: 30px;}
        .room-config label { font-weight: 800; color: var(--primary); font-size: 15px; display: block; margin-bottom: 12px;}
        .room-config select { width: 100%; padding: 14px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 15px; font-family: 'Inter'; outline: none; background: white; color: var(--primary); font-weight: 600; cursor: pointer;}
        .room-config select:focus { border-color: var(--secondary); box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);}

        .dates-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 18px; }
        .ticket-card { display: flex; flex-direction: column; border: 2px solid var(--border); border-radius: 14px; padding: 18px 20px; cursor: pointer; transition: all 0.2s ease; background: white;}
        .ticket-card:hover { border-color: #93c5fd; box-shadow: 0 6px 15px rgba(37,99,235,0.05); transform: translateY(-2px);}
        .ticket-card:has(input:checked) { border-color: var(--secondary); background: #eff6ff; box-shadow: 0 0 0 1px var(--secondary); }
        .season-badge { text-align: center; color: white; padding: 5px 8px; font-size: 11px; font-weight: 700; border-radius: 8px; margin-bottom: 12px; letter-spacing: 0.5px;}
        .ticket-main { display: flex; align-items: center; }
        .ticket-main input { margin-right: 15px; }
        .ticket-content { flex: 1; display: flex; align-items: center; justify-content: space-between; }
        .t-date { text-align: center; min-width: 60px;}
        .t-month { display: block; font-size: 12px; font-weight: 800; color: #ef4444; letter-spacing: 1px;}
        .t-day { display: block; font-size: 30px; font-weight: 900; color: var(--primary); margin: 2px 0; line-height: 1;}
        .t-name { display: block; font-size: 12px; font-weight: 600; color: var(--text-muted);}
        .t-divider { flex: 1; display: flex; flex-direction: column; align-items: center; position: relative; margin: 0 15px;}
        .t-divider::before { content: ''; position: absolute; top: 50%; left: 0; right: 0; height: 2px; background: repeating-linear-gradient(to right, var(--border) 0, var(--border) 5px, transparent 5px, transparent 10px); z-index: 1; }
        .t-duration { position: relative; z-index: 2; background: white; padding: 4px 12px; border-radius: 12px; font-size: 11px; font-weight: 700; color: var(--text-muted); border: 1px solid var(--border);}

        /* ΚΑΡΤΕΣ ΞΕΝΟΔΟΧΕΙΩΝ - ΜΕ ΥΠΟΣΤΗΡΙΞΗ SOLD OUT */
        .hotel-card { display: flex; border: 2px solid var(--border); border-radius: 16px; margin-bottom: 25px; background: white; transition: 0.3s; cursor: pointer; overflow: hidden;}
        .hotel-card:hover { border-color: #93c5fd; box-shadow: 0 8px 20px rgba(15,23,42,0.08); transform: translateY(-2px);}
        .hotel-card:has(input:checked) { border-color: var(--secondary); background: #eff6ff; box-shadow: 0 0 0 1px var(--secondary); }
        
        /* SOLD OUT ΚΑΤΑΣΤΑΣΗ */
        .hotel-sold-out { opacity: 0.55; pointer-events: none; filter: grayscale(50%); cursor: not-allowed !important; border-color: #e2e8f0;}
        .hotel-sold-out .h-badge { background: #ef4444 !important; color: white !important; }

        .hotel-img { width: 300px; min-height: 250px; position: relative; flex-shrink: 0; border-right: 1px solid #e2e8f0;}
        .hotel-img img { width: 100%; height: 100%; object-fit: cover; }
        .h-badge { position: absolute; top: 15px; left: 15px; background: rgba(15, 23, 42, 0.85); color: white; padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 600; letter-spacing: 0.5px; transition: 0.3s;}
        
        .hotel-body { padding: 30px 25px; flex: 1; display: flex; flex-direction: column; justify-content: flex-start;}
        .hotel-body h3 { margin: 0 0 10px 0; font-size: 22px; font-weight: 800; color: var(--primary); display: flex; align-items: center; justify-content: space-between;}
        .stars { color: #f59e0b; font-size: 14px; letter-spacing: 1.5px;}
        
        .map-link { display: inline-flex; align-items: center; gap: 6px; background: #f1f5f9; color: var(--secondary); padding: 8px 14px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 13.5px; margin-bottom: 18px; width: fit-content; transition: 0.2s; border: 1px solid #e2e8f0;}
        .map-link:hover { background: #e2e8f0; color: #1d4ed8;}
        
        .h-amenities { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 20px;}
        .amenity-badge { background: white; color: #475569; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; border: 1px solid #cbd5e1; box-shadow: 0 1px 2px rgba(0,0,0,0.02);}
        
        details { background: #f8fafc; padding: 15px 20px; border-radius: 10px; font-size: 13.5px; color: #475569; border: 1px solid var(--border); margin-top: auto; line-height: 1.6;}
        summary { font-weight: 700; color: var(--primary); cursor: pointer; outline: none; margin-bottom: 10px;}
        .contact-info { margin-top: 15px; padding-top: 15px; border-top: 1px dashed var(--border); display: flex; flex-wrap: wrap; gap: 20px;}
        .contact-info a { color: var(--secondary); text-decoration: none; font-weight: 600;}
        
        .hotel-price-sec { width: 220px; border-left: 2px solid var(--border); padding: 30px; display: flex; flex-direction: column; align-items: center; justify-content: center; background: #f8fafc; transition: 0.3s;}
        .hotel-price-sec input { margin-bottom: 20px; margin-right: 0; transform: scale(1.6);}
        .price-num { font-size: 30px; font-weight: 900; color: var(--primary); transition: 0.3s; line-height: 1;}
        .price-note { font-size: 11px; font-weight: 700; color: var(--text-muted); text-align: center; margin-top: 10px;}
        .nights-badge { background: #e0f2fe; color: #1d4ed8; padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 800; margin-bottom: 8px;}

        .btn-submit { display: block; width: 100%; background: var(--secondary); color: white; border: none; padding: 22px; border-radius: 14px; font-size: 17px; font-weight: 800; cursor: pointer; margin-top: 40px; transition: 0.3s; box-shadow: 0 10px 20px rgba(37, 99, 235, 0.2); letter-spacing: 0.5px;}
        .btn-submit:hover { transform: translateY(-3px); box-shadow: 0 15px 25px rgba(37, 99, 235, 0.3); background: #1d4ed8; }

        /* =========================================================
           📱 RESPONSIVE ΓΙΑ CHECKOUT & MOBILE
           ========================================================= */
        @media (max-width: 950px) { 
            .wrapper { margin: 20px; padding: 30px; }
            .hotel-card { flex-direction: column; } 
            .hotel-img { width: 100%; height: 250px; border-right: none; border-bottom: 1px solid #e2e8f0;} 
            .hotel-price-sec { width: 100%; border-left: none; border-top: 2px solid var(--border); flex-direction: row; justify-content: space-between; padding: 20px 25px;} 
            .hotel-price-sec input { margin-bottom: 0; margin-right: 15px;} 
            .wizard-nav { flex-direction: column; gap: 10px;} 
            .w-line { display: none;} 
        }

        @media (max-width: 600px) {
            header { padding: 15px; flex-direction: row; }
            .brand h2 { font-size: 18px; }
            .brand svg { width: 30px; height: 30px; }
            .btn-cancel { padding: 8px 12px; font-size: 12px; }

            .wrapper { margin: 15px 10px; padding: 20px 15px; border-radius: 16px; border-top-width: 4px;}
            .page-title { font-size: 22px; margin-bottom: 20px; }
            
            .wizard-nav { flex-direction: row; flex-wrap: wrap; justify-content: center; gap: 10px; padding: 12px; margin-bottom: 25px;}
            .wizard-step { font-size: 13px; }
            .w-num { width: 22px; height: 22px; font-size: 11px; }

            .section-title { font-size: 16px; margin-top: 30px; margin-bottom: 15px; }
            .room-config { padding: 15px; }
            .room-config label { font-size: 13px; }
            .room-config select { font-size: 14px; padding: 12px; }

            .dates-grid { grid-template-columns: 1fr; gap: 12px; }
            .ticket-card { padding: 15px; }
            .t-day { font-size: 24px; }
            .t-duration { font-size: 10px; padding: 3px 8px; }

            .hotel-body { padding: 20px 15px; }
            .hotel-body h3 { font-size: 18px; flex-direction: column; align-items: flex-start; gap: 5px; }
            .map-link { font-size: 12px; padding: 6px 12px; }
            
            .hotel-price-sec { padding: 15px; flex-wrap: wrap; gap: 10px;}
            .price-num { font-size: 24px; }
            .nights-badge { font-size: 10px; padding: 4px 8px; }
            .price-note { font-size: 10px; }

            .btn-submit { font-size: 15px; padding: 18px; margin-top: 30px; }
        }
    </style>
</head>
<body>

<header>
    <div class="brand">
        <svg width="40" height="40" viewBox="0 0 50 50" fill="none">
            <rect x="3" y="3" width="44" height="44" rx="12" fill="rgba(255,255,255,0.1)" stroke="#3b82f6" stroke-width="2"/>
            <text x="25" y="26" font-family="'Inter', sans-serif" font-weight="900" font-size="16" fill="#ffffff" text-anchor="middle" dominant-baseline="middle">STP</text>
        </svg>
        <h2>Smart Travel Planner</h2>
    </div>
    <a href="destination.php?id=<?php echo $id; ?>&days=<?php echo $days; ?>&persons=<?php echo $persons; ?>&budget=<?php echo $budget; ?>&lang=<?php echo $lang; ?>" class="btn-cancel">
        <?php echo $t['cancel']; ?>
    </a>
</header>

<div class="wrapper">
    <div class="wizard-nav">
        <div class="wizard-step step-active"><div class="w-num">1</div> <?php echo $t['step1']; ?></div>
        <div class="w-line"></div>
        <div class="wizard-step step-pending"><div class="w-num">2</div> <?php echo $t['step2']; ?></div>
        <div class="w-line"></div>
        <div class="wizard-step step-pending"><div class="w-num">3</div> <?php echo $t['step3']; ?></div>
    </div>

    <h1 class="page-title"><?php echo $t['booking_title'] . htmlspecialchars($dest_name); ?></h1>
    
    <form action="transport.php?id=<?php echo $id; ?>&days=<?php echo $days; ?>&persons=<?php echo $persons; ?>&budget=<?php echo $budget; ?>" method="POST">
        
        <div class="section-title"><span>1</span> <?php echo $t['section1']; ?></div>
        <div class="room-config">
            <label><?php echo $t['room_label'] . " " . $persons . " " . $t['persons']; ?></label>
            <select name="room_type" required>
                <?php foreach($room_options as $option): ?>
                    <option value="<?php echo htmlspecialchars($option); ?>"><?php echo htmlspecialchars($option); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="section-title"><span>2</span> <?php echo $t['section2']; ?></div>
        <div class="dates-grid">
            <?php foreach($dates_list as $index => $dt): ?>
                <label class="ticket-card">
                    <div class="season-badge" style="background-color: <?php echo $dt['tag_color']; ?>;">
                        <?php echo $dt['season_tag']; ?>
                    </div>
                    
                    <div class="ticket-main">
                        <input type="radio" class="date-selector" name="selected_dates" 
                               value="<?php echo $dt['value']; ?>" 
                               data-multiplier="<?php echo $dt['multiplier']; ?>" 
                               data-soldout="<?php echo $dt['sold_out']; ?>"
                               <?php echo ($index==0)?'checked':''; ?> required>
                        
                        <div class="ticket-content">
                            <div class="t-date">
                                <span class="t-month"><?php echo $dt['in']['month']; ?></span>
                                <span class="t-day"><?php echo $dt['in']['day']; ?></span>
                                <span class="t-name"><?php echo $dt['in']['day_name']; ?></span>
                            </div>
                            <div class="t-divider">
                                <span class="t-duration"><?php echo $days; ?> <?php echo $t['days_label']; ?></span>
                            </div>
                            <div class="t-date">
                                <span class="t-month"><?php echo $dt['out']['month']; ?></span>
                                <span class="t-day"><?php echo $dt['out']['day']; ?></span>
                                <span class="t-name"><?php echo $dt['out']['day_name']; ?></span>
                            </div>
                        </div>
                    </div>
                </label>
            <?php endforeach; ?>
        </div>

        <div class="section-title"><span>3</span> <?php echo $t['section3']; ?> (<?php echo count($hotels); ?> <?php echo $t['options_label']; ?>)</div>
        
        <?php foreach ($hotels as $index => $hotel): 
            $base_hotel_cost = $hotel['price_per_night'] * $days * $rooms_needed;
            $hotel_stars = str_repeat('⭐', $hotel['stars']);
            $lat = isset($hotel['latitude']) ? $hotel['latitude'] : 37.9838;
            $lng = isset($hotel['longitude']) ? $hotel['longitude'] : 23.7275;
            $map_url = "https://www.google.com/maps/search/?api=1&query=" . $lat . "," . $lng;
            $amenities_arr = explode(',', $hotel['amenities']);
        ?>
            <label class="hotel-card" id="card_hotel_<?php echo $index; ?>">
                <div class="hotel-img">
                    <img src="<?php echo htmlspecialchars($hotel['image_url']); ?>" alt="Hotel">
                    <div class="h-badge" id="badge_<?php echo $index; ?>" data-original="<?php echo htmlspecialchars($hotel['category']); ?>"><?php echo htmlspecialchars($hotel['category']); ?></div>
                </div>
                
                <div class="hotel-body">
                    <h3><?php echo htmlspecialchars($hotel['hotel_name']); ?> <span style="font-size:13px; letter-spacing:1px; color:#f59e0b;"><?php echo $hotel_stars; ?></span></h3>
                    <a href="<?php echo $map_url; ?>" target="_blank" class="map-link" onclick="event.stopPropagation();"><?php echo $t['view_map']; ?></a>
                    
                    <div class="h-amenities">
                        <?php foreach($amenities_arr as $am): ?>
                            <span class="amenity-badge">✨ <?php echo htmlspecialchars(trim($am)); ?></span>
                        <?php endforeach; ?>
                    </div>
                    
                    <details onclick="event.stopPropagation();">
                        <summary><?php echo $t['more_info']; ?></summary>
                        <div style="margin-bottom: 10px;"><?php echo htmlspecialchars($hotel['description']); ?></div>
                        <div class="contact-info">
                            <div>📞 <?php echo htmlspecialchars($hotel['phone'] ?? '+30 210 0000000'); ?></div>
                            <div>🌐 <a href="<?php echo htmlspecialchars($hotel['website'] ?? '#'); ?>" target="_blank"><?php echo $t['website']; ?></a></div>
                        </div>
                    </details>
                </div>
                
                <div class="hotel-price-sec">
                    <input type="radio" class="hotel-selector" name="hotel_data" 
                           id="radio_hotel_<?php echo $index; ?>"
                           data-baseprice="<?php echo $base_hotel_cost; ?>" 
                           data-hotelname="<?php echo htmlspecialchars($hotel['hotel_name']); ?>" 
                           value="" <?php echo ($index==0)?'checked':''; ?> required>
                    <div>
                        <div class="nights-badge" id="nights_<?php echo $index; ?>">🌙 <?php echo $days . " " . $t['nights_label']; ?></div>
                        <div class="price-num" id="display_price_<?php echo $index; ?>">--€</div>
                        <div class="price-note" id="note_price_<?php echo $index; ?>"><?php echo $t['final_price']; ?><br><?php echo $rooms_needed . " " . $t['rooms_label']; ?></div>
                    </div>
                </div>
            </label>
        <?php endforeach; ?>

        <button type="submit" class="btn-submit" id="main_submit_btn">
            <?php echo $t['continue_btn']; ?>
        </button>
    </form>
</div>

<script>
// PHP variables for JS translations
const T_SOLD_OUT = <?php echo json_encode($t['js_sold_out']); ?>;
const T_UNAVAILABLE_DATE = <?php echo json_encode($t['js_unavailable_date']); ?>;
const T_NO_AVAILABILITY = <?php echo json_encode($t['js_no_availability']); ?>;
const T_CONTINUE_BTN = <?php echo json_encode($t['continue_btn']); ?>;
const T_FINAL_PRICE = <?php echo json_encode($t['final_price']); ?>;
const T_ROOMS = <?php echo json_encode($t['rooms_label']); ?>;
const ROOMS_NEEDED = <?php echo $rooms_needed; ?>;

document.addEventListener("DOMContentLoaded", function() {
    const dateRadios = document.querySelectorAll('.date-selector');
    const hotelInputs = document.querySelectorAll('.hotel-selector');
    const submitBtn = document.getElementById('main_submit_btn');
    
    function updatePricesAndAvailability() {
        let selectedDate = document.querySelector('.date-selector:checked');
        if(!selectedDate) return;
        
        let multiplier = parseFloat(selectedDate.getAttribute('data-multiplier'));
        let soldOutAttr = selectedDate.getAttribute('data-soldout');
        let soldOutArr = soldOutAttr ? soldOutAttr.split(',').map(Number) : [];

        let firstAvailableIndex = -1;
        let currentSelectedSoldOut = false;

        hotelInputs.forEach((input, index) => {
            let card = document.getElementById('card_hotel_' + index);
            let display = document.getElementById('display_price_' + index);
            let note = document.getElementById('note_price_' + index);
            let badge = document.getElementById('badge_' + index);
            let nights = document.getElementById('nights_' + index);

            if (soldOutArr.includes(index)) {
                card.classList.add('hotel-sold-out');
                input.disabled = true;
                
                if (input.checked) currentSelectedSoldOut = true;
                
                display.innerText = T_SOLD_OUT;
                display.style.color = '#ef4444';
                note.innerText = T_UNAVAILABLE_DATE;
                badge.innerText = T_SOLD_OUT;
                nights.style.display = 'none';
                
            } else {
                card.classList.remove('hotel-sold-out');
                input.disabled = false;
                
                if (firstAvailableIndex === -1) firstAvailableIndex = index;
                
                let basePrice = parseFloat(input.getAttribute('data-baseprice'));
                let newPrice = (basePrice * multiplier).toFixed(2);
                
                display.innerText = newPrice + '€';
                note.innerHTML = T_FINAL_PRICE + '<br>' + ROOMS_NEEDED + ' ' + T_ROOMS;
                badge.innerText = badge.getAttribute('data-original');
                nights.style.display = 'inline-block';
                
                if (multiplier >= 1.5) display.style.color = '#ef4444'; 
                else if (multiplier >= 1.2) display.style.color = '#f59e0b'; 
                else if (multiplier >= 1.0) display.style.color = '#0284c7'; 
                else display.style.color = '#10b981'; 
                
                input.value = input.getAttribute('data-hotelname') + '|' + newPrice;
            }
        });

        if (currentSelectedSoldOut && firstAvailableIndex !== -1) {
            hotelInputs[firstAvailableIndex].checked = true;
        }

        if (firstAvailableIndex === -1) {
            submitBtn.disabled = true;
            submitBtn.innerText = T_NO_AVAILABILITY;
            submitBtn.style.background = "#94a3b8";
        } else {
            submitBtn.disabled = false;
            submitBtn.innerText = T_CONTINUE_BTN;
            submitBtn.style.background = "var(--secondary)";
        }
    }

    updatePricesAndAvailability();
    dateRadios.forEach(radio => radio.addEventListener('change', updatePricesAndAvailability));
});
</script>

</body>
</html>