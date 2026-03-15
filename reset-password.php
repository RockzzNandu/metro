<?php
ob_start();
require_once 'includes/config.php';
require_once 'includes/auth_functions.php';

$error = '';
$success = '';

if (!isset($_SESSION['otp_verified']) || !isset($_SESSION['reset_user_id'])) {
    header("Location: forgot-password.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (empty($new_password) || empty($confirm_password)) {
        $error = "Both password fields are required.";
    } elseif ($new_password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($new_password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        if (resetPassword($_SESSION['reset_user_id'], $new_password)) {
            unset($_SESSION['reset_user_id']);
            unset($_SESSION['reset_email']);
            unset($_SESSION['otp_verified']);
            $success = "Password reset successful! You can now login.";
            header("Location: login.php?msg=reset_success");
            exit;
        } else {
            $error = "Failed to reset password. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="col-md-5 col-lg-4">
            <div class="card p-4">
                <div class="card-body">
                    <h4 class="text-center mb-4 fw-bold">Set New Password</h4>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger py-2 mb-4"><small><?php echo $error; ?></small></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-4">
                            <label class="form-label small text-uppercase fw-bold opacity-75">New Password</label>
                            <input type="password" name="new_password" class="form-control form-control-lg" required autofocus>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small text-uppercase fw-bold opacity-75">Confirm Password</label>
                            <input type="password" name="confirm_password" class="form-control form-control-lg" required>
                        </div>
                        <div class="d-grid mb-4">
                            <button type="submit" class="btn btn-primary btn-lg">Update Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
