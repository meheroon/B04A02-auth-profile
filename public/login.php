<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Auth.php';

$errors = [];
$email = '';

if (Auth::check()) {
    redirect('profile.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    if ($email === '' || $pass === '') {
        $errors[] = "Email and password are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (!$errors) {
        $db = new Database();
        $userModel = new User($db);

        $user = $userModel->findByEmail($email);

        if (!$user || !password_verify($pass, $user['password'])) {
            $errors[] = "Invalid credentials. Please try again.";
        } else {
            Auth::login((int)$user['id']);
            redirect('profile.php');
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>
<div class="w-full max-w-md bg-white rounded-xl shadow p-6">
  <h1 class="text-2xl font-bold mb-4">Login</h1>

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
      <label class="block text-sm font-medium">Email</label>
      <input name="email" value="<?= e($email) ?>" class="w-full border rounded px-3 py-2" />
    </div>
    <div>
      <label class="block text-sm font-medium">Password</label>
      <input type="password" name="password" class="w-full border rounded px-3 py-2" />
    </div>

    <button class="w-full bg-slate-900 text-white rounded px-3 py-2 font-semibold">Login</button>
  </form>

  <p class="text-sm mt-4">
    New here?
    <a class="text-blue-600 underline" href="register.php">Create an account</a>
  </p>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
