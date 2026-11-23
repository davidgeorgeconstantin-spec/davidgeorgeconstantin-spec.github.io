// David Constantin — portfolio v2. Vanilla JS, no dependencies.
(function () {
  var root = document.documentElement;

  // Theme toggle (dark / light), remembered in localStorage
  var toggle = document.querySelector('.theme-toggle');
  function setTheme(t, save) {
    root.setAttribute('data-theme', t);
    if (save) { try { localStorage.setItem('theme', t); } catch (e) {} }
    if (toggle) {
      toggle.setAttribute('aria-pressed', t === 'light' ? 'true' : 'false');
      toggle.setAttribute('aria-label', t === 'light' ? 'Switch to dark theme' : 'Switch to light theme');
    }
    var meta = document.querySelector('meta[name="theme-color"]');
    if (meta) meta.setAttribute('content', t === 'light' ? '#f7f8fc' : '#0a0d17');
  }
  setTheme(root.getAttribute('data-theme') || 'dark');
  if (toggle) toggle.addEventListener('click', function () {
    setTheme(root.getAttribute('data-theme') === 'light' ? 'dark' : 'light', true);
  });

  // Mobile menu
  var btn = document.querySelector('.menu-btn'), menu = document.getElementById('menu');
  function closeMenu() { if (!menu) return; menu.classList.remove('open'); btn.setAttribute('aria-expanded', 'false'); }
  if (btn && menu) {
    btn.addEventListener('click', function () {
      var open = menu.classList.toggle('open');
      btn.setAttribute('aria-expanded', open ? 'true' : 'false');
      if (open) { var first = menu.querySelector('a'); if (first) first.focus(); }
    });
    menu.querySelectorAll('a').forEach(function (a) { a.addEventListener('click', closeMenu); });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && menu.classList.contains('open')) { closeMenu(); btn.focus(); }
    });
  }

  // Nav border on scroll
  var nav = document.querySelector('.nav');
  function onScroll() { if (nav) nav.classList.toggle('scrolled', window.scrollY > 8); }
  window.addEventListener('scroll', onScroll, { passive: true }); onScroll();

  // Reveal on scroll (skipped if reduced motion or no IntersectionObserver)
  var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if ('IntersectionObserver' in window && !reduce) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } });
    }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });
    document.querySelectorAll('[data-reveal] > *').forEach(function (el, i) {
      el.classList.add('reveal'); el.style.transitionDelay = (i % 4) * 70 + 'ms'; io.observe(el);
    });
  }

  // Copy buttons for code blocks
  document.querySelectorAll('.code-block').forEach(function (block) {
    var b = block.querySelector('.copy'); var pre = block.querySelector('pre');
    if (!b || !pre || !navigator.clipboard) { if (b) b.hidden = true; return; }
    b.addEventListener('click', function () {
      navigator.clipboard.writeText(pre.innerText).then(function () {
        b.textContent = 'Copied'; setTimeout(function () { b.textContent = 'Copy'; }, 1600);
      });
    });
  });

  var y = document.getElementById('y'); if (y) y.textContent = new Date().getFullYear();
})();
