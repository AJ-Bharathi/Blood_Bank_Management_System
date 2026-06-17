<?php
require_once 'config.php';
require_once 'send_email.php';

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'donor') {
    header("Location: index.php");
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['request_id'])) {
    $request_id = $_POST['request_id'];

    // Get donor id
    $stmt = $pdo->prepare("SELECT id FROM donors WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $donor = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if already responded
    $stmt = $pdo->prepare("SELECT id FROM donor_responses 
                            WHERE request_id = ? AND donor_id = ?");
    $stmt->execute([$request_id, $donor['id']]);
    $already = $stmt->fetch();

    if(!$already) {
        // Save response
        $stmt = $pdo->prepare("INSERT INTO donor_responses (request_id, donor_id, response) 
                        VALUES (?, ?, 'accepted')");
        $stmt->execute([$request_id, $donor['id']]);

        // Update request as donor committed
        $stmt = $pdo->prepare("UPDATE blood_requests 
                        SET donor_committed = 1, 
                            committed_donor_name = ?
                        WHERE id = ?");
        $stmt->execute([$_SESSION['name'], $request_id]);

        // Get request and patient details to notify patient
        $stmt = $pdo->prepare("SELECT br.*, u.name as patient_name, u.email as patient_email 
                                FROM blood_requests br
                                JOIN patients p ON br.patient_id = p.id
                                JOIN users u ON p.user_id = u.id
                                WHERE br.id = ?");
        $stmt->execute([$request_id]);
        $request = $stmt->fetch(PDO::FETCH_ASSOC);

        // Send email to patient
        sendPatientNotification(
            $request['patient_email'],
            $request['patient_name'],
            $_SESSION['name'],
            $request['blood_group'],
            $request['units_needed'],
            $request['location']
        );
    }
}

header("Location: donor_dashboard.php");
exit();
?>