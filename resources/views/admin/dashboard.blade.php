@extends('layouts.manage')

@section('title', 'Admin Panel')

@section('content')
  <div class="page-head">
    <div>
      <h1 class="h1">Admin Dashboard</h1>
      <p class="muted">Kelola akun capster dan catatan keuangan.</p>
    </div>
    <div class="head-actions">
      <button class="btn btn-primary" type="button" id="openCapsterModal">+ Tambah Capster</button>
      <button class="btn btn-ghost" type="button" id="openFinanceModal">+ Catat Keuangan</button>
    </div>
  </div>

  <div class="dash-grid dash-grid--stack">
    <section class="card">
      <h2 class="h2">Akun Capster</h2>

      <form class="toolbar" method="GET" action="/admin">
        <!-- <input class="search" type="text" name="capster_q" value="{{ $capsterQ }}" placeholder="Cari capster..."> -->
        <div class="search-wrap">
          <input
            class="search"
            type="text"
            name="capster_q"
            value="{{ $capsterQ }}"
            placeholder="Cari capster..."
            id="capsterSearch"
          >

          <button
            type="button"
            class="search-clear"
            id="clearCapsterSearchBtn"
            aria-label="Hapus pencarian"
            @if (!$capsterQ) style="display:none;" @endif
          >✕</button>
        </div>
        <select class="select" name="capster_status" onchange="this.form.submit()">
          <option value="all" {{ $capsterStatus==='all'?'selected':'' }}>Semua status</option>
          <option value="aktif" {{ $capsterStatus==='aktif'?'selected':'' }}>Aktif</option>
          <option value="nonaktif" {{ $capsterStatus==='nonaktif'?'selected':'' }}>Nonaktif</option>
        </select>
        {{-- keep finance params --}}
        <input type="hidden" name="finance_q" value="{{ $financeQ }}">
        <input type="hidden" name="finance_range" value="{{ $financeRange }}">
      </form>

      <div class="table-wrap">
        <table class="table">
          <thead>
            <tr>
              <th>Nama</th>
              <th>Username</th>
              <th>Status</th>
              <th style="text-align:right;">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($capsters as $c)
              <tr>
                <td>{{ $c->name }}</td>
                <td>{{ $c->username }}</td>
                <td>
                  <span class="badge {{ $c->status==='aktif' ? 'badge-ok' : 'badge-warn' }}">
                    {{ ucfirst($c->status) }}
                  </span>
                </td>
                <td class="actions">
                  <form action="/admin/capsters/{{ $c->id }}/toggle" method="POST" style="display:inline;">
                    @csrf
                    <button class="btn btn-mini" type="submit">
                      {{ $c->status==='aktif' ? 'Nonaktif' : 'Aktifkan' }}
                    </button>
                  </form>

                  <form action="/admin/capsters/{{ $c->id }}" method="POST" style="display:inline;"
                        data-confirm="Return Confirm ('Yakin hapus capster ini?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-mini btn-danger" type="submit">Hapus</button>
                  </form>
                </td>
              </tr>
            @empty
              <tr><td colspan="4" class="muted" style="padding:14px;">Belum ada capster.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </section>

    <!-- Keuangan -->
    <section class="card">
      <h2 class="h2">Keuangan</h2>
      <p class="muted">Ringkas pemasukan & pengeluaran.</p>
      <form class="toolbar" method="GET" action="/admin">
        <div class="search-wrap">
          <input
            class="search"
            type="text"
            name="finance_q"
            value="{{ $financeQ }}"
            placeholder="Cari nama / keterangan..."
            id="financeSearch"
          >

          <button
            type="button"
            class="search-clear"
            id="clearFinanceSearchBtn"
            aria-label="Hapus pencarian"
            @if (!$financeQ) style="display:none;" @endif
          >✕</button>
        </div>

        <select class="select" name="finance_range" onchange="this.form.submit()">
          <option value="1" {{ $financeRange==='1'?'selected':'' }}>Hari ini</option>
          <option value="7" {{ $financeRange==='7'?'selected':'' }}>7 hari terakhir</option>
          <option value="30" {{ $financeRange==='30'?'selected':'' }}>30 hari</option>
          <option value="all" {{ $financeRange==='all'?'selected':'' }}>Semua</option>
        </select>

        <select class="select" name="per_page" onchange="this.form.submit()">
          @foreach([5,10,20,30,50] as $n)
            <option value="{{ $n }}" {{ (int)request('per_page',10)===$n ? 'selected':'' }}>
              {{ $n }} data/halaman
            </option>
          @endforeach
        </select>

        <a class="btn btn-mini btn-excel" href="/admin/finance/export?finance_range={{ $financeRange }}&finance_q={{ urlencode($financeQ) }}">
          <span class="material-symbols-outlined" aria-hidden="true">download</span>
          <span>Excel</span>
        </a>

        {{-- keep capster params --}}
        <input type="hidden" name="capster_q" value="{{ $capsterQ }}">
        <input type="hidden" name="capster_status" value="{{ $capsterStatus }}">
      </form>

      <div class="stat-grid">
        <div class="stat">
          <div class="stat-label">Pemasukan bulan ini</div>
          <div class="stat-value">Rp {{ number_format($incomeMonth,0,',','.') }}</div>
        </div>
        <div class="stat">
          <div class="stat-label">Pengeluaran bulan ini</div>
          <div class="stat-value">Rp {{ number_format($expenseMonth,0,',','.') }}</div>
        </div>
        <div class="stat">
          <div class="stat-label">Saldo</div>
          <div class="stat-value">Rp {{ number_format($saldoMonth,0,',','.') }}</div>
        </div>
      </div>

      <div class="table-wrap" style="margin-top:12px;">
        <table class="table">
          <thead>
            <tr>
              <th>Tanggal</th>
              <th>Sumber</th>
              <th>Capster</th>
              <th>Keterangan</th>
              <th>Jenis</th>
              <th style="text-align:right;">Nominal</th>
              <th style="text-align:right;">Aksi</th>
            </tr>
          </thead>

          <tbody>
            @forelse($ledgerPage as $row)
              <tr>
                <td>{{ $row->date }}</td>

                <td>
                  <span class="badge {{ $row->source==='booking' ? 'badge-ok' : '' }}">
                    {{ $row->source==='booking' ? 'Booking' : 'Manual' }}
                  </span>
                </td>

                <td>{{ $row->capster_name ?? '-' }}</td>
                <td>{{ $row->note }}</td>

                <td>
                  <span class="badge {{ $row->type==='masuk' ? 'badge-ok' : 'badge-warn' }}">
                    {{ ucfirst($row->type) }}
                  </span>
                </td>

                <td style="text-align:right;">Rp {{ number_format($row->amount,0,',','.') }}</td>

                <td class="actions">
                  @if($row->can_edit)
                    <button class="btn btn-mini"
                      type="button"
                      data-edit-finance="true"
                      data-id="{{ $row->id }}"
                      data-date="{{ $row->date }}"
                      data-type="{{ $row->type }}"
                      data-category="{{ $row->category }}"
                      data-method="{{ $row->method }}"
                      data-capster="{{ $row->capster_id ?? '' }}"
                      data-amount="{{ $row->amount }}"
                      data-note="{{ e($row->note) }}">
                      Edit
                    </button>

                    <form action="/admin/finance/{{ $row->id }}" method="POST" style="display:inline"
                          data-confirm="Return Confirm ('Hapus catatan ini?')">
                      @csrf
                      @method('DELETE')
                      <button class="btn btn-mini btn-danger" type="submit">Hapus</button>
                    </form>
                  @else
                    <span class="muted">—</span>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="muted" style="padding:14px;">Belum ada data keuangan.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- pagination taruh DI SINI (di bawah table) --}}
      @if($ledgerPage->hasPages())
        <div class="pager">
          {{ $ledgerPage->withQueryString()->links('pagination.compact') }}

          <div class="pager-info">
            Showing {{ $ledgerPage->firstItem() }} to {{ $ledgerPage->lastItem() }} of {{ $ledgerPage->total() }} results
          </div>
        </div>
      @endif
    </section>
  </div>

  <!-- Modal: Tambah Capster -->
