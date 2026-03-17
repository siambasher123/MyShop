<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard - MyShop</title>

  <!-- ✅ Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- ✅ Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <!-- ✅ Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <style>
    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeInUp { animation: fadeInUp 0.6s ease-out; }
    tr:hover { background-color: #f9fafb !important; transition: all 0.2s ease; }
  </style>
</head>

<body class="bg-gray-100 text-gray-800 flex min-h-screen">

  <!-- ✅ SIDEBAR -->
  <aside class="bg-[#0d0d0d] text-white w-64 flex flex-col fixed top-0 left-0 h-full shadow-lg z-50">
    <div class="p-5 flex items-center space-x-2 border-b border-gray-700">
      <span class="text-2xl">🛍</span>
      <h1 class="text-xl font-bold tracking-wide">MyShop Admin</h1>
    </div>

    <nav class="flex-1 p-4 space-y-3">
      <a href="{{ route('admin.dashboard') }}" 
         class="block px-4 py-2 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('admin.dashboard') ? 'bg-gray-800' : '' }}">
        🏠 Dashboard
      </a>
      <a href="{{ route('admin.products') }}" 
         class="block px-4 py-2 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('admin.products') ? 'bg-gray-800' : '' }}">
        🛒 Products
      </a>
      <a href="{{ route('admin.discounts') }}"
         class="block px-4 py-2 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('admin.discounts') ? 'bg-gray-800' : '' }}">
        💸 Give Discount
      </a>
      <a href="{{ route('admin.orders') }}"
         class="block px-4 py-2 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('admin.orders') ? 'bg-gray-800' : '' }}">
        🧾 Orders
      </a>
      <a href="{{ route('admin.transactions') }}"
         class="block px-4 py-2 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('admin.transactions') ? 'bg-gray-800' : '' }}">
        💳 Transactions
      </a>
      <a href="{{ route('admin.reviews') }}"
   class="block px-4 py-2 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('admin.reviews') ? 'bg-gray-800' : '' }}">
  ⭐ Reviews
</a>

      <a href="{{ route('admin.inquiries') }}" 
         class="block px-4 py-2 rounded-lg hover:bg-gray-800 transition {{ request()->routeIs('admin.inquiries') ? 'bg-gray-800' : '' }}">
        📞 Contact Requests
      </a>
      <a href="{{ route('admin.clients') }}"
         class="block px-4 py-2.5 rounded-lg hover:bg-gray-800 transition duration-200 ease-in-out 
                {{ request()->routeIs('admin.clients') ? 'bg-gray-800' : '' }}">
        👥 Registered Clients
      </a>
    </nav>

    <!-- ✅ Logout fixed at bottom -->
    <div class="mt-auto p-4 border-t border-gray-700">
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" 
                class="w-full bg-white text-black font-semibold py-2 rounded-lg hover:bg-gray-200 transition text-sm">
          Logout
        </button>
      </form>
    </div>
  </aside>

  <!-- ✅ MAIN CONTENT -->
  <main class="ml-64 flex-1 p-10 animate-fadeInUp">
    <h1 class="text-4xl font-extrabold mb-8 text-gray-900">👑 Welcome, Admin!</h1>

    <!-- 🔹 Quick Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
      <div class="bg-white p-6 rounded-2xl shadow hover:shadow-xl transition">
        <h2 class="text-sm font-semibold text-gray-500 mb-1">Total Customers</h2>
        <p class="text-3xl font-bold text-indigo-600">
          {{ \DB::table('users')->where('role', 'user')->count() }}
        </p>
      </div>

      <div class="bg-white p-6 rounded-2xl shadow hover:shadow-xl transition">
        <h2 class="text-sm font-semibold text-gray-500 mb-1">Total Sales</h2>
        <p class="text-3xl font-bold text-green-600">
          ${{ number_format(\DB::table('orders1')->sum('total'), 2) }}
        </p>
      </div>

      <div class="bg-white p-6 rounded-2xl shadow hover:shadow-xl transition">
        <h2 class="text-sm font-semibold text-gray-500 mb-1">Pending Orders</h2>
        <p class="text-3xl font-bold text-yellow-500">
          {{ \DB::table('orders1')->where('status', 'pending')->count() }}
        </p>
      </div>

      <div class="bg-white p-6 rounded-2xl shadow hover:shadow-xl transition">
        <h2 class="text-sm font-semibold text-gray-500 mb-1">All Stock</h2>
        <p class="text-3xl font-bold text-blue-500">
          {{ \DB::table('products1')->sum('stock') }}
        </p>
      </div>
    </div>

    <!-- 🔹 Monthly Sales Overview Chart -->
    <div class="bg-white p-8 rounded-2xl shadow-lg mb-10">
      <h2 class="text-2xl font-bold mb-6 text-gray-800">📈 Monthly Sales Overview</h2>
      <canvas id="salesChart" height="100"></canvas>
    </div>

    <!-- 🔹 Recent Orders Table -->
    <div class="bg-white p-8 rounded-2xl shadow-lg">
      <h2 class="text-2xl font-bold mb-6 text-gray-800">🧾 Recent Orders</h2>

      @php
        $orders = \DB::table('orders1')
            ->select('id', 'full_name', 'status', 'total', 'created_at')
            ->latest()
            ->take(6)
            ->get();
      @endphp

      <div class="table-responsive">
        <table class="table align-middle table-hover">
          <thead class="bg-gray-200 text-gray-700">
            <tr>
              <th>Order ID</th>
              <th>Customer</th>
              <th>Date</th>
              <th>Status</th>
              <th>Total</th>
            </tr>
          </thead>
          <tbody>
            @foreach($orders as $order)
              <tr class="hover:bg-gray-100 transition">
                <td class="font-semibold text-gray-800">#{{ $order->id }}</td>
                <td class="text-gray-700">{{ $order->full_name }}</td>
                <td class="text-gray-600">{{ \Carbon\Carbon::parse($order->created_at)->format('M d, Y') }}</td>
                <td>
                  @if($order->status === 'delivered')
                    <span class="px-3 py-1 rounded-full text-white bg-green-600 text-sm font-medium">Delivered</span>
                  @elseif($order->status === 'pending')
                    <span class="px-3 py-1 rounded-full text-black bg-yellow-400 text-sm font-medium">Pending</span>
                  @elseif($order->status === 'cancelled')
                    <span class="px-3 py-1 rounded-full text-white bg-red-600 text-sm font-medium">Cancelled</span>
                  @else
                    <span class="px-3 py-1 rounded-full text-white bg-gray-500 text-sm font-medium">{{ ucfirst($order->status) }}</span>
                  @endif
                </td>
                <td class="font-semibold text-gray-800">${{ number_format($order->total, 2) }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </main>

  <!-- ✅ Chart.js - Gradient Line Chart -->
  <script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(79,70,229,0.4)');
    gradient.addColorStop(1, 'rgba(79,70,229,0.05)');

    new Chart(ctx, {
      type: 'line',
      data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
        datasets: [{
          label: 'Monthly Sales ($)',
          data: [1200, 1900, 2800, 3500, 4200, 5000, 6200],
          borderColor: '#4F46E5',
          backgroundColor: gradient,
          fill: true,
          tension: 0.4,
          borderWidth: 3,
          pointBackgroundColor: '#4338CA',
          pointBorderColor: '#fff',
          pointHoverRadius: 8,
          pointRadius: 5
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            display: true,
            labels: { color: '#1F2937', font: { size: 14, weight: 'bold' } }
          },
          tooltip: {
            backgroundColor: '#111827',
            titleColor: '#F9FAFB',
            bodyColor: '#E5E7EB',
            cornerRadius: 8,
            padding: 12
          }
        },
        scales: {
          x: { grid: { display: false }, ticks: { color: '#6B7280' } },
          y: { beginAtZero: true, ticks: { color: '#6B7280' }, grid: { color: '#E5E7EB' } }
        }
      }
    });
  </script>

</body>
</html>
