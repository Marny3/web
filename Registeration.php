<?php
session_start();
require 'db.php';
require 'vendor/autoload.php'; // Composer autoload for Google API and PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Google\Client;

// Initialize variables
$name = $email = $password_plain = $confirm_password = "";
$name_error = $email_error = $password_error = $confirm_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli("localhost", "root", "", "dating_app");
    if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

    // Sanitize inputs
    $name = trim($_POST['name'] ?? '');
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $password_plain = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validation
    if (empty($name)) $name_error = "Full Name is required.";
    elseif (strlen($name) < 3) $name_error = "Full Name must be at least 3 characters.";

    if (empty($email)) $email_error = "Email is required.";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $email_error = "Invalid email format.";

    if (empty($password_plain)) $password_error = "Password is required.";
    elseif (strlen($password_plain) < 6) $password_error = "Password must be at least 6 characters.";

    if ($password_plain !== $confirm_password) $confirm_error = "Passwords do not match.";

    // Only run if no errors
    if (!$name_error && !$email_error && !$password_error && !$confirm_error) {
        // Check duplicate email
        $stmt_check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        $stmt_check->store_result();
        if ($stmt_check->num_rows > 0) {
            $email_error = "An account with this email already exists.";
        } else {
            $password_hashed = password_hash($password_plain, PASSWORD_BCRYPT);
            $otp = rand(100000, 999999);

            $stmt = $conn->prepare("INSERT INTO users (name, email, password, otp, status) VALUES (?, ?, ?, ?, 0)");
            $stmt->bind_param("sssi", $name, $email, $password_hashed, $otp);

            if ($stmt->execute()) {
                $_SESSION['email'] = $email;

                // Send OTP
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'nyimarn2003@gmail.com'; // Gmail address
                    $mail->Password = 'mzaybfwossewtmti';     // Gmail App Password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom('nyimarn2003@gmail.com', 'Dating App');
                    $mail->addAddress($email, $name);

                    $mail->isHTML(true);
                    $mail->Subject = 'Your OTP Code for Dating App';
                    $mail->Body = "Hi $name,<br><br>Your OTP is: <b>$otp</b><br><br>This code is valid for 5 minutes.";

                    $mail->send();
                    header("Location: OTP_vertification.php");
                    exit();
                } catch (Exception $e) {
                    $email_error = "OTP could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }
            } else {
                $email_error = "Database error: " . $stmt->error;
            }

            $stmt->close();
        }
        $stmt_check->close();
    }

    $conn->close();
}

// Google OAuth setup
$google_client = new Client();
$google_client->setClientId('YOUR_GOOGLE_CLIENT_ID');
$google_client->setClientSecret('YOUR_GOOGLE_CLIENT_SECRET');
$google_client->setRedirectUri('http://localhost/Dating_Web/google-callback.php'); // Update to your callback
$google_client->addScope('email');
$google_client->addScope('profile');
$google_auth_url = $google_client->createAuthUrl();
?>

<?php include("header.php"); ?>

