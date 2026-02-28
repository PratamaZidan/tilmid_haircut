<?php

namespace App\Exports;

use App\Models\Booking;
use App\Models\FinanceTransaction;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class AdminLedgerExport implements FromCollection, WithHeadings, WithMapping, WithCustomStartCell, WithEvents
{
    public function __construct(
        private string $range = '7',
        private string $q = ''
    ) {}

    private function startDate(): ?string
    {
        if ($this->range === 'all') return null;
        $days = (int) $this->range;
        return now()->subDays($days)->toDateString();
    }

    public function collection()
    {
        $startDate = $this->startDate();
        $q = trim($this->q);

        // ===== booking income =====
        $bookingQ = Booking::query()
            ->with('capster:id,name')
            ->withSum('addons as addons_sum', 'amount')
            ->where('status','done');

        if ($startDate) {
            $bookingQ->whereDate('booking_date', '>=', $startDate);
        }

        if ($q !== '') {
            $bookingQ->where(function($w) use ($q){
                $w->where('customer_name','like',"%{$q}%")
                  ->orWhere('service_label','like',"%{$q}%")
                  ->orWhere('code','like',"%{$q}%");
            });
        }

        $bookingRows = $bookingQ->get()->map(function ($b) {
            $total = (int)$b->service_price + (int)($b->addons_sum ?? 0);

            return (object)[
                'date' => $b->booking_date,
                'time' => substr($b->booking_time,0,5),
                'source' => 'Booking',
                'capster' => optional($b->capster)->name,
                'type' => 'Masuk',
                'amount' => $total,
                'note' => "Booking: {$b->customer_name} • {$b->service_label} ({$b->code})",
            ];
        });

        // ===== manual finance =====
        $manualQ = FinanceTransaction::query()
            ->with('capster:id,name');

        if ($startDate) {
            $manualQ->whereDate('date', '>=', $startDate);
        }

        if ($q !== '') {
            $manualQ->where(function($w) use ($q){
                $w->where('note','like',"%{$q}%")
                  ->orWhere('category','like',"%{$q}%")
                  ->orWhere('method','like',"%{$q}%");
            });
        }

        $manualRows = $manualQ->get()->map(function ($tx) {
            return (object)[
                'date' => $tx->date,
                'time' => '',
                'source' => 'Manual',
                'capster' => optional($tx->capster)->name,
                'type' => ucfirst($tx->type),
                'amount' => (int)$tx->amount,
                'note' => $tx->note,
            ];
        });

        /** @var Collection $all */
        $all = $bookingRows->concat($manualRows)
            ->sortByDesc(fn($x) => $x->date.' '.$x->time)
            ->values();

        return $all;
    }

    public function headings(): array
    {
        return ['Tanggal','Jam','Sumber','Capster','Jenis','Nominal','Keterangan'];
    }

    public function map($row): array
    {
        return [
            $row->date,
            $row->time,
            $row->source,
            $row->capster ?? '-',
            $row->type,
            $row->amount,
            $row->note,
        ];
    }

    public function startCell(): string
{
    // data table mulai dari A6 (karena A1-A4 dipakai ringkasan)
    return 'A6';
}

public function registerEvents(): array
{
    return [
        AfterSheet::class => function (AfterSheet $event) {
            [$income, $expense, $saldo] = $this->summary();

            $sheet = $event->sheet->getDelegate();

            // Ringkasan
            $sheet->setCellValue('A1', 'Ringkasan');
            $sheet->setCellValue('A2', 'Total Pemasukan');
            $sheet->setCellValue('B2', $income);

            $sheet->setCellValue('A3', 'Total Pengeluaran');
            $sheet->setCellValue('B3', $expense);

            $sheet->setCellValue('A4', 'Saldo');
            $sheet->setCellValue('B4', $saldo);

            // sedikit bold biar enak
            $sheet->getStyle('A1')->getFont()->setBold(true);
            $sheet->getStyle('A2:A4')->getFont()->setBold(true);
            $sheet->getStyle('B2:B4')->getFont()->setBold(true);

            // format angka (opsional)
            $sheet->getStyle('F6:F9999')->getNumberFormat()->setFormatCode('#,##0');
        }
    ];
}

private function summary(): array
{
    $startDate = $this->startDate();
    $q = trim($this->q);

    // ===== booking total income =====
    $bookingQ = Booking::query()
        ->withSum('addons as addons_sum', 'amount')
        ->where('status', 'done');

    if ($startDate) {
        $bookingQ->whereDate('booking_date', '>=', $startDate);
    }

    if ($q !== '') {
        $bookingQ->where(function($w) use ($q){
            $w->where('customer_name','like',"%{$q}%")
              ->orWhere('service_label','like',"%{$q}%")
              ->orWhere('code','like',"%{$q}%");
        });
    }

    $bookingIncome = $bookingQ->get()
        ->sum(fn($b) => (int)$b->service_price + (int)($b->addons_sum ?? 0));

    // ===== manual totals =====
    $manualQ = FinanceTransaction::query();

    if ($startDate) {
        $manualQ->whereDate('date', '>=', $startDate);
    }

    if ($q !== '') {
        $manualQ->where(function($w) use ($q){
            $w->where('note','like',"%{$q}%")
              ->orWhere('category','like',"%{$q}%")
              ->orWhere('method','like',"%{$q}%");
        });
    }

    $manualIncome = (clone $manualQ)->where('type','masuk')->sum('amount');
    $manualExpense = (clone $manualQ)->where('type','keluar')->sum('amount');

    $income = (int)$bookingIncome + (int)$manualIncome;
    $expense = (int)$manualExpense;
    $saldo = $income - $expense;

    return [$income, $expense, $saldo];
}
}