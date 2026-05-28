<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: ../auth/login.php"); exit(); }

if (!isset($_POST['selected_dates'])) {
    header("Location: ../index.php");
    exit();
}

// --- ΣΩΣΤΗ ΔΙΑΧΕΙΡΙΣΗ ΔΙΓΛΩΣΣΙΑΣ ΜΕΣΩ SESSION ---
if (isset($_GET['lang']) && in_array($_GET['lang'], ['gr', 'en'])) {
    $_SESSION['lang'] = $_GET['lang'];
}
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'gr';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$days = isset($_GET['days']) ? intval($_GET['days']) : 1;
$persons = isset($_GET['persons']) ? intval($_GET['persons']) : 1; 
$budget = isset($_GET['budget']) ? floatval($_GET['budget']) : 0;

$translations = [
    'gr' => [
        'page_title' => 'Βήμα 2: Μετάβαση | Smart Travel Planner',
        'back_btn' => '← ΠΙΣΩ',
        'step1' => 'Διαμονή',
        'step2' => 'Μετάβαση',
        'step3' => 'Checkout',
        'transport_title' => 'Επιλογή Μεταφορικού: ',
        'hotel_label' => 'Ξενοδοχείο',
        'acc_cost' => 'Κόστος Διαμονής',
        'section1' => '1. Μετάβαση προς ',
        'own_vehicle' => '❌ Με δικό μου όχημα (Οδικά)',
        'own_vehicle_desc' => 'Δεν θα χρειαστώ εισιτήριο μετάβασης.',
        'one_way_fwd' => 'Απλή Μετάβαση',
        'persons' => 'Άτομα',
        'section2' => '2. Επιστροφή από ',
        'no_return' => '❌ Χωρίς Επιστροφή (One-Way)',
        'no_return_desc' => 'Δεν επιθυμώ να κλείσω εισιτήριο επιστροφής.',
        'one_way_ret' => 'Απλή Επιστροφή',
        'section3' => '3. Ενοικίαση Αυτοκινήτου στον Προορισμό',
        'car_pickup' => 'Παραλαβή από τοπικό κατάστημα του προορισμού.',
        'car_avail' => 'Διαθέσιμο για όλη τη διάρκεια',
        'days' => 'Ημέρες',
        'temp_total' => 'Προσωρινό Σύνολο Κράτησης',
        'continue_btn' => 'Συνέχεια: Στοιχεία & Extras ➔',
        'dest_not_found' => 'Προορισμός δεν βρέθηκε.',
        'error' => 'Σφάλμα: '
    ],
    'en' => [
        'page_title' => 'Step 2: Transport | Smart Travel Planner',
        'back_btn' => '← BACK',
        'step1' => 'Accommodation',
        'step2' => 'Transport',
        'step3' => 'Checkout',
        'transport_title' => 'Transport Selection: ',
        'hotel_label' => 'Hotel',
        'acc_cost' => 'Accommodation Cost',
        'section1' => '1. Transport to ',
        'own_vehicle' => '❌ With my own vehicle (Road)',
        'own_vehicle_desc' => 'I will not need a transport ticket.',
        'one_way_fwd' => 'One-Way Ticket',
        'persons' => 'Persons',
        'section2' => '2. Return from ',
        'no_return' => '❌ No Return (One-Way)',
        'no_return_desc' => 'I do not wish to book a return ticket.',
        'one_way_ret' => 'One-Way Return Ticket',
        'section3' => '3. Car Rental at Destination',
        'car_pickup' => 'Pick up from a local branch at the destination.',
        'car_avail' => 'Available for the whole duration',
        'days' => 'Days',
        'temp_total' => 'Temporary Booking Total',
        'continue_btn' => 'Continue: Details & Extras ➔',
        'dest_not_found' => 'Destination not found.',
        'error' => 'Error: '
    ]
];
$t_lang = $translations[$lang];

$t_company = [
    'ΚΤΕΛ Υπεραστικό' => 'Intercity KTEL',
    'ΚΤΕΛ VIP Express' => 'KTEL VIP Express',
    'Ενοικίαση Οχήματος' => 'Car Rental',
    'Aegean Airlines' => 'Aegean Airlines',
    'Ryanair' => 'Ryanair',
    'Sky Express' => 'Sky Express',
    'Seajets' => 'Seajets',
    'Golden Star Ferries' => 'Golden Star Ferries',
    'Aegean Flying Dolphins' => 'Aegean Flying Dolphins',
    'Fast Ferries' => 'Fast Ferries',
    'Blue Star Ferries' => 'Blue Star Ferries',
    'Zante Ferries' => 'Zante Ferries',
    'Hellenic Seaways' => 'Hellenic Seaways',
    'Generic' => 'Generic'
];

$t_label = [
    'Οικονομική Πτήση' => 'Budget Flight',
    'Premium Πτήση' => 'Premium Flight',
    'Ταχύπλοο (Αριθμημένη Θέση)' => 'Fast Ferry (Reserved Seat)',
    'Συμβατικό Πλοίο (Οικονομικό)' => 'Conventional Ferry (Economy)',
    'Λεωφορείο ΚΤΕΛ' => 'KTEL Bus',
    'ΚΤΕΛ Express (Χωρίς Στάσεις)' => 'KTEL Express (No Stops)',
    'Ενοικίαση για όλο το ταξίδι' => 'Rental for the whole trip',
    'Απλή Μετάβαση' => 'One-Way Ticket'
];

