<?php
ob_start();
require_once 'includes/config.php';
require_once 'includes/auth_functions.php';

$error = '';
$success = '';

// Fetch cities for dropdown
$cities = $pdo->query("SELECT * FROM cities ORDER BY name ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = [
        'name' => $_POST['name'],
        'email' => $_POST['email'],
        'mobile' => $_POST['mobile'],
        'password' => $_POST['password'],
        'city_id' => $_POST['city_id']
    ];
    
    // Simple validation
    if (empty($data['name']) || empty($data['email']) || empty($data['mobile']) || empty($data['password']) || empty($data['city_id'])) {
        $error = 'Please fill in all fields including city.';
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format.';
    } else {
        $result = registerUser($data);

        if ($result && isset($result['user_id'])) {
            // Persist the temporary user id for OTP verification
            $_SESSION['temp_user_id'] = $result['user_id'];

            // If this reused an existing unverified account, show an info message on OTP screen
            if (!empty($result['existing'])) {
                $_SESSION['resend_msg'] = 'An account with these details already exists but was not verified. We have sent a new verification code.';
                $_SESSION['resend_type'] = 'warning';
            }

            // If email sending failed, keep the user on this page with a clear message
            if (isset($result['email_sent']) && !$result['email_sent']) {
                $error = 'Account created or found but failed to send verification code. Please try again later or contact support.';
            } else {
                header("Location: verify-otp.php");
                exit;
            }
        } else {
            $error = 'An account with this email or mobile is already fully active. Please login or use "Forgot Password" to regain access.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="col-md-6 col-lg-5">
            <div class="text-center mb-5">
                <h1 class="navbar-brand d-block mb-2" style="font-size: 2.5rem;">METRO EXPRESS</h1>
                <p class="text-muted">Create Your Universal Transit Identity</p>
            </div>
            
            <div class="card p-4">
                <div class="card-body">
                    <h4 class="text-center mb-4 fw-bold">Account Registration</h4>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger py-2" style="background: rgba(220, 53, 69, 0.1); border: 1px solid rgba(220, 53, 69, 0.2); color: #ff6b6b;">
                            <small><?php echo $error; ?></small>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="register.php">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="name" class="form-label small text-uppercase fw-bold opacity-75">Full Name</label>
                                <input type="text" name="name" id="name" class="form-control" placeholder="John Doe" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label small text-uppercase fw-bold opacity-75">Email Address</label>
                                <input type="email" name="email" id="email" class="form-control" placeholder="john@example.com" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="mobile" class="form-label small text-uppercase fw-bold opacity-75">Mobile Number</label>
                                <input type="text" name="mobile" id="mobile" class="form-control" placeholder="+91 98765 43210" required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="city_id" class="form-label small text-uppercase fw-bold opacity-75">Operational City</label>
                                <select name="city_id" id="city_id" class="form-select" required>
                                    <option value="">Select City</option>
                                    <?php foreach ($cities as $city): ?>
                                        <option value="<?php echo $city['id']; ?>"><?php echo $city['name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-12 mb-4">
                                <label for="password" class="form-label small text-uppercase fw-bold opacity-75">Security Password</label>
                                <div class="position-relative">
                                    <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
                                    <span class="password-toggle" id="togglePassword">
                                        <i class="bi bi-eye-slash" id="toggleIcon"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="d-grid mb-4">
                            <button type="submit" class="btn btn-primary btn-lg">Generate Account</button>
                        </div>
                    </form>
                    
                    <script>
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
                        <p class="mb-0 small opacity-75">Already have a profile? <a href="login.php" class="text-info text-decoration-none fw-bold">Authenticate Here</a></p>
                    </div>
                </div>
            </div>
            
            <p class="text-center mt-4 small opacity-50">&copy; 2026 Metro Express AI Systems</p>
        </div>
    </div>
</body>
</html>
