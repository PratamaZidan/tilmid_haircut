<?php

namespace App\Exports;

use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CapsterHistoryExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private string $range) {}

    public function collection()
    {
        $user = Auth::user();
        $today = now()->toDateString();

        $q = Booking::query()
            ->where('capster_id', $user->id)
            ->withSum('addons as addons_sum', 'amount')
            ->orderByDesc('booking_date')
            ->orderByDesc('booking_time');

        if ($this->range === '1') {
            $q->where('booking_date', $today);
        } elseif ($this->range === 'all') {
            // no filter
        } else {
            $days = (int) $this->range; // 7/30
            $q->whereDate('booking_date', '>=', now()->subDays($days)->toDateString());
        }

        return $q->get();
    }

    public function headings(): array
    {
        return ['Tanggal', 'Jam', 'Nama', 'Layanan', 'Status', 'Total'];
    }

    public function map($b): array
    {
        $total = (int)$b->service_price + (int)($b->addons_sum ?? 0);

        return [
            $b->booking_date,
            substr($b->booking_time, 0, 5),
            $b->customer_name,
            $b->service_label,
            $b->status,
            $total,
        ];
    }
}