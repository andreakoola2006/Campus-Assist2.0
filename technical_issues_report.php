<?php
$pdo = new PDO("mysql:host=localhost;dbname=tech_support;charset=utf8mb4","root","");

$issueTypes = [];
$issueCounts = [];

$q1 = $pdo->query("SELECT issue_type, COUNT(*) as total FROM issues GROUP BY issue_type");
while ($row = $q1->fetch(PDO::FETCH_ASSOC)) {
    $issueTypes[] = $row['issue_type'];
    $issueCounts[] = (int)$row['total'];
}

$priorityTypes = [];
$priorityCounts = [];

$q2 = $pdo->query("SELECT priority, COUNT(*) as total FROM issues GROUP BY priority");
while ($row = $q2->fetch(PDO::FETCH_ASSOC)) {
    $priorityTypes[] = $row['priority'];
    $priorityCounts[] = (int)$row['total'];
}

$buildingNames = [];
$buildingCounts = [];

$q3 = $pdo->query("SELECT building_name, COUNT(*) as total FROM issues GROUP BY building_name");
while ($row = $q3->fetch(PDO::FETCH_ASSOC)) {
    $buildingNames[] = $row['building_name'];
    $buildingCounts[] = (int)$row['total'];
}

$days = [];
$counts = [];

for ($i = 0; $i <= 10; $i++) {
    $days[] = $i == 0 ? "Same Day" : $i . " Day";

    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM issues 
        WHERE status='Resolved' 
        AND completed_at IS NOT NULL 
        AND DATEDIFF(completed_at, created_at) = ?
    ");
    $stmt->execute([$i]);

    $counts[] = (int)$stmt->fetchColumn();
}
$pendingIssues = $pdo->query("
    SELECT * FROM issues 
    WHERE status != 'Resolved' 
    AND DATEDIFF(NOW(), created_at) > 10
    ORDER BY created_at ASC
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Technical Issues Report</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', sans-serif;
}

body {
    background: radial-gradient(circle at top, #0f172a, #020617);
    color: white;
    padding: 40px;
}

.container {
    max-width: 1200px;
    margin: auto;
}

.header {
    text-align: center;
    margin-bottom: 40px;
}

.header h1 {
    font-size: 28px;
    background: linear-gradient(90deg, #4facfe, #00f2fe);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.grid {
    display: flex;
    justify-content: center;
    gap: 30px;
    flex-wrap: wrap;
}

.card {
    width: 360px;
    background: rgba(255,255,255,0.05);
    padding: 25px;
    border-radius: 20px;
    backdrop-filter: blur(15px);
    box-shadow: 0 0 25px rgba(0,150,255,0.15);
}

.full-card {
    margin-top: 40px;
    background: rgba(255,255,255,0.05);
    padding: 30px;
    border-radius: 20px;
    backdrop-filter: blur(15px);
    box-shadow: 0 0 25px rgba(0,150,255,0.15);
}

.card h2,
.full-card h2 {
    text-align: center;
    margin-bottom: 15px;
    font-size: 18px;
}

canvas {
    width: 100% !important;
    height: 300px !important;
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

<div class="container">

    <div class="header">
    <h1>Technical Issues Report</h1>
</div>
    <a href="admin_dashboard.php" class="back-btn">← Dashboard</a>

    <div class="grid">

        <div class="card">
            <h2>Issue Type</h2>
            <canvas id="chart1"></canvas>
        </div>

        <div class="card">
            <h2>Priority Level</h2>
            <canvas id="chart2"></canvas>
        </div>

        <div class="card">
            <h2>Building Wise</h2>
            <canvas id="chart3"></canvas>
        </div>

    </div>

    <div class="full-card">
        <h2>Resolution Time (Days)</h2>
        <canvas id="chart4"></canvas>
    </div>
    <div class="full-card">
    <h2>Issues Pending Over 10 Days</h2>

    <?php if(count($pendingIssues) > 0): ?>
        <?php foreach($pendingIssues as $issue): ?>
            <div style="margin-bottom:15px; padding:15px; border-radius:12px; background:rgba(255,255,255,0.05);">
                <strong><?php echo htmlspecialchars($issue['issue_type']); ?></strong><br>
                <?php echo htmlspecialchars($issue['description']); ?><br>
                <?php echo htmlspecialchars($issue['building_name']); ?> - <?php echo htmlspecialchars($issue['room_number']); ?><br>
                Reported by: <?php echo htmlspecialchars($issue['reported_by']); ?><br>
                <span style="opacity:0.7; font-size:13px;">
                    <?php echo date('d M Y, h:i A', strtotime($issue['created_at'])); ?>
                </span>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="text-align:center; opacity:0.7;">No long pending issues</p>
    <?php endif; ?>
</div>

</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    Chart.defaults.color = "#ffffff";
Chart.defaults.font.size = 13;

    const issueLabels = <?php echo json_encode($issueTypes); ?>;
    const issueData = <?php echo json_encode($issueCounts); ?>;

    const priorityLabels = <?php echo json_encode($priorityTypes); ?>;
    const priorityData = <?php echo json_encode($priorityCounts); ?>;

    const buildingLabels = <?php echo json_encode($buildingNames); ?>;
    const buildingData = <?php echo json_encode($buildingCounts); ?>;

    const daysLabels = <?php echo json_encode($days); ?>;
    const daysData = <?php echo json_encode($counts); ?>;

    if(issueData.length > 0){
        new Chart(document.getElementById('chart1'), {
            type: 'pie',
            data: {
                labels: issueLabels,
                datasets: [{
                    data: issueData,
                    backgroundColor: ['#4facfe','#00f2fe','#43e97b','#38f9d7','#667eea','#764ba2','#00c6ff']
                }]
            },
options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            labels: {
                color: "#ffffff",
                font: { size: 13, weight: "bold" }
            }
        }
    }
}        });
    }

    if(priorityData.length > 0){
        new Chart(document.getElementById('chart2'), {
            type: 'pie',
            data: {
                labels: priorityLabels,
                datasets: [{
                    data: priorityData,
                    backgroundColor: ['#ff6a00','#ee0979','#ff3d3d']
                }]
            },
options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            labels: {
                color: "#ffffff",
                font: { size: 13, weight: "bold" }
            }
        }
    }
}        });
    }

    if(buildingData.length > 0){
        new Chart(document.getElementById('chart3'), {
            type: 'pie',
            data: {
                labels: buildingLabels,
                datasets: [{
                    data: buildingData,
                    backgroundColor: ['#00c9ff','#92fe9d','#00b09b','#96c93d','#3a7bd5','#3a6073']
                }]
            },
options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            labels: {
                color: "#ffffff",
                font: { size: 13, weight: "bold" }
            }
        }
    }
}        });
    }

    if(daysData.length > 0){
        new Chart(document.getElementById('chart4'), {
            type: 'bar',
            data: {
                labels: daysLabels,
                datasets: [{
                    label: 'Resolved Issues',
                    data: daysData,
                    backgroundColor: '#4facfe'
                }]
            },
            options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            labels: {
                color: "#ffffff",
                font: { size: 13, weight: "bold" }
            }
        }
    },
    scales: {
        x: {
            ticks: {
                color: "#ffffff",
                font: { weight: "bold" }
            }
        },
        y: {
    beginAtZero: true,
    ticks: {
        stepSize: 1,
        color: "#ffffff",
        font: { weight: "bold" },
        callback: function(value) {
            return Number.isInteger(value) ? value : null;
        }
    }
}
    }
}
        });
    }

});
</script>
<br>

</body>
</html>