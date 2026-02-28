<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/booking.css">
    <script src="/js/booking-form.js" defer></script>
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
            <select name="layanan" required>
                <option value="" selected disabled hidden>Pilih Layanan</option>

                <optgroup label="Haircut +">
                    @foreach ($services->where('category','haircut') as $s)
                    <option value="{{ $s->code }}" {{ old('layanan')==$s->code?'selected':'' }}>
                        {{ $s->name }} - Rp {{ number_format($s->price,0,',','.') }}
                    </option>
                    @endforeach
                </optgroup>

                <optgroup label="Treatment +">
                    @foreach ($services->where('category','treatment') as $s)
                    <option value="{{ $s->code }}" {{ old('layanan')==$s->code?'selected':'' }}>
                        {{ $s->name }} - Rp {{ number_format($s->price,0,',','.') }}
                    </option>
                    @endforeach
                </optgroup>
            </select>

            <!-- tanggal, jam, capster -->
            <div class="form-group">
                <label>Tanggal Booking</label>
                <input type="date" name="tanggal" required>
            </div>

            <!-- jam booking -->
            <div class="form-group">
                <label>Jam Kedatangan</label>
                <input type="time" name="jam" required>
            </div>

            <!-- pilih capster -->
            <select name="capster" required>
                <option value="" selected disabled hidden>Pilih Capster</option>
                @foreach ($capsters as $c)
                    <option value="{{ $c->id }}" {{ old('capster') == $c->id ? 'selected' : '' }}>
                    {{ $c->name }}
                    </option>
                @endforeach
            </select>

            <button type="submit">Konfirmasi Jadwal Sekarang</button>
        </form>
    </div>

    <footer>
        &copy; 2026 Tilmid Haircut - Est. 2024. All Rights Reserved.
    </footer>
</body>
</html>