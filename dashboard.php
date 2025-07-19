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
$totalBookings = count(array_filter($_SESSION['bookings'], function($b) use ($uid) { return $b['user_email'] === $uid; }));
$totalUsers = count($_SESSION['users']);
$bookings = array_filter($_SESSION['bookings'], function($b) use ($uid) { return $b['user_email'] === $uid; });
$usersList = $_SESSION['users'];

// Handle delete booking
if (isset($_GET['delete_booking'])) {
    $delete_idx = intval($_GET['delete_booking']);
    if (isset($_SESSION['bookings'][$delete_idx]) && $_SESSION['bookings'][$delete_idx]['user_email'] === $uid) {
        unset($_SESSION['bookings'][$delete_idx]);
        $_SESSION['bookings'] = array_values($_SESSION['bookings']); // reindex
        header('Location: dashboard.php');
        exit;
    }
}
// Handle edit booking
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
  <title>Dashboard</title>
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
<body x-data="{ drawerOpen: false, showBookings: false, showUsers: false }" class="bg-gradient-to-br from-[#e0dfff] via-[#d8e9f4] to-[#bcd9ea] min-h-screen flex">

<!-- ❄️ Snowflakes (Auto-Generated) -->
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
      <a href="dashboard.php" class="flex items-center space-x-2 px-3 py-2 rounded-lg bg-blue-200 font-bold"><span>🏠</span><span>Dashboard</span></a>
      <a href="booking.php" class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-blue-100 transition-all"><span>📅</span><span>Book Now</span></a>
      <a href="users.php" class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-blue-100 transition-all"><span>👥</span><span>Users</span></a>
      <a href="notifications.php" class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-blue-100 transition-all"><span>🔔</span><span>Notifications</span></a>
      <a href="profile.php" class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-blue-100 transition-all"><span>👤</span><span>Profile</span></a>
      <div class="border-t border-blue-200 my-3"></div>
      <a href="logout.php" class="flex items-center space-x-2 px-3 py-2 rounded-lg text-red-600 hover:bg-red-100 transition-all"><span>🚪</span><span>Logout</span></a>
    </nav>
  </div>
</aside>

<!-- Overlay for mobile -->
<div x-show="drawerOpen" @click="drawerOpen = false" class="fixed inset-0 bg-black bg-opacity-40 sm:hidden z-30" style="display: none;"></div>

<!-- Main Content -->
<main class="flex-1 p-6 sm:ml-64">

  <!-- Toggle Sidebar (Mobile) -->
  <button @click="drawerOpen = true" class="sm:hidden mb-4 p-2 bg-white rounded-md shadow text-blue-800">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
    </svg>
  </button>

  <!-- Welcome Box -->
  <div class="bg-white/30 backdrop-blur-lg rounded-2xl p-8 border border-teal-300 shadow-lg">
    <h3 class="text-3xl font-semibold text-[#334188]">Welcome, <?= htmlspecialchars($_SESSION['user']['name']) ?>!</h3>
    <p class="text-[#4a5c7a] mt-2 text-lg">Here’s your personalized dashboard summary:</p>
  </div>

  <!-- Stats -->
  <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 my-8">
    <div @click="showBookings = !showBookings"
         class="cursor-pointer bg-white/60 border-l-4 border-teal-500 p-6 rounded-2xl hover:bg-white/80 shadow transition">
      <h4 class="text-teal-700 font-semibold text-xl">Total Bookings</h4>
      <p class="text-4xl font-bold text-teal-600 mt-2"><?= $totalBookings ?></p>
    </div>
    <div @click="showUsers = !showUsers"
         class="cursor-pointer bg-white/60 border-l-4 border-indigo-500 p-6 rounded-2xl hover:bg-white/80 shadow transition">
      <h4 class="text-indigo-700 font-semibold text-xl">Total Users</h4>
      <p class="text-4xl font-bold text-indigo-600 mt-2"><?= $totalUsers ?></p>
    </div>
  </div>

  <!-- Bookings List -->
  <div x-show="showBookings" x-transition class="bg-white/40 backdrop-blur-lg shadow-xl rounded-2xl p-8 border border-indigo-300" style="display: none;">
    <h3 class="text-2xl font-semibold text-indigo-700 mb-6">Your Bookings</h3>
    <?php if (count($bookings)): ?>
      <div class="space-y-5">
        <?php foreach ($bookings as $idx => $booking): ?>
          <div class="bg-white rounded-xl p-5 border border-indigo-200 shadow hover:shadow-xl transition duration-300">
            <?php if (isset($_GET['edit_booking']) && $_GET['edit_booking'] == $idx): ?>
              <form method="POST" class="space-y-2">
                <input type="hidden" name="edit_idx" value="<?= $idx ?>">
                <input name="title" value="<?= htmlspecialchars($booking['title']) ?>" class="w-full border p-2 rounded" required>
                <textarea name="description" class="w-full border p-2 rounded" required><?= htmlspecialchars($booking['description']) ?></textarea>
                <input name="booking_date" value="<?= htmlspecialchars($booking['booking_date']) ?>" class="w-full border p-2 rounded" required>
                <div class="flex gap-2">
                  <button type="submit" name="edit_booking" class="bg-blue-500 text-white px-4 py-2 rounded">Save</button>
                  <a href="dashboard.php" class="bg-gray-300 px-4 py-2 rounded">Cancel</a>
                </div>
              </form>
            <?php else: ?>
              <h4 class="text-lg font-semibold text-indigo-800"><?= htmlspecialchars($booking['title']) ?></h4>
              <p class="text-indigo-700 text-sm mt-1"><?= htmlspecialchars($booking['description']) ?></p>
              <p class="text-xs text-indigo-500 mt-3">📅 <?= date('F j, Y h:i A', strtotime($booking['booking_date'])) ?></p>
              <div class="mt-4 flex space-x-6">
                <a href="dashboard.php?edit_booking=<?= $idx ?>" class="text-blue-600 hover:text-blue-800 font-semibold underline">Edit</a>
                <a href="dashboard.php?delete_booking=<?= $idx ?>" onclick="return confirm('Are you sure you want to delete this booking?');" class="text-red-600 hover:text-red-800 font-semibold underline">Delete</a>
              </div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p class="text-center text-indigo-600 italic text-sm">You don’t have any bookings yet.</p>
    <?php endif; ?>
  </div>

  <!-- Users List -->
  <div x-show="showUsers" x-transition class="bg-white/40 backdrop-blur-lg shadow-xl rounded-2xl p-8 border border-teal-300 text-indigo-800 mt-6" style="display: none;">
    <h3 class="text-2xl font-semibold text-teal-800 mb-4">All Users</h3>
    <?php if (count($usersList)): ?>
      <ul class="space-y-4">
        <?php foreach ($usersList as $user): ?>
          <li class="bg-white rounded-xl p-4 border border-teal-200 shadow hover:shadow-md transition">
            <h4 class="font-semibold text-lg text-blue-700"><?= htmlspecialchars($user['name']) ?></h4>
            <p class="text-sm text-indigo-600"><?= htmlspecialchars($user['email']) ?></p>
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
