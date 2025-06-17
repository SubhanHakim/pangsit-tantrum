<?php

namespace App\Filament\Pages;

use App\Models\Order;
use Filament\Pages\Page;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Actions\Action;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinancialReport extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationLabel = 'Laporan Keuangan';
    protected static ?string $title = 'Laporan Keuangan';
    protected static ?string $navigationGroup = 'Transaksi';

    public ?array $data = [];
    public $startDate;
    public $endDate;

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
        $this->generateReport();
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('Filter Laporan')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            DatePicker::make('startDate')
                                ->label('Tanggal Mulai')
                                ->required()
                                ->displayFormat('d M Y'),
                            DatePicker::make('endDate')
                                ->label('Tanggal Akhir')
                                ->required()
                                ->displayFormat('d M Y'),
                        ])
                ])
                ->collapsible()
                ->columnSpanFull(),
        ];
    }

    public function generateReport()
    {
        $query = Order::query()
            ->whereDate('created_at', '>=', $this->startDate)
            ->whereDate('created_at', '<=', $this->endDate)
            ->where('status', 'success');

        // Total Pendapatan
        $totalRevenue = $query->sum('total');

        // Jumlah Transaksi
        $totalTransactions = $query->count();

        // Rata-rata per Transaksi
        $averageTransaction = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

        // Pendapatan per Hari
        $dailyRevenue = Order::query()
            ->whereDate('created_at', '>=', $this->startDate)
            ->whereDate('created_at', '<=', $this->endDate)
            ->where('status', 'success')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total) as revenue'))
            ->groupBy('date')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->date => $item->revenue];
            });

        // Menu Terlaris
        $topMenus = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('menus', 'order_items.menu_id', '=', 'menus.id')
            ->select('menus.name', DB::raw('SUM(order_items.quantity) as total_qty'))
            ->whereDate('orders.created_at', '>=', $this->startDate)
            ->whereDate('orders.created_at', '<=', $this->endDate)
            ->where('orders.status', 'success')
            ->groupBy('menus.name')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        $this->data = [
            'totalRevenue' => $totalRevenue,
            'totalTransactions' => $totalTransactions,
            'averageTransaction' => $averageTransaction,
            'dailyRevenue' => $dailyRevenue,
            'topMenus' => $topMenus,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ];
    }

    protected function getActions(): array
    {
        return [
            Action::make('print')
                ->label('Cetak PDF')
                ->icon('heroicon-o-printer')
                ->action(function () {
                    return response()->streamDownload(function () {
                        echo $this->generatePdf()->output();
                    }, "laporan-keuangan-{$this->startDate}-sampai-{$this->endDate}.pdf");
                }),
        ];
    }

    protected function generatePdf()
    {
        $this->generateReport();

        $pdf = Pdf::loadView('reports.financial', [
            'data' => $this->data,
        ]);

        return $pdf;
    }

    protected function getViewData(): array
    {
        return [
            'data' => $this->data,
        ];
    }

    protected static string $view = 'filament.pages.financial-report';
}
