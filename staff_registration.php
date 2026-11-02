<?php
$conn = new mysqli("localhost", "root", "", "login");

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm = $_POST['confirmPassword'];
    $sub_role = $_POST['sub_role'];
    $role="Staff";

    $allowed_sub_roles = ['Teaching Staff', 'Technical Staff'];

    // Email validation (MIT-WPU only)


// Role validation
if (!in_array($sub_role, $allowed_sub_roles)) {
    $error = "Invalid role selected";
}
// Password match
elseif ($password !== $confirm) {
    $error = "Passwords do not match";
}
    else {

        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "Email already exists";
        } else {

            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $role = "staff";

            $stmt = $conn->prepare(
                "INSERT INTO users (email, password, role, full_name, created_at, sub_role)
                 VALUES (?, ?, ?, ?, NOW(), ?)"
            );

            $stmt->bind_param("sssss", $email, $hashed, $role, $name, $sub_role);

            if ($stmt->execute()) {
                $success = ucfirst($sub_role) . " registered successfully!";
            } else {
                $error = "Error occurred while registering";
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
<title>Teacher Sign Up</title>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', sans-serif;
}

body {
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background: radial-gradient(circle at top, #0f2027, #0b0c10,#000000);
}

.container {
    width: 800px;
    display: flex;
    border-radius: 20px;
    overflow: hidden;
    backdrop-filter: blur(20px);
    background: rgba(255,255,255,0.05);
    box-shadow: 0 0 40px rgba(0,255,200,0.15);
}

/* LEFT */
.left {
    width: 50%;
    padding: 40px;
    color: white;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    background: linear-gradient(135deg, rgba(0,255,200,0.2), rgba(0,0,0,0.3));
}

.logo {
    width: 170px;
    margin-bottom: 15px;
}

.left h1 {
    margin-bottom: 10px;
}

.left p {
    opacity: 0.7;
}

/* RIGHT */
.right {
    width: 50%;
    padding: 40px;
    color: white;
    text-align: center;
}

.right h2{
    height:40px;

}

/* INPUT */
input,select,option {
    width: 100%;
    padding: 12px;
    margin: 10px 0;
    border-radius: 10px;
    border: none;
    background: rgba(255,255,255,0.08);
    color: white;
    outline: none;
}

option{
    color:black;
    background: rgba(255, 255, 255, 0.53);

}

input::placeholder {
    color: #aaa;
}

/* BUTTON */
button {
    width: 100%;
    padding: 12px;
    border-radius: 12px;
    margin-top:15px;
    border: none;
    background: linear-gradient(90deg, #00ffc3, #00c3ff);
    color: black;
    font-weight: bold;
    cursor: pointer;
}

button:hover {
    box-shadow: 0 0 15px #00ffc3;
}

/* TEXT */
.link {
    margin-top: 12px;
    display: block;
    color: #00e6a8;
    text-decoration: none;
    text-align: center;
}

.error {
    color: #ff6b6b;
    margin-bottom: 10px;
}

.success {
    color: #00ffae;
    margin-bottom: 10px;
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
        <h2>Staff Sign Up</h2>

        <?php if($error) echo "<div class='error'>$error</div>"; ?>
        <?php if($success) echo "<div class='success'>$success</div>"; ?>

        <form method="POST">

            <input type="text" name="name" placeholder="Full Name" required>
<input type="email" name="email" id="email" placeholder="@mitwpu.edu.in Email only"
       pattern="^[^ ]+@mitwpu\.edu\.in$" title="Email  must end with @mitwpu.edu.in only"
 required> 
<input type="tel" name="phone"
placeholder="Phone Number"
pattern="[0-9]{10}"
maxlength="10"
title="Phone number must be exactly 10 digits"
required>
<input type="password" name="password"
placeholder="Password"
pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*[^A-Za-z0-9]).{6,}"
title="Must contain at least 1 uppercase, 1 lowercase, 1 special character, and be at least 6 characters long"
required> 
            <input type="password" name="confirmPassword" placeholder="Confirm Password" required>
            <select name="sub_role" required>
            <option value="Teaching Staff">Teaching Staff</option>
            <option value="Technical Staff">Technical Staff</option>
            <option value="" disabled selected hidden>Select Role</option>
            </select>
            <button type="submit">Sign Up</button>
        </form>

        <a href="staff_login.php" class="link">Already have an account? Login</a>
    </div>

</div>

</body>
</html>