<div class="modal" id="capsterModal" aria-hidden="true">
  <div class="modal__backdrop" data-close="true"></div>

  <div class="modal__panel" role="dialog" aria-modal="true" aria-label="Tambah capster">
    <div class="modal__head">
      <div>
        <div class="modal__title">Tambah Capster</div>
        <div class="modal__sub">Buat akun capster untuk login dan mengelola booking.</div>
      </div>
      <button class="modal__close" type="button" data-close="true">✕</button>
    </div>

    <form class="form" action="/admin/capsters" method="POST">
      @csrf
      <div class="grid-2">
        <div class="field">
          <label>Nama Lengkap</label>
          <input type="text" name="name" placeholder="Contoh: Raka Gymnastiar" required>
        </div>
        <div class="field">
          <label>Username</label>
          <input type="text" name="username" placeholder="contoh: raka" required>
        </div>
      </div>

      <div class="grid-2">
        <div class="field">
          <label>No. WhatsApp</label>
          <input type="tel" name="phone" placeholder="contoh: 0851xxxx" required>
        </div>
        <div class="field">
          <label>Status</label>
          <select name="status" required>
            <option value="aktif" selected>Aktif</option>
            <option value="nonaktif">Nonaktif</option>
          </select>
        </div>
      </div>

      <div class="grid-2">
        <div class="field">
          <label>Password</label>
          <input type="password" name="password" placeholder="Minimal 6 karakter" required>
        </div>
        <div class="field">
          <label>Konfirmasi Password</label>
          <input type="password" name="password_confirmation" placeholder="Ulangi password" required>
        </div>
      </div>

      <div class="modal__actions">
        <button class="btn btn-ghost" type="button" data-close="true">Batal</button>
        <button class="btn btn-primary" type="submit">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal: Catat Keuangan -->
