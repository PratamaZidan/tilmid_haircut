@extends('layouts.manage')

@section('title', 'Login')

@section('content')
  <div class="grid-2">
    <section class="card">
      <h1 class="h1">Login</h1>
      <p class="muted">
        Masuk untuk akses Admin / Capster.
      </p>

      <form class="form" action="/login" method="POST">
        @csrf
        <!-- username -->
        <div class="field">
          <label>Username</label>
          <input type="text" name="username" placeholder="Username" value="{{ old('username') }}" required>
          @error('username')
            <div style="margin-top:6px; color:#8b0000; font-weight:700;">{{ $message }}</div>
          @enderror
        </div>

        <!-- password -->
        <div class="field">
          <label>Password</label>

          <div class="password-wrap">
            <input type="password" id="password" name="password" placeholder="••••••••" required>
            <button class="pw-toggle" type="button" aria-label="Tampilkan password" data-target="password">
              <span class="material-symbols-outlined" aria-hidden="true">visibility</span>
            </button>
          </div>

          @error('password')
            <div style="margin-top:6px; color:#8b0000; font-weight:700;">{{ $message }}</div>
          @enderror
        </div>

        <!-- ingat saya dan lupa password -->
        <div class="row">
          <label class="check">
            <input type="checkbox" name="remember" value="1">
            <span>Ingat saya</span>
          </label>
          <a class="link" href="#">Lupa password?</a>
        </div>

        <button class="btn btn-primary" type="submit">Login</button>
      </form>
    </section>

    <aside class="card card-accent">
      <h2 class="h2">Info</h2>
      <div class="pill-list" style="display:flex; gap:10px; flex-wrap:wrap;">
        <span class="badge">Admin: kelola capster & keuangan</span>
        <span class="badge">Capster: booking & pemasukan</span>
      </div>
    </aside>
  </div>
@endsection