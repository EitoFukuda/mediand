document.addEventListener('DOMContentLoaded', function() {
    console.log('[Stagewise] Attempting to initialize Stagewise Toolbar...');
    const stagewiseConfig = {
        plugins: []
    };

    // StagewiseのinitToolbarがグローバルに利用可能か確認します。
    // これは@stagewise/toolbarのUMD/IIFEビルドがどのように関数を公開するかに依存します。
    // window.Stagewise.initToolbar または window.initToolbar のような形式を試みます。
    if (typeof window.Stagewise !== 'undefined' && typeof window.Stagewise.initToolbar === 'function') {
        window.Stagewise.initToolbar(stagewiseConfig);
        console.log('[Stagewise] Toolbar initialized successfully via window.Stagewise.initToolbar().');
    } else if (typeof window.initToolbar === 'function') {
        window.initToolbar(stagewiseConfig);
        console.log('[Stagewise] Toolbar initialized successfully via window.initToolbar().');
    } else {
        console.error('[Stagewise] initToolbar function not found. Ensure @stagewise/toolbar is loaded and exposes initToolbar globally.');
    }
});
