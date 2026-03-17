<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Registered Clients - MyShop</title>

  {{-- Tailwind + Bootstrap --}}
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    @keyframes fadeIn { from {opacity:0; transform:translateY(10px);} to {opacity:1; transform:translateY(0);} }
    .animate-fadeIn { animation: fadeIn 0.5s ease-out; }
    .card-hover:hover { transform: translateY(-3px); transition: 0.3s; box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
    .table thead th { text-transform: uppercase; font-size: 13px; font-weight: 600; color: #374151; background-color: #f9fafb; }
    .dark { background-color: #111827; color: #f9fafb; }
    .dark .bg-white { background-color: #1f2937 !important; color: #f9fafb !important; }
    .dark .table-striped>tbody>tr:nth-of-type(odd) { --bs-table-accent-bg: #1f2937; }
  </style>
</head>

<body class="bg-gray-50 text-gray-800 flex min-h-screen transition-all duration-500">

  {{-- Sidebar --}}
  @include('admin.sidebar')

  {{-- Main --}}
  <main class="ml-64 flex-1 p-10 animate-fadeIn">

    <div class="text-center mb-10">
      <h1 class="text-4xl font-extrabold text-gray-900 mb-2">👥 Registered Clients</h1>
      <p class="text-gray-500">Manage your customers interactively</p>
    </div>

    {{-- Stats --}}
    @php
      $total = $clients->count();
      $new = $clients->whereBetween('created_at', [now()->subDays(7), now()])->count();
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
      <div class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white text-center p-6 rounded-2xl shadow-lg card-hover">
        <h2 class="text-3xl font-extrabold">{{ $total }}</h2>
        <p class="text-sm mt-1">Total Clients</p>
      </div>
      <div class="bg-gradient-to-r from-green-500 to-emerald-600 text-white text-center p-6 rounded-2xl shadow-lg card-hover">
        <h2 class="text-3xl font-extrabold">{{ $new }}</h2>
        <p class="text-sm mt-1">New This Week</p>
      </div>
      <div class="bg-gradient-to-r from-blue-500 to-cyan-500 text-white text-center p-6 rounded-2xl shadow-lg card-hover">
        <h2 class="text-3xl font-extrabold">{{ date('Y') }}</h2>
        <p class="text-sm mt-1">Active Year</p>
      </div>
    </div>

    {{-- Search + Sort --}}
    <div class="flex flex-wrap items-center justify-between mb-6 gap-3">
      <div class="flex gap-2 w-full sm:w-1/2">
        <input type="text" id="searchBox" placeholder="🔍 Search by name or email..." class="form-control flex-1">
        <button id="searchBtn" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition">Search</button>
      </div>
      <select id="sortSelect" class="form-select w-full sm:w-1/4">
        <option value="recent">Newest First</option>
        <option value="oldest">Oldest First</option>
        <option value="name">By Name (A–Z)</option>
      </select>
    </div>

    {{-- Clients Table --}}
    <div class="bg-white shadow-xl rounded-3xl overflow-hidden border border-gray-200 transition-all duration-500">
      <div class="bg-gray-800 text-white px-6 py-3 text-center text-lg fw-semibold">All Registered Clients</div>
      <div class="overflow-x-auto">
        <table class="table table-striped table-hover align-middle mb-0" id="clientsTable">
          <thead>
            <tr>
              <th>#</th>
              <th>Name</th>
              <th>Email</th>
              <th>Joined</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach($clients as $client)
              <tr class="transition hover:bg-gray-50">
                <td class="fw-bold">#{{ $client->id }}</td>
                <td class="fw-semibold">{{ $client->first_name }} {{ $client->last_name }}</td>
                <td>{{ $client->email }}</td>
                <td>{{ $client->created_at->format('d M, Y') }}</td>
                <td>
                  <button class="btn btn-sm btn-outline-primary view-btn"
                    data-name="{{ $client->first_name }} {{ $client->last_name }}"
                    data-email="{{ $client->email }}"
                    data-joined="{{ $client->created_at->format('d M, Y h:i A') }}">
                      View
                  </button>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </main>

  {{-- Modal --}}
  <div class="modal fade" id="clientModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-3 shadow-lg">
        <div class="modal-header bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
          <h5 class="modal-title font-semibold">👤 Client Information</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body p-6">
          <div class="bg-gray-50 p-5 rounded-xl shadow-inner space-y-3">
            <p><span class="font-bold text-gray-700">Name:</span> <span id="clientName" class="text-gray-800"></span></p>
            <p><span class="font-bold text-gray-700">Email:</span> <span id="clientEmail" class="text-gray-800"></span></p>
            <p><span class="font-bold text-gray-700">Join Date:</span> <span id="clientJoined" class="text-gray-800"></span></p>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary px-4 py-2 rounded-lg" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

 

  <script>
    // 🔍 Search Button Functionality
    const searchBox = document.getElementById('searchBox');
    const searchBtn = document.getElementById('searchBtn');
    const rows = Array.from(document.querySelectorAll('#clientsTable tbody tr'));
    searchBtn.addEventListener('click', () => {
      const term = searchBox.value.toLowerCase();
      rows.forEach(row => {
        const match = row.innerText.toLowerCase().includes(term);
        row.style.display = match ? '' : 'none';
      });
    });

    // 🔽 Sorting
    const sortSelect = document.getElementById('sortSelect');
    sortSelect.addEventListener('change', () => {
      const option = sortSelect.value;
      const tbody = document.querySelector('#clientsTable tbody');
      const sorted = [...rows].sort((a, b) => {
        if (option === 'name') return a.children[1].innerText.localeCompare(b.children[1].innerText);
        if (option === 'oldest') return a.children[0].innerText.localeCompare(b.children[0].innerText);
        return b.children[0].innerText.localeCompare(a.children[0].innerText);
      });
      tbody.innerHTML = '';
      sorted.forEach(r => tbody.appendChild(r));
    });

    // 👁️ View Client Modal
    document.querySelectorAll('.view-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        document.getElementById('clientName').textContent = btn.dataset.name;
        document.getElementById('clientEmail').textContent = btn.dataset.email;
        document.getElementById('clientJoined').textContent = btn.dataset.joined;
        new bootstrap.Modal(document.getElementById('clientModal')).show();
      });
    });


  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
