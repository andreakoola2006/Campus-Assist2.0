<?php
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// DB connection
$host = 'localhost';
$db = 'class_availability';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Floor mapping for display
$floorNames = [
    0 => '0th Floor (TYBtech)',
    1 => '1st Floor (SYBtech)',
    2 => '2nd Floor (FYBtech)'
];

// Handle selections
$selectedDate = $_POST['selected_date'] ?? $_GET['selected_date'] ?? null;
$floor = $_GET['floor'] ?? null;

// Handle status update
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_class']) && $selectedDate && $floor !== null) {  
    $labNumber = (int)$_POST['lab_number'];
    $currentStatus = $_POST['current_status'];
    $newStatus = ($currentStatus === 'available') ? 'occupied' : 'available';

    if ($newStatus === 'occupied') {
        $stmt = $pdo->prepare("INSERT INTO btech_lab (floor, lab_number, status, date) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE status = ?");  
        $stmt->execute([$floor, $labNumber, $newStatus, $selectedDate, $newStatus]);
        $message = "Lab $labNumber Blocked";
    } else {
        $stmt = $pdo->prepare("DELETE FROM btech_lab WHERE floor = ? AND lab_number = ? AND date = ?");  
        $stmt->execute([$floor, $labNumber, $selectedDate]);
                $message = "Lab $class_number Free";

    }
     header("Location: ?floor=$floor&selected_date=$selectedDate&msg=" . urlencode($message));
    exit();
}


$labs = [];
if ($selectedDate && $floor !== null) {  // Fixed for floor 0
    $stmt = $pdo->prepare("SELECT lab_number, status FROM btech_lab WHERE floor = ? AND date = ?"); 
    $stmt->execute([$floor, $selectedDate]);
    $existing = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $labs = array_fill(1, 6, 'available');  // 6 labs
    foreach ($existing as $row) {
        $labs[$row['lab_number']] = $row['status'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>BTech Lab Availability</title>
    <link rel="stylesheet" href="btech_lab.css?v=<?php echo time(); ?>">  
</head>
<body>
    <?php
if(isset($_GET['msg'])){
    if(strpos($_GET['msg'], 'Blocked') !== false){
        $bgColor = '#800000'; // maroon
        $color = '#ffffff';
    } else {
        $bgColor = '#2ecc71'; // green
        $color = '#ffffff';
    }
}
?>
    <?php if(isset($_GET['msg'])): ?>
<div id="msgBox" style="
position:absolute;
    text-align:center;
top: 60px;              
    left: 36.5%;  
      padding:12px 16px;
    width:320px;
    border-radius:10px;
background: <?php echo $bgColor; ?>;
color: <?php echo $color; ?>;
    font-weight:600;
    border: 1px solid white;
    
    transition: opacity 0.5s ease, transform 0.5s ease;
">
    <?php echo $_GET['msg']; ?>
</div>
<?php endif; ?>
    <div class="overlay" style="position:relative">
        <div class="content">
            <div class="title">
                <h1>BTech Lab Availability</h1>
                <?php if ($selectedDate): ?>
                    <p>Selected Date: <?php echo date('F j, Y', strtotime($selectedDate)); ?></p>
                    <?php if ($floor !== null && isset($floorNames[$floor])): ?>
                        <p>Selected Floor: <?php echo $floorNames[$floor]; ?></p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <!-- Date Selector -->
            <form method="POST" style="text-align: center; margin-bottom: 20px;">
                <label for="selected_date" style="color: #ffffff; margin-right: 10px;">Select Date:</label>
                <input type="date" id="selected_date" name="selected_date" min="<?php echo date('Y-m-d'); ?>" value="<?php echo $selectedDate; ?>" required style="padding: 8px; border-radius: 4px; border: 1px solid #ccc; background: rgba(255, 255, 255, 0.9); color: #000;">
                <button type="submit" style="padding: 8px 12px; margin-left: 10px; background: #1e90ff; color: white; border: none; border-radius: 4px;">Go</button>
            </form>

            <?php if ($selectedDate && $floor === null): ?>

    <!-- Floor Selection -->
    <div class="floor-buttons">
        <a href="?floor=0&selected_date=<?php echo $selectedDate; ?>" class="floor-btn">0th Floor (TYBtech)</a>
        <a href="?floor=1&selected_date=<?php echo $selectedDate; ?>" class="floor-btn">1st Floor (SYBtech)</a>
        <a href="?floor=2&selected_date=<?php echo $selectedDate; ?>" class="floor-btn">2nd Floor (FYBtech)</a>
    </div>

    <a href="../p1.html" style="display:block; text-align:center; margin-top:20px;">Change selection</a>

<?php elseif ($selectedDate && $floor !== null): ?>

    <!-- Lab Grid -->
    <div class="class-grid">
        <?php for ($labNum = 1; $labNum <= 6; $labNum++): ?>
            <form method="POST" style="display: inline;">
                <input type="hidden" name="selected_date" value="<?php echo $selectedDate; ?>">
                <input type="hidden" name="floor" value="<?php echo $floor; ?>">
                <input type="hidden" name="lab_number" value="<?php echo $labNum; ?>">
                <input type="hidden" name="current_status" value="<?php echo $labs[$labNum]; ?>">
                <button type="submit" name="update_class" class="class-btn <?php echo $labs[$labNum]; ?>">
                    Lab <?php echo $labNum; ?>
                </button>
            </form>
        <?php endfor; ?>
    </div>

    <a href="?selected_date=<?php echo $selectedDate; ?>" style="display:block; text-align:center; margin-top:20px; color:#1e90ff;">
        Change Floor
    </a>

<?php endif; ?>
        </div>
    </div>
    <script>
const msg = document.getElementById("msgBox");

if(msg){
    msg.style.opacity = "0";
    msg.style.transform = "scale(0.9)";
    msg.style.transition = "opacity 0.4s ease, transform 0.4s ease";

    setTimeout(() => {
        msg.style.opacity = "1";
        msg.style.transform = "scale(1)";
    }, 100);

    setTimeout(() => {
        msg.style.opacity = "0";
        msg.style.transform = "scale(0)";
    }, 3000);
}
</script>
</body>
</html>