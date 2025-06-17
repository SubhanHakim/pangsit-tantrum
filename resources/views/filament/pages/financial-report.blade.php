<x-filament-panels::page>
    <x-filament::section>
        <form wire:submit="generateReport" class="space-y-6">
            <div class="w-full">
                {{ $this->form }}
            </div>
            <div class="mt-6 flex justify-">
                <x-filament::button type="submit" color="primary">
                    Filter Laporan
                </x-filament::button>
            </div>
        </form>
    </x-filament::section>

    <x-filament::section class="mt-6">
        <div class="grid gap-6 md:grid-cols-3">
            <div class="rounded-lg bg-white p-6 shadow">
                <h3 class="text-lg font-medium">Total Pendapatan</h3>
                <p class="mt-2 text-3xl font-bold text-primary-600">
                    Rp {{ number_format($data['totalRevenue'] ?? 0, 0, ',', '.') }}
                </p>
            </div>
            <div class="rounded-lg bg-white p-6 shadow">
                <h3 class="text-lg font-medium">Jumlah Transaksi</h3>
                <p class="mt-2 text-3xl font-bold text-primary-600">
                    {{ $data['totalTransactions'] ?? 0 }}
                </p>
            </div>
            <div class="rounded-lg bg-white p-6 shadow">
                <h3 class="text-lg font-medium">Rata-rata per Transaksi</h3>
                <p class="mt-2 text-3xl font-bold text-primary-600">
                    Rp {{ number_format($data['averageTransaction'] ?? 0, 0, ',', '.') }}
                </p>
            </div>
        </div>

        <div class="mt-6">
            <h3 class="text-lg font-medium">Menu Terlaris</h3>
            <div class="mt-4 overflow-hidden rounded-lg shadow">
                <table class="w-fit bg-white">
                    <thead>
                        <tr>
                            <th class="border-b px-6 py-3 text-left font-medium text-gray-500">Menu</th>
                            <th class="border-b px-6 py-3 text-right font-medium text-gray-500">Jumlah Terjual</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['topMenus'] ?? [] as $menu)
                            <tr>
                                <td class="border-b px-6 py-4">{{ $menu->name }}</td>
                                <td class="border-b px-6 py-4 text-right">{{ $menu->total_qty }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="border-b px-6 py-4 text-center text-gray-500">Tidak ada data
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </x-filament::section>
</x-filament-panels::page>