$t_port = [
    'Αφετηρία' => 'Starting Point',
    'Αεροδρόμιο Ελ. Βενιζέλος (ATH)' => 'El. Venizelos Airport (ATH)',
    'Λιμάνι Ραφήνας' => 'Rafina Port',
    'Νέο Λιμάνι Τούρλου' => 'New Port of Tourlos',
    'Λιμάνι Πειραιά' => 'Piraeus Port',
    'Κεντρικό Λιμάνι' => 'Main Port',
    'Λιμάνι Αθηνιού' => 'Athinios Port',
    'Λιμάνι Βόλου' => 'Volos Port',
    'Λιμάνι Σκοπέλου' => 'Skopelos Port',
    'Λιμάνι Νησιού' => 'Island Port',
    'Λιμάνι Β. Αιγαίου' => 'North Aegean Port',
    'Σταθμός ΚΤΕΛ' => 'KTEL Station',
    'Παραλαβή από τοπικό κατάστημα' => 'Pick up from local store'
];

$host = 'localhost'; $db = 'smart_travel_planner'; $user = 'root'; $pass = ''; 
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT * FROM destinations WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $dest = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$dest) { die($t_lang['dest_not_found']); }
    $dest_name_gr = $dest['name_gr'];

} catch(PDOException $e) { die($t_lang['error'] . $e->getMessage()); }

$dest_name = ($lang == 'en') ? $dest['name_en'] : $dest['name_gr'];

$start_date_selected = explode('|', $_POST['selected_dates'])[0];
$ts_selected = strtotime($start_date_selected);
$m = (int)date('m', $ts_selected);
$d = (int)date('d', $ts_selected);

$multiplier = 1.0;
if ($m == 8 && $d <= 25) { $multiplier = 1.35; }
elseif ($m == 7 || $m == 8) { $multiplier = 1.25; }
else { $multiplier = 0.9; }
$t_multiplier = 1.0 + (($multiplier - 1.0) * 0.4);

$selected_dates_val = htmlspecialchars($_POST['selected_dates']);
$room_type_val = htmlspecialchars($_POST['room_type']);
$hotel_data_val = htmlspecialchars($_POST['hotel_data']);

$hotel_name_display = explode('|', $_POST['hotel_data'])[0];
$hotel_cost_display = explode('|', $_POST['hotel_data'])[1];

// =========================================================
// ΓΕΩΓΡΑΦΙΚΗ ΑΝΑΓΝΩΡΙΣΗ (ΠΡΟΣΤΕΘΗΚΑΝ ΟΙ 3 ΝΕΟΙ ΠΡΟΟΡΙΣΜΟΙ)
// =========================================================
$is_island_list = ['Ηράκλειο', 'Ηράκλειο (Κνωσός)', 'Χανιά', 'Ρόδος', 'Ρόδος (Παλιά Πόλη)', 'Σαντορίνη', 'Μύκονος', 'Πάρος', 'Σκιάθος', 'Κέρκυρα', 'Μήλος', 'Ίος', 'Σαμοθράκη', 'Ικαρία', 'Σύρος', 'Ζάκυνθος', 'Κεφαλονιά', 'Αλόννησος', 'Κως', 'Αστυπάλαια', 'Σύμη', 'Φολέγανδρος', 'Νάξος', 'Μυτιλήνη (Λέσβος)', 'Χίος', 'Σκόπελος'];

$has_airport = in_array($dest_name_gr, ['Ηράκλειο', 'Ηράκλειο (Κνωσός)', 'Χανιά', 'Ρόδος', 'Ρόδος (Παλιά Πόλη)', 'Σαντορίνη', 'Μύκονος', 'Πάρος', 'Σκιάθος', 'Κέρκυρα', 'Μήλος', 'Ικαρία', 'Θεσσαλονίκη', 'Αθήνα', 'Ιωάννινα', 'Ζάκυνθος', 'Κεφαλονιά', 'Κως', 'Καστοριά', 'Αστυπάλαια', 'Σύρος', 'Νάξος', 'Μυτιλήνη (Λέσβος)', 'Χίος']);

$has_ferry = in_array($dest_name_gr, $is_island_list);
$has_fast_ferry = in_array($dest_name_gr, ['Ηράκλειο', 'Ηράκλειο (Κνωσός)', 'Σαντορίνη', 'Μύκονος', 'Πάρος', 'Σκιάθος', 'Μήλος', 'Ίος', 'Σύρος', 'Ζάκυνθος', 'Αλόννησος', 'Κως', 'Φολέγανδρος', 'Νάξος', 'Ρόδος (Παλιά Πόλη)', 'Σκόπελος']);

$is_mainland = !in_array($dest_name_gr, $is_island_list) || $dest_name_gr == 'Λευκάδα' || $dest_name_gr == 'Εύβοια';

