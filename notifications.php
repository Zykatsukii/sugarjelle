<?php
include 'db.php'; // Session start assumed inside this file

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$uid = intval($_SESSION['user']['id']);
$res = $conn->query("SELECT * FROM notifications WHERE user_id=$uid ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Notifications</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
</head>
<body class="bg-gradient-to-br from-[#e0dfff] via-[#d8e9f4] to-[#bcd9ea] min-h-screen flex">

    <!-- Sidebar -->
    <nav x-data="{ open: false }" class="fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-[#e0dfff] via-[#d8e9f4] to-[#bcd9ea] border-r border-blue-300 shadow-lg flex flex-col">
        <div class="flex items-center justify-center h-16 border-b border-blue-200">
            <h1 class="text-xl font-bold text-blue-700">My Dashboard</h1>
        </div>
        <div class="flex-1 p-4 space-y-2 text-blue-900 font-medium">
            <a href="dashboard.php" class="block px-3 py-2 rounded hover:bg-blue-100">🏠 Dashboard</a>
            <a href="notifications.php" class="block px-3 py-2 rounded bg-blue-200 font-semibold">🔔 Notifications</a>
            <a href="profile.php" class="block px-3 py-2 rounded hover:bg-blue-100">👤 Profile</a>
            <a href="logout.php" class="block px-3 py-2 rounded hover:bg-red-100 text-red-600">🚪 Logout</a>
        </div>
    </nav>

    <!-- Page Content -->
    <main class="flex-1 ml-64 p-8">
        <h3 class="text-2xl font-semibold text-blue-800 mb-4">🔔 Your Notifications</h3>
        
        <div class="space-y-3">
            <?php if ($res && $res->num_rows > 0): ?>
                <?php while ($row = $res->fetch_assoc()): ?>
                    <div class="bg-white p-4 rounded-xl shadow-sm border border-blue-100 hover:shadow-md transition duration-200">
                        <p class="text-blue-900"><?= htmlspecialchars($row['message']) ?></p>
                        <p class="text-sm text-gray-500 mt-1"><?= date("F j, Y, g:i a", strtotime($row['created_at'])) ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-gray-600 italic">No notifications found.</p>
            <?php endif; ?>
        </div>
    </main>

</body>
</html>
