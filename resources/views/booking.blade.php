<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/booking.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="/js/booking-form.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <title>Tilmid Haircut | Booking</title>
</head>
<body>

    <div class="barber-pole"></div>
    <a href="/" class="back-floating">←</a>
    <header>
        <h1>TILMID HAIRCUT</h1>
        <p><i>"Gentleman's Pride & Classic Style"</i></p>
    </header>

    <div class="container">
        <h2>Booking Kursi Anda</h2>
        <form class="booking-form" action="/booking" method="POST">
        @csrf
            <!-- Nama -->
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama" placeholder="Contoh: Budi Santoso" required>
            </div>

            <!-- nomer wa -->
            <div class="form-group">
                <label>Nomor WhatsApp</label>
                <input type="tel" name="whatsapp" placeholder="0812xxxx" required>
            </div>

            <!-- pilih layanan -->
            <div class="form-group">
                <label>Pilih Layanan</label>
                <select name="layanan" required>
                    <option value="" selected disabled hidden>Pilih Layanan</option>

                    <optgroup label="Haircut +">
                        @foreach ($services->where('category','haircut') as $s)
                        <option value="{{ $s->code }}" {{ old('layanan')==$s->code?'selected':'' }}>
                            {{ $s->name }} - Rp {{ number_format($s->price,0,',','.') }}
                            {{ !empty($s->description) ? "({$s->description})" : "" }}
                        </option>
                        @endforeach
                    </optgroup>

                    <optgroup label="Treatment +">
                        @foreach ($services->where('category','treatment') as $s)
                        <option value="{{ $s->code }}" {{ old('layanan')==$s->code?'selected':'' }}>
                            {{ $s->name }} - Rp {{ number_format($s->price,0,',','.') }}
                            {{ !empty($s->description) ? "({$s->description})" : "" }}
                        </option>
                        @endforeach
                    </optgroup>
                </select>
            </div>

            <!-- pilih capster -->
            <div class="form-group">
                <label>Pilih Capster</label>
                <select name="capster" id="capsterSelect" required>
                    <option value="" selected disabled hidden>Pilih Capster</option>
                    @foreach ($capsters as $c)
                        <option value="{{ $c->id }}" {{ old('capster') == $c->id ? 'selected' : '' }}>
                            {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- tanggal booking -->
            <div class="form-group">
                <label>Tanggal Booking</label>
                <input
                    type="date"
                    name="tanggal"
                    id="tanggalBooking"
                    placeholder="Pilih tanggal booking"
                    autocomplete="off"
                    required
                >
            </div>

            <!-- jam booking -->
            <div class="form-group">
                <label>Jam Booking</label>
                <input type="hidden" name="jam" id="jamBooking" required>
                <div id="slotContainer" class="slot-grid"></div>
                <small id="slotMessage">Pilih capster dan tanggal terlebih dahulu.</small>
            </div>

            <button type="submit">Konfirmasi Jadwal Sekarang</button>
        </form>
    </div>

    <footer>
        &copy; 2026 Tilmid Haircut - Est. 2024. All Rights Reserved.
    </footer>

    <script>
        window.bookingAvailabilityUrl = "{{ secure_url('/booking/availability') }}";
        window.bookingDisabledDatesUrl = "{{ secure_url('/booking/disabled-dates') }}";
    </script>
</body>
</html>