<?php
ob_start();
session_start();
// Static users array for demo (in-memory)
if (!isset($_SESSION['users'])) {
    $_SESSION['users'] = [
        ["name" => "Alice Example", "email" => "alice@example.com", "password" => password_hash("password1", PASSWORD_DEFAULT)],
        ["name" => "Bob Demo", "email" => "bob@demo.com", "password" => password_hash("password2", PASSWORD_DEFAULT)],
    ];
}
$success = $error = '';
if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    if ($password !== $confirm_password) {
        $error = "Passwords do not match 🥲";
    } else {
        $password_hashed = password_hash($password, PASSWORD_DEFAULT);
        $exists = false;
        foreach ($_SESSION['users'] as $user) {
            if ($user['email'] === $email) {
                $exists = true;
                break;
            }
        }
        if ($exists) {
            $error = "Email already exists 💌";
        } else {
            $_SESSION['users'][] = ["name" => $name, "email" => $email, "password" => $password_hashed];
            $success = "Registered successfully 💜 <a href='login.php' class='underline'>Login</a>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Kuromi Register</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;700&display=swap');
    body {
      font-family: 'Outfit', sans-serif;
      background: linear-gradient(to bottom right, #1e1b2e, #5e4b8b, #f8b4d6);
      background-size: cover;
      overflow: hidden;
    }
    .glow {
      text-shadow: 0 0 10px #f9a8d4, 0 0 20px #a78bfa;
    }
    .snowflake {
      position: fixed;
      top: -2rem;
      color: #ffffffcc;
      user-select: none;
      font-size: 1rem;
      z-index: 50;
      animation: fall linear infinite;
      pointer-events: none;
    }
    @keyframes fall {
      0% { transform: translateY(-2rem) rotate(0deg); opacity: 1; }
      100% { transform: translateY(100vh) rotate(360deg); opacity: 0; }
    }
  </style>
</head>
<body class="flex items-center justify-center min-h-screen text-white relative">

  <!-- Kuromi Registration Box -->
  <form method="POST" class="bg-[#2a223a]/80 p-8 rounded-2xl shadow-2xl w-full max-w-sm space-y-5 z-10 backdrop-blur-md border border-pink-300">
    <div class="text-center space-y-1">
      
      <h1 class="text-3xl font-bold text-pink-200 glow">Register</h1>
      <p class="text-sm italic text-pink-300">Welcome to the cute & chaotic side 🖤</p>
    </div>

    <input name="name" type="text" required placeholder="Name"
      class="w-full p-3 rounded bg-white/10 text-white border border-pink-300 placeholder-pink-200 focus:outline-none focus:ring-2 focus:ring-purple-400">

    <input name="email" type="email" required placeholder="Email"
      class="w-full p-3 rounded bg-white/10 text-white border border-pink-300 placeholder-pink-200 focus:outline-none focus:ring-2 focus:ring-purple-400">

    <input name="password" type="password" required placeholder="Password"
      class="w-full p-3 rounded bg-white/10 text-white border border-pink-300 placeholder-pink-200 focus:outline-none focus:ring-2 focus:ring-purple-400">

    <input name="confirm_password" type="password" required placeholder="Confirm Password"
      class="w-full p-3 rounded bg-white/10 text-white border border-pink-300 placeholder-pink-200 focus:outline-none focus:ring-2 focus:ring-purple-400">

    <button name="register"
      class="w-full bg-gradient-to-r from-pink-500 to-purple-500 hover:from-pink-400 hover:to-purple-600 py-2 rounded text-white font-semibold transition-all duration-300">
      Register
    </button>

    <p class="text-sm text-center text-pink-200">Already have an account?
      <a href="login.php" class="text-purple-300 hover:underline">Login</a>
    </p>
  </form>

  <!-- Display Messages -->
  <?php if ($success): ?>
    <p class='text-green-200 absolute bottom-5 text-center w-full'><?= $success ?></p>
  <?php elseif ($error): ?>
    <p class='text-red-200 absolute bottom-5 text-center w-full'><?= $error ?></p>
  <?php endif; ?>

  <!-- Snowflake Animation -->
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const snowContainer = document.createElement("div");
      document.body.appendChild(snowContainer);
      for (let i = 0; i < 50; i++) {
        const snowflake = document.createElement("div");
        snowflake.className = "snowflake";
        snowflake.style.left = Math.random() * 100 + "vw";
        snowflake.style.animationDuration = (5 + Math.random() * 10) + "s";
        snowflake.style.fontSize = (12 + Math.random() * 18) + "px";
        snowflake.textContent = ["🖤", "🎀", "✨"][Math.floor(Math.random() * 3)];
        snowContainer.appendChild(snowflake);
      }
    });
  </script>

</body>
</html>
