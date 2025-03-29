
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

// Initialize error and success messages
$error_message = "";
$success_message = "";

// Process registration form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection
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
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];

    // Validate email format and domain
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !strpos($email, '@somaiya.edu')) {
        $error_message = "Please use a valid @somaiya.edu email address";
    }
    // Check if passwords match
    elseif ($password !== $confirm_password) {
        $error_message = "Passwords do not match";
    }
    // Check if email already exists
    else {
        $check_email = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check_email->bind_param("s", $email);
        $check_email->execute();
        $result = $check_email->get_result();
        
        if ($result->num_rows > 0) {
            $error_message = "Email already registered. Please use a different email.";
        } else {
            // In a production environment, we would hash the password
            // $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Prepare and execute the insert statement
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $password, $role);
            
            if ($stmt->execute()) {
                $success_message = "Registration successful! You can now login.";
            } else {
                $error_message = "Error: " . $stmt->error;
            }
            
            $stmt->close();
        }
        
        $check_email->close();
    }
    
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - KJ CONNECT</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .register-container {
            max-width: 500px;
            margin: 4rem auto;
            padding: 2rem;
            background-color: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }
        
        .register-logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .register-logo img {
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
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #E5E7EB;
            border-radius: var(--border-radius);
            font-size: 1rem;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(30, 58, 138, 0.1);
        }
        
        .btn-register-form {
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
        
        .btn-register-form:hover {
            background-color: #152b67;
        }
        
        .login-link {
            text-align: center;
            margin-top: 1.5rem;
        }
        
        .login-link a {
            color: var(--secondary-color);
            font-weight: 500;
        }
        
        .login-link a:hover {
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
        
        .success-message {
            background-color: #DCFCE7;
            color: #166534;
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
            <div class="register-container">
                <div class="register-logo">
                    <img src="img/logo.png" alt="KJ CONNECT Logo">
                    <h2>Create an Account</h2>
                </div>
                
                <?php if (!empty($error_message)): ?>
                    <div class="error-message">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($success_message)): ?>
                    <div class="success-message">
                        <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>
                
                <form action="" method="post">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" placeholder="Your Name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="your.email@somaiya.edu" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="••••••••" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="••••••••" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="role">Register as</label>
                        <select id="role" name="role" required>
                            <option value="student">Student</option>
                            <option value="committee_head">Committee Head</option>
                            <option value="exam_cell">Exam Cell Staff</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn-register-form">Register</button>
                </form>
                
                <div class="login-link">
                    <p>Already have an account? <a href="login.php">Login here</a></p>
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
