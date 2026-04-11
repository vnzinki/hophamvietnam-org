/**
 * Tests for theme/assets/js/main.js
 *
 * @jest-environment jsdom
 */

'use strict';

const { initSlider, initMobileMenu, initSearchOverlay, initBrokenImageHandler } =
    require('../../theme/assets/js/main.js');

// ─── Helpers ────────────────────────────────────────────────

/** Build a minimal DOM for the hero slider. */
function buildSliderDOM(slideCount = 3) {
    document.body.innerHTML = '';

    const sliderEl = document.createElement('div');
    sliderEl.className = 'hero-slider';

    for (let i = 0; i < slideCount; i++) {
        const slide = document.createElement('div');
        slide.className = 'slide' + (i === 0 ? ' active' : '');
        sliderEl.appendChild(slide);

        const dot = document.createElement('button');
        dot.className = 'dot' + (i === 0 ? ' active' : '');
        dot.dataset.slide = String(i);
        sliderEl.appendChild(dot);
    }

    document.body.appendChild(sliderEl);
    return sliderEl;
}

// ─── initSlider ─────────────────────────────────────────────

describe('initSlider', () => {
    beforeEach(() => jest.useFakeTimers());
    afterEach(() => {
        jest.useRealTimers();
        document.body.innerHTML = '';
    });

    test('does nothing when no .slide elements exist', () => {
        document.body.innerHTML = '<div></div>';
        expect(() => initSlider()).not.toThrow();
    });

    test('first slide starts active', () => {
        buildSliderDOM(3);
        initSlider();
        const slides = document.querySelectorAll('.slide');
        expect(slides[0].classList.contains('active')).toBe(true);
    });

    test('auto-advances to next slide after 5 s', () => {
        buildSliderDOM(3);
        initSlider();
        jest.advanceTimersByTime(5000);
        const slides = document.querySelectorAll('.slide');
        expect(slides[0].classList.contains('active')).toBe(false);
        expect(slides[1].classList.contains('active')).toBe(true);
    });

    test('wraps around to first slide after last', () => {
        buildSliderDOM(2);
        initSlider();
        jest.advanceTimersByTime(5000); // → slide 1
        jest.advanceTimersByTime(5000); // → slide 0 (wrap)
        const slides = document.querySelectorAll('.slide');
        expect(slides[0].classList.contains('active')).toBe(true);
        expect(slides[1].classList.contains('active')).toBe(false);
    });

    test('dot click navigates to the correct slide', () => {
        buildSliderDOM(3);
        initSlider();
        const dots   = document.querySelectorAll('.dot');
        const slides = document.querySelectorAll('.slide');

        dots[2].click();
        expect(slides[2].classList.contains('active')).toBe(true);
        expect(slides[0].classList.contains('active')).toBe(false);
        expect(dots[2].classList.contains('active')).toBe(true);
        expect(dots[0].classList.contains('active')).toBe(false);
    });

    test('dot click also updates active dot class', () => {
        buildSliderDOM(3);
        initSlider();
        const dots = document.querySelectorAll('.dot');

        dots[1].click();
        expect(dots[0].classList.contains('active')).toBe(false);
        expect(dots[1].classList.contains('active')).toBe(true);
    });

    test('swipe left advances the slider', () => {
        buildSliderDOM(3);
        initSlider();
        const slider = document.querySelector('.hero-slider');
        const slides = document.querySelectorAll('.slide');

        slider.dispatchEvent(new TouchEvent('touchstart', {
            changedTouches: [{ screenX: 200 }],
        }));
        slider.dispatchEvent(new TouchEvent('touchend', {
            changedTouches: [{ screenX: 80 }], // diff = -120 → advance
        }));

        expect(slides[1].classList.contains('active')).toBe(true);
    });

    test('swipe right goes to previous slide', () => {
        buildSliderDOM(3);
        initSlider();
        const slider = document.querySelector('.hero-slider');
        const slides = document.querySelectorAll('.slide');

        // First go to slide 1 via dot click so we can swipe back
        document.querySelectorAll('.dot')[1].click();
        expect(slides[1].classList.contains('active')).toBe(true);

        slider.dispatchEvent(new TouchEvent('touchstart', {
            changedTouches: [{ screenX: 100 }],
        }));
        slider.dispatchEvent(new TouchEvent('touchend', {
            changedTouches: [{ screenX: 220 }], // diff = +120 → previous
        }));

        expect(slides[0].classList.contains('active')).toBe(true);
    });

    test('tiny swipe (< 50 px) does not change slide', () => {
        buildSliderDOM(3);
        initSlider();
        const slider = document.querySelector('.hero-slider');
        const slides = document.querySelectorAll('.slide');

        slider.dispatchEvent(new TouchEvent('touchstart', {
            changedTouches: [{ screenX: 100 }],
        }));
        slider.dispatchEvent(new TouchEvent('touchend', {
            changedTouches: [{ screenX: 130 }], // diff = 30, below threshold
        }));

        expect(slides[0].classList.contains('active')).toBe(true);
    });

    test('works correctly with a single slide', () => {
        buildSliderDOM(1);
        initSlider();
        jest.advanceTimersByTime(5000);
        const slides = document.querySelectorAll('.slide');
        // Single slide stays active after wrap-around
        expect(slides[0].classList.contains('active')).toBe(true);
    });
});

