<?php
session_start();

// --- ΣΩΣΤΗ ΔΙΑΧΕΙΡΙΣΗ ΔΙΓΛΩΣΣΙΑΣ ΜΕΣΩ SESSION ---
// 1. Αν περαστεί ρητά στο URL, το αποθηκεύουμε
if (isset($_GET['lang']) && in_array($_GET['lang'], ['gr', 'en'])) {
    $_SESSION['lang'] = $_GET['lang'];
} 
// 2. Αν ΔΕΝ υπάρχει στο URL και ο χρήστης έρχεται από το index (όπου είχε επιλέξει γλώσσα)
elseif (!isset($_GET['lang']) && isset($_SERVER['HTTP_REFERER'])) {
    if (strpos($_SERVER['HTTP_REFERER'], 'lang=en') !== false) {
        $_SESSION['lang'] = 'en';
    } elseif (strpos($_SERVER['HTTP_REFERER'], 'lang=gr') !== false) {
        $_SESSION['lang'] = 'gr';
    }
}

// Τραβάμε τη γλώσσα από το Session. Αν δεν υπάρχει, βάζουμε προεπιλογή τα Ελληνικά ('gr')
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'gr';
// ------------------------------------------------

if (!isset($_SESSION['user_id'])) {
    header("Location: auth/login.php?lang=" . $lang . "&auth_required=1");
    exit();
}

$translations = [
    'gr' => [
        'subtitle' => 'Ο Προσωπικός σας Ταξιδιωτικός Σύμβουλος',
        'page_title' => 'Το Προφίλ μου',
        'new_search' => 'Νέα Αναζήτηση',
        'logout' => 'Αποσύνδεση',
        'hero_hello' => 'Γεια σου,',
        'hero_desc' => 'Διαχειρίσου τις κρατήσεις σου, κατέβασε τα e-tickets στο κινητό σου και ετοιμάσου για αναχώρηση.',
        'msg_success' => 'Η κράτησή σας ολοκληρώθηκε με επιτυχία! Τα εισιτήρια έχουν εκδοθεί.',
        'msg_deleted' => 'Η κράτηση ακυρώθηκε και διεγράφη επιτυχώς.',
        'badge_confirmed' => 'Επιβεβαιωμενη',
        'hotel_section' => 'Κρατηση Ξενοδοχειου',
        'hotel_label' => 'Κατάλυμα:',
        'transport_section' => 'Μεταφορικα',
        'transport_label' => 'Μέσο:',
        'pnr_label' => 'Κωδ. PNR:',
        'car_section' => 'Ενοικιαση Οχηματος',
        'car_type' => 'Τύπος:',
        'car_days' => 'Ημέρες:',
        'car_license' => 'Αριθμ. Διπλώματος:',
        'car_voucher' => 'Κωδ. Voucher:',
        'total_label' => 'Συνολο',
        'btn_tickets' => 'Εισιτήρια',
        'btn_cancel' => 'Ακύρωση',
        'cancel_confirm' => '⚠️ Είστε σίγουροι ότι θέλετε να ακυρώσετε οριστικά αυτή την κράτηση; Η ενέργεια δεν αναιρείται.',
        'empty_title' => 'Δεν έχετε κάνει καμία κράτηση ακόμα.',
        'empty_desc' => 'Ξεκινήστε τώρα και ανακαλύψτε τον επόμενο αγαπημένο σας προορισμό!',
        'empty_btn' => 'Νέα Αναζήτηση Ταξιδιού',
        'footer_desc' => 'Η Smart Travel Planner είναι η κορυφαία πλατφόρμα έξυπνου τουρισμού. Σχεδιάζουμε το τέλειο ταξίδι αποκλειστικά για εσάς.',
        'copyright' => '© 2026 Smart Travel Planner | Όλα τα δικαιώματα διατηρούνται.',
        'modal_title' => 'Ψηφιακά Εισιτήρια (E-Tickets)',
        'btn_download_all' => 'Λήψη Όλων',
        'btn_close' => 'Κλείσιμο',
        // JavaScript translations
        'js_airport' => 'ΑΕΡΟΔΡΟΜΙΟ',
        'js_bus_station' => 'ΣΤΑΘΜΟΣ ΚΤΕΛ',
        'js_port_piraeus' => 'ΛΙΜΑΝΙ ΠΕΙΡΑΙΑ',
        'js_port_rafina' => 'ΛΙΜΑΝΙ ΡΑΦΗΝΑΣ',
        'js_port_volos' => 'ΛΙΜΑΝΙ ΒΟΛΟΥ',
        'js_port_igoumenitsa' => 'ΗΓΟΥΜΕΝΙΤΣΑ',
        'js_port_kyllini' => 'ΛΙΜΑΝΙ ΚΥΛΛΗΝΗΣ',
        'js_no_seat' => 'ΑΝΕΥ ΘΕΣΗΣ',
        'js_vehicle' => 'ΟΧΗΜΑ',
        'js_passenger' => 'ΕΠΙΒΑΤΗΣ',
        'js_seat_type' => 'ΘΕΣΗ / ΤΥΠΟΣ',
        'js_date' => 'ΗΜΕΡΟΜΗΝΙΑ',
        'js_time' => 'ΩΡΑ',
        'js_departure' => 'ΑΝΑΧΩΡΗΣΗ',
        'js_return' => 'ΕΠΙΣΤΡΟΦΗ',
        'js_download_ticket' => 'Λήψη εισιτηρίου',
        'js_downloading' => 'Λήψη...',
        'js_saved' => 'Αποθηκεύτηκε!',
        'js_download_error' => 'Σφάλμα κατά τη λήψη. Παρακαλώ κάντε screenshot την οθόνη σας.',
        'js_downloading_all' => 'Γίνεται λήψη όλων...',
        'js_all_done' => 'Ολοκληρώθηκαν όλα!',
        'js_private_car' => 'ΙΧ ΟΧΗΜΑ',
        'pnr_unavailable' => 'ΜΗ ΔΙΑΘΕΣΙΜΟ',
        // --- ΖΩΝΗ ΚΙΝΔΥΝΟΥ ---
        'delete_title' => 'Ζώνη Κινδύνου',
        'delete_desc' => 'Η διαγραφή του λογαριασμού σας είναι μόνιμη και μη αναστρέψιμη. Θα διαγραφούν όλες οι κρατήσεις, τα εισιτήρια και τα προσωπικά σας δεδομένα.',
        'delete_btn' => 'Διαγραφή Λογαριασμού',
        'delete_confirm' => '⚠️ ΠΡΟΣΟΧΗ! Η διαγραφή του λογαριασμού είναι ΟΡΙΣΤΙΚΗ.\n\nΘα διαγραφούν:\n• Όλες οι κρατήσεις σας\n• Τα εισιτήρια σας\n• Τα στοιχεία του λογαριασμού σας\n\nΕίστε σίγουροι ότι θέλετε να συνεχίσετε;',
        'delete_password_label' => 'Εισάγετε τον κωδικό σας για επιβεβαίωση:',
        'delete_modal_btn' => 'Οριστική Διαγραφή',
        'delete_modal_cancel' => 'Άκυρο',
        'delete_wrong_pass' => 'Λάθος κωδικός. Η διαγραφή ακυρώθηκε.',
        // --- ΑΛΛΑΓΗ ΚΩΔΙΚΟΥ ---
        'chpass_title' => 'Αλλαγή Κωδικού Πρόσβασης',
        'chpass_current' => 'Τρέχων Κωδικός',
        'chpass_new' => 'Νέος Κωδικός',
        'chpass_confirm' => 'Επιβεβαίωση Νέου Κωδικού',
        'chpass_btn' => 'Αλλαγή Κωδικού',
        'chpass_min' => 'Ελάχιστο 6 χαρακτήρες',
        'chpass_mismatch' => 'Οι νέοι κωδικοί δεν ταιριάζουν.',
        'chpass_wrong' => 'Ο τρέχων κωδικός είναι λάθος.',
        'chpass_short' => 'Ο νέος κωδικός πρέπει να έχει τουλάχιστον 6 χαρακτήρες.',
        'chpass_success' => 'Ο κωδικός σας άλλαξε επιτυχώς!'
    ],
    'en' => [
        'subtitle' => 'Your Personal Travel Advisor',
        'page_title' => 'My Profile',
        'new_search' => 'New Search',
        'logout' => 'Logout',
        'hero_hello' => 'Hello,',
        'hero_desc' => 'Manage your bookings, download your e-tickets on your phone and get ready for departure.',
        'msg_success' => 'Your booking was completed successfully! Tickets have been issued.',
        'msg_deleted' => 'The booking was cancelled and deleted successfully.',
        'badge_confirmed' => 'Confirmed',
        'hotel_section' => 'Hotel Booking',
        'hotel_label' => 'Accommodation:',
        'transport_section' => 'Transport',
        'transport_label' => 'Method:',
        'pnr_label' => 'PNR Code:',
        'car_section' => 'Car Rental',
        'car_type' => 'Type:',
        'car_days' => 'Days:',
        'car_license' => 'License No:',
        'car_voucher' => 'Voucher Code:',
        'total_label' => 'Total',
        'btn_tickets' => 'Tickets',
        'btn_cancel' => 'Cancel',
        'cancel_confirm' => '⚠️ Are you sure you want to permanently cancel this booking? This action cannot be undone.',
        'empty_title' => 'You haven\'t made any bookings yet.',
        'empty_desc' => 'Start now and discover your next favorite destination!',
        'empty_btn' => 'New Travel Search',
        'footer_desc' => 'Smart Travel Planner is the leading smart tourism platform. We plan the perfect trip exclusively for you.',
        'copyright' => '© 2026 Smart Travel Planner | All rights reserved.',
        'modal_title' => 'Digital Tickets (E-Tickets)',
        'btn_download_all' => 'Download All',
        'btn_close' => 'Close',
        // JavaScript translations
        'js_airport' => 'AIRPORT',
        'js_bus_station' => 'BUS STATION',
        'js_port_piraeus' => 'PIRAEUS PORT',
        'js_port_rafina' => 'RAFINA PORT',
        'js_port_volos' => 'VOLOS PORT',
        'js_port_igoumenitsa' => 'IGOUMENITSA',
        'js_port_kyllini' => 'KYLLINI PORT',
        'js_no_seat' => 'NO ASSIGNED SEAT',
        'js_vehicle' => 'VEHICLE',
        'js_passenger' => 'PASSENGER',
        'js_seat_type' => 'SEAT / TYPE',
        'js_date' => 'DATE',
        'js_time' => 'TIME',
        'js_departure' => 'DEPARTURE',
        'js_return' => 'RETURN',
        'js_download_ticket' => 'Download ticket',
        'js_downloading' => 'Downloading...',
        'js_saved' => 'Saved!',
        'js_download_error' => 'Error downloading. Please take a screenshot instead.',
        'js_downloading_all' => 'Downloading all...',
        'js_all_done' => 'All done!',
        'js_private_car' => 'PRIVATE CAR',
        'pnr_unavailable' => 'N/A',
        // --- DANGER ZONE ---
        'delete_title' => 'Danger Zone',
        'delete_desc' => 'Deleting your account is permanent and irreversible. All your bookings, tickets, and personal data will be removed.',
        'delete_btn' => 'Delete Account',
        'delete_confirm' => '⚠️ WARNING! Account deletion is PERMANENT.\n\nThe following will be deleted:\n• All your bookings\n• Your tickets\n• Your account details\n\nAre you sure you want to continue?',
        'delete_password_label' => 'Enter your password to confirm:',
        'delete_modal_btn' => 'Permanently Delete',
        'delete_modal_cancel' => 'Cancel',
        'delete_wrong_pass' => 'Wrong password. Deletion cancelled.',
        // --- CHANGE PASSWORD ---
        'chpass_title' => 'Change Password',
        'chpass_current' => 'Current Password',
        'chpass_new' => 'New Password',
        'chpass_confirm' => 'Confirm New Password',
        'chpass_btn' => 'Change Password',
        'chpass_min' => 'Minimum 6 characters',
        'chpass_mismatch' => 'New passwords do not match.',
        'chpass_wrong' => 'Current password is incorrect.',
        'chpass_short' => 'New password must be at least 6 characters long.',
        'chpass_success' => 'Your password has been changed successfully!'
    ]
];

