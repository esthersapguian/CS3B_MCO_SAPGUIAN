<?php
require 'config.php';

// Pre-fill email if coming from send_otp.php
$email = isset($_GET['email']) ? trim($_GET['email']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
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

        .form-container {
            background: white;
            padding: 2rem 3rem;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 600px;
            width: 100%;
        }

        .form-container h2 {
            margin-bottom: 1.5rem;
            font-size: 1.75rem;
            color: #333;
        }

        .form-container input[type="email"],
        .form-container input[type="text"] {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ccc;
            border-radius: 8px;
            margin-bottom: 1rem;
            font-size: 1rem;
        }

        .form-container input[readonly] {
            background-color: #f5f5f5;
        }

        .form-container button {
            background-color: #4a90e2;
            color: white;
            padding: 0.75rem 7rem;
            font-size: 1rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .form-container button:hover {
            background-color: #357ab8;
        }



    </style>
</head>
<body>
    <div class="form-container">
        <h2>Verify Your OTP</h2>
     <form action="verify_otp.php" method="POST">
        <input 
            type="email" 
            name="email" 
            value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" 
            required 
            placeholder="Enter your email"><br>
        <input type="text" name="otp" placeholder="Enter 6-digit OTP" required><br>
        <button type="submit">Verify</button>
     </form>
    </div>
</body>
</html>
