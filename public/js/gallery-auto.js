document.addEventListener('DOMContentLoaded', () => {
  const track = document.getElementById('galleryTrack');
  console.log('track:', track);
  if (!track) return;

  // start dari ujung kanan biar langsung jalan ke kiri
  track.scrollLeft = track.scrollWidth;

  let dir = -1;        // -1 = ke kiri
  let paused = false;
  const speed = 0.6;

  function step() {
    if (!paused) {
      track.scrollLeft += dir * speed;

      const atEnd = track.scrollLeft + track.clientWidth >= track.scrollWidth - 1;
      const atStart = track.scrollLeft <= 0;

      if (atEnd) dir = -1;
      if (atStart) dir = 1;
    }
    requestAnimationFrame(step);
  }

  track.addEventListener('mouseenter', () => paused = true);
  track.addEventListener('mouseleave', () => paused = false);
  track.addEventListener('touchstart', () => paused = true, { passive: true });
  track.addEventListener('touchend', () => paused = false);

  requestAnimationFrame(step);
});