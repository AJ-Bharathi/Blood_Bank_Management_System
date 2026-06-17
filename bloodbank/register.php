<?php
require_once 'config.php';

if(isset($_SESSION['user_id'])) {
    header("Location: " . $_SESSION['role'] . "_dashboard.php");
    exit();
}

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = MD5($_POST['password']);
    $role = $_POST['role'];
    $phone = $_POST['phone'];
    $location = $_POST['location'];
    $blood_group = $_POST['blood_group'];

    // Check if email exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    if($stmt->fetch()) {
        $error = "Email already registered!";
    } else {
        try {
            $pdo->beginTransaction();

            // Insert into users table
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $password, $role]);
            $user_id = $pdo->lastInsertId();

            // Insert into role specific table
            if($role == 'donor') {
                $stmt = $pdo->prepare("INSERT INTO donors (user_id, blood_group, phone, location) VALUES (?, ?, ?, ?)");
                $stmt->execute([$user_id, $blood_group, $phone, $location]);
            } else if($role == 'patient') {
                $stmt = $pdo->prepare("INSERT INTO patients (user_id, blood_group, phone, location) VALUES (?, ?, ?, ?)");
                $stmt->execute([$user_id, $blood_group, $phone, $location]);
            }

            $pdo->commit();
            $success = "Registration successful! Please login.";

        } catch(Exception $e) {
            $pdo->rollBack();
            $error = "Registration failed. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Blood Bank</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="center">
    <div class="container">
        <div class="form-box" style="max-width:480px;">
            <div class="logo"><img src="logo.png" alt="logo" 
     style="height:34px; vertical-align:middle; margin-right:10px; 
            background:white; padding:3px; border-radius:4px;"></div>
            <h2>Create Account</h2>
            <p class="subtitle">Register as Donor or Patient</p>

            <?php if($error): ?>
                <div class="alert error"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if($success): ?>
                <div class="alert success"><?php echo $success; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" placeholder="Enter your full name" required>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="Enter your email" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Create a password" required>
                </div>
                <div class="form-group">
                    <label>Register As</label>
                    <select name="role" required>
                        <option value="">-- Select Role --</option>
                        <option value="donor">Donor</option>
                        <option value="patient">Patient</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Blood Group</label>
                    <select name="blood_group" required>
                        <option value="">-- Select Blood Group --</option>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" placeholder="Enter your phone number" required>
                </div>
                <div class="form-group">
                    <label>Location / City</label>
                    <input type="text" name="location" placeholder="Enter your city" required>
                </div>
                <button type="submit" class="btn">Register</button>
            </form>
            <p class="link">Already have an account? <a href="index.php">Login here</a></p>
        </div>
    </div>
</body>
</html>