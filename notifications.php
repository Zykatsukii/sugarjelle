<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}
$uid = $_SESSION['user']['email'];

if (!isset($_SESSION['notifications'])) {
    $_SESSION['notifications'] = [
        ["user_email" => "alice@example.com", "message" => "Welcome Alice!"],
        ["user_email" => "bob@demo.com", "message" => "Welcome Bob!"],
    ];
}

$my_notifications = array_filter($_SESSION['notifications'], function($n) use ($uid) {
    return $n['user_email'] === $uid;
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kuromi Notifications</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500&display=swap');
        body {
            font-family: 'Playfair Display', serif;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-[#fceeff] via-[#fcd8f8] to-[#f5c9ff] min-h-screen flex text-gray-900">

    <!-- Sidebar -->
    <nav x-data="{ open: false }" class="fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-[#e5d0f7] via-[#f5c9ff] to-[#fcd8f8] border-r border-pink-200 shadow-xl flex flex-col">
        <div class="flex items-center justify-center h-16 border-b border-pink-200">
            <h1 class="text-xl font-bold text-[#a3489b]">💜 Kuromi Panel</h1>
        </div>
        <div class="flex-1 p-4 space-y-2 font-medium text-[#742f79]">
            <a href="dashboard.php" class="block px-3 py-2 rounded hover:bg-[#fbe7ff]">🏠 Dashboard</a>
            <a href="bookings.php" class="block px-3 py-2 rounded hover:bg-[#fbe7ff]">📅 Bookings</a>
            <a href="notifications.php" class="block px-3 py-2 rounded bg-[#fcd8f8] font-semibold">🔔 Notifications</a>
            <a href="profile.php" class="block px-3 py-2 rounded hover:bg-[#fbe7ff]">👤 Profile</a>
            <a href="logout.php" class="block px-3 py-2 rounded hover:bg-red-100 text-red-600">🚪 Logout</a>
        </div>
    </nav>

    <!-- Page Content -->
    <main class="flex-1 ml-64 p-8">
        <h2 class="text-3xl font-bold text-[#a3489b] mb-6">🔔 Your Notifications</h2>

        <?php if (count($my_notifications)): ?>
            <ul class="space-y-4">
                <?php foreach ($my_notifications as $n): ?>
                    <li class="bg-white p-4 rounded-lg border border-pink-200 shadow hover:shadow-lg transition">
                        <p class="text-[#742f79] font-semibold"><?= htmlspecialchars($n['message']) ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-center italic text-[#742f79]">No notifications found.</p>
        <?php endif; ?>
    </main>

</body>
</html>
