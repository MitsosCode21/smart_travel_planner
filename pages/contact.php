<?php
session_start(); 
$lang = isset($_GET['lang']) ? $_GET['lang'] : 'gr';

$translations = [
    'gr' => [
        'home' => 'Αρχική',
        'how_it_works' => 'Πώς λειτουργεί;',
        'login' => 'Σύνδεση',
        'logout' => 'Αποσύνδεση',
        'profile' => 'Το Προφίλ μου',
        'subtitle' => 'Ο Προσωπικός σας Ταξιδιωτικός Σύμβουλος',
        'desc' => 'Μην χάνετε ώρες ψάχνοντας. Εισάγετε το budget, τις ημέρες και το ιδανικό τοπίο, και εμείς οργανώνουμε το τέλειο ταξίδι για εσάς.',
        'f_company' => 'ΕΤΑΙΡΕΙΑ',
        'f_about' => 'Σχετικά με εμάς',
        'f_support' => 'Υποστήριξη',
        'f_contact' => 'Επικοινωνία',
        'f_legal' => 'ΝΟΜΙΚΑ',
        'f_terms' => 'Όροι Χρήσης',
        'f_privacy' => 'Πολιτική Απορρήτου',
        'f_cookies' => 'Πολιτική Cookies',
        'copyright' => '© 2026 Smart Travel Planner | Σχεδιασμένο για μοναδικές εμπειρίες',
        
        // Σελίδα Επικοινωνίας
        'cont_h1' => 'Επικοινωνήστε μαζί μας',
        'cont_sub' => 'Είμαστε εδώ για να σας βοηθήσουμε να οργανώσετε το τέλειο ταξίδι.',
        'info_h' => 'Στοιχεία Επικοινωνίας',
        'info_hq' => '📍 Κεντρικά Γραφεία',
        'info_hq_text' => 'Λεωφόρος Καινοτομίας 15<br>Αθήνα, Τ.Κ. 10431, Ελλάδα',
        'info_tel' => '📞 Τηλεφωνική Υποστήριξη',
        'info_tel_text' => '+30 210 1234567<br><small>(Δευτέρα - Παρασκευή, 09:00 - 17:00)</small>',
        'info_email' => '✉️ Email & Πληροφορίες',
        'info_email_text' => 'Γενικές απορίες: info@smarttravelplanner.gr<br>Υποστήριξη: support@stp.gr',
        'form_h' => 'Στείλτε μας μήνυμα',
        'f_name' => 'Ονοματεπωνυμο',
        'f_email' => 'EMAIL',
        'f_msg' => 'ΤΟ ΜΗΝΥΜΑ ΣΑΣ',
        'btn_submit' => '✉️ ΑΠΟΣΤΟΛΗ ΜΗΝΥΜΑΤΟΣ',
        
        // Μηνύματα φόρμας
        'msg_success' => '✅ Το μήνυμά σας στάλθηκε με επιτυχία! Θα επικοινωνήσουμε μαζί σας σύντομα.',
        'msg_err_empty' => '⚠️ Παρακαλώ συμπληρώστε όλα τα πεδία της φόρμας.',
        'msg_err_db' => '❌ Παρουσιάστηκε σφάλμα κατά την αποστολή.'
    ],
    'en' => [
        'home' => 'Home',
        'how_it_works' => 'How it works',
        'login' => 'Login',
        'logout' => 'Logout',
        'profile' => 'My Profile',
        'subtitle' => 'Your Personal Travel Advisor',
        'desc' => 'Stop wasting hours searching. Enter your budget, available days, and preferred landscape, and let us organize the perfect trip.',
        'f_company' => 'COMPANY',
        'f_about' => 'About Us',
        'f_support' => 'Support',
        'f_contact' => 'Contact',
        'f_legal' => 'LEGAL',
        'f_terms' => 'Terms of Use',
        'f_privacy' => 'Privacy Policy',
        'f_cookies' => 'Cookie Policy',
        'copyright' => '© 2026 Smart Travel Planner | Designed for unique experiences',
        
        // Contact Page
        'cont_h1' => 'Contact Us',
        'cont_sub' => 'We are here to help you organize the perfect trip.',
        'info_h' => 'Contact Information',
        'info_hq' => '📍 Headquarters',
        'info_hq_text' => '15 Innovation Avenue<br>Athens, 10431, Greece',
        'info_tel' => '📞 Phone Support',
        'info_tel_text' => '+30 210 1234567<br><small>(Monday - Friday, 09:00 - 17:00)</small>',
        'info_email' => '✉️ Email & Information',
        'info_email_text' => 'General inquiries: info@smarttravelplanner.gr<br>Support: support@stp.gr',
        'form_h' => 'Send us a message',
        'f_name' => 'FULL NAME',
        'f_email' => 'EMAIL',
        'f_msg' => 'YOUR MESSAGE',
        'btn_submit' => '✉️ SEND MESSAGE',
        
        // Form messages
        'msg_success' => '✅ Your message was sent successfully! We will contact you soon.',
        'msg_err_empty' => '⚠️ Please fill in all the form fields.',
        'msg_err_db' => '❌ An error occurred while sending.'
    ]
];
$t = $translations[$lang];

