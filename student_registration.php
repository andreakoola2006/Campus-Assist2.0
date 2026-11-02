<?php
$conn = new mysqli("localhost", "root", "", "login");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm = $_POST['confirmPassword'];

    if ($password !== $confirm) {
        $error = "Passwords do not match";
    } else {

        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "Email already exists";
        } else {

            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $role = "student";
            $sub_role = NULL;

            $stmt = $conn->prepare(
    "INSERT INTO users (email, password, role, full_name, phone, created_at) 
     VALUES (?, ?, ?, ?, ?, NOW())"
);

            $stmt->bind_param("sssss", $email, $hashed, $role, $name, $phone);

            if ($stmt->execute()) {
                $success = "Signup successful!";
            } else {
                $error = "Error while registering";
            }

            $stmt->close();
        }

        $check->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Sign Up</title>

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

/* 🧊 Glass Card */
.container {
    display: flex;
    width: 800px;
    border-radius: 20px;
    overflow: hidden;
    backdrop-filter: blur(20px);
    background: rgba(255, 255, 255, 0.05);
    box-shadow: 0 0 40px rgba(0,140,255,0.2);
}

/* LEFT PANEL */
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

/* RIGHT PANEL */
.right {
    width: 50%;
    padding: 40px;
    text-align: center;
    color: white;
}



/* Inputs */
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

/* Button */
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


/* TEXTS */
.message {
    margin-bottom: 10px;
    font-size: 14px;
}

.success {
    color: #00ffae;
}

.error {
    color: #ff6b6b;
}

.bottom-text {
    margin-top: 18px;
    font-size: 14px;
    opacity: 0.8;
}

.bottom-text a {
    color: #00d4ff;
    text-decoration: none;
}

.bottom-text a:hover {
    text-decoration: underline;
}

/* LOGO */
.logo {
    width: 170px;
    margin-bottom: 15px;
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
        <h2>Student Sign Up</h2>

        <?php if ($success): ?>
            <div class="message success"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" onsubmit="return validateForm()">

            <input type="text" name="name" id="name" placeholder="Full Name">
<input type="email" name="email" id="email" placeholder="@mitwpu.edu.in Email only"
       pattern="^[^ ]+@mitwpu\.edu\.in$" title="Email  must end with @mitwpu.edu.in only" required> 
<input type="tel" name="phone" id="phone" placeholder="10 digit Phone Number"
       pattern="[0-9]{10}" maxlength="10" title="Phone number must be exactly 10 digits" required>            
<input type="password" name="password"
placeholder="Password"
pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*[^A-Za-z0-9]).{6,}"
title="Must contain at least 1 uppercase, 1 lowercase, 1 special character, and be at least 6 characters long"
required>     
       <input type="password" name="confirmPassword" id="confirmPassword" placeholder="Confirm Password">

            <button type="submit">Sign Up</button>

            <div class="bottom-text">
                Already have an account? <a href="student_login.php">Log In</a>
            </div>

        </form>
    </div>

</div>

<script>
function validateForm() {

    let name = document.getElementById("name").value.trim();
    let email = document.getElementById("email").value.trim();
    let password = document.getElementById("password").value;
    let confirmPassword = document.getElementById("confirmPassword").value;

    if (name.length < 3) {
        alert("Name must be at least 3 characters");
        return false;
    }

    let emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;
    if (!email.match(emailPattern)) {
        alert("Invalid email");
        return false;
    }

    let passwordPattern = /^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{6,}$/;
    if (!password.match(passwordPattern)) {
        alert("Password must have uppercase, number, special char");
        return false;
    }

    if (password !== confirmPassword) {
        alert("Passwords do not match");
        return false;
    }

    return true;
}
</script>

</body>
</html>