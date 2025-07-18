<?php
include 'db.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

// ✅ FORM HANDLER
if (isset($_POST['book'])) {
    $uid = $_SESSION['user']['id'];
    $title = $conn->real_escape_string($_POST['title']);
    $desc = $conn->real_escape_string($_POST['description']);
    $date = $conn->real_escape_string($_POST['booking_date']);

    if (!empty($title) && !empty($desc) && !empty($date)) {
        $datetime = explode(' ', $date);
        $booking_date = $datetime[0];
        $booking_time = $datetime[1] ?? '00:00:00';

        $insert = $conn->query("INSERT INTO bookings (user_id, title, description, booking_date, booking_time) VALUES ($uid, '$title', '$desc', '$booking_date', '$booking_time')");

        if ($insert) {
            $msg = "New booking: $title on $booking_date at $booking_time";
            $conn->query("INSERT INTO notifications (user_id, message) VALUES ($uid, '$msg')");

            $_SESSION['success'] = "Booking created successfully!";
            header("Location: booking.php");
            exit;
        } else {
            $error = "Error: " . $conn->error;
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Booking</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="//unpkg.com/alpinejs" defer></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" />
  <style>
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

    /* ❄️ Snowflake Styles */
    .snowflake {
      color: #fff;
      font-size: 1.5rem;
      position: fixed;
      top: -10px;
      z-index: 50;
      animation: fall linear infinite;
      user-select: none;
      pointer-events: none;
    }

    @keyframes fall {
      0% { transform: translateY(0) translateX(0); opacity: 1; }
      100% { transform: translateY(100vh) translateX(50px); opacity: 0; }
    }

    .snowflake:nth-child(1) { left: 5%; animation-duration: 10s; animation-delay: 0s; }
    .snowflake:nth-child(2) { left: 15%; animation-duration: 12s; animation-delay: 2s; }
    .snowflake:nth-child(3) { left: 25%; animation-duration: 9s;  animation-delay: 4s; }
    .snowflake:nth-child(4) { left: 35%; animation-duration: 11s; animation-delay: 1s; }
    .snowflake:nth-child(5) { left: 50%; animation-duration: 13s; animation-delay: 3s; }
    .snowflake:nth-child(6) { left: 65%; animation-duration: 10s; animation-delay: 2s; }
    .snowflake:nth-child(7) { left: 75%; animation-duration: 14s; animation-delay: 0s; }
    .snowflake:nth-child(8) { left: 85%; animation-duration: 12s; animation-delay: 2s; }
    .snowflake:nth-child(9) { left: 10%; animation-duration: 11s; animation-delay: 1s; }
    .snowflake:nth-child(10) { left: 90%; animation-duration: 9s; animation-delay: 3s; }
  </style>
</head>
<body x-data="{ drawerOpen: false }" class="bg-gradient-to-br from-[#e0dfff] via-[#d8e9f4] to-[#bcd9ea] min-h-screen flex">

  <!-- ❄️ Snowflakes -->
  <div class="snowflake">❄️</div>
  <div class="snowflake">❅</div>
  <div class="snowflake">❆</div>
  <div class="snowflake">❄️</div>
  <div class="snowflake">❅</div>
  <div class="snowflake">❆</div>
  <div class="snowflake">❄️</div>
  <div class="snowflake">❅</div>
  <div class="snowflake">❄️</div>
  <div class="snowflake">❆</div>

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
        <a href="dashboard.php" class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-blue-100 transition-all">
          <span>🏠</span><span>Dashboard</span>
        </a>
        <a href="booking.php" class="flex items-center space-x-2 px-3 py-2 rounded-lg bg-blue-200">
          <span>📅</span><span>Book Now</span>
        </a>
        <a href="users.php" class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-blue-100 transition-all">
          <span>👥</span><span>Users</span>
        </a>
        <a href="notifications.php" class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-blue-100 transition-all">
          <span>🔔</span><span>Notifications</span>
        </a>
        <a href="profile.php" class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-blue-100 transition-all">
          <span>👤</span><span>Profile</span>
        </a>
        <div class="border-t border-blue-200 my-3"></div>
        <a href="logout.php" class="flex items-center space-x-2 px-3 py-2 rounded-lg text-red-600 hover:bg-red-100 transition-all">
          <span>🚪</span><span>Logout</span>
        </a>
      </nav>
    </div>
  </aside>

  <div x-show="drawerOpen" @click="drawerOpen = false" class="fixed inset-0 bg-black bg-opacity-40 sm:hidden z-30" style="display: none;"></div>

  <!-- Main Content -->
  <main class="flex-1 p-6 sm:ml-64">
    <button @click="drawerOpen = true" class="sm:hidden mb-4 p-2 bg-white rounded-md shadow text-blue-800">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
      </svg>
    </button>

    <div class="max-w-3xl mx-auto space-y-10">

      <?php if (!empty($_SESSION['success'])): ?>
        <div class="bg-green-100 text-green-800 p-4 rounded-lg shadow-md border border-green-300 text-center">
          <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($error)): ?>
        <div class="bg-red-100 text-red-800 p-4 rounded-lg shadow-md border border-red-300 text-center">
          <?= $error ?>
        </div>
      <?php endif; ?>

      <section class="bg-white/60 backdrop-blur-lg shadow-2xl rounded-2xl p-8 border border-blue-200">
        <h2 class="text-3xl font-bold mb-8 text-blue-800 text-center">Create a Booking</h2>

        <form method="POST" action="booking.php" class="space-y-6">
          <div>
            <label for="title" class="block text-sm font-medium text-blue-700">Title</label>
            <input type="text" name="title" id="title" required
              class="w-full mt-1 border border-blue-300 bg-white text-blue-900 rounded-lg p-3 focus:ring-2 focus:ring-blue-400 focus:border-blue-400" />
          </div>

          <div>
            <label for="description" class="block text-sm font-medium text-blue-700">Description</label>
            <textarea name="description" id="description" rows="3" required
              class="w-full mt-1 border border-blue-300 bg-white text-blue-900 rounded-lg p-3 focus:ring-2 focus:ring-blue-400 focus:border-blue-400"></textarea>
          </div>

          <input type="hidden" name="booking_date" id="booking_date" />

          <div>
            <label for="inline-calendar" class="block text-sm font-medium text-blue-700 mb-2">Booking Date & Time</label>
            <div id="inline-calendar" class="rounded-md border border-blue-200 bg-white/70 p-4"></div>
            <p class="text-blue-600 text-xs mt-2">Select your booking date and time.</p>
          </div>

          <div class="pt-4 text-center">
            <button type="submit" name="book"
              class="bg-blue-500 text-white py-3 px-8 rounded-full hover:bg-blue-600 transition font-semibold text-lg">
              Create Booking
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
