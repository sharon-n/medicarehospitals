<?php
require __DIR__ . '/../mpesa.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $phone_number = $_POST['phone_number'];
    $amount = $_POST['amount'];
    $account_reference = $_POST['account_reference'];
    
    // Assuming these are stored securely
    $response = sendMpesaSTKPush($phone_number, $amount, $account_reference);
    
    error_log("M-Pesa Response: " . print_r($response, true));
    
    if (isset($response['ResponseCode']) && $response['ResponseCode'] == '0') {
        echo "Payment request sent successfully. Please complete the payment on your phone.";
    } else {
        echo "Failed to initiate payment. Please try again.";
        if (isset($response['errorMessage'])) {
            echo "<br>Error Message: " . $response['errorMessage'];
        }
    }
} else {
    echo "Invalid request.";
}
?>

