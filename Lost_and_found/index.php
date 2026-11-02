<?php
session_start();

// Database Configuration
$host = 'localhost';
$db   = 'lost_found';
$user = 'root'; 
$pass = ''; 
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

// 1. HANDLE DELETE ACTION
$deleteMessage = "";

if (isset($_GET['delete_id']) && isset($_SESSION['student'])) {

    $deleteId = $_GET['delete_id'];
    $userEmail = $_SESSION['student']; 

    try {
        $pdo = new PDO($dsn, $user, $pass, $options);

        // Check item ownership
        $stmt = $pdo->prepare("SELECT image_path, user_email FROM items WHERE id = ?");
        $stmt->execute([$deleteId]);
        $item = $stmt->fetch();

        if ($item) {

            // DEBUG (remove later if not needed)
            // echo $item['user_email'] . " == " . $userEmail;

            if (trim($item['user_email']) === trim($userEmail)) {

                // Delete file
                if (!empty($item['image_path'])) {
    $filePath = "uploads/" . $item['image_path'];

    if (file_exists($filePath) && is_file($filePath)) {
        unlink($filePath);
    }
}

                // Delete from DB
                $stmt = $pdo->prepare("DELETE FROM items WHERE id = ?");
                $stmt->execute([$deleteId]);

                $deleteMessage = "<div class='success-msg'>Item deleted successfully!</div>";

            } else {
                $deleteMessage = "<div class='error-msg'>You can only delete your own item!</div>";
            }

        } else {
            $deleteMessage = "<div class='error-msg'>Item not found!</div>";
        }

    } catch (PDOException $e) {
        $deleteMessage = "<div class='error-msg'>Database error: " . $e->getMessage() . "</div>";
    }
}
// 2. HANDLE SEARCH (Building + Class Number)
$searchBuilding = isset($_GET['search_building']) ? trim($_GET['search_building']) : '';
$searchClass = isset($_GET['search_class']) ? trim($_GET['search_class']) : '';
$searchItems = [];
$searchError = "";

if (!empty($searchBuilding) || !empty($searchClass)) {
    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
        
        // Build dynamic query based on which fields are filled
        $sql = "SELECT * FROM items WHERE 1=1";
        $params = [];
        
        if (!empty($searchBuilding)) {
            $sql .= " AND building_name LIKE ?";
            $params[] = "%{$searchBuilding}%";
        }
        
        if (!empty($searchClass)) {
            $sql .= " AND lab_class_no LIKE ?";
            $params[] = "%{$searchClass}%";
        }
        
        $sql .= " ORDER BY date_found DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $searchItems = $stmt->fetchAll();
        
        if (empty($searchItems)) {
            $searchError = "No items found for the selected criteria.";
        }
    } catch (PDOException $e) {
        $searchError = "Search error: " . $e->getMessage();
        $searchItems = [];
    }
}

// 3. HANDLE FORM SUBMISSION (Pure PHP)
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $itemName = $_POST['item_name'] ?? '';
    $description = $_POST['description'] ?? '';
    $contact = $_POST['contact'] ?? '';
    $building = $_POST['building_name'] ?? '';
    $labClass = $_POST['lab_class_no'] ?? '';
    
    // Handle Image Upload
    $imageName = null; // default = no image

// Check if image is uploaded
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
        $message = "<div class='error-msg'>Invalid file type. Only JPG, PNG, GIF allowed.</div>";
    }
}

// ✅ INSERT ALWAYS (with or without image)
try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    $userEmail = $_SESSION['student'];

    $stmt = $pdo->prepare("
        INSERT INTO items 
        (item_name, description, contact_info, image_path, building_name, lab_class_no, user_email) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $itemName,
        $description,
        $contact,
        $imageName, // NULL if no image
        $building,
        $labClass,
        $userEmail
    ]);

    $message = "<div class='success-msg'>Item posted successfully!</div>";

} catch (PDOException $e) {
    $message = "<div class='error-msg'>Database error: " . $e->getMessage() . "</div>";
}
}


