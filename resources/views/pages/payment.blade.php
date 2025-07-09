{{-- filepath: d:\project\client\pangsit-tantrum\resources\views\pages\payment.blade.php --}}
@extends('layouts.app')

@section('title', 'Payment')

@section('content')
    <div class="min-h-screen flex flex-col bg-[#FAFAFA]">
        <div class="flex items-center justify-between px-4 pt-6 pb-2 bg-white shadow-sm">
            <a href="{{ url()->previous() }}"
                class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-gray-100">
                <img src="{{ asset('assets/images/icons/tabler_arrow-left.svg') }}" class="w-6 h-6" alt="back">
            </a>
            <h1 class="text-lg font-bold">Pembayaran</h1>
            <div class="w-10"></div>
        </div>
        <div class="w-full max-w-md mx-auto flex-1 px-4 py-2 pb-40 overflow-y-auto">
            
            {{-- Pindahkan error message ke sini --}}
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul class="list-disc ml-4">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('payment.process') }}" method="POST" class="space-y-5" id="payment-form">
                @csrf
                <div class="font-semibold text-base text-gray-900 mb-2">Customer Information</div>

                <div>
                    <label class="block text-sm font-semibold mb-1 text-gray-700" for="customer_name">Nama</label>
                    <input type="text" name="customer_name" id="customer_name" required
                        value="{{ old('customer_name') }}"
                        class="w-full border border-gray-200 rounded-lg px-4 py-3 focus:ring-2 focus:ring-[#811D0E] focus:outline-none transition text-base bg-white placeholder:text-gray-300"
                        placeholder="Masukkan nama Anda">
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1 text-gray-700" for="customer_phone">No. HP</label>
                    <input type="text" name="customer_phone" id="customer_phone" required
                        value="{{ old('customer_phone') }}"
                        class="w-full border border-gray-200 rounded-lg px-4 py-3 focus:ring-2 focus:ring-[#811D0E] focus:outline-none transition text-base bg-white placeholder:text-gray-300"
                        placeholder="08xxxxxxxxxx">
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-1 text-gray-700" for="customer_email">Email</label>
                    <input type="email" name="customer_email" id="customer_email" required
                        value="{{ old('customer_email') }}"
                        class="w-full border border-gray-200 rounded-lg px-4 py-3 focus:ring-2 focus:ring-[#811D0E] focus:outline-none transition text-base bg-white placeholder:text-gray-300"
                        placeholder="email@contoh.com">
                </div>

                <!-- Pilihan Tipe Pesanan -->
                <div class="mb-4">
                    <label class="block text-sm font-semibold mb-2 text-gray-700">Tipe Pesanan</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="order_type" value="dine_in" class="sr-only" 
                                {{ old('order_type', 'dine_in') === 'dine_in' ? 'checked' : '' }}>
                            <div class="order-type-option border-2 border-gray-200 rounded-lg p-4 text-center transition-all hover:border-[#811D0E]">
                                <div class="text-2xl mb-2">🍽️</div>
                                <div class="text-sm font-semibold">Makan di Tempat</div>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="order_type" value="takeaway" class="sr-only"
                                {{ old('order_type') === 'takeaway' ? 'checked' : '' }}>
                            <div class="order-type-option border-2 border-gray-200 rounded-lg p-4 text-center transition-all hover:border-[#811D0E]">
                                <div class="text-2xl mb-2">🥡</div>
                                <div class="text-sm font-semibold">Dibawa Pulang</div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Nomor Meja (hanya untuk dine in) -->
                <div class="mb-4" id="table-selection">
                    <label for="table_number" class="block text-sm font-semibold mb-1 text-gray-700">Nomor Meja</label>
                    <select name="table_number" id="table_number"
                        class="w-full border border-gray-200 rounded-lg px-4 py-3 focus:ring-2 focus:ring-[#811D0E] focus:outline-none transition text-base bg-white">
                        <option value="">Pilih Meja</option>
                        @foreach ($tables as $table)
                            <option value="{{ $table->id }}"
                                {{ old('table_number') == $table->id || ($selectedTable && $selectedTable->id == $table->id) ? 'selected' : '' }}>
                                {{ $table->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>

        <div class="fixed bottom-0 left-0 right-0 z-20 bg-white border-t border-gray-100">
            <div class="flex items-center justify-between px-4 py-4 w-full max-w-md mx-auto">
                <div>
                    <div class="text-xs text-gray-400">Total Harga</div>
                    <div class="text-2xl font-bold text-gray-900">Rp. {{ number_format($total, 0, ',', '.') }}</div>
                </div>
                <button type="submit" form="payment-form" id="submit-btn"
                    class="bg-[#811D0E] hover:bg-[#a23a2c] text-white font-bold rounded-xl px-7 py-3 text-base shadow transition">
                    Lanjut Pembayaran
                </button>
            </div>
        </div>
    </div>

    <style>
        .order-type-option {
            transition: all 0.3s ease;
        }

        input[type="radio"]:checked+.order-type-option {
            border-color: #811D0E;
            background-color: #fef2f2;
        }

        #table-selection.hidden {
            display: none;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const orderTypeRadios = document.querySelectorAll('input[name="order_type"]');
            const tableSelection = document.getElementById('table-selection');
            const tableSelect = document.getElementById('table_number');
            const submitBtn = document.getElementById('submit-btn');

            function toggleTableSelection() {
                const selectedType = document.querySelector('input[name="order_type"]:checked').value;

                if (selectedType === 'takeaway') {
                    tableSelection.classList.add('hidden');
                    tableSelect.required = false;
                    tableSelect.value = '';
                } else {
                    tableSelection.classList.remove('hidden');
                    tableSelect.required = true;
                }
            }

            // Event listener untuk perubahan order type
            orderTypeRadios.forEach(radio => {
                radio.addEventListener('change', toggleTableSelection);
            });

            // Initial check
            toggleTableSelection();

            // Prevent double submission
            document.getElementById('payment-form').addEventListener('submit', function(e) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Processing...';
                
                // Re-enable after 5 seconds in case of error
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Lanjut Pembayaran';
                }, 5000);
            });
        });
    </script>
@endsection