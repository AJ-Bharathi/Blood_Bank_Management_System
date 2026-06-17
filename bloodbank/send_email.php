<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once 'PHPMailer/PHPMailer.php';
require_once 'PHPMailer/SMTP.php';
require_once 'PHPMailer/Exception.php';

function sendDonorNotification($donor_email, $donor_name, $patient_name, $blood_group, $units_needed, $location) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'ajbharathi2005@gmail.com'; // Replace with your Gmail
        $mail->Password   = 'bmzj ujyk xbne bhub';     // Replace with your App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('your_gmail@gmail.com', 'Blood Bank System');
        $mail->addAddress($donor_email, $donor_name);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = '🩸 Urgent Blood Donation Request - ' . $blood_group;
        $mail->Body    = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: #c0392b; padding: 20px; text-align: center;'>
                <h1 style='color: white; margin: 0;'>🩸 Blood Bank System</h1>
            </div>
            <div style='padding: 30px; background: #f9f9f9;'>
                <h2 style='color: #c0392b;'>Urgent Blood Donation Needed!</h2>
                <p>Dear <strong>{$donor_name}</strong>,</p>
                <p>A patient urgently needs blood. You are a matching donor!</p>
                
                <div style='background: white; padding: 20px; border-radius: 8px; 
                            border-left: 4px solid #c0392b; margin: 20px 0;'>
                    <p><strong>Patient Name:</strong> {$patient_name}</p>
                    <p><strong>Blood Group Needed:</strong> {$blood_group}</p>
                    <p><strong>Units Needed:</strong> {$units_needed}</p>
                    <p><strong>Location:</strong> {$location}</p>
                </div>

                <p>Please login to the Blood Bank System to respond to this request.</p>
                <p style='color: #c0392b;'><strong>Your donation can save a life!</strong></p>
            </div>
            <div style='background: #333; padding: 15px; text-align: center;'>
                <p style='color: #aaa; margin: 0; font-size: 12px;'>
                    Blood Bank Management System
                </p>
            </div>
        </div>
        ";

        $mail->send();
        return true;

    } catch (Exception $e) {
        return false;
    }
}

function sendPatientNotification($patient_email, $patient_name, $donor_name, $blood_group, $units_needed, $location) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'your_gmail@gmail.com'; // Replace with your Gmail
        $mail->Password   = 'your_app_password';     // Replace with your App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('your_gmail@gmail.com', 'Blood Bank System');
        $mail->addAddress($patient_email, $patient_name);

        $mail->isHTML(true);
        $mail->Subject = '✅ Good News! A Donor Has Responded to Your Request';
        $mail->Body    = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: #27ae60; padding: 20px; text-align: center;'>
                <h1 style='color: white; margin: 0;'>🩸 Blood Bank System</h1>
            </div>
            <div style='padding: 30px; background: #f9f9f9;'>
                <h2 style='color: #27ae60;'>A Donor Has Accepted Your Request!</h2>
                <p>Dear <strong>{$patient_name}</strong>,</p>
                <p>Great news! A donor is willing to help you.</p>
                
                <div style='background: white; padding: 20px; border-radius: 8px; 
                            border-left: 4px solid #27ae60; margin: 20px 0;'>
                    <p><strong>Donor Name:</strong> {$donor_name}</p>
                    <p><strong>Blood Group:</strong> {$blood_group}</p>
                    <p><strong>Units:</strong> {$units_needed}</p>
                    <p><strong>Location:</strong> {$location}</p>
                </div>

                <p>Please login to Blood Bank System to see donor contact details.</p>
                <p style='color: #27ae60;'><strong>Help is on the way!</strong></p>
            </div>
            <div style='background: #333; padding: 15px; text-align: center;'>
                <p style='color: #aaa; margin: 0; font-size: 12px;'>
                    Blood Bank Management System
                </p>
            </div>
        </div>
        ";

        $mail->send();
        return true;

    } catch (Exception $e) {
        return false;
    }
}
?>