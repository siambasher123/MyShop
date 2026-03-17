<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login - MyShop</title>
  <script src="https://cdn.tailwindcss.com"></script>

  <meta name="csrf-token" content="{{ csrf_token() }}">

  <style>
    @keyframes fadeInUp {
      0% { opacity: 0; transform: translateY(20px); }
      100% { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeInUp { animation: fadeInUp 0.6s ease-out; }
    .fade-out { opacity: 0; transition: opacity 0.5s ease; }
  </style>
</head>

<body class="bg-gradient-to-br from-gray-100 via-gray-200 to-gray-300 text-gray-800">

  <!-- NAVBAR -->
  @include('partials.navbar')

  <!-- SUCCESS MESSAGE (from register) -->
  @if (session('success'))
  <div id="flash-message"
       class="fixed top-24 left-1/2 transform -translate-x-1/2 w-full max-w-md bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded-lg shadow-md text-center animate-fadeInUp z-50">
      <strong class="block font-semibold">{{ session('success') }}</strong>
  </div>
  <script>
    setTimeout(() => {
      const flash = document.getElementById('flash-message');
      if (flash) flash.remove();
    }, 3000);
  </script>
  @endif

  <!-- LOGIN FORM -->
  <section class="min-h-screen flex items-center justify-center px-6">
    <div class="bg-white/80 backdrop-blur-md p-10 rounded-2xl shadow-2xl w-full max-w-md border border-gray-200 animate-fadeInUp transition-all duration-300 hover:shadow-3xl hover:scale-[1.02]">
      <h2 class="text-3xl font-bold text-center mb-6 text-gray-900">Welcome Back 👋</h2>

      @if ($errors->any())
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4 text-sm text-center">
        {{ $errors->first() }}
      </div>
      @endif

      <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
        @csrf
        <div>
          <label for="email" class="block font-medium text-gray-700 mb-2">Email Address</label>
          <input type="email" id="email" name="email" required
                 class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none shadow-sm hover:shadow-md transition">
        </div>

        <div>
          <label for="password" class="block font-medium text-gray-700 mb-2">Password</label>
          <input type="password" id="password" name="password" required
                 class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-indigo-500 focus:outline-none shadow-sm hover:shadow-md transition">
        </div>

        <div class="flex items-center justify-between text-sm">
          <label class="flex items-center space-x-2">
            <input type="checkbox" name="remember" class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
            <span>Remember me</span>
          </label>
          <a href="#" id="forgotPassword" class="text-indigo-600 hover:underline">Forgot Password?</a>
        </div>

        <button type="submit"
                class="w-full bg-gradient-to-r from-black via-gray-900 to-gray-700 text-white font-semibold py-2.5 rounded-lg hover:from-gray-800 hover:to-black transition duration-300 shadow-md hover:shadow-xl">
          Login
        </button>
      </form>

      <p class="text-center text-sm text-gray-700 mt-6">
        Don’t have an account?
        <a href="{{ route('register') }}" class="text-indigo-600 font-medium hover:underline hover:text-indigo-700 transition">
          Sign Up
        </a>
      </p>
    </div>
  </section>

  <!-- EMAIL MESSAGE POPUP -->
  <div id="emailMessage"
       class="hidden fixed top-24 left-1/2 transform -translate-x-1/2 w-full max-w-md px-4 py-3 rounded-lg shadow-md text-center animate-fadeInUp z-50"></div>

  <script>
    document.getElementById('forgotPassword').addEventListener('click', async function (e) {
      e.preventDefault();
      const emailInput = document.getElementById('email');
      const msg = document.getElementById('emailMessage');
      const email = emailInput.value.trim();

      // Validate email format
      const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!email || !emailPattern.test(email)) {
        showMessage('Please enter a valid email address!', 'red');
        return;
      }

      try {
        const token = document.querySelector('meta[name="csrf-token"]').content;
        const response = await fetch("{{ url('/check-email') }}", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": token,
            "Accept": "application/json"
          },
          body: JSON.stringify({ email })
        });

        if (!response.ok) throw new Error('Network error');
        const data = await response.json();

        if (data.exists) {
          showMessage('An email has been sent to your address!', 'blue');
        } else {
          showMessage('Email not found in our records!', 'red');
        }
      } catch (error) {
        console.error(error);
        showMessage('Server error. Please try again later.', 'red');
      }

      function showMessage(text, color) {
        msg.classList.remove('hidden', 'fade-out');
        msg.innerHTML = `<strong class="block font-semibold">${text}</strong>`;
        msg.className = `fixed top-24 left-1/2 transform -translate-x-1/2 w-full max-w-md ${color === 'blue'
          ? 'bg-blue-100 border border-blue-400 text-blue-800'
          : 'bg-red-100 border border-red-400 text-red-800'} px-4 py-3 rounded-lg shadow-md text-center animate-fadeInUp z-50`;

        setTimeout(() => {
          msg.classList.add('fade-out');
          setTimeout(() => msg.classList.add('hidden'), 500);
        }, 2000);
      }
    });
  </script>

</body>
</html>
