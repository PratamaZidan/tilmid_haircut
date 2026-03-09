<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use App\Models\Service;
use App\Services\BookingAvailabilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function create()
    {
        $capsters = User::where('role', 'capster')
            ->where('status', 'aktif') // ganti ke is_active kalau project-mu pakai boolean
            ->orderBy('name')
            ->get(['id', 'name']);

        $services = Service::where('is_active', true)
            ->where('is_public', true)
            ->orderBy('category')
            ->orderBy('sort_order')
            ->get(['code', 'name', 'description' ,'price', 'category']);

        return view('booking', compact('capsters', 'services'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:100',
            'whatsapp' => 'required|string|max:30',
            'layanan' => 'required|string',
            'tanggal' => 'required|date',
            'jam' => 'required|date_format:H:i',
            'capster' => 'required|exists:users,id',
        ]);

        $capster = User::where('id', $data['capster'])
            ->where('role', 'capster')
            ->firstOrFail();

        $service = Service::where('code', $data['layanan'])
            ->where('is_active', true)
            ->where('is_public', true)
            ->firstOrFail();

        $availability = app(BookingAvailabilityService::class)
            ->getAvailableSlots($data['tanggal'], (int) $capster->id);

        if (!in_array($data['jam'], $availability['available_slots'], true)) {
            return back()
                ->withErrors(['jam' => 'Slot tidak tersedia. Silakan pilih slot lain.'])
                ->withInput();
        }

        $code = 'TLM-' . strtoupper(Str::random(6));

        $booking = Booking::create([
            'code' => $code,
            'customer_name' => $data['nama'],
            'customer_whatsapp' => $data['whatsapp'],
            'service_code' => $service->code,
            'service_label' => $service->name,
            'service_price' => $service->price,
            'booking_date' => $data['tanggal'],
            'booking_time' => $data['jam'],
            'capster_id' => $capster->id,
            'status' => 'pending',
        ]);

        return redirect("/booking/success/{$booking->code}");
    }

    public function availability(Request $request, BookingAvailabilityService $service)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'capster_id' => 'required|exists:users,id',
        ]);

        return response()->json(
            $service->getAvailableSlots($data['date'], (int) $data['capster_id'])
        );
    }

    public function disabledDates(Request $request, BookingAvailabilityService $service)
    {
        $data = $request->validate([
            'month' => 'required|date_format:Y-m',
            'capster_id' => 'required|exists:users,id',
        ]);

        return response()->json([
            'month' => $data['month'],
            'disabled_dates' => $service->getFullyBookedDates((int) $data['capster_id'], $data['month']),
        ]);
    }

    public function success(string $code)
    {
        $booking = Booking::with('capster')
            ->where('code', $code)
            ->firstOrFail();

        return view('booking-success', compact('booking'));
    }
}