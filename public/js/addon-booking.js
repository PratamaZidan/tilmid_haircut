document.addEventListener('DOMContentLoaded', () => {
  const bookingSel = document.getElementById('addonBooking');
  const typeSel = document.getElementById('addonType');
  const amountInp = document.getElementById('addonAmount');
  const info = document.getElementById('addonBookingInfo');

  if (!bookingSel || !typeSel || !amountInp || !info) return;

  const updateBookingInfo = () => {
    const opt = bookingSel.selectedOptions?.[0];
    if (!opt || !opt.value) {
      info.textContent = 'Booking: belum dipilih';
      return;
    }
    const time = opt.dataset.time || '';
    const name = opt.dataset.name || '';
    const service = opt.dataset.service || '';
    info.textContent = `Booking: ${time} • ${name} • ${service} (${opt.value})`;
  };

  bookingSel.addEventListener('change', updateBookingInfo);

  typeSel.addEventListener('change', () => {
    const opt = typeSel.selectedOptions?.[0];
    const price = opt?.dataset?.price;
    if (price) amountInp.value = price; // auto isi kalau ada harga
    // kalau tip/lainnya -> biarin user isi manual
  });

  updateBookingInfo();
});