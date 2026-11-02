<?php
session_start();
// Database Configuration
$host = 'localhost';
$db   = 'tech_support'; // Changed DB name
$user = 'root'; 
$pass = ''; 
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

// 1. HANDLE DELETE ACTION (ONLY OWNER CAN DELETE)
$deleteMessage = "";

if (isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id'];

    // Get logged-in user email
    $userEmail = $_SESSION['user'] ?? $_SESSION['student'] ?? null;

    try {
        $pdo = new PDO($dsn, $user, $pass, $options);

        // Check if this issue belongs to the logged-in user
        $stmt = $pdo->prepare("SELECT image_path FROM issues WHERE id = ? AND user_email = ?");
        $stmt->execute([$deleteId, $userEmail]);
        $issue = $stmt->fetch();

        if ($issue) {
            // Delete image file
            if (!empty($issue['image_path'])) {
    $filePath = "uploads/" . $issue['image_path'];

    if (file_exists($filePath) && is_file($filePath)) {
        unlink($filePath);
    }
}

            // Delete from database
            $stmt = $pdo->prepare("DELETE FROM issues WHERE id = ? AND user_email = ?");
            $stmt->execute([$deleteId, $userEmail]);

            $deleteMessage = "<div class='success-msg'>Issue ticket deleted successfully!</div>";
        } else {
            $deleteMessage = "<div class='error-msg'>You can only delete your own issue!</div>";
        }

    } catch (PDOException $e) {
        $deleteMessage = "<div class='error-msg'>Database error: " . $e->getMessage() . "</div>";
    }
}
// 2. HANDLE SEARCH (Building + Room Number)
$searchBuilding = isset($_GET['search_building']) ? trim($_GET['search_building']) : '';
$searchRoom = isset($_GET['search_room']) ? trim($_GET['search_room']) : '';
$searchItems = [];
$searchError = "";

if (!empty($searchBuilding) || !empty($searchRoom)) {
    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
        
        $sql = "SELECT * FROM issues WHERE 1=1";
        $params = [];
        
        if (!empty($searchBuilding)) {
            $sql .= " AND building_name LIKE ?";
            $params[] = "%{$searchBuilding}%";
        }
        
        if (!empty($searchRoom)) {
            $sql .= " AND room_number LIKE ?";
            $params[] = "%{$searchRoom}%";
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $searchItems = $stmt->fetchAll();
        
        if (empty($searchItems)) {
            $searchError = "No issues found for the selected location.";
        }
    } catch (PDOException $e) {
        $searchError = "Search error: " . $e->getMessage();
        $searchItems = [];
    }
}

// 3. HANDLE FORM SUBMISSION
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $issueType = $_POST['issue_type'] ?? '';
    $description = $_POST['description'] ?? '';
    $reportedBy = $_POST['reported_by'] ?? '';
    $building = $_POST['building_name'] ?? '';
    $room = $_POST['room_number'] ?? '';
    $priority = $_POST['priority'] ?? 'Normal';
    
    // Handle Image Upload
$imageName = null; // default (no image)

if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {

    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $filename = $_FILES['image']['name'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    if (in_array($ext, $allowed)) {

        $imageName = uniqid() . '.' . $ext;
        $targetPath = "uploads/" . $imageName;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $message = "<div class='error-msg'>Failed to upload image.</div>";
        }

    } else {
        $message = "<div class='error-msg'>Invalid file type.</div>";
    }
}

// ✅ ALWAYS INSERT (with or without image)
try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    $userEmail = $_SESSION['user'] ?? $_SESSION['student'] ?? null;

    $stmt = $pdo->prepare("
        INSERT INTO issues 
        (issue_type, description, reported_by, building_name, room_number, priority, image_path, status, user_email) 
        VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending', ?)
    ");

    $stmt->execute([
        $issueType,
        $description,
        $reportedBy,
        $building,
        $room,
        $priority,
        $imageName, // NULL if no image
        $userEmail
    ]);

    $message = "<div class='success-msg'>Issue reported successfully!</div>";

} catch (PDOException $e) {
    $message = "<div class='error-msg'>Database error: " . $e->getMessage() . "</div>";
}
}

