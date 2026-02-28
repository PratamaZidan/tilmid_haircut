document.addEventListener('DOMContentLoaded', () => {
  const badge = document.getElementById('newBadge');
  if (!badge) return;

  let lastCount = null;
  let first = true;
  let soundEnabled = false;

  const audio = new Audio('/sounds/notify.mp3');
  audio.volume = 1;

  // tombol enable sound (bisa kamu taruh di UI)
  const enableBtn = document.getElementById('enableSound');
  if (enableBtn) {
    enableBtn.addEventListener('click', async () => {
      try {
        soundEnabled = true;
        // trik: play-pause kecil biar browser "unlock"
        await audio.play();
        audio.pause();
        audio.currentTime = 0;
        enableBtn.style.display = 'none';
      } catch (e) {
        // kalau masih diblok, user klik lagi
        console.log('Sound blocked, click again', e);
      }
    });
  } else {
    // fallback: kalau user pernah klik apapun di page, enable otomatis
    window.addEventListener('click', () => (soundEnabled = true), { once: true });
  }

  async function tick() {
    try {
      const res = await fetch('/capster/notify', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        cache: 'no-store'
      });

      const ct = (res.headers.get('content-type') || '').toLowerCase();
      if (!res.ok || !ct.includes('application/json')) return;

      const data = await res.json();

      if (lastCount === null) lastCount = data.count;

      if (!first && data.count > lastCount) {
        badge.style.display = 'inline-flex';
        badge.classList.add('badge-ok');

        if (data.latest) {
          badge.textContent = `Booking Baru: ${data.latest.customer_name} (${String(data.latest.booking_time).slice(0,5)})`;
        }

        if (soundEnabled) {
          audio.currentTime = 0;
          audio.play().catch(() => {});
        }

        setTimeout(() => {
          badge.style.display = 'none';
          badge.textContent = 'Booking Baru';
        }, 8000);
      }

      first = false;
      lastCount = data.count;
    } catch (e) {
      // network/json error, ignore
    }
  }

  tick();
  setInterval(tick, 12000);
});