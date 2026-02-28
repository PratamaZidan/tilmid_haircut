<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Service;

class BookingController extends Controller
{
    public function create()
    {
        $capsters = User::where('role','capster')->where('status','aktif')->orderBy('name')->get(['id','name']);

        $services = Service::where('is_active', true)
            ->where('is_public', true)
            ->orderBy('category')
            ->orderBy('sort_order')
            ->get(['code','name','price','category']);

        return view('booking', compact('capsters','services'));
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'nama' => 'required|string|max:100',
            'whatsapp' => 'required|string|max:30',
            'layanan' => 'required|string',
            'tanggal' => 'required|date',
            'jam' => 'required',
            'capster' => 'required|exists:users,id',
        ]);

        $capster = User::where('id', $data['capster'])->where('role','capster')->firstOrFail();

        $service = Service::where('code', $data['layanan'])
            ->where('is_active', true)
            ->where('is_public', true)
            ->firstOrFail();

        // kode booking unik (mis. TLM-8F3K2A)
        $code = 'TLM-' . strtoupper(Str::random(6));

        // (opsional) cek bentrok jadwal capster
        $conflict = Booking::where('capster_id', $capster->id)
            ->where('booking_date', $data['tanggal'])
            ->where('booking_time', $data['jam'])
            ->whereIn('status', ['pending','confirmed'])
            ->exists();
        if ($conflict) {
            return back()->withErrors(['jam' => 'Slot waktu sudah terisi, pilih jam lain.'])->withInput();
        }

        $booking = Booking::create([
            'code' => $code,
            'customer_name' => $data['nama'],
            'customer_whatsapp' => $data['whatsapp'],
            'service_code'  => $service->code,
            'service_label' => $service->name,
            'service_price' => $service->price,
            'booking_date' => $data['tanggal'],
            'booking_time' => $data['jam'],
            'capster_id' => $capster->id,
            'status' => 'pending',
        ]);

        return redirect("/booking/success/{$booking->code}");
    }

    public function success(string $code)
    {
        $booking = Booking::with('capster')->where('code', $code)->firstOrFail();
        return view('booking-success', compact('booking'));
    }
}
