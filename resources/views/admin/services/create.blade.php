<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tambah Paket / Tarif Layanan</h2>
            <a href="{{ route('services.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">
                &larr; Kembali ke Daftar Layanan
            </a>
        </div>
    </x-slot>

    <div class="py-12 max-w-2xl mx-auto sm:px-6 lg:px-8">
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded shadow-sm">
                <p class="font-bold mb-1">Terjadi kesalahan input:</p>
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('services.store') }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow-sm border">
            @csrf
            <div class="mb-4">
                <label class="block font-medium mb-1">Nama Service</label>
                <input type="text" name="service_name" value="{{ old('service_name') }}" class="w-full border rounded p-2" required>
            </div>
            <div class="mb-4">
                <label class="block font-medium mb-1">Kategori</label>
                <select name="category" class="w-full border rounded p-2" required>
                    <option value="Kiloan" {{ old('category') === 'Kiloan' ? 'selected' : '' }}>Kiloan</option>
                    <option value="Satuan" {{ old('category') === 'Satuan' ? 'selected' : '' }}>Satuan</option>
                    <option value="Express" {{ old('category') === 'Express' ? 'selected' : '' }}>Express</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block font-medium mb-1">Harga per Unit (Rp)</label>
                <input type="number" name="price_per_unit" value="{{ old('price_per_unit') }}" class="w-full border rounded p-2" min="1" required>
            </div>
            <div class="mb-4">
                <label class="block font-medium mb-1">Tipe Unit</label>
                <select name="unit_type" class="w-full border rounded p-2" required>
                    <option value="Kg" {{ old('unit_type') === 'Kg' ? 'selected' : '' }}>Kg</option>
                    <option value="Pcs" {{ old('unit_type') === 'Pcs' ? 'selected' : '' }}>Pcs</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block font-medium mb-1">Deskripsi</label>
                <textarea name="description" class="w-full border rounded p-2" rows="3">{{ old('description') }}</textarea>
            </div>
            <div class="mb-4">
                <label class="block font-medium mb-1">Gambar Service (Opsional)</label>
                <input type="file" name="image" accept="image/*" class="w-full border rounded p-2">
            </div>
            <div class="flex items-center gap-3">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2.5 rounded font-semibold">Simpan Data</button>
                <a href="{{ route('services.index') }}" class="text-gray-600 hover:text-gray-800 text-sm font-semibold">Batal</a>
            </div>
        </form>
    </div>
</x-app-layout>