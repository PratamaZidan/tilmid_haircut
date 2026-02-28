document.addEventListener('DOMContentLoaded', () => {
  const form = document.querySelector('.booking-form');
  if (!form) return;

  form.addEventListener('submit', () => {
    // jangan preventDefault -> biar POST ke /booking jalan
    const btn = form.querySelector('button[type="submit"]');
    if (!btn) return;

    btn.disabled = true;
    btn.dataset.oldText = btn.textContent;
    btn.textContent = 'Memproses...';
  });
});