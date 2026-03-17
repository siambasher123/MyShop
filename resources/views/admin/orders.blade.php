<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Orders - MyShop</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    @keyframes fadeIn { from {opacity:0; transform:translateY(10px);} to {opacity:1; transform:translateY(0);} }
    .animate-fadeIn { animation: fadeIn 0.5s ease-out; }
    .card-hover:hover { transform: translateY(-3px); transition: 0.3s; box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
    .table thead th { text-transform: uppercase; font-size: 13px; font-weight: 600; color: #374151; background-color: #f9fafb; }
    .table td { vertical-align: middle; }
    .badge { font-size: 0.75rem; padding: 0.4em 0.6em; }
    .dark { background-color: #111827; color: #f9fafb; }
    .dark .bg-white { background-color: #1f2937 !important; color: #f9fafb !important; }
    .dark .table-striped>tbody>tr:nth-of-type(odd) { --bs-table-accent-bg: #1f2937; }
    .dark .table-light { background-color: #374151 !important; color: #f9fafb !important; }
  </style>
</head>

<body class="bg-gray-50 text-gray-800 flex min-h-screen transition-all duration-500">
  @include('admin.sidebar')

  <main class="ml-64 flex-1 p-10 animate-fadeIn">

    <div class="text-center mb-10">
      <h1 class="text-4xl font-extrabold text-gray-900 mb-2">📦 Orders Management</h1>
     
    </div>

    @php
      $totalOrders = count($orders);
      $accepted = $orders->where('status', 'accepted')->count();
      $pending = $orders->where('status', 'pending')->count();
      $rejected = $orders->where('status', 'rejected')->count();
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
      <div class="bg-gradient-to-r from-blue-500 to-indigo-500 text-white text-center p-6 rounded-2xl shadow-lg card-hover">
        <h2 class="text-3xl font-extrabold">{{ $totalOrders }}</h2>
        <p class="text-sm mt-1">Total Orders</p>
      </div>
      <div class="bg-gradient-to-r from-yellow-400 to-amber-500 text-white text-center p-6 rounded-2xl shadow-lg card-hover">
        <h2 class="text-3xl font-extrabold">{{ $pending }}</h2>
        <p class="text-sm mt-1">Pending Orders</p>
      </div>
      <div class="bg-gradient-to-r from-green-500 to-emerald-600 text-white text-center p-6 rounded-2xl shadow-lg card-hover">
        <h2 class="text-3xl font-extrabold">{{ $accepted }}</h2>
        <p class="text-sm mt-1">Accepted Orders</p>
      </div>
      <div class="bg-gradient-to-r from-red-500 to-rose-600 text-white text-center p-6 rounded-2xl shadow-lg card-hover">
        <h2 class="text-3xl font-extrabold">{{ $rejected }}</h2>
        <p class="text-sm mt-1">Rejected Orders</p>
      </div>
    </div>

    <div class="mb-10">
      <div class="mt-2 bg-gray-200 rounded-full h-3 w-full overflow-hidden">
        <div class="bg-green-500 h-3 rounded-full transition-all duration-500" 
             style="width: {{ ($totalOrders ? ($accepted / $totalOrders) * 100 : 0) }}%">
        </div>
      </div>
      <p class="text-sm text-gray-600 mt-1">
        {{ round($totalOrders ? ($accepted / $totalOrders) * 100 : 0) }}% of orders accepted
      </p>
    </div>

    <div class="flex flex-wrap justify-between items-center mb-5 gap-3">
      <input type="text" id="searchBox" placeholder="🔍 Search by customer..." class="form-control w-full sm:w-1/3">
      <select id="statusFilter" class="form-select w-full sm:w-1/4">
        <option value="">All Status</option>
        <option value="accepted">Accepted</option>
        <option value="pending">Pending</option>
        <option value="rejected">Rejected</option>
      </select>
    </div>

    <div class="bg-white shadow-xl rounded-3xl overflow-hidden border border-gray-200 transition-all duration-500">
      <div class="bg-gray-800 text-white px-6 py-3 text-center text-lg fw-semibold">All Recent Orders</div>

      <div class="overflow-x-auto">
        <table class="table table-striped table-hover align-middle mb-0">
          <thead>
            <tr>
              <th>Serial</th>
              <th>Customer</th>
              <th>Phone</th>
              <th>Address</th>
              <th>View Products</th>
              <th>Codes</th>
              <th>Qty</th>
              <th>Total ($)</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse($orders as $order)
              @php
                $products = is_string($order->products)
                    ? json_decode($order->products, true)
                    : ($order->products ?? []);
                $totalQty = collect($products ?? [])->sum('quantity');
                $codes = is_string($order->product_codes)
                    ? json_decode($order->product_codes, true)
                    : ($order->product_codes ?? []);
              @endphp

              <tr class="transition transform hover:scale-[1.01] hover:bg-gray-50">
                <td class="fw-bold">#{{ $order->id }}</td>
                <td>
                  <div>
                    <span class="fw-semibold">{{ $order->full_name }}</span><br>
                    <small class="text-muted">{{ $order->email }}</small>
                  </div>
                </td>
                <td>{{ $order->mobile_number }}</td>
                <td>{{ Str::limit($order->address, 35) }}</td>
                <td>
                  <button class="btn btn-sm btn-outline-dark" data-bs-toggle="modal" data-bs-target="#modal{{ $order->id }}">
                    View Products
                  </button>
                </td>
                <td>
                  @if(!empty($codes))
                    <div class="d-flex flex-wrap gap-1">
                      @foreach($codes as $code)
                        <span class="badge bg-dark copy-code cursor-pointer" data-code="{{ $code }}">{{ $code }}</span>
                      @endforeach
                    </div>
                  @else
                    <span class="text-muted">—</span>
                  @endif
                </td>
                <td class="text-center fw-semibold">{{ $totalQty }}</td>
                <td class="fw-semibold">${{ number_format($order->total, 2) }}</td>
                <td>
                  @if($order->status === 'accepted')
                    <span class="badge bg-success">Accepted</span>
                  @elseif($order->status === 'rejected')
                    <span class="badge bg-danger">Rejected</span>
                  @else
                    <span class="badge bg-warning text-dark">Pending</span>
                  @endif
                </td>
                <td>
                  <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="status" value="accepted">
                    <button type="submit" class="btn btn-success btn-sm px-3 fw-semibold update-status">Accept</button>
                  </form>
                  <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="status" value="rejected">
                    <button type="submit" class="btn btn-danger btn-sm px-3 fw-semibold update-status">Reject</button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="10" class="text-center py-5 text-muted">
                  <img src="https://cdn-icons-png.flaticon.com/512/4076/4076500.png" class="w-16 mx-auto mb-3 opacity-70">
                  <p>No orders found yet.</p>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- Product Modals --}}
    @foreach($orders as $order)
      @php
        $products = is_string($order->products)
            ? json_decode($order->products, true)
            : ($order->products ?? []);
      @endphp

      <div class="modal fade" id="modal{{ $order->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
          <div class="modal-content rounded-3 shadow-lg">
            <div class="modal-header bg-dark text-white">
              <h5 class="modal-title">🛍 Products in Order #{{ $order->id }}</h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              @if(!empty($products))
                <table class="table table-bordered align-middle">
                  <thead class="table-light">
                    <tr>
                      <th>Product</th>
                      <th>Price</th>
                      <th>Qty</th>
                      <th>Total</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($products as $product)
                      <tr>
                        <td>{{ $product['name'] ?? 'N/A' }}</td>
                        <td>${{ number_format($product['price'], 2) }}</td>
                        <td>{{ $product['quantity'] ?? 1 }}</td>
                        <td>${{ number_format(($product['price'] ?? 0) * ($product['quantity'] ?? 1), 2) }}</td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              @else
                <p class="text-center text-muted">No product data found.</p>
              @endif
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>
    @endforeach

  </main>

  <div id="toast" class="fixed top-5 right-5 bg-green-600 text-white px-4 py-2 rounded-lg shadow-lg hidden z-50">
    ✅ Action completed!
  </div>

  <button id="themeToggle" class="fixed bottom-5 right-5 bg-gray-800 text-white p-3 rounded-full shadow-lg z-50">
    🌙
  </button>

  <script>
    // ✅ Copy Codes
    document.addEventListener("DOMContentLoaded", () => {
      document.querySelectorAll(".copy-code").forEach(el => {
        el.addEventListener("click", () => {
          navigator.clipboard.writeText(el.dataset.code);
          showToast("Code copied to clipboard!");
          el.innerText = "Copied!";
          el.classList.add("bg-success");
          setTimeout(() => {
            el.innerText = el.dataset.code;
            el.classList.remove("bg-success");
          }, 1000);
        });
      });
    });

    // ✅ Toast
    function showToast(message) {
      const toast = document.getElementById('toast');
      toast.textContent = message;
      toast.classList.remove('hidden');
      setTimeout(() => toast.classList.add('hidden'), 2000);
    }

    // ✅ Live Search + Filter
    const searchBox = document.getElementById('searchBox');
    const statusFilter = document.getElementById('statusFilter');

    function filterTable() {
      const search = searchBox.value.toLowerCase();
      const status = statusFilter.value;
      document.querySelectorAll('tbody tr').forEach(row => {
        const name = row.querySelector('td:nth-child(2)')?.innerText.toLowerCase();
        const rowStatus = row.querySelector('td:nth-child(9)')?.innerText.toLowerCase();
        const matches = (!status || rowStatus.includes(status)) && (!search || name.includes(search));
        row.style.display = matches ? '' : 'none';
      });
    }

    searchBox.addEventListener('input', filterTable);
    statusFilter.addEventListener('change', filterTable);

    // ✅ Counter + Progress Bar Direct Update
    function updateCounterDirect(changeType) {
      const acceptedEl = document.querySelector('.from-green-500 h2');
      const pendingEl = document.querySelector('.from-yellow-400 h2');
      const rejectedEl = document.querySelector('.from-red-500 h2');
      const bar = document.querySelector('.bg-green-500.h-3');
      const text = document.querySelector('.text-sm.text-gray-600.mt-1');

      let accepted = parseInt(acceptedEl.textContent);
      let pending = parseInt(pendingEl.textContent);
      let rejected = parseInt(rejectedEl.textContent);

      if (changeType === 'accepted') {
        accepted++;
        pending = Math.max(0, pending - 1);
      } else if (changeType === 'rejected') {
        rejected++;
        pending = Math.max(0, pending - 1);
      }

      acceptedEl.textContent = accepted;
      pendingEl.textContent = pending;
      rejectedEl.textContent = rejected;

      const total = accepted + pending + rejected;
      const percent = total ? Math.round((accepted / total) * 100) : 0;
      bar.style.width = percent + "%";
      text.textContent = percent + "% of orders accepted";
    }

    // ✅ AJAX Order Status Update + Counter Adjust
    document.querySelectorAll('.update-status').forEach(button => {
      button.addEventListener('click', async e => {
        e.preventDefault();
        const form = e.target.closest('form');
        const statusValue = form.querySelector('input[name="status"]').value;
        const res = await fetch(form.action, {
          method: 'POST',
          headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
          body: new FormData(form)
        });

        if (res.ok) {
          const row = e.target.closest('tr');
          const statusCell = row.querySelector('td:nth-child(9)');
          if (statusValue === 'accepted') {
            statusCell.innerHTML = '<span class="badge bg-success">Accepted</span>';
          } else if (statusValue === 'rejected') {
            statusCell.innerHTML = '<span class="badge bg-danger">Rejected</span>';
          }
          showToast("Order updated successfully!");
          row.classList.add('bg-green-50');
          updateCounterDirect(statusValue); // 🔥 Update counters instantly
        }
      });
    });

    // ✅ Dark Mode
    const body = document.body;
    const toggle = document.getElementById('themeToggle');
    toggle.onclick = () => {
      body.classList.toggle('dark');
      localStorage.setItem('theme', body.classList.contains('dark') ? 'dark' : 'light');
    };
    if(localStorage.getItem('theme') === 'dark') body.classList.add('dark');
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