// --- ΛΟΓΙΚΗ ΑΠΟΘΗΚΕΥΣΗΣ ΜΗΝΥΜΑΤΟΣ ΣΤΗ ΒΑΣΗ ---
$success_msg = "";
$error_msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_contact'])) {
    
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    if (!empty($full_name) && !empty($email) && !empty($message)) {
        
        $host = 'localhost'; 
        $db = 'smart_travel_planner'; 
        $user = 'root'; 
        $pass = ''; 

        try {
            $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $pdo->prepare("INSERT INTO contact_messages (full_name, email, message) VALUES (:name, :email, :msg)");
            $stmt->execute([
                'name' => $full_name,
                'email' => $email,
                'msg' => $message
            ]);

            $success_msg = $t['msg_success'];
        } catch(PDOException $e) {
            $error_msg = $t['msg_err_db'] . " (" . $e->getMessage() . ")";
        }
    } else {
        $error_msg = $t['msg_err_empty'];
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $t['cont_h1']; ?> | Smart Travel Planner</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root { 
            --primary: #0f172a; 
            --secondary: #3b82f6; 
            --accent: #0ea5e9;
            --text-dark: #1e293b; 
            --text-muted: #64748b; 
            --bg: #f8fafc;
        }
        * { box-sizing: border-box; }
        
        body { 
            margin: 0; 
            font-family: 'Inter', sans-serif; 
            background: linear-gradient(rgba(15, 23, 42, 0.8), rgba(15, 23, 42, 0.95)), url('https://images.unsplash.com/photo-1436491865332-7a61a109cc05?q=80&w=1920&auto=format&fit=crop') no-repeat center center fixed; 
            background-size: cover; 
            color: white; 
            display: flex; 
            flex-direction: column; 
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        /* ---------------------------------------------------- */
        /* FULL-WIDTH STICKY NAV BAR (APPLE STYLE)              */
        /* ---------------------------------------------------- */
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
        
        .brand { display: flex; align-items: center; gap: 15px; cursor: pointer; text-decoration: none; transition: 0.3s; }
        .brand:hover { transform: scale(1.02); }
        .brand h2 { margin: 0; font-size: 24px; font-weight: 900; letter-spacing: -0.5px; background: linear-gradient(to right, #ffffff, #bae6fd); -webkit-background-clip: text; -webkit-text-fill-color: transparent;}
        .brand span { font-size: 11px; font-weight: 600; color: #cbd5e0; letter-spacing: 0.5px; text-transform: uppercase;}
        
        .top-nav { display: flex; align-items: center; gap: 25px; }
        
        .nav-link { 
            color: rgba(255,255,255,0.85); text-decoration: none; font-size: 14.5px; font-weight: 600; 
            position: relative; padding-bottom: 4px; transition: color 0.3s ease; 
        }
        .nav-link::after {
            content: ''; position: absolute; width: 0; height: 2px; bottom: 0; left: 0;
            background-color: var(--accent); transition: width 0.3s ease; border-radius: 2px;
        }
        .nav-link:hover { color: #ffffff; }
        .nav-link:hover::after { width: 100%; }
        
        .login-btn { background: rgba(255, 255, 255, 0.1); padding: 10px 24px; border-radius: 20px; border: 1px solid rgba(255, 255, 255, 0.2); transition: 0.3s; color: white; text-decoration: none; font-weight: 700; font-size: 13.5px;}
        .login-btn:hover { background: rgba(255, 255, 255, 0.2); box-shadow: 0 5px 15px rgba(0,0,0,0.2); transform: translateY(-2px);}
        
        .profile-btn { background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.3); padding: 10px 22px; border-radius: 20px; text-decoration: none; color: white; font-weight: 700; font-size: 13.5px; transition: 0.3s; display: flex; align-items: center; gap: 6px;}
        .profile-btn:hover { background: rgba(255, 255, 255, 0.2); transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.2);}
        
        .logout-btn { background: rgba(239, 68, 68, 0.8); padding: 10px 18px; border-radius: 20px; text-decoration: none; color: white; font-weight: bold; font-size: 13.5px; transition: 0.3s; box-shadow: 0 4px 10px rgba(231, 76, 60, 0.3);}
        .logout-btn:hover { background: #dc2626; transform: translateY(-2px);}
        
        .lang-switch { display: flex; gap: 8px; font-size: 13px; font-weight: 800; border-left: 1px solid rgba(255,255,255,0.15); padding-left: 20px; }
        .lang-switch a { color: rgba(255,255,255,0.5); text-decoration: none; transition: 0.3s; padding: 5px 10px; border-radius: 8px;}
        .lang-switch a.active { color: var(--primary); background: #ffffff; box-shadow: 0 2px 10px rgba(0,0,0,0.15);}
        .lang-switch a:hover:not(.active) { color: #ffffff; background: rgba(255,255,255,0.15);}

        /* CLASSES ΕΛΕΓΧΟΥ ΟΡΑΤΟΤΗΤΑΣ & HAMBURGER MENU */
        .mobile-only { display: none; }
        .desktop-only { display: flex; }
        .menu-toggle { display: none; flex-direction: column; gap: 5px; cursor: pointer; padding: 5px; }
        .menu-toggle span { display: block; width: 26px; height: 3px; background: white; border-radius: 3px; transition: 0.3s; }

        /* ---------------------------------------------------- */
        /* ΠΕΡΙΕΧΟΜΕΝΟ - ΕΠΙΚΟΙΝΩΝΙΑ                           */
        /* ---------------------------------------------------- */
        .wrapper { max-width: 1200px; margin: 0 auto; padding: 140px 20px 80px 20px; flex: 1; width: 100%; box-sizing: border-box; animation: slideUp 0.8s ease-out;}
        
        .page-title { text-align: center; margin-bottom: 50px; }
        .page-title h1 { font-size: 46px; font-weight: 900; margin-bottom: 10px; letter-spacing: -1px;}
        .page-title p { font-size: 18px; color: #bae6fd; font-weight: 400;}

        .msg-box { padding: 16px 20px; border-radius: 12px; margin-bottom: 25px; font-weight: 700; font-size: 14px; border: 1px solid;}
        .msg-success { background: #d1fae5; color: #065f46; border-color: #a7f3d0;}
        .msg-error { background: #fee2e2; color: #991b1b; border-color: #fca5a5;}

        .contact-grid { display: grid; grid-template-columns: 1fr 1.5fr; gap: 40px; align-items: start;}

        .glass-card { background: rgba(255, 255, 255, 0.92); backdrop-filter: blur(30px); border-radius: 28px; padding: 45px; box-shadow: 0 30px 60px rgba(0,0,0,0.4); color: var(--text-dark); position: relative; border-top: 6px solid var(--secondary); box-sizing: border-box;}
        
        .info-box { margin-bottom: 30px;}
        .info-box h4 { margin: 0 0 8px 0; color: var(--primary); font-size: 18px; font-weight: 800;}
        .info-box p { margin: 0; color: var(--text-muted); font-size: 15px; line-height: 1.6; font-weight: 500;}

        /* ΦΟΡΜΑ ΕΠΙΚΟΙΝΩΝΙΑΣ */
        .form-group { margin-bottom: 20px; width: 100%;}
        .form-group label { display: block; font-size: 12.5px; font-weight: 800; color: var(--primary); margin-bottom: 8px; letter-spacing: 0.5px;}
        
        .form-group input, .form-group textarea { 
            display: block;
            width: 100%; 
            padding: 16px 20px; 
            border-radius: 16px; 
            border: 1px solid rgba(226, 232, 240, 1); 
            font-family: 'Inter', sans-serif; 
            font-size: 14.5px; 
            font-weight: 600;
            outline: none; 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
            box-sizing: border-box;
            background: rgba(241, 245, 249, 0.8);
            color: var(--text-dark);
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
        }
        
        .form-group textarea {
            resize: vertical; 
            min-height: 140px;
        }

        .form-group input:hover, .form-group textarea:hover { background: rgba(226, 232, 240, 0.9); }
        .form-group input:focus, .form-group textarea:focus { 
            background: #ffffff; 
            border-color: var(--secondary); 
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15), inset 0 2px 4px rgba(0,0,0,0.02); 
        }
        
        .btn-submit { 
            background: linear-gradient(135deg, var(--secondary), #0ea5e9); color: white; 
            border: none; padding: 18px 30px; border-radius: 16px; font-size: 16px; font-weight: 900; 
            cursor: pointer; width: 100%; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
            margin-top: 10px; letter-spacing: 1px; box-shadow: 0 10px 25px rgba(59,130,246,0.3);
        }
        .btn-submit:hover { transform: translateY(-3px) scale(1.01); box-shadow: 0 15px 35px rgba(59,130,246,0.4); background: linear-gradient(135deg, #1d4ed8, #0284c7);}

        /* ---------------------------------------------------- */
        /* ΜΕΓΑΛΟ FOOTER ΟΠΩΣ ΣΤΗΝ ΑΡΧΙΚΗ ΣΕΛΙΔΑ                */
        /* ---------------------------------------------------- */
        footer { background: var(--primary); color: white; padding: 60px 40px 30px 40px; margin-top: auto;}
        .footer-content { max-width: 1400px; margin: 0 auto; display: flex; flex-wrap: wrap; justify-content: space-between; gap: 40px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 40px; margin-bottom: 30px;}
        .f-brand h2 { margin: 0 0 10px 0; font-weight: 900; color: white;}
        .f-brand p { color: #94a3b8; font-size: 14.5px; max-width: 300px; line-height: 1.6;}
        .f-links { display: flex; gap: 60px; }
        .f-col h4 { margin: 0 0 20px 0; font-size: 16px; color: #cbd5e1; text-transform: uppercase; letter-spacing: 1px;}
        .f-col a { display: block; color: #94a3b8; text-decoration: none; margin-bottom: 12px; font-size: 14.5px; transition: 0.2s;}
        .f-col a:hover { color: white; transform: translateX(5px);}
        .copyright { text-align: center; color: #64748b; font-size: 14px;}

        @keyframes slideUp { from {opacity: 0; transform: translateY(30px);} to {opacity: 1; transform: translateY(0);} }

        /* =========================================================
           📱 ΕΞΕΙΔΙΚΕΥΜΕΝΟ RESPONSIVE (ΓΙΑ IPHONE ΚΑΙ TABLET)
           ========================================================= */
        @media (max-width: 900px) {
            html, body { overflow-x: hidden; width: 100%; }

            /* 1. Header & Νέο Μενού (Hamburger) */
            header { 
                flex-direction: row; 
                justify-content: space-between; 
                padding: 12px 20px; 
            }
            
            .brand { gap: 10px; flex: 1; justify-content: flex-start;}
            .brand h2 { font-size: 18px; }
            .brand span { font-size: 9px; }
            .brand svg { width: 30px; height: 30px; }

            .mobile-only { display: flex; align-items: center; gap: 15px;}
            .desktop-only { display: none; }
            
            .lang-switch.mobile-only { border-left: none; padding-left: 0; }
            .lang-switch a { padding: 5px 8px; font-size: 11px; }

            .menu-toggle { display: flex; } 

            /* Το Κάθετο Μενού */
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
                border-bottom: 1px solid rgba(255,255,255,0.1);
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

            .nav-link { font-size: 16px; text-align: center; display: block;}
            .login-btn, .profile-btn, .logout-btn { 
                width: 100%; text-align: center; justify-content: center; 
            }

            /* 2. Κεντρικό περιεχόμενο */
            .wrapper { padding: 100px 15px 40px 15px; }
            .page-title h1 { font-size: 32px; margin-bottom: 15px; letter-spacing: -0.5px;}
            .page-title p { font-size: 15px; padding: 0 10px;}

            /* 3. Φόρμα & Πληροφορίες - Το ένα κάτω από το άλλο! */
            .contact-grid { grid-template-columns: 1fr; gap: 25px; }
            .glass-card { padding: 30px 25px; border-radius: 20px; }
            
            .glass-card h3 { font-size: 20px; }
            .info-box h4 { font-size: 16px; }
            .info-box p { font-size: 14px; }
            
            .form-group label { font-size: 12px; }
            .form-group input, .form-group textarea { padding: 14px; font-size: 14px; border-radius: 12px;}
            .btn-submit { padding: 16px; font-size: 15px; border-radius: 14px;}

            /* 4. Footer */
            .f-links { flex-direction: column; gap: 30px; align-items: center; text-align: center; }
            .f-col { text-align: center; }
            .footer-content { flex-direction: column; align-items: center; text-align: center; }
        }
    </style>
</head>
<body>

    <header>
        <a href="../index.php?lang=<?php echo $lang; ?>" class="brand">
            <svg width="44" height="44" viewBox="0 0 50 50" fill="none" style="filter: drop-shadow(0px 4px 6px rgba(0,0,0,0.3));">
                <defs>
                    <linearGradient id="logo-bg" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#00d2ff" />
                        <stop offset="100%" stop-color="#3b82f6" />
                    </linearGradient>
                </defs>
                <rect x="3" y="3" width="44" height="44" rx="14" fill="url(#logo-bg)" fill-opacity="0.15" stroke="url(#logo-bg)" stroke-width="2"/>
                <text x="25" y="26" font-family="'Inter', sans-serif" font-weight="900" font-size="17" fill="#ffffff" text-anchor="middle" dominant-baseline="middle" letter-spacing="1">STP</text>
                <path d="M 14 34 Q 25 40 36 34" stroke="#00d2ff" stroke-width="2.5" stroke-linecap="round"/>
                <circle cx="36" cy="34" r="2.5" fill="#ffffff"/>
            </svg>
            <div>
                <h2>Smart Travel Planner</h2>
                <span><?php echo $t['subtitle']; ?></span>
            </div>
        </a>
        
        <div class="mobile-only">
            <div class="lang-switch mobile-only">
                <a href="?lang=gr" class="<?php echo $lang == 'gr' ? 'active' : ''; ?>">GR</a>
                <a href="?lang=en" class="<?php echo $lang == 'en' ? 'active' : ''; ?>">EN</a>
            </div>
            <div class="menu-toggle" onclick="toggleMenu()">
                <span></span><span></span><span></span>
            </div>
        </div>

        <nav class="top-nav" id="nav-menu">
            <a href="../index.php?lang=<?php echo $lang; ?>" class="nav-link"><?php echo $t['home']; ?></a>
            <a href="about.php?lang=<?php echo $lang; ?>" class="nav-link"><?php echo $t['how_it_works']; ?></a>
            <a href="about_us.php?lang=<?php echo $lang; ?>" class="nav-link"><?php echo $t['f_about']; ?></a>
            <a href="contact.php?lang=<?php echo $lang; ?>" class="nav-link"><?php echo $t['f_contact']; ?></a>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="../profile.php" class="profile-btn">
                    👤 <?php echo $t['profile']; ?>
                </a>
                <a href="../auth/logout.php" class="logout-btn"><?php echo $t['logout']; ?></a>
            <?php else: ?>
                <a href="../auth/login.php?lang=<?php echo $lang; ?>" class="login-btn"><?php echo $t['login']; ?></a>
            <?php endif; ?>

            <div class="lang-switch desktop-only">
                <a href="?lang=gr" class="<?php echo $lang == 'gr' ? 'active' : ''; ?>">GR</a>
                <a href="?lang=en" class="<?php echo $lang == 'en' ? 'active' : ''; ?>">EN</a>
            </div>
        </nav>
    </header>

    <div class="wrapper">
        <div class="page-title">
            <h1><?php echo $t['cont_h1']; ?></h1>
            <p><?php echo $t['cont_sub']; ?></p>
        </div>

        <div class="contact-grid">
            
            <div class="glass-card" style="border-top-color: #0ea5e9;">
                <h3 style="margin-top:0; font-size:24px; color:var(--primary); font-weight:900; margin-bottom:30px; letter-spacing:-0.5px;"><?php echo $t['info_h']; ?></h3>
                
                <div class="info-box">
                    <h4><?php echo $t['info_hq']; ?></h4>
                    <p><?php echo $t['info_hq_text']; ?></p>
                </div>
                <div class="info-box">
                    <h4><?php echo $t['info_tel']; ?></h4>
                    <p><?php echo $t['info_tel_text']; ?></p>
                </div>
                <div class="info-box">
                    <h4><?php echo $t['info_email']; ?></h4>
                    <p><?php echo $t['info_email_text']; ?></p>
                </div>
            </div>

            <div class="glass-card">
                <h3 style="margin-top:0; font-size:24px; color:var(--primary); font-weight:900; margin-bottom:30px; letter-spacing:-0.5px;"><?php echo $t['form_h']; ?></h3>
                
                <?php if(!empty($success_msg)): ?>
                    <div class="msg-box msg-success"><?php echo $success_msg; ?></div>
                <?php endif; ?>
                
                <?php if(!empty($error_msg)): ?>
                    <div class="msg-box msg-error"><?php echo $error_msg; ?></div>
                <?php endif; ?>

                <form action="contact.php?lang=<?php echo $lang; ?>" method="POST">
                    <div class="form-group">
                        <label><?php echo $t['f_name']; ?></label>
                        <input type="text" name="full_name" required placeholder="π.χ. Γιάννης Παπαδόπουλος">
                    </div>
                    <div class="form-group">
                        <label><?php echo $t['f_email']; ?></label>
                        <input type="email" name="email" required placeholder="name@example.com">
                    </div>
                    <div class="form-group">
                        <label><?php echo $t['f_msg']; ?></label>
                        <textarea name="message" required placeholder="Γράψτε μας το ερώτημά σας εδώ..."></textarea>
                    </div>
                    <input type="hidden" name="submit_contact" value="1">
                    <button type="submit" class="btn-submit"><?php echo $t['btn_submit']; ?></button>
                </form>
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
                    <a href="about_us.php?lang=<?php echo $lang; ?>"><?php echo $t['f_about']; ?></a>
                    <a href="support.php?lang=<?php echo $lang; ?>"><?php echo $t['f_support']; ?></a>
                    <a href="contact.php?lang=<?php echo $lang; ?>"><?php echo $t['f_contact']; ?></a>
                </div>
                <div class="f-col">
                    <h4><?php echo $t['f_legal']; ?></h4>
                    <a href="terms.php?lang=<?php echo $lang; ?>"><?php echo $t['f_terms']; ?></a>
                    <a href="privacy.php?lang=<?php echo $lang; ?>"><?php echo $t['f_privacy']; ?></a>
                    <a href="cookies.php?lang=<?php echo $lang; ?>"><?php echo $t['f_cookies']; ?></a>
                </div>
            </div>
        </div>
        <div class="copyright">
            <?php echo $t['copyright']; ?>
        </div>
    </footer>

    <script>
        function toggleMenu() {
            document.getElementById('nav-menu').classList.toggle('active');
        }
    </script>
</body>
</html>