<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}
$uid = $_SESSION['user']['email'];
$user = $_SESSION['user'];
$success = '';
$error = '';
if (!isset($_SESSION['users'])) {
    $_SESSION['users'] = [
        ["name" => "Alice Example", "email" => "alice@example.com", "password" => password_hash("password1", PASSWORD_DEFAULT)],
        ["name" => "Bob Demo", "email" => "bob@demo.com", "password" => password_hash("password2", PASSWORD_DEFAULT)],
    ];
}
if (isset($_POST['update'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm = $_POST['password_confirmation'];
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";
    } elseif ($password && $password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        foreach ($_SESSION['users'] as &$u) {
            if ($u['email'] === $uid) {
                $u['name'] = $name;
                $u['email'] = $email;
                if (!empty($password)) {
                    $u['password'] = password_hash($password, PASSWORD_DEFAULT);
                }
                $_SESSION['user'] = $u;
                $success = "Profile updated successfully.";
                $uid = $email;
                break;
            }
        }
        unset($u);
    }
}
if (isset($_POST['delete'])) {
    $password = $_POST['confirm_password'];
    foreach ($_SESSION['users'] as $i => $u) {
        if ($u['email'] === $uid && password_verify($password, $u['password'])) {
            unset($_SESSION['users'][$i]);
            session_destroy();
            header("Location: index.php");
            exit;
        }
    }
    $error = "Incorrect password. Account not deleted.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Profile</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="//unpkg.com/alpinejs" defer></script>
  <style>
    .snowflake {
      position: fixed;
      top: -2rem;
      color: white;
      user-select: none;
      font-size: 1rem;
      z-index: 50;
      animation-name: fall;
      animation-timing-function: linear;
      animation-iteration-count: infinite;
      pointer-events: none;
    }
    @keyframes fall {
      0% { transform: translateY(-2rem) rotate(0deg); opacity: 1; }
      100% { transform: translateY(100vh) rotate(360deg); opacity: 0; }
    }
  </style>
</head>
<body x-data="{ drawerOpen: false }" class="bg-gradient-to-br from-[#e0dfff] via-[#d8e9f4] to-[#bcd9ea] min-h-screen flex">

<!-- ❄️ Snowflake Effect -->
<script>
document.addEventListener("DOMContentLoaded", () => {
  const snowContainer = document.createElement("div");
  document.body.appendChild(snowContainer);

  for (let i = 0; i < 30; i++) {
    const snowflake = document.createElement("div");
    snowflake.className = "snowflake";
    snowflake.style.left = Math.random() * 100 + "vw";
    snowflake.style.animationDuration = (5 + Math.random() * 10) + "s";
    snowflake.style.fontSize = (10 + Math.random() * 15) + "px";
    snowflake.style.opacity = Math.random();
    snowflake.textContent = ["❄️", "❅", "❆"][Math.floor(Math.random() * 3)];
    snowContainer.appendChild(snowflake);
  }
});
</script>

<!-- Sidebar -->
<aside :class="drawerOpen ? 'translate-x-0' : '-translate-x-full'" 
       class="fixed inset-y-0 left-0 w-64 bg-gradient-to-b from-white to-blue-100 border-r border-blue-200 shadow-lg transform transition duration-300 ease-in-out sm:translate-x-0 z-40">
  <div class="flex flex-col h-full">
    <div class="p-6 border-b border-blue-200">
      <h2 class="text-2xl font-bold text-blue-800 tracking-tight mb-3">📋 AYAKA</h2>
      <div class="flex items-center space-x-3">
        <div class="w-10 h-10 rounded-full bg-blue-300 text-white flex items-center justify-center text-xl font-semibold">
          <?= strtoupper(substr($_SESSION['user']['name'], 0, 1)) ?>
        </div>
        <div class="leading-tight">
          <p class="text-blue-800 font-semibold"><?= htmlspecialchars($_SESSION['user']['name']) ?></p>
          <p class="text-xs text-blue-500">Member</p>
        </div>
      </div>
    </div>
    <nav class="flex-1 px-4 py-6 space-y-1 text-sm font-medium text-blue-900">
      <a href="dashboard.php" class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-blue-100 transition-all"><span>🏠</span><span>Dashboard</span></a>
      <a href="booking.php" class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-blue-100 transition-all"><span>📅</span><span>Book Now</span></a>
      <a href="users.php" class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-blue-100 transition-all"><span>👥</span><span>Users</span></a>
      <a href="notifications.php" class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-blue-100 transition-all"><span>🔔</span><span>Notifications</span></a>
      <a href="profile.php" class="flex items-center space-x-2 px-3 py-2 rounded-lg bg-blue-200 font-bold"><span>👤</span><span>Profile</span></a>
      <div class="border-t border-blue-200 my-3"></div>
      <a href="logout.php" class="flex items-center space-x-2 px-3 py-2 rounded-lg text-red-600 hover:bg-red-100 transition-all"><span>🚪</span><span>Logout</span></a>
    </nav>
  </div>
</aside>

<!-- Overlay for Mobile -->
<div x-show="drawerOpen" @click="drawerOpen = false" class="fixed inset-0 bg-black bg-opacity-40 sm:hidden z-30" style="display: none;"></div>

<!-- Main Content -->
<main class="flex-1 p-6 sm:ml-64">

  <!-- Toggle Sidebar Button (Mobile) -->
  <button @click="drawerOpen = true" class="sm:hidden mb-4 p-2 bg-white rounded-md shadow text-blue-800">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
    </svg>
  </button>

  <div class="max-w-4xl mx-auto bg-white/60 backdrop-blur-lg rounded-2xl shadow-2xl p-10 border border-blue-200">
    <h2 class="text-4xl font-bold text-[#334188] mb-10 text-center drop-shadow-md">❄️ Edit Your Profile</h2>

    <?php if ($success): ?>
      <div class="bg-green-100 text-green-800 p-4 mb-8 rounded-lg border border-green-300 text-center"><?= $success ?></div>
    <?php elseif ($error): ?>
      <div class="bg-red-100 text-red-800 p-4 mb-8 rounded-lg border border-red-300 text-center"><?= $error ?></div>
    <?php endif; ?>

    <!-- Profile Update Form -->
    <form method="POST" class="space-y-8">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div>
          <label class="block text-sm font-medium text-blue-800 mb-1">Name</label>
          <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>"
              class="w-full border border-blue-300 bg-white rounded-lg px-4 py-3 text-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-400"
              required>
        </div>

        <div>
          <label class="block text-sm font-medium text-blue-800 mb-1">Email</label>
          <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>"
              class="w-full border border-blue-300 bg-white rounded-lg px-4 py-3 text-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-400"
              required>
        </div>

        <div>
          <label class="block text-sm font-medium text-blue-800 mb-1">New Password <span class="text-xs text-gray-500">(optional)</span></label>
          <input type="password" name="password"
              class="w-full border border-blue-300 bg-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>

        <div>
          <label class="block text-sm font-medium text-blue-800 mb-1">Confirm New Password</label>
          <input type="password" name="password_confirmation"
              class="w-full border border-blue-300 bg-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-400">
        </div>
      </div>

      <div class="text-center pt-6">
        <button type="submit" name="update"
            class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 px-10 rounded-full transition shadow-lg">
          Update Profile
        </button>
      </div>
    </form>

    <hr class="my-12 border-blue-200">

    <!-- Delete Account -->
    <form method="POST" onsubmit="return confirm('Are you sure you want to delete your account?')">
      <h3 class="text-2xl font-semibold text-red-700 mb-6 text-center">⚠️ Delete Account</h3>
      <div class="max-w-md mx-auto space-y-4">
        <label class="block text-sm font-medium text-red-700 mb-1">Confirm Password</label>
        <input type="password" name="confirm_password"
            class="w-full border border-red-300 bg-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-red-400"
            required>

        <div class="text-center pt-4">
          <button type="submit" name="delete"
              class="bg-red-500 hover:bg-red-600 text-white font-semibold py-3 px-10 rounded-full transition shadow-md">
            Delete Account
          </button>
        </div>
      </div>
    </form>
  </div>
</main>
</body>
</html>
