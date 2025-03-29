
<?php
session_start();

// Check if ID is provided
if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: events.php");
    exit;
}

$event_id = $_GET['id'];

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

// Get event details
$sql = "SELECT e.*, c.name as committee_name, u.name as posted_by_name 
        FROM events e 
        LEFT JOIN committees c ON e.committee_id = c.id
        LEFT JOIN users u ON e.created_by = u.id
        WHERE e.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 0) {
    header("Location: events.php");
    exit;
}

$event = $result->fetch_assoc();

// Get event attachments
$attachments_sql = "SELECT * FROM event_attachments WHERE event_id = ?";
$attachments_stmt = $conn->prepare($attachments_sql);
$attachments_stmt->bind_param("i", $event_id);
$attachments_stmt->execute();
$attachments_result = $attachments_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($event['title']); ?> - KJ CONNECT</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .event-header {
            background-color: var(--primary-color);
            color: var(--white);
            padding: 3rem 0;
            margin-bottom: 2rem;
        }
        
        .event-type-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--white);
            margin-bottom: 1rem;
        }
        
        .hackathon-badge {
            background-color: var(--hackathon-color);
        }
        
        .workshop-badge {
            background-color: var(--workshop-color);
        }
        
        .internship-badge {
            background-color: var(--internship-color);
        }
        
        .event-title {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .event-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            margin-top: 1.5rem;
            color: rgba(255, 255, 255, 0.9);
        }
        
        .event-meta-item {
            display: flex;
            align-items: center;
        }
        
        .event-meta-item i {
            margin-right: 0.5rem;
        }
        
        .event-content {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 2rem;
        }
        
        .event-description {
            background-color: var(--white);
            padding: 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }
        
        .event-description h2 {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            color: var(--primary-color);
        }
        
        .event-description p {
            margin-bottom: 1.5rem;
            line-height: 1.7;
        }
        
        .event-sidebar {
            background-color: var(--white);
            padding: 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }
        
        .sidebar-section {
            margin-bottom: 2rem;
        }
        
        .sidebar-section:last-child {
            margin-bottom: 0;
        }
        
        .sidebar-section h3 {
            font-size: 1.25rem;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid var(--light-gray);
            color: var(--primary-color);
        }
        
        .registration-btn {
            width: 100%;
            text-align: center;
            margin-top: 1rem;
        }
        
        .attachments-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        
        .attachment-item {
            display: flex;
            align-items: center;
            padding: 0.75rem;
            background-color: var(--light-gray);
            border-radius: var(--border-radius);
            transition: var(--transition);
        }
        
        .attachment-item:hover {
            background-color: #E5E7EB;
        }
        
        .attachment-item i {
            font-size: 1.25rem;
            margin-right: 0.75rem;
            color: var(--primary-color);
        }
        
        .attachment-item span {
            flex: 1;
            font-size: 0.875rem;
        }
        
        .back-button {
            display: inline-flex;
            align-items: center;
            margin-bottom: 2rem;
            color: var(--primary-color);
            font-weight: 600;
            transition: var(--transition);
        }
        
        .back-button i {
            margin-right: 0.5rem;
        }
        
        .back-button:hover {
            color: var(--secondary-color);
        }
        
        @media (max-width: 992px) {
            .event-content {
                grid-template-columns: 1fr;
            }
            
            .event-title {
                font-size: 2rem;
            }
        }
        
        @media (max-width: 576px) {
            .event-meta {
                flex-direction: column;
                gap: 0.75rem;
            }
            
            .event-title {
                font-size: 1.75rem;
            }
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
                        <li><a href="events.php" class="active">Events</a></li>
                        <li><a href="exam-cell.php">Exam Cell</a></li>
                        <li><a href="committees.php">Committees</a></li>
                        <li><a href="about.php">About</a></li>
                    </ul>
                </nav>
                <div class="auth-buttons">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="logout.php" class="btn btn-login">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-login">Login</a>
                    <?php endif; ?>
                </div>
                <button class="mobile-menu-toggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </header>

    <div class="event-header">
        <div class="container">
            <div class="event-type-badge <?php echo $event['event_type']; ?>-badge">
                <?php echo ucfirst($event['event_type']); ?>
            </div>
            <h1 class="event-title"><?php echo htmlspecialchars($event['title']); ?></h1>
            
            <div class="event-meta">
                <div class="event-meta-item">
                    <i class="far fa-calendar"></i>
                    <span><?php echo date("F j, Y", strtotime($event['event_date'])); ?></span>
                </div>
                <div class="event-meta-item">
                    <i class="far fa-clock"></i>
                    <span><?php echo date("g:i A", strtotime($event['event_date'])); ?></span>
                </div>
                <div class="event-meta-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span><?php echo htmlspecialchars($event['location']); ?></span>
                </div>
                <?php if(!empty($event['committee_name'])): ?>
                <div class="event-meta-item">
                    <i class="fas fa-users"></i>
                    <span><?php echo htmlspecialchars($event['committee_name']); ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <main>
        <div class="container">
            <a href="events.php" class="back-button">
                <i class="fas fa-arrow-left"></i> Back to Events
            </a>
            
            <div class="event-content">
                <div class="event-description">
                    <h2>About This Event</h2>
                    <p><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
                    
                    <?php if(!empty($event['posted_by_name'])): ?>
                    <p class="event-posted-by">
                        <small>Posted by: <?php echo htmlspecialchars($event['posted_by_name']); ?></small>
                    </p>
                    <?php endif; ?>
                </div>
                
                <div class="event-sidebar">
                    <?php if(!empty($event['registration_link'])): ?>
                    <div class="sidebar-section">
                        <h3>Registration</h3>
                        <p>Register for this event using the link below:</p>
                        <a href="<?php echo htmlspecialchars($event['registration_link']); ?>" target="_blank" class="btn btn-primary registration-btn">
                            Register Now <i class="fas fa-external-link-alt"></i>
                        </a>
                    </div>
                    <?php endif; ?>
                    
                    <?php if($attachments_result->num_rows > 0): ?>
                    <div class="sidebar-section">
                        <h3>Attachments</h3>
                        <div class="attachments-list">
                            <?php while($attachment = $attachments_result->fetch_assoc()): ?>
                                <a href="<?php echo htmlspecialchars($attachment['file_path']); ?>" class="attachment-item" download>
                                    <i class="fas fa-file-download"></i>
                                    <span><?php echo htmlspecialchars($attachment['file_name']); ?></span>
                                </a>
                            <?php endwhile; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="sidebar-section">
                        <h3>Share This Event</h3>
                        <div class="social-share-buttons">
                            <a href="#" class="btn btn-outline" onclick="window.open('https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(window.location.href), 'facebook-share-dialog', 'width=626,height=436'); return false;">
                                <i class="fab fa-facebook-f"></i> Share
                            </a>
                            <a href="#" class="btn btn-outline" onclick="window.open('https://twitter.com/intent/tweet?text=<?php echo urlencode($event['title']); ?>&url=' + encodeURIComponent(window.location.href), 'twitter-share-dialog', 'width=626,height=436'); return false;">
                                <i class="fab fa-twitter"></i> Tweet
                            </a>
                        </div>
                    </div>
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
<?php
// Close connections
$stmt->close();
$attachments_stmt->close();
$conn->close();
?>
