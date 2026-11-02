<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin  Dashboard</title>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', sans-serif;
}

body {
    min-height: 100vh;
    background: radial-gradient(circle at top, #2b0a0a, #120000, #000000);
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
    box-shadow: 0 0 40px rgba(128,0,0,0.25);
}

.logo-section {
    display: flex;
    align-items: center;
    gap: 15px;
}

.logo {
    width: 160px;
}

.title {
    font-size: 50px;
    font-weight: 600;
}

.right-header {
    display: flex;
    align-items: center;
    gap: 25px;
}

.panel-text {
    color: #b30000;
    font-size: 14px;
    font-weight: 500;
}

.logout a {
    color: #b30000;
    text-decoration: none;
    font-weight: 500;
}

.welcome {
    margin: 40px 10px 10px;
    font-size: 28px;
    font-weight: 600;
    background: linear-gradient(90deg, #800000, #b30000);
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
    flex-wrap: wrap;
    gap: 30px;
    margin-top: 40px;
}

.card {
    flex: 1 1 calc(50% - 30px);
    padding: 50px;
    border-radius: 20px;
    cursor: pointer;
    text-align: left;
    background: linear-gradient(135deg, rgba(128,0,0,0.3), rgba(0,0,0,0.4));
    transition: 0.3s;
}

.card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 0 30px #800000;
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
            <div class="title">CampusAssist</div>
        </div>

        <div class="right-header">
            <div class="panel-text">Admin Panel</div>
            <div class="logout">
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </div>

    <div class="welcome">
        Welcome, Admin
    </div>

    <div class="subtext">
        Manage system reports and operations
    </div>

    <div class="cards">

        <div class="card" onclick="location.href='lost_and_found_report.php'">
            <h2>Lost & Found Reports</h2>
            <p>View building-wise item reports</p>
        </div>

        <div class="card" onclick="location.href='technical_issues_report.php'">
            <h2>Technical Issues Report</h2>
            <p>Analyze issue tracking performance</p>
        </div>

        <div class="card" onclick="location.href='p1.php'">
            <h2>Class Availability</h2>
            <p>Check room usage and availability</p>
        </div>

        <div class="card" onclick="location.href='website_report.php'">
            <h2>Website Report</h2>
            <p>Monitor overall system usage</p>
        </div>

    </div>

    <div class="footer">
        CampusAssist • Admin Panel
    </div>

</div>

</body>
</html>