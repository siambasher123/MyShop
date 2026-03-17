<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>MyShop - E-Commerce</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#f8f8f8] text-gray-800">

  <!-- NAVBAR -->
  <header class="bg-[#0d0d0d] sticky top-0 z-50 shadow-md relative">

    <!-- LEFT: LOGO -->
    <div class="absolute top-3 left-6 flex items-center space-x-2">
      <span class="text-2xl">🛍</span>
      <h1 class="text-2xl font-bold text-white tracking-wide">MyShop</h1>
    </div>

    <!-- MAIN NAVBAR CONTENT -->
    <div class="max-w-7xl mx-auto px-6 py-3 flex items-center justify-center">
      <div class="flex items-center space-x-6">
        <nav class="hidden lg:flex space-x-6 font-medium text-gray-300 relative">

          @php
            $categories = [
              'Men' => ['T-Shirts', 'Jeans', 'Punjabi', 'Formal Shirts', 'Jackets'],
              'Women' => ['Sarees', 'Kurtis', 'Tops', 'Dresses', 'Three-Pieces'],
              'Kids' => ['Boys’ Wear', 'Girls’ Wear', 'Baby Wear', 'School Uniforms', 'Accessories'],
              'Winter' => ['Sweaters', 'Hoodies', 'Jackets', 'Scarves', 'Thermals'],
              'Jewellery' => ['Necklaces', 'Earrings', 'Bracelets', 'Rings', 'Anklets'],
              'Shoes' => ['Sneakers', 'Formal Shoes', 'Sandals', 'Sports Shoes', 'Boots'],
              'Home Decor' => ['Wall Art', 'Lamps', 'Cushions', 'Vases', 'Clocks'],
              'Perfumes' => ['Men’s Perfumes', 'Women’s Perfumes', 'Unisex Fragrances', 'Body Mists', 'Deodorants']
            ];
          @endphp

          @foreach($categories as $cat => $subs)
            <div class="group relative">
              <a href="{{ route('shop.category', ['category' => urlencode($cat)]) }}" class="hover:text-white transition">
                {{ $cat }}
              </a>

              <!-- DROPDOWN MENU -->
              <div 
  class="absolute opacity-0 group-hover:opacity-100 hover:opacity-100 
         pointer-events-none group-hover:pointer-events-auto hover:pointer-events-auto 
         bg-white text-gray-800 rounded-lg mt-1 shadow-lg py-2 w-52 z-50 
         transition duration-200 ease-out"
>

                @foreach($subs as $sub)
                  <a href="{{ route('shop.subcategory', ['category' => urlencode($cat), 'subcategory' => urlencode($sub)]) }}"
                     class="block px-4 py-2 hover:bg-gray-100 whitespace-nowrap">
                    {{ $sub }}
                  </a>
                @endforeach
              </div>
            </div>
          @endforeach

        </nav>

        <!-- SEARCH BAR -->
        <form action="{{ route('find') }}" method="GET" class="relative hidden md:block w-64">
          <input 
            name="q" 
            type="text" 
            placeholder="Search products..."
            class="w-full rounded-full px-4 py-1.5 bg-white text-gray-800 focus:ring-2 focus:ring-indigo-500 focus:outline-none placeholder-gray-500 text-sm"
          >
          <button type="submit" class="absolute right-3 top-1.5 text-gray-500 hover:text-indigo-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-4.35-4.35m1.35-5.65a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </button>
        </form>
      </div>
    </div>

    <!-- RIGHT: CART + CONTACT + LOGIN -->
    <div class="absolute top-3 right-6 flex items-center space-x-6">
      <a href="{{ route('cart.index') }}" class="relative text-gray-200 hover:text-white" title="Cart">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 9m10-9l2 9m-6-9v9" />
        </svg>
        <span class="absolute -top-2 -right-2 bg-white text-black text-xs px-1 rounded-full">
          {{ count(session('cart', [])) }}
        </span>
      </a>

      <a href="{{ route('contact.form') }}" 
         class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-semibold px-4 py-1.5 rounded-lg hover:scale-105 hover:shadow-md transition text-sm">
        Contact Us
      </a>

      @auth
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="bg-white text-black font-semibold px-4 py-1.5 rounded-lg hover:bg-gray-200 transition text-sm">
            Logout
          </button>
        </form>
      @else
        <a href="{{ route('login') }}" 
           class="bg-white text-black font-semibold px-4 py-1.5 rounded-lg hover:bg-gray-200 transition text-sm">
          Login
        </a>
      @endauth
    </div>

  </header>

  <!-- HERO -->
  <section class="bg-gradient-to-r from-gray-100 to-gray-200 py-20">
    <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row items-center justify-between">
      <div class="mb-10 md:mb-0">
        <h2 class="text-4xl md:text-5xl font-bold mb-4 text-gray-900">Shop the Latest Trends</h2>
        <p class="text-lg mb-6 text-gray-600">Discover premium fashion, accessories, and fragrances designed for you.</p>
        <a href="{{ route('shop.index') }}" class="bg-black text-white px-6 py-3 rounded-lg font-semibold hover:bg-gray-800 transition">
          Shop Now
        </a>
      </div>
      <img src="https://images.unsplash.com/photo-1542291026-7eec264c27ff?auto=format&fit=crop&w=800&q=80"
        alt="Fashion" class="rounded-2xl shadow-xl w-full md:w-1/2">
    </div>
  </section>

  <!-- CATEGORY GRID -->
  <section class="max-w-7xl mx-auto px-6 py-16">
    <h3 class="text-3xl font-bold text-center mb-10 text-gray-900">Shop by Category</h3>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
      @foreach ([ 
        ['Men','https://i.ebayimg.com/images/g/kVEAAOSw729jR3za/s-l1200.jpg'],
        ['Women','https://resources.mandmdirect.com/Images/_default/o/y/3/oy30835_1_cloudzoom.jpg'],
        ['Kids','https://images.pexels.com/photos/1620760/pexels-photo-1620760.jpeg?auto=compress&cs=tinysrgb&dpr=1&w=500'],
        ['Perfumes','https://e0.pxfuel.com/wallpapers/977/266/desktop-wallpaper-expensive-perfume.jpg']
      ] as [$title,$img])
      <a href="{{ route('shop.category', ['category' => urlencode($title)]) }}" 
         class="relative group overflow-hidden rounded-xl shadow-md hover:shadow-xl transition">
        <img src="{{ $img }}?auto=format&fit=crop&w=500&q=60" alt="{{ $title }}"
             class="w-full h-64 object-cover group-hover:scale-110 transition">
        <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
          <p class="text-white text-lg font-semibold">{{ $title }}</p>
        </div>
      </a>
      @endforeach
    </div>
  </section>

  <!-- FOOTER -->
  <footer class="bg-[#0d0d0d] text-gray-300 py-10 mt-10">
    <div class="max-w-7xl mx-auto px-6 text-center">
      <h4 class="text-lg font-semibold mb-3 text-white">Follow Us</h4>
      <div class="flex justify-center space-x-6 mb-6">
        <a href="#" class="hover:text-white transition">Facebook</a>
        <a href="#" class="hover:text-white transition">Instagram</a>
        <a href="#" class="hover:text-white transition">Twitter</a>
      </div>
      <p class="text-sm">&copy; 2025 MyShop. All rights reserved.</p>
    </div>
  </footer>

 <!-- 🧠 Floating AI Chatbot -->
