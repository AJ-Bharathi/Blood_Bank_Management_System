<?php
require_once 'config.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Fetch blood stock
$stock = $pdo->query("SELECT * FROM blood_stock ORDER BY blood_group")->fetchAll();

// Fetch all donors
$donors = $pdo->query("SELECT u.name, u.email, d.blood_group, d.phone, d.location, d.is_available 
                        FROM donors d JOIN users u ON d.user_id = u.id")->fetchAll();

// Fetch all requests
$requests = $pdo->query("SELECT br.id, u.name as patient_name, br.blood_group, br.units_needed, 
                          br.location, br.status, br.requested_at,
                          br.donor_committed, br.committed_donor_name
                          FROM blood_requests br 
                          JOIN patients p ON br.patient_id = p.id 
                          JOIN users u ON p.user_id = u.id 
                          ORDER BY br.requested_at DESC")->fetchAll();

// Count stats
$total_donors = count($donors);
$total_requests = count($requests);
$pending_requests = $pdo->query("SELECT COUNT(*) FROM blood_requests WHERE status='pending'")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Blood Bank</title>
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
        Blood Bank Management System — Admin
    </span>
    <span>Welcome, <?php echo $_SESSION['name']; ?> | <a href="logout.php">Logout</a></span>
</nav>

<div class="dashboard">
    <h1>Admin Dashboard</h1>

    <!-- Stats Cards -->
    <div class="cards">
        <div class="card">
            <h3><?php echo $total_donors; ?></h3>
            <p>Total Donors</p>
        </div>
        <div class="card">
            <h3><?php echo $total_requests; ?></h3>
            <p>Total Requests</p>
        </div>
        <div class="card">
            <h3><?php echo $pending_requests; ?></h3>
            <p>Pending Requests</p>
        </div>
    </div>

    <!-- Blood Stock Table -->
    <h2 style="margin-bottom:15px; color:#b22222;">Blood Stock</h2>
    <table style="margin-bottom:30px;">
        <tr>
            <th>Blood Group</th>
            <th>Units Available</th>
            <th>Last Updated</th>
            <th>Action</th>
        </tr>
        <?php foreach($stock as $s): ?>
        <tr>
            <td><strong><?php echo $s['blood_group']; ?></strong></td>
            <td><?php echo $s['units_available']; ?> units</td>
            <td><?php echo date('d M Y', strtotime($s['last_updated'])); ?></td>
            <td>
                <form method="POST" action="update_stock.php" style="display:flex; gap:8px;">
                    <input type="hidden" name="blood_group" value="<?php echo $s['blood_group']; ?>">
                    <input type="number" name="units" placeholder="Add units" 
                           style="width:100px; padding:5px 8px; border:1px solid #ddd; border-radius:6px;">
                    <button type="submit" class="btn" style="width:auto; padding:5px 12px; font-size:12px;">Update</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <!-- Donors Table -->
    <h2 style="margin-bottom:15px; color:#b22222;">Registered Donors</h2>
    <table style="margin-bottom:30px;">
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Blood Group</th>
            <th>Phone</th>
            <th>Location</th>
            <th>Status</th>
        </tr>
        <?php if(count($donors) > 0): ?>
            <?php foreach($donors as $d): ?>
            <tr>
                <td><?php echo $d['name']; ?></td>
                <td><?php echo $d['email']; ?></td>
                <td><strong><?php echo $d['blood_group']; ?></strong></td>
                <td><?php echo $d['phone']; ?></td>
                <td><?php echo $d['location']; ?></td>
                <td>
                    <span class="badge <?php echo $d['is_available'] ? 'available' : 'unavailable'; ?>">
                        <?php echo $d['is_available'] ? 'Available' : 'Unavailable'; ?>
                    </span>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="6" style="text-align:center; color:#888;">No donors registered yet</td></tr>
        <?php endif; ?>
    </table>

    <!-- Requests Table -->
    <h2 style="margin-bottom:15px; color:#b22222;">Blood Requests</h2>
    <table>
        <tr>
            <th>Patient</th>
            <th>Blood Group</th>
            <th>Units Needed</th>
            <th>Location</th>
            <th>Donor Committed</th>
            <th>Status</th>
            <th>Requested On</th>
            <th>Action</th>
        </tr>
        <?php if(count($requests) > 0): ?>
            <?php foreach($requests as $r): ?>
            <tr>
                <td><?php echo $r['patient_name']; ?></td>
                <td><strong><?php echo $r['blood_group']; ?></strong></td>
                <td><?php echo $r['units_needed']; ?> units</td>
                <td><?php echo $r['location']; ?></td>
                <td>
                    <?php if($r['donor_committed']): ?>
                    <span class="badge available">✅ <?php echo $r['committed_donor_name']; ?></span>
                    <?php else: ?>
                    <span class="badge unavailable">⏳ Waiting</span>
                <?php endif; ?>
                </td>
                <td><span class="badge <?php echo $r['status']; ?>"><?php echo ucfirst($r['status']); ?></span></td>
                <td><?php echo date('d M Y', strtotime($r['requested_at'])); ?></td>
                <td>
                    <?php if($r['status'] == 'pending'): ?>
                    <form method="POST" action="fulfill_request.php">
                        <input type="hidden" name="request_id" value="<?php echo $r['id']; ?>">
                        <button type="submit" class="btn" 
                                style="width:auto; padding:5px 12px; font-size:12px;">Fulfill</button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="7" style="text-align:center; color:#888;">No requests yet</td></tr>
        <?php endif; ?>
    </table>
</div>
<!-- FOOTER -->
<footer class="footer">
    <p>&copy; <?php echo date('Y'); ?> Blood Bank Management System. All Rights Reserved.</p>
</footer>
</body>
</html>