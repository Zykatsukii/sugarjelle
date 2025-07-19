<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}
$users = [
    ["name" => "Alice Example", "email" => "alice@example.com"],
    ["name" => "Bob Demo", "email" => "bob@demo.com"],
    ["name" => "Charlie Test", "email" => "charlie@test.com"],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>👥 Kuromi Users</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="//unpkg.com/alpinejs" defer></script>
  <style>
    body {
      font-family: 'Comic Sans MS', cursive, sans-serif;
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
<body x-data="{ drawerOpen: false }" class="bg-gradient-to-br from-[#fceaff] via-[#e9d6ff] to-[#d0b3f7] min-h-screen flex overflow-x-hidden">

<!-- ✨ Kuromi Sparkles -->
<script>
  document.addEventListener("DOMContentLoaded", () => {
    const sparkleContainer = document.createElement("div");
    document.body.appendChild(sparkleContainer);

    const symbols = ["💜", "🌙", "🎀", "🖤"];
    for (let i = 0; i < 25; i++) {
      const sparkle = document.createElement("div");
      sparkle.className = "kuromi-sparkle";
      sparkle.style.left = Math.random() * 100 + "vw";
      sparkle.style.animationDuration = (6 + Math.random() * 8) + "s";
      sparkle.style.fontSize = (14 + Math.random() * 12) + "px";
      sparkle.style.opacity = Math.random();
      sparkle.textContent = symbols[Math.floor(Math.random() * symbols.length)];
      sparkleContainer.appendChild(sparkle);
    }
  });
</script>

<!-- Sidebar -->
<aside :class="drawerOpen ? 'translate-x-0' : '-translate-x-full'" 
       class="fixed inset-y-0 left-0 w-64 bg-gradient-to-b from-[#fff0f9] to-[#eed9fc] border-r border-pink-200 shadow-lg transform transition duration-300 ease-in-out sm:translate-x-0 z-40">
  <div class="flex flex-col h-full">
    <div class="p-6 border-b border-pink-200">
      <h2 class="text-2xl font-bold text-purple-800 tracking-tight mb-3">🎀 Kuromi Panel</h2>
      <div class="flex items-center space-x-3">
        <div class="w-10 h-10 rounded-full bg-purple-400 text-white flex items-center justify-center text-xl font-semibold">
          <?= strtoupper(substr($_SESSION['user']['name'], 0, 1)) ?>
        </div>
        <div class="leading-tight">
          <p class="text-purple-900 font-semibold"><?= htmlspecialchars($_SESSION['user']['name']) ?></p>
          <p class="text-xs text-purple-600">Member</p>
        </div>
      </div>
    </div>
    <nav class="flex-1 px-4 py-6 space-y-1 text-sm font-medium text-purple-900">
      <a href="dashboard.php" class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-purple-100 transition-all"><span>🏠</span><span>Dashboard</span></a>
      <a href="booking.php" class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-purple-100 transition-all"><span>📅</span><span>Book Now</span></a>
      <a href="users.php" class="flex items-center space-x-2 px-3 py-2 rounded-lg bg-purple-200 font-bold"><span>👥</span><span>Users</span></a>
      <a href="notifications.php" class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-purple-100 transition-all"><span>🔔</span><span>Notifications</span></a>
      <a href="profile.php" class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-purple-100 transition-all"><span>👤</span><span>Profile</span></a>
      <div class="border-t border-purple-200 my-3"></div>
      <a href="logout.php" class="flex items-center space-x-2 px-3 py-2 rounded-lg text-red-500 hover:bg-red-100 transition-all"><span>🚪</span><span>Logout</span></a>
    </nav>
  </div>
</aside>

<!-- Overlay -->
<div x-show="drawerOpen" @click="drawerOpen = false" class="fixed inset-0 bg-black bg-opacity-40 sm:hidden z-30" style="display: none;"></div>

<!-- Main Content -->
<main class="flex-1 p-6 sm:ml-64 relative z-10">

  <!-- Toggle Sidebar Button -->
  <button @click="drawerOpen = true" class="sm:hidden mb-4 p-2 bg-white rounded-md shadow text-purple-800">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
    </svg>
  </button>

  <!-- User List -->
  <section class="bg-white/60 backdrop-blur-lg p-8 rounded-2xl shadow-xl border border-purple-200">
    <h3 class="text-3xl font-bold text-purple-800 mb-6 text-center">All Users 💜</h3>

    <?php if (count($users)): ?>
      <ul class="space-y-4">
        <?php foreach ($users as $row): ?>
          <li class="bg-white p-4 rounded-lg border border-purple-100 shadow hover:shadow-md transition">
            <p class="text-lg text-purple-900 font-semibold"><?= htmlspecialchars($row['name']) ?></p>
            <p class="text-sm text-purple-600"><?= htmlspecialchars($row['email']) ?></p>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <p class="text-center italic text-purple-700">No users found.</p>
    <?php endif; ?>
  </section>
</main>
</body>
</html>
