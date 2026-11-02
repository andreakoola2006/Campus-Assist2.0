<?php
session_start();

if (!isset($_SESSION['sub_role']) || $_SESSION['sub_role'] != 'Technical Staff') {
    header("Location: staff_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Technical Staff Dashboard</title>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', sans-serif;
}

body {
    min-height: 100vh;
    background: radial-gradient(circle at top, #0f2027, #0b0c10, #000000);
    color: white;
}

.dashboard {
    width: 100%;
    padding: 40px;
}

.header {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 40px;
    border-radius: 20px;
    backdrop-filter: blur(20px);
    background: rgba(255, 255, 255, 0.05);
    box-shadow: 0 0 30px rgba(0,255,200,0.15);
}

.logo-section {
    display: flex;
    align-items: center;
    gap: 15px;
}

.logo {
    width: 160px;
}

.title-wrap {
    display: flex;
    flex-direction: column;
}

.title {
    font-size: 50px;
    font-weight: 600;
}

.subtitle {
    font-size: 12px;
    color: #00ffc3;
    letter-spacing: 1px;
}

.right-header {
    display: flex;
    align-items: center;
    gap: 25px;
}

.panel-text {
    color: #00ffc3;
    font-size: 14px;
    font-weight: 500;
}

.logout a {
    color: #00ffc3;
    text-decoration: none;
    font-weight: 500;
}

.welcome {
    margin: 40px 10px 10px;
    font-size: 28px;
    font-weight: 600;
    background: linear-gradient(90deg, #00ffc3, #00c3ff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.subtext {
    margin-left: 10px;
    font-size: 15px;
    opacity: 0.7;
}

.cards {
    display: flex;
    gap: 30px;
    margin-top: 40px;
}

.card {
    flex: 1;
    padding: 50px;
    border-radius: 20px;
    cursor: pointer;
    text-align: left;
    background: linear-gradient(135deg, rgba(0,255,200,0.2), rgba(0,0,0,0.3));
    transition: 0.3s;
}

.card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 0 30px #00ffc3;
}

.card h2 {
    font-size: 22px;
    margin-bottom: 10px;
}

.card p {
    font-size: 15px;
    opacity: 0.75;
}

.footer {
    margin-top: 60px;
    text-align: center;
    font-size: 13px;
    opacity: 0.5;
}
</style>
</head>

<body>

<div class="dashboard">

    <div class="header">
        <div class="logo-section">
            <img src="logo.png" class="logo">
            <div class="title-wrap">
                <div class="title">CampusAssist</div>
            </div>
        </div>

        <div class="right-header">
            <div class="panel-text">Technical Staff Panel</div>
            <div class="logout">
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </div>

    <div class="welcome">
        Welcome, <?php echo $_SESSION['name']; ?>
    </div>

    <div class="subtext">
        Manage  technical issues efficiently
    </div>

    <div class="cards">

        <div class="card" onclick="location.href='technical_tracker.php'">
            <h2>Technical Issues Tracker</h2>
            <p>Check and manage Tech issues</p>
        </div>

        <div class="card" onclick="location.href='activity.php'">
    <h2> Activity</h2>
    <p>Check work done</p>
</div>

    </div>

    <div class="footer">
        CampusAssist • Technical Staff Panel
    </div>

</div>

</body>
</html>