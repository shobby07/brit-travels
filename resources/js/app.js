import Alpine from 'alpinejs';
import Lenis from 'lenis';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

window.Alpine = Alpine;
Alpine.start();

const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

if (!prefersReducedMotion) {
    // Smooth scrolling
    const lenis = new Lenis({
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
} else {
    // Reduced motion: ensure everything is visible
    document.querySelectorAll('.gsap-reveal, [data-hero-reveal]').forEach((el) => {
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