$allItems = [];
if (empty($searchBuilding) && empty($searchClass)) {
    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
        $stmt = $pdo->query("SELECT * FROM items ORDER BY date_found DESC");
        $allItems = $stmt->fetchAll();
        
        if (empty($allItems)) {
            $searchError = "No items found yet.";
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
    <title>Student Lost & Found</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container">
         <header class="header">
            <div class="logo-section">
                <img src="..\logo.png" class="logo">
                <div class="title-wrap">
                    <div class="title">CampusAssist</div>
                </div>
            </div>

            <div class="right-header">
                <div class="panel-text">Student Login</div>
                <div class="logout">
                    <a href="..\logout.php">Logout</a>
                </div>
            </div>
        </header>
            <div class="title">
                <h1>Lost & Found Board</h1>
                <p>Found something? Upload it here so the owner can find it!</p>
            </div>
        

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

        <!-- Upload Section -->
        <section class="upload-section">
            <h2>Report a Found Item</h2>
            <form action="index.php" method="POST" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group">
                        <label for="itemName">Item Name</label>
                        <input type="text" name="item_name" id="itemName" required placeholder="e.g., Blue Water Bottle">
                    </div>
                    
                    <div class="form-group">
                        <label for="building">Building Name</label>
                        <input type="text" name="building_name" id="building" required placeholder="e.g., Science Building">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="labClass">Lab/Class No.</label>
                        <input type="text" name="lab_class_no" id="labClass" required placeholder="e.g., Lab 101">
                    </div>
                    
                    <div class="form-group">
                        <label for="contact">Contact Info (Email/Phone)</label>
                        <input type="text" name="contact" id="contact" required placeholder="e.g., student@school.edu">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" rows="3" placeholder="Any specific details?"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="image">Upload Image</label>
<input type="file" name="image" id="image" accept="image/*">

<!-- Preview Box -->
<div id="previewBox" style="margin-top:10px; display:none; position:relative; width:150px;">
    <img id="previewImg" src="" style="width:100%; border-radius:8px;">

    <!-- ❌ Cross Button -->
    <span id="removeImg" style="
        position:absolute;
        top:-8px;
        right:-8px;
        background:red;
        color:white;
        border-radius:50%;
        padding:2px 6px;
        cursor:pointer;
        font-size:12px;
        font-weight:bold;
    ">✕</span>
</div>                    </div>
                </div>

                <button type="submit">Post Item</button>
            </form>
        </section>

        <hr>

        <!-- Search Section -->
        <section class="search-section">
            <h2>Search by Location</h2>
            <form action="index.php" method="GET" class="search-form">
                <input type="text" name="search_building" class="search-input" placeholder="Building Name..." value="<?php echo htmlspecialchars($searchBuilding); ?>">
                <input type="text" name="search_class" class="search-input" placeholder="Lab/Class No..." value="<?php echo htmlspecialchars($searchClass); ?>">
                <button type="submit" class="search-btn">Search</button>
                <?php if (!empty($searchBuilding) || !empty($searchClass)): ?>
                    <a href="index.php" class="clear-btn">Clear Search</a>
                <?php endif; ?>
            </form>
        </section>

        <!-- Display Section -->
        <section class="items-section">
            <h2>Recent Finds</h2>
            <div class="grid">
                <?php if (!empty($searchBuilding) || !empty($searchClass)): ?>
                    <!-- Show Search Results -->
<?php if (!empty($searchItems)): ?>
    <?php foreach ($searchItems as $item): ?>
        <div class="card">

            <?php if (isset($_SESSION['student']) && ($item['user_email'] ?? '') === $_SESSION['student']): ?>
                <a href="index.php?delete_id=<?php echo $item['id']; ?>" 
                   class="delete-btn"
                   onclick="return confirm('Delete this item?');">
                    Delete
                </a>
            <?php endif; ?>

<?php if (!empty($item['image_path'])): ?>
    <img src="uploads/<?php echo htmlspecialchars($item['image_path']); ?>">
<?php endif; ?>
                                <div class="card-body">
                                    <h3 class="card-title"><?php echo htmlspecialchars($item['item_name']); ?></h3>
                                    <p class="card-desc"><?php echo htmlspecialchars($item['description']); ?></p>
                                    <div class="card-contact"><font color="red">Contact-</font> <?php echo htmlspecialchars($item['contact_info']); ?></div>
                                    <div class="card-location"><font color="red">Location-</font> <?php echo htmlspecialchars($item['building_name']); ?> - <?php echo htmlspecialchars($item['lab_class_no']); ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="color: #666; text-align: center;">No items found for "<?php echo htmlspecialchars($searchBuilding); ?>" in <?php echo htmlspecialchars($searchClass); ?></p>
                    <?php endif; ?>
                <?php elseif (empty($searchBuilding) && empty($searchClass) && !empty($allItems)): ?>
                    <!-- Show All Items -->
                    <?php foreach ($allItems as $item): ?>
    <div class="card">

        <?php if (isset($_SESSION['student']) && ($item['user_email'] ?? '') === $_SESSION['student']): ?>
            <a href="index.php?delete_id=<?php echo $item['id']; ?>" 
               class="delete-btn"
               onclick="return confirm('Are you sure you want to delete this item?');">
                Delete
            </a>
        <?php endif; ?>
                            <img src="uploads/<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['item_name']); ?>">
                            <div class="card-body">
                                <h3 class="card-title"><?php echo htmlspecialchars($item['item_name']); ?></h3>
                                <p class="card-desc"><?php echo htmlspecialchars($item['description']); ?></p>
                                <div class="card-contact"><font color="red">Contact-</font> <?php echo htmlspecialchars($item['contact_info']); ?></div>
                                <div class="card-location"><font color="red">Location-</font> <?php echo htmlspecialchars($item['building_name']); ?> - <?php echo htmlspecialchars($item['lab_class_no']); ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php elseif (empty($searchBuilding) && empty($searchClass) && empty($allItems)): ?>
                    <p style="color: #666; text-align: center;">No items found yet.</p>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <script src="script.js"></script>
</body>
</html>