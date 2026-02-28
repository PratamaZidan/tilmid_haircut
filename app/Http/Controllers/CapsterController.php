<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingAddon;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CapsterHistoryExport;

class CapsterController extends Controller
{
    public function dashboard(Request $r)
    {
        $user = Auth::user();
        
        // Search
        $q = trim((string) $r->query('q', ''));

        // filter history (default 7 hari)
        $range = $r->query('range', '7');
        $today = now()->toDateString();

        $startDate = null;
            if ($range === '1') {
                $startDate = $today;
            } elseif ($range === '7' || $range === '30') {
                $startDate = now()->subDays((int)$range)->toDateString();
            } elseif ($range === 'all') {
                $startDate = null; // tanpa filter tanggal
            } else {
                // fallback kalau ada value aneh
                $startDate = now()->subDays(7)->toDateString();
                $range = '7';
        }

        // Booking hari ini (capster login)
        $bookDate = $r->query('date', now()->toDateString()); // default hari ini

        $todayBookings = Booking::query()
        ->where('capster_id', $user->id)
        ->where('booking_date', $bookDate)
        ->orderBy('booking_time')
        ->get();

        // History (capster login)
        $historyQuery = Booking::query()
            ->where('capster_id', $user->id);

        if ($startDate) {
            $historyQuery->whereDate('booking_date', '>=', $startDate);
        }

        if ($q !== '') {
            $historyQuery->where(function ($w) use ($q) {
                $w->where('customer_name', 'like', "%{$q}%")
                ->orWhere('service_label', 'like', "%{$q}%")
                ->orWhere('code', 'like', "%{$q}%");
            });
        }

        if ($range === '1') {
            // Hari ini: ambil yang tanggalnya persis hari ini
            $historyQuery->where('booking_date', $today);

        } elseif ($range === 'all') {
            // Semua: tidak pakai filter tanggal

        } else {
            // 7 atau 30 hari terakhir: mulai dari N hari yang lalu
            $days = (int) $range;
            $historyQuery->whereDate('booking_date', '>=', now()->subDays($days)->toDateString());
        }

        $history = $historyQuery
            ->orderByDesc('booking_date')
            ->orderByDesc('booking_time')
            ->withSum('addons as addons_sum', 'amount')
            ->get()
            ->map(function ($b) {
                $rawTotal = (int)$b->service_price + (int)($b->addons_sum ?? 0);

                // cancelled -> tidak dihitung pendapatan
                $b->total = $b->status === 'cancelled' ? 0 : $rawTotal;

                return $b;
            });

        $historyTotal = $history->sum('total');

        // Services treatment untuk add-on dropdown
        $addonServices = Service::query()
            ->where('is_active', true)
            ->where('category', 'treatment')
            ->orderBy('sort_order')
            ->get(['code','name','price']);

        $servicesPublic = Service::query()
            ->where('is_active', true)
            ->where('is_public', true)
            ->orderBy('category')
            ->orderBy('sort_order')
            ->get();

        return view('capster.dashboard', compact('todayBookings', 'history', 'historyTotal', 'addonServices','servicesPublic', 'range', 'q', 'bookDate'));
    }

    public function exportHistory(Request $r)
        {
            $range = $r->query('range', '7');
            $filename = 'history-'.$range.'-'.now()->format('Y-m-d').'.xlsx';

            return Excel::download(new CapsterHistoryExport($range), $filename);
        }

    public function markDone(Booking $booking)
    {
        $user = Auth::user();
        abort_unless($booking->capster_id === $user->id, 403);

        $booking->update(['status' => 'done']);
        return back();
    }

    public function cancel(Booking $booking)
    {
        $user = Auth::user();
        abort_unless($booking->capster_id === $user->id, 403);

        $booking->update(['status' => 'cancelled']);
        return back();
    }

    public function storeAddon(Request $r)
    {
        $user = Auth::user();

        $data = $r->validate([
            'booking_id' => 'required|exists:bookings,id',
            'addon_code' => 'required|exists:services,code',
            'amount'     => 'required|integer|min:0',
            'date'       => 'required|date',
            'note'       => 'nullable|string|max:255',
        ]);

        $booking = Booking::findOrFail($data['booking_id']);
        abort_unless($booking->capster_id === $user->id, 403);

        $svc = Service::where('code', $data['addon_code'])->firstOrFail();

        BookingAddon::create([
            'booking_id'  => $booking->id,
            'addon_type'  => 'treatment',
            'addon_label' => $svc->name,
            'amount'      => $data['amount'],
            'note'        => $data['note'] ?? null,
        ]);

        return back();
    }

    public function storeWalkin(Request $r)
    {
        $user = Auth::user();

        $data = $r->validate([
            'date' => 'required|date',
            'time' => 'required',
            'customer_name' => 'required|string|max:100',
            'customer_whatsapp' => 'nullable|string|max:30',
            'service_code' => 'required|exists:services,code',
            'status' => 'required|in:confirmed,done',
            'note' => 'nullable|string|max:255',
        ]);

        $svc = Service::where('code', $data['service_code'])
            ->where('is_active', true)
            ->firstOrFail();

        $code = 'WALK-' . strtoupper(\Illuminate\Support\Str::random(6));

        Booking::create([
            'code' => $code,
            'customer_name' => $data['customer_name'],
            'customer_whatsapp' => $data['customer_whatsapp'] ?? '-',
            'service_code' => $svc->code,
            'service_label' => $svc->name,
            'service_price' => $svc->price,
            'booking_date' => $data['date'],
            'booking_time' => $data['time'],
            'capster_id' => $user->id, // penting: pakai capster login
            'status' => $data['status'],
            'notes' => $data['note'] ?? null,
        ]);

        return back();
    }

    public function notify()
    {
        $user = Auth::user();
        $today = now()->toDateString();

        $count = Booking::query()
            ->where('capster_id', $user->id)
            ->where('booking_date', $today)
            ->whereIn('status', ['pending','confirmed'])
            ->count();

        // ambil booking terbaru juga biar bisa tampil nama/jam
        $latest = Booking::query()
            ->where('capster_id', $user->id)
            ->where('booking_date', $today)
            ->whereIn('status', ['pending','confirmed'])
            ->orderByDesc('id')
            ->first(['id','customer_name','booking_time','service_label','code']);

        return response()->json([
            'count' => $count,
            'latest' => $latest,
            'ts' => now()->timestamp,
        ]);
    }
}