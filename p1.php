<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Campus Assist</title>
    <link rel="stylesheet" href="p1.css">
</head>
<body>

<div class="overlay">
<?php
session_start();

$dashboard = 'teacher_dashboard.php'; // default

if (isset($_SESSION['admin'])) {
    $dashboard = 'admin_dashboard.php';
}
?>

<a href="<?php echo $dashboard; ?>" class="back-btn">← Dashboard</a>
    <div class="main">

        <div class="title"><h1><font color="white">Class and Lab Availability</font></h1></div>
        <br><br><br>

        <div class="section">
            <h2>Select Section</h2>
            <div class="buttons">
                <button onclick="selectSection('BCA')">BCA</button>
                <button onclick="selectSection('BCS')">BCS</button>
                <button onclick="selectSection('BTECH')">BTech</button>
            </div>
        </div>

        <div class="section hidden" id="typeBox">
            <h2 id="sectionTitle"></h2>
            <p>Select Room Type</p>
            <div class="buttons">
                <button onclick="selectType('Class')">Class</button>
                <button onclick="selectType('Lab')">Lab</button>
            </div>
        </div>

        <div class="section hidden" id="resultBox">
            <h2>Selection</h2>
            <p id="resultText"></p>
        </div>

    </div>
</div>

<script src="p1.js"></script>
</body>
</html>