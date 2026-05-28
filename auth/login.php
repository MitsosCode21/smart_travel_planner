<?php
session_start();
$lang = isset($_GET['lang']) ? $_GET['lang'] : 'gr';

$translations = [
    'gr' => [
        'subtitle' => 'Ο Προσωπικός σας Ταξιδιωτικός Σύμβουλος',
        'how_it_works' => 'Πώς λειτουργεί;',
        'f_about' => 'Σχετικά με εμάς',
        'f_contact' => 'Επικοινωνία',
        'home' => 'Αρχική',
        'welcome' => 'Καλώς ήρθατε',
        'login_msg' => 'Συνδεθείτε στον λογαριασμό σας',
        'pass' => 'Κωδικός Πρόσβασης',
        'remember' => 'Να παραμεινω συνδεδεμενος',
        'forgot' => 'Ξεχάσατε τον κωδικό;',
        'btn_login' => 'Είσοδος',
        'no_account' => 'Δεν έχετε λογαριασμό;',
        'register_here' => 'Εγγραφείτε εδώ',
        'err_wrong' => 'Λάθος Email ή Κωδικός πρόσβασης.',
        'msg_reg_ok' => 'Η εγγραφή ολοκληρώθηκε! Παρακαλώ συνδεθείτε.',
        'msg_auth_req' => '⚠️ Πρέπει να συνδεθείτε για να προχωρήσετε.'
    ],
    'en' => [
        'subtitle' => 'Your Personal Travel Advisor',
        'how_it_works' => 'How it works',
        'f_about' => 'About Us',
        'f_contact' => 'Contact',
        'home' => 'Home',
        'welcome' => 'Welcome Back',
        'login_msg' => 'Log in to your account',
        'pass' => 'Password',
        'remember' => 'Remember me',
        'forgot' => 'Forgot password?',
        'btn_login' => 'Login',
        'no_account' => "Don't have an account?",
        'register_here' => 'Sign up here',
        'err_wrong' => 'Invalid Email or Password.',
        'msg_reg_ok' => 'Registration successful! Please log in.',
        'msg_auth_req' => '⚠️ You must log in to continue.'
    ]
];
$t = $translations[$lang];

$host = 'localhost'; $db = 'smart_travel_planner'; $user = 'root'; $pass = ''; 
$message = "";

