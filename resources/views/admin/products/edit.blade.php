<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Product - Admin | MyShop</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex min-h-screen">
  @include('admin.sidebar')

  <main class="ml-64 p-10 w-full">
    <h1 class="text-3xl font-bold text-gray-900 mb-6">✏️ Edit Product</h1>

    @if($errors->any())
      <div class="mb-6 bg-red-100 border border-red-400 text-red-800 px-4 py-2 rounded-lg">{{ $errors->first() }}</div>
    @endif

    <div class="bg-white p-8 rounded-2xl shadow-lg max-w-2xl">
      <form method="POST" action="{{ route('admin.products.update', $product->id) }}" enctype="multipart/form-data" class="grid grid-cols-1 gap-5">
        @csrf @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
          <div>
            <label class="block text-gray-700 font-medium mb-2">Category</label>
            <select name="category" id="category" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500" required>
              @foreach(array_keys($map) as $cat)
                <option value="{{ $cat }}" @selected($product->category === $cat)>{{ $cat }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="block text-gray-700 font-medium mb-2">Subcategory</label>
            <select name="subcategory" id="subcategory" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500" required>
              <!-- options filled by JS -->
            </select>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
          <div>
            <label class="block text-gray-700 font-medium mb-2">Product Code</label>
            <input type="text" name="code" value="{{ $product->code }}" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500" required>
          </div>
          <div>
            <label class="block text-gray-700 font-medium mb-2">Price ($)</label>
            <input type="number" step="0.01" name="price" value="{{ $product->price }}" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500" required>
          </div>
          <div>
            <label class="block text-gray-700 font-medium mb-2">Stock</label>
            <input type="number" name="stock" min="0" value="{{ $product->stock }}" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500" required>
          </div>
        </div>

        <div>
          <label class="block text-gray-700 font-medium mb-2">Description</label>
          <textarea name="description" rows="3" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500">{{ $product->description }}</textarea>
        </div>

        <div>
          <label class="block text-gray-700 font-medium mb-2">Replace Image (optional)</label>
          <input type="file" name="image" accept="image/*" class="w-full text-gray-600">
          @if($product->image)
            <p class="text-xs text-gray-500 mt-1">Current: {{ $product->image }}</p>
          @endif
        </div>

        <div class="flex gap-3">
          <button class="bg-black text-white px-6 py-2 rounded-lg hover:bg-gray-800 transition">Update</button>
          <a href="{{ route('admin.products') }}" class="px-6 py-2 rounded-lg border border-gray-300 hover:bg-gray-100">Cancel</a>
        </div>
      </form>
    </div>
  </main>

  <script>
    const map = @json($map);
    const selCategory = document.getElementById('category');
    const selSub = document.getElementById('subcategory');

    function fillSubs(cat, selected) {
      const list = map[cat] || [];
      selSub.innerHTML = '';
      list.forEach(sc => {
        const opt = document.createElement('option');
        opt.value = sc; opt.textContent = sc;
        if (sc === selected) opt.selected = true;
        selSub.appendChild(opt);
      });
    }

    // initial
    fillSubs(selCategory.value, @json($product->subcategory));

    selCategory.addEventListener('change', e => fillSubs(e.target.value, ''));
  </script>
</body>
</html>
