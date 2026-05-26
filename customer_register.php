<?php
include 'db_connect.php';

$message = '';
$error = '';

$full_name = '';
$email = '';
$phone = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        $check = $conn->query("SELECT id FROM users WHERE email = '$email'");
        if ($check->num_rows > 0) {
            $error = "Email already registered!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $token = bin2hex(random_bytes(16));
            
            $sql = "INSERT INTO users (full_name, email, phone, password, role, is_active, verification_token) 
                    VALUES ('$full_name', '$email', '$phone', '$hashed_password', 'customer', 0, '$token')";
            
            if ($conn->query($sql) === TRUE) {
                // Send email
                // Note: $_SERVER['HTTP_HOST'] gets the host name.
                // We construct the full URL to activate.php
                $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
                $base_url = $protocol . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
                $base_url = rtrim($base_url, '/'); // Remove trailing slash if any
                $activation_link = $base_url . "/activate.php?token=" . $token;
                
                $subject = "Activate your Customer Account - ICT with Dilhara Academy";
                $body = "Hi $full_name,\n\nWelcome to our store!\n\nPlease click the link below to activate your customer account and start shopping:\n$activation_link\n\nThanks,\nICT with Dilhara Academy Team";
                $headers = "From: noreply@" . $_SERVER['HTTP_HOST'] . "\r\n" .
                           "Reply-To: noreply@" . $_SERVER['HTTP_HOST'] . "\r\n" .
                           "X-Mailer: PHP/" . phpversion();

                // Suppress warning if mail is not configured locally by using @
                if(@mail($email, $subject, $body, $headers)) {
                    $message = "Registration successful! Please check your email to activate your account.";
                } else {
                    // Fallback for local development environments where mail() isn't configured
                    $message = "Registration successful! <br><br><span style='color:var(--gray);font-size:0.9rem;'>(Note: Email server not configured. <a href='$activation_link' style='color:var(--primary);'>Click here to manually activate</a>)</span>";
                }
            } else {
                $error = "Error: " . $conn->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Registration | ICT with Dilhara</title>
    <link rel="icon" type="image/png" href="assest/logo/logo1.png">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0066FF;
            --secondary: #7C3AED;
            --dark: #0F172A;
            --light: #F8FAFC;
            --gray: #64748B;
        }
        * { box-sizing: border-box; }
        body { font-family: 'Outfit', sans-serif; background: var(--light); display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 2rem; margin: 0; }
        .card { background: white; padding: 2.5rem; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); width: 100%; max-width: 500px; }
        .logo { text-align: center; margin-bottom: 2rem; }
        .logo img { height: 50px; }
        h2 { text-align: center; color: var(--dark); margin-bottom: 0.5rem; font-weight: 800; }
        p.subtitle { text-align: center; color: var(--gray); margin-bottom: 2rem; }
        .form-group { margin-bottom: 1.2rem; }
        label { display: block; margin-bottom: 0.5rem; color: var(--dark); font-weight: 600; }
        input { width: 100%; padding: 0.8rem 1rem; border: 1px solid #e2e8f0; border-radius: 8px; font-family: inherit; font-size: 1rem; transition: border-color 0.3s; }
        input:focus { outline: none; border-color: var(--primary); }
        .btn { background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white; padding: 1rem; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; width: 100%; font-size: 1.1rem; transition: transform 0.2s; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 4px 10px rgba(0,102,255,0.2); }
        .alert { padding: 1rem; border-radius: 8px; margin-bottom: 2rem; text-align: center; }
        .alert-success { background: #dcfce7; color: #166534; }
        .alert-error { background: #fee2e2; color: #ef4444; }
        .links { text-align: center; margin-top: 1.5rem; color: var(--gray); font-size: 0.95rem; }
        .links a { color: var(--primary); text-decoration: none; font-weight: 600; }
        .links a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo">
            <img src="assest/logo/logo1.png" alt="Logo">
        </div>
        <h2>Create Account</h2>
        <p class="subtitle">Join as a customer to purchase items.</p>
        
        <?php if($message): ?>
            <div class="alert alert-success"><?php echo $message; ?></div>
        <?php else: ?>
            <?php if($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" value="<?php echo htmlspecialchars($full_name); ?>" required>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                </div>
                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="tel" name="phone" value="<?php echo htmlspecialchars($phone); ?>" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" required>
                </div>
                
                <button type="submit" class="btn">Register</button>
            </form>
        <?php endif; ?>
        
        <div class="links">
            Already have an account? <a href="login.php">Login here</a><br><br>
            <a href="index.php">← Back to Home</a>
        </div>
    </div>
</body>
</html>
