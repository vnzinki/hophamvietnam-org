/**
 * Họ Phạm Việt Nam — Main JS
 */
document.addEventListener('DOMContentLoaded', () => {
    initSlider();
    initMobileMenu();
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

    // Close menu when clicking outside
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.main-nav')) {
            menu.classList.remove('is-open');
            toggle.classList.remove('is-active');
        }
    });
}