// 4. FETCH ALL ITEMS
$allItems = [];
if (empty($searchBuilding) && empty($searchRoom)) {
    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
        $stmt = $pdo->query("SELECT * FROM issues ORDER BY created_at DESC");
        $allItems = $stmt->fetchAll();
        
        if (empty($allItems)) {
            $searchError = "No issues reported yet.";
        }
    } catch (PDOException $e) {
        $searchError = "Database error: " . $e->getMessage();
        $allItems = [];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IT Support - Issue Tracker</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container">
            <a href="../teacher_dashboard.php" class="back-btn">← Dashboard</a>

        <header>
            <div class="title">
                <h1>IT Support Desk</h1>
                <p>Report Lab Computer or Projector Issues</p>
            </div>
        </header>

        <!-- Success/Error Messages -->
        <?php if (!empty($deleteMessage)): ?>
            <?php echo $deleteMessage; ?>
        <?php endif; ?>
        
        <?php if (!empty($message)): ?>
            <?php echo $message; ?>
        <?php endif; ?>
        
        <?php if (!empty($searchError)): ?>
            <div class="error-msg"><?php echo $searchError; ?></div>
        <?php endif; ?>

        <!-- Report Form -->
        <section class="upload-section">
            <h2>Report a Technical Issue</h2>
            <form action="index.php" method="POST" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group">
                        <label for="issueType">Issue Type</label>
                        <select name="issue_type" id="issueType" required>
                            <option value="">Select Issue...</option>
                            <option value="Projector Not Working">Projector Not Working</option>
                            <option value="Computer Hardware">Computer Hardware</option>
                            <option value="Software Error">Software Error</option>
                            <option value="Network/WiFi">Network/WiFi</option>
                            <option value="Audio/Visual">Audio/Visual</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="priority">Priority Level</label>
                        <select name="priority" id="priority">
                            <option value="Normal">Normal</option>
                            <option value="High">High (Class in session)</option>
                            <option value="Critical">Critical (System Down)</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="building">Building Name</label>
                        <input type="text" name="building_name" id="building" required placeholder="e.g., Engineering Hall">
                    </div>
                    
                    <div class="form-group">
                        <label for="room">Room / Lab Number</label>
                        <input type="text" name="room_number" id="room" required placeholder="e.g., Lab 204">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="reportedBy">Your Name / ID</label>
                        <input type="text" name="reported_by" id="reportedBy" required placeholder="e.g., John Doe (ID: 12345)">
                    </div>
                    
                    <div class="form-group">
    <label for="image">Upload Image</label>
    <input type="file" id="image" name="image" accept="image/*">
</div>
                </div>

                <div class="form-group">
                    <label for="description">Problem Description</label>
                    <textarea name="description" id="description" rows="3" placeholder="Describe the error message or specific problem..."></textarea>
                </div>

                <button type="submit">Submit Ticket</button>
            </form>
        </section>

        <hr>

        <!-- Search Section -->
        <section class="search-section">
            <h2>Search by Location</h2>
            <form action="index.php" method="GET" class="search-form">
                <input type="text" name="search_building" class="search-input" placeholder="Building Name..." value="<?php echo htmlspecialchars($searchBuilding); ?>">
                <input type="text" name="search_room" class="search-input" placeholder="Room Number..." value="<?php echo htmlspecialchars($searchRoom); ?>">
                <button type="submit" class="search-btn">Search</button>
                <?php if (!empty($searchBuilding) || !empty($searchRoom)): ?>
                    <a href="index.php" class="clear-btn">Clear Search</a>
                <?php endif; ?>
            </form>
        </section>

        <!-- Display Section -->
        <section class="items-section">
            <h2>Active Tickets</h2>
            <div class="grid">
                <?php 
                $displayItems = (!empty($searchBuilding) || !empty($searchRoom)) ? $searchItems : $allItems;
                
                if (!empty($displayItems)): 
                    foreach ($displayItems as $item): 
                ?>
                    <div class="card">
                        <div class="status-badge <?php echo $item['status'] == 'Resolved' ? 'resolved' : 'pending'; ?>">
                            <?php echo $item['status']; ?>
                        </div>
                        
                        <?php if(($item['user_email'] ?? '') == ($_SESSION['user'] ?? $_SESSION['student'] ?? '')): ?>
    <a href="index.php?delete_id=<?php echo $item['id']; ?>" class="delete-btn" onclick="return confirm('Delete this ticket?');">
        Delete
    </a>
<?php endif; ?>

<?php if (!empty($item['image_path'])): ?>
    <img src="uploads/<?php echo htmlspecialchars($item['image_path']); ?>">
<?php else: ?>
    <img src="https://via.placeholder.com/300x200?text=No+Image">
<?php endif; ?>
                        
                        <div class="card-body">
                            <div class="card-header">
                                <h3 class="card-title"><?php echo htmlspecialchars($item['issue_type']); ?></h3>
                                <span class="priority-tag"><?php echo htmlspecialchars($item['priority']); ?></span>
                            </div>
                            
                            <p class="card-desc"><?php echo htmlspecialchars($item['description']); ?></p>
                            
                            <div class="card-meta">
                                <div class="card-location"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($item['building_name']); ?> - <?php echo htmlspecialchars($item['room_number']); ?></div>
                                <div class="card-contact"><i class="fas fa-user"></i> Reported by: <?php echo htmlspecialchars($item['reported_by']); ?></div>
                            </div>
                        </div>
                    </div>
                <?php 
                    endforeach; 
                else: 
                ?>
                    <p style="color: #666; text-align: center; grid-column: 1/-1;">No issues found.</p>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <script src="script.js"></script>
</body>
</html>