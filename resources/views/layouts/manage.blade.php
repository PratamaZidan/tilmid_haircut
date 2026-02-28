<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="{{ asset('css/manage.css') }}">
  <script src="{{ asset('js/income-modal.js') }}" defer></script>
  <script src="{{ asset('js/addon-booking.js') }}" defer></script>
  <script src="{{ asset('js/admin-modals.js') }}" defer></script>
  <script src="{{ asset('js/admin-edit.js') }}" defer></script>
  <script src="{{ asset('js/password-toogle.js') }}" defer></script>
  <script src="{{ asset('js/search-clear.js') }}" defer></script>
  <script src="/js/capster-notify.js" defer></script>
  <link rel="stylesheet"
  href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,400,0,0" />
  <title>@yield('title', 'Panel') | Tilmid Haircut</title>
</head>
<body>
  <div class="barber-pole"></div>

  <header class="topbar">
    <div class="wrap topbar-inner">
      <a class="brand" href="/">
        <img class="brand-logo" src="/images/logotilmid.webp" alt="Tilmid Haircut Logo">
        <div class="brand-text">
            <div class="brand-title">TILMID HAIRCUT</div>
            <div class="brand-sub">Classic Vintage Barbershop</div>
        </div>
      </a>

      <nav class="top-actions">
        @php $path = request()->path(); @endphp

        @if (str_starts_with($path, 'capster'))
          <a class="btn btn-dark" href="/capster">Dashboard</a>
          <a class="btn btn-dark" href="/capster/profile">Profile</a>
        @endif

        @if (str_starts_with($path, 'admin'))
          <a class="btn btn-dark" href="/admin">Dashboard</a>
          <a class="btn btn-dark" href="/admin/price">Price</a>
        @endif

        {{-- di halaman login: tampilkan tombol kembali --}}
        @if ($path === 'login')
          <a class="btn btn-dark" href="/">Kembali</a>
        @else
          <form action="/logout" method="POST" style="display:inline;">
            @csrf
            <button class="btn btn-dark" type="submit">Logout</button>
          </form>
        @endif
      </nav>
    </div>
  </header>

  <main class="wrap page">
    @yield('content')
  </main>

  <footer class="footer">
    &copy; 2026 Tilmid Haircut — Est. 2024.
  </footer>

  <!-- Modal Confirm (Global) -->
  <div class="modal" id="confirmModal" aria-hidden="true">
    <div class="modal__backdrop" data-close="true"></div>

    <div class="modal__panel modal__panel--confirm" role="dialog" aria-modal="true" aria-label="Konfirmasi">
      <div class="modal__head">
        <div>
          <div class="modal__title">Konfirmasi</div>
          <div class="modal__sub" id="confirmMessage">Yakin?</div>
        </div>
        <button class="modal__close" type="button" data-close="true">✕</button>
      </div>

      <div class="modal__actions" style="justify-content:flex-end;">
        <button class="btn btn-ghost" type="button" id="confirmCancel">Batal</button>
        <button class="btn btn-primary" type="button" id="confirmOk">Ya, lanjut</button>
      </div>
    </div>
  </div>
</body>
</html>