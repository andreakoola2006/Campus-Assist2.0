<?php
$host = 'localhost';
$db   = 'lost_found';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

$pdo = new PDO($dsn, $user, $pass, $options);

$stmt = $pdo->query("SELECT building_name, COUNT(*) as total FROM items GROUP BY building_name");
$data = $stmt->fetchAll();

$labels = [];
$counts = [];

foreach ($data as $row) {
    $labels[] = $row['building_name'];
    $counts[] = $row['total'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Building Report</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background: radial-gradient(circle at top, #0f2027, #0b0c10, #000000);
    color: white;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
}

.container {
    width: 700px;
    padding: 25px;
    border-radius: 20px;
    background: rgba(255,255,255,0.05);
    backdrop-filter: blur(20px);
    box-shadow: 0 0 40px rgba(0,255,200,0.15);
    text-align: center;
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

h2 {
    margin-bottom: 20px;
    color: #00ffc3;
}

.chart-box {
    width: 350px;
    margin: auto;
}

canvas {
    width: 100% !important;
    height: auto !important;
}
</style>
</head>

<body>
    <a href="admin_dashboard.php" class="back-btn">← Dashboard</a>

<div class="container">
    <h2>Items Lost - Building Wise</h2>
    
    <div class="chart-box">
        <canvas id="pieChart"></canvas>
    </div>
</div>

<script>
const ctx = document.getElementById('pieChart');

new Chart(ctx, {
    type: 'pie',
    data: {
        labels: <?php echo json_encode($labels); ?>,
        datasets: [{
            data: <?php echo json_encode($counts); ?>,
            backgroundColor: [
                '#00ffc3','#00c3ff','#22c55e','#facc15','#ef4444','#a855f7','#3b82f6'
            ]
        }]
    },
    options: {
        plugins: {
            legend: {
                labels: {
                    color: 'white'
                }
            }
        }
    }
});
</script>

</body>
</html>