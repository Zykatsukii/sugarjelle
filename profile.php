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
    <title>Kuromi Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500&display=swap');
        body {
            font-family: 'Playfair Display', serif;
        }
    </style>
</head>
<body x-data="{ drawerOpen: false }" class="bg-gradient-to-br from-[#fceeff] via-[#fcd8f8] to-[#f5c9ff] min-h-screen flex text-gray-900">

<!-- Sidebar -->
<aside class="fixed inset-y-0 left-0 w-64 bg-gradient-to-b from-[#fcd8f8] via-[#f5c9ff] to-[#fceeff] border-r border-pink-200 shadow-lg z-40">
    <div class="p-6 border-b border-pink-200">
        <h2 class="text-2xl font-bold text-[#a3489b] tracking-tight mb-3">💜 Kuromi Panel</h2>
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-full bg-[#a3489b] text-white flex items-center justify-center text-xl font-semibold">
                <?= strtoupper(substr($_SESSION['user']['name'], 0, 1)) ?>
            </div>
            <div class="leading-tight">
                <p class="text-[#742f79] font-semibold"><?= htmlspecialchars($_SESSION['user']['name']) ?></p>
                <p class="text-xs text-[#b26bb2]">Member</p>
            </div>
        </div>
    </div>
    <nav class="flex-1 px-4 py-6 space-y-1 text-sm font-medium text-[#742f79]">
        <a href="dashboard.php" class="flex items-center px-3 py-2 rounded hover:bg-[#fbe7ff]">🏠 Dashboard</a>
        <a href="booking.php" class="flex items-center px-3 py-2 rounded hover:bg-[#fbe7ff]">📅 Bookings</a>
        <a href="notifications.php" class="flex items-center px-3 py-2 rounded hover:bg-[#fbe7ff]">🔔 Notifications</a>
        <a href="profile.php" class="flex items-center px-3 py-2 rounded bg-[#fcd8f8] font-bold">👤 Profile</a>
        <div class="border-t border-pink-200 my-3"></div>
        <a href="logout.php" class="flex items-center px-3 py-2 rounded text-red-600 hover:bg-red-100">🚪 Logout</a>
    </nav>
</aside>

<!-- Main Content -->
<main class="flex-1 ml-64 p-8">
    <div class="max-w-4xl mx-auto bg-white/70 backdrop-blur-lg border border-pink-200 shadow-2xl p-10 rounded-2xl">
        <h2 class="text-4xl font-bold text-[#a3489b] mb-10 text-center drop-shadow">👤 Edit Profile</h2>

        <?php if ($success): ?>
            <div class="bg-green-100 text-green-800 p-4 mb-6 rounded-lg text-center"><?= $success ?></div>
        <?php elseif ($error): ?>
            <div class="bg-red-100 text-red-800 p-4 mb-6 rounded-lg text-center"><?= $error ?></div>
        <?php endif; ?>

        <!-- Update Profile Form -->
        <form method="POST" class="space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label class="block text-sm font-semibold text-[#742f79] mb-1">Name</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($user['name']) ?>" required
                           class="w-full border border-pink-300 bg-white rounded-lg px-4 py-3 text-[#742f79] focus:ring-2 focus:ring-pink-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[#742f79] mb-1">Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required
                           class="w-full border border-pink-300 bg-white rounded-lg px-4 py-3 text-[#742f79] focus:ring-2 focus:ring-pink-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[#742f79] mb-1">New Password <span class="text-xs text-gray-500">(optional)</span></label>
                    <input type="password" name="password"
                           class="w-full border border-pink-300 bg-white rounded-lg px-4 py-3 focus:ring-2 focus:ring-pink-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-[#742f79] mb-1">Confirm Password</label>
                    <input type="password" name="password_confirmation"
                           class="w-full border border-pink-300 bg-white rounded-lg px-4 py-3 focus:ring-2 focus:ring-pink-400 focus:outline-none">
                </div>
            </div>
            <div class="text-center">
                <button type="submit" name="update"
                        class="bg-[#a3489b] hover:bg-[#922c86] text-white font-semibold px-10 py-3 rounded-full shadow-lg transition">
                    Update Profile
                </button>
            </div>
        </form>

        <hr class="my-12 border-pink-300">

        <!-- Delete Account Form -->
        <form method="POST" onsubmit="return confirm('Are you sure you want to delete your account?')">
            <h3 class="text-2xl font-bold text-red-600 mb-4 text-center">⚠️ Delete Account</h3>
            <div class="max-w-md mx-auto space-y-4">
                <label class="block text-sm font-semibold text-red-600 mb-1">Confirm Password</label>
                <input type="password" name="confirm_password" required
                       class="w-full border border-red-300 bg-white rounded-lg px-4 py-3 focus:ring-2 focus:ring-red-400 focus:outline-none">
                <div class="text-center">
                    <button type="submit" name="delete"
                            class="bg-red-500 hover:bg-red-600 text-white font-semibold px-10 py-3 rounded-full shadow-md transition">
                        Delete Account
                    </button>
                </div>
            </div>
        </form>
    </div>
</main>
</body>
</html>
