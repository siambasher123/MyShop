<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Transactions - MyShop Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeIn { animation: fadeIn 0.4s ease-out; }
    .summary-card:hover { transform: scale(1.02); transition: all 0.3s ease; }
    .note-box { min-width: 240px; resize: none; }
  </style>
</head>

<body class="bg-gray-50 text-gray-800 flex min-h-screen">
  {{-- Sidebar --}}
  @include('admin.sidebar')

  {{-- Main --}}
  <main class="ml-64 flex-1 p-8 animate-fadeIn">
    <h1 class="text-4xl font-extrabold text-gray-900 mb-6">💳 Transactions</h1>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
      <div class="summary-card bg-white p-5 rounded-2xl shadow border-l-4 border-blue-500 text-center">
        <p class="text-gray-500 text-sm">Total Transactions</p>
        <h2 id="totalCount" class="text-3xl font-bold text-gray-900 mt-1">{{ $transactions->count() }}</h2>
      </div>

      <div class="summary-card bg-white p-5 rounded-2xl shadow border-l-4 border-yellow-500 text-center">
        <p class="text-gray-500 text-sm">Pending</p>
        <h2 id="pendingCount" class="text-3xl font-bold text-yellow-600 mt-1">
          {{ $transactions->where('status','pending')->count() }}
        </h2>
      </div>

      <div class="summary-card bg-white p-5 rounded-2xl shadow border-l-4 border-green-500 text-center">
        <p class="text-gray-500 text-sm">Completed</p>
        <h2 id="doneCount" class="text-3xl font-bold text-green-600 mt-1">
          {{ $transactions->where('status','done')->count() }}
        </h2>
      </div>
    </div>

    {{-- Flash message --}}
    @if(session('success'))
      <div class="alert alert-success d-flex justify-content-between align-items-center shadow-sm mb-4">
        <span>✅ {{ session('success') }}</span>
        <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
      </div>
    @endif

    {{-- Transactions Table --}}
    <div class="bg-white rounded-3 shadow-lg overflow-hidden border border-gray-200">
      <div class="bg-gradient-to-r from-gray-100 to-gray-200 px-6 py-3 border-b">
        <h2 class="text-lg font-semibold text-gray-700">All Transactions</h2>
      </div>

      <div class="overflow-x-auto">
        <table class="table table-hover align-middle mb-0">
          <thead class="bg-gray-100 text-gray-600 text-sm uppercase">
            <tr>
              <th>#</th>
              <th>Customer</th>
              <th>Phone</th>
              <th>Address</th>
              <th>Codes</th>
              <th>Qty</th>
              <th>Total ($)</th>
              <th>Payment Note</th>
              <th>Status</th>
              <th class="text-center">Action</th>
            </tr>
          </thead>

          <tbody id="transactionBody">
            @forelse($transactions as $index => $tx)
              @php
                $codes = is_string($tx->product_codes)
                    ? json_decode($tx->product_codes, true)
                    : ($tx->product_codes ?? []);
              @endphp
              <tr id="row-{{ $tx->id }}" class="hover:bg-gray-50 transition">
                <td class="fw-bold">#{{ $index + 1 }}</td>
                <td>{{ $tx->full_name }}</td>
                <td>{{ $tx->mobile_number }}</td>
                <td class="text-sm text-gray-600">{{ Str::limit($tx->address, 35) }}</td>

                <td>
                  @if(!empty($codes))
                    <div class="d-flex flex-wrap gap-1">
                      @foreach($codes as $code)
                        <span class="badge bg-dark copy-code" data-code="{{ $code }}" style="cursor:pointer;">{{ $code }}</span>
                      @endforeach
                    </div>
                  @else
                    <span class="text-gray-400 text-sm">—</span>
                  @endif
                </td>

                <td class="text-center fw-semibold">{{ $tx->quantity }}</td>
                <td class="fw-semibold">${{ number_format($tx->total, 2) }}</td>

                {{-- Payment Note --}}
                <td>
                  <textarea
                    class="form-control note-box shadow-sm border-gray-300"
                    rows="1"
                    data-id="{{ $tx->id }}"
                    placeholder="Write payment details..."
                  >{{ $tx->payment_note }}</textarea>
                </td>

                {{-- Status --}}
                <td id="status-{{ $tx->id }}">
                  @if($tx->status === 'done')
                    <span class="badge bg-success">Done</span>
                  @else
                    <span class="badge bg-warning text-dark">Pending</span>
                  @endif
                </td>

                {{-- Action --}}
                <td class="text-center">
                  <button
                    class="btn btn-success btn-sm px-3 mark-done"
                    data-id="{{ $tx->id }}"
                    {{ $tx->status === 'done' ? 'disabled' : '' }}
                  >
                    {{ $tx->status === 'done' ? 'Completed' : 'Mark Done' }}
                  </button>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="10" class="text-center py-5 text-gray-500">
                  <img src="https://cdn-icons-png.flaticon.com/512/4076/4076500.png" class="w-16 mx-auto opacity-70 mb-3">
                  <p>No transactions yet.</p>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- Footer --}}
    <div class="mt-10 text-center text-sm text-gray-500">
      © {{ date('Y') }} <strong>MyShop Admin</strong>. All Rights Reserved.
    </div>
  </main>

  {{-- JS --}}
  <script>
    // ✅ Copy Code
    document.querySelectorAll(".copy-code").forEach(el => {
      el.addEventListener("click", () => {
        navigator.clipboard.writeText(el.dataset.code);
        el.innerText = "Copied!";
        el.classList.add("bg-success");
        setTimeout(() => {
          el.innerText = el.dataset.code;
          el.classList.remove("bg-success");
        }, 800);
      });
    });

    // ✅ Auto-Save Notes (Persistent)
    document.querySelectorAll(".note-box").forEach(box => {
      let timer;
      box.addEventListener("input", () => {
        clearTimeout(timer);
        timer = setTimeout(async () => {
          const id = box.dataset.id;
          const note = box.value;
          await fetch(`/admin/transactions/note/${id}`, {
            method: "POST",
            headers: {
              "X-CSRF-TOKEN": "{{ csrf_token() }}",
              "Content-Type": "application/json",
            },
            body: JSON.stringify({ note }),
          });
          box.classList.add("border-success");
          setTimeout(() => box.classList.remove("border-success"), 1200);
        }, 800); // Save 0.8s after typing stops
      });
    });

    // ✅ Mark Done Interactively + Update Counters
    document.querySelectorAll(".mark-done").forEach(btn => {
      btn.addEventListener("click", async e => {
        const id = e.target.dataset.id;
        const res = await fetch(`/admin/transactions/status/${id}`, {
          method: "POST",
          headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Content-Type": "application/json",
          },
          body: JSON.stringify({ status: "done" }),
        });

        if (res.ok) {
          // Update row visually
          const statusCell = document.getElementById(`status-${id}`);
          statusCell.innerHTML = '<span class="badge bg-success">Done</span>';
          e.target.innerText = "Completed";
          e.target.classList.replace("btn-success", "btn-secondary");
          e.target.disabled = true;

          // Update summary cards dynamically
          const doneCount = document.getElementById("doneCount");
          const pendingCount = document.getElementById("pendingCount");

          doneCount.textContent = parseInt(doneCount.textContent) + 1;
          pendingCount.textContent = Math.max(0, parseInt(pendingCount.textContent) - 1);
        }
      });
    });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
