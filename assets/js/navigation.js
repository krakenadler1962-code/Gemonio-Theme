(() => {
  const toggle = document.querySelector('.nav-toggle');
  const nav = document.querySelector('.primary-navigation');
  const header = document.querySelector('.site-header');
  const settings = window.gemonioNavigation || {};
  const reduceMotion = window.matchMedia?.('(prefers-reduced-motion: reduce)').matches;

  if (toggle && nav) {
    toggle.addEventListener('click', () => {
      const open = toggle.getAttribute('aria-expanded') === 'true';
      toggle.setAttribute('aria-expanded', String(!open));
      nav.classList.toggle('is-open', !open);
    });

    nav.addEventListener('click', (event) => {
      if (event.target.closest('a')) {
        toggle.setAttribute('aria-expanded', 'false');
        nav.classList.remove('is-open');
      }
    });
  }

  const easing = (name, t) => {
    if (name === 'linear') return t;
    if (name === 'soft') return 1 - Math.pow(1 - t, 5);
    return t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;
  };

  const scrollToY = (targetY, duration = Number(settings.scrollDuration) || 0, callback) => {
    const startY = window.scrollY;
    const distance = targetY - startY;
    const ms = reduceMotion ? 0 : Math.max(0, duration);
    if (!ms || Math.abs(distance) < 2) {
      window.scrollTo(0, targetY);
      callback?.();
      return;
    }

    const start = performance.now();
    const step = (now) => {
      const progress = Math.min(1, (now - start) / ms);
      const eased = easing(settings.scrollEasing || 'natural', progress);
      window.scrollTo(0, startY + distance * eased);
      if (progress < 1) requestAnimationFrame(step);
      else callback?.();
    };
    requestAnimationFrame(step);
  };

  const sectionTargetY = (target) => {
    const headerHeight = header?.getBoundingClientRect().height || 0;
    return Math.max(0, target.getBoundingClientRect().top + window.scrollY - headerHeight + 1);
  };

  document.addEventListener('click', (event) => {
    const link = event.target.closest('a[data-gemonio-section-link]');
    if (!link) return;
    const anchor = link.dataset.gemonioSectionLink;
    const target = document.querySelector(`[data-gemonio-section="${CSS.escape(anchor)}"]`);
    if (!target) return;

    event.preventDefault();
    scrollToY(sectionTargetY(target), Number(settings.scrollDuration) || 0, () => {
      if (settings.updateHash && history.replaceState) {
        history.replaceState(null, '', `#${anchor}`);
      }
    });
  });

  const links = [...document.querySelectorAll('[data-gemonio-section-link]')];
  const sections = [...document.querySelectorAll('[data-gemonio-section]')];

  if (links.length && sections.length && 'IntersectionObserver' in window) {
    const byAnchor = new Map(links.map((link) => [link.dataset.gemonioSectionLink, link]));
    let current;

    const activate = (anchor) => {
      if (!anchor || anchor === current) return;
      current = anchor;
      links.forEach((link) => {
        const active = link.dataset.gemonioSectionLink === anchor;
        link.classList.toggle('is-current', active);
        if (active) link.setAttribute('aria-current', 'location');
        else link.removeAttribute('aria-current');
      });
    };

    const observer = new IntersectionObserver((entries) => {
      const visible = entries
        .filter((entry) => entry.isIntersecting)
        .sort((a, b) => b.intersectionRatio - a.intersectionRatio)[0];
      if (visible && byAnchor.has(visible.target.dataset.gemonioSection)) {
        activate(visible.target.dataset.gemonioSection);
      }
    }, {
      rootMargin: '-24% 0px -58% 0px',
      threshold: [0, 0.1, 0.25, 0.5],
    });
    sections.forEach((section) => observer.observe(section));
  }

  const backToTop = document.querySelector('[data-gemonio-back-to-top]');
  const updateScrolledState = () => {
    const scrolled = window.scrollY > 80;
    if (settings.compactHeader && header) header.classList.toggle('is-scrolled', scrolled);
    if (backToTop) backToTop.hidden = window.scrollY < Math.max(420, window.innerHeight * 0.65);
  };
  updateScrolledState();
  window.addEventListener('scroll', updateScrolledState, { passive: true });

  backToTop?.addEventListener('click', () => {
    scrollToY(0, Number(settings.scrollDuration) || 0, () => {
      if (settings.updateHash && history.replaceState) {
        history.replaceState(null, '', `${location.pathname}${location.search}`);
      }
    });
  });

  if (location.hash) {
    const anchor = decodeURIComponent(location.hash.slice(1));
    const target = document.querySelector(`[data-gemonio-section="${CSS.escape(anchor)}"]`);
    if (target) {
      requestAnimationFrame(() => window.scrollTo(0, sectionTargetY(target)));
    }
  }
})();
