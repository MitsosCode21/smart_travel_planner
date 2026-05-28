<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: ../auth/login.php"); exit(); }

if (!isset($_POST['selected_dates']) || !isset($_POST['outbound_transport'])) {
    header("Location: ../index.php");
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$days = isset($_GET['days']) ? intval($_GET['days']) : 1;
$persons = isset($_GET['persons']) ? intval($_GET['persons']) : 1; 

// --- ΣΩΣΤΗ ΔΙΑΧΕΙΡΙΣΗ ΔΙΓΛΩΣΣΙΑΣ ΜΕΣΩ SESSION ---
if (isset($_GET['lang']) && in_array($_GET['lang'], ['gr', 'en'])) {
    $_SESSION['lang'] = $_GET['lang'];
}
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'gr';

$translations = [
    'gr' => [
        'page_title' => 'Βήμα 4: Πληρωμή | Smart Travel Planner',
        'secure_env' => 'Ασφαλές Περιβάλλον',
        'back_btn' => '← ΠΙΣΩ',
        'step1' => 'Διαμονή',
        'step2' => 'Μετάβαση',
        'step3' => 'Στοιχεία',
        'step4' => 'Πληρωμή',
        'payment_title' => 'Επιλέξτε τρόπο ολοκλήρωσης',
        'pay_now' => 'Πληρωμή Τώρα',
        'pay_later' => '3 Μέρες Πριν',
        'pay_property' => 'Στο Κατάλυμα',
        'pay_digital' => 'Digital Pay',
        'alert_now' => '<strong>Άμεση χρέωση:</strong> Θα εξοφλήσετε ολόκληρο το ποσό της κράτησης (Διαμονή + Μεταφορικά) σήμερα.',
        'card_name' => 'Ονοματεπώνυμο Κατόχου',
        'card_name_ph' => 'π.χ. GIANNIS PAPADOPOULOS',
        'card_num' => 'Αριθμός Κάρτας',
        'card_exp' => 'Ημ. Λήξης',
        'alert_later' => '<strong>Κράτηση χωρίς σημερινή χρέωση!</strong> Το ποσό θα τραβηχτεί αυτόματα από την κάρτα σας 3 ημέρες πριν την άφιξη. Εισάγετε κάρτα μόνο για εγγύηση.',
        'card_num_guarantee' => 'Αριθμός Κάρτας Εγγύησης',
        'alert_split' => '<strong>Διαχωρισμός Κόστους:</strong> Το Ξενοδοχείο (<strong>%s€</strong>) θα εξοφληθεί από κοντά. Επειδή επιλέξατε Μεταφορικά/Extras (<strong>%s€</strong>), αυτά πρέπει να πληρωθούν τώρα.',
        'alert_free' => '<strong>Δεν απαιτείται πληρωμή τώρα!</strong> <br><br> Δεν επιλέξατε μεταφορικά μέσα. Δεν χρειάζεται να εισάγετε πιστωτική κάρτα. Θα πληρώσετε ολόκληρο το ποσό της διαμονής απευθείας στο κατάλυμα!',
        'free_booking' => '✅ Δωρεάν Δέσμευση',
        'free_booking_desc' => 'Πατήστε το κουμπί για να κατοχυρώσετε το ξενοδοχείο σας αμέσως.',
        'digital_desc' => 'Θα μεταφερθείτε στο ασφαλές ψηφιακό περιβάλλον για να ολοκληρώσετε την άμεση πληρωμή σας γρήγορα και με ασφάλεια.',
        'summary_title' => 'Σύνοψη Κράτησης',
        'hotel_cost' => 'Διαμονή (%s ημέρες)',
        'tickets_cost' => 'Εισιτήρια / Οχήματα',
        'extras_cost' => 'Πρόσθετες Παροχές',
        'pay_today' => 'Πληρωτέο ΤΩΡΑ:',
        'balance' => 'Υπόλοιπο:',
        'balance_later' => 'Σε αργότερο χρόνο:',
        'balance_property' => 'Στο Κατάλυμα:',
        'grand_total' => 'ΣΥΝΟΛΙΚΟ ΚΟΣΤΟΣ:',
        'ssl_secure' => 'Χρησιμοποιούμε κρυπτογράφηση SSL 256-bit για την απόλυτη ασφάλεια των συναλλαγών σας.',
        'dest_not_found' => 'Προορισμός δεν βρέθηκε.',
        'error' => 'Σφάλμα: ',
        
        // JS strings
        'js_alert_card' => 'Παρακαλώ συμπληρώστε σωστά όλα τα στοιχεία της κάρτας σας.',
        'js_btn_pay' => '🔒 Πληρωμή ',
        'js_btn_digital' => '📱 Πληρωμή Digital Wallet',
        'js_btn_guarantee' => '🔒 Δέσμευση Κράτησης',
        'js_btn_tickets' => ' (Εισιτήρια)',
        'js_btn_free' => '✅ Ολοκλήρωση Χωρίς Κάρτα',
        'js_btn_loading' => '🔄 Επεξεργασία...',
        'js_btn_success' => '✅ Επιτυχής Κράτηση!'
    ],
    'en' => [
        'page_title' => 'Step 4: Payment | Smart Travel Planner',
        'secure_env' => 'Secure Environment',
        'back_btn' => '← BACK',
        'step1' => 'Accommodation',
        'step2' => 'Transport',
        'step3' => 'Details',
        'step4' => 'Payment',
        'payment_title' => 'Select Completion Method',
        'pay_now' => 'Pay Now',
        'pay_later' => '3 Days Before',
        'pay_property' => 'At Property',
        'pay_digital' => 'Digital Pay',
        'alert_now' => '<strong>Instant charge:</strong> You will pay the entire booking amount (Accommodation + Transport) today.',
        'card_name' => 'Cardholder Name',
        'card_name_ph' => 'e.g., JOHN DOE',
        'card_num' => 'Card Number',
        'card_exp' => 'Expiration Date',
        'alert_later' => '<strong>Booking without today\'s charge!</strong> The amount will be automatically charged to your card 3 days before arrival. Enter a card only for guarantee.',
        'card_num_guarantee' => 'Guarantee Card Number',
        'alert_split' => '<strong>Cost Split:</strong> The Hotel (<strong>%s€</strong>) will be paid on-site. Because you selected Transport/Extras (<strong>%s€</strong>), these must be paid now.',
        'alert_free' => '<strong>No payment required now!</strong> <br><br> You did not select transport. You do not need to enter a credit card. You will pay the entire accommodation amount directly at the property!',
        'free_booking' => '✅ Free Guarantee',
        'free_booking_desc' => 'Click the button to secure your hotel immediately.',
        'digital_desc' => 'You will be redirected to the secure digital environment to complete your instant payment quickly and safely.',
        'summary_title' => 'Booking Summary',
        'hotel_cost' => 'Accommodation (%s days)',
        'tickets_cost' => 'Tickets / Vehicles',
        'extras_cost' => 'Additional Extras',
        'pay_today' => 'Pay NOW:',
        'balance' => 'Balance:',
        'balance_later' => 'At a later time:',
        'balance_property' => 'At Property:',
        'grand_total' => 'TOTAL COST:',
        'ssl_secure' => 'We use 256-bit SSL encryption for the absolute security of your transactions.',
        'dest_not_found' => 'Destination not found.',
        'error' => 'Error: ',
        
        // JS strings
        'js_alert_card' => 'Please fill in all your card details correctly.',
        'js_btn_pay' => '🔒 Pay ',
        'js_btn_digital' => '📱 Digital Wallet Payment',
        'js_btn_guarantee' => '🔒 Guarantee Booking',
        'js_btn_tickets' => ' (Tickets)',
        'js_btn_free' => '✅ Complete Without Card',
        'js_btn_loading' => '🔄 Processing...',
        'js_btn_success' => '✅ Booking Successful!'
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
    if (!$dest) die($t_lang['dest_not_found']);

    // --- ΑΝΑΚΤΗΣΗ & ΥΠΟΛΟΓΙΣΜΟΣ ΠΟΣΩΝ ---
    $selected_dates_arr = explode('|', $_POST['selected_dates']);
    $check_in_date_str = trim($selected_dates_arr[0]);
    $room_type = $_POST['room_type']; 
    $h_data = explode('|', $_POST['hotel_data']); 
    
    $outbound_data = explode('|', $_POST['outbound_transport']); 
    $return_data = explode('|', $_POST['return_transport']);
    
    $out_type = trim($outbound_data[2] ?? 'none');
    $ret_type = trim($return_data[2] ?? 'none');
    $has_transport = ($out_type !== 'none' || $ret_type !== 'none');
    
    $has_car_rental = isset($_POST['rent_car']) && !empty($_POST['rent_car']);
    $rent_car_cost = 0;
    if ($has_car_rental) {
        $rc_data = explode('|', $_POST['rent_car']);
        $rent_car_cost = floatval($rc_data[1] ?? 0);
    }

    $outbound_cost = floatval($outbound_data[1] ?? 0);
    $return_cost = floatval($return_data[1] ?? 0);
    $transport_cost = $outbound_cost + $return_cost + $rent_car_cost;

    // --- ΔΙΑΧΩΡΙΣΜΟΣ ΠΟΣΩΝ ---
    $hotel_cost = floatval($h_data[1]); 
    
    $extras_cost = 0;
    $extras_arr = [];

    if ($has_car_rental) {
        $car_cat = $_POST['car_category'] ?? 'economy';
        $driver_license = strtoupper(trim($_POST['driver_license'] ?? ''));
        $cat_name = "Economy";
        if ($car_cat == 'standard') { $extras_cost += (15 * $days); $cat_name = "Standard"; }
        if ($car_cat == 'suv') { $extras_cost += (35 * $days); $cat_name = "SUV"; }
        $extras_arr[] = "ΙΧ: $cat_name (Δίπλωμα: $driver_license)";
    }

    if (isset($_POST['extra_luggage']) && $_POST['extra_luggage'] > 0) {
        $lugs = (int)$_POST['extra_luggage'];
        $extras_cost += ($lugs * 25); 
        $extras_arr[] = "$lugs x Βαλίτσα 23kg";
    }
    if (isset($_POST['ferry_vehicle']) && $_POST['ferry_vehicle'] !== 'none') {
        $v_type = $_POST['ferry_vehicle'];
        $plate = strtoupper(trim($_POST['license_plate'] ?? ''));
        if ($v_type == 'moto') { $extras_cost += 30; $extras_arr[] = "Μοτοσυκλέτα (Πιν: $plate)"; }
        if ($v_type == 'car') { $extras_cost += 80; $extras_arr[] = "ΙΧ Πλοίο (Πιν: $plate)"; }
    }

    $other_cost = $transport_cost + $extras_cost; // Μεταφορικά + Extras
    $grand_total = $hotel_cost + $other_cost;     // Τελικό Σύνολο

    // --- ΥΠΟΛΟΓΙΣΜΟΣ ΗΜΕΡΟΜΗΝΙΩΝ ---
    $date_parts = explode('/', $check_in_date_str);
    if(count($date_parts) == 3) {
        $check_in_timestamp = strtotime($date_parts[2] . '-' . $date_parts[1] . '-' . $date_parts[0]);
    } else {
        $check_in_timestamp = strtotime('+10 days'); 
    }
    
    $charge_timestamp = strtotime('-3 days', $check_in_timestamp);
    if ($charge_timestamp <= time()) {
        $charge_date_formatted = "Σήμερα (Άμεσα)";
    } else {
        $charge_date_formatted = date('d/m/Y', $charge_timestamp);
    }
    $checkin_formatted = date('d/m/Y', $check_in_timestamp);

    // --- ΕΠΕΞΕΡΓΑΣΙΑ E-TICKETS & ΕΠΙΒΑΤΩΝ ---
    $t_dep = ($lang == 'en') ? "Departure: " : "Αναχώρηση: ";
    $t_ret = ($lang == 'en') ? " | Return: " : " | Επιστροφή: ";
    $transport_method = $t_dep . trim($outbound_data[0]) . $t_ret . trim($return_data[0]);
    if ($has_car_rental) {
        $t_car = ($lang == 'en') ? " | + Car Rental (" : " | + Ενοικίαση Οχήματος (";
        $t_days = ($lang == 'en') ? " Days)" : " Ημέρες)";
        $transport_method .= $t_car . $days . $t_days;
    }
    
    $passenger_details = "";
    if ($has_transport) {
        $pnr = "STP-" . strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));
        $t_pnr = ($lang == 'en') ? "🎫 E-TICKET CODE (PNR): " : "🎫 ΚΩΔΙΚΟΣ E-TICKET (PNR): ";
        $passenger_details .= $t_pnr . $pnr . "\n-------------------------------------\n";
        $passengers = [];
        for ($i = 1; $i <= $persons; $i++) {
            $p_name = trim($_POST["pass_name_$i"] ?? '');
            $p_id = trim($_POST["pass_id_$i"] ?? '');
            if (!empty($p_name)) {
                $p_str = strtoupper($p_name);
                if (!empty($p_id)) $p_str .= " (ID: " . strtoupper($p_id) . ")";
                $t_pass = ($lang == 'en') ? "Passenger" : "Επιβάτης";
                $passengers[] = "$t_pass $i: $p_str";
            }
        }
        if (!empty($passengers)) $passenger_details .= implode("\n", $passengers);
    } else {
        $passenger_details = ($lang == 'en') ? "Hotel Booking in account name." : "Κράτηση Ξενοδοχείου στο όνομα του λογαριασμού.";
    }

    if (!empty($extras_arr)) $passenger_details .= "\n\n[EXTRAS: " . implode(" | ", $extras_arr) . "]";

    // ==========================================
    // ΤΕΛΙΚΗ ΥΠΟΒΟΛΗ ΚΡΑΤΗΣΗΣ
    // ==========================================
    if (isset($_POST['process_payment'])) {
        $payment_type = $_POST['payment_method_selected'] ?? 'Άμεση Πληρωμή (Κάρτα)';

        // 1. Καθορισμός του σωστού Status
        $booking_status = 'Επιβεβαιώθηκε'; 
        if (strpos($payment_type, '3 μέρες') !== false) {
            $booking_status = 'Εκκρεμεί'; 
        }

        // 2. Μετατροπή ημερομηνιών σε YYYY-MM-DD
        $db_check_in = date('Y-m-d', strtotime(str_replace('/', '-', trim($selected_dates_arr[0]))));
        $db_check_out = date('Y-m-d', strtotime(str_replace('/', '-', trim($selected_dates_arr[1]))));

        // 3. Εισαγωγή στη βάση
        $insert = $pdo->prepare("INSERT INTO reservations (user_id, destination_name, check_in, check_out, persons, room_type, hotel_name, transport_method, passenger_details, total_price, payment_method, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $insert->execute([$_SESSION['user_id'], $dest['name_gr'], $db_check_in, $db_check_out, $persons, $room_type, $h_data[0], $transport_method, $passenger_details, $grand_total, $payment_type, $booking_status]);

        header("Location: ../profile.php?success=1"); 
        exit();
    }

} catch(PDOException $e) { die($t_lang['error'] . $e->getMessage()); }
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $t_lang['page_title']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #0f172a; --secondary: #3b82f6; --bg-gradient: #f8fafc; --text-main: #334155; --text-muted: #64748b; --success: #10b981; --border: #e2e8f0;}
        body { margin: 0; font-family: 'Inter', sans-serif; background: var(--bg-gradient); color: var(--text-main); min-height: 100vh;}
        
        /* HEADER (ΑΝΑΝΕΩΜΕΝΟ ΓΙΑ ΝΑ ΜΗΝ ΧΑΛΑΕΙ) */
        header { background: white; border-bottom: 1px solid var(--border); padding: 15px 5%; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.02); position: sticky; top: 0; z-index: 1000; box-sizing: border-box;}
        .brand { display: flex; align-items: center; gap: 12px; text-decoration: none;}
        .brand h2 { margin: 0; font-size: 20px; font-weight: 900; color: var(--primary);}
        
        .header-actions { display: flex; align-items: center; gap: 15px; }
        .secure-lock { display: flex; align-items: center; gap: 8px; font-size: 13.5px; font-weight: 700; color: var(--success);}
        .btn-cancel { border: 1px solid var(--border); color: var(--text-main); text-decoration: none; font-weight: 700; font-size: 13.5px; background: #f8fafc; padding: 8px 16px; border-radius: 8px; transition: 0.3s; display: flex; align-items: center;}
        .btn-cancel:hover { background: #e2e8f0; }

        /* WIZARD NAV */
        .wizard-nav { display: flex; align-items: center; justify-content: center; gap: 10px; padding: 30px 20px;}
        .wizard-step { display: flex; align-items: center; gap: 8px; font-weight: 700; font-size: 14.5px;}
        .step-completed { color: var(--success); }
        .step-completed .w-num { background: var(--success); color: white; width: 26px; height: 26px; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-size: 13px;}
        .step-active { color: var(--secondary); }
        .step-active .w-num { background: var(--secondary); color: white; width: 26px; height: 26px; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-size: 13px; box-shadow: 0 0 0 4px rgba(59,130,246,0.2);}
        .w-line { width: 35px; height: 2px; background: var(--success); border-radius: 2px;}

        .container { max-width: 1200px; margin: 0 auto 50px auto; padding: 0 20px; display: grid; grid-template-columns: 1.3fr 0.7fr; gap: 40px; align-items: start;}
        
        /* ALERT BOXES */
        .alert-box { padding: 20px; border-radius: 14px; font-size: 14.5px; margin-bottom: 25px; line-height: 1.6; }
        .alert-box.info { background: #eff6ff; color: #1e3a8a; border: 1px solid #bfdbfe; }
        .alert-box.warning { background: #fffbeb; color: #854d0e; border: 1px solid #fde68a; }
        .alert-box.success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }

        /* PAYMENT BOX & METHODS */
        .payment-box { background: white; border-radius: 20px; box-shadow: 0 15px 40px rgba(0,0,0,0.05); padding: 35px; border: 1px solid var(--border);}
        .payment-box h3 { margin-top: 0; font-size: 22px; color: var(--primary); margin-bottom: 20px; font-weight: 900;}
        
        .methods { display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 12px; margin-bottom: 30px; border-bottom: 2px solid #f1f5f9; padding-bottom: 20px;}
        .method-tab { display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 8px; padding: 15px 10px; border-radius: 14px; font-weight: 700; font-size: 13.5px; cursor: pointer; border: 2px solid #e2e8f0; transition: all 0.2s ease; color: var(--text-muted); background: #f8fafc; text-align: center;}
        .method-tab span { font-size: 22px; margin-bottom: 2px;}
        .method-tab:hover { background: #f1f5f9; border-color: #cbd5e1; transform: translateY(-2px);}
        .method-tab.active { background: #eff6ff; color: var(--secondary); border-color: var(--secondary); box-shadow: 0 4px 15px rgba(59, 130, 246, 0.15);}

        .tab-content { display: none; animation: fadeIn 0.4s ease-out; }
        .tab-content.active { display: block; }
        @keyframes fadeIn { from {opacity:0; transform:translateY(10px);} to {opacity:1; transform:translateY(0);} }

        /* FORM INPUTS */
        .input-group { margin-bottom: 20px;}
        .input-group label { display: block; font-size: 13px; font-weight: 700; color: var(--primary); margin-bottom: 8px;}
        .input-group input { width: 100%; padding: 15px; border-radius: 12px; border: 1px solid #cbd5e1; font-size: 14.5px; font-family: 'Inter', sans-serif; outline: none; transition: 0.3s; box-sizing: border-box;}
        .input-group input:focus { border-color: var(--secondary); box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15); background: #ffffff;}
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px;}

        .paypal-box, .free-box { text-align: center; padding: 40px 20px; background: #f8fafc; border-radius: 16px; border: 2px dashed #cbd5e1;}
        
        /* ORDER SUMMARY - SIDEBAR */
        .summary-box { background: var(--primary); color: white; border-radius: 20px; padding: 35px; box-shadow: 0 20px 50px rgba(15,23,42,0.2); position: sticky; top: 100px;}
        .summary-box h3 { margin-top: 0; font-size: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 15px; margin-bottom: 20px; font-weight: 800;}
        
        .s-item { display: flex; justify-content: space-between; margin-bottom: 12px; font-size: 14.5px; color: #cbd5e1;}
        .s-item strong { color: white; font-weight: 700;}
        
        .split-box { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 14px; padding: 20px; margin-top: 25px;}
        .split-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; font-size: 14.5px;}
        .split-row:last-child { margin-bottom: 0; padding-top: 15px; border-top: 1px dashed rgba(255,255,255,0.2); }

        .btn-pay { display: flex; justify-content: center; align-items: center; gap: 10px; width: 100%; background: linear-gradient(135deg, var(--secondary), #1d4ed8); color: white; border: none; padding: 20px; border-radius: 14px; font-size: 16.5px; font-weight: 900; cursor: pointer; margin-top: 30px; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 10px 25px rgba(59, 130, 246, 0.4); text-transform: uppercase; letter-spacing: 0.5px;}
        .btn-pay:hover { transform: translateY(-4px); box-shadow: 0 15px 35px rgba(59, 130, 246, 0.5); }
        .btn-pay.success { background: linear-gradient(135deg, var(--success), #059669); box-shadow: 0 10px 25px rgba(16, 185, 129, 0.4);}
        .btn-pay.loading { background: #64748b; pointer-events: none; animation: pulse 1.5s infinite; box-shadow: none;}
        
        @keyframes pulse { 0%{opacity:1;} 50%{opacity:0.7;} 100%{opacity:1;} }

        .desktop-only { display: flex; }
        
        /* =========================================================
           📱 RESPONSIVE IPHONE & MOBILE ΣΥΣΚΕΥΕΣ
           ========================================================= */
        @media (max-width: 950px) { 
            .container { grid-template-columns: 1fr; gap: 20px;} 
            .summary-box { position: static; margin-bottom: 30px;} 
        }

        @media (max-width: 600px) {
            /* 1. Header */
            header { padding: 12px 15px; }
            .brand h2 { font-size: 16px; }
            .brand svg { width: 30px; height: 30px; }
            .desktop-only { display: none !important; } /* Κρύβουμε το λουκέτο για να μη στριμώχνεται το header */
            .btn-cancel { padding: 8px 12px; font-size: 12px; }

            /* 2. Wizard Nav (Βήματα) */
            .wizard-nav { padding: 15px 10px; flex-wrap: wrap; gap: 8px; margin-bottom: 10px;}
            .wizard-step { font-size: 12px; }
            .w-num { width: 22px; height: 22px; font-size: 11px; }
            .w-line { width: 20px; }

            /* 3. Κουτί Πληρωμής */
            .payment-box { padding: 20px 15px; border-radius: 18px; margin-bottom: 25px;}
            .payment-box h3 { font-size: 18px; text-align: center; }
            
            /* TABS: 2x2 Grid στο κινητό για να μη στριμώχνονται και να φαίνονται όμορφα! */
            .methods { grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 20px; padding-bottom: 0; border-bottom: none;}
            .method-tab { padding: 12px 5px; font-size: 12px; border-radius: 12px;}
            .method-tab span { font-size: 20px; }

            .alert-box { padding: 15px; font-size: 13.5px; margin-bottom: 20px;}

            /* 4. Φόρμα (Inputs) */
            .input-group { margin-bottom: 15px; }
            .input-group label { font-size: 12.5px; }
            .input-group input { padding: 14px 15px; font-size: 14px; border-radius: 10px;}
            .form-row { grid-template-columns: 1fr 1fr; gap: 10px; } /* Ημ. Λήξης & CVV δίπλα-δίπλα */

            /* 5. Sidebar - Σύνοψη */
            .summary-box { padding: 25px 20px; border-radius: 18px; margin-top: 10px;}
            .summary-box h3 { font-size: 18px; margin-bottom: 15px;}
            .s-item { font-size: 13.5px; margin-bottom: 10px;}
            
            .split-box { padding: 15px; margin-top: 20px; }
            .split-row { font-size: 13.5px; margin-bottom: 10px; }
            .split-row:last-child strong { font-size: 22px; }

            .btn-pay { font-size: 15px; padding: 18px; margin-top: 25px; border-radius: 12px;}
        }
    </style>
</head>
<body>

<header>
    <div class="brand">
        <svg width="40" height="40" viewBox="0 0 50 50" fill="none">
            <rect x="3" y="3" width="44" height="44" rx="12" fill="rgba(255,255,255,0.1)" stroke="#3b82f6" stroke-width="2"/>
            <text x="25" y="26" font-family="'Inter', sans-serif" font-weight="900" font-size="16" fill="#0f172a" text-anchor="middle" dominant-baseline="middle">STP</text>
        </svg>
        <h2>Smart Travel Planner</h2>
    </div>
    
    <div class="header-actions">
        <div class="secure-lock desktop-only">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0"></path></svg>
            <?php echo $t_lang['secure_env']; ?>
        </div>
        
        <form action="checkout.php?id=<?php echo $id; ?>&days=<?php echo $days; ?>&persons=<?php echo $persons; ?>&lang=<?php echo $lang; ?>" method="POST" style="margin: 0; padding: 0; display: flex; align-items: center;">
            <?php foreach($_POST as $key => $val): ?>
                <input type="hidden" name="<?php echo htmlspecialchars($key); ?>" value="<?php echo htmlspecialchars($val); ?>">
            <?php endforeach; ?>
            <button type="submit" style="cursor: pointer; background: #f8fafc; border: 1px solid #e2e8f0; color: #334155; font-family: 'Inter', sans-serif; font-weight: 700; font-size: 13.5px; padding: 8px 16px; border-radius: 8px; display: flex; align-items: center; outline: none; margin: 0;">
                <?php echo $t_lang['back_btn']; ?>
            </button>
        </form>
    </div>
</header>

<div class="wizard-nav">
    <div class="wizard-step step-completed"><div class="w-num">✓</div> <?php echo $t_lang['step1']; ?></div><div class="w-line"></div>
    <div class="wizard-step step-completed"><div class="w-num">✓</div> <?php echo $t_lang['step2']; ?></div><div class="w-line"></div>
    <div class="wizard-step step-completed"><div class="w-num">✓</div> <?php echo $t_lang['step3']; ?></div><div class="w-line"></div>
    <div class="wizard-step step-active"><div class="w-num">4</div> <?php echo $t_lang['step4']; ?></div>
</div>

<div class="container">
    
    <div class="payment-box">
        <h3><?php echo $t_lang['payment_title']; ?></h3>
        
        <div class="methods">
            <div class="method-tab active" data-target="tab-card" data-val="Άμεση Πληρωμή (Κάρτα)">
                <span>💳</span> <?php echo $t_lang['pay_now']; ?>
            </div>
            <div class="method-tab" data-target="tab-later" data-val="Πληρωμή 3 μέρες πριν">
                <span>⏳</span> <?php echo $t_lang['pay_later']; ?>
            </div>
            <div class="method-tab" data-target="tab-property" data-val="Πληρωμή στο Κατάλυμα">
                <span>🏨</span> <?php echo $t_lang['pay_property']; ?>
            </div>
            <div class="method-tab" data-target="tab-digital" data-val="Digital Pay (Apple/PayPal)">
                <span>📱</span> <?php echo $t_lang['pay_digital']; ?>
            </div>
        </div>

        <div class="tab-content active" id="tab-card">
            <div class="alert-box info">
                <?php echo $t_lang['alert_now']; ?>
            </div>
            <div class="input-group">
                <label><?php echo $t_lang['card_name']; ?></label>
                <input type="text" id="ccName1" placeholder="<?php echo $t_lang['card_name_ph']; ?>" oninput="this.value = this.value.toUpperCase();">
            </div>
            <div class="input-group">
                <label><?php echo $t_lang['card_num']; ?></label>
                <input type="text" id="ccNum1" placeholder="0000 0000 0000 0000" maxlength="19" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(.{4})/g, '$1 ').trim();">
            </div>
            <div class="form-row">
                <div class="input-group">
                    <label><?php echo $t_lang['card_exp']; ?></label>
                    <input type="text" id="ccExp1" placeholder="MM/YY" maxlength="5" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/^([2-9])$/g, '0$1').replace(/^(1[3-9])$/g, '01').replace(/^([0-1]{1}[0-9]{1})([0-9]{1,2}).*/g, '$1/$2');">
                </div>
                <div class="input-group">
                    <label>CVV</label>
                    <input type="password" id="ccCvv1" placeholder="123" maxlength="3" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                </div>
            </div>
        </div>

        <div class="tab-content" id="tab-later">
            <div class="alert-box warning">
                <?php echo $t_lang['alert_later']; ?>
            </div>
            <div class="input-group">
                <label><?php echo $t_lang['card_name']; ?></label>
                <input type="text" id="ccName2" placeholder="<?php echo $t_lang['card_name_ph']; ?>" oninput="this.value = this.value.toUpperCase();">
            </div>
            <div class="input-group">
                <label><?php echo $t_lang['card_num_guarantee']; ?></label>
                <input type="text" id="ccNum2" placeholder="0000 0000 0000 0000" maxlength="19" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(.{4})/g, '$1 ').trim();">
            </div>
            <div class="form-row">
                <div class="input-group">
                    <label><?php echo $t_lang['card_exp']; ?></label>
                    <input type="text" id="ccExp2" placeholder="MM/YY" maxlength="5" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/^([2-9])$/g, '0$1').replace(/^(1[3-9])$/g, '01').replace(/^([0-1]{1}[0-9]{1})([0-9]{1,2}).*/g, '$1/$2');">
                </div>
                <div class="input-group">
                    <label>CVV</label>
                    <input type="password" id="ccCvv2" placeholder="123" maxlength="3" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                </div>
            </div>
        </div>

        <div class="tab-content" id="tab-property">
            <?php if ($other_cost > 0): ?>
                <div class="alert-box info">
                    <?php echo sprintf($t_lang['alert_split'], number_format($hotel_cost, 2), number_format($other_cost, 2)); ?>
                </div>
                <div class="input-group">
                    <label><?php echo $t_lang['card_name']; ?></label>
                    <input type="text" id="ccName3" placeholder="<?php echo $t_lang['card_name_ph']; ?>" oninput="this.value = this.value.toUpperCase();">
                </div>
                <div class="input-group">
                    <label><?php echo $t_lang['card_num']; ?></label>
                    <input type="text" id="ccNum3" placeholder="0000 0000 0000 0000" maxlength="19" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(.{4})/g, '$1 ').trim();">
                </div>
                <div class="form-row">
                    <div class="input-group">
                        <label><?php echo $t_lang['card_exp']; ?></label>
                        <input type="text" id="ccExp3" placeholder="MM/YY" maxlength="5" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/^([2-9])$/g, '0$1').replace(/^(1[3-9])$/g, '01').replace(/^([0-1]{1}[0-9]{1})([0-9]{1,2}).*/g, '$1/$2');">
                    </div>
                    <div class="input-group">
                        <label>CVV</label>
                        <input type="password" id="ccCvv3" placeholder="123" maxlength="3" oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                    </div>
                </div>
            <?php else: ?>
                <div class="alert-box success">
                    <?php echo $t_lang['alert_free']; ?>
                </div>
                <div class="free-box">
                    <h3 style="margin:0 0 10px 0; color:var(--success);"><?php echo $t_lang['free_booking']; ?></h3>
                    <p style="margin:0; font-size:14.5px; color:var(--text-muted);"><?php echo $t_lang['free_booking_desc']; ?></p>
                </div>
            <?php endif; ?>
        </div>

        <div class="tab-content" id="tab-digital">
            <div class="free-box">
                <h3 style="margin:0 0 15px 0;">Apple Pay / PayPal</h3>
                <p style="font-size:14.5px; color:var(--text-muted); line-height:1.6;"><?php echo $t_lang['digital_desc']; ?></p>
            </div>
        </div>

    </div>

    <div class="summary-box">
        <h3><?php echo $t_lang['summary_title']; ?></h3>
        <div class="s-item"><span><?php echo sprintf($t_lang['hotel_cost'], $days); ?></span> <strong><?php echo number_format($hotel_cost, 2); ?>€</strong></div>
        <?php if($transport_cost > 0): ?><div class="s-item"><span><?php echo $t_lang['tickets_cost']; ?></span> <strong><?php echo number_format($transport_cost, 2); ?>€</strong></div><?php endif; ?>
        <?php if($extras_cost > 0): ?><div class="s-item"><span><?php echo $t_lang['extras_cost']; ?></span> <strong><?php echo number_format($extras_cost, 2); ?>€</strong></div><?php endif; ?>
        
        <div class="split-box">
            <div class="split-row">
                <span style="color:#94a3b8;" id="label_now"><?php echo $t_lang['pay_today']; ?></span>
                <strong style="color:#38bdf8; font-size:19px;" id="val_now"><?php echo number_format($grand_total, 2); ?>€</strong>
            </div>
            <div class="split-row">
                <span style="color:#94a3b8;" id="label_later"><?php echo $t_lang['balance']; ?></span>
                <strong style="color:#34d399; font-size:19px;" id="val_later">0.00€</strong>
            </div>
            <div class="split-row" style="margin-top:5px; border-top:1px solid rgba(255,255,255,0.1); padding-top:15px;">
                <span><?php echo $t_lang['grand_total']; ?></span>
                <strong style="color:white; font-size:26px;"><?php echo number_format($grand_total, 2); ?>€</strong>
            </div>
        </div>

        <form id="realPaymentForm" action="" method="POST">
            <?php foreach($_POST as $key => $val): ?>
                <input type="hidden" name="<?php echo htmlspecialchars($key); ?>" value="<?php echo htmlspecialchars($val); ?>">
            <?php endforeach; ?>
            <input type="hidden" name="process_payment" value="1">
            <input type="hidden" name="payment_method_selected" id="payment_method_selected" value="Άμεση Πληρωμή (Κάρτα)">
            
            <button type="button" class="btn-pay" id="triggerPaymentBtn">
                <?php echo $t_lang['js_btn_pay']; ?><?php echo number_format($grand_total, 2); ?>€
            </button>
        </form>

        <p style="text-align:center; font-size:12.5px; color:rgba(255,255,255,0.5); margin-top:25px;"><?php echo $t_lang['ssl_secure']; ?></p>
    </div>

</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const tabs = document.querySelectorAll('.method-tab');
    const contents = document.querySelectorAll('.tab-content');
    const triggerBtn = document.getElementById('triggerPaymentBtn');
    const hiddenMethodInput = document.getElementById('payment_method_selected');

    const hotelCost = <?php echo $hotel_cost; ?>;
    const otherCost = <?php echo $other_cost; ?>;
    const grandTotal = <?php echo $grand_total; ?>;

    const labelNow = document.getElementById('label_now');
    const valNow = document.getElementById('val_now');
    const labelLater = document.getElementById('label_later');
    const valLater = document.getElementById('val_later');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('active'));
            contents.forEach(c => c.classList.remove('active'));
            
            tab.classList.add('active');
            const targetId = tab.getAttribute('data-target');
            document.getElementById(targetId).classList.add('active');
            
            hiddenMethodInput.value = tab.getAttribute('data-val');

            // ΔΥΝΑΜΙΚΗ ΑΛΛΑΓΗ ΤΙΜΩΝ
            if (targetId === 'tab-card' || targetId === 'tab-digital') {
                valNow.innerText = grandTotal.toFixed(2) + '€';
                valLater.innerText = '0.00€';
                labelLater.innerText = '<?php echo $t_lang['balance']; ?>';
                triggerBtn.innerHTML = targetId === 'tab-card' ? ('<?php echo $t_lang['js_btn_pay']; ?>' + grandTotal.toFixed(2) + '€') : '<?php echo $t_lang['js_btn_digital']; ?>';
            } 
            else if (targetId === 'tab-later') {
                valNow.innerText = '0.00€';
                valLater.innerText = grandTotal.toFixed(2) + '€';
                labelLater.innerText = '<?php echo $t_lang['balance_later']; ?>';
                triggerBtn.innerHTML = '<?php echo $t_lang['js_btn_guarantee']; ?>';
            } 
            else if (targetId === 'tab-property') {
                valNow.innerText = otherCost.toFixed(2) + '€';
                valLater.innerText = hotelCost.toFixed(2) + '€';
                labelLater.innerText = '<?php echo $t_lang['balance_property']; ?>';
                
                if (otherCost > 0) {
                    triggerBtn.innerHTML = '<?php echo $t_lang['js_btn_pay']; ?>' + otherCost.toFixed(2) + '€<?php echo $t_lang['js_btn_tickets']; ?>';
                } else {
                    triggerBtn.innerHTML = '<?php echo $t_lang['js_btn_free']; ?>';
                }
            }
        });
    });

    const realForm = document.getElementById('realPaymentForm');

    triggerBtn.addEventListener('click', function() {
        const activeTab = document.querySelector('.method-tab.active').getAttribute('data-target');
        
        function validateCard(suffix) {
            let num = document.getElementById('ccNum'+suffix).value.replace(/\s/g, '');
            let name = document.getElementById('ccName'+suffix).value;
            let exp = document.getElementById('ccExp'+suffix).value;
            let cvv = document.getElementById('ccCvv'+suffix).value;
            if(name === '' || num.length < 16 || exp.length < 5 || cvv.length < 3) {
                alert("<?php echo $t_lang['js_alert_card']; ?>");
                return false;
            }
            return true;
        }

        // VALIDATION
        if(activeTab === 'tab-card') { if(!validateCard('1')) return; }
        if(activeTab === 'tab-later') { if(!validateCard('2')) return; }
        if(activeTab === 'tab-property' && otherCost > 0) { if(!validateCard('3')) return; }

        // LOADING EFFECT
        triggerBtn.classList.add('loading');
        triggerBtn.innerHTML = '<?php echo $t_lang['js_btn_loading']; ?>';

        setTimeout(() => {
            triggerBtn.classList.remove('loading');
            triggerBtn.classList.add('success');
            triggerBtn.innerHTML = '<?php echo $t_lang['js_btn_success']; ?>';
            
            setTimeout(() => {
                realForm.submit();
            }, 800);
        }, 2000);
    });
});
</script>
</body>
</html>