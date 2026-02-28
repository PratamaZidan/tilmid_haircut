<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\FinanceTransaction;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AdminLedgerExport;

class AdminController extends Controller
{
    public function dashboard(Request $r)
    {
        // ===== Capster filters =====
        $capsterQ = trim((string)$r->query('capster_q', ''));
        $capsterStatus = $r->query('capster_status', 'all'); // all|aktif|nonaktif

        $capstersQuery = User::query()
            ->where('role', 'capster');

        if ($capsterStatus !== 'all') {
            $capstersQuery->where('status', $capsterStatus);
        }

        if ($capsterQ !== '') {
            $capstersQuery->where(function ($w) use ($capsterQ) {
                $w->where('name', 'like', "%{$capsterQ}%")
                  ->orWhere('username', 'like', "%{$capsterQ}%")
                  ->orWhere('phone', 'like', "%{$capsterQ}%");
            });
        }

        $capsters = $capstersQuery->orderBy('name')->get(['id','name','username','status','phone']);

        // ===== Finance filters =====
        $financeQ = trim((string)$r->query('finance_q', ''));
        $perPage = (int) $r->query('per_page', 10);
        $perPage = max(5, min(50, $perPage)); // clamp 5..50
        $financeRange = $r->query('finance_range', '7'); // 1|7|30|all
        $today = now()->toDateString();

        $startDate = null;
        if ($financeRange === '1') {
            $startDate = $today;                 // hari ini
        } elseif ($financeRange === '7' || $financeRange === '30') {
            $startDate = now()->subDays((int)$financeRange)->toDateString(); // 7/30 hari
        } elseif ($financeRange === 'all') {
            $startDate = null;                   // tanpa filter
        } else {
            $financeRange = '7';
            $startDate = now()->subDays(7)->toDateString();
        }

        $bookingIncomeQuery = Booking::query()
            ->with('capster:id,name')
            ->withSum('addons as addons_sum', 'amount')
            ->where('status', 'done')
            ->orderByDesc('booking_date')
            ->orderByDesc('booking_time');

        if ($startDate) {
            $bookingIncomeQuery->whereDate('booking_date', '>=', $startDate);
            if ($financeRange === '1') $bookingIncomeQuery->where('booking_date', $today);
        }

        if ($financeQ !== '') {
            $bookingIncomeQuery->where(function($w) use ($financeQ){
                $w->where('customer_name','like',"%{$financeQ}%")
                ->orWhere('service_label','like',"%{$financeQ}%")
                ->orWhere('code','like',"%{$financeQ}%");
            });
        }

        $bookingIncome = $bookingIncomeQuery->get()
            ->map(function ($b) {
                $b->total_income = (int)$b->service_price + (int)($b->addons_sum ?? 0);
                return $b;
            });

        $bookingIncomeTotal = $bookingIncome->sum('total_income');

        $financeQuery = FinanceTransaction::query()
            ->with(['capster:id,name', 'creator:id,name'])
            ->orderByDesc('date')
            ->orderByDesc('id');

        if ($financeQ !== '') {
        $bookingIncomeQuery->where(function($w) use ($financeQ){
        $w->where('customer_name','like',"%{$financeQ}%")
          ->orWhere('service_label','like',"%{$financeQ}%")
          ->orWhere('code','like',"%{$financeQ}%");
    });
}

        // if ($financeRange !== 'all') {
        //     $days = (int)$financeRange;
        //     $financeQuery->whereDate('date', '>=', now()->subDays($days)->toDateString());
        // }

        // // if ($financeQ !== '') {
        // //     $bookingIncomeQuery->where(function($w) use ($financeQ){
        // //         $w->where('customer_name','like',"%{$financeQ}%")
        // //         ->orWhere('service_label','like',"%{$financeQ}%")
        // //         ->orWhere('code','like',"%{$financeQ}%");
        // //     });
        // // }

        if ($financeQ !== '') {
            $financeQuery->where(function ($w) use ($financeQ) {
                $w->where('note', 'like', "%{$financeQ}%")
                ->orWhere('category', 'like', "%{$financeQ}%")
                ->orWhere('method', 'like', "%{$financeQ}%");
            });
        }

        $finance = $financeQuery->get();

        $bookingRows = $bookingIncome->map(function ($b) {
            return (object)[
                'date' => $b->booking_date,
                'source' => 'booking',
                'capster_name' => optional($b->capster)->name,
                'type' => 'masuk',
                'amount' => (int)$b->total_income,
                'note' => "Booking: {$b->customer_name} • {$b->service_label} ({$b->code})",
                'can_edit' => false,
                'id' => $b->id,
                'time' => $b->booking_time,
            ];
        });

        $manualRows = $finance->map(function ($tx) {
            return (object)[
                'date' => $tx->date,
                'source' => 'manual',
                'capster_name' => optional($tx->capster)->name,
                'type' => $tx->type,
                'amount' => (int)$tx->amount,
                'note' => $tx->note,
                'can_edit' => true,
                'id' => $tx->id,
                'category' => $tx->category,
                'method' => $tx->method,
                'capster_id' => $tx->capster_id,
                'time' => null,
            ];
        });

        $ledger = $bookingRows
            ->concat($manualRows)
            ->sortByDesc(fn($x) => $x->date . ' ' . ($x->time ?? '23:59'))
            ->values();
        
        $page = (int) $r->query('page', 1);
        $page = max(1, $page);

        $ledgerPage = new \Illuminate\Pagination\LengthAwarePaginator(
            $ledger->forPage($page, $perPage)->values(),
            $ledger->count(),
            $perPage,
            $page,
            [
                'path' => url('/admin'),
                'query' => $r->query(), // biar querystring kebawa
            ]
        );

        // ===== Stats bulan ini =====
        $startMonth = now()->startOfMonth()->toDateString();
        $endMonth = now()->endOfMonth()->toDateString();

        $bookingIncomeMonth = Booking::query()
            ->where('status','done')
            ->whereBetween('booking_date', [$startMonth, $endMonth])
            ->withSum('addons as addons_sum', 'amount')
            ->get()
            ->sum(fn($b) => (int)$b->service_price + (int)($b->addons_sum ?? 0));

        $manualIncomeMonth = FinanceTransaction::where('type','masuk')
            ->whereBetween('date', [$startMonth,$endMonth])
            ->sum('amount');

        $expenseMonth = FinanceTransaction::where('type','keluar')
            ->whereBetween('date', [$startMonth,$endMonth])
            ->sum('amount');

        $incomeMonth = (int)$bookingIncomeMonth + (int)$manualIncomeMonth;
        $saldoMonth = (int)$incomeMonth - (int)$expenseMonth;

        return view('admin.dashboard', compact(
            'capsters','capsterQ','capsterStatus',
            'financeQ','financeRange', 'perPage',
            'incomeMonth','expenseMonth','saldoMonth',
            'ledgerPage'
        ));
    }

