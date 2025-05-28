<?php
/**
 * The header for our GENSEN child theme.
 * Displays all of the <head> section and a custom header structure.
 *
 * @package medi& GENSEN Child
 */

global $dp_options;
if (! $dp_options) $dp_options = get_desing_plus_option(); // 親テーマのオプション取得
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<?php if(isset($dp_options['use_ogp']) && $dp_options['use_ogp']) { // 親テーマのOGP設定を尊重 ?>
<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb#">
<?php } else { ?>
<head>
<?php } ?>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="https://gmpg.org/xfn/11">
<?php if (isset($dp_options['favicon']) && ($favicon_attachment_id = $dp_options['favicon']) && ($favicon = wp_get_attachment_image_src($favicon_attachment_id, 'full'))) : // 親テーマのファビコン設定 ?>
<link rel="shortcut icon" href="<?php echo esc_url($favicon[0]); ?>">
<?php endif; ?>
<?php wp_head(); // WordPress必須のアクションフック ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php if (isset($dp_options['use_load_icon']) && $dp_options['use_load_icon']) { /* 親テーマのローディングアイコン */ ?>
<div id="site_loader_overlay"><div id="site_loader_animation"><?php if (isset($dp_options['load_icon']) && $dp_options['load_icon'] == 'type3') { ?><i></i><i></i><i></i><i></i><?php } ?></div></div>
<?php } ?>

<header class="site-header-custom <?php if (isset($dp_options['header_fix']) && $dp_options['header_fix'] == 'type2') { echo ' is-fixed-header'; } // 親テーマの固定ヘッダー設定を考慮 ?>">
  <div class="container site-header-custom__container">
    <div class="site-header-custom__inner">
      <div class="site-header-custom__logo">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="site-header-custom__logo-link">
          <?php // ロゴ画像のパスは子テーマの assets/images/logo.png を想定 ?>
          <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/logo.png" alt="<?php bloginfo('name'); // サイト名 ?>" class="site-header-custom__logo-img">
        </a>
      </div>
      <!-- 37-48行目を以下に変更 -->
<nav class="site-header-custom__nav" role="navigation" aria-label="<?php esc_attr_e( 'Main Navigation', 'gensen_tcd050-child' ); ?>">
    <?php
    if (has_nav_menu('primary')) { 
        wp_nav_menu(array(
            'theme_location' => 'primary',
            'container'      => false,
            'menu_class'     => 'site-header-custom__menu',
            'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
            'fallback_cb'    => false,
        ));
    } else {
        // デフォルトメニューを表示
        echo '<ul class="site-header-custom__menu">';
        echo '<li><a href="' . esc_url(home_url('/')) . '">ホーム</a></li>';
        echo '<li><a href="' . esc_url(get_post_type_archive_link('store')) . '">店舗一覧</a></li>';
        echo '</ul>';
    }
    ?>
</nav>
      <?php // モバイル用のハンバーガーメニューボタンなどをここに追加する場合 ?>
      </div>
  </div>
</header>

<div id="main_contents" class="clearfix"> <?php // 親テーマのメインコンテンツ開始のラッパーは残すことが多い ?>