// Διαχείριση Μηνυμάτων
if (isset($_GET['registered']) && $_GET['registered'] == 1) {
    $message = "<div class='msg-box msg-success'>" . $t['msg_reg_ok'] . "</div>";
}
if (isset($_GET['auth_required']) && $_GET['auth_required'] == 1) {
    $message = "<div class='msg-box msg-error'>" . $t['msg_auth_req'] . "</div>";
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user_data && password_verify($password, $user_data['password'])) {
            $_SESSION['user_id'] = $user_data['id'];
            $_SESSION['fullname'] = $user_data['fullname'];
            
            // --- ΑΠΟΘΗΚΕΥΣΗ ΤΟΥ ΡΟΛΟΥ ΣΤΟ SESSION ---
            $_SESSION['role'] = $user_data['role'];
            
            // --- ΕΛΕΓΧΟΣ: ΕΙΝΑΙ ADMIN Η ΑΠΛΟΣ ΧΡΗΣΤΗΣ; ---
            if ($user_data['role'] === 'admin') {
                header("Location: ../admin/dashboard.php"); // Τον πάμε στο Admin Panel
            } else {
                header("Location: ../index.php?lang=" . $lang); // Τον πάμε στην Αρχική
            }
            exit();
        } else {
            $message = "<div class='msg-box msg-error'>" . $t['err_wrong'] . "</div>";
        }
    }
} catch(PDOException $e) {
    $message = "<div class='msg-box msg-error'>Error: " . $e->getMessage() . "</div>";
}
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Smart Travel Planner</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #0f172a; --secondary: #3b82f6; --accent: #0ea5e9; --text-dark: #1e293b; --text-muted: #64748b; }
        * { box-sizing: border-box; }
        
        body { 
            margin: 0; 
            font-family: 'Inter', sans-serif; 
            background: linear-gradient(rgba(10, 20, 30, 0.6), rgba(15, 30, 45, 0.9)), url('https://images.unsplash.com/photo-1436491865332-7a61a109cc05?q=80&w=1920&auto=format&fit=crop') no-repeat center center fixed; 
            background-size: cover; 
            color: #ffffff; 
            min-height: 100vh;
            display: flex; 
            flex-direction: column; 
            overflow-x: hidden;
        }
        
        /* ---------------------------------------------------- */
        /* FULL-WIDTH STICKY NAV BAR (ΙΔΙΟ ΜΕ ΤΗΝ ΑΡΧΙΚΗ)       */
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
        /* ΚΕΝΤΡΑΡΙΣΜΕΝΗ ΓΥΑΛΙΝΗ ΚΑΡΤΑ ΕΙΣΟΔΟΥ                  */
        /* ---------------------------------------------------- */
        .auth-container { 
            flex: 1; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            width: 100%; 
            padding-top: 100px; 
            padding-bottom: 50px;
        }
        
        .auth-card { 
            background: rgba(255, 255, 255, 0.90); 
            backdrop-filter: blur(40px); 
            border: 1px solid rgba(255, 255, 255, 0.8); 
            border-radius: 32px; 
            padding: 50px 40px; 
            box-shadow: 0 40px 80px -20px rgba(0, 0, 0, 0.6), 0 0 60px rgba(56, 189, 248, 0.25); 
            width: 100%; 
            max-width: 460px; 
            color: var(--text-dark); 
            position: relative;
            animation: slideUp 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
        }
        
        .brand-header { text-align: center; margin-bottom: 35px; }
        .brand-header h2 { margin: 0; font-size: 32px; color: var(--primary); font-weight: 900; letter-spacing: -1px;}
        .brand-header span { font-size: 15px; color: var(--text-muted); font-weight: 500;}
        
        /* ΜΗΝΥΜΑΤΑ ΕΠΙΤΥΧΙΑΣ ΚΑΙ ΣΦΑΛΜΑΤΟΣ */
        .msg-box { padding: 12px 15px; border-radius: 12px; font-size: 14px; font-weight: 700; text-align: center; margin-bottom: 25px; border: 1px solid; }
        .msg-error { color: #b91c1c; background: #fef2f2; border-color: #fecaca;}
        .msg-success { color: #047857; background: #d1fae5; border-color: #a7f3d0;}

        /* ΦΟΡΜΑ */
        form { display: flex; flex-direction: column; gap: 20px; }
        .field { display: flex; flex-direction: column; gap: 8px; }
        
        label { font-size: 13px; font-weight: 800; color: var(--primary); text-transform: uppercase; letter-spacing: 0.5px;}
        
        input[type="email"], input[type="password"], input[type="text"] { 
            width: 100%; padding: 16px 20px; border: 2px solid transparent; 
            border-radius: 16px; font-size: 15px; background: rgba(241, 245, 249, 0.8); 
            outline: none; font-family: 'Inter', sans-serif; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
            color: var(--text-dark); font-weight: 600; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
            box-sizing: border-box;
        }
        input[type="email"]:hover, input[type="password"]:hover, input[type="text"]:hover { background: rgba(226, 232, 240, 0.9); }
        input[type="email"]:focus, input[type="password"]:focus, input[type="text"]:focus { 
            background: #ffffff; border-color: var(--secondary); 
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15), inset 0 2px 4px rgba(0,0,0,0.02); 
        }
        
        .options-row { display: flex; justify-content: space-between; align-items: center; font-size: 13.5px; margin-top: -5px;}
        .options-row label { display: flex; align-items: center; gap: 8px; font-weight: 600; color: var(--text-muted); cursor: pointer;}
        .options-row a { color: var(--secondary); text-decoration: none; font-weight: 700; transition: 0.3s;}
        .options-row a:hover { color: var(--primary); }

        .btn { 
            width: 100%; background: linear-gradient(135deg, #2563eb, #0ea5e9); color: white; padding: 18px; 
            border-radius: 16px; border: none; cursor: pointer; font-weight: 900; font-size: 17px; 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 10px 25px rgba(37, 99, 235, 0.3); 
            letter-spacing: 1px; margin-top: 10px;
        }
        .btn:hover { transform: translateY(-3px) scale(1.02); box-shadow: 0 15px 35px rgba(37, 99, 235, 0.4); background: linear-gradient(135deg, #1d4ed8, #0284c7);}

        .auth-footer { text-align: center; margin-top: 30px; font-size: 14.5px; color: var(--text-muted); font-weight: 500;}
        .auth-footer a { color: var(--secondary); font-weight: 800; text-decoration: none; transition: 0.3s;}
        .auth-footer a:hover { color: var(--primary); }

        /* Animations */
        @keyframes slideUp { from { opacity: 0; transform: translateY(40px); } to { opacity: 1; transform: translateY(0); } }

        /* =========================================================
           📱 ΕΞΕΙΔΙΚΕΥΜΕΝΟ RESPONSIVE ΓΙΑ IPHONE ΚΑΙ TABLET
           ========================================================= */
        @media (max-width: 800px) {
            html, body { overflow-x: hidden; width: 100%; }

            /* Header & Hamburger */
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

            /* Στοίχιση Κάρτας Σύνδεσης */
            .auth-container { padding-top: 100px; padding-left: 20px; padding-right: 20px; align-items: flex-start;}
            .auth-card { padding: 40px 25px; border-radius: 24px; width: 100%; max-width: 100%;}
            
            .brand-header h2 { font-size: 26px; }
            .field input { padding: 14px 18px; font-size: 14px; }
            .btn { font-size: 15px; padding: 16px; }
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
            <a href="../pages/about.php?lang=<?php echo $lang; ?>" class="nav-link"><?php echo $t['how_it_works']; ?></a>
            <a href="../pages/about_us.php?lang=<?php echo $lang; ?>" class="nav-link"><?php echo $t['f_about']; ?></a>
            <a href="../pages/contact.php?lang=<?php echo $lang; ?>" class="nav-link"><?php echo $t['f_contact']; ?></a>
            
            <div class="lang-switch desktop-only">
                <a href="?lang=gr" class="<?php echo $lang == 'gr' ? 'active' : ''; ?>">GR</a>
                <a href="?lang=en" class="<?php echo $lang == 'en' ? 'active' : ''; ?>">EN</a>
            </div>
        </nav>
    </header>

    <div class="auth-container">
        <div class="auth-card">
            <div class="brand-header">
                <h2><?php echo $t['welcome']; ?></h2>
                <span><?php echo $t['login_msg']; ?></span>
            </div>

            <?php echo $message; ?>

            <form action="login.php?lang=<?php echo $lang; ?>" method="POST">
                <div class="field">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="name@example.com" required>
                </div>
                <div class="field">
                    <label><?php echo $t['pass']; ?></label>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>
                
                <div class="options-row">
                    <label><input type="checkbox" name="remember" style="accent-color: var(--secondary);"> <?php echo $t['remember']; ?></label>
                    <a href="#"><?php echo $t['forgot']; ?></a>
                </div>

                <button class="btn" type="submit" name="login"><?php echo $t['btn_login']; ?></button>
            </form>

            <div class="auth-footer">
                <?php echo $t['no_account']; ?> <a href="register.php?lang=<?php echo $lang; ?>"><?php echo $t['register_here']; ?></a>
            </div>
        </div>
    </div>

    <script>
        function toggleMenu() {
            document.getElementById('nav-menu').classList.toggle('active');
        }
    </script>
</body>
</html>