<?php
session_start();
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $code = $_POST['code'];
    $password = $_POST['password'];

    if ($code === "TTRV" && $password === "mitwpu#2k26") {

        $_SESSION['admin'] = true;

        header("Location: admin_dashboard.php");
        exit();
    }

    $error = "Invalid admin credentials";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Login</title>

<style>
body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background: radial-gradient(circle at top, #2b0a0a, #120000, #000000);
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
}

.container {
    display: flex;
    width: 800px;
    border-radius: 20px;
    overflow: hidden;
    backdrop-filter: blur(20px);
    background: rgba(255, 255, 255, 0.05);
    box-shadow: 0 0 40px rgba(128,0,0,0.25);
}

.left {
    width: 50%;
    padding: 40px;
    align-items: center;
    color: white;
    display: flex;
    flex-direction: column;
    justify-content: center;
    background: linear-gradient(135deg, rgba(128,0,0,0.3), rgba(0,0,0,0.4));
}

.left h1 {
    margin-bottom: 10px;
}

.left p {
    opacity: 0.7;
}

.right {
    width: 50%;
    padding: 40px;
    text-align: center;
    color: white;
}

input {
    width: 100%;
    padding: 12px;
    margin: 10px 0;
    border-radius: 10px;
    border: none;
    outline: none;
    background: rgba(255,255,255,0.08);
    color: white;
}

input::placeholder {
    color: #aaa;
}

button {
    width: 100%;
    padding: 12px;
    margin-top: 15px;
    border: none;
    border-radius: 12px;
    background: linear-gradient(90deg, #800000, #b30000);
    color: white;
    font-weight: bold;
    cursor: pointer;
    transition: 0.3s;
}

button:hover {
    box-shadow: 0 0 15px #800000;
}

.error {
    color: #ff6b6b;
    margin-bottom: 10px;
}

a {
    color: #b30000;
    text-decoration: none;
}

.logo {
    width:170px;
}
</style>
</head>

<body>

<div class="container">

    <div class="left">
        <img src="logo.png" class="logo">
        <h1>CampusAssist</h1>
        <p>Smart Help for Smart Campuses</p>
    </div>

    <div class="right">

        <h2>Admin Login</h2>

        <?php if($error) echo "<div class='error'>$error</div>"; ?>

        <form method="POST">
            <input type="text" name="code" placeholder="Admin Code" required>
            <input type="password" name="password" placeholder="Password" required>

            <button type="submit">Login</button>
        </form>

    </div>

</div>

</body>
</html>