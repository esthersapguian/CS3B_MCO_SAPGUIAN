<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Import PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Require PHPMailer files
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'config.php';

// Set the timezone if needed

date_default_timezone_set('Asia/Manila');

$message = '';

// Define the function BEFORE calling it
function sendOtpEmail($email, $otp) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'esthersapguian@gmail.com';
        $mail->Password   = 'sukb tqrs ktjg ldqm';         
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('esthersapguian@gmail.com', 'OTP Verification');
        $mail->addAddress($email);  

        $mail->isHTML(true); // Enable HTML email
        $mail->Subject = "Your OTP Code";
        $mail->Body = "
            <h3>Your OTP Code: <strong>$otp</strong></h3>
            <p>Be reminded,OTP will expire within 5 minutes.</p>
            <p><a href='http://localhost/SAPGUIAN_MCO/verify.php?email=$email'>Click here to verify</a></p>
        ";

        $mail->send();
        return "📤 OTP email sent successfully to <strong>$email</strong>.";
    } catch (Exception $e) {
        return "❌ Mailer Error: " . $mail->ErrorInfo;
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $otp = rand(100000, 999999); // Generate 6-digit OTP - the OTP is generated from here
    $otp_expiry = date("Y-m-d H:i:s", time() + 10 * 60); // Correct expiration time calculation

  
    $clear_sql = "DELETE FROM users WHERE email = ?";
    $clear_stmt = $conn->prepare($clear_sql);
    $clear_stmt->bind_param("s", $email);
    $clear_stmt->execute();

    $sql = "INSERT INTO users (email, otp_code, otp_expiry) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $email, $otp, $otp_expiry);

    if ($stmt->execute()) {
        $message = "✅ OTP generated: <strong>$otp</strong> (expires at $otp_expiry)<br>";        
        $message .= sendOtpEmail($email, $otp); // this calls the function above to execute the sending of OTP to target recipient

        // Optionally redirect
        // header("Location: verify.php?email=" . urlencode($email));
        // exit();
    } else {
        $message = "❌ Database error: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Send OTP</title>
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
            margin-bottom: 0.75rem;
        }

        .success {
            color: green;
        }

        .error {
            color: red;
        }

        .info {
            color: #ff9800;
        }

        a {
            color: #4a90e2;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="message-container">
        <h2>OTP Process Result</h2>
        <p class="<?php
            if (strpos($message, '✅') !== false || strpos($message, '📤') !== false) echo 'success';
            elseif (strpos($message, '❌') !== false) echo 'error';
            else echo 'info';
        ?>">


            <?php echo $message ?: "No request processed."; ?>
        </p>

        <div class="already-have-code">
                <p>
                    Already received your code?
                    <a href="verify.php" id="already-have-code">Proceed here.</a>
                </p>
        </div>
    </div>
</body>
</html>
