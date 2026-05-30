<?php
session_cache_limiter('private_no_expire');
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: ../auth/login.php"); exit(); }

if (!isset($_POST['selected_dates']) || !isset($_POST['outbound_transport'])) {
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
        'page_title' => 'Βήμα 3: Στοιχεία & Extras | Smart Travel Planner',
        'back_btn' => '← ΠΙΣΩ',
        'step1' => 'Διαμονή',
        'step2' => 'Μετάβαση',
        'step3' => 'Στοιχεία & Extras',
        'pass_info_title' => '👤 Στοιχεία Ταξιδιωτών',
        'auto_assign_info' => 'ℹ️ Οι ακριβείς θέσεις/καμπίνες <b>αποδίδονται αυτόματα</b> από το σύστημα βάσει των Extras.',
        'passenger' => 'Επιβάτης',
        'fullname' => 'Ονοματεπώνυμο',
        'fullname_placeholder' => 'π.χ. GIANNIS PAPADOPOULOS',
        'id_passport' => 'Αρ. Ταυτότητας / Διαβατηρίου',
        'id_placeholder' => 'π.χ. AO123456',
        'no_transport_msg' => 'Η κράτηση του ξενοδοχείου θα ολοκληρωθεί στα στοιχεία του λογαριασμού σας.',
        'extras_title' => '🎒 Διαμόρφωση Εισιτηρίων (Extras)',
        'ferry_seat_cat' => '⚓ Επιλογή Κατηγορίας Καμπίνας / Θέσης',
        'free' => 'ΔΩΡΕΑΝ',
        'vehicle_ferry' => '🚗 Όχημα στο Πλοίο (Γκαράζ)',
        'no_vehicle' => 'Χωρίς Όχημα',
        'motorcycle' => 'Μοτοσυκλέτα',
        'car' => 'ΙΧ Αυτοκίνητο',
        'suv' => 'SUV (Μεγάλο Όχημα)',
        'plate_number' => 'Αριθμός Πινακίδας',
        'plate_placeholder' => 'π.χ. IHA-1234',
        'flight_seat_cat' => '✈️ Κατηγορία Θέσης (Πτήση)',
        'economy_seat' => 'Τυπική θέση',
        'up_front' => 'Μπροστινές θέσεις',
        'extra_legroom' => 'Περισσότερος χώρος',
        'luggage_23kg' => '🧳 Αποσκευή 23kg (Χώρος Αποσκευών)',
        'no_luggage' => 'Καμία έξτρα αποσκευή',
        'luggage' => 'Βαλίτσα(ες)',
        'summary_title' => 'Σύνοψη Κράτησης',
        'hotel' => '🏨 Ξενοδοχείο',
        'tickets_trans' => '🎟️ Εισιτήρια/Μεταφορά',
        'car_rental' => '🚗 Ενοικίαση Οχήματος',
        'extras' => '➕ Πρόσθετες Παροχές',
        'final_total' => 'Τελικό Ποσό Κράτησης',
        'checkout_btn' => 'Ολοκληρωση & Ταμειο ➔',
        'dest_not_found' => 'Προορισμός δεν βρέθηκε.',
        'error' => 'Σφάλμα: ',
        
        // JS strings for DB/Tickets
        'js_seat' => 'Θέση',
        'js_vehicle' => 'Όχημα',
        'js_plate' => 'Πινακίδα',
        'js_deck' => 'Ελεύθερη Θέση (Κατάστρωμα)',
        'js_numbered' => 'Αριθμημένη Θέση (Σαλόνι 1 - Θέση ',
        'js_cabin' => 'Καμπίνα ',
        'js_bed' => ' - Κρεβάτι '
    ],
    'en' => [
        'page_title' => 'Step 3: Details & Extras | Smart Travel Planner',
        'back_btn' => '← BACK',
        'step1' => 'Accommodation',
        'step2' => 'Transport',
        'step3' => 'Details & Extras',
        'pass_info_title' => '👤 Traveler Details',
        'auto_assign_info' => 'ℹ️ Exact seats/cabins are <b>assigned automatically</b> by the system based on Extras.',
        'passenger' => 'Passenger',
        'fullname' => 'Full Name',
        'fullname_placeholder' => 'e.g., JOHN DOE',
        'id_passport' => 'ID / Passport No.',
        'id_placeholder' => 'e.g., AO123456',
        'no_transport_msg' => 'The hotel booking will be completed using your account details.',
        'extras_title' => '🎒 Ticket Configuration (Extras)',
        'ferry_seat_cat' => '⚓ Cabin / Seat Category Selection',
        'free' => 'FREE',
        'vehicle_ferry' => '🚗 Vehicle on Ferry (Garage)',
        'no_vehicle' => 'No Vehicle',
        'motorcycle' => 'Motorcycle',
        'car' => 'Car',
        'suv' => 'SUV (Large Vehicle)',
        'plate_number' => 'License Plate Number',
        'plate_placeholder' => 'e.g., IHA-1234',
        'flight_seat_cat' => '✈️ Seat Category (Flight)',
        'economy_seat' => 'Standard seat',
        'up_front' => 'Front seats',
        'extra_legroom' => 'More legroom',
        'luggage_23kg' => '🧳 23kg Luggage (Checked Baggage)',
        'no_luggage' => 'No extra luggage',
        'luggage' => 'Suitcase(s)',
        'summary_title' => 'Booking Summary',
        'hotel' => '🏨 Hotel',
        'tickets_trans' => '🎟️ Tickets/Transport',
        'car_rental' => '🚗 Car Rental',
        'extras' => '➕ Additional Services',
        'final_total' => 'Final Booking Amount',
        'checkout_btn' => 'Complete & Checkout ➔',
        'dest_not_found' => 'Destination not found.',
        'error' => 'Error: ',
        
        // JS strings for DB/Tickets
        'js_seat' => 'Seat',
        'js_vehicle' => 'Vehicle',
        'js_plate' => 'Plate',
        'js_deck' => 'Open Seat (Deck)',
        'js_numbered' => 'Reserved Seat (Lounge 1 - Seat ',
        'js_cabin' => 'Cabin ',
        'js_bed' => ' - Bed '
    ]
];
$t_lang = $translations[$lang];

