/**
 * 背景装飾アニメーション
 * サイト全体の<main>要素に浮遊する丸の装飾を追加
 */

(function($) {
    'use strict';

    // 設定
    const config = {
        // 丸の数
        circleCount: {
            desktop: 25,  // 15から25に増加
            tablet: 18,   // 10から18に増加
            mobile: 12    // 6から12に増加
        },
        // 色のパレット（コンセプトカラー）
        colors: [
            'rgba(232, 180, 232, 0.4)',  // 薄い紫
            'rgba(199, 125, 199, 0.35)', // ミディアム紫
            'rgba(221, 160, 221, 0.45)', // ライト紫
            'rgba(155, 89, 182, 0.3)',   // ディープ紫
            'rgba(230, 126, 200, 0.4)',  // ピンク紫
            'rgba(186, 85, 211, 0.35)'   // バイオレット
        ],
        
        // サイズ範囲
        sizeRange: {
            min: 30,
            max: 300
        },
        // アニメーション速度範囲（秒）
        animationSpeed: {
            min: 12,
            max: 35
        },
        // Z-index
        zIndex: -1
    };

    class BackgroundDecorations {
        constructor() {
            this.circles = [];
            this.container = null;
            this.isInitialized = false;
            
            this.init();
        }

        init() {
            if (this.isInitialized) return;
            
            // DOMが準備できたら実行
            $(document).ready(() => {
                this.createContainer();
                this.createCircles();
                this.bindEvents();
                this.isInitialized = true;
                
                console.log('[Background Decorations] Initialized successfully');
            });
        }

        createContainer() {
            // メインコンテナを探す
            const $main = $('main, .main-content, #main, .homepage-wrapper, .content-area').first();
            
            if ($main.length === 0) {
                console.warn('[Background Decorations] Main container not found, using body');
                this.container = $('body');
            } else {
                this.container = $main;
            }

            // コンテナのスタイルを調整
            this.container.css({
                'position': 'relative',
                'overflow': 'hidden'
            });

            // 装飾用のコンテナを作成
            const $decorationContainer = $('<div class="bg-decorations-container"></div>');
            $decorationContainer.css({
                'position': 'absolute',
                'top': '0',
                'left': '0',
                'width': '100%',
                'height': '100%',
                'pointer-events': 'none',
                'z-index': config.zIndex,
                'overflow': 'hidden'
            });

            this.container.prepend($decorationContainer);
            this.decorationContainer = $decorationContainer;
        }

        getCircleCount() {
            const width = $(window).width();
            
            if (width <= 768) {
                return config.circleCount.mobile;
            } else if (width <= 1024) {
                return config.circleCount.tablet;
            } else {
                return config.circleCount.desktop;
            }
        }

        createCircles() {
            const circleCount = this.getCircleCount();
            
            // 既存の円を削除
            this.circles = [];
            this.decorationContainer.empty();

            for (let i = 0; i < circleCount; i++) {
                this.createCircle(i);
            }
        }

        createCircle(index) {
            const circle = {
                element: null,
                size: this.randomBetween(config.sizeRange.min, config.sizeRange.max),
                color: config.colors[Math.floor(Math.random() * config.colors.length)],
                x: Math.random() * 100,
                y: Math.random() * 100,
                duration: this.randomBetween(config.animationSpeed.min, config.animationSpeed.max),
                delay: Math.random() * 5 // 0-5秒の遅延
            };

            // 円要素を作成
            const $circle = $('<div class="bg-decoration-circle"></div>');
            
            $circle.css({
                'position': 'absolute',
                'width': circle.size + 'px',
                'height': circle.size + 'px',
                'background': circle.color,
                'border-radius': '50%',
                'left': circle.x + '%',
                'top': circle.y + '%',
                'transform': 'translate(-50%, -50%)',
                'animation': `float-${index} ${circle.duration}s ease-in-out infinite`,
                'animation-delay': circle.delay + 's',
                'will-change': 'transform'
            });

            // アニメーションキーフレームを動的に生成
            this.createFloatAnimation(index, circle);

            circle.element = $circle;
            this.circles.push(circle);
            this.decorationContainer.append($circle);
        }

        createFloatAnimation(index, circle) {
            const animationName = `float-${index}`;
            
            // ランダムな移動パスを生成
            const keyframes = this.generateFloatKeyframes();
            
            // スタイルシートに追加
            if (!$(`#bg-decoration-styles-${index}`).length) {
                const style = $(`<style id="bg-decoration-styles-${index}">
                    @keyframes ${animationName} {
                        ${keyframes}
                    }
                </style>`);
                $('head').append(style);
            }
        }

        generateFloatKeyframes() {
            const points = [
                { percent: 0, x: 0, y: 0, scale: 1 },
                { percent: 25, x: this.randomBetween(-30, 30), y: this.randomBetween(-20, 20), scale: this.randomBetween(0.8, 1.2) },
                { percent: 50, x: this.randomBetween(-20, 20), y: this.randomBetween(-30, 30), scale: this.randomBetween(0.9, 1.1) },
                { percent: 75, x: this.randomBetween(-25, 25), y: this.randomBetween(-15, 15), scale: this.randomBetween(0.85, 1.15) },
                { percent: 100, x: 0, y: 0, scale: 1 }
            ];

            return points.map(point => 
                `${point.percent}% {
                    transform: translate(calc(-50% + ${point.x}px), calc(-50% + ${point.y}px)) scale(${point.scale});
                }`
            ).join('\n');
        }

        randomBetween(min, max) {
            return Math.random() * (max - min) + min;
        }

        bindEvents() {
            // リサイズイベント
            let resizeTimer;
            $(window).on('resize', () => {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(() => {
                    this.handleResize();
                }, 250);
            });

            // 可視性変更時の処理
            $(document).on('visibilitychange', () => {
                this.handleVisibilityChange();
            });
        }

        handleResize() {
            const newCount = this.getCircleCount();
            
            if (newCount !== this.circles.length) {
                console.log('[Background Decorations] Recreating circles for new screen size');
                this.createCircles();
            }
        }

        handleVisibilityChange() {
            if (document.hidden) {
                // タブが非アクティブになったらアニメーションを一時停止
                this.decorationContainer.css('animation-play-state', 'paused');
            } else {
                // タブがアクティブになったらアニメーションを再開
                this.decorationContainer.css('animation-play-state', 'running');
            }
        }

        // パフォーマンス最適化：アニメーションの有効/無効切り替え
        toggleAnimations(enable = true) {
            const display = enable ? 'block' : 'none';
            this.decorationContainer.css('display', display);
            
            console.log(`[Background Decorations] Animations ${enable ? 'enabled' : 'disabled'}`);
        }

        // 動的に円を追加
        addCircle() {
            const index = this.circles.length;
            this.createCircle(index);
        }

        // 全ての円を削除
        removeAllCircles() {
            this.circles = [];
            this.decorationContainer.empty();
            
            // 動的に作成したスタイルも削除
            $('[id^="bg-decoration-styles-"]').remove();
        }

        // 色テーマを変更
        changeColorTheme(newColors) {
            if (Array.isArray(newColors) && newColors.length > 0) {
                config.colors = newColors;
                this.createCircles(); // 円を再作成
                console.log('[Background Decorations] Color theme updated');
            }
        }

        // パフォーマンス監視
        getPerformanceInfo() {
            return {
                circleCount: this.circles.length,
                containerSize: {
                    width: this.container.width(),
                    height: this.container.height()
                },
                isVisible: !document.hidden
            };
        }
    }

    // グローバルアクセス用
    window.BackgroundDecorations = BackgroundDecorations;

    // 自動初期化
    const backgroundDecorations = new BackgroundDecorations();

    // 開発者向けコンソールコマンド
    if (typeof window.console !== 'undefined') {
        console.log('%c[Background Decorations] Available commands:', 'color: #C77DC7; font-weight: bold;');
        console.log('- backgroundDecorations.toggleAnimations(false) // アニメーションを無効化');
        console.log('- backgroundDecorations.addCircle() // 円を追加');
        console.log('- backgroundDecorations.removeAllCircles() // 全ての円を削除');
        console.log('- backgroundDecorations.getPerformanceInfo() // パフォーマンス情報を取得');
    }

    // モジュールエクスポート（開発環境用）
    if (typeof module !== 'undefined' && module.exports) {
        module.exports = BackgroundDecorations;
    }

})(jQuery);

