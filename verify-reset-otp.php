<?php
ob_start();
require_once 'includes/config.php';
require_once 'includes/auth_functions.php';

$error = '';
$success = '';
$debug_otp = '';

if (!isset($_SESSION['reset_user_id'])) {
    header("Location: forgot-password.php");
    exit;
}

// In local development (localhost), expose the current reset OTP to help testing
if (strpos(SITE_URL, 'localhost') !== false && isset($_SESSION['reset_user_id'])) {
    $stmt = $pdo->prepare("SELECT otp_code FROM users WHERE id = :id");
    $stmt->execute(['id' => $_SESSION['reset_user_id']]);
    $row = $stmt->fetch();
    if ($row && !empty($row['otp_code'])) {
        $debug_otp = $row['otp_code'];
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $otp = $_POST['otp'];
    if (verifyOTP($_SESSION['reset_user_id'], $otp)) {
        $_SESSION['otp_verified'] = true;
        header("Location: reset-password.php");
        exit;
    } else {
        $error = "Invalid or expired reset code.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Reset Code - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="col-md-5 col-lg-4 text-center">
            <div class="card p-4">
                <div class="card-body">
                    <h4 class="text-center mb-4 fw-bold">Verify Identity</h4>
                    <p class="small opacity-75">A 6-digit reset code has been sent to your email.</p>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger py-2 mb-4"><small><?php echo $error; ?></small></div>
                    <?php endif; ?>

                    <?php if (!empty($debug_otp)): ?>
                        <div class="alert alert-warning py-2 mb-3">
                            <small><strong>Dev only (localhost):</strong> current reset code is <?php echo htmlspecialchars($debug_otp, ENT_QUOTES, 'UTF-8'); ?></small>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['resend_msg'])): ?>
                        <div class="alert alert-<?php echo $_SESSION['resend_type']; ?> py-2 mb-4">
                            <small><?php echo $_SESSION['resend_msg']; ?></small>
                        </div>
                        <?php unset($_SESSION['resend_msg'], $_SESSION['resend_type']); ?>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-4">
                            <input type="text" name="otp" class="form-control otp-input" maxlength="6" placeholder="000000" required autofocus>
                        </div>
                        <div class="d-grid mb-4">
                            <button type="submit" class="btn btn-primary btn-lg">Verify Code</button>
                        </div>
                    </form>
                    
                    <div class="text-center">
                        <a href="resend-otp.php" class="text-info text-decoration-none small">Resend Code</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
