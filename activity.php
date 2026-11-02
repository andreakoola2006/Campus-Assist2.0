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

$total = $pdo->query("SELECT COUNT(*) FROM issues")->fetchColumn();
$pending = $pdo->query("SELECT COUNT(*) FROM issues WHERE status='Pending'")->fetchColumn();
$progress = $pdo->query("SELECT COUNT(*) FROM issues WHERE status='In Progress'")->fetchColumn();
$resolved = $pdo->query("SELECT COUNT(*) FROM issues WHERE status='Resolved'")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Activity</title>

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

.cards {
    display: flex;
    gap: 20px;
    margin-top: 40px;
}

.card {
    flex: 1;
    padding: 40px;
    border-radius: 20px;
    text-align: center;
    background: linear-gradient(135deg, rgba(0,255,200,0.2), rgba(0,0,0,0.3));
    transition: 0.3s;
}

.card:hover {
    transform: translateY(-6px);
    box-shadow: 0 0 25px #00ffc3;
}

.card h2 {
    font-size: 18px;
    margin-bottom: 10px;
}

.card .count {
    font-size: 36px;
    font-weight: bold;
    color: #00ffc3;
}

.total {
    margin-top: 40px;
    text-align: center;
    font-size: 18px;
    opacity: 0.7;
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
    <div class="title">Activity</div>
</div>

<div class="cards">

    <div class="card">
        <h2>Active</h2>
        <div class="count"><?php echo $pending; ?></div>
    </div>

    <div class="card">
        <h2>In Progress</h2>
        <div class="count"><?php echo $progress; ?></div>
    </div>

    <div class="card">
        <h2>Completed</h2>
        <div class="count"><?php echo $resolved; ?></div>
    </div>

</div>

<div class="total">
    Total Tickets Handled: <?php echo $total; ?>
</div>

</body>
</html>