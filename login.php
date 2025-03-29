
<?php
session_start();

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    // Redirect based on user role
    switch ($_SESSION['role']) {
        case 'committee_head':
            header("Location: committee-dashboard.php");
            break;
        case 'exam_cell':
            header("Location: exam-dashboard.php");
            break;
        case 'student':
            header("Location: student-dashboard.php");
            break;
    }
    exit;
}

// Initialize error message
$error_message = "";

// Process login form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection (replace with your actual connection code)
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "kjconnect_db";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get user input
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !strpos($email, '@somaiya.edu')) {
        $error_message = "Please use a valid @somaiya.edu email address";
    } else {
        // Prepare statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // Verify password (using password_verify in a real app)
            if ($password == $user['password']) { // In production, use password_verify()
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                
                // Redirect based on user role
                switch ($user['role']) {
                    case 'committee_head':
                        header("Location: committee-dashboard.php");
                        break;
                    case 'exam_cell':
                        header("Location: exam-dashboard.php");
                        break;
                    case 'student':
                        header("Location: student-dashboard.php");
                        break;
                }
                exit;
            } else {
                $error_message = "Invalid password";
            }
        } else {
            $error_message = "User not found";
        }
        
        $stmt->close();
    }
    
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - KJ CONNECT</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .login-container {
            max-width: 400px;
            margin: 4rem auto;
            padding: 2rem;
            background-color: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }
        
        .login-logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .login-logo img {
            height: 60px;
            margin-bottom: 1rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        
        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #E5E7EB;
            border-radius: var(--border-radius);
            font-size: 1rem;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(30, 58, 138, 0.1);
        }
        
        .btn-login-form {
            width: 100%;
            padding: 0.75rem;
            background-color: var(--primary-color);
            color: var(--white);
            border: none;
            border-radius: var(--border-radius);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .btn-login-form:hover {
            background-color: #152b67;
        }
        
        .register-link {
            text-align: center;
            margin-top: 1.5rem;
        }
        
        .register-link a {
            color: var(--secondary-color);
            font-weight: 500;
        }
        
        .register-link a:hover {
            text-decoration: underline;
        }
        
        .error-message {
            background-color: #FEE2E2;
            color: #B91C1C;
            padding: 0.75rem;
            border-radius: var(--border-radius);
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <img src="img/logo.png" alt="KJ CONNECT Logo">
                    <h1>KJ CONNECT</h1>
                </div>
                <nav class="main-nav">
                    <ul>
                        <li><a href="index.html">Home</a></li>
                        <li><a href="events.php">Events</a></li>
                        <li><a href="exam-cell.php">Exam Cell</a></li>
                        <li><a href="committees.php">Committees</a></li>
                        <li><a href="about.php">About</a></li>
                    </ul>
                </nav>
                <button class="mobile-menu-toggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </header>

    <main>
        <div class="container">
            <div class="login-container">
                <div class="login-logo">
                    <img src="img/logo.png" alt="KJ CONNECT Logo">
                    <h2>Login to KJ CONNECT</h2>
                </div>
                
                <?php if (!empty($error_message)): ?>
                    <div class="error-message">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
                
                <form action="" method="post">
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="your.email@somaiya.edu" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="••••••••" required>
                    </div>
                    
                    <button type="submit" class="btn-login-form">Login</button>
                </form>
                
                <div class="register-link">
                    <p>Don't have an account? <a href="register.php">Register here</a></p>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <img src="img/logo.png" alt="KJ CONNECT Logo">
                    <h2>KJ CONNECT</h2>
                    <p>Connecting students with events, opportunities, and academic information at K.J. Somaiya College.</p>
                </div>
                <div class="footer-links">
                    <div class="footer-column">
                        <h3>Quick Links</h3>
                        <ul>
                            <li><a href="index.html">Home</a></li>
                            <li><a href="events.php">Events</a></li>
                            <li><a href="exam-cell.php">Exam Cell</a></li>
                            <li><a href="about.php">About</a></li>
                        </ul>
                    </div>
                    <div class="footer-column">
                        <h3>Resources</h3>
                        <ul>
                            <li><a href="committees.php">Committees</a></li>
                            <li><a href="internships.php">Internships</a></li>
                            <li><a href="faq.php">FAQs</a></li>
                        </ul>
                    </div>
                    <div class="footer-column">
                        <h3>Contact</h3>
                        <p>K.J. Somaiya College</p>
                        <p>Mumbai, Maharashtra</p>
                        <p>info@kjconnect.edu</p>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> KJ CONNECT. All rights reserved.</p>
                <div class="footer-social">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <script src="js/main.js"></script>
</body>
</html>
