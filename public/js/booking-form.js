document.addEventListener('DOMContentLoaded', () => {
    const capster = document.getElementById('capsterSelect');
    const tanggal = document.getElementById('tanggalBooking');
    const jam = document.getElementById('jamBooking');
    const slotContainer = document.getElementById('slotContainer');
    const slotMessage = document.getElementById('slotMessage');

    async function loadSlots() {
        const capsterId = capster.value;
        const date = tanggal.value;

        jam.value = '';
        slotContainer.innerHTML = '';
        slotMessage.textContent = '';

        if (!capsterId || !date) return;

        const url = `${window.bookingAvailabilityUrl}?capster_id=${capsterId}&date=${date}`;
        const res = await fetch(url);
        const data = await res.json();

        if (!data.is_shop_open) {
            slotMessage.textContent = 'Barber tutup di tanggal ini.';
            return;
        }

        if (!data.is_capster_working) {
            slotMessage.textContent = 'Capster tidak bekerja di tanggal ini.';
            return;
        }

        if (!data.available_slots.length) {
            slotMessage.textContent = 'Semua slot sudah penuh.';
            return;
        }

        data.all_slots.forEach(slot => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.textContent = slot;
            btn.classList.add('slot-btn');

            if (data.booked_slots.includes(slot)) {
                btn.disabled = true;
                btn.classList.add('slot-booked');
            } else {
                btn.classList.add('slot-available');
                btn.addEventListener('click', () => {
                    document.querySelectorAll('#slotContainer .slot-btn').forEach(el => {
                        el.classList.remove('slot-selected');
                        if (!el.disabled) {
                            el.classList.add('slot-available');
                        }
                    });

                    btn.classList.remove('slot-available');
                    btn.classList.add('slot-selected');
                    jam.value = slot;
                });
            }

            slotContainer.appendChild(btn);
        });
    }

    capster.addEventListener('change', loadSlots);
    tanggal.addEventListener('change', loadSlots);
});