// ─── initMobileMenu ─────────────────────────────────────────

describe('initMobileMenu', () => {
    function buildMenuDOM() {
        document.body.innerHTML = `
            <nav class="main-nav">
                <button class="menu-toggle"></button>
                <ul class="nav-menu"></ul>
            </nav>
            <div id="outside"></div>`;
    }

    afterEach(() => { document.body.innerHTML = ''; });

    test('does nothing when toggle or menu is absent', () => {
        document.body.innerHTML = '<div></div>';
        expect(() => initMobileMenu()).not.toThrow();
    });

    test('toggle click opens the menu', () => {
        buildMenuDOM();
        initMobileMenu();
        const toggle = document.querySelector('.menu-toggle');
        const menu   = document.querySelector('.nav-menu');

        toggle.click();
        expect(menu.classList.contains('is-open')).toBe(true);
        expect(toggle.classList.contains('is-active')).toBe(true);
    });

    test('second toggle click closes the menu', () => {
        buildMenuDOM();
        initMobileMenu();
        const toggle = document.querySelector('.menu-toggle');
        const menu   = document.querySelector('.nav-menu');

        toggle.click(); // open
        toggle.click(); // close
        expect(menu.classList.contains('is-open')).toBe(false);
        expect(toggle.classList.contains('is-active')).toBe(false);
    });

    test('clicking outside .main-nav closes the menu', () => {
        buildMenuDOM();
        initMobileMenu();
        const toggle  = document.querySelector('.menu-toggle');
        const menu    = document.querySelector('.nav-menu');
        const outside = document.getElementById('outside');

        toggle.click(); // open
        outside.click(); // outside nav
        expect(menu.classList.contains('is-open')).toBe(false);
        expect(toggle.classList.contains('is-active')).toBe(false);
    });

    test('clicking inside .main-nav does NOT close the menu', () => {
        buildMenuDOM();
        initMobileMenu();
        const toggle = document.querySelector('.menu-toggle');
        const menu   = document.querySelector('.nav-menu');
        const nav    = document.querySelector('.main-nav');

        toggle.click(); // open
        nav.click();    // inside nav
        expect(menu.classList.contains('is-open')).toBe(true);
    });
});

// ─── initSearchOverlay ──────────────────────────────────────

describe('initSearchOverlay', () => {
    function buildSearchDOM() {
        document.body.innerHTML = `
            <button class="nav-search-toggle"></button>
            <div id="searchOverlay">
                <button class="search-close"></button>
                <input class="search-field">
            </div>`;
    }

    afterEach(() => { document.body.innerHTML = ''; });

    test('does nothing when toggle or overlay is absent', () => {
        document.body.innerHTML = '<div></div>';
        expect(() => initSearchOverlay()).not.toThrow();
    });

    test('toggle click opens the overlay', () => {
        buildSearchDOM();
        initSearchOverlay();
        document.querySelector('.nav-search-toggle').click();
        expect(document.getElementById('searchOverlay').classList.contains('is-open')).toBe(true);
    });

    test('second toggle click closes the overlay', () => {
        buildSearchDOM();
        initSearchOverlay();
        const toggle  = document.querySelector('.nav-search-toggle');
        const overlay = document.getElementById('searchOverlay');

        toggle.click(); // open
        toggle.click(); // close
        expect(overlay.classList.contains('is-open')).toBe(false);
    });

    test('opening the overlay focuses the search input', () => {
        buildSearchDOM();
        initSearchOverlay();
        const input = document.querySelector('.search-field');
        const focusSpy = jest.spyOn(input, 'focus');

        document.querySelector('.nav-search-toggle').click();
        expect(focusSpy).toHaveBeenCalled();
    });

    test('close button closes the overlay', () => {
        buildSearchDOM();
        initSearchOverlay();
        const overlay = document.getElementById('searchOverlay');
        overlay.classList.add('is-open');

        document.querySelector('.search-close').click();
        expect(overlay.classList.contains('is-open')).toBe(false);
    });

    test('clicking the overlay backdrop closes it', () => {
        buildSearchDOM();
        initSearchOverlay();
        const overlay = document.getElementById('searchOverlay');
        overlay.classList.add('is-open');

        // Simulate click on the overlay element itself (not a child)
        const evt = new MouseEvent('click', { bubbles: true });
        Object.defineProperty(evt, 'target', { value: overlay, writable: false });
        overlay.dispatchEvent(evt);

        expect(overlay.classList.contains('is-open')).toBe(false);
    });

    test('pressing Escape closes the overlay', () => {
        buildSearchDOM();
        initSearchOverlay();
        const overlay = document.getElementById('searchOverlay');
        overlay.classList.add('is-open');

        document.dispatchEvent(new KeyboardEvent('keydown', { key: 'Escape', bubbles: true }));
        expect(overlay.classList.contains('is-open')).toBe(false);
    });

    test('pressing a non-Escape key does not close the overlay', () => {
        buildSearchDOM();
        initSearchOverlay();
        const overlay = document.getElementById('searchOverlay');
        overlay.classList.add('is-open');

        document.dispatchEvent(new KeyboardEvent('keydown', { key: 'Enter', bubbles: true }));
        expect(overlay.classList.contains('is-open')).toBe(true);
    });
});