<div 
  x-data="{ 
    open: false, 
    messages: [{ from: 'bot', text: '👋 Hi! I\'m your shopping assistant. Ask me anything!' }], 
    input: '' 
  }"
  class="fixed bottom-6 right-6 z-50"
  x-init="$watch('messages', () => { $nextTick(() => { let chatBox = $refs.chatScroll; chatBox.scrollTop = chatBox.scrollHeight; }); })"
>
  <!-- Chat toggle button -->
  <button 
    @click="open = !open"
    class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-full w-14 h-14 shadow-lg flex items-center justify-center focus:outline-none transition-transform duration-300 hover:scale-110"
  >
    💬
  </button>

  <!-- Chat window -->
  <div 
    x-show="open"
    x-transition
    class="absolute bottom-16 right-0 w-80 bg-white rounded-2xl shadow-2xl border border-gray-200 flex flex-col overflow-hidden"
  >
    <div class="bg-indigo-600 text-white px-4 py-3 flex justify-between items-center">
      <h3 class="font-semibold text-sm">🛍 MyShop AI Assistant</h3>
      <button @click="open = false" class="text-white hover:text-gray-200">&times;</button>
    </div>

    <!-- Message area -->
    <div class="flex-1 overflow-y-auto px-4 py-3 space-y-3 text-sm" x-ref="chatScroll" style="max-height: 300px;">
      <template x-for="(msg, index) in messages" :key="index">
        <div :class="msg.from === 'bot' ? 'text-left' : 'text-right'">
          <div 
            :class="msg.from === 'bot' 
              ? 'bg-gray-100 text-gray-800 inline-block px-3 py-2 rounded-lg rounded-tl-none'
              : 'bg-indigo-600 text-white inline-block px-3 py-2 rounded-lg rounded-tr-none'">
            <span x-text="msg.text"></span>
          </div>
        </div>
      </template>
    </div>

    <!-- Input area -->
    <div class="border-t px-3 py-2 bg-gray-50 flex items-center">
      <input 
        type="text" 
        x-model="input" 
        @keydown.enter="
          if (input.trim() !== '') {
            messages.push({ from: 'user', text: input });
            let q = input;
            input = '';
            messages.push({ from: 'bot', text: '💭 Typing...' });
            fetch('{{ route('ai.chat') }}', {
              method: 'POST',
              headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              body: JSON.stringify({ message: q })
            })
            .then(res => res.json())
            .then(data => {
              messages.pop();
              messages.push({ from: 'bot', text: data.reply });
            })
            .catch(() => {
              messages.pop();
              messages.push({ from: 'bot', text: '⚠️ Connection error, please try again.' });
            });
          }
        " 
        placeholder="Ask me something..." 
        class="flex-1 border-none focus:ring-0 bg-transparent text-sm text-gray-700 placeholder-gray-400 outline-none"
      >
      <button 
        @click="
          if (input.trim() !== '') {
            messages.push({ from: 'user', text: input });
            let q = input;
            input = '';
            messages.push({ from: 'bot', text: '💭 Typing...' });
            fetch('{{ route('ai.chat') }}', {
              method: 'POST',
              headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              },
              body: JSON.stringify({ message: q })
            })
            .then(res => res.json())
            .then(data => {
              messages.pop();
              messages.push({ from: 'bot', text: data.reply });
            })
            .catch(() => {
              messages.pop();
              messages.push({ from: 'bot', text: '⚠️ Connection error, please try again.' });
            });
          }
        "
        class="text-indigo-600 font-semibold px-2"
      >
        Send
      </button>
    </div>
  </div>
</div>


  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</body>
</html>