// Ανάκτηση μεταφράσεων (με fallback στα Ελληνικά αν κάτι πάει λάθος)
$t = isset($translations[$lang]) ? $translations[$lang] : $translations['gr'];

$host = 'localhost'; $db = 'smart_travel_planner'; $user = 'root'; $pass = ''; 
$reservations = [];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Διαγραφή κράτησης
    if (isset($_POST['cancel_booking_id'])) {
        $cancel_id = intval($_POST['cancel_booking_id']);
        $del_stmt = $pdo->prepare("DELETE FROM reservations WHERE id = ? AND user_id = ?");
        $del_stmt->execute([$cancel_id, $_SESSION['user_id']]);
        
        // Διατηρούμε τη γλώσσα και κατά την ανακατεύθυνση διαγραφής
        header("Location: profile.php?lang=" . $lang . "&deleted=1");
        exit();
    }

    // --- ΔΙΑΓΡΑΦΗ ΛΟΓΑΡΙΑΣΜΟΥ ΧΡΗΣΤΗ ---
    if (isset($_POST['delete_account']) && isset($_POST['delete_password'])) {
        $uid = $_SESSION['user_id'];
        $pass_input = $_POST['delete_password'];
        
        // Επαλήθευση κωδικού πρόσβασης πριν τη διαγραφή
        $check_stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $check_stmt->execute([$uid]);
        $stored_pass = $check_stmt->fetchColumn();
        
        if ($stored_pass && password_verify($pass_input, $stored_pass)) {
            // 1. Διαγραφή ΟΛΩΝ των κρατήσεων του χρήστη
            $del_res = $pdo->prepare("DELETE FROM reservations WHERE user_id = ?");
            $del_res->execute([$uid]);
            
            // 2. Διαγραφή του λογαριασμού του χρήστη
            $del_user = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $del_user->execute([$uid]);
            
            // 3. Καταστροφή του session και ανακατεύθυνση στην αρχική σελίδα
            session_destroy();
            header("Location: index.php?account_deleted=1");
            exit();
        } else {
            $delete_error = true;
        }
    }

    // --- ΑΛΛΑΓΗ ΚΩΔΙΚΟΥ ΠΡΟΣΒΑΣΗΣ ---
    if (isset($_POST['change_password'])) {
        $uid = $_SESSION['user_id'];
        $current_pass = $_POST['current_password'];
        $new_pass = $_POST['new_password'];
        $confirm_pass = $_POST['confirm_password'];
        
        // Ελέγχους ασφαλείας:
        $check_stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $check_stmt->execute([$uid]);
        $stored_pass = $check_stmt->fetchColumn();
        
        if (!$stored_pass || !password_verify($current_pass, $stored_pass)) {
            // 1. Λάθος τρέχων κωδικός
            $chpass_error = 'wrong';
        } elseif (strlen($new_pass) < 6) {
            // 2. Πολύ μικρός νέος κωδικός
            $chpass_error = 'short';
        } elseif ($new_pass !== $confirm_pass) {
            // 3. Οι νέοι κωδικοί δεν ταιριάζουν
            $chpass_error = 'mismatch';
        } else {
            // Επιτυχία: Κρυπτογράφηση και αποθήκευση νέου κωδικού (password_hash + bcrypt)
            $hashed = password_hash($new_pass, PASSWORD_BCRYPT);
            $upd_stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $upd_stmt->execute([$hashed, $uid]);
            $chpass_success = true;
        }
    }

    $stmt = $pdo->prepare("SELECT * FROM reservations WHERE user_id = :uid ORDER BY id DESC");
    $stmt->execute(['uid' => $_SESSION['user_id']]);
    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $t['page_title']; ?> | Smart Travel Planner</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <style>
        :root { --primary: #0f172a; --secondary: #3b82f6; --accent: #0ea5e9; --bg: #f8fafc; --text: #334155; --text-muted: #64748b; --border: #e2e8f0; }
        body { margin: 0; font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; display: flex; flex-direction: column; overflow-x: hidden;}
        
        /* HEADER */
        header { display: flex; align-items: center; justify-content: space-between; padding: 15px 5%; background: rgba(15, 23, 42, 0.95); border-bottom: 1px solid rgba(255, 255, 255, 0.08); position: sticky; width: 100%; top: 0; z-index: 1000; box-sizing: border-box; backdrop-filter: blur(10px);}
        .brand { display: flex; align-items: center; gap: 15px; text-decoration: none; transition: 0.3s;}
        .brand:hover { transform: scale(1.02); }
        .brand h2 { margin: 0; font-size: 24px; font-weight: 900; background: linear-gradient(to right, #ffffff, #bae6fd); -webkit-background-clip: text; -webkit-text-fill-color: transparent;}
        .brand span { font-size: 11px; font-weight: 600; color: #cbd5e0; letter-spacing: 0.5px; text-transform: uppercase;}
        
        .top-nav { display: flex; align-items: center; gap: 20px; }
        .btn-new-search { background: linear-gradient(135deg, var(--secondary), #0ea5e9); color: white; padding: 10px 20px; border-radius: 12px; text-decoration: none; font-weight: 800; font-size: 14px; transition: 0.3s; display: flex; align-items: center; gap: 6px;}
        .btn-new-search:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(59, 130, 246, 0.4); filter: brightness(1.1);}
        .btn-logout { background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #fca5a5; padding: 9px 18px; border-radius: 12px; text-decoration: none; font-weight: 700; font-size: 14px; transition: 0.3s;}
        .btn-logout:hover { background: #ef4444; color: white;}

        .mobile-only { display: none; }
        .desktop-only { display: flex; }
        .menu-toggle { display: none; flex-direction: column; gap: 6px; cursor: pointer; padding: 5px; }
        .menu-toggle span { display: block; width: 26px; height: 3px; background: white; border-radius: 3px; transition: 0.3s; }

        /* HERO SECTION */
        .profile-hero { position: relative; background: url('https://images.unsplash.com/photo-1499856871958-5b9627545d1a?q=80&w=1920&auto=format&fit=crop') no-repeat center center; background-size: cover; padding: 100px 20px 120px 20px; text-align: center; color: white;}
        .hero-overlay { position: absolute; inset: 0; background: linear-gradient(to bottom, rgba(15, 23, 42, 0.85), rgba(15, 23, 42, 1)); }
        .hero-content { position: relative; z-index: 2; max-width: 900px; margin: 0 auto;}
        .hero-content h1 { font-size: 42px; font-weight: 900; margin: 0 0 15px 0; letter-spacing: -1px; line-height: 1.2; text-shadow: 0 4px 20px rgba(0,0,0,0.5);}
        .hero-content p { font-size: 17px; color: #bae6fd; font-weight: 500; margin: 0; line-height: 1.6; padding: 0 15px;}

        .container { max-width: 1200px; margin: -60px auto 60px auto; padding: 0 20px; flex: 1; width: 100%; box-sizing: border-box; position: relative; z-index: 10;}
        
        .msg-box { padding: 16px 20px; border-radius: 12px; margin-bottom: 30px; font-weight: 700; font-size: 14.5px; display: flex; align-items: center; gap: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.15);}
        .success-msg { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0;}
        .deleted-msg { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca;}

        .cards-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(380px, 1fr)); gap: 30px; align-items: flex-start; }
        .r-card { background: white; border-radius: 20px; border: 1px solid var(--border); overflow: hidden; box-shadow: 0 15px 35px rgba(0,0,0,0.08); transition: 0.3s; display: flex; flex-direction: column; position: relative;}
        .r-card:hover { transform: translateY(-8px); box-shadow: 0 25px 50px rgba(0,0,0,0.15); border-color: #bfdbfe;}
        
        .r-header { background: #0f172a; color: white; padding: 25px 20px; position: relative;}
        .r-header::after { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 5px; background: linear-gradient(90deg, #3b82f6, #10b981);}
        .r-header-top { display: flex; justify-content: space-between; align-items: flex-start; gap: 15px; margin-bottom: 12px;}
        .r-header h3 { margin: 0; font-size: 22px; font-weight: 900; line-height: 1.3;}
        .badge-status { flex-shrink: 0; background: #10b981; color: white; padding: 6px 14px; border-radius: 20px; font-size: 11.5px; font-weight: 900; text-transform: uppercase; box-shadow: 0 0 12px rgba(16, 185, 129, 0.4); letter-spacing: 0.5px;}
        .r-header p { margin: 0; font-size: 14.5px; color: #cbd5e1; display: flex; align-items: center; gap: 8px; font-weight: 600;}

        .r-body { padding: 25px 20px; flex: 1; display: flex; flex-direction: column; gap: 20px;}
        .info-section { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 15px;}
        .info-title { font-size: 13px; color: var(--secondary); text-transform: uppercase; font-weight: 900; letter-spacing: 0.5px; margin-bottom: 10px; border-bottom: 1px solid #e2e8f0; padding-bottom: 8px;}
        .info-row { display: flex; justify-content: space-between; align-items: center; font-size: 13.5px; margin-bottom: 8px;}
        .info-row:last-child { margin-bottom: 0;}
        .info-row span { color: var(--text-muted); font-weight: 600;}
        .info-row strong { color: var(--primary); font-weight: 800; text-align: right; max-width: 65%;}
        
        .total-price { background: #eff6ff; padding: 15px; border-radius: 12px; text-align: center; margin-top: auto; border: 1px dashed #bfdbfe;}
        .total-price span { display: block; font-size: 12px; color: var(--secondary); text-transform: uppercase; font-weight: 800; letter-spacing: 0.5px;}
        .total-price strong { font-size: 26px; color: var(--primary); font-weight: 900; line-height: 1.2;}

        .r-actions { padding: 20px; background: white; border-top: 1px solid var(--border); display: flex; gap: 10px; flex-wrap: wrap;}
        .btn-action { flex: 1; padding: 14px; border-radius: 12px; font-size: 14.5px; font-weight: 800; text-align: center; cursor: pointer; border: none; transition: 0.3s; display: flex; align-items: center; justify-content: center; gap: 6px; min-width: 45%;}
        .btn-ticket { background: var(--primary); color: white; box-shadow: 0 4px 15px rgba(15, 23, 42, 0.2);}
        .btn-ticket:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(15, 23, 42, 0.4); background: #1e293b;}
        .btn-cancel-bk { background: white; color: #ef4444; border: 1px solid #fca5a5;}
        .btn-cancel-bk:hover { background: #fee2e2; border-color: #ef4444;}

        .empty-state { text-align: center; padding: 80px 20px; background: white; border-radius: 24px; border: 2px dashed #cbd5e1; grid-column: 1/-1; box-shadow: 0 10px 30px rgba(0,0,0,0.05);}
        .empty-state h3 { font-size: 26px; color: var(--primary); margin-bottom: 10px; font-weight: 900;}
        .empty-state p { font-size: 16px; color: var(--text-muted); margin-bottom: 25px;}

        /* E-TICKET MODAL & ΝΕΑ ΚΟΥΜΠΙΑ (Πτυχιακή) */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15,23,42,0.98); backdrop-filter: blur(15px); z-index: 10000; display: flex; flex-direction: column; align-items: center; justify-content: flex-start; opacity: 0; pointer-events: none; transition: 0.3s; overflow-y: auto; padding: 40px 20px; box-sizing: border-box;}
        .modal-overlay.active { opacity: 1; pointer-events: auto;}
        
        .modal-header { width: 100%; max-width: 900px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;}
        .modal-title { color: white; font-size: 24px; font-weight: 900;}
        
        .modal-actions { display: flex; gap: 10px; align-items: center;}
        
        /* ΝΕΟ FEATURE ΠΤΥΧΙΑΚΗΣ: ΚΟΥΜΠΙ "Λήψη Όλων" */
        .btn-download-all { background: linear-gradient(135deg, var(--secondary), #0ea5e9); border: none; color: white; padding: 10px 20px; border-radius: 12px; font-weight: 800; cursor: pointer; transition: 0.2s; box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3);}
        .btn-download-all:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(59, 130, 246, 0.4);}
        
        .btn-close-modal { background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.3); color: white; padding: 10px 20px; border-radius: 12px; font-weight: 800; cursor: pointer; transition: 0.2s;}
        .btn-close-modal:hover { background: #ef4444; border-color: #ef4444;}

        .ticket-tabs { display: flex; gap: 10px; margin-bottom: 25px; width: 100%; max-width: 900px; padding-bottom: 10px; justify-content: center;}
        .t-tab { background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; padding: 12px 20px; border-radius: 14px; font-weight: 800; cursor: pointer; transition: 0.3s; display: flex; align-items: center; gap: 8px; white-space: nowrap;}
        .t-tab.active { background: var(--secondary); border-color: var(--secondary); box-shadow: 0 5px 15px rgba(37,99,235,0.5);}
        
        .t-pane { display: none; width: 100%; max-width: 900px; animation: fadeIn 0.4s ease-out;}
        .t-pane.active { display: block; }
        
        .ticket-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 25px; justify-content: center; }
        .ticket-grid.single-ticket { display: flex; justify-content: center; } 

        /* WALLET PASS STYLING */
        .wallet-pass { background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.5); position: relative; display: flex; flex-direction: column; height: 100%; max-width: 420px; width: 100%; margin: 0 auto;}
        .pass-header { padding: 20px; color: white; display: flex; justify-content: space-between; align-items: center;}
        .pass-icon { font-size: 24px; background: rgba(255,255,255,0.2); width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-radius: 10px;}
        .pass-carrier { font-size: 16px; font-weight: 900; letter-spacing: 1px;}
        
        .pass-body { padding: 0 20px; background: white; flex: 1;}
        .leg { padding: 20px 0; position: relative;}
        .leg-type { font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px dashed #e2e8f0;}
        
        .cities-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;}
        .city { text-align: left; width: 40%;}
        .city.right { text-align: right;}
        .city h2 { margin: 0; font-size: 32px; font-weight: 900; color: var(--primary); letter-spacing: -1px;}
        .city p { margin: 0; font-size: 11px; font-weight: 800; color: var(--text-muted);}
        .plane-arrow { font-size: 20px; color: #cbd5e1;}

        .times-row { display: flex; justify-content: space-between;}
        .time-box { text-align: left;}
        .time-box.right { text-align: right;}
        .time-box span { display: block; font-size: 10px; color: var(--text-muted); font-weight: 800; text-transform: uppercase; margin-bottom: 4px;}
        .time-box strong { font-size: 15px; color: var(--primary); font-weight: 900;}

        .pass-footer { background: #f8fafc; padding: 20px; display: flex; flex-direction: column; align-items: center; text-align: center; border-top: 1px dashed #cbd5e1;}
        .pass-footer-row { display: flex; justify-content: space-between; width: 100%; margin-bottom: 15px; text-align: left; gap: 10px;}
        
        .qr-box { background: white; padding: 10px; border-radius: 12px; border: 1px solid #e2e8f0; display: inline-block;}
        .qr-code { width: 130px; height: 130px; display: block;}
        
        .btn-download-pass { margin-top: 15px; background: #10b981; color: white; padding: 12px; border-radius: 10px; font-weight: 800; font-size: 13px; cursor: pointer; border: none; width: 100%; transition: 0.2s;}
        .btn-download-pass:hover { background: #059669; transform: translateY(-2px);}

        footer { background: rgba(15, 23, 42, 0.95); padding: 40px 20px; text-align: center; margin-top: auto; border-top: 4px solid var(--secondary);}
        .footer-desc { max-width: 600px; margin: 0 auto 20px auto; color: #94a3b8; font-size: 14px; line-height: 1.6;}
        .footer-copy { font-size: 13px; color: white; font-weight: 700;}

        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        
        /* RESPONSIVE DESIGN */
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
            .btn-new-search, .btn-logout { width: 100%; justify-content: center; }

            .cards-grid { grid-template-columns: 1fr; } 
            .ticket-grid { grid-template-columns: 1fr; }
        }

        /* Responsive Fixes - Mobile */
        @media (max-width: 600px) {
            .profile-hero { padding: 80px 20px 100px 20px; }
            .hero-content h1 { font-size: 28px; line-height: 1.3; letter-spacing: -0.5px; margin-bottom: 15px;}
            .hero-content p { font-size: 14.5px; padding: 0 5px; }
            
            .container { margin-top: -40px; padding: 0 15px; }
            .r-card { border-radius: 16px; }
            .r-header h3 { font-size: 20px; }
            
            .modal-overlay { padding: 20px 15px; justify-content: flex-start; overflow-x: hidden; } 
            
            .modal-header { 
                flex-direction: column; 
                align-items: stretch; 
                gap: 12px;
                margin-bottom: 20px; 
                width: 100%; 
            }
            .modal-title { font-size: 22px; text-align: center; }
            
            .modal-actions { flex-direction: column; width: 100%; }
            .btn-download-all, .btn-close-modal { width: 100%; text-align: center; padding: 12px; font-size: 15px; }
            .btn-close-modal { background: rgba(239, 68, 68, 0.15); border-color: rgba(239, 68, 68, 0.4); color: #fca5a5; }
            
            .ticket-tabs { 
                display: flex;
                flex-direction: column; 
                width: 100%;
                margin-bottom: 25px; 
                padding-bottom: 0; 
                gap: 10px;
            }
            .t-tab { width: 100%; justify-content: center; white-space: normal; font-size: 14px; padding: 14px 15px; border-radius: 12px; }
            
            .ticket-grid { grid-template-columns: 1fr; gap: 20px; }
            .wallet-pass { max-width: 340px; margin: 0 auto; border-radius: 16px;}
            .pass-header { padding: 15px; }
            .city h2 { font-size: 26px; }
        }

        /* --- ΛΟΓΑΡΙΑΣΜΟΣ & ΡΥΘΜΙΣΕΙΣ (ACCOUNT SETTINGS) --- */
        .settings-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 25px;
            margin-top: 60px;
        }

        .settings-card {
            background: white;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            border: 1px solid #e2e8f0;
        }

        .settings-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f1f5f9;
        }

        .settings-header .icon-wrapper {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            background: #f0fdf4;
            color: #16a34a;
        }

        .settings-header h3 {
            margin: 0;
            font-size: 20px;
            font-weight: 800;
            color: #1e293b;
            letter-spacing: -0.3px;
        }

        /* SECURITY CARD (ΑΛΛΑΓΗ ΚΩΔΙΚΟΥ) */
        .chpass-form { display: flex; flex-direction: column; gap: 18px; max-width: 420px; }
        .chpass-form label {
            font-size: 13.5px;
            font-weight: 700;
            color: #475569;
            margin-bottom: 8px;
            display: block;
        }
        .chpass-form input[type="password"] {
            width: 100%;
            padding: 13px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            background: #f8fafc;
            color: #1e293b;
            outline: none;
            transition: all 0.3s;
            box-sizing: border-box;
        }
        .chpass-form input[type="password"]:focus { 
            border-color: #16a34a; 
            background: white;
            box-shadow: 0 0 0 4px rgba(22, 163, 74, 0.1);
        }
        .chpass-hint { font-size: 12.5px; color: #64748b; margin-top: 6px; display: block; }
        .chpass-msg {
            padding: 12px 18px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 20px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .chpass-msg.error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .chpass-msg.success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .btn-change-pass {
            background: linear-gradient(135deg, #16a34a, #15803d);
            color: white;
            border: none;
            padding: 14px 28px;
            border-radius: 10px;
            font-family: 'Inter', sans-serif;
            font-size: 14.5px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(22, 163, 74, 0.25);
            width: fit-content;
            margin-top: 5px;
        }
        .btn-change-pass:hover {
            background: linear-gradient(135deg, #15803d, #166534);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(22, 163, 74, 0.35);
        }

        /* DANGER CARD (ΔΙΑΓΡΑΦΗ ΛΟΓΑΡΙΑΣΜΟΥ) */
        .danger-card {
            border: 2px solid #fca5a5;
            background: linear-gradient(135deg, #fef2f2, #fff1f2);
        }
        .danger-card .icon-wrapper {
            background: #fee2e2;
        }
        .danger-card .icon-wrapper svg {
            color: #ef4444;
        }
        .danger-card h3 { color: #991b1b; }
        .danger-card .settings-header { border-bottom-color: #fecaca; }
        .danger-desc {
            font-size: 14.5px;
            color: #7f1d1d;
            line-height: 1.7;
            margin: 0 0 20px 0;
            opacity: 0.9;
        }
        .danger-error {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px 18px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 16px;
            border: 1px solid #fecaca;
        }
        .btn-delete-account {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            border: none;
            padding: 14px 32px;
            border-radius: 12px;
            font-family: 'Inter', sans-serif;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-delete-account:hover {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.45);
        }

        /* --- MODAL ΑΣΦΑΛΟΥΣ ΔΙΑΓΡΑΦΗΣ --- */
        .delete-modal-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }
        .delete-modal-overlay.active { display: flex; }
        .delete-modal {
            background: white;
            border-radius: 20px;
            padding: 35px;
            max-width: 440px;
            width: 90%;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.3);
            animation: modalSlideIn 0.3s ease;
        }
        @keyframes modalSlideIn {
            from { transform: translateY(30px) scale(0.95); opacity: 0; }
            to { transform: translateY(0) scale(1); opacity: 1; }
        }
        .delete-modal h3 {
            margin: 0 0 8px 0;
            font-size: 20px;
            font-weight: 800;
            color: #991b1b;
        }
        .delete-modal p {
            font-size: 14px;
            color: #7f1d1d;
            line-height: 1.6;
            margin: 0 0 20px 0;
        }
        .delete-modal label {
            font-size: 13px;
            font-weight: 700;
            color: #991b1b;
            display: block;
            margin-bottom: 6px;
        }
        .delete-modal input[type="password"] {
            width: 100%;
            padding: 13px 16px;
            border: 2px solid #fecaca;
            border-radius: 10px;
            font-family: 'Inter', sans-serif;
            font-size: 15px;
            outline: none;
            transition: border 0.3s;
            margin-bottom: 20px;
            box-sizing: border-box;
        }
        .delete-modal input[type="password"]:focus { border-color: #ef4444; }
        .delete-modal-actions {
            display: flex;
            gap: 12px;
        }
        .delete-modal-actions .btn-modal-cancel {
            flex: 1;
            padding: 13px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            background: white;
            color: #64748b;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
        }
        .delete-modal-actions .btn-modal-cancel:hover { background: #f1f5f9; }
        .delete-modal-actions .btn-modal-delete {
            flex: 1;
            padding: 13px;
            border: none;
            border-radius: 10px;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
            box-shadow: 0 3px 12px rgba(239, 68, 68, 0.3);
        }
        .delete-modal-actions .btn-modal-delete:hover { background: linear-gradient(135deg, #dc2626, #b91c1c); }
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
            <a href="index.php?lang=<?php echo $lang; ?>" class="btn-new-search">🔍 <?php echo $t['new_search']; ?></a>
            <a href="auth/logout.php?lang=<?php echo $lang; ?>" class="btn-logout"><?php echo $t['logout']; ?></a>
        </nav>
    </header>

    <div class="profile-hero">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1><?php echo $t['hero_hello']; ?> <?php echo strtoupper(htmlspecialchars($_SESSION['fullname'])); ?>!</h1>
            <p><?php echo $t['hero_desc']; ?></p>
        </div>
    </div>

    <div class="container">
        
        <?php if(isset($_GET['success'])): ?>
            <div class="msg-box success-msg">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                <?php echo $t['msg_success']; ?>
            </div>
        <?php endif; ?>

        <?php if(isset($_GET['deleted'])): ?>
            <div class="msg-box deleted-msg">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                <?php echo $t['msg_deleted']; ?>
            </div>
        <?php endif; ?>

        <div class="cards-grid">
            <?php if (count($reservations) > 0): ?>
                <?php foreach ($reservations as $res): 
                    $pnr = $t['pnr_unavailable'];
                    $pass_details = $res['passenger_details'];
                    
                    if (preg_match('/\(PNR\):\s*([A-Z0-9-]+)/i', $pass_details, $matches)) {
                        $pnr = $matches[1];
                    }
                    
                    $method_lower = mb_strtolower($res['transport_method'], 'UTF-8');
                    $is_one_way = (strpos($method_lower, 'χωρίς επιστροφή') !== false);
                    
                    $has_car = false;
                    $car_days = "";
                    $car_type = "";
                    $car_license = "";
                    
                    if (preg_match('/Ενοικίαση Οχήματος\s*\((\d+)\s*Ημέρες\)/ui', $res['transport_method'], $carMatch)) {
                        $has_car = true;
                        $car_days = $carMatch[1];
                    }
                    
                    if ($has_car && preg_match('/\[EXTRAS:.*?(ΙΧ:.*?)\s*(?:\||\])/ui', $pass_details, $exMatch)) {
                        $car_info_raw = $exMatch[1]; 
                        if (preg_match('/ΙΧ:\s*(.*?)\s*\(Δίπλωμα:\s*(.*?)\)/ui', $car_info_raw, $typeMatch)) {
                            $car_type = trim($typeMatch[1]);
                            $car_license = trim($typeMatch[2]);
                        } else {
                            $car_type = $car_info_raw;
                        }
                    }

                    $car_voucher = "CAR-" . strtoupper(substr(md5($res['id'] . 'rental'), 0, 6));
                    $has_ticket = (strpos($method_lower, 'δικό μου όχημα') === false && strpos($method_lower, 'χωρίς μεταφορικά') === false && $pnr !== $t['pnr_unavailable']);
                    $hotel_ref = "HTL-" . date('Y', strtotime($res['check_in'])) . "-" . strtoupper(substr(md5($res['id']), 0, 5));
                    $js_details = htmlspecialchars(json_encode($pass_details), ENT_QUOTES, 'UTF-8');
                ?>
                    <div class="r-card">
                        <div class="r-header">
                            <div class="r-header-top">
                                <h3><?php echo htmlspecialchars($res['destination_name']); ?></h3>
                                <div class="badge-status"><?php echo $t['badge_confirmed']; ?></div>
                            </div>
                            <p>📅 <?php echo htmlspecialchars($res['check_in']); ?> — <?php echo htmlspecialchars($res['check_out']); ?></p>
                        </div>
                        
                        <div class="r-body">
                            <div class="info-section">
                                <div class="info-title">🏨 <?php echo $t['hotel_section']; ?></div>
                                <div class="info-row">
                                    <span><?php echo $t['hotel_label']; ?></span>
                                    <strong><?php echo htmlspecialchars($res['hotel_name']); ?></strong>
                                </div>
                                <div class="info-row">
                                    <span>Voucher:</span>
                                    <strong style="color: var(--secondary);"><?php echo $hotel_ref; ?></strong>
                                </div>
                            </div>

                            <div class="info-section">
                                <div class="info-title">🚢 <?php echo $t['transport_section']; ?></div>
                                <div class="info-row">
                                    <span><?php echo $t['transport_label']; ?></span>
                                    <strong><?php echo htmlspecialchars(explode('|', $res['transport_method'])[0]); ?></strong>
                                </div>
                                <?php if ($has_ticket): ?>
                                <div class="info-row">
                                    <span><?php echo $t['pnr_label']; ?></span>
                                    <strong style="color: #ef4444;"><?php echo $pnr; ?></strong>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($has_car): ?>
                            <div class="info-section" style="border: 2px dashed #f59e0b; background: #fffbeb;">
                                <div class="info-title" style="color: #d97706;">🚗 <?php echo $t['car_section']; ?></div>
                                <div class="info-row">
                                    <span><?php echo $t['car_type']; ?></span>
                                    <strong style="color: #d97706;"><?php echo htmlspecialchars($car_type); ?></strong>
                                </div>
                                <div class="info-row">
                                    <span><?php echo $t['car_days']; ?></span>
                                    <strong><?php echo htmlspecialchars($car_days); ?></strong>
                                </div>
                                <div class="info-row">
                                    <span><?php echo $t['car_license']; ?></span>
                                    <strong><?php echo htmlspecialchars($car_license); ?></strong>
                                </div>
                                <div class="info-row" style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #fde68a;">
                                    <span><?php echo $t['car_voucher']; ?></span>
                                    <strong style="font-size: 16px; color: #b45309; background: #fef3c7; padding: 4px 8px; border-radius: 6px;"><?php echo $car_voucher; ?></strong>
                                </div>
                            </div>
                            <?php endif; ?>

                            <div class="total-price">
                                <span><?php echo $t['total_label']; ?> (<?php echo htmlspecialchars($res['payment_method']); ?>)</span>
                                <strong><?php echo number_format($res['total_price'], 2); ?>€</strong>
                            </div>
                        </div>
                        
                        <div class="r-actions">
                            <?php if ($has_ticket): ?>
                                <button class="btn-action btn-ticket" 
                                    onclick='openTicketManager("<?php echo addslashes($res['destination_name']); ?>", "<?php echo addslashes($res['check_in']); ?>", "<?php echo addslashes($res['check_out']); ?>", "<?php echo addslashes($pnr); ?>", "<?php echo addslashes($res['transport_method']); ?>", "<?php echo addslashes($_SESSION['fullname']); ?>", <?php echo $js_details; ?>, <?php echo $is_one_way ? 'true' : 'false'; ?>)'>
                                    📱 <?php echo $t['btn_tickets']; ?>
                                </button>
                            <?php endif; ?>
                            
                            <form method="POST" style="flex:1;" onsubmit="return confirm('<?php echo addslashes($t['cancel_confirm']); ?>');">
                                <input type="hidden" name="cancel_booking_id" value="<?php echo $res['id']; ?>">
                                <button type="submit" class="btn-action btn-cancel-bk" style="width:100%;">❌ <?php echo $t['btn_cancel']; ?></button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <h3><?php echo $t['empty_title']; ?></h3>
                    <p><?php echo $t['empty_desc']; ?></p>
                    <a href="index.php?lang=<?php echo $lang; ?>" class="btn-new-search" style="display:inline-flex; width: fit-content; margin: 0 auto;">🔍 <?php echo $t['empty_btn']; ?></a>
                </div>
            <?php endif; ?>
        </div>

        <?php // --- ΛΟΓΑΡΙΑΣΜΟΣ & ΡΥΘΜΙΣΕΙΣ --- ?>
        <div class="settings-grid">
            
            <?php // --- 1. ΑΛΛΑΓΗ ΚΩΔΙΚΟΥ ΠΡΟΣΒΑΣΗΣ --- ?>
            <div class="settings-card security-card">
                <div class="settings-header">
                    <div class="icon-wrapper">🔒</div>
                    <h3><?php echo $t['chpass_title']; ?></h3>
                </div>
                
                <?php if (isset($chpass_error)): ?>
                    <div class="chpass-msg error">
                        ❌ <?php 
                            if ($chpass_error === 'wrong') echo $t['chpass_wrong'];
                            elseif ($chpass_error === 'short') echo $t['chpass_short'];
                            elseif ($chpass_error === 'mismatch') echo $t['chpass_mismatch'];
                        ?>
                    </div>
                <?php endif; ?>
                <?php if (isset($chpass_success) && $chpass_success): ?>
                    <div class="chpass-msg success">✅ <?php echo $t['chpass_success']; ?></div>
                <?php endif; ?>
                
                <form method="POST" class="chpass-form" autocomplete="off">
                    <div>
                        <label><?php echo $t['chpass_current']; ?></label>
                        <input type="password" name="current_password" required autocomplete="current-password">
                    </div>
                    <div>
                        <label><?php echo $t['chpass_new']; ?></label>
                        <input type="password" name="new_password" required minlength="6" autocomplete="new-password">
                        <div class="chpass-hint"><?php echo $t['chpass_min']; ?></div>
                    </div>
                    <div>
                        <label><?php echo $t['chpass_confirm']; ?></label>
                        <input type="password" name="confirm_password" required minlength="6" autocomplete="new-password">
                    </div>
                    <input type="hidden" name="change_password" value="1">
                    <button type="submit" class="btn-change-pass">🔐 <?php echo $t['chpass_btn']; ?></button>
                </form>
            </div>

            <?php // --- 2. ΖΩΝΗ ΚΙΝΔΥΝΟΥ: ΔΙΑΓΡΑΦΗ ΛΟΓΑΡΙΑΣΜΟΥ --- ?>
            <div class="settings-card danger-card">
                <div class="settings-header">
                    <div class="icon-wrapper">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                    </div>
                    <h3><?php echo $t['delete_title']; ?></h3>
                </div>
                
                <p class="danger-desc"><?php echo $t['delete_desc']; ?></p>
                
                <?php if (isset($delete_error) && $delete_error): ?>
                    <div class="danger-error">
                        ❌ <?php echo $t['delete_wrong_pass']; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" id="deleteAccountForm">
                    <input type="hidden" name="delete_account" value="1">
                    <input type="hidden" name="delete_password" id="deletePasswordField" value="">
                    <button type="button" class="btn-delete-account" onclick="openDeleteModal()">
                        🗑️ <?php echo $t['delete_btn']; ?>
                    </button>
                </form>
            </div>

        </div>
    </div>

    <footer>
        <div class="footer-desc"><?php echo $t['footer_desc']; ?></div>
        <div class="footer-copy"><?php echo $t['copyright']; ?></div>
    </footer>

    <div class="modal-overlay" id="ticketOverlay">
        <div class="modal-header">
            <div class="modal-title"><?php echo $t['modal_title']; ?></div>
            <div class="modal-actions">
                <button class="btn-download-all" id="btnDownloadAll" onclick="downloadAllPasses()">📥 <?php echo $t['btn_download_all']; ?></button>
                <button class="btn-close-modal" onclick="closeTicket()">✕ <?php echo $t['btn_close']; ?></button>
            </div>
        </div>
        <div class="ticket-tabs" id="ticket-tabs-container"></div>
        <div id="ticket-panes-container" style="width: 100%; max-width: 900px;"></div>
    </div>

    <script>
        function toggleMenu() {
            document.getElementById('nav-menu').classList.toggle('active');
        }

        const overlay = document.getElementById('ticketOverlay');

        // Translations object passed from PHP
        const T = <?php echo json_encode([
            'airport' => $t['js_airport'],
            'bus_station' => $t['js_bus_station'],
            'port_piraeus' => $t['js_port_piraeus'],
            'port_rafina' => $t['js_port_rafina'],
            'port_volos' => $t['js_port_volos'],
            'port_igoumenitsa' => $t['js_port_igoumenitsa'],
            'port_kyllini' => $t['js_port_kyllini'],
            'no_seat' => $t['js_no_seat'],
            'vehicle' => $t['js_vehicle'],
            'passenger' => $t['js_passenger'],
            'seat_type' => $t['js_seat_type'],
            'date' => $t['js_date'],
            'time' => $t['js_time'],
            'departure' => $t['js_departure'],
            'return' => $t['js_return'],
            'download_ticket' => $t['js_download_ticket'],
            'downloading' => $t['js_downloading'],
            'saved' => $t['js_saved'],
            'download_error' => $t['js_download_error'],
            'downloading_all' => $t['js_downloading_all'],
            'all_done' => $t['js_all_done'],
            'private_car' => $t['js_private_car']
        ], JSON_UNESCAPED_UNICODE); ?>;

        function generateTime(seedStr) {
            let hash = 0;
            for (let i = 0; i < seedStr.length; i++) hash = seedStr.charCodeAt(i) + ((hash << 5) - hash);
            let hour = Math.abs(hash % 12) + 8; 
            let min = (Math.abs(hash % 4) * 15).toString().padStart(2, '0');
            return hour.toString().padStart(2, '0') + ':' + min;
        }

        // Άνοιγμα Modal και Δημιουργία των εισιτηρίων
        function openTicketManager(destName, inDate, outDate, pnr, method, buyerName, rawDetails, isOneWay) {
            let tIcon = '✈️'; let depCode = 'ATH'; let depName = T.airport; let colorTheme = '#0284c7'; 
            let transportType = 'flight'; 

            let actualCarrier = method.split('|')[0].replace('Αναχώρηση:', '').trim();
            let carrierUpper = actualCarrier.toUpperCase();

            if (carrierUpper.includes('ΚΤΕΛ') || carrierUpper.includes('BUS') || carrierUpper.includes('ΛΕΩΦΟΡΕΙΟ')) {
                transportType = 'bus'; tIcon = '🚌'; depCode = 'KIF'; depName = T.bus_station; colorTheme = '#059669'; 
            } else if (carrierUpper.includes('AEGEAN') || carrierUpper.includes('SKY EXPRESS') || carrierUpper.includes('RYANAIR') || carrierUpper.includes('AIR') || carrierUpper.includes('ΠΤΗΣΗ') || carrierUpper.includes('ΑΕΡΟΠΛΑΝΟ')) {
                transportType = 'flight'; tIcon = '✈️'; depCode = 'ATH'; depName = T.airport; colorTheme = '#0369a1';
            } else {
                transportType = 'ferry'; tIcon = '⛴️'; depCode = 'PIR'; depName = T.port_piraeus; colorTheme = '#0f172a';
                if (carrierUpper.includes('ΡΑΦΗΝΑ') || carrierUpper.includes('FAST FERRIES')) { depCode = 'RAF'; depName = T.port_rafina; }
                if (carrierUpper.includes('ΒΟΛΟΥ')) { depCode = 'VOL'; depName = T.port_volos; }
                if (carrierUpper.includes('ΗΓΟ')) { depCode = 'IGO'; depName = T.port_igoumenitsa; }
                if (carrierUpper.includes('ΚΥΛΛ')) { depCode = 'KYL'; depName = T.port_kyllini; }
            }

            const destCode = destName.substring(0, 3).toUpperCase();
            let outTime = generateTime(pnr + "outbound");
            let retTime = generateTime(pnr + "return");
            let entities = [];
            let totalBags = 0;

            if(rawDetails) {
                let tOutMatch = rawDetails.match(/\[TIME_OUT:\s*([^\]]+)\]/);
                if (tOutMatch) outTime = tOutMatch[1].trim();
                
                let tRetMatch = rawDetails.match(/\[TIME_RET:\s*([^\]]+)\]/);
                if (tRetMatch && tRetMatch[1].trim() !== 'none') retTime = tRetMatch[1].trim();

                let lines = rawDetails.split('\n');
                lines.forEach(line => {
                    let l = line.trim();
                    let bagMatch = l.match(/(\d+)\s*[xX]?\s*Βαλίτσ/i) || l.match(/Extras?.*?:?\s*(\d+)\s*Βαλίτσ/i);
                    if (bagMatch) totalBags += parseInt(bagMatch[1]);
                });

                lines.forEach(line => {
                    let l = line.trim();
                    if (!l || l.includes('PNR') || l.includes('E-TICKET') || l.match(/^[-_]+$/)) return;
                    if (l.toUpperCase().includes('EXTRAS') || l.toUpperCase().includes('ΒΑΛΙΤΣ')) return;
                    if (l.toUpperCase().includes('ΕΝΟΙΚΙΑΣΗ') || l.toUpperCase().includes('RENT A CAR')) return;
                    if (l.includes('[TIME_OUT') || l.includes('[TIME_RET')) return;

                    if (l.match(/^(Όχημα|ΙΧ|Μοτο)/i) || l.includes('Πινακίδα:')) {
                        let name = T.private_car;
                        let seat = "GARAGE";
                        let plateMatch = l.match(/([A-Z]{3}-?\d{4})/i) || l.match(/Πινακίδα:\s*([A-Z0-9-]+)/i);
                        if (plateMatch) name = "ΙΧ (" + plateMatch[1].trim().toUpperCase() + ")";
                        if (l.toLowerCase().includes('μικρό')) seat = "GARAGE (ΜΙΚΡΟ)";
                        else if (l.toLowerCase().includes('suv') || l.toLowerCase().includes('μεγάλο')) seat = "GARAGE (SUV)";
                        else if (l.toLowerCase().includes('μοτο')) seat = "GARAGE (MOTO)";
                        entities.push({ type: 'vehicle', name: name, seat: seat });
                    } else {
                        if (!l.match(/[a-zA-Zα-ωΑ-Ω]/)) return; 
                        let cleanLine = l.replace(/^Επιβάτης\s*\d*:\s*/i, '');
                        let name = cleanLine;
                        let seat = T.no_seat;
                        
                        if (cleanLine.includes(' - ')) {
                            let segments = cleanLine.split(' - ');
                            name = segments[0].trim();
                            seat = segments.slice(1).join(' - ').trim();
                        } else if (cleanLine.includes('(')) {
                            let pIndex = cleanLine.indexOf('(');
                            name = cleanLine.substring(0, pIndex).trim();
                            seat = cleanLine.substring(pIndex).replace(/[()]/g, '').trim();
                        }
                        seat = seat.replace(/Θέση:?\s*/i, '').trim();
                        if (!seat || seat === '') seat = T.no_seat;
                        if (name.length > 2) { entities.push({ type: 'passenger', name: name, seat: seat }); }
                    }
                });
            }
            
            if (totalBags > 0) {
                let passengers = entities.filter(e => e.type === 'passenger');
                if (passengers.length > 0) {
                    let bagsPerPax = Math.floor(totalBags / passengers.length);
                    let remainder = totalBags % passengers.length;
                    passengers.forEach((p, idx) => {
                        let bagsForThisPax = bagsPerPax + (idx < remainder ? 1 : 0);
                        if (bagsForThisPax > 0) { p.seat += ` + ${bagsForThisPax} 🧳`; }
                    });
                }
            }

            if(entities.length === 0) {
                entities.push({ type: 'passenger', name: buyerName.toUpperCase(), seat: T.no_seat });
            }

            let tabsHTML = '';
            let panesHTML = '';

            entities.forEach((entity, idx) => {
                let activeCls = (idx === 0) ? 'active' : '';
                let tabIcon = (entity.type === 'vehicle') ? '🚗' : '👤';
                tabsHTML += `<button class="t-tab ${activeCls}" onclick="switchTab(${idx})">${tabIcon} ${entity.name}</button>`;

                let passIcon = (entity.type === 'vehicle') ? '🚗' : tIcon;

                const createPass = (legType, cFromCode, cFromName, cToCode, cToName, legDate, legTime, arrowTransform) => {
                    let legColor = legType === T.departure ? 'var(--secondary)' : '#10b981';
                    let divId = `pass-${legType === T.departure ? 'out' : 'ret'}-${idx}`;
                    let qrData = `SMART TRAVEL PLANNER\nName: ${entity.name}\nRoute: ${cFromCode}-${cToCode}\nDate: ${legDate} ${legTime}\nSeat: ${entity.seat}\nPNR: ${pnr}`;
                    let qrUrl = `https://quickchart.io/qr?size=150&text=${encodeURIComponent(qrData)}`;

                    return `
                    <div class="wallet-pass" id="${divId}">
                        <div class="pass-header" style="background: ${colorTheme};">
                            <div class="pass-header-left">
                                <div class="pass-icon">${passIcon}</div>
                                <div class="pass-carrier">${actualCarrier.toUpperCase()}</div>
                            </div>
                            <div style="font-size:10px; font-weight:800; opacity:0.8;">SMART TRAVEL PLANNER</div>
                        </div>
                        <div class="pass-body">
                            <div class="leg">
                                <div class="leg-type" style="color: ${legColor}; border-bottom:none;">➔ ${legType} TICKET</div>
                                <div class="cities-row">
                                    <div class="city"><h2>${cFromCode}</h2><p>${cFromName}</p></div>
                                    <div class="plane-arrow" style="${arrowTransform}">${tIcon}</div>
                                    <div class="city right"><h2>${cToCode}</h2><p>${cToName}</p></div>
                                </div>
                                <div class="times-row">
                                    <div class="time-box"><span>${T.date}</span><strong>${legDate}</strong></div>
                                    <div class="time-box right"><span>${T.time}</span><strong>${legTime}</strong></div>
                                </div>
                            </div>
                        </div>
                        <div class="pass-footer">
                            <div class="pass-footer-row">
                                <div class="time-box" style="flex:1;"><span>${entity.type === 'vehicle' ? T.vehicle : T.passenger}</span><strong style="font-size:13px; display:block; line-height:1.3; word-break:break-word;">${entity.name.toUpperCase()}</strong></div>
                                <div class="time-box" style="flex:1; text-align:center;"><span>${T.seat_type}</span><strong style="font-size:13px; color:var(--primary); display:block; line-height:1.3; word-break:break-word;">${entity.seat.toUpperCase()}</strong></div>
                                <div class="time-box right" style="flex:1;"><span>PNR</span><strong style="font-size:15px; color: ${colorTheme};">${pnr}</strong></div>
                            </div>
                            <div class="qr-box"><img src="${qrUrl}" alt="QR" class="qr-code" crossorigin="anonymous"></div>
                            <button class="btn-download-pass" onclick="downloadSinglePass('${divId}', '${pnr}', '${legType}')">⬇️ ${T.download_ticket}</button>
                        </div>
                    </div>`;
                };

                let gridClass = isOneWay ? 'ticket-grid single-ticket' : 'ticket-grid';
                let passesHtml = createPass(T.departure, depCode, depName, destCode, destName.toUpperCase(), inDate, outTime, '');
                if (!isOneWay) { passesHtml += createPass(T.return, destCode, destName.toUpperCase(), depCode, depName, outDate, retTime, 'transform: scaleX(-1);'); }

                panesHTML += `<div class="t-pane ${activeCls}" id="pane-${idx}"><div class="${gridClass}">${passesHtml}</div></div>`;
            });

            document.getElementById('ticket-tabs-container').innerHTML = tabsHTML;
            document.getElementById('ticket-panes-container').innerHTML = panesHTML;
            overlay.classList.add('active');
        }

        // Εναλλαγή μεταξύ των επιβατών/οχημάτων
        function switchTab(index) {
            document.querySelectorAll('.t-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.t-pane').forEach(p => p.classList.remove('active'));
            document.querySelectorAll('.t-tab')[index].classList.add('active');
            document.getElementById(`pane-${index}`).classList.add('active');
        }

        function closeTicket() { overlay.classList.remove('active'); }

        // Λήψη ΜΕΜΟΝΩΜΕΝΟΥ εισιτηρίου
        function downloadSinglePass(divId, pnr, legType) {
            const ticketElement = document.getElementById(divId);
            const btn = ticketElement.querySelector('.btn-download-pass');
            
            let originalText = btn.innerText;
            btn.innerText = '⏳ ' + T.downloading;
            btn.style.pointerEvents = 'none';
            btn.style.display = 'none';

            html2canvas(ticketElement, { scale: 3, useCORS: true, backgroundColor: null, borderRadius: 20 }).then(canvas => {
                const link = document.createElement('a');
                link.download = `STP-Ticket-${pnr}-${legType}.jpg`;
                link.href = canvas.toDataURL('image/jpeg', 1.0);
                document.body.appendChild(link); link.click(); document.body.removeChild(link);

                btn.style.display = 'block';
                btn.innerText = '✅ ' + T.saved;
                setTimeout(() => { btn.innerText = originalText; btn.style.pointerEvents = 'auto'; }, 2000);
            }).catch(err => {
                btn.style.display = 'block';
                alert(T.download_error);
                btn.innerText = originalText;
                btn.style.pointerEvents = 'auto';
            });
        }

        // 🚀 ΝΕΟ FEATURE: ΑΣΥΓΧΡΟΝΗ ΛΗΨΗ ΟΛΩΝ ΤΩΝ ΕΙΣΙΤΗΡΙΩΝ (ASYNC/AWAIT) 🚀
        async function downloadAllPasses() {
            const downloadAllBtn = document.getElementById('btnDownloadAll');
            let originalText = downloadAllBtn.innerText;
            
            // Ενημέρωση UI για να ξέρει ο χρήστης ότι ξεκίνησε η διαδικασία
            downloadAllBtn.innerText = '⏳ ' + T.downloading_all;
            downloadAllBtn.style.pointerEvents = 'none';

            const tabs = document.querySelectorAll('.t-tab');
            
            // Loop μέσα από κάθε επιβάτη/όχημα
            for (let i = 0; i < tabs.length; i++) {
                
                // Ενεργοποίηση της συγκεκριμένης καρτέλας
                switchTab(i);
                
                // Αναμονή 300ms για να προλάβει ο Browser να κάνει render την καρτέλα
                await new Promise(resolve => setTimeout(resolve, 300));

                const activePane = document.getElementById(`pane-${i}`);
                const passes = activePane.querySelectorAll('.wallet-pass'); // Αναχώρηση & Επιστροφή

                // Loop μέσα σε κάθε εισιτήριο (Πήγαινε & Έλα)
                for (let pass of passes) {
                    
                    // Κρύβουμε το μικρό κουμπί
                    const singleBtn = pass.querySelector('.btn-download-pass');
                    if(singleBtn) singleBtn.style.display = 'none';

                    try {
                        const canvas = await html2canvas(pass, { scale: 3, useCORS: true, backgroundColor: null, borderRadius: 20 });
                        
                        const link = document.createElement('a');
                        link.download = `STP-${pass.id}.jpg`; 
                        link.href = canvas.toDataURL('image/jpeg', 1.0);
                        
                        document.body.appendChild(link); 
                        link.click(); 
                        document.body.removeChild(link);
                    } catch (err) {
                        console.error("Σφάλμα κατά τη μαζική λήψη:", err);
                    }

                    // Επαναφορά του μικρού κουμπιού
                    if(singleBtn) singleBtn.style.display = 'block';
                    
                    // Αναμονή 300ms πριν την επόμενη λήψη
                    await new Promise(resolve => setTimeout(resolve, 300));
                }
            }

            // Ολοκλήρωση διαδικασίας και επιστροφή στην αρχική καρτέλα
            downloadAllBtn.innerText = '✅ ' + T.all_done;
            setTimeout(() => { 
                downloadAllBtn.innerText = originalText; 
                downloadAllBtn.style.pointerEvents = 'auto'; 
                switchTab(0); // Γυρνάμε τον χρήστη πίσω στον 1ο επιβάτη
            }, 2500);
        }

        // --- ΑΣΦΑΛΗΣ ΔΙΑΓΡΑΦΗ ΛΟΓΑΡΙΑΣΜΟΥ ΜΕΣΩ MODAL ---
        function openDeleteModal() {
            const confirmMsg = <?php echo json_encode($t['delete_confirm']); ?>;
            // Βήμα 1: Confirm dialog ως πρώτο φίλτρο
            if (!confirm(confirmMsg)) return;
            // Βήμα 2: Άνοιγμα ασφαλούς modal με κρυφό πεδίο κωδικού
            document.getElementById('deleteModalOverlay').classList.add('active');
            document.getElementById('deleteModalPassword').value = '';
            document.getElementById('deleteModalPassword').focus();
        }

        function closeDeleteModal() {
            document.getElementById('deleteModalOverlay').classList.remove('active');
        }

        function submitDeleteAccount() {
            const pass = document.getElementById('deleteModalPassword').value;
            if (!pass || pass.trim() === '') return;
            document.getElementById('deletePasswordField').value = pass;
            document.getElementById('deleteAccountForm').submit();
        }

        // Κλείσιμο modal με Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeDeleteModal();
        });
    </script>

    <?php // --- MODAL ΑΣΦΑΛΟΥΣ ΔΙΑΓΡΑΦΗΣ (type=password για κρυφό κωδικό) --- ?>
    <div class="delete-modal-overlay" id="deleteModalOverlay">
        <div class="delete-modal">
            <h3>⚠️ <?php echo $t['delete_btn']; ?></h3>
            <p><?php echo $t['delete_desc']; ?></p>
            <label><?php echo $t['delete_password_label']; ?></label>
            <input type="password" id="deleteModalPassword" autocomplete="current-password" placeholder="••••••••">
            <div class="delete-modal-actions">
                <button type="button" class="btn-modal-cancel" onclick="closeDeleteModal()"><?php echo $t['delete_modal_cancel']; ?></button>
                <button type="button" class="btn-modal-delete" onclick="submitDeleteAccount()">🗑️ <?php echo $t['delete_modal_btn']; ?></button>
            </div>
        </div>
    </div>
</body>
</html>