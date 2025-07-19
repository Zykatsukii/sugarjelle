<?php
session_start();
if (!isset($_SESSION['user'])) header("Location: index.php");
$uid = $_SESSION['user']['email'];

if (!isset($_SESSION['users'])) {
    $_SESSION['users'] = [
        ["name" => "Alice Example", "email" => "alice@example.com", "password" => password_hash("password1", PASSWORD_DEFAULT)],
        ["name" => "Bob Demo", "email" => "bob@demo.com", "password" => password_hash("password2", PASSWORD_DEFAULT)],
    ];
}
if (!isset($_SESSION['bookings'])) {
    $_SESSION['bookings'] = [
        ["user_email" => "alice@example.com", "title" => "Sample Booking", "description" => "Test booking", "booking_date" => date("Y-m-d H:i")],
    ];
}

$totalBookings = count(array_filter($_SESSION['bookings'], fn($b) => $b['user_email'] === $uid));
$totalUsers = count($_SESSION['users']);
$bookings = array_filter($_SESSION['bookings'], fn($b) => $b['user_email'] === $uid);
$usersList = $_SESSION['users'];

if (isset($_GET['delete_booking'])) {
    $delete_idx = intval($_GET['delete_booking']);
    if (isset($_SESSION['bookings'][$delete_idx]) && $_SESSION['bookings'][$delete_idx]['user_email'] === $uid) {
        unset($_SESSION['bookings'][$delete_idx]);
        $_SESSION['bookings'] = array_values($_SESSION['bookings']);
        header('Location: dashboard.php');
        exit;
    }
}
if (isset($_POST['edit_booking'])) {
    $edit_idx = intval($_POST['edit_idx']);
    if (isset($_SESSION['bookings'][$edit_idx]) && $_SESSION['bookings'][$edit_idx]['user_email'] === $uid) {
        $_SESSION['bookings'][$edit_idx]['title'] = $_POST['title'];
        $_SESSION['bookings'][$edit_idx]['description'] = $_POST['description'];
        $_SESSION['bookings'][$edit_idx]['booking_date'] = $_POST['booking_date'];
        header('Location: dashboard.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Kuromi Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="//unpkg.com/alpinejs" defer></script>
  <style>
    body {
      font-family: 'Comic Sans MS', cursive, sans-serif;
      background: linear-gradient(to bottom right, #fbe4f0, #e3d4ff, #c9c9ff);
    }
    .kuromi-sparkle {
      position: fixed;
      top: -2rem;
      color: #ffb3d1;
      user-select: none;
      font-size: 1rem;
      z-index: 50;
      animation-name: sparkle;
      animation-timing-function: linear;
      animation-iteration-count: infinite;
      pointer-events: none;
    }
    @keyframes sparkle {
      0% { transform: translateY(-2rem) rotate(0deg); opacity: 1; }
      100% { transform: translateY(100vh) rotate(360deg); opacity: 0; }
    }
  </style>
</head>
<body x-data="{ drawerOpen: false, showBookings: false, showUsers: false }" class="min-h-screen flex">

<!-- 💜 Sparkles -->
<script>
  document.addEventListener("DOMContentLoaded", () => {
    const sparkleContainer = document.createElement("div");
    document.body.appendChild(sparkleContainer);

    for (let i = 0; i < 25; i++) {
      const sparkle = document.createElement("div");
      sparkle.className = "kuromi-sparkle";
      sparkle.style.left = Math.random() * 100 + "vw";
      sparkle.style.animationDuration = (6 + Math.random() * 10) + "s";
      sparkle.style.fontSize = (12 + Math.random() * 18) + "px";
      sparkle.style.opacity = Math.random();
      sparkle.textContent = ["💜", "✨", "🌙", "🖤"][Math.floor(Math.random() * 4)];
      sparkleContainer.appendChild(sparkle);
    }
  });
</script>

<!-- Sidebar -->
<aside :class="drawerOpen ? 'translate-x-0' : '-translate-x-full'" 
       class="fixed inset-y-0 left-0 w-64 bg-pink-100 border-r border-pink-300 shadow-lg transform transition duration-300 ease-in-out sm:translate-x-0 z-40">
  <div class="flex flex-col h-full">
    <div class="p-6 border-b border-pink-300">
      <h2 class="text-3xl font-bold text-purple-700 mb-3">🎀 KUROMI</h2>
      <div class="flex items-center space-x-3">
        <div class="w-10 h-10 rounded-full bg-purple-400 text-white flex items-center justify-center text-xl font-bold">
          <?= strtoupper(substr($_SESSION['user']['name'], 0, 1)) ?>
        </div>
        <div class="leading-tight">
          <p class="text-purple-800 font-semibold"><?= htmlspecialchars($_SESSION['user']['name']) ?></p>
          <p class="text-xs text-purple-600">Magical Member</p>
        </div>
      </div>
    </div>
    <nav class="flex-1 px-4 py-6 space-y-1 text-sm font-medium text-purple-900">
      <a href="dashboard.php" class="flex items-center space-x-2 px-3 py-2 rounded-lg bg-purple-200 font-bold"><span>🏠</span><span>Dashboard</span></a>
      <a href="booking.php" class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-pink-200 transition"><span>📅</span><span>Book Now</span></a>
      <a href="users.php" class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-pink-200 transition"><span>👥</span><span>Users</span></a>
      <a href="notifications.php" class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-pink-200 transition"><span>🔔</span><span>Notifications</span></a>
      <a href="profile.php" class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-pink-200 transition"><span>👤</span><span>Profile</span></a>
      <div class="border-t border-pink-300 my-3"></div>
      <a href="logout.php" class="flex items-center space-x-2 px-3 py-2 rounded-lg text-red-600 hover:bg-red-100 transition"><span>🚪</span><span>Logout</span></a>
    </nav>
  </div>
</aside>

<!-- Overlay -->
<div x-show="drawerOpen" @click="drawerOpen = false" class="fixed inset-0 bg-black bg-opacity-40 sm:hidden z-30" style="display: none;"></div>

<!-- Main Content -->
<main class="flex-1 p-6 sm:ml-64">

  <!-- Mobile Toggle -->
  <button @click="drawerOpen = true" class="sm:hidden mb-4 p-2 bg-white rounded-md shadow text-purple-800">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" stroke="currentColor" fill="none">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
    </svg>
  </button>

  <!-- Welcome Card -->
  <div class="bg-white/40 backdrop-blur-lg rounded-2xl p-8 border border-purple-300 shadow-lg">
    <h3 class="text-3xl font-semibold text-purple-800">Hi, <?= htmlspecialchars($_SESSION['user']['name']) ?> 💖</h3>
    <p class="text-purple-600 mt-2 text-lg">Here's your magical dashboard summary!</p>
  </div>

  <!-- Stats -->
  <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 my-8">
    <div @click="showBookings = !showBookings"
         class="cursor-pointer bg-white/60 border-l-4 border-pink-500 p-6 rounded-2xl hover:bg-white/80 shadow transition">
      <h4 class="text-pink-700 font-semibold text-xl">Total Bookings</h4>
      <p class="text-4xl font-bold text-pink-600 mt-2"><?= $totalBookings ?></p>
    </div>
    <div @click="showUsers = !showUsers"
         class="cursor-pointer bg-white/60 border-l-4 border-purple-500 p-6 rounded-2xl hover:bg-white/80 shadow transition">
      <h4 class="text-purple-700 font-semibold text-xl">Total Users</h4>
      <p class="text-4xl font-bold text-purple-600 mt-2"><?= $totalUsers ?></p>
    </div>
  </div>

  <!-- Bookings List -->
  <div x-show="showBookings" x-transition class="bg-white/50 backdrop-blur-lg shadow-xl rounded-2xl p-8 border border-purple-300" style="display: none;">
    <h3 class="text-2xl font-semibold text-purple-700 mb-6">Your Bookings 💜</h3>
    <?php if (count($bookings)): ?>
      <div class="space-y-5">
        <?php foreach ($bookings as $idx => $booking): ?>
          <div class="bg-white rounded-xl p-5 border border-purple-200 shadow hover:shadow-xl transition">
            <?php if (isset($_GET['edit_booking']) && $_GET['edit_booking'] == $idx): ?>
              <form method="POST" class="space-y-2">
                <input type="hidden" name="edit_idx" value="<?= $idx ?>">
                <input name="title" value="<?= htmlspecialchars($booking['title']) ?>" class="w-full border p-2 rounded" required>
                <textarea name="description" class="w-full border p-2 rounded" required><?= htmlspecialchars($booking['description']) ?></textarea>
                <input name="booking_date" value="<?= htmlspecialchars($booking['booking_date']) ?>" class="w-full border p-2 rounded" required>
                <div class="flex gap-2">
                  <button type="submit" name="edit_booking" class="bg-purple-500 text-white px-4 py-2 rounded">Save</button>
                  <a href="dashboard.php" class="bg-gray-300 px-4 py-2 rounded">Cancel</a>
                </div>
              </form>
            <?php else: ?>
              <h4 class="text-lg font-semibold text-purple-800"><?= htmlspecialchars($booking['title']) ?></h4>
              <p class="text-purple-700 text-sm mt-1"><?= htmlspecialchars($booking['description']) ?></p>
              <p class="text-xs text-purple-500 mt-3">📅 <?= date('F j, Y h:i A', strtotime($booking['booking_date'])) ?></p>
              <div class="mt-4 flex space-x-6">
                <a href="dashboard.php?edit_booking=<?= $idx ?>" class="text-blue-600 hover:text-blue-800 font-semibold underline">Edit</a>
                <a href="dashboard.php?delete_booking=<?= $idx ?>" onclick="return confirm('Are you sure you want to delete this booking?');" class="text-red-600 hover:text-red-800 font-semibold underline">Delete</a>
              </div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="text-center text-purple-600 italic text-sm">You don’t have any bookings yet.</p>
    <?php endif; ?>
  </div>

  <!-- Users List -->
  <div x-show="showUsers" x-transition class="bg-white/50 backdrop-blur-lg shadow-xl rounded-2xl p-8 border border-pink-300 mt-6" style="display: none;">
    <h3 class="text-2xl font-semibold text-pink-700 mb-4">All Users 🎀</h3>
    <?php if (count($usersList)): ?>
      <ul class="space-y-4">
        <?php foreach ($usersList as $user): ?>
          <li class="bg-white rounded-xl p-4 border border-pink-200 shadow hover:shadow-md transition">
            <h4 class="font-semibold text-lg text-purple-700"><?= htmlspecialchars($user['name']) ?></h4>
            <p class="text-sm text-purple-600"><?= htmlspecialchars($user['email']) ?></p>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <p class="italic text-sm text-center text-gray-600">No users found.</p>
    <?php endif; ?>
  </div>

</main>
</body>
</html>