    // ===== Capster CRUD =====
    public function storeCapster(Request $r)
    {
        $data = $r->validate([
            'name' => ['required','string','max:100'],
            'username' => ['required','string','max:50','unique:users,username'],
            'phone' => ['required','string','max:30'],
            'status' => ['required','in:aktif,nonaktif'],
            'password' => ['required','min:6','confirmed'],
        ]);

        User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'phone' => $data['phone'],
            'status' => $data['status'],
            'role' => 'capster',
            'password' => Hash::make($data['password']),
        ]);

        return back()->with('ok', 'Capster berhasil ditambahkan.');
    }

    public function toggleCapster(User $user)
    {
        abort_unless($user->role === 'capster', 404);

        $user->update([
            'status' => $user->status === 'aktif' ? 'nonaktif' : 'aktif'
        ]);

        return back()->with('ok', 'Status capster diperbarui.');
    }

    public function destroyCapster(User $user)
    {
        abort_unless($user->role === 'capster', 404);
        $user->delete();

        return back()->with('ok', 'Capster dihapus.');
    }

    // ===== Finance CRUD =====
    public function storeFinance(Request $r)
    {
        $data = $r->validate([
            'date' => ['required','date'],
            'type' => ['required','in:masuk,keluar'],
            'category' => ['required','string','max:30'],
            'method' => ['required','string','max:30'],
            'capster_id' => ['nullable','exists:users,id'],
            'amount' => ['required','integer','min:0'],
            'note' => ['required','string','max:255'],
        ]);

        FinanceTransaction::create([
            'date' => $data['date'],
            'type' => $data['type'],
            'category' => $data['category'],
            'method' => $data['method'],
            'amount' => $data['amount'],
            'note' => $data['note'],
            'capster_id' => $data['capster_id'] ?? null,
            'created_by' => Auth::id(),
        ]);

        return back()->with('ok', 'Catatan keuangan tersimpan.');
    }

    public function updateFinance(Request $r, FinanceTransaction $tx)
    {
        $data = $r->validate([
            'date' => ['required','date'],
            'type' => ['required','in:masuk,keluar'],
            'category' => ['required','string','max:30'],
            'method' => ['required','string','max:30'],
            'capster_id' => ['nullable','exists:users,id'],
            'amount' => ['required','integer','min:0'],
            'note' => ['required','string','max:255'],
        ]);

        $tx->update([
            'date' => $data['date'],
            'type' => $data['type'],
            'category' => $data['category'],
            'method' => $data['method'],
            'amount' => $data['amount'],
            'note' => $data['note'],
            'capster_id' => $data['capster_id'] ?? null,
        ]);

        return back()->with('ok', 'Catatan keuangan diupdate.');
    }

    // Hapus catatan keuangan
    public function destroyFinance(FinanceTransaction $tx)
    {
        $tx->delete();
        return back()->with('ok', 'Catatan keuangan dihapus.');
    }

    // export ledger ke Excel
    public function exportLedger(Request $r)
    {
        $range = $r->query('finance_range', '7');
        $q = (string) $r->query('finance_q', '');

        $filename = 'ledger-'.$range.'-'.now()->format('Y-m-d').'.xlsx';

        return Excel::download(new AdminLedgerExport($range, $q), $filename);
    }
}