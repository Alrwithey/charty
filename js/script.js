document.addEventListener('DOMContentLoaded', function() {

    // --- Sticky Header ---
    const header = document.getElementById('main-header');
    if (header) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) { header.classList.add('scrolled'); } 
            else { header.classList.remove('scrolled'); }
        });
    }

    // --- Mobile Menu Toggle ---
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mainNav = document.getElementById('main-nav');
    if (mobileMenuBtn && mainNav) {
        mobileMenuBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            mainNav.classList.toggle('active');
        });
        // إغلاق القائمة عند الضغط في أي مكان خارجها
        document.addEventListener('click', function(e) {
            if (mainNav.classList.contains('active') && !mainNav.contains(e.target) && !mobileMenuBtn.contains(e.target)) {
                mainNav.classList.remove('active');
            }
        });
        // إغلاق القائمة عند الضغط على أحد روابطها
        mainNav.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', (e) => {
                // لا تغلق القائمة إذا كان الرابط هو رابط قسم داخلي في نفس الصفحة
                if (link.getAttribute('href').charAt(0) !== '#') {
                     if (mainNav.classList.contains('active')) {
                        mainNav.classList.remove('active');
                     }
                }
            });
        });
    }

    // --- About Section Tabs ---
    const tabContainer = document.querySelector('.tabs-nav');
    if (tabContainer) {
        tabContainer.addEventListener('click', (e) => {
            const clickedButton = e.target.closest('.tab-btn');
            if (clickedButton) {
                const targetTabId = clickedButton.dataset.tab;
                const targetPane = document.getElementById(targetTabId);
                if (targetPane) {
                    tabContainer.querySelector('.tab-btn.active').classList.remove('active');
                    document.querySelector('.tabs-content .tab-pane.active').classList.remove('active');
                    clickedButton.classList.add('active');
                    targetPane.classList.add('active');
                }
            }
        });
    }
    
    // --- Partners Slider Pause on Hover ---
    const partnersTrack = document.querySelector('.partners-track');
    if (partnersTrack) {
        partnersTrack.addEventListener('mouseenter', () => { partnersTrack.style.animationPlayState = 'paused'; });
        partnersTrack.addEventListener('mouseleave', () => { partnersTrack.style.animationPlayState = 'running'; });
    }

    // --- Animated Statistics (SVG Version) ---
    const statItems = document.querySelectorAll('.stat-item-new');
    const statObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const statItem = entry.target;
                statItem.classList.add('is-animated');
                
                const counter = statItem.querySelector('.counter');
                const target = +counter.getAttribute('data-target');
                let count = 0;
                const speed = 100;
                const inc = Math.max(target / speed, 1);

                const updateCount = () => {
                    if (count < target) {
                        count += inc;
                        if (count > target) count = target;
                        counter.innerText = Math.ceil(count).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                        setTimeout(updateCount, 20);
                    } else {
                        counter.innerText = target.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                    }
                };
                updateCount();
                
                observer.unobserve(statItem);
            }
        });
    }, { threshold: 0.5 });
    statItems.forEach(item => statObserver.observe(item));

    // --- Footer Year ---
    const yearSpan = document.getElementById('year');
    if(yearSpan) {
        yearSpan.textContent = new Date().getFullYear();
    }
});