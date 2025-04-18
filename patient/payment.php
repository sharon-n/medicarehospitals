<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        input[type="text"], input[type="submit"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="submit"] {
            background-color: #28a745;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #218838;
        }
        label {
            font-size: 16px;
            margin-bottom: 5px;
            display: block;
        }
        .error-message {
            color: red;
            font-size: 14px;
            display: none;
        }
    </style>
    <script>
        function validatePhoneNumber() {
            var phoneNumber = document.getElementById("phone_number").value;
            var phoneRegex = /^2547\d{8}$/;
            var errorMessage = document.getElementById("error-message");

            if (!phoneRegex.test(phoneNumber)) {
                errorMessage.style.display = "block";
                return false;
            } else {
                errorMessage.style.display = "none";
                return true;
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Enter Your Phone Number</h2>
        <form method="post" action="process-phone-number.php" onsubmit="return validatePhoneNumber();">
            <input type="hidden" name="amount" value="<?php echo isset($_GET['amount']) ? htmlspecialchars($_GET['amount']) : ''; ?>">
            <input type="hidden" name="account_reference" value="<?php echo isset($_GET['apponum']) ? htmlspecialchars($_GET['apponum']) : ''; ?>">
            <input type="hidden" name="scheduleid" value="<?php echo isset($_GET['scheduleid']) ? htmlspecialchars($_GET['scheduleid']) : ''; ?>">
            <label for="phone_number">Phone Number:</label>
            <input type="text" id="phone_number" name="phone_number" placeholder="2547xxxxxxxx" required>
            <span id="error-message" class="error-message">Phone number must be in the format 2547xxxxxxxx.</span>
            <input type="submit" value="Submit">
        </form>
    </div>
</body>
</html>

