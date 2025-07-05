{{-- filepath: resources/views/pages/payment.blade.php --}}
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
            <form action="{{ route('payment.process') }}" method="POST" class="space-y-5" id="payment-form">
                @csrf
                <div class="font-semibold text-base text-gray-900 mb-2">Customer Information</div>
                <div>
                    <label class="block text-sm font-semibold mb-1 text-gray-700" for="customer_name">Nama</label>
                    <input type="text" name="customer_name" id="customer_name" required
                        class="w-full border border-gray-200 rounded-lg px-4 py-3 focus:ring-2 focus:ring-[#811D0E] focus:outline-none transition text-base bg-white placeholder:text-gray-300"
                        placeholder="Masukkan nama Anda">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1 text-gray-700" for="customer_phone">No. HP</label>
                    <input type="text" name="customer_phone" id="customer_phone" required
                        class="w-full border border-gray-200 rounded-lg px-4 py-3 focus:ring-2 focus:ring-[#811D0E] focus:outline-none transition text-base bg-white placeholder:text-gray-300"
                        placeholder="08xxxxxxxxxx">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1 text-gray-700" for="customer_email">Email</label>
                    <input type="email" name="customer_email" id="customer_email" required
                        class="w-full border border-gray-200 rounded-lg px-4 py-3 focus:ring-2 focus:ring-[#811D0E] focus:outline-none transition text-base bg-white placeholder:text-gray-300"
                        placeholder="email@contoh.com">
                </div>
                <div class="mb-4">
                    <label for="table_number" class="block text-sm font-medium text-gray-700 mb-1">Nomor Meja</label>
                    <select name="table_number" id="table_number"
                        class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        required>
                        <option value="">Pilih Meja</option>
                        @foreach ($tables as $table)
                            <option value="{{ $table->id }}"
                                {{ $selectedTable && $selectedTable->id == $table->id ? 'selected' : '' }}>
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
                <button type="submit" form="payment-form"
                    class="bg-[#811D0E] hover:bg-[#a23a2c] text-white font-bold rounded-xl px-7 py-3 text-base shadow transition">
                    Lanjut Pembayaran
                </button>
            </div>
        </div>
    </div>
@endsection