// ─── initBrokenImageHandler ─────────────────────────────────

describe('initBrokenImageHandler', () => {
    function buildImgDOM(src = 'broken.jpg', width = 300, height = 200) {
        document.body.innerHTML = `<img src="${src}" width="${width}" height="${height}">`;
        return document.querySelector('img');
    }

    afterEach(() => { document.body.innerHTML = ''; });

    test('does not throw when there are no images', () => {
        document.body.innerHTML = '<div></div>';
        expect(() => initBrokenImageHandler()).not.toThrow();
    });

    test('replaces already-broken images synchronously', () => {
        const img = buildImgDOM('missing.jpg', 300, 200);
        // Simulate a broken image: complete=true, naturalWidth=0, src set
        Object.defineProperty(img, 'complete', { value: true, writable: false });
        Object.defineProperty(img, 'naturalWidth', { value: 0, writable: false });

        initBrokenImageHandler();

        // The original broken <img> should be gone; replaced by a placeholder div
        expect(document.querySelector('img[src="missing.jpg"]')).toBeNull();
        const placeholder = document.querySelector('.img-placeholder');
        expect(placeholder).not.toBeNull();
        expect(placeholder.querySelector('.img-placeholder-text').textContent)
            .toBe('Không tải được hình ảnh');
    });

    test('broken image placeholder has correct width style', () => {
        const img = buildImgDOM('missing.jpg', 400, 200);
        Object.defineProperty(img, 'complete', { value: true, writable: false });
        Object.defineProperty(img, 'naturalWidth', { value: 0, writable: false });

        initBrokenImageHandler();

        const placeholder = document.querySelector('.img-placeholder');
        expect(placeholder.style.width).toBe('400px');
    });

    test('does not replace a successfully loaded image', () => {
        const img = buildImgDOM('good.jpg', 300, 200);
        Object.defineProperty(img, 'complete', { value: true, writable: false });
        Object.defineProperty(img, 'naturalWidth', { value: 100, writable: false });

        initBrokenImageHandler();

        expect(document.querySelector('img')).not.toBeNull();
        expect(document.querySelector('.img-placeholder')).toBeNull();
    });

    test('handles future error events on <img> tags', () => {
        document.body.innerHTML = '<img src="will-fail.jpg" width="200" height="150">';
        const img = document.querySelector('img');

        initBrokenImageHandler();

        // Fire an error event as if the image failed to load after init
        const evt = new Event('error', { bubbles: true });
        Object.defineProperty(evt, 'target', { value: img, writable: false });
        // Ensure tagName is 'IMG' for the handler
        document.dispatchEvent(evt);

        // The handler checks e.target.tagName === 'IMG'; since it is, replace it
        const placeholder = document.querySelector('.img-placeholder');
        expect(placeholder).not.toBeNull();
    });

    test('caps very tall placeholder heights', () => {
        const img = buildImgDOM('tall.jpg', 300, 1000);
        Object.defineProperty(img, 'complete', { value: true, writable: false });
        Object.defineProperty(img, 'naturalWidth', { value: 0, writable: false });

        initBrokenImageHandler();

        const placeholder = document.querySelector('.img-placeholder');
        // Height > 400 should be capped
        const maxH = parseInt(placeholder.style.maxHeight);
        expect(maxH).toBeLessThanOrEqual(400);
    });

    test('does not process the same broken image twice', () => {
        const img = buildImgDOM('missing.jpg', 300, 200);
        Object.defineProperty(img, 'complete', { value: true, writable: false });
        Object.defineProperty(img, 'naturalWidth', { value: 0, writable: false });

        initBrokenImageHandler();
        // Calling again should not throw even though the img is gone
        expect(() => initBrokenImageHandler()).not.toThrow();
    });

    test('ignores non-IMG error events', () => {
        document.body.innerHTML = '<img src="ok.jpg" width="100" height="100"><script src="fail.js"></script>';
        const img = document.querySelector('img');
        Object.defineProperty(img, 'complete', { value: true, writable: false });
        Object.defineProperty(img, 'naturalWidth', { value: 50, writable: false });

        initBrokenImageHandler();

        // Fire error from a script element — should not create placeholder
        const scriptEl = document.querySelector('script');
        const evt = new Event('error', { bubbles: true });
        Object.defineProperty(evt, 'target', { value: scriptEl, writable: false });
        document.dispatchEvent(evt);

        expect(document.querySelector('.img-placeholder')).toBeNull();
    });
});
