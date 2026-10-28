const slides = [...document.querySelectorAll('.slide')];
const prev = document.getElementById('prev');
const next = document.getElementById('next');
const counter = document.getElementById('counter');
let current = 0;

// Khmer digits for the slide counter
const kh = (n) => String(n).replace(/\d/g, (d) => '០១២៣៤៥៦៧៨៩'[d]);

function show(index, updateHash = true) {
  current = Math.max(0, Math.min(slides.length - 1, index));
  slides.forEach((s, i) => { s.hidden = i !== current; });
  counter.textContent = `${kh(current + 1)} / ${kh(slides.length)}`;
  prev.disabled = current === 0;
  next.disabled = current === slides.length - 1;
  if (updateHash) history.replaceState(null, '', '#' + (current + 1));
  window.scrollTo(0, 0);
}

prev.addEventListener('click', () => show(current - 1));
next.addEventListener('click', () => show(current + 1));

document.addEventListener('keydown', (e) => {
  if (e.altKey || e.ctrlKey || e.metaKey) return;
  if (e.key === 'ArrowRight' || e.key === 'PageDown') show(current + 1);
  else if (e.key === 'ArrowLeft' || e.key === 'PageUp') show(current - 1);
  else if (e.key === 'Home') show(0);
  else if (e.key === 'End') show(slides.length - 1);
});

// Swipe left or right on a phone
let startX = null;
document.addEventListener('touchstart', (e) => { startX = e.touches[0].clientX; }, { passive: true });
document.addEventListener('touchend', (e) => {
  if (startX === null) return;
  const dx = e.changedTouches[0].clientX - startX;
  startX = null;
  if (Math.abs(dx) > 70) show(current + (dx < 0 ? 1 : -1));
}, { passive: true });

// Open the slide named in the address, e.g. index.php#5
const fromHash = () => {
  const n = parseInt(location.hash.slice(1), 10);
  show(Number.isNaN(n) ? 0 : n - 1, false);
};
window.addEventListener('hashchange', fromHash);
fromHash();

// Draw the math (KaTeX is loaded from cdnjs in index.php)
window.addEventListener('load', () => {
  if (typeof renderMathInElement !== 'function') return;
  renderMathInElement(document.body, {
    delimiters: [
      { left: '\\[', right: '\\]', display: true },
      { left: '\\(', right: '\\)', display: false },
    ],
    throwOnError: false,
  });
});
