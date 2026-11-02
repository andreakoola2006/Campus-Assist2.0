<?php
session_start();
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $conn = new mysqli("localhost", "root", "", "login");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows == 1) {

        $user = $result->fetch_assoc();

        
        if (password_verify($password, $user['password'])) {

            $_SESSION['student'] = $user['email'];
            $_SESSION['name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];

            header("Location:Lost_and_found/index.php");
            exit();

        } else {
            $error = "Invalid password!";
        }

    } else {
        $error = "User not found!";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Login</title>

<style>

body {
    margin: 0;
    font-family: 'Segoe UI', sans-serif;
    background: radial-gradient(circle at top, #0a1a2f, #05070f, #000000);
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
    box-shadow: 0 0 40px rgba(0,140,255,0.2);
}


.left {
    width: 50%;
    padding: 40px;
    align-items: center;
    color: white;
    display: flex;
    flex-direction: column;
    justify-content: center;
    background: linear-gradient(135deg, rgba(0,140,255,0.25), rgba(0,0,0,0.3));
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
    background: linear-gradient(90deg, #008cff, #00c6ff);
    color: white;
    font-weight: bold;
    cursor: pointer;
    transition: 0.3s;
}

button:hover {
    box-shadow: 0 0 15px #008cff;
}


.error {
    color: #ff6b6b;
    margin-bottom: 10px;
}


a {
    color: #00c6ff;
    text-decoration: none;
}
.logo {
    width: 170px;
}
</style>
</head>

<body>

<div class="container">

    
    <div class="left">
        <img src="logo.png"class="logo">
        <h1>CampusAssist</h1>
        <p>Smart Help for Smart Campuses</p>
    </div>

    
    <div class="right">
        

        <h2>Student Login</h2>

        <?php if($error) echo "<div class='error'>$error</div>"; ?>

        <form method="POST">
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="password" name="password" placeholder="Password" required>

            <button type="submit">Login</button>
        </form>

        <p style="margin-top:15px;">
             <a href="student_registration.php">Register Now</a>
        </p>
    </div>

</div>

</body>
</html>