$host = 'localhost'; $db = 'smart_travel_planner'; $user = 'root'; $pass = ''; 
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT * FROM destinations WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $dest = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$dest) { die($t_lang['dest_not_found']); }
    
    // --- ΑΝΑΚΤΗΣΗ ΔΕΔΟΜΕΝΩΝ ---
    $selected_dates = explode('|', $_POST['selected_dates']);
    $room_type = $_POST['room_type']; 
    $h_data = explode('|', $_POST['hotel_data']); 
    
    $outbound_data = explode('|', $_POST['outbound_transport']); 
    $return_data = explode('|', $_POST['return_transport']);
    
    $out_type = trim($outbound_data[2] ?? 'none');
    $ret_type = trim($return_data[2] ?? 'none');

    $has_transport = ($out_type !== 'none' || $ret_type !== 'none');
    $has_flight = (strpos($out_type, 'flight') !== false || strpos($ret_type, 'flight') !== false);
    $has_ferry = (strpos($out_type, 'ferry') !== false || strpos($ret_type, 'ferry') !== false);
    
    $has_car_rental = isset($_POST['rent_car']) && !empty($_POST['rent_car']);
    $rent_car_cost = 0;
    if ($has_car_rental) {
        $rc_data = explode('|', $_POST['rent_car']);
        $rent_car_cost = floatval($rc_data[1] ?? 0);
    }

    $outbound_cost = floatval($outbound_data[1] ?? 0);
    $return_cost = floatval($return_data[1] ?? 0);
    $transport_cost = $outbound_cost + $return_cost + $rent_car_cost;

    $base_total_price = floatval($h_data[1]) + $transport_cost;

    // --- ΔΥΝΑΜΙΚΟΣ ΥΠΟΛΟΓΙΣΜΟΣ ΚΑΜΠΙΝΩΝ ΒΑΣΕΙ ΑΤΟΜΩΝ ---
    $seat_options = [];
    $seat_options[] = [
        'val' => 'Κατάστρωμα / Σαλόνι', 
        'display_val' => ($lang == 'en') ? 'Deck / Lounge' : 'Κατάστρωμα / Σαλόνι',
        'desc' => ($lang == 'en') ? 'Open seat (No reservation)' : 'Ελεύθερη θέση (Χωρίς κράτηση)', 
        'cost' => 0, 'icon' => '🚶'
    ];
    $seat_options[] = [
        'val' => 'Αριθμημένο Κάθισμα', 
        'display_val' => ($lang == 'en') ? 'Reserved Seat' : 'Αριθμημένο Κάθισμα',
        'desc' => ($lang == 'en') ? 'Airplane-type seat' : 'Θέση αεροπορικού τύπου', 
        'cost' => 15 * $persons, 'icon' => '💺'
    ];

    if ($persons <= 2) {
        $seat_options[] = ['val' => '2-κλινη Καμπίνα (Εσωτ.)', 'display_val' => ($lang == 'en') ? '2-bed Cabin (Inside)' : '2-κλινη Καμπίνα (Εσωτ.)', 'desc' => ($lang == 'en') ? 'Private no window' : 'Ιδιωτική χωρίς παράθυρο', 'cost' => 50, 'icon' => '🛏️'];
        $seat_options[] = ['val' => '2-κλινη Καμπίνα (Εξωτ.)', 'display_val' => ($lang == 'en') ? '2-bed Cabin (Outside)' : '2-κλινη Καμπίνα (Εξωτ.)', 'desc' => ($lang == 'en') ? 'Private with window' : 'Ιδιωτική με παράθυρο', 'cost' => 70, 'icon' => '🌅'];
    } elseif ($persons == 3) {
        $seat_options[] = ['val' => '3-κλινη Καμπίνα (Εσωτ.)', 'display_val' => ($lang == 'en') ? '3-bed Cabin (Inside)' : '3-κλινη Καμπίνα (Εσωτ.)', 'desc' => ($lang == 'en') ? 'Private no window' : 'Ιδιωτική χωρίς παράθυρο', 'cost' => 75, 'icon' => '🛏️'];
        $seat_options[] = ['val' => '3-κλινη Καμπίνα (Εξωτ.)', 'display_val' => ($lang == 'en') ? '3-bed Cabin (Outside)' : '3-κλινη Καμπίνα (Εξωτ.)', 'desc' => ($lang == 'en') ? 'Private with window' : 'Ιδιωτική με παράθυρο', 'cost' => 95, 'icon' => '🌅'];
    } elseif ($persons == 4) {
        $seat_options[] = ['val' => '4-κλινη Καμπίνα (Εσωτ.)', 'display_val' => ($lang == 'en') ? '4-bed Cabin (Inside)' : '4-κλινη Καμπίνα (Εσωτ.)', 'desc' => ($lang == 'en') ? 'Private no window' : 'Ιδιωτική χωρίς παράθυρο', 'cost' => 100, 'icon' => '🛏️'];
        $seat_options[] = ['val' => '4-κλινη Καμπίνα (Εξωτ.)', 'display_val' => ($lang == 'en') ? '4-bed Cabin (Outside)' : '4-κλινη Καμπίνα (Εξωτ.)', 'desc' => ($lang == 'en') ? 'Private with window' : 'Ιδιωτική με παράθυρο', 'cost' => 120, 'icon' => '🌅'];
    } else {
        $seat_options[] = ['val' => 'Ομαδικές Καμπίνες', 'display_val' => ($lang == 'en') ? 'Group Cabins' : 'Ομαδικές Καμπίνες', 'desc' => ($lang == 'en') ? 'Combination of cabins' : 'Συνδυασμός καμπινών', 'cost' => 50 * ceil($persons/2), 'icon' => '🚪'];
    }

} catch(PDOException $e) { die($t_lang['error'] . $e->getMessage()); }
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $t_lang['page_title']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #0f172a; --secondary: #3b82f6; --accent: #0ea5e9; --bg-gradient: #f1f5f9; --text-main: #334155; --text-muted: #64748b; --success: #10b981; --border: #e2e8f0;}
        body { margin: 0; font-family: 'Inter', sans-serif; background: var(--bg-gradient); color: var(--text-main); min-height: 100vh;}
        
        /* CHECKOUT HEADER (Minimal, χωρίς μενού, μόνο Πίσω) */
        header { display: flex; align-items: center; justify-content: space-between; padding: 15px 5%; background: var(--primary); border-bottom: 1px solid rgba(255, 255, 255, 0.08); position: sticky; width: 100%; top: 0; z-index: 1000; box-sizing: border-box;}
        .brand { display: flex; align-items: center; gap: 15px; text-decoration: none;}
        .brand h2 { margin: 0; font-size: 20px; font-weight: 900; background: linear-gradient(to right, #ffffff, #bae6fd); -webkit-background-clip: text; -webkit-text-fill-color: transparent;}
        
        .btn-cancel { background: transparent; color: #e2e8f0; text-decoration: none; font-family: 'Inter', sans-serif; font-size: 13px; font-weight: 700; border: 1px solid rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 8px; transition: 0.3s; display: flex; align-items: center; gap: 5px; cursor: pointer;}
        .btn-cancel:hover { background: rgba(255,255,255,0.15); border-color: rgba(255,255,255,0.4); color: #ffffff;}
        /* WIZARD NAV */
        .wizard-nav { display: flex; align-items: center; justify-content: center; gap: 15px; padding: 40px 20px;}
        .wizard-step { display: flex; align-items: center; gap: 10px; font-weight: 800; font-size: 15px;}
        .step-completed { color: var(--success); }
        .step-completed .w-num { background: var(--success); color: white; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-size: 13px;}
        .step-active { color: var(--secondary); }
        .step-active .w-num { background: var(--secondary); color: white; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-size: 13px; box-shadow: 0 4px 12px rgba(59,130,246,0.3);}
        .step-pending { color: var(--text-muted); opacity: 0.6; }
        .step-pending .w-num { background: #e2e8f0; width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-size: 13px; color: var(--text-muted); font-weight: 800;}
        .w-line { width: 60px; height: 3px; background: var(--success); border-radius: 2px;}
        .w-line.pending { background: #e2e8f0; }

        .container { max-width: 1200px; margin: 0 auto 60px auto; padding: 0 20px; display: grid; grid-template-columns: 1.8fr 1fr; gap: 35px;}
        
        .panel { background: white; padding: 40px; border-radius: 24px; box-shadow: 0 15px 35px -5px rgba(0,0,0,0.05); border: 1px solid rgba(226, 232, 240, 0.8); margin-bottom: 30px;}
        .panel-title { font-size: 22px; color: var(--primary); font-weight: 900; margin-top: 0; margin-bottom: 25px; display: flex; align-items: center; gap: 12px; border-bottom: 2px solid #f8fafc; padding-bottom: 18px;}
        
        /* ΚΑΡΤΕΣ ΕΠΙΒΑΤΩΝ */
        .passenger-card { background: #f8fafc; border: 1px solid var(--border); padding: 25px; border-radius: 18px; margin-bottom: 20px; border-left: 6px solid var(--secondary); transition: 0.3s;}
        .passenger-card:hover { box-shadow: 0 8px 20px rgba(0,0,0,0.03); border-color: #cbd5e1;}
        .p-title { font-size: 16px; font-weight: 900; color: var(--primary); margin-bottom: 18px; display:flex; justify-content: space-between; align-items: center;}
        
        /* INPUTS */
        .input-group { margin-bottom: 18px;}
        .input-group label { display: block; font-size: 13.5px; font-weight: 800; color: var(--text-muted); margin-bottom: 8px;}
        .input-group input[type="text"], .input-group select { width: 100%; padding: 16px 20px; border-radius: 14px; border: 2px solid #e2e8f0; font-size: 15px; font-family: 'Inter', sans-serif; font-weight: 600; color: var(--primary); outline: none; transition: all 0.3s; background: white; box-sizing: border-box;}
        .input-group input[type="text"]:focus, .input-group select:focus { border-color: var(--secondary); box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15); background: #fff;}
        .input-group input[type="text"]::placeholder { color: #94a3b8; font-weight: 500; }
        
        /* RADIO CARDS (EXTRAS) */
        .radio-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 25px;}
        .radio-card { display: flex; align-items: flex-start; gap: 15px; border: 2px solid #e2e8f0; border-radius: 18px; padding: 22px; cursor: pointer; transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); background: #fff;}
        .radio-card:hover { border-color: #93c5fd; transform: translateY(-3px); box-shadow: 0 8px 20px rgba(37,99,235,0.08);}
        .radio-card input { display: none; }
        .radio-card:has(input:checked) { border-color: var(--secondary); background: #eff6ff; box-shadow: 0 0 0 2px var(--secondary), 0 8px 20px rgba(37,99,235,0.1);}
        
        .rc-icon { font-size: 28px; line-height: 1;}
        .rc-details { flex: 1; }
        .rc-details h4 { margin: 0 0 5px 0; font-size: 15px; color: var(--primary); font-weight: 900;}
        .rc-details p { margin: 0; font-size: 13px; color: var(--text-muted); font-weight: 600;}
        .rc-price { font-size: 15px; font-weight: 900; color: var(--secondary); margin-top: 10px; display: inline-block;}
        .rc-price.free { color: var(--success); }

        .extra-box { background: #f8fafc; padding: 25px; border-radius: 18px; border: 2px dashed #cbd5e1; margin-bottom: 20px;}

        /* SIDEBAR (ΣΥΝΟΨΗ) */
        .sidebar { position: sticky; top: 100px; background: #0f172a; color: white; padding: 40px; border-radius: 24px; box-shadow: 0 25px 50px rgba(15,23,42,0.25); height: fit-content; border: 1px solid rgba(255,255,255,0.08);}
        .sidebar h3 { margin-top: 0; color: white; border-bottom: 1px dashed rgba(255,255,255,0.15); padding-bottom: 20px; margin-bottom: 25px; font-size: 22px; font-weight: 900;}
        
        .sum-item { display: flex; justify-content: space-between; align-items: center; font-size: 14.5px; margin-bottom: 18px; color: #94a3b8; font-weight: 600;}
        .sum-item strong { color: white; font-weight: 800; font-size: 15.5px;}
        .sum-item.highlight { border-top: 1px dashed rgba(255,255,255,0.15); padding-top: 20px; margin-top: 20px; color: #bae6fd;}
        
        .total-box { background: rgba(0,0,0,0.25); padding: 25px; border-radius: 18px; text-align: center; margin-top: 35px; border: 1px solid rgba(255,255,255,0.05);}
        .total-box span { display: block; font-size: 13px; color: #7dd3fc; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; font-weight: 800;}
        .total-box strong { font-size: 42px; font-weight: 900; color: #ffffff; line-height: 1; text-shadow: 0 4px 15px rgba(0,0,0,0.3);}
        
        .btn-submit { display: flex; justify-content: center; align-items: center; gap: 10px; width: 100%; background: linear-gradient(135deg, var(--secondary), #0ea5e9); color: white; border: none; padding: 20px; border-radius: 16px; font-size: 17px; font-weight: 900; cursor: pointer; margin-top: 30px; transition: 0.3s; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 10px 25px rgba(59, 130, 246, 0.4);}
        .btn-submit:hover { transform: translateY(-3px); box-shadow: 0 15px 35px rgba(59, 130, 246, 0.6); filter: brightness(1.1); }
        
        .no-transport-msg { background: #eff6ff; border: 1px solid #bfdbfe; padding: 20px 25px; border-radius: 16px; color: #1e3a8a; display: flex; align-items: center; gap: 15px; font-weight: 700; font-size: 15px;}

        /* =========================================================
           📱 RESPONSIVE ΓΙΑ CHECKOUT & MOBILE
           ========================================================= */
        @media (max-width: 950px) { 
            .container { grid-template-columns: 1fr; margin-top: 10px;} 
            .sidebar { position: static; } 
        }

        @media (max-width: 600px) {
            header { padding: 15px; flex-direction: row; }
            .brand h2 { font-size: 18px; }
            .brand svg { width: 30px; height: 30px; }
            .btn-cancel { padding: 8px 12px; font-size: 12px; }

            .wizard-nav { flex-direction: row; flex-wrap: wrap; justify-content: center; gap: 10px; padding: 20px 10px; margin-bottom: 0;}
            .wizard-step { font-size: 12px; }
            .w-num { width: 22px; height: 22px; font-size: 11px; }
            .w-line { display: none; } /* Κρύβουμε τις γραμμές στο κινητό */

            .panel { padding: 25px 20px; border-radius: 20px; margin-bottom: 20px;}
            .panel-title { font-size: 18px; margin-bottom: 15px; padding-bottom: 10px;}

            .passenger-card { padding: 15px; border-radius: 14px; }
            .p-title { font-size: 14px; margin-bottom: 15px; }
            .input-group input[type="text"], .input-group select { padding: 12px 15px; font-size: 14px; }
            
            /* Σημαντικό: 1 Στήλη στα Extras για να μην πατικώνονται */
            .radio-grid { grid-template-columns: 1fr; gap: 12px; margin-bottom: 15px;}
            .radio-card { padding: 15px; }
            .rc-icon { font-size: 24px; }
            .rc-details h4 { font-size: 14px; }
            .rc-details p { font-size: 12px; }
            
            .extra-box { padding: 15px; margin-bottom: 15px;}
            
            .sidebar { padding: 25px 20px; border-radius: 20px; margin-bottom: 20px;}
            .sidebar h3 { font-size: 20px; }
            .sum-item { font-size: 13px; }
            .sum-item strong { font-size: 14px; }
            
            .total-box { padding: 20px; margin-top: 25px; }
            .total-box strong { font-size: 32px; }
            .btn-submit { font-size: 15px; padding: 18px; }
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
    
    <!-- ΔΙΟΡΘΩΣΗ: Αόρατη φόρμα (POST) που κρατάει τα δεδομένα του ξενοδοχείου ζωντανά 
         *Σημείωση: Βεβαιώσου ότι το αρχείο σου λέγεται transport.php ή transports.php και διόρθωσέ το στο action αν χρειάζεται -->
    <form action="transport.php?id=<?php echo $id; ?>&days=<?php echo $days; ?>&persons=<?php echo $persons; ?>&budget=<?php echo $budget; ?>&lang=<?php echo $lang; ?>" method="POST" style="margin: 0; padding: 0;">
        <?php foreach($_POST as $key => $val): ?>
            <input type="hidden" name="<?php echo htmlspecialchars($key); ?>" value="<?php echo htmlspecialchars($val); ?>">
        <?php endforeach; ?>
        <button type="submit" class="btn-cancel" style="cursor: pointer;">
            <?php echo $t_lang['back_btn']; ?>
        </button>
    </form>
</header>

<div class="wizard-nav">
    <div class="wizard-step step-completed"><div class="w-num">✓</div> <?php echo $t_lang['step1']; ?></div><div class="w-line"></div>
    <div class="wizard-step step-completed"><div class="w-num">✓</div> <?php echo $t_lang['step2']; ?></div><div class="w-line"></div>
    <div class="wizard-step step-active"><div class="w-num">3</div> <?php echo $t_lang['step3']; ?></div>
</div>

<form action="payment.php?id=<?php echo $id; ?>&days=<?php echo $days; ?>&persons=<?php echo $persons; ?>&lang=<?php echo $lang; ?>" method="POST" id="checkoutForm">
    <input type="hidden" name="selected_dates" value="<?php echo htmlspecialchars($_POST['selected_dates']); ?>">
    <input type="hidden" name="room_type" value="<?php echo htmlspecialchars($room_type); ?>">
    <input type="hidden" name="hotel_data" value="<?php echo htmlspecialchars($_POST['hotel_data']); ?>">
    
    <input type="hidden" name="outbound_transport" id="outbound_transport" value="<?php echo htmlspecialchars($_POST['outbound_transport']); ?>">
    <input type="hidden" name="return_transport" id="return_transport" value="<?php echo htmlspecialchars($_POST['return_transport']); ?>">
    
    <?php if($has_car_rental): ?>
    <input type="hidden" name="rent_car" value="<?php echo htmlspecialchars($_POST['rent_car']); ?>">
    <?php endif; ?>

    <div class="container">
        <div class="main-col">
            
            <div class="panel">
                <h3 class="panel-title"><?php echo $t_lang['pass_info_title']; ?></h3>
                <?php if($has_transport): ?>
                    <div style="background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 15px 20px; border-radius: 12px; font-weight: 600; font-size: 14px; margin-bottom: 25px; display: flex; gap: 10px; align-items: center;">
                        <span><?php echo $t_lang['auto_assign_info']; ?></span>
                    </div>
                    
                    <?php for($i=1; $i<=$persons; $i++): ?>
                        <div class="passenger-card">
                            <div class="p-title"><?php echo $t_lang['passenger']; ?> <?php echo $i; ?></div>
                            <div class="input-group" <?php if(!$has_flight) echo 'style="margin-bottom:0;"'; ?>>
                                <label><?php echo $t_lang['fullname']; ?> <span style="color:#ef4444">*</span></label>
                                <input type="text" id="raw_pass_name_<?php echo $i; ?>" required placeholder="<?php echo $t_lang['fullname_placeholder']; ?>" oninput="this.value = this.value.replace(/[^A-Za-z\s]/g, '').toUpperCase();">
                                <textarea style="display:none;" name="pass_name_<?php echo $i; ?>" id="final_pass_name_<?php echo $i; ?>"></textarea>
                            </div>
                            
                            <?php if($has_flight): ?>
                            <div class="input-group" style="margin-bottom:0;">
                                <label><?php echo $t_lang['id_passport']; ?> <span style="color:#ef4444">*</span></label>
                                <input type="text" name="pass_id_<?php echo $i; ?>" required placeholder="<?php echo $t_lang['id_placeholder']; ?>" maxlength="9" oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');">
                            </div>
                            <?php endif; ?>
                        </div>
                    <?php endfor; ?>
                <?php else: ?>
                    <div class="no-transport-msg"><span>✅</span><div><?php echo $t_lang['no_transport_msg']; ?></div></div>
                <?php endif; ?>
            </div>

            <?php if($has_flight || $has_ferry): ?>
            <div class="panel">
                <h3 class="panel-title"><?php echo $t_lang['extras_title']; ?></h3>
                
                <?php if($has_ferry): ?>
                    <label style="display:block; font-size:15px; font-weight:900; color:var(--primary); margin-bottom:15px;"><?php echo $t_lang['ferry_seat_cat']; ?></label>
                    <div class="radio-grid">
                        <?php foreach($seat_options as $idx => $opt): ?>
                            <label class="radio-card">
                                <input type="radio" name="ferry_seat" value="<?php echo htmlspecialchars($opt['val']).'|'.$opt['cost']; ?>" data-cost="<?php echo $opt['cost']; ?>" class="calc-extra-radio" <?php if($idx==0) echo 'checked'; ?>>
                                <div class="rc-icon"><?php echo $opt['icon']; ?></div>
                                <div class="rc-details">
                                    <h4><?php echo $opt['display_val']; ?></h4>
                                    <p><?php echo $opt['desc']; ?></p>
                                    <span class="rc-price <?php if($opt['cost']==0) echo 'free'; ?>">
                                        <?php echo $opt['cost'] == 0 ? $t_lang['free'] : '+'.$opt['cost'].'€'; ?>
                                    </span>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>

                    <div class="extra-box">
                        <div class="input-group" style="margin-bottom:0;">
                            <label><?php echo $t_lang['vehicle_ferry']; ?></label>
                            <select name="ferry_vehicle" id="ferry_vehicle" class="calc-extra-select">
                                <option value="none|0" data-cost="0"><?php echo $t_lang['no_vehicle']; ?> - 0€</option>
                                <option value="Μοτοσυκλέτα|30" data-cost="30"><?php echo $t_lang['motorcycle']; ?> - +30€</option>
                                <option value="ΙΧ Αυτοκίνητο|80" data-cost="80"><?php echo $t_lang['car']; ?> - +80€</option>
                                <option value="SUV (Μεγάλο)|110" data-cost="110"><?php echo $t_lang['suv']; ?> - +110€</option>
                            </select>
                        </div>
                        <div class="input-group" id="plate_container" style="display:none; margin-top:20px; margin-bottom:0;">
                            <label><?php echo $t_lang['plate_number']; ?> <span style="color:#ef4444">*</span></label>
                            <input type="text" name="license_plate" id="license_plate" placeholder="<?php echo $t_lang['plate_placeholder']; ?>" oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9-]/g, '');">
                        </div>
                    </div>
                <?php endif; ?>

                <?php if($has_flight): ?>
                    <label style="display:block; font-size:15px; font-weight:900; color:var(--primary); margin-bottom:15px;"><?php echo $t_lang['flight_seat_cat']; ?></label>
                    <div class="radio-grid">
                        <label class="radio-card">
                            <input type="radio" name="flight_seat" value="Economy Seat|0" data-cost="0" class="calc-extra-radio" checked>
                            <div class="rc-icon">💺</div>
                            <div class="rc-details">
                                <h4>Economy Seat</h4>
                                <p><?php echo $t_lang['economy_seat']; ?></p>
                                <span class="rc-price free"><?php echo $t_lang['free']; ?></span>
                            </div>
                        </label>
                        <label class="radio-card">
                            <input type="radio" name="flight_seat" value="Up Front Seat|<?php echo 12*$persons; ?>" data-cost="<?php echo 12*$persons; ?>" class="calc-extra-radio">
                            <div class="rc-icon">✨</div>
                            <div class="rc-details">
                                <h4>Up Front</h4>
                                <p><?php echo $t_lang['up_front']; ?></p>
                                <span class="rc-price">+<?php echo 12*$persons; ?>€</span>
                            </div>
                        </label>
                        <label class="radio-card">
                            <input type="radio" name="flight_seat" value="Extra Legroom|<?php echo 25*$persons; ?>" data-cost="<?php echo 25*$persons; ?>" class="calc-extra-radio">
                            <div class="rc-icon">🦵</div>
                            <div class="rc-details">
                                <h4>Extra Legroom</h4>
                                <p><?php echo $t_lang['extra_legroom']; ?></p>
                                <span class="rc-price">+<?php echo 25*$persons; ?>€</span>
                            </div>
                        </label>
                    </div>

                    <div class="extra-box">
                        <div class="input-group" style="margin-bottom:0;">
                            <label><?php echo $t_lang['luggage_23kg']; ?></label>
                            <select name="extra_luggage" class="calc-extra-select">
                                <option value="0 Βαλίτσες|0" data-cost="0"><?php echo $t_lang['no_luggage']; ?> - 0€</option>
                                <?php for($i=1; $i<=$persons; $i++): ?>
                                    <option value="<?php echo $i; ?> Βαλίτσα(ες)|<?php echo $i * 25; ?>" data-cost="<?php echo $i * 25; ?>">
                                        <?php echo $i; ?> <?php echo $t_lang['luggage']; ?> - +<?php echo $i * 25; ?>€
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

        </div>

        <div class="sidebar">
            <h3><?php echo $t_lang['summary_title']; ?></h3>
            <div class="sum-item"><span><?php echo $t_lang['hotel']; ?></span><strong><?php echo number_format(floatval($h_data[1]), 2); ?>€</strong></div>
            <?php if($transport_cost > 0): ?><div class="sum-item"><span><?php echo $t_lang['tickets_trans']; ?></span><strong><?php echo number_format($outbound_cost + $return_cost, 2); ?>€</strong></div><?php endif; ?>
            <?php if($has_car_rental): ?><div class="sum-item"><span><?php echo $t_lang['car_rental']; ?></span><strong><?php echo number_format($rent_car_cost, 2); ?>€</strong></div><?php endif; ?>
            
            <div class="sum-item highlight"><span><?php echo $t_lang['extras']; ?></span><strong id="extras_display">0.00€</strong></div>
            
            <div class="total-box">
                <span><?php echo $t_lang['final_total']; ?></span>
                <strong id="live_grand_total"><?php echo number_format($base_total_price, 2); ?>€</strong>
            </div>
            
            <button type="submit" class="btn-submit">
                <?php echo $t_lang['checkout_btn']; ?>
            </button>
        </div>
    </div>
</form>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const baseTotal = <?php echo $base_total_price; ?>;
    const grandTotalEl = document.getElementById('live_grand_total');
    const extrasDisplayEl = document.getElementById('extras_display');
    
    const extraRadios = document.querySelectorAll('.calc-extra-radio');
    const extraSelects = document.querySelectorAll('.calc-extra-select');
    
    const vehicleSelect = document.getElementById('ferry_vehicle');
    const plateContainer = document.getElementById('plate_container');
    const plateInput = document.getElementById('license_plate');

    function calculateTotals() {
        let extras = 0;
        
        document.querySelectorAll('.calc-extra-radio:checked').forEach(radio => {
            extras += parseFloat(radio.getAttribute('data-cost'));
        });

        extraSelects.forEach(select => {
            let option = select.options[select.selectedIndex];
            if(option && option.getAttribute('data-cost')) {
                extras += parseFloat(option.getAttribute('data-cost'));
            }
        });

        if (vehicleSelect) {
            if (vehicleSelect.value.indexOf('none') === -1) {
                plateContainer.style.display = 'block'; 
                plateInput.required = true;
            } else {
                plateContainer.style.display = 'none'; 
                plateInput.required = false;
            }
        }

        extrasDisplayEl.innerText = extras.toFixed(2) + '€';
        grandTotalEl.innerText = (baseTotal + extras).toFixed(2) + '€';
    }

    extraRadios.forEach(radio => radio.addEventListener('change', calculateTotals));
    extraSelects.forEach(select => select.addEventListener('change', calculateTotals));

    calculateTotals();

    // =======================================================================
    // Ο ΑΛΓΟΡΙΘΜΟΣ ΠΟΥ ΕΝΣΩΜΑΤΩΝΕΙ ΤΙΣ ΩΡΕΣ & ΤΙΣ ΘΕΣΕΙΣ ΣΤΟ ON-SUBMIT!
    // =======================================================================
    document.getElementById('checkoutForm').addEventListener('submit', function(e) {
        
        let globalClass = "Τυπική Θέση"; 
        let isFerry = false;
        let isFlight = false;

        let ferrySeatRadio = document.querySelector('input[name="ferry_seat"]:checked');
        let flightSeatRadio = document.querySelector('input[name="flight_seat"]:checked');
        
        if (ferrySeatRadio) {
            globalClass = ferrySeatRadio.value.split('|')[0].trim();
            isFerry = true;
        } else if (flightSeatRadio) {
            globalClass = flightSeatRadio.value.split('|')[0].trim();
            isFlight = true;
        }

        let outInput = document.getElementById('outbound_transport').value;
        let retInput = document.getElementById('return_transport').value;
        
        let outTime = outInput.split('|').length > 3 ? outInput.split('|')[3].trim() : '';
        let retTime = retInput.split('|').length > 3 ? retInput.split('|')[3].trim() : '';

        let totalPersons = <?php echo $persons; ?>;
        
        for(let i=1; i<=totalPersons; i++) {
            
            let rawName = document.getElementById('raw_pass_name_' + i).value.trim();
            let specificSeat = globalClass; 
            
            if (isFerry) {
                if (globalClass.includes("Κατάστρωμα")) {
                    specificSeat = "<?php echo $t_lang['js_deck']; ?>";
                } 
                else if (globalClass.includes("Αριθμημένο") || globalClass.includes("Αεροπορικό")) {
                    let seatNum = 40 + i; 
                    specificSeat = "<?php echo $t_lang['js_numbered']; ?>" + seatNum + ")";
                } 
                else if (globalClass.includes("Καμπίνα") || globalClass.includes("Ομαδικές")) {
                    let capacity = 2;
                    if (globalClass.includes("3-κλινη")) capacity = 3;
                    if (globalClass.includes("4-κλινη") || globalClass.includes("Ομαδικές")) capacity = 4;
                    
                    let cabinNumber = capacity * 100 + Math.ceil(i / capacity); 
                    let bedNumber = ((i - 1) % capacity) + 1;
                    
                    let displayClass = globalClass;
                    <?php if($lang == 'en'): ?>
                    if(globalClass === "2-κλινη Καμπίνα (Εσωτ.)") displayClass = "2-bed Cabin (Inside)";
                    else if(globalClass === "2-κλινη Καμπίνα (Εξωτ.)") displayClass = "2-bed Cabin (Outside)";
                    else if(globalClass === "3-κλινη Καμπίνα (Εσωτ.)") displayClass = "3-bed Cabin (Inside)";
                    else if(globalClass === "3-κλινη Καμπίνα (Εξωτ.)") displayClass = "3-bed Cabin (Outside)";
                    else if(globalClass === "4-κλινη Καμπίνα (Εσωτ.)") displayClass = "4-bed Cabin (Inside)";
                    else if(globalClass === "4-κλινη Καμπίνα (Εξωτ.)") displayClass = "4-bed Cabin (Outside)";
                    else if(globalClass === "Ομαδικές Καμπίνες") displayClass = "Group Cabins";
                    <?php endif; ?>
                    
                    specificSeat = displayClass + " (<?php echo $t_lang['js_cabin']; ?>" + cabinNumber + "<?php echo $t_lang['js_bed']; ?>" + bedNumber + ")";
                }
            } 
            else if (isFlight) {
                let startRow = 22;
                if (globalClass.includes("Up Front")) startRow = 8;
                else if (globalClass.includes("Extra Legroom")) startRow = 14;

                let letters = ["A", "B", "C", "D", "E", "F"];
                let actualRow = startRow + Math.floor((i - 1) / 6);
                let letter = letters[(i - 1) % 6];
                
                let seatStr = "<?php echo ($lang == 'en') ? 'Seat ' : 'Θέση '; ?>";
                specificSeat = globalClass + " (" + seatStr + actualRow + letter + ")";
            }

            let finalValueForDB = rawName + " - <?php echo $t_lang['js_seat']; ?>: " + specificSeat;
            
            if (i === 1) {
                if (vehicleSelect && vehicleSelect.value.indexOf('none') === -1 && plateInput && plateInput.value.trim() !== '') {
                    let vType = vehicleSelect.options[vehicleSelect.selectedIndex].text.split('-')[0].trim();
                    let plate = plateInput.value.toUpperCase().trim();
                    finalValueForDB += "\n<?php echo $t_lang['js_vehicle']; ?>: " + vType + " - <?php echo $t_lang['js_plate']; ?>: " + plate;
                }

                if (outTime) finalValueForDB += "\n[TIME_OUT: " + outTime + "]";
                if (retTime && retTime !== 'none') finalValueForDB += "\n[TIME_RET: " + retTime + "]";
            }
            
            document.getElementById('final_pass_name_' + i).value = finalValueForDB;
        }
    });
});
</script>
</body>
</html>