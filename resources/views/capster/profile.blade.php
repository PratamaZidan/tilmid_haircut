@extends('layouts.manage')

@section('title', 'Profile Capster')

@section('content')
  <div class="page-head">
    <div>
      <h1 class="h1">Profile</h1>
      <p class="muted">Ubah data akun kamu. Password opsional.</p>
    </div>
  </div>

  <div class="dash-grid dash-grid--stack">
    <section class="card">
      <h2 class="h2">Data Diri</h2>

      @if(session('ok_profile'))
        <div class="badge badge-ok" style="margin-bottom:12px;">{{ session('ok_profile') }}</div>
      @endif

      <form class="form" action="/capster/profile" method="POST">
        @csrf

        <div class="grid-2">
          <div class="field">
            <label>Nama Lengkap</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
            @error('name') <div style="color:#8b0000;font-weight:700">{{ $message }}</div> @enderror
          </div>
          <div class="field">
            <label>Username</label>
            <input type="text" name="username" value="{{ old('username', $user->username) }}" required>
            @error('username') <div style="color:#8b0000;font-weight:700">{{ $message }}</div> @enderror
          </div>
        </div>

        <div class="grid-2">
          <div class="field">
            <label>No. WhatsApp</label>
            <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" required>
            @error('phone') <div style="color:#8b0000;font-weight:700">{{ $message }}</div> @enderror
          </div>
          <div class="field">
            <label>Instagram</label>
            <input type="text" name="instagram"
                  value="{{ old('instagram', $user->instagram) }}"
                  placeholder="contoh: tilmidhaircut.id / @tilmidhaircut.id">
            @error('instagram') <div style="color:#8b0000;font-weight:700">{{ $message }}</div> @enderror
          </div>
        </div>

        <div class="modal__actions" style="justify-content:flex-end;">
          <button class="btn btn-primary" type="submit">Simpan Profile</button>
        </div>
      </form>
    </section>

    <section class="card">
      <h2 class="h2">Ganti Password</h2>
      <p class="muted">Kosongkan jika tidak ingin mengganti.</p>

      @if(session('ok_password'))
        <div class="badge badge-ok" style="margin-bottom:12px;">{{ session('ok_password') }}</div>
      @endif

      <form class="form" action="/capster/profile/password" method="POST">
        @csrf

        <div class="grid-2">
          <div class="field">
            <label>Password Baru</label>
            <input type="password" name="password" placeholder="minimal 6 karakter">
            @error('password') <div style="color:#8b0000;font-weight:700">{{ $message }}</div> @enderror
          </div>
          <div class="field">
            <label>Konfirmasi</label>
            <input type="password" name="password_confirmation" placeholder="ulangi password baru">
          </div>
        </div>

        <div class="modal__actions" style="justify-content:flex-end;">
          <button class="btn btn-primary" type="submit">Update Password</button>
        </div>
      </form>
    </section>
  </div>
@endsection