$transports = [];
if ($has_ferry) {
    $transports[] = ['type' => 'ferry_slow', 'price' => 40.00];
    if ($has_fast_ferry) { $transports[] = ['type' => 'ferry_fast', 'price' => 75.00]; }
}
if ($has_airport) {
    $transports[] = ['type' => 'flight_budget', 'price' => 60.00];
    $transports[] = ['type' => 'flight_premium', 'price' => 110.00];
}
if ($is_mainland) {
    $transports[] = ['type' => 'bus_std', 'price' => 25.00];
    if (!in_array($dest_name_gr, ['Ζαγοροχώρια', 'Νυμφαίο', 'Σαμοθράκη'])) {
        $transports[] = ['type' => 'bus_express', 'price' => 40.00];
    }
}
$transports[] = ['type' => 'car_rental', 'price' => 25.00];

// =========================================================
// ΜΗΧΑΝΗ ΔΡΟΜΟΛΟΓΙΩΝ: ΜΟΝΑΔΙΚΕΣ & ΡΕΑΛΙΣΤΙΚΕΣ ΩΡΕΣ!
// =========================================================
function getRealisticRouting($type, $dest, $is_return = false) {
    $company = "Generic"; $port = "Αφετηρία"; $arr_port = $dest; $icon = "🚗";
    $dur_h = 0; $dur_m = 0; $label = "Απλή Μετάβαση"; $is_car = false;
    
    // Αρχικές Ώρες
    $dep_h = 8; $dep_m = 0;

    switch($type) {
        case 'flight_budget':
            $icon = '✈️'; $label = "Οικονομική Πτήση";
            $company = in_array($dest, ['Σαντορίνη', 'Ρόδος', 'Χανιά', 'Μυτιλήνη (Λέσβος)']) ? 'Ryanair' : 'Sky Express';
            $port = 'Αεροδρόμιο Ελ. Βενιζέλος (ATH)'; 
            $dep_h = 6; $dep_m = 15; 
            $dur_h=0; $dur_m=50; 
            break;

        case 'flight_premium':
            $icon = '✈️'; $label = "Premium Πτήση"; $company = 'Aegean Airlines';
            $port = 'Αεροδρόμιο Ελ. Βενιζέλος (ATH)'; 
            $dep_h = 10; $dep_m = 40; 
            $dur_h=0; $dur_m=50;
            break;

        case 'ferry_fast':
            $icon = '🚤'; $label = "Ταχύπλοο (Αριθμημένη Θέση)";
            $dep_h = 8; $dep_m = 5; 
            
            $ferry_times = [
                'Μύκονος' => [2, 30], 'Άνδρος' => [1, 15], 'Σύρος' => [2, 0], 'Πάρος' => [2, 45], 'Νάξος' => [3, 20], 'Σαντορίνη' => [4, 45], 'Ίος' => [4, 0], 'Μήλος' => [3, 0], 'Φολέγανδρος' => [4, 0], 'Ηράκλειο' => [6, 30], 'Χανιά' => [6, 30], 'Σκιάθος' => [1, 25], 'Αλόννησος' => [2, 0], 'Κέρκυρα' => [1, 10], 'Ζάκυνθος' => [1, 0], 'Κεφαλονιά' => [1, 20], 'Ρόδος' => [13, 0], 'Κως' => [10, 0], 'Σκόπελος' => [2, 0]
            ];
            if(isset($ferry_times[$dest])) { $dur_h = $ferry_times[$dest][0]; $dur_m = $ferry_times[$dest][1]; }
            else { $dur_h = 4; $dur_m = 0; }

            if(in_array($dest, ['Μύκονος', 'Άνδρος'])) { $company = 'Seajets'; $port = 'Λιμάνι Ραφήνας'; $arr_port = 'Νέο Λιμάνι Τούρλου'; $dep_h = 7; $dep_m = 15; }
            elseif(in_array($dest, ['Πάρος', 'Νάξος', 'Σύρος'])) { $company = 'Seajets'; $port = 'Λιμάνι Πειραιά'; $arr_port = 'Κεντρικό Λιμάνι'; }
            elseif(in_array($dest, ['Σαντορίνη', 'Ίος'])) { $company = 'Golden Star Ferries'; $port = 'Λιμάνι Πειραιά'; $arr_port = 'Λιμάνι Αθηνιού'; $dep_h = 7; $dep_m = 45; }
            elseif($dest == 'Σκόπελος') { $company = 'Aegean Flying Dolphins'; $port = 'Λιμάνι Βόλου'; $arr_port = 'Λιμάνι Σκοπέλου'; $dep_h = 9; $dep_m = 0; }
            else { $company = 'Seajets'; $port = 'Λιμάνι Πειραιά'; $arr_port = 'Λιμάνι Νησιού'; }
            break;

        case 'ferry_slow':
            $icon = '⛴️'; $label = "Συμβατικό Πλοίο (Οικονομικό)";
            $dep_h = 7; $dep_m = 30; 
            
            $ferry_times_s = [
                'Μύκονος' => [4, 45], 'Άνδρος' => [2, 0], 'Σύρος' => [3, 45], 'Πάρος' => [4, 15], 'Νάξος' => [5, 30], 'Σαντορίνη' => [8, 0], 'Ίος' => [7, 0], 'Μήλος' => [6, 30], 'Φολέγανδρος' => [8, 30], 'Ηράκλειο' => [9, 0], 'Χανιά' => [9, 0], 'Σκιάθος' => [2, 15], 'Αλόννησος' => [3, 45], 'Κέρκυρα' => [1, 45], 'Ζάκυνθος' => [1, 15], 'Κεφαλονιά' => [1, 40], 'Ρόδος' => [16, 0], 'Κως' => [13, 30], 'Μυτιλήνη (Λέσβος)' => [12, 0], 'Χίος' => [9, 0], 'Σκόπελος' => [3, 45]
            ];
            if(isset($ferry_times_s[$dest])) { $dur_h = $ferry_times_s[$dest][0]; $dur_m = $ferry_times_s[$dest][1]; }
            else { $dur_h = 7; $dur_m = 0; }

            if(in_array($dest, ['Μύκονος', 'Άνδρος'])) { $company = 'Fast Ferries'; $port = 'Λιμάνι Ραφήνας'; $arr_port = 'Νέο Λιμάνι Τούρλου'; $dep_h = 8; $dep_m = 0; }
            elseif(in_array($dest, ['Πάρος', 'Νάξος', 'Σύρος'])) { $company = 'Blue Star Ferries'; $port = 'Λιμάνι Πειραιά'; $arr_port = 'Κεντρικό Λιμάνι'; }
            elseif(in_array($dest, ['Σαντορίνη', 'Ίος'])) { $company = 'Zante Ferries'; $port = 'Λιμάνι Πειραιά'; $arr_port = 'Λιμάνι Αθηνιού'; }
            elseif(in_array($dest, ['Μυτιλήνη (Λέσβος)', 'Χίος'])) { $company = 'Blue Star Ferries'; $port = 'Λιμάνι Πειραιά'; $arr_port = 'Λιμάνι Β. Αιγαίου'; $dep_h = 20; $dep_m = 0; }
            elseif($dest == 'Σκόπελος') { $company = 'Hellenic Seaways'; $port = 'Λιμάνι Βόλου'; $arr_port = 'Λιμάνι Σκοπέλου'; $dep_h = 9; $dep_m = 0; }
            else { $company = 'Blue Star Ferries'; $port = 'Λιμάνι Πειραιά'; $arr_port = 'Λιμάνι Νησιού'; }
            
            if(in_array($dest, ['Ηράκλειο', 'Ηράκλειο (Κνωσός)', 'Χανιά'])) { $dep_h = 21; $dep_m = 0; }
            break;

        case 'bus_std':
            $icon = '🚌'; $label = "Λεωφορείο ΚΤΕΛ"; $company = 'ΚΤΕΛ Υπεραστικό'; $port = 'Σταθμός ΚΤΕΛ';
            $dep_h = 9; $dep_m = 0; $dur_h = 5; $dur_m = 30;
            break;
        case 'bus_express':
            $icon = '🚌'; $label = "ΚΤΕΛ Express (Χωρίς Στάσεις)"; $company = 'ΚΤΕΛ VIP Express'; $port = 'Σταθμός ΚΤΕΛ';
            $dep_h = 12; $dep_m = 30; $dur_h = 4; $dur_m = 15;
            break;
        case 'car_rental':
            $is_car = true; $icon = '🚗'; $company = 'Ενοικίαση Οχήματος'; 
            $port = 'Παραλαβή από τοπικό κατάστημα'; $arr_port = ''; $label = "Ενοικίαση για όλο το ταξίδι";
            break;
    }

    if ($is_return && !$is_car) {
        $dep_h = ($dep_h + 7) % 24; 
        if ($dep_h < 8) $dep_h += 10; 
        
        $temp = $port; $port = $arr_port; $arr_port = $temp;
    }

    $arr_h = ($dep_h + $dur_h) % 24;
    $arr_m = $dep_m + $dur_m;
    if ($arr_m >= 60) {
        $arr_h = ($arr_h + floor($arr_m / 60)) % 24;
        $arr_m = $arr_m % 60;
    }

    $dep_t_str = sprintf("%02d:%02d", $dep_h, $dep_m);
    $arr_t_str = sprintf("%02d:%02d", $arr_h, $arr_m);

    $dur_string = "";
    if ($dur_h > 0) $dur_string .= "$dur_h ώρ ";
    if ($dur_m > 0) $dur_string .= "$dur_m λ";

    return [
        'icon' => $icon, 'company' => $company, 'port' => $port, 'arr_port' => $arr_port, 'label' => $label,
        'dep_t' => $dep_t_str, 'arr_t' => $arr_t_str, 'dur_str' => trim($dur_string), 'type' => $type, 'is_car' => $is_car
    ];
}
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $t_lang['page_title']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #0f172a; --secondary: #3b82f6; --bg-gradient: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); --text-main: #334155; --text-muted: #64748b; --success: #10b981; --border: #cbd5e1;}
        body { margin: 0; font-family: 'Inter', sans-serif; background: var(--bg-gradient); background-attachment: fixed; color: var(--text-main); min-height: 100vh; overflow-x: hidden;}
        
        /* CHECKOUT HEADER (Minimal, χωρίς μενού, μόνο Πίσω) */
        header { display: flex; align-items: center; justify-content: space-between; padding: 15px 5%; background: var(--primary); border-bottom: 1px solid rgba(255, 255, 255, 0.08); position: sticky; width: 100%; top: 0; z-index: 1000; box-sizing: border-box;}
        .brand { display: flex; align-items: center; gap: 15px; text-decoration: none;}
        .brand h2 { margin: 0; font-size: 20px; font-weight: 900; background: linear-gradient(to right, #ffffff, #bae6fd); -webkit-background-clip: text; -webkit-text-fill-color: transparent;}
        
        .btn-cancel { color: #e2e8f0; text-decoration: none; font-size: 13px; font-weight: 700; border: 1px solid rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 8px; transition: 0.3s; display: flex; align-items: center; gap: 5px;}
        .btn-cancel:hover { background: rgba(255,255,255,0.15); border-color: rgba(255,255,255,0.4); color: #ffffff;}

        .wrapper { max-width: 900px; margin: 40px auto; background: white; padding: 40px; border-radius: 20px; box-shadow: 0 20px 40px rgba(15, 23, 42, 0.08); border-top: 6px solid var(--success);}
        
        .wizard-nav { display: flex; align-items: center; justify-content: center; gap: 15px; margin-bottom: 30px; background: #f1f5f9; padding: 15px; border-radius: 12px;}
        .wizard-step { display: flex; align-items: center; gap: 10px; font-weight: 700; font-size: 15px;}
        .step-completed { color: var(--success); }
        .step-completed .w-num { background: var(--success); color: white; border: 2px solid var(--success); width: 26px; height: 26px; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-size: 13px;}
        .step-active { color: var(--secondary); }
        .step-active .w-num { background: var(--secondary); color: white; border: 2px solid var(--secondary); width: 26px; height: 26px; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-size: 13px;}
        .step-pending { color: var(--text-muted); opacity: 0.6; }
        .step-pending .w-num { border: 2px solid var(--text-muted); width: 26px; height: 26px; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-size: 13px; }
        .w-line { width: 50px; height: 2px; background: var(--success); }
        .w-line.pending { background: #cbd5e1; }

        .page-title { color: var(--primary); margin-top: 0; font-size: 26px; font-weight: 900; border-bottom: 2px solid #f1f5f9; padding-bottom: 15px; margin-bottom: 20px;}
        
        .summary-box { background: #f8fafc; border: 1px solid var(--border); padding: 20px; border-radius: 12px; margin-bottom: 40px; display: flex; justify-content: space-between; align-items: center;}
        .summary-box div { font-size: 13px; color: var(--text-muted); font-weight: 700;}
        .summary-box strong { color: var(--primary); display: block; font-size: 16px; margin-top: 4px;}
        .summary-price { text-align: right; }
        .summary-price strong { color: var(--success); font-size: 22px;}

        .section-title { font-size: 18px; font-weight: 800; color: var(--primary); margin-bottom: 15px; margin-top: 30px; border-bottom: 2px solid var(--secondary); padding-bottom: 10px; display: inline-block;}

        .transport-card { display: flex; align-items: center; justify-content: space-between; border: 2px solid var(--border); padding: 20px 25px; border-radius: 12px; margin-bottom: 15px; cursor: pointer; transition: 0.2s; background: white;}
        .transport-card:hover { border-color: #93c5fd; box-shadow: 0 5px 15px rgba(15,23,42,0.05);}
        .transport-card:has(input[type="radio"]:checked), .transport-card:has(input[type="checkbox"]:checked) { border-color: var(--secondary); background: #eff6ff; box-shadow: 0 0 0 1px var(--secondary); }
        
        .t-info { display: flex; align-items: center; gap: 20px; flex: 1;}
        input[type="radio"], input[type="checkbox"] { transform: scale(1.4); accent-color: var(--secondary); cursor: pointer;}
        .t-details { flex: 1; }
        .t-details h4 { margin: 0 0 6px 0; font-size: 17px; font-weight: 800; color: var(--primary);}
        .t-details p { margin: 0; font-size: 13px; font-weight: 600; color: var(--text-muted);}
        
        .t-route { display: flex; align-items: center; gap: 15px; margin-top: 12px; padding-top: 12px; border-top: 1px dashed #cbd5e1;}
        .route-point { font-size: 12.5px; font-weight: 700; color: var(--text-muted);}
        .route-time { font-size: 15px; font-weight: 900; color: var(--primary); display: block;}
        .route-line { flex: 1; height: 2px; background: repeating-linear-gradient(to right, #cbd5e1 0, #cbd5e1 4px, transparent 4px, transparent 8px); position: relative; text-align: center;}
        .route-dur { background: #eff6ff; padding: 2px 8px; font-size: 11px; font-weight: 800; color: var(--secondary); border-radius: 10px; position: absolute; top: -8px; left: 50%; transform: translateX(-50%);}

        .car-rental-info { font-size: 13px; font-weight: 700; color: #047857; margin-top: 12px; padding-top: 12px; border-top: 1px dashed #cbd5e1; display: flex; align-items: center; gap: 8px;}

        .t-price { font-size: 24px; font-weight: 900; color: var(--primary); text-align: right;}

        .btn-submit { display: flex; justify-content: center; align-items: center; gap: 10px; width: 100%; background: var(--secondary); color: white; border: none; padding: 20px; border-radius: 12px; font-size: 17px; font-weight: 800; cursor: pointer; margin-top: 40px; transition: 0.3s; text-transform: uppercase; box-shadow: 0 10px 20px rgba(37, 99, 235, 0.2);}
        .btn-submit:hover { background: #1d4ed8; transform: translateY(-2px); box-shadow: 0 15px 30px rgba(37, 99, 235, 0.3); }
        
        .total-preview { background: #1e293b; color: white; padding: 20px; border-radius: 12px; margin-top: 30px; display: flex; justify-content: space-between; align-items: center;}
        .total-preview span { font-size: 14px; color: #cbd5e1;}
        .total-preview strong { font-size: 24px; color: #facc15;}

        .car-rental-card { border: 2px dashed var(--secondary); background: #f8fafc;}
        .disabled-card { opacity: 0.4; pointer-events: none; filter: grayscale(100%);}

        /* =========================================================
           📱 RESPONSIVE ΓΙΑ CHECKOUT & MOBILE
           ========================================================= */
        @media (max-width: 900px) { 
            .wrapper { margin: 20px; padding: 30px; }
            .summary-box { flex-direction: column; align-items: flex-start; gap: 15px;} 
            .summary-price { text-align: left; } 
        }

        @media (max-width: 600px) {
            header { padding: 15px; flex-direction: row; }
            .brand h2 { font-size: 18px; }
            .brand svg { width: 30px; height: 30px; }
            .btn-cancel { padding: 8px 12px; font-size: 12px; }

            .wrapper { margin: 15px 10px; padding: 20px 15px; border-radius: 16px; border-top-width: 4px;}
            .page-title { font-size: 20px; margin-bottom: 20px; }
            
            .wizard-nav { flex-direction: row; flex-wrap: wrap; justify-content: center; gap: 10px; padding: 12px; margin-bottom: 25px;}
            .wizard-step { font-size: 13px; }
            .w-num { width: 22px; height: 22px; font-size: 11px; }
            .w-line { display: none; }

            .section-title { font-size: 16px; margin-top: 30px; margin-bottom: 15px; }
            
            .transport-card { flex-direction: column; align-items: flex-start; gap: 15px; padding: 15px;} 
            .t-info { width: 100%; align-items: flex-start; }
            .t-details h4 { font-size: 15px; }
            .t-details p { font-size: 12px; }

            .t-route { flex-direction: column; align-items: flex-start; gap: 8px; width: 100%; border-top: 1px solid #e2e8f0; margin-top: 10px; padding-top: 10px;} 
            .route-line { display: none; } /* Κρύβουμε τη γραμμή στα κινητά */
            .route-point { display: flex; align-items: center; gap: 10px; width: 100%; }

            .t-price { width: 100%; text-align: left; border-top: 1px dashed #e2e8f0; padding-top: 12px; font-size: 22px;} 

            .total-preview { flex-direction: column; align-items: center; text-align: center; gap: 5px; padding: 15px; }
            .btn-submit { font-size: 14px; padding: 18px; margin-top: 25px; }
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
    <a href="javascript:history.back()" class="btn-cancel">
        <?php echo $t_lang['back_btn']; ?>
    </a>
</header>

<div class="wrapper">
    <div class="wizard-nav">
        <div class="wizard-step step-completed"><div class="w-num">✓</div> <?php echo $t_lang['step1']; ?></div>
        <div class="w-line"></div>
        <div class="wizard-step step-active"><div class="w-num">2</div> <?php echo $t_lang['step2']; ?></div>
        <div class="w-line pending"></div>
        <div class="wizard-step step-pending"><div class="w-num">3</div> <?php echo $t_lang['step3']; ?></div>
    </div>

    <h1 class="page-title"><?php echo $t_lang['transport_title'] . htmlspecialchars($dest_name); ?></h1>

    <div class="summary-box">
        <div><?php echo $t_lang['hotel_label']; ?> <strong>🏨 <?php echo htmlspecialchars($hotel_name_display); ?></strong></div>
        <div class="summary-price"><?php echo $t_lang['acc_cost']; ?> <strong><?php echo number_format($hotel_cost_display, 2); ?>€</strong></div>
    </div>
    
    <form action="checkout.php?id=<?php echo $id; ?>&days=<?php echo $days; ?>&persons=<?php echo $persons; ?>&budget=<?php echo $budget; ?>" method="POST">
        
        <input type="hidden" name="selected_dates" value="<?php echo $selected_dates_val; ?>">
        <input type="hidden" name="room_type" value="<?php echo $room_type_val; ?>">
        <input type="hidden" name="hotel_data" value="<?php echo $hotel_data_val; ?>">

        <h3 class="section-title"><?php echo $t_lang['section1'] . htmlspecialchars($dest_name); ?></h3>
        
        <label class="transport-card">
            <div class="t-info">
                <input type="radio" name="outbound_transport" value="Δικό μου Όχημα | 0 | none" checked class="calc-cost out-radio" data-iscarr="1">
                <div class="t-details">
                    <h4><?php echo $t_lang['own_vehicle']; ?></h4>
                    <p><?php echo $t_lang['own_vehicle_desc']; ?></p>
                </div>
            </div>
            <div class="t-price">0.00€</div>
        </label>

        <?php foreach ($transports as $index => $t): 
            $route = getRealisticRouting($t['type'], $dest_name_gr, false);
            if ($route['is_car']) continue; 
            
            $out_cost = round($t['price'] * $persons * $t_multiplier, 2);
            
            // ΕΝΣΩΜΑΤΩΣΗ ΤΗΣ ΑΚΡΙΒΟΥΣ ΩΡΑΣ ΣΤΟ VALUE ΠΟΥ ΘΑ ΠΑΕΙ ΣΤΗ ΒΑΣΗ
            $db_value = htmlspecialchars($route['company']) . " | " . $out_cost . " | " . $route['type'] . " | " . $route['dep_t'];
            
            // TRANSLATIONS
            $d_company = ($lang == 'en' && isset($t_company[$route['company']])) ? $t_company[$route['company']] : $route['company'];
            $d_label = ($lang == 'en' && isset($t_label[$route['label']])) ? $t_label[$route['label']] : $route['label'];
            $d_port = ($lang == 'en' && isset($t_port[$route['port']])) ? $t_port[$route['port']] : $route['port'];
            if ($route['port'] == $dest_name_gr) $d_port = $dest_name;
            $d_arr_port = ($lang == 'en' && isset($t_port[$route['arr_port']])) ? $t_port[$route['arr_port']] : $route['arr_port'];
            if ($route['arr_port'] == $dest_name_gr) $d_arr_port = $dest_name;
            $d_dur_str = ($lang == 'en') ? str_replace(['ώρ', 'λ'], ['h', 'm'], $route['dur_str']) : $route['dur_str'];
        ?>
            <label class="transport-card">
                <div class="t-info">
                    <input type="radio" name="outbound_transport" value="<?php echo $db_value; ?>" class="calc-cost out-radio" data-iscarr="0">
                    <div class="t-details">
                        <h4><?php echo $route['icon']; ?> <?php echo htmlspecialchars($d_company); ?></h4>
                        <p><?php echo $d_label; ?> • <?php echo $t_lang['one_way_fwd']; ?> (<?php echo $persons; ?> <?php echo $t_lang['persons']; ?>)</p>
                        
                        <div class="t-route">
                            <div class="route-point"><span class="route-time"><?php echo $route['dep_t']; ?></span><?php echo htmlspecialchars($d_port); ?></div>
                            <div class="route-line"><span class="route-dur"><?php echo $d_dur_str; ?></span></div>
                            <div class="route-point"><span class="route-time"><?php echo $route['arr_t']; ?></span><?php echo htmlspecialchars($d_arr_port); ?></div>
                        </div>
                    </div>
                </div>
                <div class="t-price"><?php echo number_format($out_cost, 2); ?>€</div>
            </label>
        <?php endforeach; ?>


        <div id="return_section">
            <h3 class="section-title" style="margin-top: 40px;"><?php echo $t_lang['section2'] . htmlspecialchars($dest_name); ?></h3>

            <label class="transport-card">
                <div class="t-info">
                    <input type="radio" name="return_transport" value="Χωρίς Επιστροφή | 0 | none" checked class="calc-cost">
                    <div class="t-details">
                        <h4><?php echo $t_lang['no_return']; ?></h4>
                        <p><?php echo $t_lang['no_return_desc']; ?></p>
                    </div>
                </div>
                <div class="t-price">0.00€</div>
            </label>

            <?php foreach ($transports as $index => $t): 
                $route = getRealisticRouting($t['type'], $dest_name_gr, true); // true = Επιστροφή!
                if ($route['is_car']) continue; 

                $ret_cost = round($t['price'] * $persons * $t_multiplier, 2);
                
                // ΕΝΣΩΜΑΤΩΣΗ ΤΗΣ ΑΚΡΙΒΟΥΣ ΩΡΑΣ ΕΠΙΣΤΡΟΦΗΣ
                $db_value_ret = htmlspecialchars($route['company']) . " (Επιστροφή) | " . $ret_cost . " | " . $route['type'] . " | " . $route['dep_t'];
                
                // TRANSLATIONS
                $d_company = ($lang == 'en' && isset($t_company[$route['company']])) ? $t_company[$route['company']] : $route['company'];
                $d_label = ($lang == 'en' && isset($t_label[$route['label']])) ? $t_label[$route['label']] : $route['label'];
                $d_port = ($lang == 'en' && isset($t_port[$route['port']])) ? $t_port[$route['port']] : $route['port'];
                if ($route['port'] == $dest_name_gr) $d_port = $dest_name;
                $d_arr_port = ($lang == 'en' && isset($t_port[$route['arr_port']])) ? $t_port[$route['arr_port']] : $route['arr_port'];
                if ($route['arr_port'] == $dest_name_gr) $d_arr_port = $dest_name;
                $d_dur_str = ($lang == 'en') ? str_replace(['ώρ', 'λ'], ['h', 'm'], $route['dur_str']) : $route['dur_str'];
            ?>
                <label class="transport-card">
                    <div class="t-info">
                        <input type="radio" name="return_transport" value="<?php echo $db_value_ret; ?>" class="calc-cost">
                        <div class="t-details">
                            <h4><?php echo $route['icon']; ?> <?php echo htmlspecialchars($d_company); ?></h4>
                            <p><?php echo $d_label; ?> • <?php echo $t_lang['one_way_ret']; ?> (<?php echo $persons; ?> <?php echo $t_lang['persons']; ?>)</p>
                            
                            <div class="t-route">
                                <div class="route-point"><span class="route-time"><?php echo $route['dep_t']; ?></span><?php echo htmlspecialchars($d_port); ?></div>
                                <div class="route-line"><span class="route-dur"><?php echo $d_dur_str; ?></span></div>
                                <div class="route-point"><span class="route-time"><?php echo $route['arr_t']; ?></span><?php echo htmlspecialchars($d_arr_port); ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="t-price"><?php echo number_format($ret_cost, 2); ?>€</div>
                </label>
            <?php endforeach; ?>
        </div>

        <h3 class="section-title" style="margin-top: 40px;"><?php echo $t_lang['section3']; ?></h3>
        
        <?php 
        $car_route = getRealisticRouting('car_rental', $dest_name_gr);
        $total_car_cost = round(25.00 * $days * $t_multiplier, 2);
        
        $d_company_car = ($lang == 'en' && isset($t_company[$car_route['company']])) ? $t_company[$car_route['company']] : $car_route['company'];
        ?>
        <label class="transport-card car-rental-card" id="car_rental_card">
            <div class="t-info">
                <input type="checkbox" name="rent_car" id="rent_car_box" value="Ενοικίαση Οχήματος | <?php echo $total_car_cost; ?>" class="calc-cost">
                <div class="t-details">
                    <h4>🚗 <?php echo htmlspecialchars($d_company_car); ?></h4>
                    <p><?php echo $t_lang['car_pickup']; ?></p>
                    <p style="color:var(--success); font-weight:800; font-size:12px; margin-top:5px;"><?php echo $t_lang['car_avail']; ?> (<?php echo $days; ?> <?php echo $t_lang['days']; ?>).</p>
                </div>
            </div>
            <div class="t-price">+<?php echo number_format($total_car_cost, 2); ?>€</div>
        </label>

        <div class="total-preview">
            <span><?php echo $t_lang['temp_total']; ?></span>
            <strong id="live_total"><?php echo number_format($hotel_cost_display, 2); ?>€</strong>
        </div>

        <button type="submit" class="btn-submit">
            <?php echo $t_lang['continue_btn']; ?>
        </button>
    </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const hotelCost = <?php echo $hotel_cost_display; ?>;
    const radios = document.querySelectorAll('.calc-cost');
    const outRadios = document.querySelectorAll('.out-radio');
    const liveTotal = document.getElementById('live_total');
    
    const returnSection = document.getElementById('return_section');
    const carRentalCard = document.getElementById('car_rental_card');
    const rentCarBox = document.getElementById('rent_car_box');

    function updateLiveTotal() {
        let outCost = 0; let retCost = 0; let rentCost = 0;

        let outSelected = document.querySelector('input[name="outbound_transport"]:checked');
        if (outSelected) {
            outCost = parseFloat(outSelected.value.split('|')[1].trim());
            
            if(outSelected.getAttribute('data-iscarr') === '1') {
                returnSection.style.display = 'none';
                document.querySelector('input[name="return_transport"][value="Χωρίς Επιστροφή | 0 | none"]').checked = true;
                
                carRentalCard.classList.add('disabled-card');
                rentCarBox.checked = false;
            } else {
                returnSection.style.display = 'block';
                carRentalCard.classList.remove('disabled-card');
            }
        }

        let retSelected = document.querySelector('input[name="return_transport"]:checked');
        if (retSelected && returnSection.style.display !== 'none') {
            retCost = parseFloat(retSelected.value.split('|')[1].trim());
        }

        if (rentCarBox && rentCarBox.checked) {
            rentCost = parseFloat(rentCarBox.value.split('|')[1].trim());
        }

        let total = hotelCost + outCost + retCost + rentCost;
        liveTotal.innerText = total.toFixed(2) + '€';
    }

    radios.forEach(radio => {
        radio.addEventListener('change', updateLiveTotal);
    });

    updateLiveTotal();
});
</script>

</body>
</html>