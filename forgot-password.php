<?php
ob_start();
require_once 'includes/config.php';
require_once 'includes/auth_functions.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    if (empty($email)) {
        $error = "Email is required.";
    } else {
        $result = initiatePasswordReset($email);

        if ($result && isset($result['status']) && $result['status'] === 'ok') {
            $_SESSION['reset_user_id'] = $result['user_id'];
            $_SESSION['reset_email'] = $email;
            header("Location: verify-reset-otp.php");
            exit;
        } elseif ($result && isset($result['status']) && $result['status'] === 'mail_failed') {
            $error = "We found your account but failed to send the reset code. Please try again later.";
        } else {
            $error = "No account found with that email.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="col-md-5 col-lg-4">
            <div class="card p-4">
                <div class="card-body">
                    <h4 class="text-center mb-4 fw-bold">Reset Password</h4>
                    <p class="text-center small opacity-75 mb-4">Enter your email address to receive a reset code.</p>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger py-2 mb-4"><small><?php echo $error; ?></small></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-4">
                            <label class="form-label small text-uppercase fw-bold opacity-75">Email Address</label>
                            <input type="email" name="email" class="form-control form-control-lg" required autofocus>
                        </div>
                        <div class="d-grid mb-4">
                            <button type="submit" class="btn btn-primary btn-lg">Send Reset Code</button>
                        </div>
                    </form>
                    
                    <div class="text-center">
                        <a href="login.php" class="text-info text-decoration-none small">Back to Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
