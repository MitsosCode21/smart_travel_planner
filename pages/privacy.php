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
        
        // Περιεχόμενο Σελίδας Απορρήτου
        'priv_h1' => 'Πολιτική Απορρήτου',
        'priv_sub' => 'Πώς προστατεύουμε και διαχειριζόμαστε τα προσωπικά σας δεδομένα (GDPR).',
        's1_title' => '1. Σεβασμός στην Ιδιωτικότητα',
        's1_text' => 'Στο <strong>Smart Travel Planner</strong> λαμβάνουμε την προστασία των προσωπικών σας δεδομένων πολύ σοβαρά. Η παρούσα Πολιτική Απορρήτου εξηγεί τον τρόπο με τον οποίο συλλέγουμε, χρησιμοποιούμε και προστατεύουμε τις πληροφορίες σας όταν χρησιμοποιείτε την πλατφόρμα μας, σύμφωνα με τον Γενικό Κανονισμό Προστασίας Δεδομένων (GDPR).',
        's2_title' => '2. Δεδομένα που Συλλέγουμε',
        's2_text' => 'Για την παροχή των υπηρεσιών μας συλλέγουμε μόνο τα απολύτως απαραίτητα δεδομένα:',
        's2_li1' => '<strong>Στοιχεία Ταυτοποίησης:</strong> Ονοματεπώνυμο, Διεύθυνση Email (κατά την εγγραφή σας).',
        's2_li2' => '<strong>Στοιχεία Κρατήσεων:</strong> Επιλογές προορισμών, ημερομηνίες ταξιδιού, αριθμός ατόμων (για την έκδοση E-Tickets).',
        's2_li3' => '<strong>Στοιχεία Επικοινωνίας:</strong> Μηνύματα που μας στέλνετε μέσω της φόρμας επικοινωνίας.',
        's3_title' => '3. Χρήση των Δεδομένων',
        's3_text' => 'Τα δεδομένα σας χρησιμοποιούνται αποκλειστικά για τους παρακάτω σκοπούς:',
        's3_li1' => 'Δημιουργία και διαχείριση του προσωπικού σας λογαριασμού (Profile).',
        's3_li2' => 'Ολοκλήρωση των κρατήσεών σας και δυναμική παραγωγή των ψηφιακών σας εισιτηρίων.',
        's3_li3' => 'Αποστολή ενημερώσεων σχετικά με την κράτησή σας.',
        's4_title' => '4. Ασφάλεια και Πληρωμές',
        's4_text' => 'Η πλατφόρμα μας χρησιμοποιεί κρυπτογράφηση για την ασφαλή μετάδοση των δεδομένων σας. Τονίζουμε ότι <strong>δεν αποθηκεύουμε στοιχεία πιστωτικών καρτών</strong> στους διακομιστές μας. Όλες οι συναλλαγές πραγματοποιούνται μέσω ασφαλών, εξωτερικών τραπεζικών πυλών.',
        's5_title' => '5. Τα Δικαιώματά σας (GDPR)',
        's5_text' => 'Ως χρήστης έχετε το δικαίωμα να ζητήσετε την προβολή των δεδομένων που διατηρούμε για εσάς, καθώς και την <strong>οριστική διαγραφή</strong> του λογαριασμού και των κρατήσεών σας από τη βάση δεδομένων (Right to be forgotten).',
        'last_upd' => 'Τελευταία Ενημέρωση: Μάρτιος 2026',
        
        'cookie_msg' => 'Χρησιμοποιούμε cookies για να βελτιώσουμε την εμπειρία σας στην πλατφόρμα μας.',
        'cookie_btn' => 'Εντάξει, το κατάλαβα!'
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
        
        // Privacy Page Content
        'priv_h1' => 'Privacy Policy',
        'priv_sub' => 'How we protect and manage your personal data (GDPR).',
        's1_title' => '1. Respect for Privacy',
        's1_text' => 'At <strong>Smart Travel Planner</strong> we take the protection of your personal data very seriously. This Privacy Policy explains how we collect, use, and protect your information when you use our platform, in accordance with the General Data Protection Regulation (GDPR).',
        's2_title' => '2. Data We Collect',
        's2_text' => 'To provide our services, we collect only the absolutely necessary data:',
        's2_li1' => '<strong>Identification Data:</strong> Full Name, Email Address (upon registration).',
        's2_li2' => '<strong>Booking Data:</strong> Destination preferences, travel dates, number of people (for E-Ticket issuance).',
        's2_li3' => '<strong>Contact Data:</strong> Messages you send us via the contact form.',
        's3_title' => '3. Use of Data',
        's3_text' => 'Your data is used exclusively for the following purposes:',
        's3_li1' => 'Creation and management of your personal account (Profile).',
        's3_li2' => 'Completion of your bookings and dynamic generation of your digital tickets.',
        's3_li3' => 'Sending updates regarding your booking.',
        's4_title' => '4. Security and Payments',
        's4_text' => 'Our platform uses encryption for the secure transmission of your data. We emphasize that <strong>we do not store credit card details</strong> on our servers. All transactions are carried out through secure, external banking gateways.',
        's5_title' => '5. Your Rights (GDPR)',
        's5_text' => 'As a user, you have the right to request access to the data we hold about you, as well as the <strong>permanent deletion</strong> of your account and bookings from our database (Right to be forgotten).',
        'last_upd' => 'Last Updated: March 2026',
        
        'cookie_msg' => 'We use cookies to improve your experience on our platform.',
        'cookie_btn' => 'Got it!'
    ]
];
$t = $translations[$lang];
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $t['priv_h1']; ?> | Smart Travel Planner</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
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
        /* FULL-WIDTH STICKY NAV BAR                            */
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
        /* ΠΕΡΙΕΧΟΜΕΝΟ ΑΠΟΡΡΗΤΟΥ                               */
        /* ---------------------------------------------------- */
        .wrapper { max-width: 900px; margin: 0 auto; padding: 140px 20px 80px 20px; flex: 1; width: 100%; box-sizing: border-box; animation: slideUp 0.8s ease-out;}
        
        .page-title { text-align: center; margin-bottom: 40px; }
        .page-title h1 { font-size: 46px; font-weight: 900; margin-bottom: 10px; letter-spacing: -1px;}
        .page-title p { font-size: 18px; color: #bae6fd; font-weight: 400;}

        .glass-card { 
            background: rgba(255, 255, 255, 0.95); 
            backdrop-filter: blur(20px); 
            border-radius: 32px; 
            padding: 50px 60px; 
            box-shadow: 0 30px 60px rgba(0,0,0,0.5); 
            color: var(--text-dark); 
            position: relative; 
            border-top: 6px solid var(--secondary);
        }
        
        .legal-section { margin-bottom: 30px; }
        .legal-section h3 { color: var(--primary); font-size: 20px; font-weight: 800; margin-top: 0; margin-bottom: 15px; letter-spacing: -0.5px;}
        .legal-section p { font-size: 15.5px; line-height: 1.8; color: var(--text-muted); margin-bottom: 15px;}
        .legal-section ul { padding-left: 20px; color: var(--text-muted); font-size: 15.5px; line-height: 1.8; margin-bottom: 15px;}
        .legal-section li { margin-bottom: 8px;}
        
        .last-updated { font-size: 13px; color: var(--text-muted); text-align: right; margin-top: 40px; font-style: italic; border-top: 1px solid #e2e8f0; padding-top: 15px;}

        /* ---------------------------------------------------- */
        /* ΜΕΓΑΛΟ FOOTER                                        */
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

        /* COOKIE BANNER */
        .cookie-banner { position: fixed; bottom: 30px; left: 50%; transform: translateX(-50%) translateY(200%); background: white; padding: 20px 30px; border-radius: 16px; box-shadow: 0 15px 50px rgba(0,0,0,0.2); display: flex; align-items: center; gap: 25px; z-index: 9999; transition: transform 0.5s cubic-bezier(0.2, 0.8, 0.2, 1); width: 90%; max-width: 700px; border: 1px solid #e2e8f0; visibility: hidden;}
        .cookie-banner.show { transform: translateX(-50%) translateY(0); visibility: visible; }
        .cookie-text { flex: 1; font-size: 14px; color: var(--text-dark); line-height: 1.5; font-weight: 500;}
        .cookie-btn { background: var(--primary); color: white; border: none; padding: 12px 24px; border-radius: 10px; font-weight: 800; cursor: pointer; transition: 0.2s; white-space: nowrap;}
        .cookie-btn:hover { background: var(--secondary); }

        @keyframes slideUp { from {opacity: 0; transform: translateY(30px);} to {opacity: 1; transform: translateY(0);} }
        @keyframes fadeInDown { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }

        /* =========================================================
           📱 ΕΞΕΙΔΙΚΕΥΜΕΝΟ RESPONSIVE ΓΙΑ IPHONE ΚΑΙ TABLET
           ========================================================= */
        @media (max-width: 800px) {
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

            /* 2. Κεντρικό περιεχόμενο (Συμμάζεμα) */
            .wrapper { padding: 100px 15px 40px 15px; }
            .page-title h1 { font-size: 32px; margin-bottom: 15px; letter-spacing: -0.5px;}
            .page-title p { font-size: 15px; padding: 0 10px;}

            /* 3. Glass Card - Προσαρμογή Paddings */
            .glass-card { padding: 35px 25px; border-radius: 20px; }
            .legal-section h3 { font-size: 18px; margin-bottom: 10px;}
            .legal-section p, .legal-section ul { font-size: 14.5px; }

            /* 4. Footer & Cookie Banner */
            .f-links { flex-direction: column; gap: 30px; align-items: center; text-align: center; }
            .f-col { text-align: center; }
            .footer-content { flex-direction: column; align-items: center; text-align: center; }
            
            .cookie-banner { flex-direction: column; text-align: center; padding: 15px; gap: 15px;}
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
            <h1><?php echo $t['priv_h1']; ?></h1>
            <p><?php echo $t['priv_sub']; ?></p>
        </div>

        <div class="glass-card">
            
            <div class="legal-section">
                <h3><?php echo $t['s1_title']; ?></h3>
                <p><?php echo $t['s1_text']; ?></p>
            </div>

            <div class="legal-section">
                <h3><?php echo $t['s2_title']; ?></h3>
                <p><?php echo $t['s2_text']; ?></p>
                <ul>
                    <li><?php echo $t['s2_li1']; ?></li>
                    <li><?php echo $t['s2_li2']; ?></li>
                    <li><?php echo $t['s2_li3']; ?></li>
                </ul>
            </div>

            <div class="legal-section">
                <h3><?php echo $t['s3_title']; ?></h3>
                <p><?php echo $t['s3_text']; ?></p>
                <ul>
                    <li><?php echo $t['s3_li1']; ?></li>
                    <li><?php echo $t['s3_li2']; ?></li>
                    <li><?php echo $t['s3_li3']; ?></li>
                </ul>
            </div>

            <div class="legal-section">
                <h3><?php echo $t['s4_title']; ?></h3>
                <p><?php echo $t['s4_text']; ?></p>
            </div>

            <div class="legal-section">
                <h3><?php echo $t['s5_title']; ?></h3>
                <p><?php echo $t['s5_text']; ?></p>
            </div>

            <div class="last-updated">
                <?php echo $t['last_upd']; ?>
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

    <div class="cookie-banner" id="cookieBanner">
        <div class="cookie-text">
            🍪 <strong><?php echo $t['f_cookies']; ?>:</strong> <?php echo $t['cookie_msg']; ?>
        </div>
        <button class="cookie-btn" onclick="acceptCookies()"><?php echo $t['cookie_btn']; ?></button>
    </div>

    <script>
        function toggleMenu() {
            document.getElementById('nav-menu').classList.toggle('active');
        }

        document.addEventListener("DOMContentLoaded", function() {
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
    </script>
</body>
</html>