// 低スペックデバイス対応：ReducedMotionメディアクエリのサポート
if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    console.log('[Background Decorations] Reduced motion preference detected, disabling animations');
    $(document).ready(() => {
        setTimeout(() => {
            if (window.backgroundDecorations) {
                window.backgroundDecorations.toggleAnimations(false);
            }
        }, 100);
    });
}
/**
 * 背景装飾アニメーション
 * サイト全体の<main>要素に浮遊する丸の装飾を追加
 */

(function($) {
    'use strict';

    // 設定
    const config = {
        // 丸の数
        circleCount: {
            desktop: 15,
            tablet: 10,
            mobile: 6
        },
        // 色のパレット（コンセプトカラー）
        colors: [
            'rgba(232, 180, 232, 0.1)',  // 薄い紫
            'rgba(199, 125, 199, 0.08)', // ミディアム紫
            'rgba(221, 160, 221, 0.12)', // ライト紫
            'rgba(155, 89, 182, 0.06)',  // ディープ紫
            'rgba(230, 126, 200, 0.09)', // ピンク紫
            'rgba(186, 85, 211, 0.07)'   // バイオレット
        ],
        // サイズ範囲
        sizeRange: {
            min: 40,
            max: 200
        },
        // アニメーション速度範囲（秒）
        animationSpeed: {
            min: 20,
            max: 50
        },
        // Z-index
        zIndex: -1
    };

    class BackgroundDecorations {
        constructor() {
            this.circles = [];
            this.container = null;
            this.isInitialized = false;
            
            this.init();
        }

        init() {
            if (this.isInitialized) return;
            
            // DOMが準備できたら実行
            $(document).ready(() => {
                this.createContainer();
                this.createCircles();
                this.bindEvents();
                this.isInitialized = true;
                
                console.log('[Background Decorations] Initialized successfully');
            });
        }

        createContainer() {
            // 対象セクションを指定
            const targetSelectors = [
                '.medi-recommend-section',
                '.medi-region-section', 
                '.medi-feeling-section',
                '.medi-situation-section',
                '.medi-genre-section',
                '.store-content-wrapper' // 店舗詳細ページ用
            ];
            
            // 各対象セクションに装飾を追加
            targetSelectors.forEach(selector => {
                const $target = $(selector);
                if ($target.length > 0) {
                    // セクションのスタイルを調整
                    $target.css({
                        'position': 'relative',
                        'overflow': 'hidden'
                    });

                    // 装飾用のコンテナを作成
                    const $decorationContainer = $('<div class="bg-decorations-container"></div>');
                    $decorationContainer.css({
                        'position': 'absolute',
                        'top': '0',
                        'left': '0',
                        'width': '100%',
                        'height': '100%',
                        'pointer-events': 'none',
                        'z-index': config.zIndex,
                        'overflow': 'hidden'
                    });

                    $target.prepend($decorationContainer);
                    
                    // 各セクションに数個の円を追加
                    this.createCirclesForContainer($decorationContainer, 3); // セクションごとに3個
                }
            });
            
            console.log('[Background Decorations] Multiple containers created');
        }

        getCircleCount() {
            const width = $(window).width();
            
            if (width <= 768) {
                return config.circleCount.mobile;
            } else if (width <= 1024) {
                return config.circleCount.tablet;
            } else {
                return config.circleCount.desktop;
            }
        }

        createCirclesForContainer($container, count) {
            for (let i = 0; i < count; i++) {
                const circle = {
                    element: null,
                    size: this.randomBetween(config.sizeRange.min, config.sizeRange.max),
                    color: config.colors[Math.floor(Math.random() * config.colors.length)],
                    x: Math.random() * 100,
                    y: Math.random() * 100,
                    duration: this.randomBetween(config.animationSpeed.min, config.animationSpeed.max),
                    delay: Math.random() * 5
                };

                const $circle = $('<div class="bg-decoration-circle"></div>');
                
                $circle.css({
                    'position': 'absolute',
                    'width': circle.size + 'px',
                    'height': circle.size + 'px',
                    'background': circle.color,
                    'border-radius': '50%',
                    'left': circle.x + '%',
                    'top': circle.y + '%',
                    'transform': 'translate(-50%, -50%)',
                    'animation': `float-${this.circles.length} ${circle.duration}s ease-in-out infinite`,
                    'animation-delay': circle.delay + 's',
                    'will-change': 'transform'
                });

                this.createFloatAnimation(this.circles.length, circle);
                
                circle.element = $circle;
                this.circles.push(circle);
                $container.append($circle);
            }
        }

        createCircle(index) {
            const circle = {
                element: null,
                size: this.randomBetween(config.sizeRange.min, config.sizeRange.max),
                color: config.colors[Math.floor(Math.random() * config.colors.length)],
                x: Math.random() * 100,
                y: Math.random() * 100,
                duration: this.randomBetween(config.animationSpeed.min, config.animationSpeed.max),
                delay: Math.random() * 5 // 0-5秒の遅延
            };

            // 円要素を作成
            const $circle = $('<div class="bg-decoration-circle"></div>');
            
            $circle.css({
                'position': 'absolute',
                'width': circle.size + 'px',
                'height': circle.size + 'px',
                'background': circle.color,
                'border-radius': '50%',
                'left': circle.x + '%',
                'top': circle.y + '%',
                'transform': 'translate(-50%, -50%)',
                'animation': `float-${index} ${circle.duration}s ease-in-out infinite`,
                'animation-delay': circle.delay + 's',
                'will-change': 'transform'
            });

            // アニメーションキーフレームを動的に生成
            this.createFloatAnimation(index, circle);

            circle.element = $circle;
            this.circles.push(circle);
            this.decorationContainer.append($circle);
        }

        createFloatAnimation(index, circle) {
            const animationName = `float-${index}`;
            
            // ランダムな移動パスを生成
            const keyframes = this.generateFloatKeyframes();
            
            // スタイルシートに追加
            if (!$(`#bg-decoration-styles-${index}`).length) {
                const style = $(`<style id="bg-decoration-styles-${index}">
                    @keyframes ${animationName} {
                        ${keyframes}
                    }
                </style>`);
                $('head').append(style);
            }
        }

        generateFloatKeyframes() {
            const points = [
                { percent: 0, x: 0, y: 0, scale: 1 },
                { percent: 25, x: this.randomBetween(-60, 60), y: this.randomBetween(-40, 40), scale: this.randomBetween(0.6, 1.4) },  // 動きを倍に
                { percent: 50, x: this.randomBetween(-40, 40), y: this.randomBetween(-60, 60), scale: this.randomBetween(0.7, 1.3) },
                { percent: 75, x: this.randomBetween(-50, 50), y: this.randomBetween(-30, 30), scale: this.randomBetween(0.65, 1.35) },
                { percent: 100, x: 0, y: 0, scale: 1 }
            ];
        
            return points.map(point => 
                `${point.percent}% {
                    transform: translate(calc(-50% + ${point.x}px), calc(-50% + ${point.y}px)) scale(${point.scale}) rotate(${point.x * 0.3}deg);
                }`  // 回転も追加
            ).join('\n');
        }

        randomBetween(min, max) {
            return Math.random() * (max - min) + min;
        }

        bindEvents() {
            // リサイズイベント
            let resizeTimer;
            $(window).on('resize', () => {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(() => {
                    this.handleResize();
                }, 250);
            });

            // 可視性変更時の処理
            $(document).on('visibilitychange', () => {
                this.handleVisibilityChange();
            });
        }

        handleResize() {
            const newCount = this.getCircleCount();
            
            if (newCount !== this.circles.length) {
                console.log('[Background Decorations] Recreating circles for new screen size');
                this.createCircles();
            }
        }

        handleVisibilityChange() {
            if (document.hidden) {
                // タブが非アクティブになったらアニメーションを一時停止
                this.decorationContainer.css('animation-play-state', 'paused');
            } else {
                // タブがアクティブになったらアニメーションを再開
                this.decorationContainer.css('animation-play-state', 'running');
            }
        }

        // パフォーマンス最適化：アニメーションの有効/無効切り替え
        toggleAnimations(enable = true) {
            const display = enable ? 'block' : 'none';
            this.decorationContainer.css('display', display);
            
            console.log(`[Background Decorations] Animations ${enable ? 'enabled' : 'disabled'}`);
        }

        // 動的に円を追加
        addCircle() {
            const index = this.circles.length;
            this.createCircle(index);
        }

        // 全ての円を削除
        removeAllCircles() {
            this.circles = [];
            this.decorationContainer.empty();
            
            // 動的に作成したスタイルも削除
            $('[id^="bg-decoration-styles-"]').remove();
        }

        // 色テーマを変更
        changeColorTheme(newColors) {
            if (Array.isArray(newColors) && newColors.length > 0) {
                config.colors = newColors;
                this.createCircles(); // 円を再作成
                console.log('[Background Decorations] Color theme updated');
            }
        }

        // パフォーマンス監視
        getPerformanceInfo() {
            return {
                circleCount: this.circles.length,
                containerSize: {
                    width: this.container.width(),
                    height: this.container.height()
                },
                isVisible: !document.hidden
            };
        }
    }

    // グローバルアクセス用
    window.BackgroundDecorations = BackgroundDecorations;

    // 自動初期化
    const backgroundDecorations = new BackgroundDecorations();

    // 開発者向けコンソールコマンド
    if (typeof window.console !== 'undefined') {
        console.log('%c[Background Decorations] Available commands:', 'color: #C77DC7; font-weight: bold;');
        console.log('- backgroundDecorations.toggleAnimations(false) // アニメーションを無効化');
        console.log('- backgroundDecorations.addCircle() // 円を追加');
        console.log('- backgroundDecorations.removeAllCircles() // 全ての円を削除');
        console.log('- backgroundDecorations.getPerformanceInfo() // パフォーマンス情報を取得');
    }

    // モジュールエクスポート（開発環境用）
    if (typeof module !== 'undefined' && module.exports) {
        module.exports = BackgroundDecorations;
    }

})(jQuery);

// 低スペックデバイス対応：ReducedMotionメディアクエリのサポート
if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    console.log('[Background Decorations] Reduced motion preference detected, disabling animations');
    $(document).ready(() => {
        setTimeout(() => {
            if (window.backgroundDecorations) {
                window.backgroundDecorations.toggleAnimations(false);
            }
        }, 100);
    });
}