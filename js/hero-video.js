document.addEventListener('DOMContentLoaded', function() {
    const heroVideo = document.querySelector('.medi-hero-video');
    const fallbackBg = document.querySelector('.medi-hero-fallback-bg');
    
    if (heroVideo) {
        console.log('[Hero Video] Video element found');
        
        // 動画の読み込み状況をチェック
        function checkVideoLoad() {
            if (heroVideo.readyState >= 3) { // HAVE_FUTURE_DATA以上
                console.log('[Hero Video] Video loaded successfully');
                playVideo();
            } else {
                console.log('[Hero Video] Video not ready, readyState:', heroVideo.readyState);
                showFallback();
            }
        }
        
        // 動画再生を試行
        function playVideo() {
            const playPromise = heroVideo.play();
            
            if (playPromise !== undefined) {
                playPromise.then(() => {
                    console.log('[Hero Video] Autoplay started successfully');
                    heroVideo.style.opacity = '1';
                    if (fallbackBg) fallbackBg.style.opacity = '0';
                }).catch(error => {
                    console.log('[Hero Video] Autoplay failed:', error);
                    showFallback();
                });
            }
        }
        
        // フォールバック画像を表示
        function showFallback() {
            console.log('[Hero Video] Showing fallback background');
            heroVideo.style.opacity = '0';
            if (fallbackBg) fallbackBg.style.opacity = '1';
        }
        
        // イベントリスナー
        heroVideo.addEventListener('loadeddata', checkVideoLoad);
        heroVideo.addEventListener('canplay', playVideo);
        heroVideo.addEventListener('error', function(e) {
            console.error('[Hero Video] Video error:', e);
            showFallback();
        });
        
        // 動画が終了したら最後のフレームで停止（ループしない場合）
        heroVideo.addEventListener('ended', function() {
            this.currentTime = this.duration;
            this.pause();
        });
        
        // 初期チェック
        if (heroVideo.readyState >= 3) {
            checkVideoLoad();
        }
        
        // 5秒後に動画が再生されていない場合はフォールバック
        setTimeout(() => {
            if (heroVideo.paused && heroVideo.readyState < 3) {
                console.log('[Hero Video] Timeout: Switching to fallback');
                showFallback();
            }
        }, 5000);
    } else {
        console.log('[Hero Video] Video element not found');
    }
});