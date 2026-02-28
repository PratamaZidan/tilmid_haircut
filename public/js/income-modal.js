document.addEventListener('DOMContentLoaded', () => {
  const openBtn = document.getElementById('openIncomeModal');
  const modal = document.getElementById('incomeModal');
  if (!openBtn || !modal) return;

  const open = () => {
    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
  };

  const close = () => {
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
  };

  openBtn.addEventListener('click', open);

  modal.addEventListener('click', (e) => {
    const target = e.target;
    if (target && target.dataset && target.dataset.close === 'true') close();
  });

  window.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') close();
  });
});