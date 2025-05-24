jQuery(document).ready(function($) {
    console.log('[medi& Homepage] JavaScript loaded');

    // =====================================
    // 地域アコーディオン機能
    // =====================================
    function initRegionAccordion() {
        $('.medi-region-accordion__header').on('click', function() {
            const $header = $(this);
            const $item = $header.closest('.medi-region-accordion__item');
            const $content = $item.find('.medi-region-accordion__content');
            const regionId = $header.data('region-id');
            
            console.log('[medi& Homepage] Region accordion clicked:', regionId);
            
            // 他のアコーディオンを閉じる
            $('.medi-region-accordion__item').not($item).each(function() {
                $(this).find('.medi-region-accordion__header').removeClass('is-active');
                $(this).find('.medi-region-accordion__content').removeClass('is-active').slideUp(300);
            });
            
            // クリックされたアコーディオンの開閉
            if ($header.hasClass('is-active')) {
                // 閉じる
                $header.removeClass('is-active');
                $content.removeClass('is-active').slideUp(300);
            } else {
                // 開く
                $header.addClass('is-active');
                $content.addClass('is-active').slideDown(300);
            }
        });
    }

    // =====================================
// 新着店舗スライダー
// =====================================
function initRecommendSlider() {
    const slider = document.getElementById('recommendSlider');
    const prevBtn = document.getElementById('recommendPrev');
    const nextBtn = document.getElementById('recommendNext');
    
    if (!slider || !prevBtn || !nextBtn) return;
    
    let currentIndex = 0;
    const slides = slider.querySelectorAll('.medi-recommend-slide');
    const totalSlides = slides.length;
    const slidesPerView = 4;
    const maxIndex = Math.max(0, totalSlides - slidesPerView);
    
    function updateSlider() {
        const translateX = -currentIndex * (100 / slidesPerView);
        slider.style.transform = `translateX(${translateX}%)`;
    }
    
    prevBtn.addEventListener('click', () => {
        currentIndex = Math.max(0, currentIndex - 1);
        updateSlider();
    });
    
    nextBtn.addEventListener('click', () => {
        currentIndex = Math.min(maxIndex, currentIndex + 1);
        updateSlider();
    });
    
    // 自動スライド
    setInterval(() => {
        currentIndex = (currentIndex + 1) > maxIndex ? 0 : currentIndex + 1;
        updateSlider();
    }, 5000);
}

    // =====================================
    // スクロールアニメーション
    // =====================================
    function initScrollAnimations() {
        // Intersection Observer for fade-in animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                }
            });
        }, observerOptions);

        // 各セクションにアニメーションクラスを追加
        $('.medi-recommend-section .medi-section-title').addClass('fade-in');
        $('.medi-recommend-section .medi-section-subtitle').addClass('fade-in');
        $('.medi-recommend-card').each(function(index) {
            $(this).addClass('fade-in').css('animation-delay', (index * 0.1) + 's');
        });

        $('.medi-region-section .medi-section-title').addClass('slide-in-left');
        $('.medi-region-accordion__item').each(function(index) {
            $(this).addClass('fade-in').css('animation-delay', (index * 0.1) + 's');
        });

        $('.medi-feeling-section .medi-section-title').addClass('fade-in');
        $('.medi-feeling-item').each(function(index) {
            $(this).addClass('fade-in').css('animation-delay', (index * 0.1) + 's');
        });

        $('.medi-situation-section .medi-section-title').addClass('slide-in-right');
        $('.medi-situation-item').each(function(index) {
            $(this).addClass('fade-in').css('animation-delay', (index * 0.1) + 's');
        });

        $('.medi-genre-section .medi-section-title').addClass('fade-in');
        $('.medi-genre-item').each(function(index) {
            $(this).addClass('fade-in').css('animation-delay', (index * 0.05) + 's');
        });

        // Observer に要素を登録
        document.querySelectorAll('.fade-in, .slide-in-left, .slide-in-right').forEach(el => {
            observer.observe(el);
        });
    }

    // =====================================
    // ヒーローセクション検索フォーム
    // =====================================
    function initHeroSearchForm() {
        $('.medi-hero-search-form').on('submit', function(e) {
            const searchTerm = $('.medi-hero-search-form__input').val().trim();
            
            if (!searchTerm) {
                e.preventDefault();
                $('.medi-hero-search-form__input').focus();
                
                // 入力欄をハイライト
                $('.medi-hero-search-form__wrapper').addClass('shake');
                setTimeout(() => {
                    $('.medi-hero-search-form__wrapper').removeClass('shake');
                }, 600);
                
                return false;
            }
            
            console.log('[medi& Homepage] Hero search submitted:', searchTerm);
        });

        // 検索入力時のプレースホルダー効果
        $('.medi-hero-search-form__input').on('focus', function() {
            $(this).closest('.medi-hero-search-form__wrapper').addClass('focused');
        }).on('blur', function() {
            $(this).closest('.medi-hero-search-form__wrapper').removeClass('focused');
        });
    }

    // =====================================
    // カード ホバーエフェクト強化
    // =====================================
    function initCardEffects() {
        // おすすめカードのホバーエフェクト
        $('.medi-recommend-card').on('mouseenter', function() {
            $(this).find('.medi-recommend-card__image img').css('transform', 'scale(1.1)');
        }).on('mouseleave', function() {
            $(this).find('.medi-recommend-card__image img').css('transform', 'scale(1)');
        });

        // シチュエーションカードのホバーエフェクト
        $('.medi-situation-item').on('mouseenter', function() {
            $(this).find('.medi-situation-item__image img').css('transform', 'scale(1.1)');
        }).on('mouseleave', function() {
            $(this).find('.medi-situation-item__image img').css('transform', 'scale(1)');
        });
    }

    // =====================================
    // スムーススクロール
    // =====================================
    function initSmoothScroll() {
        $('a[href*="#"]').not('[href="#"]').not('[href="#0"]').click(function(event) {
            if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
                var target = $(this.hash);
                target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
                
                if (target.length) {
                    event.preventDefault();
                    $('html, body').animate({
                        scrollTop: target.offset().top - 80
                    }, 800);
                }
            }
        });
    }

    // =====================================
    // パフォーマンス最適化: 画像の遅延読み込み
    // =====================================
    function initLazyLoading() {
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                });
            });

            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }
    }

    // =====================================
    // エラーハンドリング
    // =====================================
    function handleErrors() {
        window.addEventListener('error', function(e) {
            console.warn('[medi& Homepage] JavaScript error:', e.error);
        });
    }

   // =====================================
