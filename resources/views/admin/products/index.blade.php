<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Products - Admin | MyShop</title>
  <script src="https://cdn.tailwindcss.com"></script>

  <style>
    .active-tab {
      background: linear-gradient(to right, #000, #2d2d2d);
      color: white;
      box-shadow: 0 3px 10px rgba(0,0,0,0.2);
    }
    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(15px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeInUp { animation: fadeInUp 0.5s ease-out; }
  </style>
</head>

<body class="bg-gradient-to-br from-gray-50 via-gray-100 to-gray-200 flex min-h-screen text-gray-800">
  @include('admin.sidebar')

  <main class="ml-64 p-10 w-full animate-fadeInUp">
    <!-- Header -->
    <div class="flex items-center justify-between mb-10">
      <div>
        <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">🛍️ Product Management</h1>
        <p class="text-gray-500 mt-1">Add, view, and manage your store’s product listings</p>
      </div>
      <span class="px-4 py-2 text-sm bg-white shadow rounded-xl font-semibold text-gray-600">
        Admin Panel
      </span>
    </div>

    <!-- Alerts -->
    @if(session('success'))
      <div class="mb-6 bg-green-50 border border-green-400 text-green-800 px-5 py-3 rounded-xl shadow-sm">
        ✅ {{ session('success') }}
      </div>
    @endif
    @if($errors->any())
      <div class="mb-6 bg-red-50 border border-red-400 text-red-800 px-5 py-3 rounded-xl shadow-sm">
        ⚠️ {{ $errors->first() }}
      </div>
    @endif

    <!-- Tabs -->
    <div class="flex gap-4 mb-8">
      <button id="tab-all" class="tab-btn bg-white px-6 py-2.5 rounded-xl shadow hover:bg-gray-100 active-tab font-semibold">
        📦 All Products
      </button>
      <button id="tab-add" class="tab-btn bg-white px-6 py-2.5 rounded-xl shadow hover:bg-gray-100 font-semibold">
        ➕ Add Product
      </button>
    </div>

    <!-- ALL PRODUCTS SECTION -->
    <section id="all-products" class="tab-content">
      <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-200">
        <div class="px-6 py-4 bg-gradient-to-r from-gray-800 to-black text-white text-lg font-semibold">
          Product Inventory
        </div>

        <table class="min-w-full text-left">
          <thead class="bg-gray-100 text-gray-600 text-sm uppercase">
            <tr>
              <th class="py-3 px-4">#</th>
              <th class="py-3 px-4">Category</th>
              <th class="py-3 px-4">Subcategory</th>
              <th class="py-3 px-4">Code</th>
              <th class="py-3 px-4">Price</th>
              <th class="py-3 px-4">Stock</th>
              <th class="py-3 px-4">Description</th>
              <th class="py-3 px-4 text-center">Actions</th>
            </tr>
          </thead>
          <tbody class="text-gray-700 divide-y divide-gray-200">
            @forelse($products as $p)
              <tr class="hover:bg-gray-50 transition">
                <td class="py-3 px-4">{{ $p->id }}</td>
                <td class="py-3 px-4">{{ $p->category }}</td>
                <td class="py-3 px-4">{{ $p->subcategory }}</td>
                <td class="py-3 px-4 font-semibold text-gray-900">{{ $p->code }}</td>
                <td class="py-3 px-4 text-indigo-600 font-semibold">${{ number_format($p->price, 2) }}</td>
                <td class="py-3 px-4">{{ $p->stock }}</td>
                <td class="py-3 px-4 truncate max-w-xs text-sm" title="{{ $p->description }}">
                  {{ \Illuminate\Support\Str::limit($p->description, 50) }}
                </td>
                <td class="py-3 px-4 text-center space-x-2">
                  <a href="{{ route('admin.products.edit', $p->id) }}"
                     class="inline-block px-3 py-1 rounded-md text-white bg-indigo-600 hover:bg-indigo-700 text-xs font-medium">
                    Edit
                  </a>
                  <form action="{{ route('admin.products.destroy', $p->id) }}" method="POST" class="inline-block"
                        onsubmit="return confirm('Are you sure you want to delete this product?')">
                    @csrf @method('DELETE')
                    <button class="px-3 py-1 rounded-md text-white bg-red-600 hover:bg-red-700 text-xs font-medium">
                      Delete
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="py-10 text-center text-gray-500">
                  🕓 No products added yet.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="mt-8">
        {{ $products->links() }}
      </div>
    </section>

    <!-- ADD PRODUCT SECTION -->
    <section id="add-product" class="tab-content hidden">
      <div class="bg-white p-10 rounded-2xl shadow-2xl border border-gray-200 w-full">
        <h2 class="text-3xl font-bold mb-10 text-gray-900 flex items-center">
          <span class="mr-3">🆕</span> Add New Product
        </h2>

        <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-2 gap-10">
          @csrf

          <!-- Left Side (Form Inputs) -->
          <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
              <div>
                <label class="block text-gray-700 font-semibold mb-2">Category</label>
                <select name="category" id="category" class="w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-black" required>
                  <option value="">Select Category</option>
                  @foreach(array_keys($map) as $cat)
                    <option value="{{ $cat }}">{{ $cat }}</option>
                  @endforeach
                </select>
              </div>

              <div>
                <label class="block text-gray-700 font-semibold mb-2">Subcategory</label>
                <select name="subcategory" id="subcategory" class="w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-black" required disabled>
                  <option value="">Select Subcategory</option>
                </select>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
              <div>
                <label class="block text-gray-700 font-semibold mb-2">Product Code</label>
                <input type="text" name="code" placeholder="MEN-TS-1001"
                       class="w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-black" required>
              </div>
              <div>
                <label class="block text-gray-700 font-semibold mb-2">Price ($)</label>
                <input type="number" step="0.01" name="price"
                       class="w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-black" required>
              </div>
              <div>
                <label class="block text-gray-700 font-semibold mb-2">Stock</label>
                <input type="number" name="stock" min="0"
                       class="w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-black" required>
              </div>
            </div>

            <div>
              <label class="block text-gray-700 font-semibold mb-2">Description</label>
              <textarea name="description" rows="4"
                        class="w-full border rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-black"
                        placeholder="Describe the product briefly..."></textarea>
            </div>

            <button type="submit"
              class="w-full bg-gradient-to-r from-black via-gray-900 to-gray-800 text-white py-3 rounded-xl font-semibold hover:from-gray-800 hover:to-black transition">
              💾 Save Product
            </button>
          </div>

          <!-- Right Side (Image Upload + Preview) -->
          <div class="flex flex-col items-center justify-center border-l border-gray-200 pl-10">
            <label class="block text-gray-700 font-semibold mb-3">Product Image</label>
            <div class="relative w-72 h-72 border-2 border-dashed border-gray-300 rounded-xl flex items-center justify-center bg-gray-50 hover:bg-gray-100 transition">
              <input type="file" name="image" id="imageInput" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer">
              <span id="uploadText" class="text-gray-500">Click or Drop to Upload</span>
              <img id="imagePreview" class="hidden absolute inset-0 w-full h-full object-cover rounded-xl" />
            </div>
            <p class="text-sm text-gray-400 mt-3 text-center">Supported: JPG, PNG, WEBP</p>
          </div>
        </form>
      </div>
    </section>
  </main>

  <script>
    document.addEventListener("DOMContentLoaded", () => {
      // Tabs
      const tabAll = document.getElementById('tab-all');
      const tabAdd = document.getElementById('tab-add');
      const allProducts = document.getElementById('all-products');
      const addProduct = document.getElementById('add-product');

      tabAll.addEventListener('click', () => {
        allProducts.classList.remove('hidden');
        addProduct.classList.add('hidden');
        tabAll.classList.add('active-tab'); tabAdd.classList.remove('active-tab');
      });

      tabAdd.addEventListener('click', () => {
        addProduct.classList.remove('hidden');
        allProducts.classList.add('hidden');
        tabAdd.classList.add('active-tab'); tabAll.classList.remove('active-tab');
      });

      // Category → Subcategory
      const map = @json($map);
      const category = document.getElementById('category');
      const subcategory = document.getElementById('subcategory');

      if (category && subcategory) {
        category.addEventListener('change', e => {
          const selected = e.target.value;
          const list = map[selected] || [];

          subcategory.innerHTML = '<option value="">Select Subcategory</option>';
          list.forEach(item => {
            const opt = document.createElement('option');
            opt.value = item;
            opt.textContent = item;
            subcategory.appendChild(opt);
          });

          subcategory.disabled = list.length === 0;

          // nice visual highlight
          subcategory.classList.add('ring-2', 'ring-indigo-500', 'transition');
          setTimeout(() => subcategory.classList.remove('ring-2', 'ring-indigo-500'), 1000);
        });
      }

      // Live Image Preview
      const imageInput = document.getElementById('imageInput');
      const imagePreview = document.getElementById('imagePreview');
      const uploadText = document.getElementById('uploadText');

      imageInput?.addEventListener('change', e => {
        const file = e.target.files[0];
        if (file) {
          const reader = new FileReader();
          reader.onload = () => {
            imagePreview.src = reader.result;
            imagePreview.classList.remove('hidden');
            uploadText.classList.add('hidden');
          };
          reader.readAsDataURL(file);
        }
      });
    });
  </script>
</body>
</html>
