
<?php
session_start();

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

// Get filter parameters
$post_type = isset($_GET['type']) ? $_GET['type'] : '';

// Build the query
$sql = "SELECT ep.*, u.name as posted_by_name 
        FROM exam_posts ep 
        LEFT JOIN users u ON ep.posted_by = u.id 
        WHERE 1=1";

if (!empty($post_type)) {
    $sql .= " AND ep.post_type = '$post_type'";
}

$sql .= " ORDER BY ep.created_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Cell - KJ CONNECT</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .exam-header {
            background-color: var(--primary-color);
            color: var(--white);
            padding: 3rem 0;
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .exam-header p {
            max-width: 700px;
            margin: 0 auto;
            opacity: 0.9;
        }
        
        .exam-filters {
            margin-bottom: 2rem;
            padding: 1.5rem;
            background-color: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            display: flex;
            justify-content: center;
            gap: 1rem;
        }
        
        .filter-btn {
            padding: 0.75rem 1.5rem;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            border: 2px solid var(--primary-color);
            background-color: transparent;
            color: var(--primary-color);
        }
        
        .filter-btn.active,
        .filter-btn:hover {
            background-color: var(--primary-color);
            color: var(--white);
        }
        
        .exam-posts {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
        }
        
        .exam-card {
            background-color: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 1.5rem;
            transition: var(--transition);
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        
        .exam-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        .exam-card-header {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1rem;
        }
        
        .exam-card-icon {
            font-size: 2rem;
            color: var(--primary-color);
            margin-right: 1rem;
        }
        
        .exam-card-title {
            flex: 1;
        }
        
        .exam-card-title h3 {
            font-size: 1.25rem;
            margin-bottom: 0.25rem;
        }
        
        .exam-type {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--white);
            background-color: var(--secondary-color);
        }
        
        .exam-card-content {
            margin-bottom: 1.5rem;
            flex: 1;
        }
        
        .exam-card-footer {
            margin-top: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .exam-date {
            font-size: 0.875rem;
            color: var(--dark-gray);
        }
        
        .no-results {
            text-align: center;
            padding: 3rem;
            background-color: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            grid-column: 1 / -1;
        }
        
        .no-results i {
            font-size: 3rem;
            color: var(--dark-gray);
            margin-bottom: 1rem;
        }
        
        .no-results p {
            margin-bottom: 1.5rem;
            color: var(--dark-gray);
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
                        <li><a href="exam-cell.php" class="active">Exam Cell</a></li>
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

    <div class="exam-header">
        <div class="container">
            <h1>Exam Cell Information</h1>
            <p>Access the latest exam timetables, results, and other important academic information posted by the Exam Cell.</p>
        </div>
    </div>

    <main>
        <div class="container">
            <div class="exam-filters">
                <a href="exam-cell.php" class="filter-btn <?php echo empty($post_type) ? 'active' : ''; ?>">All Posts</a>
                <a href="exam-cell.php?type=timetable" class="filter-btn <?php echo $post_type === 'timetable' ? 'active' : ''; ?>">Timetables</a>
                <a href="exam-cell.php?type=result" class="filter-btn <?php echo $post_type === 'result' ? 'active' : ''; ?>">Results</a>
            </div>
            
            <?php if($result->num_rows > 0): ?>
                <div class="exam-posts">
                    <?php while($post = $result->fetch_assoc()): ?>
                        <div class="exam-card">
                            <div class="exam-card-header">
                                <div class="exam-card-icon">
                                    <?php if($post['post_type'] === 'timetable'): ?>
                                        <i class="fas fa-calendar-alt"></i>
                                    <?php else: ?>
                                        <i class="fas fa-file-alt"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="exam-card-title">
                                    <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                                    <span class="exam-type"><?php echo ucfirst($post['post_type']); ?></span>
                                </div>
                            </div>
                            <div class="exam-card-content">
                                <p><?php echo htmlspecialchars($post['description']); ?></p>
                            </div>
                            <div class="exam-card-footer">
                                <span class="exam-date"><?php echo date("M j, Y", strtotime($post['created_at'])); ?></span>
                                <a href="<?php echo htmlspecialchars($post['file_path']); ?>" class="btn btn-small" download>
                                    <i class="fas fa-download"></i> Download
                                </a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="no-results">
                    <i class="fas fa-file-excel"></i>
                    <h3>No Exam Posts Found</h3>
                    <p>There are no exam posts matching your filter criteria.</p>
                    <a href="exam-cell.php" class="btn btn-secondary">View All Posts</a>
                </div>
            <?php endif; ?>
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
// Close connection
$conn->close();
?>
