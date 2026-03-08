@extends('layouts.manage')
@section('title','Jam Operasional')

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
            <h1 class="h1">Jam Operasional</h1>
            <p class="muted">
                Atur hari buka, jam buka, dan jam tutup barber. Data ini akan dipakai di landing page
                untuk status buka/tutup dan daftar jam operasional.
            </p>
        </div>
        <div class="head-actions">
            <a href="/admin" class="btn btn-ghost">Kembali</a>
        </div>
    </div>

    @if(session('success'))
        <section class="card" style="margin-bottom:14px;">
            <span class="badge badge-ok">{{ session('success') }}</span>
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
        <form class="form" method="POST" action="/admin/business-hours">
            @csrf

            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 20%;">Hari</th>
                            <th style="width: 15%;">Status</th>
                            <th style="width: 25%;">Jam Buka</th>
                            <th style="width: 25%;">Jam Tutup</th>
                            <th style="width: 15%; text-align:right;">Info</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($days as $dayNumber => $dayName)
                            @php
                                $row = $hours[$dayNumber] ?? null;
                                $isOpen = $row && $row->is_open;
                            @endphp
                            <tr>
                                <td>
                                    <strong>{{ $dayName }}</strong>
                                </td>
                                <td>
                                    <label class="switch-inline">
                                        <input
                                            type="checkbox"
                                            name="hours[{{ $dayNumber }}][is_open]"
                                            value="1"
                                            {{ $isOpen ? 'checked' : '' }}
                                        >
                                        <span>{{ $isOpen ? 'Buka' : 'Tutup' }}</span>
                                    </label>
                                </td>
                                <td>
                                    <input
                                        type="time"
                                        name="hours[{{ $dayNumber }}][open_time]"
                                        value="{{ $row?->open_time ? \Carbon\Carbon::parse($row->open_time)->format('H:i') : '' }}"
                                    >
                                </td>
                                <td>
                                    <input
                                        type="time"
                                        name="hours[{{ $dayNumber }}][close_time]"
                                        value="{{ $row?->close_time ? \Carbon\Carbon::parse($row->close_time)->format('H:i') : '' }}"
                                    >
                                </td>
                                <td style="text-align:right;">
                                    <span class="badge {{ $isOpen ? 'badge-ok' : 'badge-warn' }}">
                                        {{ $isOpen ? 'Aktif' : 'Libur' }}
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
                    Kalau hari ditandai <b>Tutup</b>, maka landing page akan menampilkan hari tersebut sebagai tutup
                    dan booking pada hari itu sebaiknya tidak tersedia.
                </p>
                <p class="muted">
                    Pastikan jam buka lebih kecil dari jam tutup, misalnya <b>13:00 - 21:00</b>.
                </p>
            </div>

            <div class="modal__actions" style="margin-top:16px;">
                <a href="/admin" class="btn btn-ghost">Batal</a>
                <button class="btn btn-primary" type="submit">Simpan Jam Operasional</button>
            </div>
        </form>
    </section>
@endsection