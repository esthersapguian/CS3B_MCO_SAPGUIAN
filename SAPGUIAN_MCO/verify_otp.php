<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require 'config.php';

$email = '';
$otp = '';
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $otp = trim($_POST['otp']);

    $check_stmt = $conn->prepare("SELECT otp_code, otp_expiry FROM users WHERE email = ?");
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $stored_otp = $row['otp_code'];
        $otp_expiry = $row['otp_expiry'];

        if ($otp === $stored_otp) {
            if (strtotime($otp_expiry) >= time()) {
                $update_stmt = $conn->prepare("UPDATE users SET is_verified = 1 WHERE email = ?");
                $update_stmt->bind_param("s", $email);
                if ($update_stmt->execute()) {
                    $message = "Verification complete.";
                    // header("Location: login.php"); // uncomment when ready
                } else {
                    $message = "An error occurred while updating verification.";
                }
            } else {
                $message = "Expired OTP. Kindly try again with a new code.";
            }
        } else {
            $message = "Invalid OTP entered. Try again.";
        }
    } else {
        $message = "Unable to find an OTP for this email. Request a new one to proceed.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>OTP Verification Result</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }

        body {
            height: 100vh;
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .message-container {
            background: white;
            padding: 2rem 3rem;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 600px;
            width: 100%;
        }

        .message-container h2 {
            margin-bottom: 1rem;
            font-size: 1.5rem;
            color: #333;
        }

        .message-container p {
            font-size: 1.1rem;
            color: #555;
            margin-bottom: 1rem;
        }

        .message-container .success {
            color: green;
        }

        .message-container .error {
            color: red;
        }

        .message-container .info {
            color:rgb(26, 221, 75);
        }
    </style>
</head>
<body>
    <div class="message-container">
        <h2>OTP Verification</h2>
        <p class="<?php
            if (strpos($message, '✅') !== false) echo 'success';
            elseif (strpos($message, '❌') !== false) echo 'error';
            else echo 'info';
        ?>">
            <?php echo htmlspecialchars($message); ?>
        </p>

 
           
    </div>
</body>
</html>
