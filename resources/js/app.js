import Alpine from 'alpinejs';
import Lenis from 'lenis';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import intlTelInput from 'intl-tel-input';
import 'intl-tel-input/styles';

gsap.registerPlugin(ScrollTrigger);

window.Alpine = Alpine;
Alpine.start();

const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

// Module-scoped so features outside this block (e.g. the scroll-to-top button)
// can reuse the same Lenis instance. Stays null under reduced motion.
let lenis = null;

if (!prefersReducedMotion) {
    // Smooth scrolling
    lenis = new Lenis({
        duration: 1.1,
        smoothWheel: true,
    });

    lenis.on('scroll', ScrollTrigger.update);
    gsap.ticker.add((time) => lenis.raf(time * 1000));
    gsap.ticker.lagSmoothing(0);

    // Smooth-scroll anchor links
    document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
        anchor.addEventListener('click', (e) => {
            const target = document.querySelector(anchor.getAttribute('href'));
            if (target) {
                e.preventDefault();
                lenis.scrollTo(target, { offset: -90 });
            }
        });
    });

    // Scroll-reveal animations: add .gsap-reveal (+ optional data-reveal="up|left|right|scale", data-reveal-delay)
    document.querySelectorAll('.gsap-reveal').forEach((el) => {
        const direction = el.dataset.reveal || 'up';
        const delay = parseFloat(el.dataset.revealDelay || '0');

        const from = { opacity: 0 };
        if (direction === 'up') from.y = 48;
        if (direction === 'left') from.x = -48;
        if (direction === 'right') from.x = 48;
        if (direction === 'scale') from.scale = 0.92;

        gsap.fromTo(el, from, {
            opacity: 1,
            x: 0,
            y: 0,
            scale: 1,
            duration: 0.9,
            delay,
            ease: 'power3.out',
            scrollTrigger: {
                trigger: el,
                start: 'top 88%',
                once: true,
            },
        });
    });

    // Staggered reveal for groups: parent gets .gsap-stagger, children animate in sequence
    document.querySelectorAll('.gsap-stagger').forEach((group) => {
        gsap.fromTo(
            group.children,
            { opacity: 0, y: 40 },
            {
                opacity: 1,
                y: 0,
                duration: 0.8,
                stagger: 0.12,
                ease: 'power3.out',
                scrollTrigger: {
                    trigger: group,
                    start: 'top 85%',
                    once: true,
                },
            },
        );
    });

    // Animated counters: <span data-counter="500">0</span>
    document.querySelectorAll('[data-counter]').forEach((el) => {
        const target = parseInt(el.dataset.counter, 10);
        const obj = { value: 0 };
        gsap.to(obj, {
            value: target,
            duration: 2,
            ease: 'power2.out',
            scrollTrigger: { trigger: el, start: 'top 90%', once: true },
            onUpdate: () => {
                el.textContent = Math.round(obj.value).toLocaleString();
            },
        });
    });

    // Hero entrance animation
    const heroItems = document.querySelectorAll('[data-hero-reveal]');
    if (heroItems.length) {
        gsap.fromTo(
            heroItems,
            { opacity: 0, y: 36 },
            { opacity: 1, y: 0, duration: 1, stagger: 0.15, ease: 'power3.out', delay: 0.15 },
        );
    }

    // Word-by-word heading reveal: <h2 data-word-reveal><span class="reveal-word">word</span>...</h2>
    document.querySelectorAll('[data-word-reveal]').forEach((heading) => {
        const words = heading.querySelectorAll('.reveal-word');
        gsap.fromTo(
            words,
            { opacity: 0, y: '100%' },
            {
                opacity: 1,
                y: '0%',
                duration: 0.7,
                stagger: 0.08,
                ease: 'power3.out',
                scrollTrigger: { trigger: heading, start: 'top 85%', once: true },
            },
        );
    });
} else {
    // Reduced motion: ensure everything is visible
    document.querySelectorAll('.gsap-reveal, [data-hero-reveal], .reveal-word').forEach((el) => {
        el.style.opacity = '1';
    });
    document.querySelectorAll('[data-counter]').forEach((el) => {
        el.textContent = parseInt(el.dataset.counter, 10).toLocaleString();
    });
}

// Navbar background on scroll
const navbar = document.getElementById('site-nav');
if (navbar) {
    const onScroll = () => {
        navbar.classList.toggle('nav-scrolled', window.scrollY > 24);
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
}

// Scroll-to-top button with a scroll-progress ring
const scrollTopBtn = document.getElementById('scroll-to-top');
if (scrollTopBtn) {
    const ring = document.getElementById('scroll-to-top-ring');
    const circumference = 2 * Math.PI * 23; // ring radius = 23 (see the SVG)
    if (ring) {
        ring.style.strokeDasharray = String(circumference);
        ring.style.strokeDashoffset = String(circumference);
    }

    let ticking = false;
    const update = () => {
        ticking = false;
        const scrollTop = window.scrollY || document.documentElement.scrollTop || 0;
        const scrollable = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        const progress = scrollable > 0 ? Math.min(Math.max(scrollTop / scrollable, 0), 1) : 0;
        if (ring) {
            ring.style.strokeDashoffset = String(circumference * (1 - progress));
        }
        scrollTopBtn.classList.toggle('is-visible', scrollTop > 300);
    };
    const requestUpdate = () => {
        if (!ticking) {
            ticking = true;
            requestAnimationFrame(update);
        }
    };

    // Prefer Lenis's scroll event when smooth scrolling is active; otherwise
    // (e.g. under prefers-reduced-motion, where Lenis is never created) fall
    // back to a passive scroll listener. rAF throttles the ring update.
    if (lenis) {
        lenis.on('scroll', requestUpdate);
    } else {
        window.addEventListener('scroll', requestUpdate, { passive: true });
    }
    window.addEventListener('resize', requestUpdate, { passive: true });

    scrollTopBtn.addEventListener('click', () => {
        if (lenis && !prefersReducedMotion) {
            lenis.scrollTo(0, { duration: 1.1 });
        } else {
            window.scrollTo({ top: 0, behavior: 'auto' });
        }
    });

    update(); // set initial ring + visibility state
}

// International phone inputs: flag + dial-code picker on [data-phone-intl].
// The visible field holds just the national number; on submit we swap in the
// full E.164 value so the server stores e.g. +447123456789. If the utils
// bundle hasn't loaded yet we leave whatever the visitor typed alone.
document.querySelectorAll('input[data-phone-intl]').forEach((input) => {
    const iti = intlTelInput(input, {
        initialCountry: 'gb',
        countryOrder: ['gb', 'ie'],
        separateDialCode: true,
        strictMode: true,
        loadUtils: () => import('intl-tel-input/utils'),
    });

    const form = input.closest('form');
    if (!form) return;

    form.addEventListener('submit', () => {
        const full = iti.getNumber();
        if (full) input.value = full;
    });
});
