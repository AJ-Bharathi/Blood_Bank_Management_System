<?php
require_once 'config.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $blood_group = $_POST['blood_group'];
    $units = (int)$_POST['units'];
    
    $stmt = $pdo->prepare("UPDATE blood_stock SET units_available = units_available + ? 
                            WHERE blood_group = ?");
    $stmt->execute([$units, $blood_group]);
}

header("Location: admin_dashboard.php");
exit();
?>