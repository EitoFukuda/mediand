<?php
/**
 * The template for displaying the footer
 * Contains the closing of the #main_contents div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package medi& GENSEN Child
 */

global $dp_options;
if (! $dp_options) $dp_options = get_desing_plus_option(); // 親テーマのオプション取得 (フッターでも使う可能性)
?>

</div><footer id="colophon" class="site-footer-custom">
    <div class="container site-footer-custom__container">
        <div class="site-footer-custom__inner">

            <div class="site-footer-custom__logo-area">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="site-footer-custom__logo-link">
                    <?php // フッター用ロゴ画像のパス (ヘッダーと同じか、別のものを用意) ?>
                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/logo-white.png" alt="<?php bloginfo('name'); ?> フッターロゴ" class="site-footer-custom__logo-img">
                </a>
                <?php // ロゴの下にサイトの説明文などを追加する場合はここに ?>
            </div>

            <div class="site-footer-custom__nav-widgets">
                <!-- 修正後 -->
<div class="footer-category-widget">
    <h3 class="footer-category-widget__title">メニュー</h3>
    <?php
    if (has_nav_menu('primary')) { // ヘッダーと同じ 'primary' メニューを使用
        wp_nav_menu(array(
            'theme_location' => 'primary',
            'container' => false,
            'menu_class' => 'footer-category-widget__menu',
            'depth' => 1, // 階層は1階層のみ
        ));
    } else {
        // 代替コンテンツ（primaryメニューが設定されていない場合）
        echo '<ul class="footer-category-widget__menu">';
        echo '<li><a href="' . esc_url(home_url('/')) . '">ホーム</a></li>';
        echo '<li><a href="' . esc_url(get_post_type_archive_link('store')) . '">店舗一覧</a></li>';
        echo '</ul>';
    }
    ?>
</div>
            </div>

        </div>

        <div class="site-footer-custom__bottom">
            <p class="site-footer-custom__copyright">&copy; <?php echo date_i18n('Y'); ?> <?php bloginfo('name'); ?>. All rights reserved.</p>
        </div>
    </div>
</footer>

<?php wp_footer(); // WordPress必須のアクションフック ?>
</body>
</html>