
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
$event_type = isset($_GET['type']) ? $_GET['type'] : '';
$committee_id = isset($_GET['committee']) ? $_GET['committee'] : '';

// Build the query
$sql = "SELECT e.*, c.name as committee_name 
        FROM events e 
        LEFT JOIN committees c ON e.committee_id = c.id 
        WHERE 1=1";

if (!empty($event_type)) {
    $sql .= " AND e.event_type = '$event_type'";
}

if (!empty($committee_id)) {
    $sql .= " AND e.committee_id = $committee_id";
}

$sql .= " ORDER BY e.event_date DESC";

$result = $conn->query($sql);

// Get all committees for filter dropdown
$committees_query = "SELECT id, name FROM committees ORDER BY name";
$committees_result = $conn->query($committees_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events - KJ CONNECT</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .event-filters {
            margin-bottom: 2rem;
            padding: 1.5rem;
            background-color: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }
        
        .filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: center;
        }
        
        .filter-group {
            flex: 1;
            min-width: 200px;
        }
        
        .filter-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        
        .filter-group select {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #E5E7EB;
            border-radius: var(--border-radius);
        }
        
        .filter-group select:focus {
            outline: none;
            border-color: var(--primary-color);
        }
        
        .filter-buttons {
            display: flex;
            gap: 1rem;
            align-items: flex-end;
        }
        
        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
        }
        
        .no-events {
            text-align: center;
            padding: 3rem;
            background-color: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }
        
        .no-events i {
            font-size: 3rem;
            color: var(--dark-gray);
            margin-bottom: 1rem;
        }
        
        .no-events p {
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

    <main>
        <div class="container">
            <h1 class="section-title">Campus Events</h1>
            
            <div class="event-filters">
                <form class="filter-form" method="GET" action="">
                    <div class="filter-group">
                        <label for="type">Event Type</label>
                        <select id="type" name="type">
                            <option value="">All Types</option>
                            <option value="hackathon" <?php if($event_type == 'hackathon') echo 'selected'; ?>>Hackathons</option>
                            <option value="workshop" <?php if($event_type == 'workshop') echo 'selected'; ?>>Workshops</option>
                            <option value="internship" <?php if($event_type == 'internship') echo 'selected'; ?>>Internships</option>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="committee">Committee</label>
                        <select id="committee" name="committee">
                            <option value="">All Committees</option>
                            <?php while($committee = $committees_result->fetch_assoc()): ?>
                                <option value="<?php echo $committee['id']; ?>" <?php if($committee_id == $committee['id']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($committee['name']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="filter-buttons">
                        <button type="submit" class="btn btn-secondary">Apply Filters</button>
                        <a href="events.php" class="btn btn-outline">Clear Filters</a>
                    </div>
                </form>
            </div>
            
            <?php if($result->num_rows > 0): ?>
                <div class="events-grid">
                    <?php while($event = $result->fetch_assoc()): ?>
                        <div class="event-card">
                            <div class="event-badge <?php echo $event['event_type']; ?>">
                                <?php echo ucfirst($event['event_type']); ?>
                            </div>
                            <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                            <div class="event-details">
                                <p><i class="far fa-calendar"></i> <?php echo date("M j, Y", strtotime($event['event_date'])); ?></p>
                                <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($event['location']); ?></p>
                                <?php if(!empty($event['committee_name'])): ?>
                                    <p><i class="fas fa-users"></i> <?php echo htmlspecialchars($event['committee_name']); ?></p>
                                <?php endif; ?>
                            </div>
                            <p class="event-summary"><?php echo substr(htmlspecialchars($event['description']), 0, 100) . '...'; ?></p>
                            <a href="event-details.php?id=<?php echo $event['id']; ?>" class="btn btn-small">View Details</a>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="no-events">
                    <i class="far fa-calendar-times"></i>
                    <h3>No Events Found</h3>
                    <p>There are no events matching your filter criteria.</p>
                    <a href="events.php" class="btn btn-secondary">View All Events</a>
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
