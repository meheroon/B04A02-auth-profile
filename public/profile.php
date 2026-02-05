<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Auth.php';

Auth::requireLogin();

$db = new Database();
$userModel = new User($db);

$userId = Auth::id();
$currentUser = $userModel->findById((int)$userId);

if (!$currentUser) {
    Auth::logout();
    redirect('login.php');
}

$errors = [];
$success = '';

$name  = $currentUser['name'];
$email = $currentUser['email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $newPass = $_POST['new_password'] ?? '';

    if ($name === '' || $email === '') {
        $errors[] = "Name and email are required.";
    }
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if ($newPass !== '' && strlen($newPass) < 6) {
        $errors[] = "New password must be at least 6 characters (or keep it empty).";
    }

    // Unique email check (if changed)
    if (!$errors && $email !== $currentUser['email']) {
        $existing = $userModel->findByEmail($email);
        if ($existing) {
            $errors[] = "This email is already taken.";
        }
    }

    if (!$errors) {
        $userModel->update((int)$userId, $name, $email, $newPass === '' ? null : $newPass);
        $success = "Profile updated successfully.";

        // reload user
        $currentUser = $userModel->findById((int)$userId);
    }
}

include __DIR__ . '/../includes/header.php';
?>
<div class="w-full max-w-xl bg-white rounded-xl shadow p-6">
  <div class="flex items-center justify-between mb-4">
    <h1 class="text-2xl font-bold">My Profile</h1>
    <a href="logout.php" class="text-red-600 font-semibold underline">Logout</a>
  </div>

  <div class="bg-slate-50 border rounded p-4 mb-4">
    <p class="text-sm text-slate-600">Account Created:</p>
    <p class="font-semibold"><?= e($currentUser['created_at']) ?></p>
  </div>

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
      <label class="block text-sm font-medium">New Password (optional)</label>
      <input type="password" name="new_password" class="w-full border rounded px-3 py-2" />
      <p class="text-xs text-slate-500 mt-1">Keep empty if you don’t want to change password.</p>
    </div>

    <button class="bg-blue-600 text-white rounded px-4 py-2 font-semibold">Update Profile</button>
  </form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
