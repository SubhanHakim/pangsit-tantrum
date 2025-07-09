{{-- filepath: d:\project\client\pangsit-tantrum\resources\views\pages\order-detail.blade.php --}}
@extends('layouts.app')
@section('title', 'Detail Pesanan')
@section('content')
    <div
        class="w-full max-w-lg mx-auto mt-10 bg-white rounded-xl shadow-lg p-7 border border-dashed border-gray-300 relative font-mono">
        <!-- Header Struk -->
        <div class="text-center mb-4 mt-2">
            <div class="text-2xl font-bold text-[#811D0E] tracking-widest">STRUK PESANAN</div>
            <div class="text-base text-gray-400">Terima kasih telah berbelanja!</div>
            <!-- Alamat Restaurant -->
            <div class="text-xs text-gray-500 mt-2">
                Pangsit Tantrum<br>
                Jl. Tentara Pelajar<br>
                Kel Empang sari, Kec Tawang<br>
                Kota Tasikmalaya<br>
            </div>
        </div>

        <div class="border-t border-dashed border-gray-300 my-3"></div>

        <!-- Informasi Pesanan -->
        <div class="text-sm space-y-3">
            <div class="flex justify-between">
                <span>Order ID</span>
                <span class="font-semibold">{{ $order->order_id }}</span>
            </div>
            <div class="flex justify-between">
                <span>Nama</span>
                <span class="font-semibold">{{ $order->customer_name }}</span>
            </div>
            <div class="flex justify-between">
                <span>No HP</span>
                <span>{{ $order->customer_phone }}</span>
            </div>
            <div class="flex justify-between">
                <span>Email</span>
                <span>{{ $order->customer_email }}</span>
            </div>
            
            <!-- Tipe Pesanan -->
            <div class="flex justify-between">
                <span>Tipe Pesanan</span>
                <span class="font-semibold">
                    @if ($order->order_type === 'dine_in')
                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded">
                            🍽️ Makan di Tempat
                        </span>
                    @elseif($order->order_type === 'takeaway')
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded">
                            🥡 Dibawa Pulang
                        </span>
                    @else
                        <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded">
                            ❓ Tidak Diketahui
                        </span>
                    @endif
                </span>
            </div>
            
            <!-- Nomor Meja (hanya untuk dine-in) -->
            @if ($order->order_type === 'dine_in')
                <div class="flex justify-between">
                    <span>Nomor Meja</span>
                    <span class="font-semibold bg-[#811D0E] text-white px-2 py-1 rounded">
                        @if($order->table_number)
                            @php
                                // Jika table_number adalah ID, ambil nama meja
                                $table = \App\Models\Table::find($order->table_number);
                                $tableName = $table ? $table->name : 'Meja ' . $order->table_number;
                            @endphp
                            {{ $tableName }}
                        @else
                            -
                        @endif
                    </span>
                </div>
            @endif
            
            <div class="flex justify-between">
                <span>Waktu Pesan</span>
                <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
            </div>
        </div>

        <div class="border-t border-dashed border-gray-300 my-3"></div>

        <!-- Daftar Makanan -->
        <div class="text-sm mb-2 font-bold text-gray-700">Daftar Makanan:</div>
        <ul class="text-sm mb-2">
            @forelse ($order->items as $item)
                <li class="py-2 border-b border-gray-100 last:border-b-0">
                    <div class="flex justify-between">
                        <span class="font-medium">{{ $item->menu->name ?? '-' }}</span>
                        <span class="font-semibold">x{{ $item->quantity }}</span>
                    </div>
                    @if ($item->toppings && is_string($item->toppings) && json_decode($item->toppings))
                        <div class="text-xs text-gray-500 ml-4 mt-1">
                            @foreach (json_decode($item->toppings) as $topping)
                                <div>+ {{ $topping->name }} (Rp {{ number_format($topping->price, 0, ',', '.') }})</div>
                            @endforeach
                        </div>
                    @endif
                    @if ($item->spiciness_level)
                        <div class="text-xs text-gray-500 ml-4 mt-1">
                            Level Pedas:
                            @switch($item->spiciness_level)
                                @case('original')
                                    <span class="bg-gray-100 px-2 py-1 rounded">Original (Tidak Pedas)</span>
                                @break

                                @case('mild')
                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded">Sedikit Pedas</span>
                                @break

                                @case('medium')
                                    <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded">Pedas Sedang</span>
                                @break

                                @case('extra_pedas')
                                    <span class="bg-red-100 text-red-800 px-2 py-1 rounded">Extra Pedas</span>
                                @break

                                @default
                                    <span class="bg-gray-100 px-2 py-1 rounded">{{ $item->spiciness_level }}</span>
                            @endswitch
                        </div>
                    @endif
                    @if ($item->note)
                        <div class="text-xs text-gray-500 ml-4 mt-1 bg-gray-50 p-2 rounded">
                            <span class="font-semibold">Catatan:</span> {{ $item->note }}
                        </div>
                    @endif
                </li>
                @empty
                    <li class="text-gray-400 italic">Tidak ada item.</li>
                @endforelse
            </ul>

            <div class="border-t border-dashed border-gray-300 my-3"></div>

            <!-- Total -->
            <div class="flex justify-between text-lg font-bold">
                <span>Total</span>
                <span class="text-[#811D0E]">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
            </div>

            <div class="border-t border-dashed border-gray-300 my-3"></div>

            <!-- Status Section -->
            <div class="space-y-2">
                <!-- Status Pembayaran -->
                <div class="flex justify-between text-sm">
                    <span>Status Pembayaran</span>
                    <span>
                        @if ($order->status == 'success' || $order->status == 'paid')
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-semibold">
                                ✓ Sudah Dibayar
                            </span>
                        @elseif($order->status == 'pending')
                            <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs font-semibold">
                                ⏳ Menunggu
                            </span>
                        @elseif($order->status == 'challenge')
                            <span class="bg-orange-100 text-orange-800 px-2 py-1 rounded-full text-xs font-semibold">
                                ⚠️ Verifikasi
                            </span>
                        @elseif($order->status == 'expired')
                            <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs font-semibold">
                                ⏰ Kadaluarsa
                            </span>
                        @else
                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-semibold">
                                ❌ Gagal
                            </span>
                        @endif
                    </span>
                </div>

                <!-- Status Makanan -->
                <div class="flex justify-between text-sm">
                    <span>Status Makanan</span>
                    <span>
                        @if ($order->food_status == 'pending')
                            <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs font-semibold">
                                ⏳ Menunggu Konfirmasi
                            </span>
                        @elseif($order->food_status == 'preparing')
                            <span class="bg-orange-100 text-orange-800 px-2 py-1 rounded-full text-xs font-semibold">
                                👨‍🍳 Sedang Dipersiapkan
                            </span>
                        @elseif($order->food_status == 'ready')
                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-semibold">
                                🔔 Siap Disajikan
                            </span>
                        @elseif($order->food_status == 'completed')
                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-semibold">
                                ✅ Selesai
                            </span>
                        @else
                            <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs font-semibold">
                                ❓ Status Tidak Diketahui
                            </span>
                        @endif
                    </span>
                </div>
            </div>

            <!-- Pesan untuk customer berdasarkan status dan tipe pesanan -->
            <div class="mt-4 p-3 rounded-lg text-sm">
                @if ($order->food_status == 'pending')
                    <div class="bg-yellow-50 border border-yellow-200 text-yellow-800">
                        <div class="font-semibold">⏳ Pesanan Anda sedang menunggu konfirmasi</div>
                        <div class="text-xs mt-1">Mohon tunggu, pesanan Anda akan segera diproses</div>
                    </div>
                @elseif($order->food_status == 'preparing')
                    <div class="bg-orange-50 border border-orange-200 text-orange-800">
                        <div class="font-semibold">👨‍🍳 Pesanan Anda sedang dipersiapkan</div>
                        <div class="text-xs mt-1">Mohon bersabar, chef sedang menyiapkan makanan Anda</div>
                    </div>
                @elseif($order->food_status == 'ready')
                    <div class="bg-blue-50 border border-blue-200 text-blue-800">
                        <div class="font-semibold">🔔 Pesanan Anda siap disajikan!</div>
                        @if ($order->order_type === 'dine_in')
                            <div class="text-xs mt-1">Makanan Anda sudah siap dan akan segera diantar ke meja</div>
                        @else
                            <div class="text-xs mt-1">Makanan Anda sudah siap untuk diambil</div>
                        @endif
                    </div>
                @elseif($order->food_status == 'completed')
                    <div class="bg-green-50 border border-green-200 text-green-800">
                        <div class="font-semibold">✅ Pesanan selesai!</div>
                        @if ($order->order_type === 'dine_in')
                            <div class="text-xs mt-1">Selamat menikmati makanan Anda</div>
                        @else
                            <div class="text-xs mt-1">Terima kasih telah memesan. Selamat menikmati!</div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Informasi tambahan berdasarkan tipe pesanan -->
            @if ($order->order_type === 'takeaway')
                <div class="mt-2 p-3 bg-green-50 border border-green-200 rounded-lg text-sm">
                    <div class="font-semibold text-green-800">📦 Informasi Takeaway</div>
                    <div class="text-xs text-green-700 mt-1">
                        • Pesanan akan dikemas untuk dibawa pulang<br>
                        • Silakan tunggu konfirmasi melalui WhatsApp<br>
                        • Ambil pesanan di counter saat sudah siap
                    </div>
                </div>
            @elseif ($order->order_type === 'dine_in')
                <div class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-lg text-sm">
                    <div class="font-semibold text-blue-800">🍽️ Informasi Dine-in</div>
                    <div class="text-xs text-blue-700 mt-1">
                        • Pesanan akan disajikan di meja Anda<br>
                        • Mohon tetap di meja saat pesanan dipersiapkan<br>
                        • Waktu persiapan estimasi 10-15 menit
                    </div>
                </div>
            @endif

            <div class="border-t border-dashed border-gray-300 my-3"></div>

            <!-- Footer -->
            <div class="text-center text-xs text-gray-400 mt-2 mb-6">
                * Simpan struk ini sebagai bukti pembayaran<br>
                * Refresh halaman untuk update status terbaru
            </div>

            <!-- Tombol Kembali -->
            <div class="flex justify-center space-x-3">
                <a href="{{ url('/') }}"
                    class="inline-flex items-center gap-2 bg-[#811D0E] hover:bg-orange-700 text-white font-semibold px-6 py-2 rounded-full shadow transition text-base">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Kembali ke Beranda
                </a>

                <button onclick="refreshStatus()"
                    class="inline-flex items-center gap-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold px-6 py-2 rounded-full shadow transition text-base">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Refresh Status
                </button>
            </div>
        </div>

        <!-- Auto refresh script -->
        <script>
            // Auto refresh setiap 30 detik untuk update status
            setInterval(function() {
                location.reload();
            }, 30000);

            // Tambahkan loading state saat refresh
            function refreshStatus() {
                const button = event.target;
                const originalHTML = button.innerHTML;
                button.disabled = true;
                button.innerHTML = '<svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Memuat...';
                
                setTimeout(() => {
                    location.reload();
                }, 1000);
            }

            // Tambahkan notifikasi visual untuk perubahan status
            @if(session('status_updated'))
                // Bisa ditambahkan notifikasi toast di sini
                console.log('Status updated!');
            @endif
        </script>
    </div>
@endsection