// 初期化
// =====================================
function initialize() {
    console.log('[medi& Homepage] Initializing...');
    
    try {
        initRegionAccordion();
        initScrollAnimations();
        initHeroSearchForm();
        initCardEffects();
        initSmoothScroll();
        initLazyLoading();
        initRecommendSlider(); // 新着スライダー追加
        handleErrors();
        
        console.log('[medi& Homepage] Initialization completed successfully');
    } catch (error) {
        console.error('[medi& Homepage] Initialization failed:', error);
    }
}

    // DOM準備完了後に初期化実行
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize);
    } else {
        initialize();
    }

    // =====================================
    // デバッグ情報
    // =====================================
    if (window.console && console.log) {
        console.log('[medi& Homepage] Debug info:');
        console.log('- Region accordion items:', $('.medi-region-accordion__item').length);
        console.log('- Recommend cards:', $('.medi-recommend-card').length);
        console.log('- Feeling items:', $('.medi-feeling-item').length);
        console.log('- Situation items:', $('.medi-situation-item').length);
        console.log('- Genre items:', $('.medi-genre-item').length);
    }

    // =====================================
    // 追加CSS（shake アニメーション）
    // =====================================
    $('<style>')
        .prop('type', 'text/css')
        .html(`
            .medi-hero-search-form__wrapper.shake {
                animation: shake 0.6s ease-in-out;
            }
            
            @keyframes shake {
                0%, 100% { transform: translateX(0); }
                10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
                20%, 40%, 60%, 80% { transform: translateX(5px); }
            }
            
            .medi-hero-search-form__wrapper.focused {
                box-shadow: 0 25px 80px rgba(232, 180, 232, 0.2);
                transform: translateY(-3px);
            }
        `)
        .appendTo('head');
});