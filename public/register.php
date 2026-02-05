
<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/User.php';

$errors = [];
$success = '';

$name = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    if ($name === '' || $email === '' || $pass === '') {
        $errors[] = "All fields are required.";
    }
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if ($pass !== '' && strlen($pass) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }

    if (!$errors) {
        $db = new Database();
        $userModel = new User($db);

        if ($userModel->findByEmail($email)) {
            $errors[] = "Email already exists. Try another one.";
        } else {
            $userModel->create($name, $email, $pass);
            $success = "Registration successful! You can login now.";
            $name = '';
            $email = '';
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>
<div class="w-full max-w-md bg-white rounded-xl shadow p-6">
  <h1 class="text-2xl font-bold mb-4">Register</h1>

  <?php if ($success): ?>
    <div class="bg-green-100 text-green-800 p-3 rounded mb-3"><?= e($success) ?></div>
  <?php endif; ?>

  <?php if ($errors): ?>
    <div class="bg-red-100 text-red-800 p-3 rounded mb-3">
      <ul class="list-disc ml-5">
        <?php foreach ($errors as $err): ?>
          <li><?= e($err) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="POST" class="space-y-3">
    <div>
      <label class="block text-sm font-medium">Full Name</label>
      <input name="name" value="<?= e($name) ?>" class="w-full border rounded px-3 py-2" />
    </div>
    <div>
      <label class="block text-sm font-medium">Email</label>
      <input name="email" value="<?= e($email) ?>" class="w-full border rounded px-3 py-2" />
    </div>
    <div>
      <label class="block text-sm font-medium">Password</label>
      <input type="password" name="password" class="w-full border rounded px-3 py-2" />
    </div>

    <button class="w-full bg-blue-600 text-white rounded px-3 py-2 font-semibold">Create Account</button>
  </form>

  <p class="text-sm mt-4">
    Already have an account?
    <a class="text-blue-600 underline" href="login.php">Login</a>
  </p>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
