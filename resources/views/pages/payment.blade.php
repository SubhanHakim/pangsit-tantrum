{{-- filepath: resources/views/pages/payment.blade.php --}}
@extends('layouts.app')

@section('title', 'Payment')

@section('content')
    <div class="min-h-screen flex flex-col items-center justify-start bg-gray-50 pt-6">
        <div class="w-full rounded-2xl p-6 mt-4">
            <h2 class="text-2xl font-bold mb-6 text-center text-[#811D0E]">Customer Information</h2>
            <form action="{{ route('payment.process') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-semibold mb-1 text-gray-700">Nama</label>
                    <input type="text" name="customer_name" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#811D0E] focus:outline-none transition text-base bg-gray-50 bg-white">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1 text-gray-700">No. HP</label>
                    <input type="text" name="customer_phone" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#811D0E] focus:outline-none transition text-base bg-gray-50 bg-white">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1 text-gray-700">Email</label>
                    <input type="email" name="customer_email" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#811D0E] focus:outline-none transition text-base bg-gray-50 bg-white">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1 text-gray-700">Nomor Meja</label>
                    <select name="table_number" required
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#811D0E] focus:outline-none transition text-base bg-gray-50 bg-white">
                        <option value="" disabled selected>Pilih Nomor Meja</option>
                        @foreach ($tables as $table)
                            <option value="{{ $table->name }}">{{ $table->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex justify-between items-center mt-6 px-1">
                    <span class="font-semibold text-gray-700">Total yang harus dibayar:</span>
                    <span class="font-bold text-ngekos-orange text-xl">Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>
                <button type="submit"
                    class="w-full h-[50px] mt-6 rounded-xl bg-[#811D0E] text-white font-bold text-lg flex items-center justify-center shadow transition hover:bg-orange-700 ">
                    Bayar Sekarang
                </button>
                <h1>testtt</h1>
            </form>
        </div>
    </div>
@endsection