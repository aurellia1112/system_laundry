<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laundry Express</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6 font-sans">
    <div class="max-w-5xl mx-auto">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-2 mb-4 pb-3 border-b">
            <a href="/" class="text-xs font-semibold text-gray-500 hover:text-gray-800 transition">&larr; Halaman Utama</a>
            <div>
                @auth
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-medium text-gray-700">
                            {{ Auth::user()->name }}
                            @if(Auth::user()->role === \App\Models\User::ROLE_ADMIN)
                            @else
                                <span class="ml-1 text-xs bg-blue-100 text-blue-700 font-bold px-2 py-0.5 rounded">Pelanggan</span>
                            @endif
                        </span>

                        @can('admin')
                            <a href="{{ route('dashboard') }}" class="rounded-lg bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-indigo-700">
                                Dashboard Admin
                            </a>
                        @endcan

                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="rounded-lg bg-gray-800 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-gray-700 cursor-pointer">
                                Keluar (Log Out)
                            </button>
                        </form>
                    </div>
                @else
                    <div class="flex items-center gap-2">
                        <a href="{{ route('login') }}" class="rounded-lg bg-blue-600 px-3.5 py-1.5 text-xs font-semibold text-white transition hover:bg-blue-700">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="rounded-lg bg-gray-200 px-3.5 py-1.5 text-xs font-semibold text-gray-700 transition hover:bg-gray-300">
                            Daftar
                        </a>
                    </div>
                @endauth
            </div>
        </div>

        <div class="text-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Laundry Express</h1>
            <p class="text-gray-500 text-sm mt-1">Pilih layanan laundry yang Anda butuhkan</p>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-6 text-center font-semibold">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-6 text-center font-semibold">{{ session('error') }}</div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-6 font-semibold">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        <form id="orderForm" action="{{ route('customer.checkout') }}" method="POST">
            @csrf

            <div class="bg-white p-6 rounded-xl shadow-sm border mb-6">
                <h2 class="text-lg font-bold text-gray-700 mb-4 pb-2 border-b">1. Informasi Pelanggan</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-1">Nama Lengkap</label>
                        <input type="text" id="customer_name" name="customer_name" required placeholder="Masukkan nama pelanggan" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-600 mb-1">Nomor WhatsApp</label>
                        <input type="text" id="whatsapp_number" name="whatsapp_number" required placeholder="Contoh: 081234567890" class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                </div>
            </div>

            <h2 class="text-lg font-bold text-gray-700 mb-4">2. Pilih Layanan</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse($services as $service)
                    <div class="service-card bg-white rounded-xl shadow-sm border overflow-hidden flex flex-col justify-between" data-category="{{ $service->category ?? 'Kiloan' }}">
                        <div>
                            @if($service->image)
                                <img src="{{ asset('storage/' . $service->image) }}" alt="{{ $service->service_name }}" class="w-full h-40 object-cover">
                            @endif

                            <div class="p-4">
                                <div class="flex justify-between items-center mb-2 gap-2">
                                    <span class="text-xs bg-blue-100 text-blue-700 font-semibold px-2.5 py-0.5 rounded">{{ $service->category ?? 'Kiloan' }}</span>
                                    <span class="font-bold text-green-600">Rp {{ number_format($service->price_per_unit, 0, ',', '.') }} / {{ $service->unit_type }}</span>
                                </div>
                                <h3 class="font-bold text-gray-800 text-base item-name">{{ $service->service_name }}</h3>
                                @if($service->description)
                                    <p class="text-xs text-gray-500 mt-1">{{ $service->description }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="p-4 bg-gray-50 border-t">
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Jumlah ({{ $service->unit_type }})</label>
                            <input type="hidden" name="items[{{ $service->id }}][service_id]" value="{{ $service->id }}">
                            <input type="number" name="items[{{ $service->id }}][qty_or_weight]" min="0" value="0" data-name="{{ $service->service_name }}" data-price="{{ $service->price_per_unit }}" class="item-qty w-full border rounded-lg px-3 py-1.5 text-center font-bold text-gray-700 focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-8 text-gray-500 bg-white rounded-xl border">
                        Belum ada paket layanan yang tersedia.
                    </div>
                @endforelse
            </div>

            <div class="mt-8 text-right">
                <button type="button" onclick="showConfirmationModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-8 py-3 rounded-xl shadow-md transition">Pesan Sekarang</button>
            </div>

            <div id="confirmModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
                <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl transform transition-all">
                    <h3 class="text-xl font-bold text-gray-800 border-b pb-3 mb-4">Konfirmasi Pesanan</h3>
                    <div class="space-y-2 text-sm text-gray-600 mb-4">
                        <div class="flex justify-between"><span class="font-semibold">Nama:</span> <span id="modalName" class="text-gray-900 font-bold"></span></div>
                        <div class="flex justify-between"><span class="font-semibold">WhatsApp:</span> <span id="modalWhatsApp" class="text-gray-900 font-bold"></span></div>
                    </div>
                    <div class="border-t border-b py-3 mb-4 max-h-48 overflow-y-auto">
                        <p class="font-semibold text-xs text-gray-400 uppercase mb-2">Rincian Item</p>
                        <ul id="modalItemList" class="space-y-2 text-sm"></ul>
                    </div>
                    <div class="flex justify-between items-center text-lg font-bold text-gray-800 mb-6">
                        <span>Total Pembayaran:</span>
                        <span id="modalTotalPrice" class="text-green-600 text-xl">Rp 0</span>
                    </div>
                    <div class="flex gap-3">
                        <button type="button" onclick="closeConfirmationModal()" class="w-1/2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2.5 rounded-xl transition">Batal</button>
                        <button type="submit" class="w-1/2 bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 rounded-xl shadow transition">Ya, Kirim Pesanan</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        function formatCurrency(value) {
            return 'Rp ' + Number(value).toLocaleString('id-ID');
        }

        function showConfirmationModal() {
            const name = document.getElementById('customer_name').value.trim();
            const whatsapp = document.getElementById('whatsapp_number').value.trim();

            if (!name || !whatsapp) {
                alert('Nama dan nomor WhatsApp wajib diisi.');
                return;
            }

            const items = [];
            let total = 0;

            document.querySelectorAll('.item-qty').forEach(input => {
                const qty = Number(input.value || 0);
                if (qty > 0) {
                    const nameText = input.dataset.name;
                    const price = Number(input.dataset.price || 0);
                    const subtotal = qty * price;
                    items.push({ name: nameText, qty, subtotal });
                    total += subtotal;
                }
            });

            if (items.length === 0) {
                alert('Pilih minimal satu layanan sebelum checkout.');
                return;
            }

            document.getElementById('modalName').textContent = name;
            document.getElementById('modalWhatsApp').textContent = whatsapp;

            const list = document.getElementById('modalItemList');
            list.innerHTML = items.map(item => `
                <li class="flex justify-between gap-3">
                    <span>${item.name} x ${item.qty}</span>
                    <span class="font-semibold text-gray-800">${formatCurrency(item.subtotal)}</span>
                </li>
            `).join('');

            document.getElementById('modalTotalPrice').textContent = formatCurrency(total);
            document.getElementById('confirmModal').classList.remove('hidden');
            document.getElementById('confirmModal').classList.add('flex');
        }
        function closeConfirmationModal() {
            document.getElementById('confirmModal').classList.add('hidden');
            document.getElementById('confirmModal').classList.remove('flex');
        }
    </script>
</body>
</html>