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
// 新着店舗スライダー（強化版）
// =====================================
function initRecommendSlider() {
    const $slider = $('#recommendSlider');
    const $prevBtn = $('#recommendPrev');
    const $nextBtn = $('#recommendNext');
    
    if (!$slider.length || !$prevBtn.length || !$nextBtn.length) {
        console.log('[medi& Homepage] Slider elements not found');
        return;
    }
    
    let currentIndex = 0;
    const $slides = $slider.find('.medi-recommend-slide');
    const totalSlides = $slides.length;
    let slidesPerView = 4;
    let autoSlideInterval;
    let isTransitioning = false;
    
    // レスポンシブ対応
    function updateSlidesPerView() {
        const width = $(window).width();
        if (width <= 768) {
            slidesPerView = 1;
        } else if (width <= 992) {
            slidesPerView = 2;
        } else if (width <= 1200) {
            slidesPerView = 3;
        } else {
            slidesPerView = 4;
        }
    }
    
    function updateSlider(animate = true) {
        if (isTransitioning) return;
        
        const slideWidth = 100 / slidesPerView;
        const translateX = -(currentIndex * slideWidth);
        
        if (animate) {
            isTransitioning = true;
            $slider.css('transform', `translateX(${translateX}%)`);
            setTimeout(() => {
                isTransitioning = false;
            }, 600);
        } else {
            $slider.css({
                'transition': 'none',
                'transform': `translateX(${translateX}%)`
            });
            setTimeout(() => {
                $slider.css('transition', '');
            }, 50);
        }
        
        
        // ボタンの有効/無効
        $prevBtn.prop('disabled', currentIndex === 0);
        $nextBtn.prop('disabled', currentIndex >= totalSlides - slidesPerView);
        
        console.log('[medi& Homepage] Slider updated:', currentIndex, 'of', totalSlides);
    }

    // 87行目付近の initRecommendSlider 関数内に追加
function startAutoSlide() {
    stopAutoSlide();
    if (totalSlides > slidesPerView) {
        // モバイルの場合は3秒、それ以外は5秒
        const interval = $(window).width() <= 768 ? 3000 : 5000;
        autoSlideInterval = setInterval(() => {
            nextSlide();
        }, interval);
    }
}
    
    function nextSlide() {
        if (isTransitioning) return;
        if (currentIndex < totalSlides - slidesPerView) {
            currentIndex++;
        } else {
            currentIndex = 0; // 最後まで行ったら最初に戻る
        }
        updateSlider();
    }
    
    function prevSlide() {
        if (isTransitioning) return;
        if (currentIndex > 0) {
            currentIndex--;
        } else {
            currentIndex = Math.max(0, totalSlides - slidesPerView); // 最初なら最後へ
        }
        updateSlider();
    }
    
    function startAutoSlide() {
        stopAutoSlide();
        if (totalSlides > slidesPerView) {
            autoSlideInterval = setInterval(() => {
                nextSlide();
            }, 5000); // 5秒ごと
        }
    }
    
    function stopAutoSlide() {
        if (autoSlideInterval) {
            clearInterval(autoSlideInterval);
        }
    }
    
    // イベントリスナー
    $prevBtn.on('click', (e) => {
        e.preventDefault();
        stopAutoSlide();
        prevSlide();
        setTimeout(startAutoSlide, 3000); // 3秒後に自動再生再開
    });
    
    $nextBtn.on('click', (e) => {
        e.preventDefault();
        stopAutoSlide();
        nextSlide();
        setTimeout(startAutoSlide, 3000); // 3秒後に自動再生再開
    });
    
    // ホバー時は自動スライドを停止
    $slider.closest('.medi-recommend-slider-wrapper').on('mouseenter', stopAutoSlide);
    $slider.closest('.medi-recommend-slider-wrapper').on('mouseleave', startAutoSlide);
    
    // タッチ操作対応
    let touchStartX = 0;
    let touchEndX = 0;
    let touchStartY = 0;
    let touchEndY = 0;
    
    $slider.on('touchstart', (e) => {
        touchStartX = e.changedTouches[0].screenX;
        touchStartY = e.changedTouches[0].screenY;
        stopAutoSlide();
    });
    
    $slider.on('touchend', (e) => {
        touchEndX = e.changedTouches[0].screenX;
        touchEndY = e.changedTouches[0].screenY;
        handleSwipe();
        setTimeout(startAutoSlide, 3000);
    });
    
    function handleSwipe() {
        const swipeThreshold = 50;
        const diffX = touchStartX - touchEndX;
        const diffY = Math.abs(touchStartY - touchEndY);
        
        // 縦スワイプの場合はスライドしない
        if (diffY > 100) return;
        
        if (Math.abs(diffX) > swipeThreshold) {
            if (diffX > 0) {
                nextSlide(); // 左スワイプ
            } else {
                prevSlide(); // 右スワイプ
            }
        }
    }
    
    // ウィンドウリサイズ対応
    let resizeTimeout;
    $(window).on('resize', () => {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            updateSlidesPerView();
            currentIndex = Math.min(currentIndex, Math.max(0, totalSlides - slidesPerView));
            updateSlider(false);
        }, 250);
    });
    
    // 初期化
    updateSlidesPerView();
    updateSlider(false);
    startAutoSlide();
    
    console.log('[medi& Homepage] Recommend slider initialized with', totalSlides, 'slides');
}

function initMobileHeroBackground() {
    const $heroSection = $('.medi-hero-section--fullscreen');
    const mobileBg = $heroSection.data('mobile-bg');
    
    if (mobileBg && $(window).width() <= 768) {
        $heroSection.css('--mobile-bg', `url('${mobileBg}')`);
    }
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

    initMobileHeroBackground();
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

// 階層セレクト機能を追加
function initHierarchicalSelect() {
    $('#prefecture-select').on('change', function() {
        const regionId = $(this).find(':selected').data('region-id');
        const $subSelect = $('#prefecture-sub-select');
        
        if (regionId) {
            // Ajax で子要素を取得
            $.post(ajaxurl, {
                action: 'get_prefecture_children',
                region_id: regionId
            }, function(response) {
                if (response.success) {
                    $subSelect.html('<option value="">都道府県を選択</option>');
                    $.each(response.data, function(i, item) {
                        $subSelect.append(`<option value="${item.slug}">${item.name}</option>`);
                    });
                    $subSelect.show();
                    $(this).hide();
                }
            });
        }
    });
}

// initialize関数内に追加
initHierarchicalSelect();