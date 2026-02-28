<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="/css/landing.css">
    <script src="/js/nav-indicator.js" defer></script>
    <script src="/js/gallery-auto.js" defer></script>
    <script src="/js/price-modal.js" defer></script>
    <title>Tilmid Haircut | Classic Vintage Barbershop</title>
</head>
<body>
  <div class="barber-pole"></div>

  <div class="nav">
    <div class="wrap nav-inner">
      <div class="brand">
        <a href="/" class="brand-link">
            <img class="brand-logo" src="/images/logotilmid.webp" alt="Tilmid Haircut Logo">
            <div class="brand-text">
                <b>TILMID HAIRCUT</b>
                <span>Classic Vintage Barbershop</span>
            </div>
        </a>
      </div>
      <nav class="nav-links" id="navLinks">
        <span class="nav-indicator" id="navIndicator"></span>

        <a href="#about">Tentang</a>
        <a href="#gallery">Hasil</a>
        <a href="#price">Harga</a>
        <a href="#hours">Jam Buka</a>
        <a href="#location">Lokasi</a>
        <a href="/booking">Booking</a>
        <a href="/login">Login</a>
      </nav>
    </div>
  </div>

  <div id="top-sentinel" style="height:1px;"></div>

  <header class="hero">
    <div class="wrap hero-grid">
      <div>
        <h1>TILMID<br/>HAIRCUT</h1>
        <p><i>"Gentleman's Pride & Classic Style"</i></p>

        <div class="tag">
          <span>Barbershop klasik</span>
          <span>Rapih • Bersih • Nyaman</span>
          <span>Siap upgrade style kamu</span>
        </div>

        <p>
          Tilmid Haircut hadir untuk kamu yang pengen potongan rapi dengan nuansa vintage.
          Dari gaya reguler sampai premium, semuanya dikerjakan teliti—biar pede dari kursi sampai keluar pintu.
        </p>

        <div class="hero-actions">
          <a class="btn btn-primary" href="/booking">Booking Sekarang</a>

          <a class="btn btn-dark"
             href="https://wa.me/6285185111157?text=Halo%20Tilmid%20Haircut,%20saya%20mau%20booking."
             target="_blank" rel="noopener">
            Chat WhatsApp
          </a>

          <a class="btn btn-ghost"
             href="https://www.instagram.com/tilmidhaircut.id/?__pwa=1"
             target="_blank" rel="noopener">
            Lihat Instagram
          </a>
        </div>
      </div>

      <aside class="hero-mirror">
        <div class="mirror-pill">
          <img src="/images/Hero.webp" alt="Suasana Tilmid Haircut">
        </div>
      </aside>
    </div>
  </header>

  <main>
    <!-- About -->
    <section id="about">
      <div class="wrap">
        <h2 class="section-title">Tentang Tilmid</h2>
        <p class="section-sub">
          Fokus kami: pengalaman potong rambut yang rapi, nyaman, dan konsisten.
          Kami jaga detail, mulai dari konsultasi gaya, proses, sampai finishing.
        </p>

        <div class="cards">
          <div class="card">
            <h4>Classic Vibes</h4>
            <p>Nuansa vintage yang hangat, cocok untuk “gentleman look” sehari-hari.</p>
          </div>
          <div class="card">
            <h4>Detail Oriented</h4>
            <p>Potongan rapi, garis bersih, dan finishing yang bikin kamu makin pede.</p>
          </div>
          <div class="card">
            <h4>Friendly Service</h4>
            <p>Ngobrol santai, konsultasi style, dan kamu bisa request sesuai kebutuhan.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Gallery -->
    <section id="gallery">
      <div class="wrap">
        <h2 class="section-title">Hasil Haircut</h2>
        <p class="section-sub">
          Ini contoh area untuk foto hasil.
        </p>

        <div class="gallery-full">
            <div class="gallery" id="galleryTrack">
                <div class="ph">
                    <img src="/images/1.webp" alt="Hasil haircut 1">
                </div>
                <div class="ph">
                    <img src="/images/2.webp" alt="Hasil haircut 2">
                </div>
                <div class="ph">
                    <img src="/images/3.webp" alt="Hasil haircut 3">
                </div>
                <div class="ph">
                    <img src="/images/4.webp" alt="Hasil haircut 4">
                </div>
                <div class="ph">
                    <img src="/images/5.webp" alt="Hasil haircut 5">
                </div>
                <div class="ph">
                    <img src="/images/6.webp" alt="Hasil haircut 6">
                </div>
                <div class="ph">
                    <img src="/images/7.webp" alt="Hasil haircut 7">
                </div>
                <div class="ph is-video">
                    <video autoplay muted loop playsinline preload="metadata">
                        <source src="/images/8.mp4" type="video/mp4">
                    </video>
                </div>
                <div class="ph">
                    <img src="/images/9.webp" alt="Hasil haircut 9">
                </div>
                <div class="ph">
                    <img src="/images/10.webp" alt="Hasil haircut 10">
                </div>
                <div class="ph">
                    <img src="/images/11.webp" alt="Hasil haircut 11">
                </div>
                <div class="ph is-video">
                    <video autoplay muted loop playsinline preload="metadata">
                        <source src="/images/12.mp4" type="video/mp4">
                    </video>
                </div>
                <div class="ph">
                    <img src="/images/13.webp" alt="Hasil haircut 13">
                </div>
                <div class="ph">
                    <img src="/images/14.webp" alt="Hasil haircut 14">
                </div>
                <div class="ph">
                    <img src="/images/15.webp" alt="Hasil haircut 15">
                </div>
            </div>
        </div>

        <div style="text-align:center; margin-top:18px;">
          <a class="btn btn-ghost"
             href="https://www.instagram.com/tilmidhaircut.id/?__pwa=1"
             target="_blank" rel="noopener">
            Lihat Lebih Banyak di Instagram
          </a>
        </div>
      </div>
    </section>

    <section id="price" class="price">
      <div class="wrap">
        <h2 class="section-title">Price List</h2>
        <p class="section-sub">Pilih layanan sesuai kebutuhan. Konsultasi dulu biar hasilnya pas.</p>

        <div class="price-top">
          <button class="btn btn-ghost price-poster-btn" type="button" id="openPricePoster">
            Lihat Poster Price List
          </button>
          <span class="price-note">*Harga bisa berubah sewaktu-waktu.</span>
        </div>

        <div class="price-card">
          <div class="price-group">
            <h3>Haircut</h3>

            @foreach($services->where('category','haircut') as $s)
              <div class="price-row">
                <div class="price-name">{{ $s->name }}</div>
                <div class="price-amt">Rp {{ number_format($s->price,0,',','.') }}</div>
              </div>
            @endforeach
          </div>

          <div class="price-divider"></div>

          <div class="price-group">
            <h3>Treatment +</h3>

            @foreach($services->where('category','treatment') as $s)
              <div class="price-row">
                <div class="price-name">{{ $s->name }}</div>
                <div class="price-amt">Rp {{ number_format($s->price,0,',','.') }}</div>
              </div>
            @endforeach
          </div>
        </div>
      </div>

      <!-- Modal Poster -->
      <div class="price-modal" id="priceModal" aria-hidden="true">
        <div class="price-modal__backdrop" id="closePricePoster"></div>
        <div class="price-modal__content" role="dialog" aria-modal="true" aria-label="Poster Price List">
          <button class="price-modal__close" type="button" id="closePricePosterBtn">✕</button>
          <img src="/images/Price.webp" alt="Tilmid Haircut Price List Poster">
        </div>
      </div>
    </section>

    <!-- Jam Buka -->
    <section id="hours">
      <div class="wrap">
        <h2 class="section-title">Jam Operasional</h2>
        <p class="section-sub">
          Datang di jam operasional berikut untuk pengalaman potong rambut yang rapi dan nyaman. Booking dulu biar kursi siap.
        </p>

        <div class="split">
          <div class="card">
            <h4>Jam Buka</h4>
            <div class="list">
              <div class="item"><b>Senin</b><span>15.00 – 22.00</span></div>
              <div class="item"><b>Selasa</b><span>15.00 – 22.00</span></div>
              <div class="item"><b>Rabu</b><span>15.00 – 22.00</span></div>
              <div class="item"><b>Kamis</b><span>15.00 – 22.00</span></div>
              <div class="item"><b>Jumat</b><span>15.00 – 22.00</span></div>
              <div class="item"><b>Sabtu</b><span>15.00 – 22.00</span></div>
              <div class="item"><b>Minggu</b><span>15.00 – 22.00</span></div>
            </div>
          </div>

          <div class="card">
            <h4>Siap Booking?</h4>
            <p style="color:var(--muted); line-height:1.7; margin-top:6px;">
              Klik tombol di bawah untuk langsung chat WhatsApp atau lanjut booking via form.
            </p>
            <div style="display:flex; gap:12px; flex-wrap:wrap; margin-top:14px;">
              <a class="btn btn-primary" href="/booking">Booking</a>
              <a class="btn btn-dark"
                 href="https://wa.me/6285185111157?text=Halo%20Tilmid%20Haircut,%20saya%20mau%20booking."
                 target="_blank" rel="noopener">
                WhatsApp
              </a>
            </div>

            <div style="margin-top:18px; border-top:1px solid var(--line); padding-top:14px; color:var(--muted); font-size:.95rem;">
              <b style="color:var(--ink);">Catatan:</b> Datang 5–10 menit sebelum jam booking biar enak atur antrean.
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Alamat Tilmid -->
    <section id="location">
      <div class="wrap">
        <h2 class="section-title">Lokasi</h2>
        <p class="section-sub">
          Temukan Tilmid Haircut dengan mudah lewat Google Maps di bawah.
        </p>

        <div class="split">
          <div class="card">
            <h4>Alamat</h4>
            <p style="color:var(--muted); line-height:1.8; margin-top:8px;">
              <b style="color:var(--ink);">Tilmid Haircut</b><br/>
              Jl. Bromo selatan SMPN 1, Kopen, Genteng Kulon<br/>
              Kec. Genteng, Kabupaten Banyuwangi, Jawa Timur 68465
            </p>

            <div style="display:flex; gap:12px; flex-wrap:wrap; margin-top:14px;">
              <a class="btn btn-ghost" href="https://www.instagram.com/tilmidhaircut.id/?__pwa=1" target="_blank" rel="noopener">Instagram</a>
              <a class="btn btn-dark" href="https://wa.me/6285185111157" target="_blank" rel="noopener">WhatsApp</a>
            </div>
          </div>

          <!-- GANTI src iframe dengan embed maps lokasi Tilmid -->
          <iframe
            title="Google Maps Tilmid Haircut"
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"
            src="https://www.google.com/maps?q=-8.3615093,114.1474362&z=17&output=embed">
          </iframe>
        </div>
      </div>
    </section>
  </main>

  <footer>
    &copy; 2026 Tilmid Haircut — Est. 2024. All Rights Reserved.
  </footer>
</body>
</html>