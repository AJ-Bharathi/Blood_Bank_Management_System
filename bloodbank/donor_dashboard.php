<?php
require_once 'config.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'donor') {
    header("Location: index.php");
    exit();
}

// Get donor details
$stmt = $pdo->prepare("SELECT d.*, u.name, u.email FROM donors d 
                        JOIN users u ON d.user_id = u.id 
                        WHERE d.user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$donor = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle availability toggle
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['toggle_availability'])) {
    $new_status = $donor['is_available'] ? 0 : 1;
    $stmt = $pdo->prepare("UPDATE donors SET is_available = ? WHERE user_id = ?");
    $stmt->execute([$new_status, $_SESSION['user_id']]);
    header("Location: donor_dashboard.php");
    exit();
}

// Get blood requests matching donor's blood group and location
$stmt = $pdo->prepare("SELECT br.*, u.name as patient_name, p.phone as patient_phone 
                        FROM blood_requests br
                        JOIN patients p ON br.patient_id = p.id
                        JOIN users u ON p.user_id = u.id
                        WHERE br.blood_group = ? 
                        AND br.location = ?
                        AND br.status = 'pending'");
$stmt->execute([$donor['blood_group'], $donor['location']]);
$matching_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Donor Dashboard - Blood Bank Management</title>
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
        Blood Bank Management System — Donor</span>
    <span>Welcome, <?php echo $_SESSION['name']; ?> | <a href="logout.php">Logout</a></span>
</nav>

<div class="dashboard">
    <h1>Donor Dashboard</h1>

    <!-- Donor Profile Card -->
    <div class="cards" style="margin-bottom:30px;">
        <div class="card">
            <h3><?php echo $donor['blood_group']; ?></h3>
            <p>Your Blood Group</p>
        </div>
        <div class="card">
            <h3><?php echo $donor['location']; ?></h3>
            <p>Your Location</p>
        </div>
        <div class="card">
            <h3><?php echo count($matching_requests); ?></h3>
            <p>Requests Near You</p>
        </div>
    </div>

    <!-- Availability Toggle -->
    <div style="background:white; padding:20px; border-radius:12px; 
                box-shadow:0 2px 10px rgba(0,0,0,0.08); margin-bottom:30px;">
        <h2 style="color:#b22222; margin-bottom:10px;">Your Availability</h2>
        <p style="color:#666; margin-bottom:15px;">
            You are currently: 
            <span class="badge <?php echo $donor['is_available'] ? 'available' : 'unavailable'; ?>">
                <?php echo $donor['is_available'] ? 'Available to Donate' : 'Not Available'; ?>
            </span>
        </p>
        <form method="POST">
            <button type="submit" name="toggle_availability" class="btn" style="width:auto; padding:10px 25px;">
                <?php echo $donor['is_available'] ? 'Mark as Unavailable' : 'Mark as Available'; ?>
            </button>
        </form>
    </div>

    <!-- Matching Blood Requests -->
<h2 style="margin-bottom:15px; color:#b22222;">Blood Requests Matching Your Profile</h2>
<table>
    <tr>
        <th>Patient Name</th>
        <th>Blood Group</th>
        <th>Units Needed</th>
        <th>Location</th>
        <th>Contact</th>
        <th>Requested On</th>
        <th>Action</th>
    </tr>
    <?php if(count($matching_requests) > 0): ?>
        <?php foreach($matching_requests as $r): ?>
        <?php
            // Check if donor already responded
            $donor_id_check = $pdo->prepare("SELECT id FROM donors WHERE user_id = ?");
            $donor_id_check->execute([$_SESSION['user_id']]);
            $d = $donor_id_check->fetch(PDO::FETCH_ASSOC);

            $response_check = $pdo->prepare("SELECT id FROM donor_responses 
                                             WHERE request_id = ? AND donor_id = ?");
            $response_check->execute([$r['id'], $d['id']]);
            $already_responded = $response_check->fetch();
        ?>
        <tr>
            <td><?php echo $r['patient_name']; ?></td>
            <td><strong><?php echo $r['blood_group']; ?></strong></td>
            <td><?php echo $r['units_needed']; ?> units</td>
            <td><?php echo $r['location']; ?></td>
            <td><?php echo $r['patient_phone']; ?></td>
            <td><?php echo date('d M Y', strtotime($r['requested_at'])); ?></td>
            <td>
                <?php if($already_responded): ?>
                    <span class="badge available">✅ Responded</span>
                <?php else: ?>
                    <form method="POST" action="donor_respond.php">
                        <input type="hidden" name="request_id" value="<?php echo $r['id']; ?>">
                        <button type="submit" class="btn" 
                                style="width:auto; padding:6px 14px; font-size:12px;">
                            🩸 I'll Donate
                        </button>
                    </form>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="7" style="text-align:center; color:#888;">
                No pending requests matching your blood group and location
            </td>
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