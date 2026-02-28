document.addEventListener('DOMContentLoaded', () => {
  const openModal = (id) => {
    const m = document.getElementById(id);
    if (!m) return null;
    m.classList.add('is-open');
    m.setAttribute('aria-hidden', 'false');
    return m;
  };

  const closeModal = (m) => {
    if (!m) return;
    m.classList.remove('is-open');
    m.setAttribute('aria-hidden', 'true');
  };

  // Close behavior untuk semua modal
  document.querySelectorAll('.modal').forEach(m => {
    m.addEventListener('click', (e) => {
      if (e.target?.dataset?.close === 'true') closeModal(m);
    });
  });

  window.addEventListener('keydown', (e) => {
    if (e.key !== 'Escape') return;
    document.querySelectorAll('.modal.is-open').forEach(closeModal);
  });

  // ===== Edit Capster =====
  document.querySelectorAll('[data-edit-capster="true"]').forEach(btn => {
    btn.addEventListener('click', () => {
      const m = openModal('editCapsterModal');
      if (!m) return;

      document.getElementById('editCapsterId').value = btn.dataset.id || '';
      document.getElementById('editCapsterName').value = btn.dataset.name || '';
      document.getElementById('editCapsterUsername').value = btn.dataset.username || '';
      document.getElementById('editCapsterPhone').value = btn.dataset.phone || '';
      document.getElementById('editCapsterStatus').value = btn.dataset.status || 'aktif';

      // kosongkan password
      document.getElementById('editCapsterPassword').value = '';
      document.getElementById('editCapsterPassword2').value = '';
    });
  });

  // ===== Edit Finance =====
  document.querySelectorAll('[data-edit-finance="true"]').forEach(btn => {
    btn.addEventListener('click', () => {
      const m = openModal('editFinanceModal');
      if (!m) return;

      const id = btn.dataset.id || '';
      document.getElementById('editFinanceId').value = id;

      const form = document.getElementById('editFinanceForm');
      if (form && id) {
        form.action = `/admin/finance/${id}`;
      }

      document.getElementById('editFinanceDate').value = btn.dataset.date || '';
      document.getElementById('editFinanceType').value = btn.dataset.type || 'masuk';
      document.getElementById('editFinanceCategory').value = btn.dataset.category || 'service';
      document.getElementById('editFinanceMethod').value = btn.dataset.method || 'cash';
      document.getElementById('editFinanceAmount').value = btn.dataset.amount || '';
      document.getElementById('editFinanceCapster').value = btn.dataset.capster || '';
      document.getElementById('editFinanceNote').value = btn.dataset.note || '';
    });
  });

  // ===== Edit Service =====
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-edit-service="true"]');
    if (!btn) return;

    const id = btn.dataset.id;
    const form = document.getElementById('editServiceForm');
    if (!form || !id) return;

    form.action = `/admin/price/${id}`;

    document.getElementById('editServiceId').value = id;
    document.getElementById('editServiceName').value = btn.dataset.name || '';
    document.getElementById('editServiceCode').value = btn.dataset.code || '';
    document.getElementById('editServicePrice').value = btn.dataset.price || '0';
    document.getElementById('editServiceCategory').value = btn.dataset.category || 'haircut';
    document.getElementById('editServiceSort').value = btn.dataset.sort || '0';
    document.getElementById('editServiceActive').value = btn.dataset.active || '1';
    document.getElementById('editServicePublic').value = btn.dataset.public || '1';

    const modal = document.getElementById('editServiceModal');
    modal?.classList.add('is-open');
    modal?.setAttribute('aria-hidden','false');
  });
});