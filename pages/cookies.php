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
        
        // Περιεχόμενο Σελίδας Cookies
        'cook_h1' => 'Πολιτική Cookies',
        'cook_sub' => 'Μάθετε πώς χρησιμοποιούμε τα cookies για τη βελτίωση της εμπειρίας σας.',
        's1_title' => '1. Τι είναι τα Cookies;',
        's1_text' => 'Τα cookies είναι μικρά αρχεία κειμένου που αποθηκεύονται στον περιηγητή σας (browser) όταν επισκέπτεστε την πλατφόρμα Smart Travel Planner. Λειτουργούν ως η "μνήμη" της ιστοσελίδας, επιτρέποντάς της να θυμάται τις επιλογές σας και να σας αναγνωρίζει στις επόμενες επισκέψεις σας.',
        's2_title' => '2. Κατηγορίες Cookies που Χρησιμοποιούμε',
        's2_sub1' => 'Απολύτως Απαραίτητα Cookies (Essential)',
        's2_text1' => 'Είναι υποχρεωτικά για την ορθή λειτουργία της πλατφόρμας. Χρησιμοποιούνται για τη διατήρηση της συνεδρίας σας (όταν κάνετε login) και για την ασφαλή μεταφορά των δεδομένων σας στο καλάθι κρατήσεων. Χωρίς αυτά, η ιστοσελίδα δεν μπορεί να λειτουργήσει.',
        's2_sub2' => 'Cookies Λειτουργικότητας (Functional)',
        's2_text2' => 'Αυτά τα cookies θυμούνται τις προτιμήσεις σας, όπως τη γλώσσα της διεπαφής (Ελληνικά/Αγγλικά) ή το αν έχετε κλείσει το αναδυόμενο banner αποδοχής των cookies (`stp_cookies_accepted`).',
        's3_title' => '3. Πώς να διαχειριστείτε τα Cookies',
        's3_text' => 'Μπορείτε ανά πάσα στιγμή να διαγράψετε ή να απενεργοποιήσετε τα cookies μέσα από τις ρυθμίσεις του browser σας (Google Chrome, Firefox, Safari κλπ). Ωστόσο, αν απενεργοποιήσετε τα Απολύτως Απαραίτητα Cookies, δεν θα μπορείτε να συνδεθείτε στον λογαριασμό σας ούτε να ολοκληρώσετε κρατήσεις.',
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
        
        // Cookies Page Content
        'cook_h1' => 'Cookie Policy',
        'cook_sub' => 'Learn how we use cookies to improve your experience.',
        's1_title' => '1. What are Cookies?',
        's1_text' => 'Cookies are small text files stored on your browser when you visit the Smart Travel Planner platform. They act as the website\'s "memory," allowing it to remember your choices and recognize you on subsequent visits.',
        's2_title' => '2. Categories of Cookies We Use',
        's2_sub1' => 'Strictly Necessary Cookies (Essential)',
        's2_text1' => 'These are mandatory for the proper functioning of the platform. They are used to maintain your session (when you log in) and securely transfer your data to the booking cart. Without them, the website cannot function.',
        's2_sub2' => 'Functional Cookies',
        's2_text2' => 'These cookies remember your preferences, such as the interface language (Greek/English) or whether you have closed the cookie acceptance banner (`stp_cookies_accepted`).',
        's3_title' => '3. How to manage Cookies',
        's3_text' => 'You can delete or disable cookies at any time through your browser settings (Google Chrome, Firefox, Safari, etc.). However, if you disable Strictly Necessary Cookies, you will not be able to log into your account or complete bookings.',
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
    <title><?php echo $t['cook_h1']; ?> | Smart Travel Planner</title>
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
        /* ΠΕΡΙΕΧΟΜΕΝΟ COOKIES                                 */
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
        
        .cookie-type { background: rgba(241, 245, 249, 0.8); padding: 20px; border-radius: 16px; margin-bottom: 15px; border: 1px solid #e2e8f0;}
        .cookie-type h4 { margin: 0 0 8px 0; color: var(--secondary); font-size: 16px; font-weight: 800;}
        .cookie-type p { margin: 0; font-size: 14.5px; color: var(--text-muted); line-height: 1.6;}

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
            .legal-section p { font-size: 14.5px; }

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
            <h1><?php echo $t['cook_h1']; ?></h1>
            <p><?php echo $t['cook_sub']; ?></p>
        </div>

        <div class="glass-card">
            
            <div class="legal-section">
                <h3><?php echo $t['s1_title']; ?></h3>
                <p><?php echo $t['s1_text']; ?></p>
            </div>

            <div class="legal-section">
                <h3><?php echo $t['s2_title']; ?></h3>
                
                <div class="cookie-type">
                    <h4><?php echo $t['s2_sub1']; ?></h4>
                    <p><?php echo $t['s2_text1']; ?></p>
                </div>

                <div class="cookie-type">
                    <h4><?php echo $t['s2_sub2']; ?></h4>
                    <p><?php echo $t['s2_text2']; ?></p>
                </div>
            </div>

            <div class="legal-section">
                <h3><?php echo $t['s3_title']; ?></h3>
                <p><?php echo $t['s3_text']; ?></p>
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