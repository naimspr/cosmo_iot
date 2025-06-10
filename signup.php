<?php
require 'config.php';

$message = '';
$signup_success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['token_confirmed'] ?? '') === '1') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if (!$username || !$password || !$confirm) {
        $message = 'All fields are required.';
    } elseif ($password !== $confirm) {
        $message = 'Passwords do not match.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);

        if ($stmt->fetch()) {
            $message = 'Username is already taken.';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            if ($stmt->execute([$username, $hashed])) {
                $signup_success = true;
            } else {
                $message = 'Error creating account. Please try again.';
            }
        }
    }
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Sign Up</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">

<!-- Token Modal -->
<div class="modal fade" id="tokenModal" tabindex="-1" aria-labelledby="tokenModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <!-- Verification Form -->
      <div id="verifyForm" class="p-3">
        <div class="modal-header">
          <h5 class="modal-title">Token Verification</h5>
        </div>
        <div class="modal-body">
          <p>Enter the verification token:</p>
          <div id="tokenError" class="alert alert-danger d-none"></div>
          <input type="text" id="tokenInput" class="form-control mb-3" placeholder="Enter token" />
          <button type="button" class="btn btn-outline-secondary w-100" id="requestTokenBtn">Request Token</button>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="confirmToken">Confirm</button>
        </div>
      </div>

      <!-- Token Request Form -->
      <div id="requestForm" class="p-3 d-none">
        <div class="modal-header">
          <h5 class="modal-title">Request Token</h5>
        </div>
        <div class="modal-body">
          <p>Choose how to receive your token:</p>
          <div class="d-grid gap-2 mb-3">
            <button type="button" class="btn btn-primary" id="chooseEmail">Email</button>
            <button type="button" class="btn btn-success" id="chooseWhatsApp">WhatsApp</button>
            <button type="button" class="btn btn-info text-white" id="chooseSMS">SMS</button>
          </div>

          <div id="contactInputContainer" class="mb-3 d-none">
            <input type="text" id="contactInput" class="form-control" placeholder="" />
          </div>

          <button type="button" class="btn btn-secondary w-100 mb-2" id="backToVerifyBtn">Back</button>
          <button type="button" class="btn btn-primary w-100 d-none" id="sendTokenBtn">Send Token</button>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- Signup Form -->
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-4">
      <div class="card shadow">
        <div class="card-body">
          <h4 class="card-title text-center mb-4">Sign Up</h4>

          <?php if ($message): ?>
            <div class="alert alert-warning"><?= htmlspecialchars($message) ?></div>
          <?php endif; ?>

          <form method="post" id="signupForm">
            <input type="hidden" name="token_confirmed" id="tokenConfirmed" value="0" />

            <div class="mb-3">
              <label class="form-label">Username</label>
              <input type="text" name="username" class="form-control" required />
            </div>

            <div class="mb-3">
              <label class="form-label">Password</label>
              <input type="password" name="password" class="form-control" required />
            </div>

            <div class="mb-3">
              <label class="form-label">Confirm Password</label>
              <input type="password" name="confirm_password" class="form-control" required />
            </div>

            <div class="d-grid">
              <button type="submit" class="btn btn-success" id="signupButton" disabled>Sign Up</button>
            </div>

            <div class="mt-3 text-center">
              <a href="login.php">Already have an account? Login</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const tokenModal = new bootstrap.Modal(document.getElementById('tokenModal'));
  tokenModal.show();

  const verifyForm = document.getElementById('verifyForm');
  const requestForm = document.getElementById('requestForm');
  const tokenInput = document.getElementById('tokenInput');
  const confirmBtn = document.getElementById('confirmToken');
  const tokenError = document.getElementById('tokenError');
  const signupBtn = document.getElementById('signupButton');
  const tokenConfirmedInput = document.getElementById('tokenConfirmed');

  const requestTokenBtn = document.getElementById('requestTokenBtn');
  const backToVerifyBtn = document.getElementById('backToVerifyBtn');
  const chooseEmail = document.getElementById('chooseEmail');
  const chooseWhatsApp = document.getElementById('chooseWhatsApp');
  const chooseSMS = document.getElementById('chooseSMS');
  const sendTokenBtn = document.getElementById('sendTokenBtn');
  const contactInputContainer = document.getElementById('contactInputContainer');
  const contactInput = document.getElementById('contactInput');

  let currentMethod = '';

  // Confirm Token
  confirmBtn.addEventListener('click', () => {
    const entered = tokenInput.value.trim();
    const correct = 'ABC123'; // Replace with server-verified token

    if (entered === '') {
      tokenError.textContent = 'Token is required.';
      tokenError.classList.remove('d-none');
    } else if (entered !== correct) {
      tokenError.textContent = 'Invalid token.';
      tokenError.classList.remove('d-none');
    } else {
      tokenError.classList.add('d-none');
      tokenModal.hide();
      signupBtn.disabled = false;
      tokenConfirmedInput.value = '1';
    }
  });

  // Show Request Token UI
  requestTokenBtn.addEventListener('click', () => {
    verifyForm.classList.add('d-none');
    requestForm.classList.remove('d-none');
    tokenError.classList.add('d-none');
  });

  // Back to Verification
  backToVerifyBtn.addEventListener('click', () => {
    requestForm.classList.add('d-none');
    verifyForm.classList.remove('d-none');
    contactInputContainer.classList.add('d-none');
    sendTokenBtn.classList.add('d-none');
  });

  // Handlers for method selection
  function handleMethod(method, placeholder) {
    currentMethod = method;
    contactInput.placeholder = placeholder;
    contactInputContainer.classList.remove('d-none');
    sendTokenBtn.classList.remove('d-none');
  }

  chooseEmail.addEventListener('click', () => handleMethod('email', 'Enter your email address'));
  chooseWhatsApp.addEventListener('click', () => handleMethod('whatsapp', 'Enter your WhatsApp number'));
  chooseSMS.addEventListener('click', () => handleMethod('sms', 'Enter your phone number'));

  // Send Token
  sendTokenBtn.addEventListener('click', () => {
    const contact = contactInput.value.trim();
    if (!contact) {
      alert('Please enter a valid contact info.');
      return;
    }

    alert(`Token sent via ${currentMethod.toUpperCase()} to ${contact}`);
    contactInput.value = '';
    contactInputContainer.classList.add('d-none');
    sendTokenBtn.classList.add('d-none');
    requestForm.classList.add('d-none');
    verifyForm.classList.remove('d-none');
  });

  <?php if ($signup_success): ?>
    setTimeout(() => window.location.href = 'login.php', 800);
  <?php endif; ?>
});
</script>

</body>
</html>
