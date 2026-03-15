<?php
ob_start();
require_once 'includes/config.php';
require_once 'includes/auth_functions.php';

$error = '';
$success = '';
$debug_otp = '';

if (!isset($_SESSION['temp_user_id'])) {
    header("Location: register.php");
    exit;
}

// In local development (localhost), expose the current OTP to help testing
if (strpos(SITE_URL, 'localhost') !== false && isset($_SESSION['temp_user_id'])) {
    $stmt = $pdo->prepare("SELECT otp_code FROM users WHERE id = :id");
    $stmt->execute(['id' => $_SESSION['temp_user_id']]);
    $row = $stmt->fetch();
    if ($row && !empty($row['otp_code'])) {
        $debug_otp = $row['otp_code'];
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $otp = $_POST['otp'];
    $user_id = $_SESSION['temp_user_id'];
    
    if (verifyOTP($user_id, $otp)) {
        // Fetch user data to set session
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $user_id]);
        $user = $stmt->fetch();
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        
        unset($_SESSION['temp_user_id']);
        unset($_SESSION['temp_otp']);
        
        if ($user['role'] == 'admin') {
            header("Location: admin/dashboard.php");
        } else {
            header("Location: user/dashboard.php");
        }
        exit;
    } else {
        $error = 'Invalid or expired OTP.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Access - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .otp-input {
            letter-spacing: 15px;
            font-size: 2rem;
            text-align: center;
            font-weight: 800;
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid var(--glass-border);
            color: var(--accent-cyan);
        }
    </style>
</head>
<body>
    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="col-md-5 col-lg-4">
            <div class="text-center mb-5">
                <h1 class="navbar-brand d-block mb-2" style="font-size: 2.5rem;">METRO EXPRESS</h1>
                <p class="text-muted">Security Verification Required</p>
            </div>
            
            <div class="card p-4 text-center">
                <div class="card-body">
                    <div class="mb-4">
                        <div class="display-6 mb-2 text-info"><i class="bi bi-shield-lock"></i></div>
                        <h4 class="fw-bold">Confirm Identity</h4>
                        <p class="small opacity-75">We've sent a 6-digit access code to your registered Gmail account.</p>
                    </div>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger py-2 mb-4" style="background: rgba(220, 53, 69, 0.1); border: 1px solid rgba(220, 53, 69, 0.2); color: #ff6b6b;">
                            <small><?php echo $error; ?></small>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['resend_msg'])): ?>
                        <div class="alert alert-<?php echo $_SESSION['resend_type']; ?> py-2 mb-4" style="background: rgba(0, 242, 255, 0.1); border: 1px solid rgba(0, 242, 255, 0.2); color: var(--accent-cyan);">
                            <small><?php echo $_SESSION['resend_msg']; ?></small>
                        </div>
                        <?php unset($_SESSION['resend_msg'], $_SESSION['resend_type']); ?>
                    <?php endif; ?>

                    <?php if (!empty($debug_otp)): ?>
                        <div class="alert alert-warning py-2 mb-3">
                            <small><strong>Dev only (localhost):</strong> current OTP is <?php echo htmlspecialchars($debug_otp, ENT_QUOTES, 'UTF-8'); ?></small>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="verify-otp.php">
                        <div class="mb-4">
                            <input type="text" name="otp" id="otp" class="form-control otp-input" maxlength="6" placeholder="000000" required autofocus>
                        </div>
                        <div class="d-grid mb-4">
                            <button type="submit" class="btn btn-primary btn-lg">Verify & Access</button>
                        </div>
                    </form>
                    
                    <div class="text-center">
                        <p class="mb-0 small opacity-75">Didn't receive code? <a href="resend-otp.php" class="text-info text-decoration-none fw-bold">Request Resend</a></p>
                    </div>
                </div>
            </div>
            
            <p class="text-center mt-4 small opacity-50">&copy; 2026 Metro Express AI Systems</p>
        </div>
    </div>
</body>
</html>
