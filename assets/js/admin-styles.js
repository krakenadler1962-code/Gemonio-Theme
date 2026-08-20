(() => {
  const form = document.querySelector('[data-gemonio-style-form]');
  const preview = document.querySelector('[data-gemonio-style-preview]');
  const unsaved = document.querySelector('[data-gemonio-unsaved]');

  // Preview helpers must exist before colour controls initialise.
  // Colour fields call refreshPreview() during setup; keeping these helpers
  // above that setup prevents a temporal-dead-zone error that used to abort
  // all colour/palette click handlers.
  const field = (key) => document.querySelector(`[name="gemonio_styles[${key}]"]`);
  const titlePreview = preview ? preview.querySelector('.gemonio-style-preview__title') : null;

  const setVar = (name, value) => {
    if (preview && value !== undefined && value !== null && value !== '') preview.style.setProperty(name, value);
  };

  function refreshPreview() {
    if (!preview) return;

    const colors = {
      light_bg: '--preview-light-bg', light_text: '--preview-light-text', heading_color: '--preview-heading',
      title_color: '--preview-title-color', line_color: '--preview-line-color', nav_bg: '--preview-nav-bg',
      nav_text: '--preview-nav-text', nav_active_color: '--preview-nav-active', button_bg: '--preview-button-bg', button_text: '--preview-button-text'
    };
    Object.entries(colors).forEach(([key, cssVar]) => {
      const el = field(key); if (el) setVar(cssVar, el.value);
    });

    const pxVars = { body_size: '--preview-body-size', title_size: '--preview-title-size', h3_size: '--preview-h3-size', button_radius: '--preview-button-radius' };
    Object.entries(pxVars).forEach(([key, cssVar]) => {
      const el = field(key); if (el) setVar(cssVar, `${el.value}px`);
    });

    const bodyFont = field('body_font');
    const titleFont = field('title_font');
    const localBody = field('local_body_font_url');
    const localTitle = field('local_title_font_url');
    let localStyle = document.getElementById('gemonio-preview-local-fonts');
    if (!localStyle) { localStyle = document.createElement('style'); localStyle.id = 'gemonio-preview-local-fonts'; document.head.appendChild(localStyle); }
    const safeCssUrl = (value) => String(value || '').replace(/[\"\n\r]/g, '');
    const fontRules = [];
    if (localBody?.value) fontRules.push(`@font-face{font-family:"Gemonio Preview Local Text";src:url("${safeCssUrl(localBody.value)}") format("woff2");font-style:normal;font-weight:100 900;font-display:swap;}`);
    if (localTitle?.value) fontRules.push(`@font-face{font-family:"Gemonio Preview Local Display";src:url("${safeCssUrl(localTitle.value)}") format("woff2");font-style:normal;font-weight:100 900;font-display:swap;}`);
    localStyle.textContent = fontRules.join('');
    if (bodyFont) setVar('--preview-body-font', localBody?.value ? `"Gemonio Preview Local Text",${bodyFont.value}` : bodyFont.value);
    if (titleFont) setVar('--preview-title-font', localTitle?.value ? `"Gemonio Preview Local Display",${titleFont.value}` : titleFont.value);

    const weightVars = { body_weight: '--preview-body-weight', title_weight: '--preview-title-weight', h3_weight: '--preview-h3-weight', nav_weight: '--preview-nav-weight' };
    Object.entries(weightVars).forEach(([key, cssVar]) => {
      const el = field(key); if (el) setVar(cssVar, el.value);
    });

    const overlay = field('parallax_overlay');
    if (overlay) setVar('--preview-parallax-overlay', Math.max(0, Math.min(70, Number(overlay.value) || 0)) / 100);

    if (titlePreview) {
      const align = field('title_align');
      if (align) titlePreview.style.textAlign = align.value;
      const rule = field('title_rule');
      const position = field('title_rule_position');
      const enabled = rule ? rule.checked : true;
      const mode = position ? position.value : 'both';
      titlePreview.querySelectorAll('[data-preview-title-line]').forEach((line) => {
        const side = line.dataset.previewTitleLine;
        line.hidden = !enabled || (mode !== 'both' && mode !== side);
      });
    }
  }

  const markDirty = () => {
    if (unsaved) unsaved.hidden = false;
  };

  if (form) {
    form.addEventListener('input', markDirty);
    form.addEventListener('change', markDirty);
  }

  document.querySelectorAll('[data-gemonio-range-for]').forEach((range) => {
    const target = document.getElementById(range.dataset.gemonioRangeFor || '');
    if (!target) return;
    range.addEventListener('input', () => {
      target.value = range.value;
      target.dispatchEvent(new Event('input', { bubbles: true }));
    });
    target.addEventListener('input', () => {
      range.value = target.value;
    });
  });

  const normalizeHex = (value) => {
    const raw = String(value || '').trim();
    if (/^#[0-9a-f]{6}$/i.test(raw)) return raw.toUpperCase();
    if (/^#[0-9a-f]{3}$/i.test(raw)) {
      return `#${raw[1]}${raw[1]}${raw[2]}${raw[2]}${raw[3]}${raw[3]}`.toUpperCase();
    }
    return null;
  };

  const hexToRgb = (hex) => {
    const value = normalizeHex(hex);
    if (!value) return null;
    return {
      r: parseInt(value.slice(1, 3), 16),
      g: parseInt(value.slice(3, 5), 16),
      b: parseInt(value.slice(5, 7), 16)
    };
  };

  const rgbToHex = ({ r, g, b }) => `#${[r, g, b].map((part) => Math.max(0, Math.min(255, Math.round(part))).toString(16).padStart(2, '0')).join('')}`.toUpperCase();

  const mixHex = (from, to, ratio) => {
    const a = hexToRgb(from);
    const b = hexToRgb(to);
    if (!a || !b) return normalizeHex(from) || '#000000';
    return rgbToHex({
      r: a.r + (b.r - a.r) * ratio,
      g: a.g + (b.g - a.g) * ratio,
      b: a.b + (b.b - a.b) * ratio
    });
  };

  // Forms-style tonal scale: four tints, the base tone, four shades.
  const shadeScale = (base) => [
    ['50', mixHex(base, '#FFFFFF', 0.90)],
    ['100', mixHex(base, '#FFFFFF', 0.76)],
    ['200', mixHex(base, '#FFFFFF', 0.58)],
    ['300', mixHex(base, '#FFFFFF', 0.34)],
    ['500', normalizeHex(base) || '#000000'],
    ['600', mixHex(base, '#000000', 0.14)],
    ['700', mixHex(base, '#000000', 0.28)],
    ['800', mixHex(base, '#000000', 0.43)],
    ['900', mixHex(base, '#000000', 0.58)]
  ];

  const closeColorPanel = (control) => {
    if (!control) return;
    const toggle = control.querySelector('[data-gemonio-color-toggle]');
    const shades = control.querySelector('[data-gemonio-color-shades]');
    if (toggle) toggle.setAttribute('aria-expanded', 'false');
    if (shades) shades.hidden = true;
    control.classList.remove('is-open');
  };

  const closeAllColorPanels = (except = null) => {
    document.querySelectorAll('[data-gemonio-color-control].is-open').forEach((control) => {
      if (control !== except) closeColorPanel(control);
    });
  };

  const openColorPanel = (control) => {
    if (!control) return;
    closeAllColorPanels(control);
    const toggle = control.querySelector('[data-gemonio-color-toggle]');
    const shades = control.querySelector('[data-gemonio-color-shades]');
    if (toggle) toggle.setAttribute('aria-expanded', 'true');
    if (shades) shades.hidden = false;
    control.classList.add('is-open');
  };

  const renderColorShades = (color, baseOverride = null) => {
    const control = color.closest('[data-gemonio-color-control]');
    const shades = control?.querySelector('[data-gemonio-color-shades]');
    if (!control || !shades) return;
    const base = normalizeHex(baseOverride || control.dataset.gemonioShadeBase || color.value) || '#000000';
    control.dataset.gemonioShadeBase = base;
    const current = normalizeHex(color.value) || base;
    shades.innerHTML = '';
    shadeScale(base).forEach(([step, value]) => {
      const button = document.createElement('button');
      button.type = 'button';
      button.className = 'gemonio-color-shade';
      button.dataset.gemonioShadeValue = value;
      button.style.setProperty('--gemonio-shade', value);
      button.title = `${step} · ${value}`;
      button.setAttribute('aria-label', `${step} · ${value}`);
      button.setAttribute('aria-pressed', value === current ? 'true' : 'false');
      if (value === current) button.classList.add('is-active');
      button.addEventListener('click', () => {
        setColorValue(color, value, false);
        color.dispatchEvent(new Event('input', { bubbles: true }));
        closeColorPanel(control);
        control.querySelector('[data-gemonio-color-toggle]')?.focus();
      });
      shades.appendChild(button);
    });
  };

  const setColorValue = (color, value, rebuildShades = true) => {
    const normalized = normalizeHex(value);
    if (!normalized) return false;
    const control = color.closest('[data-gemonio-color-control]');
    const text = control?.querySelector('[data-gemonio-color-text]');
    const current = control?.querySelector('[data-gemonio-color-current]');
    color.value = normalized;
    if (text) text.value = normalized;
    if (current) current.style.setProperty('--gemonio-current-color', normalized);
    if (rebuildShades && control) control.dataset.gemonioShadeBase = normalized;
    renderColorShades(color, rebuildShades ? normalized : null);
    refreshPreview();
    return true;
  };

  document.querySelectorAll('[data-gemonio-color-key]').forEach((color) => {
    const control = color.closest('[data-gemonio-color-control]');
    const text = control?.querySelector('[data-gemonio-color-text]');
    if (!control || !text) return;
    const toggle = control.querySelector('[data-gemonio-color-toggle]');
    setColorValue(color, color.value, true);

    toggle?.addEventListener('click', (event) => {
      event.stopPropagation();
      if (control.classList.contains('is-open')) closeColorPanel(control);
      else openColorPanel(control);
    });

    text.addEventListener('focus', () => closeAllColorPanels());
    text.addEventListener('input', () => {
      const value = normalizeHex(text.value);
      if (!value) return;
      setColorValue(color, value, true);
      color.dispatchEvent(new Event('input', { bubbles: true }));
    });
    text.addEventListener('blur', () => {
      const value = normalizeHex(text.value);
      if (value) setColorValue(color, value, true);
      else text.value = normalizeHex(color.value) || '#000000';
    });
  });

  document.addEventListener('click', (event) => {
    if (!event.target.closest('[data-gemonio-color-control]')) closeAllColorPanels();
  });

  document.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape') return;
    const open = document.querySelector('[data-gemonio-color-control].is-open');
    if (!open) return;
    closeColorPanel(open);
    open.querySelector('[data-gemonio-color-toggle]')?.focus();
  });

  document.querySelectorAll('[data-gemonio-palette]').forEach((palette) => {
    palette.addEventListener('click', () => {
      let values;
      try { values = JSON.parse(palette.dataset.gemonioPalette || '{}'); } catch (_) { return; }
      Object.entries(values).forEach(([key, value]) => {
        const color = document.querySelector(`[name="gemonio_styles[${key}]"]`);
        if (!color) return;
        setColorValue(color, value, true);
        color.dispatchEvent(new Event('input', { bubbles: true }));
      });
      document.querySelectorAll('.gemonio-palette').forEach((item) => item.classList.remove('is-active'));
      document.querySelectorAll('[data-gemonio-palette]').forEach((item) => item.setAttribute('aria-pressed', 'false'));
      palette.classList.add('is-active');
      palette.setAttribute('aria-pressed', 'true');
      document.querySelector('[data-gemonio-custom-palette]')?.classList.remove('is-active');
      markDirty();
      refreshPreview();
    });
  });

  document.querySelectorAll('[data-gemonio-color-key]').forEach((color) => {
    color.addEventListener('input', () => {
      const active = document.querySelector('.gemonio-palette.is-active[data-gemonio-palette]');
      if (active && !active.matches(':focus')) {
        active.classList.remove('is-active');
        active.setAttribute('aria-pressed', 'false');
        document.querySelector('[data-gemonio-custom-palette]')?.classList.add('is-active');
      }
    });
  });

  // Branding logo picker.
  const logoField = document.querySelector('[data-gemonio-logo-field]');
  if (logoField && window.wp?.media) {
    const idInput = logoField.querySelector('[data-gemonio-logo-id]');
    const previewEl = logoField.querySelector('[data-gemonio-logo-preview]');
    const selectBtn = logoField.querySelector('[data-gemonio-logo-select]');
    const removeBtn = logoField.querySelector('[data-gemonio-logo-remove]');
    selectBtn?.addEventListener('click', () => {
      const frame = wp.media({ title: 'Logo wählen', button: { text: 'Logo verwenden' }, multiple: false, library: { type: 'image' } });
      frame.on('select', () => {
        const item = frame.state().get('selection').first()?.toJSON();
        if (!item || !idInput || !previewEl) return;
        idInput.value = item.id || 0;
        previewEl.innerHTML = `<img src="${item.sizes?.medium?.url || item.url}" alt="">`;
        if (removeBtn) removeBtn.hidden = false;
        idInput.dispatchEvent(new Event('input', { bubbles: true }));
      });
      frame.open();
    });
    removeBtn?.addEventListener('click', () => {
      if (idInput) idInput.value = '0';
      if (previewEl) previewEl.innerHTML = '';
      removeBtn.hidden = true;
      idInput?.dispatchEvent(new Event('input', { bubbles: true }));
    });
  }

  // Optional local WOFF2 font picker. The URL stays editable by hand.
  document.querySelectorAll('[data-gemonio-font-file]').forEach((wrap) => {
    const input = wrap.querySelector('[data-gemonio-font-url]');
    const select = wrap.querySelector('[data-gemonio-font-select]');
    const remove = wrap.querySelector('[data-gemonio-font-remove]');
    if (!input) return;
    if (select && window.wp?.media) select.addEventListener('click', () => {
      const frame = wp.media({ title: 'Lokale Font-Datei wählen', button: { text: 'Font verwenden' }, multiple: false });
      frame.on('select', () => {
        const item = frame.state().get('selection').first()?.toJSON();
        if (!item?.url) return;
        input.value = item.url;
        if (remove) remove.hidden = false;
        input.dispatchEvent(new Event('input', { bubbles: true }));
      });
      frame.open();
    });
    remove?.addEventListener('click', () => {
      input.value = '';
      remove.hidden = true;
      input.dispatchEvent(new Event('input', { bubbles: true }));
    });
  });

  // Native WordPress code editor for Additional CSS.
  const cssEditor = document.querySelector('[data-gemonio-code-editor]');
  if (cssEditor && window.wp?.codeEditor) {
    const editor = wp.codeEditor.initialize(cssEditor, window.gemonioCodeEditorSettings || {});
    editor?.codemirror?.on('change', () => {
      cssEditor.value = editor.codemirror.getValue();
      cssEditor.dispatchEvent(new Event('input', { bubbles: true }));
    });
  }

  const previewWrap = document.querySelector('[data-gemonio-preview-wrap]');
  const workspace = document.querySelector('.gemonio-style-workspace');
  const collapseButton = document.querySelector('[data-gemonio-preview-collapse]');
  const reopenButton = document.querySelector('[data-gemonio-preview-reopen]');
  const expandButton = document.querySelector('[data-gemonio-preview-expand]');
  const deviceButtons = document.querySelectorAll('[data-gemonio-preview-device]');
  const previewStateKey = 'gemonioStylePreviewState';

  const savePreviewState = () => {
    if (!previewWrap) return;
    const state = {
      collapsed: previewWrap.classList.contains('is-collapsed'),
      device: previewWrap.classList.contains('is-mobile') ? 'mobile' : 'desktop'
    };
    try { localStorage.setItem(previewStateKey, JSON.stringify(state)); } catch (_) {}
  };

  const setPreviewCollapsed = (collapsed) => {
    if (!previewWrap || !workspace) return;
    previewWrap.classList.toggle('is-collapsed', collapsed);
    workspace.classList.toggle('is-preview-collapsed', collapsed);
    collapseButton?.setAttribute('aria-pressed', collapsed ? 'true' : 'false');
    if (collapsed) {
      previewWrap.classList.remove('is-expanded');
      workspace.classList.remove('is-preview-expanded');
      expandButton?.setAttribute('aria-pressed', 'false');
    }
    savePreviewState();
  };

  const setPreviewExpanded = (expanded) => {
    if (!previewWrap || !workspace) return;
    if (expanded) setPreviewCollapsed(false);
    previewWrap.classList.toggle('is-expanded', expanded);
    workspace.classList.toggle('is-preview-expanded', expanded);
    expandButton?.setAttribute('aria-pressed', expanded ? 'true' : 'false');
    const icon = expandButton?.querySelector('.dashicons');
    if (icon) {
      icon.classList.toggle('dashicons-editor-expand', !expanded);
      icon.classList.toggle('dashicons-editor-contract', expanded);
    }
  };

  const setPreviewDevice = (device) => {
    if (!previewWrap) return;
    const mobile = device === 'mobile';
    previewWrap.classList.toggle('is-mobile', mobile);
    deviceButtons.forEach((button) => {
      const active = button.dataset.gemonioPreviewDevice === device;
      button.classList.toggle('is-active', active);
      button.setAttribute('aria-pressed', active ? 'true' : 'false');
    });
    savePreviewState();
  };

  collapseButton?.addEventListener('click', () => setPreviewCollapsed(true));
  reopenButton?.addEventListener('click', () => setPreviewCollapsed(false));
  expandButton?.addEventListener('click', () => setPreviewExpanded(!previewWrap?.classList.contains('is-expanded')));
  deviceButtons.forEach((button) => button.addEventListener('click', () => setPreviewDevice(button.dataset.gemonioPreviewDevice || 'desktop')));

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && previewWrap?.classList.contains('is-expanded')) setPreviewExpanded(false);
  });

  try {
    const stored = JSON.parse(localStorage.getItem(previewStateKey) || '{}');
    if (stored.device === 'mobile') setPreviewDevice('mobile');
    if (stored.collapsed === true) setPreviewCollapsed(true);
  } catch (_) {}

  form?.querySelectorAll('input, select').forEach((el) => {
    el.addEventListener('input', refreshPreview);
    el.addEventListener('change', refreshPreview);
  });
  refreshPreview();
})();
