document.addEventListener('DOMContentLoaded', function() {
    const heroVideo = document.querySelector('.medi-hero-video');
    
    if (heroVideo) {
        // 動画が終了したら最後のフレームで停止
        heroVideo.addEventListener('ended', function() {
            this.currentTime = this.duration;
            this.pause();
        });
        
        // 動画読み込み完了後に再生開始
        heroVideo.addEventListener('loadeddata', function() {
            this.play().catch(function(error) {
                console.log('動画の自動再生が失敗しました:', error);
            });
        });
    }
});