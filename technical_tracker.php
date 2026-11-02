<?php
$host = 'localhost';
$db   = 'tech_support';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

$pdo = new PDO($dsn, $user, $pass, $options);

$pdo->query("DELETE FROM issues 
WHERE status='Resolved' 
AND completed_at IS NOT NULL 
AND completed_at < NOW() - INTERVAL 5 DAY");

if (isset($_GET['update_id']) && isset($_GET['status'])) {
    $id = $_GET['update_id'];
    $status = $_GET['status'];

    $allowed = ['Pending', 'In Progress', 'Resolved'];

    if (in_array($status, $allowed)) {

        if ($status == 'Resolved') {
            $stmt = $pdo->prepare("UPDATE issues SET status=?, completed_at=NOW() WHERE id=?");
            $stmt->execute([$status, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE issues SET status=?, completed_at=NULL WHERE id=?");
            $stmt->execute([$status, $id]);
        }
    }

    header("Location: technical_tracker.php");
    exit();
}

$stmt = $pdo->query("SELECT * FROM issues ORDER BY created_at DESC");
$issues = $stmt->fetchAll();

$active = [];
$progress = [];
$completed = [];

foreach ($issues as $issue) {
    if ($issue['status'] == 'Pending') {
        $active[] = $issue;
    } elseif ($issue['status'] == 'In Progress') {
        $progress[] = $issue;
    } else {
        $completed[] = $issue;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Technical Tracker</title>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', sans-serif;
}

body {
    background: radial-gradient(circle at top, #0f2027, #0b0c10, #000000);
    color: white;
    min-height: 100vh;
    padding: 30px;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 30px;
    border-radius: 20px;
    background: rgba(255,255,255,0.05);
    backdrop-filter: blur(20px);
    box-shadow: 0 0 30px rgba(0,255,200,0.15);
}

.title {
    font-size: 22px;
    font-weight: 600;
}

.logout a {
    color: #00ffc3;
    text-decoration: none;
}

.board {
    display: flex;
    gap: 20px;
    margin-top: 30px;
}

.column {
    flex: 1;
    background: rgba(255,255,255,0.05);
    padding: 15px;
    border-radius: 15px;
    backdrop-filter: blur(15px);
    box-shadow: 0 0 20px rgba(0,255,200,0.1);
}

.column h2 {
    margin-bottom: 15px;
    text-align: center;
    color: #00ffc3;
}

.card {
    background: linear-gradient(135deg, rgba(0,255,200,0.2), rgba(0,0,0,0.3));
    border-radius: 15px;
    padding: 15px;
    margin-bottom: 15px;
    transition: 0.3s;
}

.card:hover {
    transform: translateY(-4px);
    box-shadow: 0 0 15px #00ffc3;
}

.card h3 {
    margin-bottom: 8px;
}

.card p {
    font-size: 13px;
    opacity: 0.8;
    margin-bottom: 6px;
}

.time {
    font-size: 12px;
    opacity: 0.6;
}

.actions a {
    display: inline-block;
    margin: 5px 5px 0 0;
    padding: 5px 8px;
    border-radius: 8px;
    font-size: 11px;
    text-decoration: none;
    background: #00ffc3;
    color: black;
}
.back-btn {
    position: fixed;
    top: 20px;
    right: 30px;
    padding: 10px 18px;
    border-radius: 10px;
    background: transparent;
    color: #ffffff;
    font-weight: 600;
    text-decoration: none;
    transition: 0.3s;
    z-index: 1000;
}

.back-btn:hover {
    background: #ffffff;
    color: #000000;
    transform: scale(1.05);
    box-shadow: 0 0 12px rgba(255,255,255,0.4);
}
</style>
</head>

<body>
<a href="technical_dashboard.php" class="back-btn">← Dashboard</a>
<div class="header">
    <div class="title">Technical Issue Tracker</div>
</div>

<div class="board">

    <div class="column">
        <h2>Active</h2>
        <?php foreach ($active as $issue): ?>
        <div class="card">
            <h3><?php echo htmlspecialchars($issue['issue_type']); ?></h3>
            <p><?php echo htmlspecialchars($issue['description']); ?></p>
            <p class="time"><?php echo date('d M Y, h:i A', strtotime($issue['created_at'])); ?></p>
            <p><?php echo htmlspecialchars($issue['building_name']); ?> - <?php echo htmlspecialchars($issue['room_number']); ?></p>
            <p><?php echo htmlspecialchars($issue['reported_by']); ?></p>
            <div class="actions">
                <a href="?update_id=<?php echo $issue['id']; ?>&status=In Progress">Start</a>
                <a href="?update_id=<?php echo $issue['id']; ?>&status=Resolved">Complete</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="column">
        <h2>In Progress</h2>
        <?php foreach ($progress as $issue): ?>
        <div class="card">
            <h3><?php echo htmlspecialchars($issue['issue_type']); ?></h3>
            <p><?php echo htmlspecialchars($issue['description']); ?></p>
            <p class="time"><?php echo date('d M Y, h:i A', strtotime($issue['created_at'])); ?></p>
            <p><?php echo htmlspecialchars($issue['building_name']); ?> - <?php echo htmlspecialchars($issue['room_number']); ?></p>
            <p><?php echo htmlspecialchars($issue['reported_by']); ?></p>
            <div class="actions">
                <a href="?update_id=<?php echo $issue['id']; ?>&status=Pending">Back</a>
                <a href="?update_id=<?php echo $issue['id']; ?>&status=Resolved">Complete</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="column">
        <h2>Completed</h2>
        <?php foreach ($completed as $issue): ?>
        <div class="card">
            <h3><?php echo htmlspecialchars($issue['issue_type']); ?></h3>
            <p><?php echo htmlspecialchars($issue['description']); ?></p>
            <p class="time"><?php echo date('d M Y, h:i A', strtotime($issue['created_at'])); ?></p>
            <p><?php echo htmlspecialchars($issue['building_name']); ?> - <?php echo htmlspecialchars($issue['room_number']); ?></p>
            <p><?php echo htmlspecialchars($issue['reported_by']); ?></p>
            <div class="actions">
                <a href="?update_id=<?php echo $issue['id']; ?>&status=Pending">Reopen</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

</div>

</body>
</html>