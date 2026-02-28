document.addEventListener('DOMContentLoaded', () => {
  const openCapster = document.getElementById('openCapsterModal');
  const openFinance = document.getElementById('openFinanceModal');

  const capsterModal = document.getElementById('capsterModal');
  const financeModal = document.getElementById('financeModal');

  const bindModal = (modal, openBtn) => {
    if (!modal || !openBtn) return;

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
      const t = e.target;
      if (t && t.dataset && t.dataset.close === 'true') close();
    });

    window.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') close();
    });
  };

  bindModal(capsterModal, openCapster);
  bindModal(financeModal, openFinance);
  bindModal(document.getElementById('serviceModal'), document.getElementById('openServiceModal'));
  bindModal(document.getElementById('editServiceModal')); // edit dibuka via tombol edit
});

// Modal Konfirmasi
document.addEventListener('DOMContentLoaded', () => {
  const modal = document.getElementById('confirmModal');
  const msgEl = document.getElementById('confirmMessage');
  const btnOk = document.getElementById('confirmOk');
  const btnCancel = document.getElementById('confirmCancel');

  if (!modal || !msgEl || !btnOk || !btnCancel) return;

  let pendingForm = null;

  const open = (message, form) => {
    pendingForm = form;
    msgEl.textContent = message || 'Yakin?';
    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden', 'false');
  };

  const close = () => {
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
    pendingForm = null;
  };

  // Intercept semua form yang punya data-confirm
  document.addEventListener('submit', (e) => {
    const form = e.target;
    if (!(form instanceof HTMLFormElement)) return;

    const message = form.getAttribute('data-confirm');
    if (!message) return;

    e.preventDefault();
    open(message, form);
  });

  btnOk.addEventListener('click', () => {
    if (!pendingForm) return close();
    // submit beneran
    pendingForm.submit();
  });

  btnCancel.addEventListener('click', close);

  modal.addEventListener('click', (e) => {
    const t = e.target;
    if (t && t.dataset && t.dataset.close === 'true') close();
  });

  window.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') close();
  });
});
