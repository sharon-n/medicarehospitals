<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../mpesa.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "edoc";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Capture form data and sanitize
$scheduleid = $conn->real_escape_string($_POST['scheduleid']);
$apponum = $conn->real_escape_string($_POST['apponum']);
$today = $conn->real_escape_string($_POST['date']);
$docname = $conn->real_escape_string($_POST['docname']);
$docemail = $conn->real_escape_string($_POST['docemail']);
$scheduledate = $conn->real_escape_string($_POST['scheduledate']);
$scheduletime = $conn->real_escape_string($_POST['scheduletime']);

// Assuming session data
session_start();
$useremail = $_SESSION["user"];
$userrow = $conn->query("SELECT * FROM patient WHERE pemail='$useremail'");
$userfetch = $userrow->fetch_assoc();
$userid = $userfetch["pid"];
$username = $userfetch["pname"];
$userphone = $userfetch["ptel"]; // User's phone number from the database

// Insert data into database
$sql = "INSERT INTO appointment (scheduleid, apponum, appodate, pid) VALUES ('$scheduleid', '$apponum', '$today', '$userid')";

if ($conn->query($sql) === TRUE) {
    // Send confirmation email
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'medicarehospitalskenya@gmail.com'; // Your Gmail address
        $mail->Password = 'uiof msdl mhnd mraf'; // Your Gmail App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients
        $mail->setFrom('medicarehospitalskenya@gmail.com', 'Medicare Hospital');
        $mail->addAddress($useremail);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Appointment Confirmation';
        $mail->Body    = "
            <p>Dear $username,</p>
            <p>Your appointment with Dr. $docname has been successfully booked.</p>
            <p>Date: $scheduledate</p>
            <p>Time: $scheduletime</p>
            <p>Appointment Number: $apponum</p>
            <p>Thank you for using our service.</p>
        ";

        $mail->send();
    } catch (Exception $e) {
        echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }

    // Redirect to payment page
    header("Location: payment.php?apponum=$apponum&amount=1500&scheduleid=$scheduleid");
    exit;
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
