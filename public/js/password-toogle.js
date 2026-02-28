document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.pw-toggle').forEach(btn => {
    btn.addEventListener('click', () => {
      const input = document.getElementById(btn.dataset.target);
      if (!input) return;

      const icon = btn.querySelector('.material-symbols-outlined');
      const isHidden = input.type === 'password';

      input.type = isHidden ? 'text' : 'password';
      if (icon) icon.textContent = isHidden ? 'visibility_off' : 'visibility';
    });
  });
});