<div class="modal" id="financeModal" aria-hidden="true">
  <div class="modal__backdrop" data-close="true"></div>

  <div class="modal__panel" role="dialog" aria-modal="true" aria-label="Catat keuangan">
    <div class="modal__head">
      <div>
        <div class="modal__title">Catat Keuangan</div>
        <div class="modal__sub">Catat pemasukan/pengeluaran di luar booking, atau koreksi kas.</div>
      </div>
      <button class="modal__close" type="button" data-close="true">✕</button>
    </div>

    <form class="form" action="/admin/finance" method="POST">
      @csrf
      <div class="grid-2">
        <div class="field">
          <label>Tanggal</label>
          <input type="date" name="date" required>
        </div>
        <div class="field">
          <label>Jenis</label>
          <select name="type" id="financeType" required>
            <option value="masuk" selected>Pemasukan (Masuk)</option>
            <option value="keluar">Pengeluaran (Keluar)</option>
          </select>
        </div>
      </div>

      <div class="grid-2">
        <div class="field">
          <label>Kategori</label>
          <select name="category" required>
            <option value="" selected disabled>Pilih kategori...</option>
            <option value="service">Service / Jasa</option>
            <option value="produk">Produk / Barang</option>
            <option value="operasional">Operasional</option>
            <option value="alat">Alat / Perlengkapan</option>
            <option value="lainnya">Lainnya</option>
          </select>
        </div>
        <div class="field">
          <label>Nominal</label>
          <input type="number" name="amount" placeholder="contoh: 50000" required>
        </div>
      </div>

      <div class="grid-2">
        <div class="field">
          <label>Metode</label>
          <select name="method" required>
            <option value="cash" selected>Cash</option>
            <option value="transfer">Transfer</option>
            <option value="qris">QRIS</option>
          </select>
        </div>
        <div class="field">
          <label>Capster (opsional)</label>
          <select name="capster_id">
            <option value="" selected>-</option>
            @foreach($capsters as $c)
              <option value="{{ $c->id }}">{{ $c->name }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="field">
        <label>Keterangan</label>
        <input type="text" name="note" placeholder="contoh: beli tonic / service walk-in / dll" required>
      </div>

      <div class="modal__actions">
        <button class="btn btn-ghost" type="button" data-close="true">Batal</button>
        <button class="btn btn-primary" type="submit">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit Keuangan -->
<div class="modal" id="editFinanceModal" aria-hidden="true">
  <div class="modal__backdrop" data-close="true"></div>

  <div class="modal__panel" role="dialog" aria-modal="true" aria-label="Edit keuangan">
    <div class="modal__head">
      <div>
        <div class="modal__title">Edit Keuangan</div>
        <div class="modal__sub">Perbarui catatan pemasukan/pengeluaran.</div>
      </div>
      <button class="modal__close" type="button" data-close="true">✕</button>
    </div>

    <form class="form" id="editFinanceForm" action="" method="POST">
      @csrf
      @method('PUT')
      <input type="hidden" id="editFinanceId" name="id">

      <div class="grid-2">
        <div class="field">
          <label>Tanggal</label>
          <input type="date" id="editFinanceDate" name="date" required>
        </div>
        <div class="field">
          <label>Jenis</label>
          <select id="editFinanceType" name="type" required>
            <option value="masuk">Masuk</option>
            <option value="keluar">Keluar</option>
          </select>
        </div>
      </div>

      <div class="grid-2">
        <div class="field">
          <label>Kategori</label>
          <select id="editFinanceCategory" name="category" required>
            <option value="service">Service / Jasa</option>
            <option value="produk">Produk / Barang</option>
            <option value="operasional">Operasional</option>
            <option value="alat">Alat / Perlengkapan</option>
            <option value="lainnya">Lainnya</option>
          </select>
        </div>
        <div class="field">
          <label>Metode</label>
          <select id="editFinanceMethod" name="method" required>
            <option value="cash">Cash</option>
            <option value="transfer">Transfer</option>
            <option value="qris">QRIS</option>
          </select>
        </div>
      </div>

      <div class="grid-2">
        <div class="field">
          <label>Nominal</label>
          <input type="number" id="editFinanceAmount" name="amount" required>
        </div>
        <div class="field">
          <label>Capster (opsional)</label>
          <select id="editFinanceCapster" name="capster_id">
            <option value="">-</option>
            @foreach($capsters as $c)
              <option value="{{ $c->id }}">{{ $c->name }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="field">
        <label>Keterangan</label>
        <input type="text" id="editFinanceNote" name="note" required>
      </div>

      <div class="modal__actions">
        <button class="btn btn-ghost" type="button" data-close="true">Batal</button>
        <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>
@endsection