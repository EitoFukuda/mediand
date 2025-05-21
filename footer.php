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
                <div class="footer-category-widget">
                    <h3 class="footer-category-widget__title">カテゴリ</h3>
                    <?php
                    if (has_nav_menu('footer_category_menu')) { // 'footer_category_menu' というメニュー位置を登録・設定した場合
                        wp_nav_menu(array(
                            'theme_location' => 'footer_category_menu',
                            'container' => false,
                            'menu_class' => 'footer-category-widget__menu',
                            'depth' => 1, // 階層は1階層のみ
                        ));
                    } else {
                        // 代替コンテンツ (例)
                        echo '<ul>';
                        echo '<li><a href="#">地域から選ぶ (仮)</a></li>';
                        echo '<li><a href="#">ココロで選ぶ (仮)</a></li>';
                        echo '<li><a href="#">シチュエーションで選ぶ (仮)</a></li>';
                        echo '<li><a href="#">ジャンルで選ぶ (仮)</a></li>';
                        echo '</ul>';
                    }
                    ?>
                </div>

                <div class="footer-category-widget">
                    <h3 class="footer-category-widget__title">サイト情報</h3>
                     <?php
                    if (has_nav_menu('footer_sitemap_menu')) { // 'footer_sitemap_menu' というメニュー位置
                        wp_nav_menu(array(
                            'theme_location' => 'footer_sitemap_menu',
                            'container' => false,
                            'menu_class' => 'footer-category-widget__menu',
                            'depth' => 1,
                        ));
                    } else {
                        echo '<ul>';
                        echo '<li><a href="#">サイトについて (仮)</a></li>';
                        echo '<li><a href="#">ご利用ガイド (仮)</a></li>';
                        echo '<li><a href="#">店舗オーナー様へ (仮)</a></li>';
                        echo '<li><a href="#">お問い合わせ (仮)</a></li>';
                        echo '</ul>';
                    }
                    ?>
                </div>

                <div class="footer-category-widget">
                    <h3 class="footer-category-widget__title">サポート</h3>
                    <?php
                    if (has_nav_menu('footer_support_menu')) { // 'footer_support_menu' というメニュー位置
                        wp_nav_menu(array(
                            'theme_location' => 'footer_support_menu',
                            'container' => false,
                            'menu_class' => 'footer-category-widget__menu',
                            'depth' => 1,
                        ));
                    } else {
                        echo '<ul>';
                        echo '<li><a href="#">よくある質問 (仮)</a></li>';
                        echo '<li><a href="#">プライバシーポリシー (仮)</a></li>';
                        echo '<li><a href="#">利用規約 (仮)</a></li>';
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