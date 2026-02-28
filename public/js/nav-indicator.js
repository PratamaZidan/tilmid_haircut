(() => {
  if ('scrollRestoration' in history) history.scrollRestoration = 'manual';
  if (location.hash) {
    history.replaceState(null, '', location.pathname);
    window.scrollTo(0, 0);
  }
})();

document.addEventListener('DOMContentLoaded', () => {
  const nav = document.getElementById('navLinks');
  const indicator = document.getElementById('navIndicator');
  if (!nav || !indicator) return;

  const links = Array.from(nav.querySelectorAll('a'));
  const anchorLinks = links.filter(a => (a.getAttribute('href') || '').startsWith('#'));

  function clearActive() {
    links.forEach(a => a.classList.remove('is-active'));
    nav.classList.remove('has-active');
  }

  function moveIndicatorTo(el) {
    const navRect = nav.getBoundingClientRect();
    const elRect = el.getBoundingClientRect();
    const left = elRect.left - navRect.left;
    const width = elRect.width;

    indicator.style.transform = `translateX(${left}px)`;
    indicator.style.width = `${width}px`;
  }

  function setActive(el) {
    if (el.classList.contains('is-active')) return; // biar gak kedip
    links.forEach(a => a.classList.remove('is-active'));
    el.classList.add('is-active');
    nav.classList.add('has-active');
    moveIndicatorTo(el);
  }

  const navOffset = 160; // sesuaikan tinggi navbar kamu (logo 70 bikin tinggi)

  // Klik: smooth scroll + update hash
  links.forEach(a => {
    a.addEventListener('click', (e) => {
      const href = a.getAttribute('href');
      if (!href) return;

      if (!href.startsWith('#')) {
        setActive(a); // /booking
        return;
      }

      e.preventDefault();
      const target = document.querySelector(href);
      if (!target) return;

      setActive(a);

      const y = target.getBoundingClientRect().top + window.pageYOffset - navOffset;
      window.scrollTo({ top: y, behavior: 'smooth' });
      history.pushState(null, '', href);
    });
  });

  // ambil section dari href
  const sections = anchorLinks
    .map(a => document.querySelector(a.getAttribute('href')))
    .filter(Boolean);

  // ✅ Deteksi section aktif: yang top-nya paling dekat dengan navOffset
  function updateActiveByScroll() {
    // kalau masih sangat atas (hero), matiin active
    if (window.scrollY < 40) {
      clearActive();
      return;
    }

    let bestSection = null;
    let bestDist = Infinity;

    for (const sec of sections) {
      const top = sec.getBoundingClientRect().top;
      const dist = Math.abs(top - navOffset);

      // ambil section yang jaraknya paling dekat dengan posisi navOffset
      if (dist < bestDist) {
        bestDist = dist;
        bestSection = sec;
      }
    }

    if (!bestSection) {
      clearActive();
      return;
    }

    const id = '#' + bestSection.id;
    const link = anchorLinks.find(a => a.getAttribute('href') === id);
    if (link) setActive(link);
  }

  // throttle scroll
  let ticking = false;
  window.addEventListener('scroll', () => {
    if (ticking) return;
    ticking = true;
    requestAnimationFrame(() => {
      updateActiveByScroll();
      ticking = false;
    });
  }, { passive: true });

  // init
  clearActive();
  updateActiveByScroll();

  window.addEventListener('resize', () => {
    const active = nav.querySelector('a.is-active');
    if (active) moveIndicatorTo(active);
    updateActiveByScroll();
  });
});