<?php
ob_start();
require_once 'includes/config.php';
require_once 'includes/auth_functions.php';

// Determine which flow we are in
$user_id = null;
$redirect_to = '';

if (isset($_SESSION['temp_user_id'])) {
    $user_id = $_SESSION['temp_user_id'];
    $redirect_to = 'verify-otp.php';
} elseif (isset($_SESSION['reset_user_id'])) {
    $user_id = $_SESSION['reset_user_id'];
    $redirect_to = 'verify-reset-otp.php';
}

if (!$user_id) {
    header("Location: login.php");
    exit;
}

// Fetch user email
$stmt = $pdo->prepare("SELECT email FROM users WHERE id = :id");
$stmt->execute(['id' => $user_id]);
$user = $stmt->fetch();

if ($user) {
    // Generate new OTP
    $new_otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    
    // Update database
    $stmt = $pdo->prepare("UPDATE users SET otp_code = :otp, otp_expiry = DATE_ADD(NOW(), INTERVAL 10 MINUTE) WHERE id = :id");
    $stmt->execute(['otp' => $new_otp, 'id' => $user_id]);
    
    // Send email
    if (sendOTPEmail($user['email'], $new_otp)) {
        $_SESSION['resend_msg'] = "A new access code has been transmitted to " . $user['email'];
        $_SESSION['resend_type'] = "success";
    } else {
        $_SESSION['resend_msg'] = "We could not resend the code due to an email error. Please try again later.";
        $_SESSION['resend_type'] = "danger";
    }
} else {
    $_SESSION['resend_msg'] = "Session expired. Please login or start the reset process again.";
    $_SESSION['resend_type'] = "danger";
}

header("Location: " . $redirect_to);
exit;
?>
