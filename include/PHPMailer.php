<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Path to PHPMailer autoload.php

// Capture appointment details (example)
$appointmentDate = $_POST['appointment_date'];
$doctorName = $_POST['doctor_name'];
$userEmail = $_POST['user_email'];

// Construct email content
$emailSubject = 'Appointment Confirmation';
$emailBody = "
    <p>Dear User,</p>
    <p>Your appointment with Dr. $doctorName has been successfully booked.</p>
    <p>Date: $appointmentDate</p>
    <p>Thank you for using our service.</p>
";

// Send email
$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.example.com'; // Specify main and backup SMTP servers
    $mail->SMTPAuth = true;
    $mail->Username = 'your_email@example.com'; // SMTP username
    $mail->Password = 'your_password'; // SMTP password
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    //Recipients
    $mail->setFrom('your_email@example.com', 'Your Name');
    $mail->addAddress($userEmail); // Add a recipient

    //Content
    $mail->isHTML(true);
    $mail->Subject = $emailSubject;
    $mail->Body    = $emailBody;

    $mail->send();
    echo 'Email has been sent';
} catch (Exception $e) {
    echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
?>
