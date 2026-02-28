<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="/css/booking.css">
  <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
  <script src="/js/receipt-download.js" defer></script>
  <title>Tilmid Haircut | Booking Berhasil</title>
</head>
<body>
  <div class="barber-pole"></div>
  <a href="/" class="back-floating">←</a>

  <header>
    <h1>TILMID HAIRCUT</h1>
    <p><i>"Gentleman's Pride & Classic Style"</i></p>
  </header>

  <div class="container">
    <div id="receipt" class="receipt" data-booking-code="{{ $booking->code }}">
      <h2>Booking Berhasil 
        <svg xmlns="http://www.w3.org/2000/svg" height="28px" viewBox="0 -960 960 960" width="28px" fill="#30cc00">
          <path d="m424-312 282-282-56-56-226 226-114-114-56 56 170 170ZM200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H200Z"/>
        </svg>
      </h2>
      <p class="success-sub">
        Terima kasih! Berikut ringkasan booking kamu. Silakan konfirmasi via WhatsApp agar admin cepat respon, dan wajib download bukti booking ya!
      </p>

      <div class="summary">
        <div class="sum-row"><b>Kode Booking</b><span>{{ $booking->code }}</span></div>
        <div class="sum-row"><b>Nama</b><span>{{ $booking->customer_name }}</span></div>
        <div class="sum-row"><b>WhatsApp</b><span>{{ $booking->customer_whatsapp }}</span></div>
        <div class="sum-row"><b>Layanan</b><span>{{ $booking->service_label }}</span></div>
        <div class="sum-row"><b>Tanggal</b><span>{{ $booking->booking_date }}</span></div>
        <div class="sum-row"><b>Jam</b><span>{{ $booking->booking_time }}</span></div>
        <div class="sum-row"><b>Capster</b><span>{{ $booking->capster->name }}</span></div>
      </div>

      @php
        $capsterWaRaw = $booking->capster->phone ?? '';
        $capsterWa = preg_replace('/\D/', '', $capsterWaRaw);

        // kalau mulai 0 -> ganti 62
        if (str_starts_with($capsterWa, '0')) {
          $capsterWa = '62' . substr($capsterWa, 1);
        }

        // fallback kalau kosong
        $targetWa = $capsterWa ?: '6285185111157';

        $msg = "Halo Tilmid Haircut, saya mau konfirmasi booking.\n"
              . "Kode: {$booking->code}\n"
              . "Nama: {$booking->customer_name}\n"
              . "WA: {$booking->customer_whatsapp}\n"
              . "Layanan: {$booking->service_label}\n"
              . "Tanggal: {$booking->booking_date}\n"
              . "Jam: {$booking->booking_time}\n"
              . "Capster: {$booking->capster->name}\n";
      @endphp

      <div class="success-actions">
        <a class="btn-primary"
          href="https://wa.me/{{ $targetWa }}?text={{ urlencode($msg) }}"
          target="_blank" rel="noopener">
          Konfirmasi via WhatsApp
        </a>

        <button class="btn-primary" type="button" id="downloadReceipt">
          Download Bukti Booking
        </button>
      </div>

      <div class="note-box">
        <b>Catatan:</b> Datang 5–10 menit sebelum jam booking biar enak atur antrean.
      </div>
    </div>
  </div>

  <footer>
    &copy; 2026 Tilmid Haircut - Est. 2024. All Rights Reserved.
  </footer>
</body>
</html>