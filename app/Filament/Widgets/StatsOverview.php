<?php

namespace App\Filament\Widgets;

use App\Models\Menu;
use App\Models\Order;
use App\Models\Table;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    
    protected function getStats(): array
    {
        $countMenu = Menu::count();
        $countOrder = Order::count();
        $countTable = Table::count();

        return [
            Stat::make('Total Menu', $countMenu . ' Menu Makanan')
                ->icon('heroicon-o-cube'),
            Stat::make('Total Pembelian', $countOrder . ' Transaksi')
                ->icon('heroicon-o-shopping-cart'),
            Stat::make('Total Pendapatan', 'Rp. ' . number_format(Order::sum('total'), 0, ',', '.'))
                ->icon('heroicon-o-credit-card'),
            Stat::make('Total Meja', $countTable . ' Meja')
                ->icon('heroicon-o-qr-code'),
        ];
    }
}
