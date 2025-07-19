<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Ayaka Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;700&display=swap');
    body {
      font-family: 'Outfit', sans-serif;
      background: linear-gradient(to bottom, #c9f0ff, #eefbff);
      background-size: cover;
      overflow: hidden;
    }
    .glow {
      text-shadow: 0 0 10px #d4f1ff, 0 0 20px #89c2d9;
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

  <form method="POST" class="bg-[#2c3e50]/80 p-8 rounded-2xl shadow-2xl w-full max-w-sm space-y-5 z-10 backdrop-blur-md border border-blue-300">
    <h1 class="text-3xl font-bold text-center text-blue-100 glow">Ayaka Login</h1>
    <input name="email" type="email" required placeholder="Email" class="w-full p-3 rounded bg-white/10 text-white border border-blue-300 placeholder-blue-200 focus:outline-none focus:ring-2 focus:ring-purple-400">
    <input name="password" type="password" required placeholder="Password" class="w-full p-3 rounded bg-white/10 text-white border border-blue-300 placeholder-blue-200 focus:outline-none focus:ring-2 focus:ring-purple-400">
    <button name="login" class="w-full bg-gradient-to-r from-blue-400 to-purple-500 hover:from-blue-500 hover:to-purple-600 py-2 rounded text-white font-semibold transition-all duration-300">Login</button>
    <p class="text-sm text-center text-blue-200">Don't have an account? <a href="register.php" class="text-purple-300 hover:underline">Register</a></p>
  </form>

  <?php
  session_start();
  if (!isset($_SESSION['users'])) {
      $_SESSION['users'] = [
          ["name" => "Alice Example", "email" => "alice@example.com", "password" => password_hash("password1", PASSWORD_DEFAULT)],
          ["name" => "Bob Demo", "email" => "bob@demo.com", "password" => password_hash("password2", PASSWORD_DEFAULT)],
      ];
  }
  $error = '';
  if (isset($_POST['login'])) {
      $email = $_POST['email'];
      $password = $_POST['password'];
      $found = false;
      foreach ($_SESSION['users'] as $user) {
          if ($user['email'] === $email && password_verify($password, $user['password'])) {
              $_SESSION['user'] = $user;
              $found = true;
              break;
          }
      }
      if ($found) {
          // Add a notification
          if (!isset($_SESSION['notifications'])) $_SESSION['notifications'] = [];
          $_SESSION['notifications'][] = ["user_email" => $email, "message" => "Logged in!"];
          header("Location: dashboard.php");
          exit;
      } else {
          $error = "Invalid email or password.";
      }
  }
  ?>
  <?php if ($error): ?>
    <p class='text-red-200 absolute bottom-5 text-center w-full'><?= $error ?></p>
  <?php endif; ?>

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
        snowflake.textContent = ["❄️", "❅", "❆"][Math.floor(Math.random() * 3)];
        snowContainer.appendChild(snowflake);
      }
    });
  </script>

</body>
</html> 