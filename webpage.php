<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Welcome to Booking System</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Inter:wght@400;600&display=swap');

    body {
      font-family: 'Inter', sans-serif;
    }

    .font-serif {
      font-family: 'Playfair Display', serif;
    }

    .glass {
      background: rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border: 1px solid rgba(255, 255, 255, 0.25);
    }
  </style>
</head>
<body class="bg-[#bcdffb] min-h-screen flex items-center justify-center">
  <div class="glass p-10 rounded-3xl shadow-xl max-w-2xl w-full mx-4 text-center text-gray-800">
    <h1 class="text-4xl md:text-5xl font-bold mb-2 font-serif text-[#3b5ca4]">
      Welcome to
    </h1>
    <h2 class="text-5xl md:text-6xl font-bold mb-6 font-serif text-[#7c9ee5]">
      Booking System
    </h2>

    <p class="text-lg leading-relaxed mb-10 italic text-gray-700">
      ❄️ Feel the elegance of Kamisato Ayaka.<br/>
      Please register or log in to continue.
    </p>

    <div class="flex flex-col sm:flex-row justify-center gap-4">
      <a href="register.php"
        class="bg-gradient-to-r from-[#7c9ee5] to-[#c5b4e3] hover:from-[#6b8cd7] hover:to-[#b59ce0] text-white font-semibold py-3 px-10 rounded-full text-lg transition duration-300 transform hover:scale-105 shadow-md">
        Register
      </a>
      <a href="index.php"
        class="bg-white hover:bg-gray-100 text-[#3b5ca4] font-semibold py-3 px-10 rounded-full text-lg transition duration-300 transform hover:scale-105 shadow-md">
        Login
      </a>
    </div>
  </div>
</body>
</html>
