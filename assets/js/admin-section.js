(($) => {
  const field = document.querySelector('[data-gemonio-media-field]');
  if (!field || !window.wp || !wp.media) return;

  const input = field.querySelector('[data-gemonio-media-id]');
  const preview = field.querySelector('[data-gemonio-media-preview]');
  const select = field.querySelector('[data-gemonio-media-select]');
  const remove = field.querySelector('[data-gemonio-media-remove]');
  let frame;

  select.addEventListener('click', () => {
    if (frame) {
      frame.open();
      return;
    }

    frame = wp.media({
      title: 'Choose separator image',
      button: { text: 'Use image' },
      library: { type: 'image' },
      multiple: false,
    });

    frame.on('select', () => {
      const attachment = frame.state().get('selection').first().toJSON();
      input.value = attachment.id;
      const src = attachment.sizes?.medium?.url || attachment.url;
      preview.innerHTML = `<img src="${src}" alt="">`;
      remove.hidden = false;
    });

    frame.open();
  });

  remove.addEventListener('click', () => {
    input.value = '';
    preview.innerHTML = '';
    remove.hidden = true;
  });
})(jQuery);
