<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}
if (!isset($_SESSION['bookings'])) {
    $_SESSION['bookings'] = [
        ["user_email" => "alice@example.com", "title" => "Sample Booking", "description" => "Test booking", "booking_date" => date("Y-m-d H:i")],
    ];
}
if (!isset($_SESSION['notifications'])) {
    $_SESSION['notifications'] = [];
}
$error = '';
if (isset($_POST['book'])) {
    $uid = $_SESSION['user']['email'];
    $title = $_POST['title'];
    $desc = $_POST['description'];
    $date = $_POST['booking_date'];
    if (!empty($title) && !empty($desc) && !empty($date)) {
        $datetime = explode(' ', $date);
        $booking_date = $datetime[0] . (isset($datetime[1]) ? ' ' . $datetime[1] : ' 00:00:00');
        $_SESSION['bookings'][] = [
            "user_email" => $uid,
            "title" => $title,
            "description" => $desc,
            "booking_date" => $booking_date
        ];
        $msg = "New booking: $title on $booking_date";
        $_SESSION['notifications'][] = ["user_email" => $uid, "message" => $msg];
        $_SESSION['success'] = "Booking created successfully!";
        header("Location: booking.php");
        exit;
    } else {
        $error = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Kuromi Booking</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="//unpkg.com/alpinejs" defer></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
  <style>
    body {
      font-family: 'Comic Sans MS', cursive, sans-serif;
    }

    .kuromi-sparkle {
      position: fixed;
      top: -2rem;
      color: #ffb3d1;
      font-size: 1rem;
      user-select: none;
      z-index: 50;
      animation: sparkle linear infinite;
      pointer-events: none;
    }

    @keyframes sparkle {
      0% { transform: translateY(-2rem) rotate(0deg); opacity: 1; }
      100% { transform: translateY(100vh) rotate(360deg); opacity: 0; }
    }

    #inline-calendar {
        width: 420px;
        max-width: 100%;
        font-size: 1.2rem;
    }
    .flatpickr-day {
        height: 3rem;
        line-height: 3rem;
        width: 3rem;
        margin: 0.15rem;
        font-size: 1.1rem;
    }
    .flatpickr-time input {
        font-size: 1.1rem;
        width: 3.5rem;
        height: 2.5rem;
        padding: 0.25rem 0.5rem;
    }
    .flatpickr-prev-month,
    .flatpickr-next-month {
        height: 2.5rem;
        width: 2.5rem;
    }
  </style>
</head>
<body x-data="{ drawerOpen: false }" class="bg-gradient-to-br from-[#fbeaff] via-[#e5d1f2] to-[#b69dd2] min-h-screen flex text-gray-800">

  <!-- 💜 Kuromi Sparkles -->
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const sparkleContainer = document.createElement("div");
      document.body.appendChild(sparkleContainer);
      const symbols = ["💜", "🖤", "🌙", "✨"];
      for (let i = 0; i < 30; i++) {
        const sparkle = document.createElement("div");
        sparkle.className = "kuromi-sparkle";
        sparkle.style.left = Math.random() * 100 + "vw";
        sparkle.style.animationDuration = (5 + Math.random() * 10) + "s";
        sparkle.style.fontSize = (12 + Math.random() * 18) + "px";
        sparkle.style.opacity = Math.random();
        sparkle.textContent = symbols[Math.floor(Math.random() * symbols.length)];
        sparkleContainer.appendChild(sparkle);
      }
    });
  </script>

  <!-- Sidebar -->
  <aside :class="drawerOpen ? 'translate-x-0' : '-translate-x-full'" 
         class="fixed inset-y-0 left-0 w-64 bg-[#ffffffbb] backdrop-blur-md border-r border-pink-200 shadow-xl transform transition duration-300 ease-in-out sm:translate-x-0 z-40 rounded-r-3xl">
    <div class="flex flex-col h-full p-6 space-y-6">
      <h2 class="text-3xl font-extrabold text-purple-800 tracking-wide">💜 Kuromi</h2>
      <div class="flex items-center space-x-3">
        <div class="w-10 h-10 rounded-full bg-purple-400 text-white flex items-center justify-center text-xl font-bold shadow">
          <?= strtoupper(substr($_SESSION['user']['name'], 0, 1)) ?>
        </div>
        <div>
          <p class="text-purple-800 font-semibold"><?= htmlspecialchars($_SESSION['user']['name']) ?></p>
          <p class="text-xs text-purple-500">Lovely Member</p>
        </div>
      </div>

      <nav class="flex-1 space-y-2 font-medium text-sm">
        <a href="dashboard.php" class="flex items-center px-4 py-2 rounded-xl bg-white/60 hover:bg-pink-100 transition-all shadow">
          🏠 Dashboard
        </a>
        <a href="booking.php" class="flex items-center px-4 py-2 rounded-xl bg-pink-300 text-white font-semibold shadow">
          📅 Book Now
        </a>
        <a href="users.php" class="flex items-center px-4 py-2 rounded-xl bg-white/60 hover:bg-pink-100 transition-all shadow">
          👥 Users
        </a>
        <a href="notifications.php" class="flex items-center px-4 py-2 rounded-xl bg-white/60 hover:bg-pink-100 transition-all shadow">
          🔔 Notifications
        </a>
        <a href="profile.php" class="flex items-center px-4 py-2 rounded-xl bg-white/60 hover:bg-pink-100 transition-all shadow">
          👤 Profile
        </a>
        <a href="logout.php" class="flex items-center px-4 py-2 mt-4 rounded-xl bg-red-100 text-red-600 hover:bg-red-200 shadow">
          🚪 Logout
        </a>
      </nav>
    </div>
  </aside>

  <!-- Main -->
  <main class="flex-1 p-6 sm:ml-64">
    <button @click="drawerOpen = true" class="sm:hidden mb-4 p-2 bg-white rounded-md shadow text-purple-700">
      ☰ Menu
    </button>

    <div class="max-w-3xl mx-auto space-y-10">

      <?php if (!empty($_SESSION['success'])): ?>
        <div class="bg-green-100 text-green-800 p-4 rounded-xl shadow border border-green-300 text-center">
          <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($error)): ?>
        <div class="bg-red-100 text-red-800 p-4 rounded-xl shadow border border-red-300 text-center">
          <?= $error ?>
        </div>
      <?php endif; ?>

      <section class="bg-white/80 backdrop-blur-md shadow-2xl rounded-2xl p-8 border border-purple-300">
        <h2 class="text-3xl font-bold mb-8 text-purple-800 text-center">🎀 Create a Booking</h2>

        <form method="POST" action="booking.php" class="space-y-6">
          <div>
            <label for="title" class="block text-sm font-medium text-purple-700">Title</label>
            <input type="text" name="title" id="title" required
              class="w-full mt-1 border border-purple-300 bg-white text-purple-900 rounded-lg p-3 focus:ring-2 focus:ring-purple-400 focus:border-purple-400" />
          </div>

          <div>
            <label for="description" class="block text-sm font-medium text-purple-700">Description</label>
            <textarea name="description" id="description" rows="3" required
              class="w-full mt-1 border border-purple-300 bg-white text-purple-900 rounded-lg p-3 focus:ring-2 focus:ring-purple-400 focus:border-purple-400"></textarea>
          </div>

          <input type="hidden" name="booking_date" id="booking_date" />

          <div>
            <label for="inline-calendar" class="block text-sm font-medium text-purple-700 mb-2">Booking Date & Time</label>
            <div id="inline-calendar" class="rounded-xl border border-purple-200 bg-white/70 p-4"></div>
            <p class="text-purple-600 text-xs mt-2">Pick your date & time, darling!</p>
          </div>

          <div class="pt-4 text-center">
            <button type="submit" name="book"
              class="bg-pink-500 text-white py-3 px-8 rounded-full hover:bg-pink-600 transition font-bold text-lg shadow">
              💖 Create Booking
            </button>
          </div>
        </form>
      </section>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script>
    flatpickr("#inline-calendar", {
      inline: true,
      enableTime: true,
      dateFormat: "Y-m-d H:i",
      minDate: "today",
      time_24hr: false,
      defaultDate: null,
      onChange: function(selectedDates, dateStr) {
        document.getElementById("booking_date").value = dateStr;
      }
    });
  </script>
</body>
</html>
