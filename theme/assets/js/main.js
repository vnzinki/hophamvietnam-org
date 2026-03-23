/**
 * Họ Phạm Việt Nam — Main JS
 */
document.addEventListener('DOMContentLoaded', () => {
    initSlider();
    initMobileMenu();
    initSearchOverlay();
    initBrokenImageHandler();
});

/* ─── Hero Slider ─────────────────────────────────────────── */
function initSlider() {
    const slides = document.querySelectorAll('.slide');
    const dots   = document.querySelectorAll('.dot');
    if (!slides.length) return;

    let current  = 0;
    let interval = null;

    function goTo(index) {
        slides[current].classList.remove('active');
        dots[current]?.classList.remove('active');
        current = (index + slides.length) % slides.length;
        slides[current].classList.add('active');
        dots[current]?.classList.add('active');
    }

    function next() { goTo(current + 1); }

    function startAuto() {
        stopAuto();
        interval = setInterval(next, 5000);
    }

    function stopAuto() {
        if (interval) clearInterval(interval);
    }

    dots.forEach(dot => {
        dot.addEventListener('click', () => {
            goTo(parseInt(dot.dataset.slide, 10));
            startAuto();
        });
    });

    // Swipe support
    const slider = document.querySelector('.hero-slider');
    if (slider) {
        let touchStartX = 0;
        slider.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
            stopAuto();
        }, { passive: true });

        slider.addEventListener('touchend', (e) => {
            const diff = e.changedTouches[0].screenX - touchStartX;
            if (Math.abs(diff) > 50) {
                diff > 0 ? goTo(current - 1) : next();
            }
            startAuto();
        }, { passive: true });
    }

    startAuto();
}

/* ─── Mobile Menu Toggle ──────────────────────────────────── */
function initMobileMenu() {
    const toggle = document.querySelector('.menu-toggle');
    const menu   = document.querySelector('.nav-menu');
    if (!toggle || !menu) return;

    toggle.addEventListener('click', () => {
        menu.classList.toggle('is-open');
        toggle.classList.toggle('is-active');
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.main-nav')) {
            menu.classList.remove('is-open');
            toggle.classList.remove('is-active');
        }
    });
}

/* ─── Search Overlay ──────────────────────────────────────── */
function initSearchOverlay() {
    const toggle  = document.querySelector('.nav-search-toggle');
    const overlay = document.getElementById('searchOverlay');
    const close   = document.querySelector('.search-close');
    if (!toggle || !overlay) return;

    toggle.addEventListener('click', () => {
        overlay.classList.toggle('is-open');
        if (overlay.classList.contains('is-open')) {
            const input = overlay.querySelector('.search-field');
            if (input) input.focus();
        }
    });

    if (close) {
        close.addEventListener('click', () => overlay.classList.remove('is-open'));
    }

    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) overlay.classList.remove('is-open');
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') overlay.classList.remove('is-open');
    });
}

/* ─── Broken Image Placeholder ────────────────────────────── */
function initBrokenImageHandler() {
    const themeUri = document.querySelector('link[href*="hopham-vietnam"]')?.href?.replace(/\/assets\/.*/, '') || '';
    const logoUrl  = themeUri + '/assets/images/logo.png';

    function handleBrokenImage(img) {
        if (img.dataset.brokenHandled) return;
        img.dataset.brokenHandled = 'true';

        var w = img.width  || img.getAttribute('width')  || 300;
        var h = img.height || img.getAttribute('height') || 200;
        // Cap height to keep placeholder reasonable
        if (h > 400) h = Math.min(h, Math.max(w * 0.6, 250));

        var wrapper = document.createElement('div');
        wrapper.className = 'img-placeholder';
        wrapper.style.width     = w + 'px';
        wrapper.style.maxHeight = h + 'px';

        wrapper.innerHTML =
            '<img class="img-placeholder-logo" src="' + logoUrl + '" alt="Logo">' +
            '<span class="img-placeholder-text">Không tải được hình ảnh</span>';

        img.parentNode.replaceChild(wrapper, img);
    }

    // Handle already-broken images
    document.querySelectorAll('img').forEach(img => {
        if (img.complete && img.naturalWidth === 0 && img.src) {
            handleBrokenImage(img);
        }
    });

    // Handle future load errors
    document.addEventListener('error', (e) => {
        if (e.target.tagName === 'IMG') handleBrokenImage(e.target);
    }, true);
}
