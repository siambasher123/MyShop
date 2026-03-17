<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Apply Discount - MyShop Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
    rel="stylesheet"
  />

  <style>
    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeInUp { animation: fadeInUp 0.6s ease-out; }

    .glass-card {
      background: rgba(255, 255, 255, 0.75);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .form-box {
      transition: all 0.2s ease-in-out;
    }

    .form-box:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
    }

    select, input {
      border-radius: 0.65rem !important;
      background-color: #fafafa !important;
      transition: all 0.3s ease-in-out;
    }

    select:focus, input:focus {
      border-color: #6366f1 !important;
      box-shadow: 0 0 0 0.2rem rgba(99, 102, 241, 0.3) !important;
      background-color: white !important;
    }

    label {
      font-weight: 600;
      color: #374151;
    }
  </style>
</head>

<body class="bg-gradient-to-br from-gray-100 to-gray-200 flex min-h-screen">
  @include('admin.sidebar')

  <main class="ml-64 flex-1 p-10 animate-fadeInUp">
    <h1 class="text-4xl font-extrabold mb-8 text-gray-900 flex items-center gap-2">
      💸 Apply Discounts
    </h1>

    @if(session('success'))
      <div class="alert alert-success shadow-sm mb-6">
        {{ session('success') }}
      </div>
    @endif

    <!-- DISCOUNT FORM -->
    <div class="glass-card p-10 rounded-4 shadow-2xl max-w-3xl mx-auto border border-gray-200 form-box">
      <form method="POST" action="{{ route('admin.discounts.apply') }}" class="space-y-8">
        @csrf

        <!-- DISCOUNT TYPE -->
        <div class="form-group">
          <label for="discount_type" class="form-label mb-2">Select Discount Type</label>
          <select
            name="discount_type"
            id="discount_type"
            class="form-select w-full py-3 text-gray-700"
            required
          >
            <option value="">Choose type</option>
            <option value="category">By Category</option>
            <option value="subcategory">By Subcategory</option>
            <option value="product">By Individual Product</option>
          </select>
        </div>

        <!-- CATEGORY -->
        <div id="category-section" class="hidden form-group">
          <label class="form-label mb-2">Select Category</label>
          <div class="input-group">
            <span class="input-group-text bg-indigo-100 text-indigo-600"><i class="bi bi-grid"></i></span>
            <select
              name="category"
              id="category"
              class="form-select py-3 text-gray-700"
            >
              <option value="">Select Category</option>
              @foreach($categories as $cat)
                <option value="{{ $cat }}">{{ $cat }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <!-- SUBCATEGORY -->
        <div id="subcategory-section" class="hidden form-group">
          <label class="form-label mb-2">Select Subcategory</label>
          <div class="input-group">
            <span class="input-group-text bg-indigo-100 text-indigo-600"><i class="bi bi-diagram-2"></i></span>
            <select
              name="subcategory"
              id="subcategory"
              class="form-select py-3 text-gray-700"
              disabled
            >
              <option value="">Select Subcategory</option>
            </select>
          </div>
        </div>

        <!-- PRODUCT -->
        <div id="product-section" class="hidden form-group">
          <label class="form-label mb-2">Product Code</label>
          <input
            type="text"
            name="product_id"
            class="form-control py-3 text-gray-700"
            placeholder="e.g. MEN-TS-1001"
          />
        </div>

        <!-- DISCOUNT VALUE -->
        <div class="form-group">
          <label class="form-label mb-2">Discount Percentage (%)</label>
          <input
            type="number"
            name="discount_percent"
            min="1"
            max="90"
            class="form-control py-3 text-gray-700"
            placeholder="Enter discount amount"
            required
          />
        </div>

        <!-- SUBMIT BUTTON -->
        <button
          type="submit"
          class="btn btn-dark w-full py-3 fw-semibold hover:bg-gray-900 transition-all"
        >
          Apply Discount
        </button>
      </form>
    </div>

    <div class="text-center mt-12 text-gray-500 text-sm">
      ⚙️ Discounts automatically update product prices on client pages.
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.js"></script>

  <script>
    const typeSelect = document.getElementById('discount_type');
    const categorySection = document.getElementById('category-section');
    const subcategorySection = document.getElementById('subcategory-section');
    const productSection = document.getElementById('product-section');
    const categorySelect = document.getElementById('category');
    const subSelect = document.getElementById('subcategory');

    typeSelect.addEventListener('change', () => {
      const type = typeSelect.value;
      categorySection.classList.add('hidden');
      subcategorySection.classList.add('hidden');
      productSection.classList.add('hidden');

      if (type === 'category') categorySection.classList.remove('hidden');
      if (type === 'subcategory') {
        categorySection.classList.remove('hidden');
        subcategorySection.classList.remove('hidden');
      }
      if (type === 'product') {
        categorySection.classList.remove('hidden');
        subcategorySection.classList.remove('hidden');
        productSection.classList.remove('hidden');
      }
    });

    const subMap = @json(
      \App\Models\Product1::select('category', 'subcategory')->get()
        ->groupBy('category')
        ->map(fn($items) => $items->pluck('subcategory')->unique()->values())
    );

    categorySelect.addEventListener('change', e => {
      const cat = e.target.value;
      subSelect.innerHTML = '<option value="">Select Subcategory</option>';

      if (cat && subMap[cat]) {
        subMap[cat].forEach(sc => {
          const opt = document.createElement('option');
          opt.value = sc;
          opt.textContent = sc;
          subSelect.appendChild(opt);
        });
        subSelect.disabled = false;
      } else {
        subSelect.disabled = true;
      }
    });
  </script>
</body>
</html>
