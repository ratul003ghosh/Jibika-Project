document.addEventListener('DOMContentLoaded', () => {
    // ── Upazila Data for District Select ──
    const upazilaData = {
        'Dhaka': ['Savar', 'Dhamrai', 'Keraniganj', 'Nawabganj', 'Dohar'],
        'Chattogram': ['Hathazari', 'Sitakunda', 'Mirsharai', 'Patiya', 'Fatikchhari'],
        'Sylhet': ['Sylhet Sadar', 'Beanibazar', 'Golapganj', 'Companiganj', 'Gowainghat'],
        'Rajshahi': ['Paba', 'Godagari', 'Tanor', 'Bagmara', 'Mohanpur'],
        'Khulna': ['Batiaghata', 'Dacope', 'Dumuria', 'Koyra', 'Paikgachha'],
        'Barishal': ['Agailjhara', 'Babuganj', 'Bakerganj', 'Banaripara', 'Gournadi'],
        'Rangpur': ['Badarganj', 'Gangachhara', 'Kaunia', 'Mithapukur', 'Pirgachha'],
        'Mymensingh': ['Bhaluka', 'Dhobaura', 'Fulbaria', 'Gaffargaon', 'Gauripur'],
        'Comilla': ['Barura', 'Brahmanpara', 'Burichang', 'Chandina', 'Chauddagram'],
        'Gazipur': ['Gazipur Sadar', 'Kaliakair', 'Kaliganj', 'Kapasia', 'Sreepur'],
        'Narayanganj': ['Araihazar', 'Bandar', 'Narayanganj Sadar', 'Rupganj', 'Sonargaon']
    };

    const districtSelect = document.getElementById('districtSelect');
    if(districtSelect) {
        districtSelect.addEventListener('change', function() {
            const upazilaList = document.getElementById('upazilaList');
            upazilaList.innerHTML = '';
            const selectedDistrict = this.value;
            if (upazilaData[selectedDistrict]) {
                upazilaData[selectedDistrict].forEach(upazila => {
                    const option = document.createElement('option');
                    option.value = upazila;
                    upazilaList.appendChild(option);
                });
            }
        });
    }

    // --- Hero Background Slider (3D Prism Transition) ---
    const slides = document.querySelectorAll('.hero-slider-bg');
    let currentSlide = 0;
    if(slides.length > 0) {
        setInterval(() => {
            // Remove classes from all slides
            slides.forEach(slide => {
                slide.classList.remove('active', 'prev');
            });
            
            // Mark the outgoing slide as 'prev'
            slides[currentSlide].classList.add('prev');
            
            // Select the next slide
            currentSlide = (currentSlide + 1) % slides.length;
            
            // Mark the incoming slide as 'active'
            slides[currentSlide].classList.add('active');
        }, 5500); // 5.5 seconds delay
    }

    // --- Testimonial 3D Stack Slider ---
    const stackCards = document.querySelectorAll('.testimonial-card-3d');
    const stackDots = document.querySelectorAll('.stack-dot');
    const stackPrevBtn = document.querySelector('.stack-nav-btn.prev');
    const stackNextBtn = document.querySelector('.stack-nav-btn.next');
    let activeStackIndex = 0;

    function updateStackSlider() {
        stackCards.forEach((card, idx) => {
            card.classList.remove('active', 'prev-card', 'next-card');
            
            if (idx === activeStackIndex) {
                card.classList.add('active');
            } else if (idx === (activeStackIndex - 1 + stackCards.length) % stackCards.length) {
                card.classList.add('prev-card');
            } else if (idx === (activeStackIndex + 1) % stackCards.length) {
                card.classList.add('next-card');
            }
        });

        stackDots.forEach((dot, idx) => {
            dot.classList.toggle('active', idx === activeStackIndex);
        });
    }

    let stackInterval;
    
    function startStackTimer() {
        clearInterval(stackInterval);
        stackInterval = setInterval(() => {
            activeStackIndex = (activeStackIndex + 1) % stackCards.length;
            updateStackSlider();
        }, 3500); // Snappy 3.5s delay
    }

    if (stackCards.length > 0) {
        // Initialize positions
        updateStackSlider();
        startStackTimer();

        stackNextBtn.addEventListener('click', () => {
            activeStackIndex = (activeStackIndex + 1) % stackCards.length;
            updateStackSlider();
            startStackTimer();
        });

        stackPrevBtn.addEventListener('click', () => {
            activeStackIndex = (activeStackIndex - 1 + stackCards.length) % stackCards.length;
            updateStackSlider();
            startStackTimer();
        });

        stackDots.forEach((dot, idx) => {
            dot.addEventListener('click', () => {
                activeStackIndex = idx;
                updateStackSlider();
                startStackTimer();
            });
        });
    }
});
