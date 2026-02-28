@extends('layouts.manage')
@section('title','Manage Price')

@section('content')
    <div class="page-head">
        <div>
        <h1 class="h1">Services & Price</h1>
        <p class="muted">Tambah, ubah, atau hapus layanan. Atur Public agar tampil di landing/booking.</p>
        </div>
        <div class="head-actions">
        <button class="btn btn-primary" type="button" id="openServiceModal">+ Tambah Layanan</button>
        </div>
    </div>

    <section class="card">
        <form class="toolbar" method="GET" action="/admin/price" id="priceFilterForm">
            <div class="search-wrap">
                <input class="search" type="text" name="q" value="{{ $q }}" placeholder="Cari layanan / code..." id="serviceSearch">

                <button
                type="button"
                class="search-clear"
                id="clearServiceSearchBtn"
                aria-label="Hapus pencarian"
                @if(!$q) style="display:none;" @endif
                >✕</button>
            </div>

            <select class="select" name="category" onchange="this.form.submit()">
                <option value="all" {{ $category==='all'?'selected':'' }}>Semua kategori</option>
                <option value="haircut" {{ $category==='haircut'?'selected':'' }}>Haircut</option>
                <option value="treatment" {{ $category==='treatment'?'selected':'' }}>Treatment</option>
            </select>
        </form>

        <div class="table-wrap" style="margin-top:12px;">
        <table class="table">
            <thead>
            <tr>
                <th>Nama</th>
                <th>Code</th>
                <th>Kategori</th>
                <th style="text-align:right;">Harga</th>
                <th>Public</th>
                <th>Status</th>
                <th style="text-align:right;">Aksi</th>
            </tr>
            </thead>
            <tbody>
            @forelse($services as $s)
                <tr>
                <td>{{ $s->name }}</td>
                <td><span class="badge">{{ $s->code }}</span></td>
                <td>{{ ucfirst($s->category) }}</td>
                <td style="text-align:right;">Rp {{ number_format($s->price,0,',','.') }}</td>

                <td>
                    <span class="badge {{ $s->is_public ? 'badge-ok' : 'badge-warn' }}">
                    {{ $s->is_public ? 'Public' : 'Internal' }}
                    </span>
                </td>

                <td>
                    <span class="badge {{ $s->is_active ? 'badge-ok' : 'badge-warn' }}">
                    {{ $s->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </td>

                <td class="actions">
                    <button class="btn btn-mini"
                    type="button"
                    data-edit-service="true"
                    data-id="{{ $s->id }}"
                    data-code="{{ $s->code }}"
                    data-name="{{ e($s->name) }}"
                    data-price="{{ $s->price }}"
                    data-category="{{ $s->category }}"
                    data-sort="{{ $s->sort_order }}"
                    data-active="{{ $s->is_active ? 1 : 0 }}"
                    data-public="{{ $s->is_public ? 1 : 0 }}"
                    >Edit</button>

                    <form action="/admin/price/{{ $s->id }}/toggle" method="POST" style="display:inline">
                    @csrf
                    <button class="btn btn-mini" type="submit">
                        {{ $s->is_active ? 'Nonaktif' : 'Aktifkan' }}
                    </button>
                    </form>

                    <form action="/admin/price/{{ $s->id }}" method="POST" style="display:inline"
                        data-confirm="Return Confirm ('Hapus layanan ini?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-mini btn-danger" type="submit">Hapus</button>
                    </form>
                </td>
                </tr>
            @empty
                <tr><td colspan="7" class="muted" style="padding:14px;">Belum ada layanan.</td></tr>
            @endforelse
            </tbody>
        </table>
        </div>
    </section>

    <!-- Modal Add Service -->
    <div class="modal" id="serviceModal" aria-hidden="true">
    <div class="modal__backdrop" data-close="true"></div>

    <div class="modal__panel" role="dialog" aria-modal="true" aria-label="Tambah layanan">
        <div class="modal__head">
        <div>
            <div class="modal__title">Tambah Layanan</div>
            <div class="modal__sub">Tambah layanan baru untuk booking / capster.</div>
        </div>
        <button class="modal__close" type="button" data-close="true">✕</button>
        </div>

        <form class="form" action="/admin/price" method="POST">
        @csrf

        <div class="grid-2">
            <div class="field">
            <label>Nama Layanan</label>
            <input type="text" name="name" placeholder="Contoh: Haircut Premium" required>
            </div>

            <div class="field">
            <label>Code (unik)</label>
            <input type="text" name="code" placeholder="contoh: premium" required>
            <div class="muted" style="margin-top:6px;font-size:.92rem;">Huruf/angka/strip/underscore (alpha_dash).</div>
            </div>
        </div>

        <div class="grid-2">
            <div class="field">
            <label>Kategori</label>
            <select name="category" required>
                <option value="haircut" selected>Haircut</option>
                <option value="treatment">Treatment</option>
            </select>
            </div>

            <div class="field">
            <label>Harga (Rp)</label>
            <input type="number" name="price" min="0" placeholder="contoh: 30000" required>
            </div>
        </div>

        <div class="grid-2">
            <div class="field">
            <label>Urutan (sort order)</label>
            <input type="number" name="sort_order" min="0" placeholder="contoh: 10 (opsional)">
            </div>

            <div class="field">
            <label>Status</label>
            <select name="is_active" required>
                <option value="1" selected>Aktif</option>
                <option value="0">Nonaktif</option>
            </select>
            </div>
        </div>

        <div class="field">
            <label>Tampil di Landing/Booking?</label>
            <select name="is_public" required>
            <option value="1" selected>Public (tampil di landing & booking)</option>
            <option value="0">Internal (hanya untuk capster/admin)</option>
            </select>
        </div>

        <div class="modal__actions">
            <button class="btn btn-ghost" type="button" data-close="true">Batal</button>
            <button class="btn btn-primary" type="submit">Simpan</button>
        </div>
        </form>
    </div>
    </div>

    <!-- Modal: Edit Layanan -->
    <div class="modal" id="editServiceModal" aria-hidden="true">
    <div class="modal__backdrop" data-close="true"></div>

    <div class="modal__panel" role="dialog" aria-modal="true" aria-label="Edit layanan">
        <div class="modal__head">
        <div>
            <div class="modal__title">Edit Layanan</div>
            <div class="modal__sub">Perbarui nama/harga/status layanan.</div>
        </div>
        <button class="modal__close" type="button" data-close="true">✕</button>
        </div>

        <form class="form" id="editServiceForm" action="" method="POST">
        @csrf
        @method('PUT')

        <input type="hidden" id="editServiceId">

        <div class="grid-2">
            <div class="field">
            <label>Nama Layanan</label>
            <input type="text" id="editServiceName" name="name" required>
            </div>

            <div class="field">
            <label>Code</label>
            <input type="text" id="editServiceCode" name="code" required>
            </div>
        </div>

        <div class="grid-2">
            <div class="field">
            <label>Kategori</label>
            <select id="editServiceCategory" name="category" required>
                <option value="haircut">Haircut</option>
                <option value="treatment">Treatment</option>
            </select>
            </div>

            <div class="field">
            <label>Harga (Rp)</label>
            <input type="number" id="editServicePrice" name="price" min="0" required>
            </div>
        </div>

        <div class="grid-2">
            <div class="field">
            <label>Urutan (sort order)</label>
            <input type="number" id="editServiceSort" name="sort_order" min="0">
            </div>

            <div class="field">
            <label>Status</label>
            <select id="editServiceActive" name="is_active" required>
                <option value="1">Aktif</option>
                <option value="0">Nonaktif</option>
            </select>
            </div>
        </div>

        <div class="field">
            <label>Tampil di Landing/Booking?</label>
            <select id="editServicePublic" name="is_public" required>
            <option value="1">Public</option>
            <option value="0">Internal</option>
            </select>
        </div>

        <div class="modal__actions">
            <button class="btn btn-ghost" type="button" data-close="true">Batal</button>
            <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
        </div>
        </form>
    </div>
    </div>
@endsection