<main class="w-full">
  <section class="hero-frame relative min-h-screen flex items-center justify-center">
    <div class="hero-bg absolute top-0 left-0 w-full h-full bg-cover bg-center z-0" 
         style="background-image: url('img/loginbg.jpg');"></div>
    <div class="hero-overlay absolute top-0 left-0 w-full h-full bg-black bg-opacity-50 z-10"></div>

    <div class="relative screen-center px-4 z-20">
      <div class="glass w-full max-w-[420px] p-6">
        <h1 class="text-[22px] leading-7 text-gray-800 mb-1">
          Create your <span class="font-bold" style="color:var(--brand)">BONDED</span> account
        </h1>
        <p class="text-[12px] text-gray-500 mb-6">Sign up and start building real bonds.</p>

        <form class="space-y-3" action="" method="post" novalidate>
          <div>
            <label for="full_name" class="block text-[13px] mb-1 text-gray-700">Full Name</label>
            <input id="full_name" name="name" 
                   class="input <?= $name_error ? 'border-red-500' : '' ?>" 
                   type="text" placeholder="e.g. John Doe" 
                   value="<?= htmlspecialchars($name) ?>" required />
            <?php if($name_error): ?><p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($name_error) ?></p><?php endif; ?>
          </div>

          <div>
            <label for="email" class="block text-[13px] mb-1 text-gray-700">Email</label>
            <input id="email" name="email" 
                   class="input <?= $email_error ? 'border-red-500' : '' ?>" 
                   type="email" placeholder="example@gmail.com" 
                   value="<?= htmlspecialchars($email) ?>" required />
            <?php if($email_error): ?><p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($email_error) ?></p><?php endif; ?>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
              <label for="password" class="block text-[13px] mb-1 text-gray-700">Password</label>
              <input id="password" name="password" 
                     class="input <?= $password_error ? 'border-red-500' : '' ?>" 
                     type="password" placeholder="min. 6 characters" required />
              <?php if($password_error): ?><p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($password_error) ?></p><?php endif; ?>
            </div>
            <div>
              <label for="confirm_password" class="block text-[13px] mb-1 text-gray-700">Confirm Password</label>
              <input id="confirm_password" name="confirm_password" 
                     class="input <?= $confirm_error ? 'border-red-500' : '' ?>" 
                     type="password" placeholder="re-enter password" required />
              <?php if($confirm_error): ?><p class="text-red-500 text-xs mt-1"><?= htmlspecialchars($confirm_error) ?></p><?php endif; ?>
            </div>
          </div>

          <div class="text-[11px] leading-4 text-gray-600">
            <label class="inline-flex items-start gap-2 select-none">
              <input type="checkbox" class="mt-[2px]" required />
              <span>I agree to the Terms and Conditions and Privacy Policy</span>
            </label>
          </div>

          <div class="flex items-center gap-x-9 pt-1">
            <a href="login.php" class="btn btn-ghost flex-1 text-center">Back to Login</a>
            <button type="submit" class="btn btn-pink flex-1">Register</button>
          </div>
        </form>

        <!-- Social login buttons -->
        <div class="flex items-center justify-center gap-x-5 gap-y-2 text-gray-500 mt-4">
          <a href="#" class="icon" aria-label="Instagram">
            <svg viewBox="0 0 24 24" fill="none">
              <rect x="3" y="3" width="18" height="18" rx="5" stroke="currentColor" stroke-width="1.6"/>
              <circle cx="12" cy="12" r="4" stroke="currentColor" stroke-width="1.6"/>
              <circle cx="17.5" cy="6.5" r="1" fill="currentColor"/>
            </svg>
          </a>
          <a href="<?= htmlspecialchars($google_auth_url) ?>" class="icon" aria-label="Google">
            <svg viewBox="0 0 24 24">
              <path d="M21.6 12.227c0-.68-.061-1.333-.176-1.96H12v3.71h5.4a4.61 4.61 0 0 1-2.004 3.028v2.52h3.244c1.898-1.748 2.96-4.322 2.96-7.298Z" fill="#4285F4"/>
              <path d="M12 22c2.7 0 4.965-.894 6.62-2.41l-3.244-2.52c-.9.6-2.05.958-3.376.958-2.596 0-4.796-1.752-5.582-4.106H3.06v2.58A9.998 9.998 0 0 0 12 22Z" fill="#34A853"/>
              <path d="M6.418 13.922A6.006 6.006 0 0 1 6.09 12c0-.667.115-1.311.328-1.922V7.498H3.06A9.996 9.996 0 0 0 2 12c0 1.601.384 3.115 1.06 4.502l3.358-2.58Z" fill="#FBBC05"/>
              <path d="M12 5.88c1.468 0 2.782.505 3.82 1.494l2.865-2.865C16.96 2.894 14.7 2 12 2 8.06 2 4.7 4.264 3.06 7.498l3.358 2.58C7.204 7.724 9.404 5.88 12 5.88Z" fill="#EA4335"/>
            </svg>
          </a>
        </div>
        <?php include __DIR__ . '/partials/footer-links.php'; ?>
      </div>
    </div>
  </section>
</main>

<?php include("footer.php"); ?>