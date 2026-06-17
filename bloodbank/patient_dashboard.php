<?php
require_once 'config.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'patient') {
    header("Location: index.php");
    exit();
}

// Get patient details
$stmt = $pdo->prepare("SELECT p.*, u.name, u.email FROM patients p 
                        JOIN users u ON p.user_id = u.id 
                        WHERE p.user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$patient = $stmt->fetch(PDO::FETCH_ASSOC);

$error = '';
$success = '';

// Handle blood request submission
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['request_blood'])) {
    $blood_group = $_POST['blood_group'];
    $units_needed = $_POST['units_needed'];
    $location = $_POST['location'];

    // Check blood stock
    $stmt = $pdo->prepare("SELECT units_available FROM blood_stock WHERE blood_group = ?");
    $stmt->execute([$blood_group]);
    $stock = $stmt->fetch(PDO::FETCH_ASSOC);

    if($stock && $stock['units_available'] >= $units_needed) {
    $stmt = $pdo->prepare("INSERT INTO blood_requests (patient_id, blood_group, units_needed, location) 
                            VALUES (?, ?, ?, ?)");
    $stmt->execute([$patient['id'], $blood_group, $units_needed, $location]);

    // Find matching available donors and notify them
    require_once 'send_email.php';
    $stmt = $pdo->prepare("SELECT u.name, u.email FROM donors d 
                            JOIN users u ON d.user_id = u.id 
                            WHERE d.blood_group = ? 
                            AND d.location = ? 
                            AND d.is_available = 1");
    $stmt->execute([$blood_group, $location]);
    $matching_donors = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $notified = 0;
    foreach($matching_donors as $donor) {
        $sent = sendDonorNotification(
            $donor['email'],
            $donor['name'],
            $_SESSION['name'],
            $blood_group,
            $units_needed,
            $location
        );
        if($sent) $notified++;
    }

    $success = "Blood request submitted! {$notified} matching donor(s) notified via email.";
    } else {
        $error = "Sorry! Insufficient blood stock for " . $blood_group . ". Available: " . 
                 ($stock ? $stock['units_available'] : 0) . " units.";
    }
}

// Get patient's requests
$stmt = $pdo->prepare("SELECT * FROM blood_requests WHERE patient_id = ? ORDER BY requested_at DESC");
$stmt->execute([$patient['id']]);
$my_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get available donors matching patient's blood group and location
$stmt = $pdo->prepare("SELECT u.name, d.phone, d.location, d.blood_group 
                        FROM donors d JOIN users u ON d.user_id = u.id 
                        WHERE d.blood_group = ? 
                        AND d.location = ? 
                        AND d.is_available = 1");
$stmt->execute([$patient['blood_group'], $patient['location']]);
$matching_donors = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Dashboard - Blood Bank Management</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* FOOTER */
        .footer {
            background: #b22222;
            color: white;
            text-align: center;
            padding: 18px;
            font-size: 12px;

            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
        }
    </style>    
</head>
<body>

<nav>
    <span>
        <img src="logo.png" alt="logo" 
     style="height:34px; vertical-align:middle; margin-right:10px; 
            background:white; padding:3px; border-radius:4px;"> 
            Blood Bank Management System — Patient
    </span>
    <span>Welcome, <?php echo $_SESSION['name']; ?> | <a href="logout.php">Logout</a></span>
</nav>

<div class="dashboard">
    <h1>Patient Dashboard</h1>

    <!-- Stats -->
    <div class="cards" style="margin-bottom:30px;">
        <div class="card">
            <h3><?php echo $patient['blood_group']; ?></h3>
            <p>Your Blood Group</p>
        </div>
        <div class="card">
            <h3><?php echo count($my_requests); ?></h3>
            <p>Your Requests</p>
        </div>
        <div class="card">
            <h3><?php echo count($matching_donors); ?></h3>
            <p>Donors Near You</p>
        </div>
    </div>

    <!-- Request Blood Form -->
    <div style="background:white; padding:25px; border-radius:12px; 
                box-shadow:0 2px 10px rgba(0,0,0,0.08); margin-bottom:30px;">
        <h2 style="color:#b22222; margin-bottom:20px;">Request Blood</h2>

        <?php if($error): ?>
            <div class="alert error"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if($success): ?>
            <div class="alert success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
            <div class="form-group">
                <label>Blood Group Needed</label>
                <select name="blood_group" required>
                    <option value="">-- Select --</option>
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
                <label>Units Needed</label>
                <input type="number" name="units_needed" min="1" max="10" placeholder="Enter units" required>
            </div>
            <div class="form-group">
                <label>Location / City</label>
                <input type="text" name="location" 
                       value="<?php echo $patient['location']; ?>" required>
            </div>
            <div class="form-group" style="display:flex; align-items:flex-end;">
                <button type="submit" name="request_blood" class="btn">Submit Request</button>
            </div>
        </form>
    </div>

    <!-- Matching Donors -->
    <h2 style="margin-bottom:15px; color:#b22222;">Available Donors Near You</h2>
    <table style="margin-bottom:30px;">
        <tr>
            <th>Donor Name</th>
            <th>Blood Group</th>
            <th>Location</th>
            <th>Phone</th>
        </tr>
        <?php if(count($matching_donors) > 0): ?>
            <?php foreach($matching_donors as $d): ?>
            <tr>
                <td><?php echo $d['name']; ?></td>
                <td><strong><?php echo $d['blood_group']; ?></strong></td>
                <td><?php echo $d['location']; ?></td>
                <td><?php echo $d['phone']; ?></td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" style="text-align:center; color:#888;">
                    No available donors in your area for your blood group
                </td>
            </tr>
        <?php endif; ?>
    </table>

    <!-- My Requests -->
    <h2 style="margin-bottom:15px; color:#b22222;">My Blood Requests</h2>
    <table>
        <tr>
            <th>Blood Group</th>
            <th>Units Needed</th>
            <th>Location</th>
            <th>Status</th>
            <th>Requested On</th>
        </tr>
        <?php if(count($my_requests) > 0): ?>
            <?php foreach($my_requests as $r): ?>
            <tr>
                <td><strong><?php echo $r['blood_group']; ?></strong></td>
                <td><?php echo $r['units_needed']; ?> units</td>
                <td><?php echo $r['location']; ?></td>
                <td><span class="badge <?php echo $r['status']; ?>"><?php echo ucfirst($r['status']); ?></span></td>
                <td><?php echo date('d M Y', strtotime($r['requested_at'])); ?></td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" style="text-align:center; color:#888;">No requests made yet</td>
            </tr>
        <?php endif; ?>
    </table>
</div>
<!-- FOOTER -->
<footer class="footer">
    <p>&copy; <?php echo date('Y'); ?> Blood Bank Management System. All Rights Reserved.</p>
</footer>
</body>
</html>