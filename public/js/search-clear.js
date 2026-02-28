document.addEventListener('DOMContentLoaded', () => {
  const configs = [
    { inputId: 'historySearch', btnId: 'clearSearchBtn', param: 'q' },                 
    { inputId: 'financeSearch', btnId: 'clearFinanceSearchBtn', param: 'finance_q' }, 
    { inputId: 'capsterSearch', btnId: 'clearCapsterSearchBtn', param: 'capster_q' },
    { inputId: 'serviceSearch', btnId: 'clearServiceSearchBtn', param: 'q' },
  ];

  configs.forEach(({ inputId, btnId, param }) => {
    const input = document.getElementById(inputId);
    const btn = document.getElementById(btnId);
    if (!input || !btn) return;

    // show/hide tombol X saat ngetik
    const sync = () => {
      btn.style.display = input.value.trim() ? 'flex' : 'none';
    };

    input.addEventListener('input', sync);

    btn.addEventListener('click', (e) => {
      e.preventDefault();
      input.value = '';

      const url = new URL(window.location.href);
      url.searchParams.delete(param);

      // optional: reset page ke 1 biar ga nyangkut pagination
      url.searchParams.delete('page');

      window.location.href = url.toString();
    });

    sync();
  });
});