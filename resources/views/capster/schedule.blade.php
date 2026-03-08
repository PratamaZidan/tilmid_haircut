@extends('layouts.manage')
@section('title', 'Jadwal Kerja Capster')

@section('content')
    @php
        $days = [
            0 => 'Minggu',
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
        ];
    @endphp

    <div class="page-head">
        <div>
            <h1 class="h1">Jadwal Kerja Saya</h1>
            <p class="muted">
                Atur hari kerja, jam mulai, jam selesai, dan interval slot booking.
            </p>
        </div>
        <div class="head-actions">
            <a href="/capster" class="btn btn-ghost">Kembali</a>
        </div>
    </div>

    @if(session('ok_schedule'))
        <section class="card" style="margin-bottom:14px;">
            <span class="badge badge-ok">{{ session('ok_schedule') }}</span>
        </section>
    @endif

    @if($errors->any())
        <section class="card" style="margin-bottom:14px;">
            <div class="badge badge-warn" style="margin-bottom:10px;">Ada data yang belum valid</div>
            <ul class="muted" style="margin:0; padding-left:18px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </section>
    @endif

    <section class="card">
        <form class="form" method="POST" action="/capster/schedule">
            @csrf

            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width:18%;">Hari</th>
                            <th style="width:16%;">Status</th>
                            <th style="width:20%;">Jam Mulai</th>
                            <th style="width:20%;">Jam Selesai</th>
                            <th style="width:18%;">Interval Slot</th>
                            <th style="width:8%; text-align:right;">Info</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($days as $dayNumber => $dayName)
                            @php
                                $row = $schedules[$dayNumber] ?? null;
                                $isWorking = $row ? (bool) $row->is_working : true;
                                $startTime = $row?->start_time ? \Carbon\Carbon::parse($row->start_time)->format('H:i') : '13:00';
                                $endTime = $row?->end_time ? \Carbon\Carbon::parse($row->end_time)->format('H:i') : '21:00';
                                $interval = $row?->slot_interval_minutes ?? 60;
                            @endphp

                            <tr>
                                <td>
                                    <strong>{{ $dayName }}</strong>
                                </td>

                                <td>
                                    <label class="switch-inline">
                                        <input
                                            type="checkbox"
                                            name="schedules[{{ $dayNumber }}][is_working]"
                                            value="1"
                                            {{ $isWorking ? 'checked' : '' }}
                                        >
                                        <span>{{ $isWorking ? 'Kerja' : 'Libur' }}</span>
                                    </label>
                                </td>

                                <td>
                                    <input
                                        type="time"
                                        name="schedules[{{ $dayNumber }}][start_time]"
                                        value="{{ old("schedules.$dayNumber.start_time", $startTime) }}"
                                    >
                                </td>

                                <td>
                                    <input
                                        type="time"
                                        name="schedules[{{ $dayNumber }}][end_time]"
                                        value="{{ old("schedules.$dayNumber.end_time", $endTime) }}"
                                    >
                                </td>

                                <td>
                                    <select name="schedules[{{ $dayNumber }}][slot_interval_minutes]">
                                        <option value="30" {{ (string) old("schedules.$dayNumber.slot_interval_minutes", $interval) === '30' ? 'selected' : '' }}>30 menit</option>
                                        <option value="60" {{ (string) old("schedules.$dayNumber.slot_interval_minutes", $interval) === '60' ? 'selected' : '' }}>60 menit</option>
                                        <option value="90" {{ (string) old("schedules.$dayNumber.slot_interval_minutes", $interval) === '90' ? 'selected' : '' }}>90 menit</option>
                                        <option value="120" {{ (string) old("schedules.$dayNumber.slot_interval_minutes", $interval) === '120' ? 'selected' : '' }}>120 menit</option>
                                    </select>
                                </td>

                                <td style="text-align:right;">
                                    <span class="badge {{ $isWorking ? 'badge-ok' : 'badge-warn' }}">
                                        {{ $isWorking ? 'Aktif' : 'Libur' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card" style="margin-top:14px; background:rgba(139,0,0,0.03);">
                <h2 class="h2" style="margin-bottom:8px;">Catatan</h2>
                <p class="muted" style="margin-top:0;">
                    Jadwal ini dipakai untuk membentuk slot booking yang tersedia untuk customer.
                </p>
                <p class="muted">
                    Default interval slot adalah <b>60 menit</b>, tapi bisa diubah per hari sesuai kebutuhan.
                </p>
                <p class="muted" style="margin-bottom:0;">
                    Pastikan jam mulai lebih kecil dari jam selesai, misalnya <b>13:00 - 21:00</b>.
                </p>
            </div>

            <div class="modal__actions" style="margin-top:16px;">
                <a href="/capster" class="btn btn-ghost">Batal</a>
                <button class="btn btn-primary" type="submit">Simpan Jadwal</button>
            </div>
        </form>
    </section>
@endsection