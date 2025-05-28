jQuery(document).ready(function($) {
    $('.mobile-menu-toggle').on('click', function() {
        $(this).toggleClass('active');
        $('.site-header-custom__nav').toggleClass('mobile-active');
    });

    // メニュー外クリックで閉じる
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.site-header-custom').length) {
            $('.mobile-menu-toggle').removeClass('active');
            $('.site-header-custom__nav').removeClass('mobile-active');
        }
    });
});