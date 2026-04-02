/*!
 * SHOPPING DATE — main.js
 * Animations & interactions — aucune dépendance
 */
(function () {
  'use strict';

  /* ═══════════════════════════════════════════
     INIT
  ═══════════════════════════════════════════ */
  document.addEventListener('DOMContentLoaded', function () {
    initNavbar();
    initScrollReveal();
    initScrollProgress();
    initParticles();
    initCounters();
    initHeroAnimations();
    initImageLightbox();
  });

  /* ═══════════════════════════════════════════
     NAVBAR — scroll + burger
  ═══════════════════════════════════════════ */
  function initNavbar() {
    var navbar  = document.getElementById('navbar');
    var burger  = document.getElementById('burger');
    var overlay = document.getElementById('navOverlay');
    if (!navbar) return;

    // Scroll sticky
    function onScroll() {
      navbar.classList.toggle('scrolled', window.scrollY > 60);
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    // Burger toggle
    if (burger && overlay) {
      burger.addEventListener('click', function () {
        var open = overlay.classList.toggle('open');
        burger.classList.toggle('open', open);
        document.body.style.overflow = open ? 'hidden' : '';
      });
      // Ferme si clic sur lien
      overlay.querySelectorAll('a').forEach(function (a) {
        a.addEventListener('click', function () {
          overlay.classList.remove('open');
          burger.classList.remove('open');
          document.body.style.overflow = '';
        });
      });
      // Ferme sur Echap
      document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && overlay.classList.contains('open')) {
          overlay.classList.remove('open');
          burger.classList.remove('open');
          document.body.style.overflow = '';
        }
      });
    }
  }

  /* ═══════════════════════════════════════════
     SCROLL PROGRESS BAR
  ═══════════════════════════════════════════ */
  function initScrollProgress() {
    var bar = document.getElementById('scroll-prog');
    if (!bar) return;
    window.addEventListener('scroll', function () {
      var d   = document.documentElement;
      var pct = d.scrollTop / (d.scrollHeight - d.clientHeight) * 100;
      bar.style.width = Math.min(pct, 100) + '%';
    }, { passive: true });
  }

  /* ═══════════════════════════════════════════
     SCROLL REVEAL — IntersectionObserver
  ═══════════════════════════════════════════ */
  function initScrollReveal() {
    var els = document.querySelectorAll('.sr, .sr-left, .sr-right, .sr-scale');
    if (!els.length) return;

    if (!window.IntersectionObserver) {
      els.forEach(function (el) { el.classList.add('visible'); });
      return;
    }

    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

    els.forEach(function (el) { io.observe(el); });
  }

  /* ═══════════════════════════════════════════
     PARTICLES — hero
  ═══════════════════════════════════════════ */
  function initParticles() {
    var container = document.getElementById('particles');
    if (!container) return;

    var colors = ['#FF1E9E', '#F057FF', '#FF0A10', '#ffffff'];
    var COUNT  = window.innerWidth < 768 ? 12 : 22;

    for (var i = 0; i < COUNT; i++) {
      var p = document.createElement('div');
      p.style.cssText = [
        'position:absolute',
        'border-radius:50%',
        'pointer-events:none',
        'left:'   + (Math.random() * 100) + '%',
        'bottom:' + (Math.random() * 40)  + '%',
        'width:'  + (Math.random() * 3 + 1.5) + 'px',
        'height:' + (Math.random() * 3 + 1.5) + 'px',
        'background:' + colors[Math.floor(Math.random() * colors.length)],
        'box-shadow:0 0 8px ' + colors[Math.floor(Math.random() * colors.length)],
        'animation:particle ' + (Math.random() * 9 + 7) + 's linear ' + (Math.random() * 8) + 's infinite'
      ].join(';');
      container.appendChild(p);
    }
  }

  /* ═══════════════════════════════════════════
     HERO ANIMATIONS — orbit + scanline
  ═══════════════════════════════════════════ */
  function initHeroAnimations() {
    // Scanline beam effect (en canvas pour perf mobile)
    var scanEl = document.getElementById('scanline');
    if (scanEl) {
      // Simple CSS animation géré via class
      scanEl.style.animation = 'scanBeam 9s linear infinite';
    }

    // Titre hero — stagger lettre par lettre si présent
    var titleLines = document.querySelectorAll('.hero-line');
    titleLines.forEach(function (line, i) {
      line.style.animationDelay = (0.3 + i * 0.18) + 's';
    });
  }

  /* ═══════════════════════════════════════════
     COUNTER ANIMATION
  ═══════════════════════════════════════════ */
  function initCounters() {
    var counters = document.querySelectorAll('[data-count]');
    if (!counters.length) return;

    if (!window.IntersectionObserver) {
      counters.forEach(function (el) {
        el.textContent = el.dataset.count + (el.dataset.suffix || '');
      });
      return;
    }

    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        animate(entry.target);
        io.unobserve(entry.target);
      });
    }, { threshold: 0.5 });

    counters.forEach(function (el) { io.observe(el); });

    function animate(el) {
      var target = parseInt(el.dataset.count, 10);
      var suffix = el.dataset.suffix || '';
      var dur    = 1600;
      var start  = null;
      function step(ts) {
        if (!start) start = ts;
        var p = Math.min((ts - start) / dur, 1);
        var e = 1 - Math.pow(1 - p, 3); // ease out cubic
        el.textContent = Math.floor(e * target) + suffix;
        if (p < 1) requestAnimationFrame(step);
        else el.textContent = target + suffix;
      }
      requestAnimationFrame(step);
    }
  }

  /* ═══════════════════════════════════════════
     LIVE TABLE SEARCH
  ═══════════════════════════════════════════ */
  window.tableSearch = function (inputId, bodyId) {
    var inp  = document.getElementById(inputId);
    var body = document.getElementById(bodyId);
    if (!inp || !body) return;
    inp.addEventListener('input', function () {
      var q = inp.value.toLowerCase();
      body.querySelectorAll('tr').forEach(function (tr) {
        tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
      });
    });
  };

  /* ═══════════════════════════════════════════
     UPLOAD PREVIEW
  ═══════════════════════════════════════════ */
  window.initUpload = function (inputId, previewId, zoneId) {
    var input   = document.getElementById(inputId);
    var preview = document.getElementById(previewId);
    var zone    = document.getElementById(zoneId);
    if (!input) return;

    function handle(file) {
      if (!file || !file.type.match(/^image\//)) {
        alert('Image invalide. Formats acceptés : JPG, PNG, WebP.');
        return;
      }
      var reader = new FileReader();
      reader.onload = function (e) {
        if (preview) { preview.src = e.target.result; preview.style.display = 'block'; }
        var hint = zone && zone.querySelector('.upload-hint');
        if (hint) hint.innerHTML = '<strong style="color:var(--pink)">' + file.name + '</strong><br><span style="font-size:.72rem;color:#444">Cliquez pour changer</span>';
      };
      reader.readAsDataURL(file);
    }

    if (zone) {
      zone.addEventListener('click', function () { input.click(); });
      zone.addEventListener('dragover', function (e) { e.preventDefault(); zone.classList.add('drag'); });
      zone.addEventListener('dragleave', function () { zone.classList.remove('drag'); });
      zone.addEventListener('drop', function (e) {
        e.preventDefault();
        zone.classList.remove('drag');
        handle(e.dataTransfer.files[0]);
      });
    }
    input.addEventListener('change', function () { handle(input.files[0]); });
  };

  /* ═══════════════════════════════════════════
     CHAR COUNTER
  ═══════════════════════════════════════════ */
  window.initCharCount = function (name, counterId, max) {
    var f = document.querySelector('[name="' + name + '"]');
    var c = document.getElementById(counterId);
    if (!f || !c) return;
    function upd() {
      var l = f.value.length;
      c.textContent = l + ' / ' + max;
      c.style.color = l > max * .88 ? 'var(--red)' : 'var(--muted)';
    }
    f.addEventListener('input', upd);
    upd();
  };

  /* ═══════════════════════════════════════════
     TOAST
  ═══════════════════════════════════════════ */
  window.showToast = function (msg, type) {
    var old = document.querySelector('.sd-toast');
    if (old) old.remove();
    var t = document.createElement('div');
    t.className = 'sd-toast';
    var err = type === 'error';
    t.style.cssText = [
      'position:fixed', 'bottom:24px', 'right:24px',
      'padding:13px 22px',
      'border-radius:12px',
      'font-family:var(--f-body)',
      'font-size:.84rem', 'font-weight:600',
      'z-index:9999',
      'max-width:320px',
      'border:1px solid ' + (err ? 'rgba(255,10,16,.3)' : 'rgba(30,255,120,.3)'),
      'background:'       + (err ? 'rgba(255,10,16,.12)' : 'rgba(30,255,120,.12)'),
      'color:'            + (err ? '#ff7070' : '#5fffaa'),
      'backdrop-filter:blur(12px)',
      '-webkit-backdrop-filter:blur(12px)',
      'animation:slideInUp .35s var(--ease)'
    ].join(';');
    t.textContent = msg;
    document.body.appendChild(t);
    setTimeout(function () {
      t.style.transition = 'opacity .4s';
      t.style.opacity = '0';
      setTimeout(function () { t.remove(); }, 420);
    }, 3400);
  };

  function initImageLightbox() {
    var lightbox = document.getElementById('imageLightbox');
    var img = document.getElementById('imageLightboxImg');
    if (!lightbox || !img) return;

    function closeLightbox() {
      lightbox.hidden = true;
      img.src = '';
      document.body.style.overflow = '';
    }

    document.querySelectorAll('.av-preview').forEach(function (preview) {
      preview.addEventListener('click', function () {
        var fullSrc = preview.getAttribute('data-full-src') || preview.getAttribute('src');
        if (!fullSrc) return;
        img.src = fullSrc;
        lightbox.hidden = false;
        document.body.style.overflow = 'hidden';
      });
    });

    lightbox.addEventListener('click', function (e) {
      if (e.target === lightbox || e.target.classList.contains('image-lightbox-close')) {
        closeLightbox();
      }
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && !lightbox.hidden) closeLightbox();
    });
  }

  /* ═══════════════════════════════════════════
     FORM VALIDATION
  ═══════════════════════════════════════════ */
  document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('sd-form');
    if (!form) return;

    var fields = form.querySelectorAll('[data-rules]');

    fields.forEach(function (f) {
      f.addEventListener('blur',  function () { validate(f) });
      f.addEventListener('input', function () { clearErr(f) });
    });

    form.addEventListener('submit', function (e) {
      var ok = true;
      fields.forEach(function (f) { if (!validate(f)) ok = false; });
      if (!ok) {
        e.preventDefault();
        var first = form.querySelector('.error');
        if (first) first.focus();
      }
    });

    function validate(f) {
      var rules = (f.dataset.rules || '').split(',');
      var val   = f.value.trim();
      var lbl   = f.dataset.label || 'Ce champ';
      var err   = '';

      for (var i = 0; i < rules.length; i++) {
        var r = rules[i].trim();
        if (err) break;
        if (r === 'required' && !val) err = lbl + ' est obligatoire.';
        else if (r.startsWith('min:')) { var m = +r.split(':')[1]; if (val.length < m) err = lbl + ' : minimum ' + m + ' caractères.' }
        else if (r === 'email' && val && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) err = 'Email invalide.';
        else if (r === 'phone' && val && !/^[\d\s+\-()]{7,20}$/.test(val)) err = 'Numéro invalide.';
        else if (r === 'age18' && +val < 18) err = 'Âge minimum : 18 ans.';
        else if (r === 'age45' && +val > 45) err = 'Âge maximum : 45 ans.';
      }

      var errEl = document.getElementById('e_' + f.name);
      if (errEl) errEl.textContent = err;
      f.classList.toggle('error', !!err);
      return !err;
    }

    function clearErr(f) {
      var e = document.getElementById('e_' + f.name);
      if (e) e.textContent = '';
      f.classList.remove('error');
    }
  });

})();
