document.addEventListener('DOMContentLoaded', () => {
  const open = document.getElementById('openPricePoster');
  const modal = document.getElementById('priceModal');
  const closeA = document.getElementById('closePricePoster');
  const closeB = document.getElementById('closePricePosterBtn');

  if (!open || !modal) return;

  const close = () => {
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
  };

  open.addEventListener('click', () => {
    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
  });

  closeA?.addEventListener('click', close);
  closeB?.addEventListener('click', close);

  window.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') close();
  });
});