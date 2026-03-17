<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Reviews - MyShop</title>
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
      <h1 class="text-4xl font-extrabold text-gray-900 mb-2">⭐ Customer Reviews</h1>
      
    </div>

    @php
      $totalReviews = count($reviews);
      $fiveStars = $reviews->where('rating', 5)->count();
      $fourStars = $reviews->where('rating', 4)->count();
      $threeStars = $reviews->where('rating', 3)->count();
      $twoStars = $reviews->where('rating', 2)->count();
      $oneStar = $reviews->where('rating', 1)->count();
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 mb-10">
      <div class="bg-gradient-to-r from-gray-800 to-gray-900 text-white text-center p-6 rounded-2xl shadow-lg card-hover">
        <h2 class="text-3xl font-extrabold">{{ $totalReviews }}</h2>
        <p class="text-sm mt-1">Total Reviews</p>
      </div>
      <div class="bg-gradient-to-r from-yellow-500 to-amber-500 text-white text-center p-6 rounded-2xl shadow-lg card-hover">
        <h2 class="text-3xl font-extrabold">{{ $fiveStars }}</h2>
        <p class="text-sm mt-1">5-Star</p>
      </div>
      <div class="bg-gradient-to-r from-emerald-500 to-green-600 text-white text-center p-6 rounded-2xl shadow-lg card-hover">
        <h2 class="text-3xl font-extrabold">{{ $fourStars }}</h2>
        <p class="text-sm mt-1">4-Star</p>
      </div>
      <div class="bg-gradient-to-r from-blue-500 to-indigo-500 text-white text-center p-6 rounded-2xl shadow-lg card-hover">
        <h2 class="text-3xl font-extrabold">{{ $threeStars }}</h2>
        <p class="text-sm mt-1">3-Star</p>
      </div>
      <div class="bg-gradient-to-r from-red-500 to-rose-600 text-white text-center p-6 rounded-2xl shadow-lg card-hover">
        <h2 class="text-3xl font-extrabold">{{ $oneStar + $twoStars }}</h2>
        <p class="text-sm mt-1">1–2 Star</p>
      </div>
    </div>

    <div class="flex flex-wrap justify-between items-center mb-5 gap-3">
      <input type="text" id="searchBox" placeholder="🔍 Search by username or code..." class="form-control w-full sm:w-1/3">
      <select id="ratingFilter" class="form-select w-full sm:w-1/4">
        <option value="">All Ratings</option>
        <option value="5">⭐⭐⭐⭐⭐</option>
        <option value="4">⭐⭐⭐⭐</option>
        <option value="3">⭐⭐⭐</option>
        <option value="2">⭐⭐</option>
        <option value="1">⭐</option>
      </select>
    </div>

    <div class="bg-white shadow-xl rounded-3xl overflow-hidden border border-gray-200 transition-all duration-500">
      <div class="bg-gray-800 text-white px-6 py-3 text-center text-lg fw-semibold">All Customer Reviews</div>

      <div class="overflow-x-auto">
        <table class="table table-striped table-hover align-middle mb-0">
          <thead>
            <tr>
              <th>Username</th>
              <th>Category</th>
              <th>Subcategory</th>
              <th>Code</th>
              <th>Price ($)</th>
              <th>Rating</th>
              <th>Review</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody>
            @forelse($reviews as $review)
              <tr class="transition transform hover:scale-[1.01] hover:bg-gray-50">
                <td class="fw-bold">{{ $review->username }}</td>
                <td>{{ $review->category }}</td>
                <td>{{ $review->subcategory }}</td>
                <td>
                  <span class="badge bg-dark copy-code" data-code="{{ $review->code }}">{{ $review->code }}</span>
                </td>
                <td>${{ number_format($review->price, 2) }}</td>
                <td>
                  <div class="flex items-center space-x-1">
                    @for($i = 1; $i <= 5; $i++)
                      @if($i <= $review->rating)
                        <span class="text-yellow-400">★</span>
                      @else
                        <span class="text-gray-300">★</span>
                      @endif
                    @endfor
                  </div>
                </td>
                <td>{{ Str::limit($review->review, 60) }}</td>
                <td class="text-gray-500 text-sm">{{ $review->created_at->format('M d, Y') }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="text-center py-5 text-muted">
                  <img src="https://cdn-icons-png.flaticon.com/512/4076/4076500.png" class="w-16 mx-auto mb-3 opacity-70">
                  <p>No reviews available.</p>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

  </main>

  <div id="toast" class="fixed top-5 right-5 bg-green-600 text-white px-4 py-2 rounded-lg shadow-lg hidden z-50">
    ✅ Copied to clipboard!
  </div>

  <button id="themeToggle" class="fixed bottom-5 right-5 bg-gray-800 text-white p-3 rounded-full shadow-lg z-50">
    🌙
  </button>

  <script>
    // ✅ Copy product code
    document.querySelectorAll('.copy-code').forEach(el => {
      el.addEventListener('click', () => {
        navigator.clipboard.writeText(el.dataset.code);
        const toast = document.getElementById('toast');
        toast.classList.remove('hidden');
        setTimeout(() => toast.classList.add('hidden'), 1500);
      });
    });

    // ✅ Search + Filter
    const searchBox = document.getElementById('searchBox');
    const ratingFilter = document.getElementById('ratingFilter');

    function filterTable() {
      const search = searchBox.value.toLowerCase();
      const rating = ratingFilter.value;
      document.querySelectorAll('tbody tr').forEach(row => {
        const user = row.querySelector('td:nth-child(1)')?.innerText.toLowerCase();
        const code = row.querySelector('td:nth-child(4)')?.innerText.toLowerCase();
        const stars = row.querySelectorAll('.text-yellow-400').length.toString();
        const match = (!rating || stars === rating) && (!search || user.includes(search) || code.includes(search));
        row.style.display = match ? '' : 'none';
      });
    }

    searchBox.addEventListener('input', filterTable);
    ratingFilter.addEventListener('change', filterTable);

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
