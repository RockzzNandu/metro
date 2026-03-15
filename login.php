<?php
ob_start();
require_once 'includes/config.php';
require_once 'includes/auth_functions.php';

$error = '';
$success = '';

if (isLoggedIn()) {
    if (isAdmin()) {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: user/dashboard.php");
    }
    exit;
}

if (isset($_GET['msg']) && $_GET['msg'] == 'reset_success') {
        $success = "Password reset successful! You can now login.";
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'] ?? null;
    
    if (empty($email)) {
        $error = 'Email is required.';
    } elseif (empty($password)) {
        // OTP Login logic - send a one-time code to the user's email
        $resetResult = initiatePasswordReset($email); // Reusing this to send OTP

        if ($resetResult && isset($resetResult['status']) && $resetResult['status'] === 'ok') {
            $_SESSION['temp_user_id'] = $resetResult['user_id'];
            header("Location: verify-otp.php");
            exit;
        } elseif ($resetResult && isset($resetResult['status']) && $resetResult['status'] === 'mail_failed') {
            $error = 'We found your account but failed to send the OTP. Please try again later.';
        } else {
            $error = 'No account found with that email.';
        }
    } else {
        $result = loginUser($email, $password);
        
        if ($result['status'] == 'success') {
            if ($result['role'] == 'admin') {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: user/dashboard.php");
            }
            exit;
        } elseif ($result['status'] == 'unverified') {
            $_SESSION['temp_user_id'] = $result['user_id'];
            $error = 'Email not verified. Please check your inbox for the OTP.';
            header("Location: verify-otp.php");
            exit;
        } else {
            $error = 'Invalid email or password.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="col-md-5 col-lg-4">
            <div class="text-center mb-5">
                <h1 class="navbar-brand d-block mb-2" style="font-size: 2.5rem;">METRO EXPRESS</h1>
                <p class="text-muted">Next Generation Transit Ticketing</p>
            </div>
            
            <div class="card p-4">
                <div class="card-body">
                        <h4 class="text-center mb-4 fw-bold">Login to Portal</h4>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success py-2 mb-4" style="background: rgba(0, 242, 255, 0.1); border: 1px solid rgba(0, 242, 255, 0.2); color: var(--accent-cyan);">
                                <small><?php echo $success; ?></small>
                            </div>
                        <?php endif; ?>

                        <?php if ($error): ?>
                        <div class="alert alert-danger py-2" style="background: rgba(220, 53, 69, 0.1); border: 1px solid rgba(220, 53, 69, 0.2); color: #ff6b6b;">
                            <small><?php echo $error; ?></small>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="login.php" id="passwordLoginForm">
                        <div class="mb-4">
                            <label for="email" class="form-label small text-uppercase fw-bold opacity-75">Email Address</label>
                            <input type="email" name="email" id="email" class="form-control form-control-lg" placeholder="name@example.com" required>
                        </div>
                        <div id="passwordField">
                            <div class="mb-4">
                                <label for="password" class="form-label small text-uppercase fw-bold opacity-75">Password</label>
                                <div class="position-relative">
                                    <input type="password" name="password" id="password" class="form-control form-control-lg" placeholder="••••••••">
                                    <span class="password-toggle" id="togglePassword">
                                        <i class="bi bi-eye-slash" id="toggleIcon"></i>
                                    </span>
                                </div>
                                <div class="text-end mt-2">
                                    <a href="forgot-password.php" class="text-info text-decoration-none small opacity-75">Forgot Security Password?</a>
                                </div>
                            </div>
                        </div>
                        <div class="d-grid mb-4">
                            <button type="submit" class="btn btn-primary btn-lg">Access Dashboard</button>
                        </div>
                        <div class="text-center mb-4">
                            <button type="button" class="btn btn-link text-info text-decoration-none small" id="otpLoginToggle">Login via Secure OTP Instead</button>
                        </div>
                    </form>
                    
                    <script>
                        document.getElementById('otpLoginToggle').addEventListener('click', function() {
                            const pwdField = document.getElementById('passwordField');
                            const btn = this;
                            if (pwdField.style.display === 'none') {
                                pwdField.style.display = 'block';
                                btn.innerText = 'Login via Secure OTP Instead';
                                document.getElementById('password').required = true;
                            } else {
                                pwdField.style.display = 'none';
                                btn.innerText = 'Login via Password Instead';
                                document.getElementById('password').required = false;
                            }
                        });
                        
                        document.getElementById('togglePassword').addEventListener('click', function (e) {
                            const password = document.getElementById('password');
                            const icon = document.getElementById('toggleIcon');
                            if (password.type === 'password') {
                                password.type = 'text';
                                icon.classList.remove('bi-eye-slash');
                                icon.classList.add('bi-eye');
                            } else {
                                password.type = 'password';
                                icon.classList.remove('bi-eye');
                                icon.classList.add('bi-eye-slash');
                            }
                        });
                    </script>
                    
                    <div class="text-center">
                        <p class="mb-3 small opacity-75">Forgot Identity? <a href="forgot-username.php" class="text-info text-decoration-none fw-bold">Recover Username</a></p>
                        <p class="mb-0 small opacity-75">New to Metro? <a href="register.php" class="text-info text-decoration-none fw-bold">Initialize Account</a></p>
                    </div>
                </div>
            </div>
            
            <p class="text-center mt-4 small opacity-50">&copy; 2026 Metro Express AI Systems</p>
        </div>
    </div>
</body>
</html>
