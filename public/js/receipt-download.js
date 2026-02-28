document.addEventListener('DOMContentLoaded', () => {
  const btn = document.getElementById('downloadReceipt');
  const receipt = document.getElementById('receipt');
  if (!btn || !receipt) return;

  btn.addEventListener('click', async () => {
    if (typeof window.html2canvas !== 'function') {
      alert('html2canvas belum ter-load.');
      return;
    }

    // aktifkan mode export (ubah layout sementara)
    receipt.classList.add('is-export');

    // tunggu 1 frame supaya CSS kebaca dulu
    await new Promise(r => requestAnimationFrame(() => r()));

    const code = receipt.dataset.bookingCode || 'booking';

    const canvas = await window.html2canvas(receipt, {
      backgroundColor: '#ffffff',
      scale: 2,
      useCORS: true,
      // ini bantu supaya ukuran sesuai export width
      windowWidth: receipt.scrollWidth,
      windowHeight: receipt.scrollHeight,
    });

    // balikin ke normal
    receipt.classList.remove('is-export');

    const link = document.createElement('a');
    link.download = `booking-${code}.png`;
    link.href = canvas.toDataURL('image/png');
    link.click();
  });
});