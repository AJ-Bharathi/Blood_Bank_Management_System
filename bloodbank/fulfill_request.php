<?php
require_once 'config.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $request_id = $_POST['request_id'];

    // Get request details
    $stmt = $pdo->prepare("SELECT * FROM blood_requests WHERE id = ?");
    $stmt->execute([$request_id]);
    $request = $stmt->fetch(PDO::FETCH_ASSOC);

    if($request && $request['status'] == 'pending') {
        // Deduct from stock
        $stmt = $pdo->prepare("UPDATE blood_stock SET units_available = units_available - ? 
                                WHERE blood_group = ?");
        $stmt->execute([$request['units_needed'], $request['blood_group']]);

        // Mark request as fulfilled
        $stmt = $pdo->prepare("UPDATE blood_requests SET status = 'fulfilled' WHERE id = ?");
        $stmt->execute([$request_id]);
    }
}

header("Location: admin_dashboard.php");
exit();
?>