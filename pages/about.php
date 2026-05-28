<?php
session_start(); 

$lang = isset($_GET['lang']) ? $_GET['lang'] : 'gr';

$translations = [
    'gr' => [
        'subtitle' => 'Ο Προσωπικός σας Ταξιδιωτικός Σύμβουλος',
        'home' => 'Αρχική',
        'how_it_works' => 'Πώς λειτουργεί;',
        'login' => 'Σύνδεση / Εγγραφή',
        'logout' => 'Αποσύνδεση',
        'profile' => 'Το Προφίλ μου',
        'hello_user' => 'Γεια σου, ',
        'page_title' => 'Πώς Δημιουργούμε το Τέλειο Ταξίδι;',
        'page_desc' => 'Το Smart Travel Planner λειτουργεί σαν τον προσωπικός σας ταξιδιωτικός σύμβουλος. Αντί να χάνεστε σε αμέτρητες αναζητήσεις, εμείς φροντίζουμε για όλα σε 4 απλά βήματα:',
        
        'step1_title' => '1. 💰 Σεβόμαστε το Budget σας',
        'step1_desc' => 'Φροντίζουμε οι προτάσεις μας να ανταποκρίνονται ρεαλιστικά στα οικονομικά σας δεδομένα. Υπολογίζουμε τα πάντα βάσει των ημερών που θέλετε να ταξιδέψετε, ώστε να μην βρεθείτε προ εκπλήξεως.',
        
        'step2_title' => '2. 🎯 Ταξίδι στα Μέτρα σας',
        'step2_desc' => 'Ψάχνουμε τους ιδανικούς προορισμούς που ταιριάζουν ακριβώς στο στυλ των διακοπών σας (π.χ. χαλάρωση, διασκέδαση, οικογένεια) και στο τοπίο που ονειρεύεστε να δείτε.',
        
        'step3_title' => '3. 💡 Έξυπνες Εναλλακτικές',
        'step3_desc' => 'Τι γίνεται αν η αρχική σας επιλογή ξεφεύγει λίγο από το budget; Δεν σας αφήνουμε με άδεια χέρια! Σας προτείνουμε υπέροχες, πιο προσιτές εναλλακτικές που θα σας χαρίσουν εξίσου μοναδικές εμπειρίες.',
        
        'step4_title' => '4. ✈️ Η Καλύτερη Διαδρομή',
        'step4_desc' => 'Σας καθοδηγούμε για το πώς θα φτάσετε εκεί. Ανάλογα με τα χρήματα που σας έχουν μείνει διαθέσιμα, σας προτείνουμε το ιδανικό μεταφορικό μέσο για να ταξιδέψετε με άνεση και οικονομία.',
        
        // Footer & Extra Links
        'f_company' => 'ΕΤΑΙΡΕΙΑ',
        'f_about' => 'Σχετικά με εμάς',
        'f_support' => 'Υποστήριξη',
        'f_contact' => 'Επικοινωνία',
        'f_legal' => 'ΝΟΜΙΚΑ',
        'f_terms' => 'Όροι Χρήσης',
        'f_privacy' => 'Πολιτική Απορρήτου',
        'f_cookies' => 'Πολιτική Cookies',
        'copyright' => '© 2026 Smart Travel Planner | Σχεδιασμένο για μοναδικές εμπειρίες',
        'desc' => 'Μην χάνετε ώρες ψάχνοντας. Εισάγετε το budget, τις ημέρες και το ιδανικό τοπίο, και εμείς οργανώνουμε το τέλειο ταξίδι για εσάς.'
    ],
    'en' => [
        'subtitle' => 'Your Personal Travel Advisor',
        'home' => 'Home',
        'how_it_works' => 'How it works',
        'login' => 'Login / Register',
        'logout' => 'Logout',
        'profile' => 'My Profile',
        'hello_user' => 'Hello, ',
        'page_title' => 'How We Craft Your Perfect Trip',
        'page_desc' => 'Smart Travel Planner acts as your personal travel advisor. Instead of getting lost in endless searches, we take care of everything in 4 simple steps:',
        
        'step1_title' => '1. 💰 Respecting Your Budget',
        'step1_desc' => 'We ensure our recommendations realistically match your financial plan. We calculate everything based on the days you want to travel, so there are no surprises.',
        
        'step2_title' => '2. 🎯 Tailored to You',
        'step2_desc' => 'We look for ideal destinations that perfectly match your vacation style (e.g., relaxation, nightlife, family) and the landscape you are dreaming of.',
        
        'step3_title' => '3. 💡 Smart Alternatives',
        'step3_desc' => 'What if your first choice is slightly over budget? We won\'t leave you empty-handed! We suggest wonderful, more affordable alternatives for an equally unique experience.',
        
        'step4_title' => '4. ✈️ The Best Route',
        'step4_desc' => 'We guide you on how to get there. Depending on your remaining budget, we recommend the ideal transport method so you can travel comfortably and economically.',
        
        // Footer & Extra Links
        'f_company' => 'COMPANY',
        'f_about' => 'About Us',
        'f_support' => 'Support',
        'f_contact' => 'Contact',
        'f_legal' => 'LEGAL',
        'f_terms' => 'Terms of Use',
        'f_privacy' => 'Privacy Policy',
        'f_cookies' => 'Cookie Policy',
        'copyright' => '© 2026 Smart Travel Planner | Designed for unique experiences',
        'desc' => 'Stop wasting hours searching. Enter your budget, available days, and preferred landscape, and let us organize the perfect trip.'
    ]
];
$t = $translations[$lang];
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Πώς λειτουργεί | Smart Travel Planner</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root { 
            --primary: #0f172a; 
            --secondary: #3b82f6;
            --accent: #0ea5e9;
            --text-dark: #1e293b; 
            --text-muted: #64748b; 
            --radius: 32px; 
        }
        * { box-sizing: border-box; }
        
        body { 
            min-height: 100vh; 
            margin: 0; 
            font-family: 'Inter', sans-serif; 
            background: linear-gradient(rgba(15, 23, 42, 0.75), rgba(15, 23, 42, 0.95)), url('https://images.unsplash.com/photo-1436491865332-7a61a109cc05?q=80&w=1920&auto=format&fit=crop') no-repeat center center fixed; 
            background-size: cover; 
            color: #ffffff; 
            display: flex; 
            flex-direction: column; 
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
        /* ΠΕΡΙΕΧΟΜΕΝΟ ΣΕΛΙΔΑΣ (ΜΕ ΣΩΣΤΟ PADDING ΛΟΓΩ HEADER)   */
        /* ---------------------------------------------------- */
        .wrapper { max-width: 1200px; margin: 0 auto; padding: 140px 40px 80px 40px; flex: 1; display: flex; flex-direction: column; width: 100%; }
        
        .about-header { text-align: center; margin-bottom: 60px; animation: slideUp 0.8s ease-out forwards; }
        .about-header h1 { font-size: 46px; margin-bottom: 20px; text-shadow: 0 10px 30px rgba(0,0,0,0.5); font-weight: 900; letter-spacing: -1.5px; background: linear-gradient(135deg, #ffffff 0%, #bae6fd 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;}
        .about-header p { font-size: 18.5px; color: #e2e8f0; max-width: 800px; margin: 0 auto; line-height: 1.7; text-shadow: 0 2px 10px rgba(0,0,0,0.5);}

        /* ------------------------------------------- */
        /* ΤΟ ΤΕΛΕΙΟ 2x2 GRID ME PREMIUM ΚΑΡΤΕΣ        */
        /* ------------------------------------------- */
        .grid-container { 
            display: grid; 
            grid-template-columns: repeat(2, 1fr); 
            gap: 40px; 
            margin-bottom: 60px; 
            animation: slideUp 1s ease-out forwards;
        }
        
        .info-card { 
            background: rgba(255, 255, 255, 0.90); 
            backdrop-filter: blur(40px);
            border: 1px solid rgba(255, 255, 255, 0.8); 
            border-radius: var(--radius); 
            padding: 45px; 
            box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.5), 0 0 40px rgba(56, 189, 248, 0.15); 
            color: var(--text-dark); 
            position: relative;
            transition: transform 0.4s cubic-bezier(0.2, 0.8, 0.2, 1), box-shadow 0.4s ease;
        }

        .info-card:hover { transform: translateY(-10px); box-shadow: 0 40px 80px -12px rgba(0, 0, 0, 0.6), 0 0 50px rgba(56, 189, 248, 0.25); }
        
        .info-card h3 { margin-top: 0; font-size: 22px; color: var(--primary); font-weight: 900; margin-bottom: 15px; border-bottom: 2px solid #f1f5f9; padding-bottom: 15px; letter-spacing: -0.5px;}
        .info-card p { font-size: 15.5px; line-height: 1.7; color: var(--text-muted); margin: 0; font-weight: 500;}

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

        /* ANIMATIONS */
        @keyframes slideUp { from { opacity: 0; transform: translateY(40px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes fadeInDown { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }

        /* =========================================================
           📱 ΕΞΕΙΔΙΚΕΥΜΕΝΟ RESPONSIVE ΓΙΑ TABLET ΚΑΙ IPHONE
           ========================================================= */
        @media (max-width: 1000px) {
            .grid-container { grid-template-columns: 1fr; gap: 25px;} /* 1 στήλη στο tablet */
            .info-card { padding: 30px; }
        }

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
            .wrapper { padding: 100px 20px 40px 20px; }
            .about-header h1 { font-size: 32px; margin-bottom: 15px; letter-spacing: -0.5px;}
            .about-header p { font-size: 15px; }

            /* 3. Footer */
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
        <div class="about-header">
            <h1><?php echo $t['page_title']; ?></h1>
            <p><?php echo $t['page_desc']; ?></p>
        </div>

        <div class="grid-container">
            <div class="info-card">
                <h3><?php echo $t['step1_title']; ?></h3>
                <p><?php echo $t['step1_desc']; ?></p>
            </div>
            <div class="info-card">
                <h3><?php echo $t['step2_title']; ?></h3>
                <p><?php echo $t['step2_desc']; ?></p>
            </div>
            <div class="info-card">
                <h3><?php echo $t['step3_title']; ?></h3>
                <p><?php echo $t['step3_desc']; ?></p>
            </div>
            <div class="info-card">
                <h3><?php echo $t['step4_title']; ?></h3>
                <p><?php echo $t['step4_desc']; ?></p>
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