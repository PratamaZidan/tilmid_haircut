@extends('layouts.manage')

@section('title', 'Capster Panel')

@section('content')
  <div class="page-head">
    <div>
      <h1 class="h1">Capster Dashboard</h1>
      <p class="muted">Booking hari ini, history, dan catat pemasukan manual.</p>
    </div>
    <div class="head-actions">
      <button class="btn btn-primary" type="button" id="openIncomeModal">+ Tambah Pemasukan</button>
    </div>
  </div>

  <div class="dash-grid">
    <section class="card">
      <h2 class="h2">Booking ({{ $bookDate }})</h2>

      <form class="toolbar booking-filter" method="GET" action="/capster">
        <input class="search" type="date" name="date" value="{{ $bookDate }}" onchange="this.form.submit()">

        {{-- keep query lain biar ga reset --}}
        <input type="hidden" name="range" value="{{ $range }}">
        <input type="hidden" name="q" value="{{ request('q') }}">
      </form>

      <div class="table-wrap">
        <table class="table">
          <thead>
            <tr>
              <th>Jam</th>
              <th>Nama</th>
              <th>Layanan</th>
              <th>WA</th>
              <th>Status</th>
              <th style="text-align:right;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($todayBookings as $b)
              <tr>
                <td>{{ substr($b->booking_time,0,5) }}</td>
                <td>{{ $b->customer_name }}</td>
                <td>{{ $b->service_label }}</td>
                <td>
                  <a class="link" target="_blank" rel="noopener"
                    href="https://wa.me/{{ preg_replace('/\D/','', $b->customer_whatsapp) }}">
                    {{ $b->customer_whatsapp }}
                  </a>
                </td>
                <td>
                  @php
                    $badgeClass = $b->status === 'done' ? 'badge-ok' : ($b->status === 'cancelled' ? 'badge-warn' : '');
                  @endphp
                  <span class="badge {{ $badgeClass }}">{{ ucfirst($b->status) }}</span>
                </td>
                <td class="actions">
                  <form action="/capster/booking/{{ $b->id }}/done" method="POST" style="display:inline;">
                    @csrf
                    <button class="btn btn-mini" type="submit" {{ in_array($b->status,['done','cancelled']) ? 'disabled' : '' }}>
                      Done
                    </button>
                  </form>

                  <form action="/capster/booking/{{ $b->id }}/cancel" method="POST" style="display:inline;">
                    @csrf
                    <button class="btn btn-mini btn-danger" type="submit" {{ in_array($b->status,['done','cancelled']) ? 'disabled' : '' }}>
                      Batal
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="muted" style="padding:14px;">Belum ada booking hari ini.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </section>

    <!-- Tambah Add On Booking -->
    <section class="card">
        <h2 class="h2">Tambah Add-on Booking</h2>
        <p class="muted">Untuk tambahan di luar booking (masker/semir/tip). Pilih booking dulu.</p>

        <form class="form" action="/capster/addon" method="POST">
          @csrf
            <div class="grid-2">
            <div class="field">
                <label>Pilih Booking</label>
                <select name="booking_id" id="addonBooking" required>
                  <option value="" selected disabled>Pilih booking...</option>
                  @foreach ($todayBookings as $b)
                    <option value="{{ $b->id }}">
                      {{ substr($b->booking_time,0,5) }} • {{ $b->customer_name }} • {{ $b->service_label }} ({{ $b->code }})
                    </option>
                  @endforeach
                </select>
            </div>

            <div class="field">
                <label>Jenis Add-on</label>
                  <select name="addon_code" id="addonType" required>
                    <option value="" selected disabled>Pilih add-on...</option>
                    @foreach ($addonServices as $s)
                      <option value="{{ $s->code }}" data-price="{{ $s->price }}">
                        {{ $s->name }} 
                        @if($s->price > 0)
                          (+{{ number_format($s->price,0,',','.') }})
                        @else
                          (custom)
                        @endif
                      </option>
                    @endforeach
                  </select>
            </div>
            </div>

            <div class="grid-2">
              <div class="field">
                  <label>Nominal Add-on</label>
                  <input id="addonAmount" name="amount" type="number" placeholder="contoh: 20000" required>
              </div>

              <div class="field">
                  <label>Tanggal</label>
                  <input type="date" name="date" required>
              </div>
            </div>

            <div class="field">
              <label>Catatan (opsional)</label>
              <input type="text" name="note" placeholder="contoh: tambah masker + styling / tip / dll">
            </div>

            <div class="row" style="justify-content: space-between;">
              <button class="btn btn-primary" type="submit">Simpan Add-on</button>
            </div>
        </form>
    </section>

    <!-- History -->
    <section class="card" style="grid-column: 1 / -1;">
      <h2 class="h2">History</h2>
      <div class="toolbar">
        <form class="toolbar" method="GET" action="/capster">
        <div class="search-wrap">
          <input
            class="search"
            type="text"
            name="q"
            value="{{ request('q') }}"
            placeholder="Cari nama / layanan..."
            id="historySearch"
          >

          <button
            type="button"
            class="search-clear"
            id="clearSearchBtn"
            aria-label="Hapus pencarian"
            @if (!request('q'))
              style="display:none;"
            @endif
          >✕</button>
        </div>
        <select class="select" onchange="location.href='?range='+this.value">
          <option value="1"  {{ $range==='1' ? 'selected':'' }}>Hari ini</option>
          <option value="7"  {{ $range==='7' ? 'selected':'' }}>7 hari terakhir</option>
          <option value="30" {{ $range==='30' ? 'selected':'' }}>30 hari</option>
          <option value="all" {{ $range==='all' ? 'selected':'' }}>Semua</option>
        </select>
        </form>
      </div>

     <div class="history-top">
        <div class="stat">
          <div class="stat-label">
            Total Pendapatan ({{ $range === 'all' ? 'Semua' : ($range === '1' ? 'Hari ini' : $range.' hari') }})
          </div>
          <div class="stat-value">Rp {{ number_format($historyTotal,0,',','.') }}</div>
        </div>

        <a class="btn btn-mini btn-excel" href="/capster/history/export?range={{ $range }}">
          <span class="material-symbols-outlined" aria-hidden="true">download</span>
          <span>Excel</span>
        </a>
      </div>

      <div class="table-wrap">
        <table class="table">
          <thead>
            <tr>
              <th>Tanggal</th>
              <th>Nama</th>
              <th>Layanan</th>
              <th>Status</th>
              <th style="text-align:right;">Total</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($history as $h)
              <tr>
                <td>{{ $h->booking_date }}</td>
                <td>{{ $h->customer_name }}</td>
                <td>{{ $h->service_label }}</td>
                <td><span class="badge {{ $h->status==='done' ? 'badge-ok':'' }}">{{ ucfirst($h->status) }}</span></td>
                <td style="text-align:right;">Rp {{ number_format($h->total,0,',','.') }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="muted" style="padding:14px;">Belum ada history.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </section>
  </div>

  <!-- Tambah Pemasukan (Walk-in) -->
  <div class="modal" id="incomeModal" aria-hidden="true">
  <div class="modal__backdrop" data-close="true"></div>

  <div class="modal__panel" role="dialog" aria-modal="true" aria-label="Tambah pemasukan manual">
    <div class="modal__head">
      <div>
        <div class="modal__title">Tambah Pemasukan (Walk-in)</div>
        <div class="modal__sub">Untuk pelanggan yang langsung datang tanpa booking.</div>
      </div>
      <button class="modal__close" type="button" data-close="true">✕</button>
    </div>

    <form class="form" action="/capster/walkin" method="POST">
      @csrf

      <div class="grid-2">
        <div class="field">
          <label>Tanggal</label>
          <input type="date" name="date" required>
        </div>
        <div class="field">
          <label>Jam</label>
          <input type="time" name="time" required>
        </div>
      </div>

      <div class="grid-2">
        <div class="field">
          <label>Nama Pelanggan</label>
          <input type="text" name="customer_name" placeholder="Contoh: Budi" required>
        </div>
        <div class="field">
          <label>WhatsApp (opsional)</label>
          <input type="tel" name="customer_whatsapp" placeholder="08xxxx (boleh kosong)">
        </div>
      </div>

      <div class="grid-2">
        <div class="field">
          <label>Layanan (Walk-in)</label>
          <select name="service_code" required>
            <option value="" selected disabled>Pilih Layanan</option>

            <optgroup label="Haircut +">
              @foreach($servicesPublic->where('category','haircut') as $s)
                <option value="{{ $s->code }}">
                  {{ $s->name }} - Rp {{ number_format($s->price,0,',','.') }}
                </option>
              @endforeach
            </optgroup>

            <optgroup label="Treatment +">
              @foreach($servicesPublic->where('category','treatment') as $s)
                <option value="{{ $s->code }}">
                  {{ $s->name }} - Rp {{ number_format($s->price,0,',','.') }}
                </option>
              @endforeach
            </optgroup>
          </select>
        </div>

        <div class="field">
          <label>Status</label>
          <select name="status" required>
            <option value="done" selected>Selesai</option>
            <option value="confirmed">Konfirmasi</option>
          </select>
        </div>
      </div>

      <div class="field">
        <label>Catatan (opsional)</label>
        <input type="text" name="note" placeholder="Contoh: tambah service / bayar cash / dll">
      </div>

      <div class="modal__actions">
        <button class="btn btn-ghost" type="button" data-close="true">Batal</button>
        <button class="btn btn-primary" type="submit">Simpan</button>
      </div>
    </form>
      </div>
    </div>
@endsection