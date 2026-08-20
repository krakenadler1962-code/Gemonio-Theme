# Gemonio Theme 0.6.5

A deliberately simple, performance-first WordPress one-page theme inspired by the strengths of SCRN, rebuilt from scratch.

## 0.5.0 — SCRN Classic titles

- Section titles now use the intended SCRN-inspired visual default: **centered, warm red-orange, with calm divider lines above and below**.
- New central Style controls for **section title colour, alignment and divider-line position**.
- Existing content headings keep their own colour; section-title colour is now a separate design token.
- Optional **Section subtitle** field for short claims beneath a section title, styled as a restrained italic accent.
- The Styles preview reflects the new title rhythm.
- All title behaviour remains global-first; individual sections only decide whether the title is shown and optionally supply a subtitle.

## 0.4.0 — Gemonio Styles

- New **Gemonio → Styles** area with six clear groups: Typography, Colours, Sections, Navigation, Buttons, Parallax / separators.
- SCRN-inspired defaults based on the confirmed SCRN 2.4 export.
- Automatic section titles on the front end.
- Per-section **Show section title** switch.
- Central CSS design tokens and per-group reset.
- No external font requests by the theme itself.

## Core idea

A section should mostly contain content. The theme should already know how a title, section, navigation, button and separator ought to look. The global Style area is for adapting that design language, not rebuilding every element by hand.


## 0.6.5

- Farbsteuerung wie in Forms: Klick auf den Farbfleck öffnet die Gemonio-Abstufungen direkt am Feld.
- Kein nativer WordPress-/Browser-Farbpicker.
- Auswahl einer Abstufung aktualisiert Wert und Live-Vorschau sofort.
- Farbpanel ist per Escape und Klick ausserhalb schliessbar und tastaturbedienbar.

## 0.6.2
- Forms-artige Farbabstufungen pro Farbwert (50–900).
- Kein nativer Browser-/WordPress-Farbpicker mehr; Auswahl erfolgt über Swatches oder Hexwert.
- Palette-first colour controls with SCRN Classic, Warm Editorial, Monochrome, Sage and Midnight presets.
- Explicit font-weight steps for core typography.
- Lightweight native image lightbox with global style controls.


## 0.6.2
- Adjustable one-page scrolling (duration + easing)
- Optional compact header and back-to-top control
- Separate active navigation colour
- Lightbox previous/next navigation
- Accent colour now used for focus and inline-link interactions


## 0.6.5
- Navigation branding: site title or media-library logo, with desktop/mobile height.
- Additional CSS under Gemonio → Styles → Erweitert, stored with WordPress custom CSS.
- Fixed live-preview font-weight override.
- Optional local WOFF2 font URLs for text and display fonts; no external Google request.
