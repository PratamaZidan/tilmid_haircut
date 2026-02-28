// sementara: ambil dari query string (nanti backend bisa ganti dari session/db)
    const params = new URLSearchParams(window.location.search);
    const nama = params.get('nama') || '-';
    const wa = params.get('wa') || '-';
    const layanan = params.get('layanan') || '-';
    const tanggal = params.get('tanggal') || '-';
    const jam = params.get('jam') || '-';
    const capster = params.get('capster') || '-';

    document.getElementById('sNama').textContent = nama;
    document.getElementById('sWa').textContent = wa;
    document.getElementById('sLayanan').textContent = layanan;
    document.getElementById('sTanggal').textContent = tanggal;
    document.getElementById('sJam').textContent = jam;
    document.getElementById('sCapster').textContent = capster;

    // GANTI nomor WA admin tilmid
    const adminWa = '6285185111157';
    const text = `Halo Tilmid Haircut, saya mau konfirmasi booking:%0A` +
      `Nama: ${nama}%0A` +
      `WA: ${wa}%0A` +
      `Layanan: ${layanan}%0A` +
      `Tanggal: ${tanggal}%0A` +
      `Jam: ${jam}%0A` +
      `Capster: ${capster}%0A`;

    document.getElementById('btnWa').href = `https://wa.me/${adminWa}?text=${text}`;