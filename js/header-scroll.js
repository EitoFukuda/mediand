document.addEventListener('DOMContentLoaded', function() {
    const header = document.querySelector('.site-header-custom');
    let lastScrollY = window.scrollY;
    
    function handleHeaderScroll() {
        const currentScrollY = window.scrollY;
        
        if (currentScrollY > 100) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
        
        lastScrollY = currentScrollY;
    }
    
    // スクロールイベント（パフォーマンス最適化）
    let ticking = false;
    window.addEventListener('scroll', function() {
        if (!ticking) {
            requestAnimationFrame(function() {
                handleHeaderScroll();
                ticking = false;
            });
            ticking = true;
        }
    });
    
    // 初期チェック
    handleHeaderScroll();
});