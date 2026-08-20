(() => {
  const images = [...document.querySelectorAll('.gemonio-section-content img')].filter((img) => {
    if (!img.currentSrc && !img.src) return false;
    if (img.closest('a')) return false;
    return true;
  });
  if (!images.length) return;

  const overlay = document.createElement('div');
  overlay.className = 'gemonio-lightbox';
  overlay.hidden = true;
  overlay.setAttribute('role', 'dialog');
  overlay.setAttribute('aria-modal', 'true');
  overlay.setAttribute('aria-label', 'Image preview');
  overlay.innerHTML = '<button class="gemonio-lightbox__prev" type="button" aria-label="Previous image">‹</button><div class="gemonio-lightbox__dialog"><img class="gemonio-lightbox__image" alt=""><p class="gemonio-lightbox__caption" hidden></p></div><button class="gemonio-lightbox__next" type="button" aria-label="Next image">›</button><button class="gemonio-lightbox__close" type="button" aria-label="Close image preview">×</button>';
  document.body.appendChild(overlay);

  const large = overlay.querySelector('.gemonio-lightbox__image');
  const caption = overlay.querySelector('.gemonio-lightbox__caption');
  const close = overlay.querySelector('.gemonio-lightbox__close');
  const prev = overlay.querySelector('.gemonio-lightbox__prev');
  const next = overlay.querySelector('.gemonio-lightbox__next');
  let opener = null;
  let index = 0;

  const getCaption = (img) => img.closest('figure')?.querySelector('figcaption')?.textContent?.trim() || img.alt?.trim() || '';
  const show = (newIndex) => {
    index = (newIndex + images.length) % images.length;
    const img = images[index];
    large.src = img.currentSrc || img.src;
    large.alt = img.alt || '';
    const text = getCaption(img);
    caption.textContent = text;
    caption.hidden = !text;
    const multiple = images.length > 1;
    prev.hidden = !multiple;
    next.hidden = !multiple;
  };
  const open = (img) => {
    opener = img;
    show(images.indexOf(img));
    overlay.hidden = false;
    document.body.classList.add('gemonio-lightbox-open');
    close.focus();
  };
  const hide = () => {
    overlay.hidden = true;
    document.body.classList.remove('gemonio-lightbox-open');
    large.removeAttribute('src');
    opener?.focus?.();
  };

  images.forEach((img) => {
    img.dataset.gemonioLightbox = '1';
    img.tabIndex = 0;
    img.setAttribute('role', 'button');
    img.setAttribute('aria-label', img.alt ? `Open image: ${img.alt}` : 'Open image');
    img.addEventListener('click', () => open(img));
    img.addEventListener('keydown', (event) => {
      if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); open(img); }
    });
  });

  prev.addEventListener('click', () => show(index - 1));
  next.addEventListener('click', () => show(index + 1));
  close.addEventListener('click', hide);
  overlay.addEventListener('click', (event) => { if (event.target === overlay) hide(); });
  document.addEventListener('keydown', (event) => {
    if (overlay.hidden) return;
    if (event.key === 'Escape') hide();
    if (event.key === 'ArrowLeft' && images.length > 1) show(index - 1);
    if (event.key === 'ArrowRight' && images.length > 1) show(index + 1);
  });
})();
