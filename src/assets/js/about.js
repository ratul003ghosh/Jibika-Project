document.addEventListener('DOMContentLoaded', function() {
    // Scroll Reveal Animation
    const reveals = document.querySelectorAll('.reveal');
    const revealOptions = { threshold: 0.1, rootMargin: "0px 0px -50px 0px" };
    
    const revealOnScroll = new IntersectionObserver(function(entries, observer) {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            entry.target.classList.add('active');
            observer.unobserve(entry.target);
        });
    }, revealOptions);

    reveals.forEach(reveal => {
        revealOnScroll.observe(reveal);
    });

    // Animated Counters with Bengali Support
    const counters = document.querySelectorAll('.stat-num');
    const isBn = document.documentElement.lang === "bn";
    const eng = ['0','1','2','3','4','5','6','7','8','9'];
    const bng = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];

    const toBnStr = (numStr) => {
        if(!isBn) return numStr;
        let res = "";
        for(let i=0; i<numStr.length; i++) {
            let ch = numStr[i];
            let idx = eng.indexOf(ch);
            res += (idx !== -1) ? bng[idx] : ch;
        }
        return res;
    };

    const counterObserver = new IntersectionObserver(function(entries, observer) {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            
            const target = +entry.target.getAttribute('data-target');
            let count = 0;
            const duration = 2000;
            const increment = target / (duration / 16);

            const updateCounter = () => {
                count += increment;
                if(count < target) {
                    let formatted = Math.ceil(count).toLocaleString();
                    entry.target.innerText = toBnStr(formatted) + "+";
                    requestAnimationFrame(updateCounter);
                } else {
                    let formatted = target.toLocaleString();
                    entry.target.innerText = toBnStr(formatted) + "+";
                }
            };
            updateCounter();
            observer.unobserve(entry.target);
        });
    }, { threshold: 0.5 });

    counters.forEach(counter => {
        counterObserver.observe(counter);
    });
});
