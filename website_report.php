<?php
$conn = new mysqli("localhost", "root", "", "login");

$student = 0;
$teaching = 0;
$technical = 0;

$q1 = $conn->query("SELECT COUNT(*) as total FROM users WHERE role='student'");
$student = $q1->fetch_assoc()['total'];

$q2 = $conn->query("SELECT COUNT(*) as total FROM users WHERE role='staff' AND sub_role='Teaching Staff'");
$teaching = $q2->fetch_assoc()['total'];

$q3 = $conn->query("SELECT COUNT(*) as total FROM users WHERE role='staff' AND sub_role='Technical Staff'");
$technical = $q3->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Website Report</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background: radial-gradient(circle at top, #021a3a, #000814);
    color: white;
    padding: 40px;
}

.container {
    max-width: 900px;
    margin: auto;
}

.header {
    text-align: center;
    margin-bottom: 40px;
}

.header h1 {
    font-size: 28px;
    background: linear-gradient(90deg, #1e90ff, #00c6ff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.card {
    background: rgba(255,255,255,0.05);
    padding: 30px;
    border-radius: 20px;
    backdrop-filter: blur(15px);
    box-shadow: 0 0 30px rgba(0,150,255,0.25);
}
.back-btn {
    position: fixed;
    top: 20px;
    right: 30px;
    padding: 10px 18px;
    border-radius: 10px;
    border:white;
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
        <a href="admin_dashboard.php" class="back-btn">← Dashboard</a>

<div class="container">

    <div class="header">
        <h1>Website Usage Report</h1>
    </div>

    <div class="card">
        <canvas id="barChart" height="120"></canvas>
    </div>

</div>

<script>
const ctx = document.getElementById('barChart');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Students', 'Teaching Staff', 'Technical Staff'],
        datasets: [{
            label: 'Users Count',
            data: [<?php echo $student; ?>, <?php echo $teaching; ?>, <?php echo $technical; ?>],
            backgroundColor: [
                'rgba(30,144,255,0.8)',
                'rgba(0,198,255,0.8)',
                'rgba(0,120,255,0.8)'
            ],
            borderRadius: 10
        }]
    },
    options: {
        plugins: {
            legend: { display: false }
        },
        scales: {
            x: {
                ticks: { color: '#fff' },
                grid: { display: false }
            },
            y: {
                ticks: { color: '#fff' },
                grid: { color: 'rgba(255,255,255,0.1)' }
            }
        }
    }
});
</script>

</body>
</html>