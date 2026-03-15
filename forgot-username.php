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
        if (recoverUsername($email)) {
            $success = "If an account exists with this email, your username has been sent.";
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
    <title>Recover Identity - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="col-md-5 col-lg-4">
            <div class="card p-4">
                <div class="card-body text-center">
                    <h4 class="mb-4 fw-bold">Recover Username</h4>
                    <p class="small opacity-75 mb-4">Enter your email to retrieve your identity handle.</p>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success py-2 mb-4"><small><?php echo $success; ?></small></div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger py-2 mb-4"><small><?php echo $error; ?></small></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-4">
                            <label class="form-label small text-uppercase fw-bold opacity-75 text-start d-block">Email Address</label>
                            <input type="email" name="email" class="form-control form-control-lg" required autofocus>
                        </div>
                        <div class="d-grid mb-4">
                            <button type="submit" class="btn btn-primary btn-lg">Recover Identity</button>
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
