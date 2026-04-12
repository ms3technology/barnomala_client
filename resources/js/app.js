import './bootstrap';

// Scroll Reveal Observer
const observerOptions = {
    threshold: 0.15,
};

const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
        if (entry.isIntersecting) {
            entry.target.classList.add('active');
            // Stop observing after reveal
            revealObserver.unobserve(entry.target);
        }
    });
}, observerOptions);

document.addEventListener('DOMContentLoaded', () => {
    const reveals = document.querySelectorAll('.reveal, .reveal-left, .reveal-right');
    reveals.forEach((el) => revealObserver.observe(el));

    // Counter Animation
    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const target = entry.target;
                const countTo = parseInt(target.getAttribute('data-count'));
                let currentCount = 0;
                const duration = 2000; // 2 seconds
                const stepTime = Math.abs(Math.floor(duration / countTo));
                
                const timer = setInterval(() => {
                    currentCount += Math.ceil(countTo / 60); // Jump in steps for smoothness
                    if (currentCount >= countTo) {
                        target.innerText = countTo;
                        clearInterval(timer);
                    } else {
                        target.innerText = currentCount;
                    }
                }, 16); // ~60fps

                counterObserver.unobserve(target);
            }
        });
    }, { threshold: 0.5 });

    document.querySelectorAll('.counter-up').forEach(counter => {
        counterObserver.observe(counter);
    });
});
