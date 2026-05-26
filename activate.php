<?php
include 'db_connect.php';

$message = '';
$error = '';

if (isset($_GET['token'])) {
    $token = mysqli_real_escape_string($conn, $_GET['token']);
    
    $stmt = $conn->prepare("SELECT id, is_active FROM users WHERE verification_token = ? LIMIT 1");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if ($user['is_active'] == 1) {
            $message = "Your account is already activated. You can login now.";
        } else {
            // Activate user and clear token
            $update = $conn->query("UPDATE users SET is_active = 1, verification_token = NULL WHERE id = " . $user['id']);
            if ($update) {
                $message = "Your account has been successfully activated! You can now login.";
            } else {
                $error = "Failed to activate account. Please try again.";
            }
        }
    } else {
        $error = "Invalid or expired activation token.";
    }
} else {
    $error = "No activation token provided.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Activation | ICT with Dilhara</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0066FF;
            --secondary: #7C3AED;
            --dark: #0F172A;
            --light: #F8FAFC;
        }
        body { font-family: 'Outfit', sans-serif; background: var(--light); display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; padding: 2rem; box-sizing: border-box; }
        .card { background: white; padding: 3rem; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); text-align: center; max-width: 500px; width: 100%; }
        h1 { margin-top: 0; color: var(--dark); font-weight: 800; }
        .alert { padding: 1.5rem; border-radius: 8px; margin: 1.5rem 0; font-size: 1.1rem; }
        .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .alert-error { background: #fee2e2; color: #ef4444; border: 1px solid #fecaca; }
        .btn { display: inline-block; background: var(--primary); color: white; padding: 0.8rem 2rem; border-radius: 8px; text-decoration: none; font-weight: 600; margin-top: 1rem; transition: background 0.2s; }
        .btn:hover { background: #0052cc; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Account Activation</h1>
        
        <?php if($message): ?>
            <div class="alert alert-success">
                ✅ <?php echo $message; ?>
            </div>
            <a href="login.php" class="btn">Go to Login</a>
        <?php endif; ?>

        <?php if($error): ?>
            <div class="alert alert-error">
                ❌ <?php echo $error; ?>
            </div>
            <a href="index.php" class="btn" style="background:var(--secondary);">Back to Home</a>
        <?php endif; ?>
    </div>
</body>
</html>
