<?php
/**
 * The template for displaying the front page (homepage)
 * TCD GENSEN integrated version
 * 
 * @package medi& GENSEN Child
 */

get_header();

// --- TCD GENSEN テーマオプション取得 ---
global $dp_options;
if (!$dp_options) $dp_options = get_desing_plus_option();

// --- 子テーマ独自オプション取得 ---
$medi_options = get_option('medi_theme_options', array());

// アイコン・画像パス
$icon_base_path = get_stylesheet_directory_uri() . '/assets/icons/';
$images_base_path = get_stylesheet_directory_uri() . '/assets/images/';

// --- おすすめ店舗取得（ACFで管理） ---
$featured_store_ids = get_field('homepage_featured_stores', 'option'); // オプションページで設定
$featured_stores_query = null;

if ($featured_store_ids && is_array($featured_store_ids)) {
    $featured_stores_query = new WP_Query(array(
        'post_type' => 'store',
        'post__in' => $featured_store_ids,
        'orderby' => 'post__in',
        'posts_per_page' => 4
    ));
}

// フォールバック：設定がない場合は最新4件
if (!$featured_stores_query || !$featured_stores_query->have_posts()) {
    $featured_stores_query = new WP_Query(array(
        'post_type' => 'store',
        'posts_per_page' => 4,
        'orderby' => 'date',
        'order' => 'DESC'
    ));
}

// --- セクション表示制御 ---
$show_featured = get_field('show_featured_section', 'option') !== false; // デフォルト表示
$show_regions = get_field('show_regions_section', 'option') !== false;
$show_feelings = get_field('show_feelings_section', 'option') !== false;
$show_situations = get_field('show_situations_section', 'option') !== false;
$show_genres = get_field('show_genres_section', 'option') !== false;

// --- タクソノミーデータ取得 ---
$region_terms = get_terms(array(
    'taxonomy' => 'prefecture',
    'parent' => 0,
    'hide_empty' => false,
    'orderby' => 'name',
    'order' => 'ASC'
));

$feeling_terms = get_terms(array(
    'taxonomy' => 'feeling',
    'hide_empty' => false,
    'orderby' => 'name',
    'order' => 'ASC'
));

$situation_terms = get_terms(array(
    'taxonomy' => 'situation',
    'hide_empty' => false,
    'orderby' => 'name',
    'order' => 'ASC'
));

$genre_terms = get_terms(array(
    'taxonomy' => 'genre',
    'hide_empty' => false,
    'orderby' => 'name',
    'order' => 'ASC'
));
?>

<div class="homepage-wrapper">
    
    <?php // --- TCDキービジュアル表示 --- ?>
    <?php if (isset($dp_options['show_index_slider']) && $dp_options['show_index_slider']) : ?>
        <?php get_template_part('template-parts/header-slider'); ?>
    <?php else : ?>
        <?php // フォールバック：独自ヒーローセクション ?>
        <section class="hero-section">
            <div class="hero-section__container">
                <div class="hero-section__content">
                    <div class="hero-section__text">
                        <h1 class="hero-section__title">
                            <span class="hero-section__title-main">SNS</span>
                            <span class="hero-section__title-sub">からリアルへ</span>
                        </h1>
                        <p class="hero-section__description">
                            <?php echo get_field('hero_description', 'option') ?: 'SNSで見つけた素敵なお店を、実際に体験してみませんか？<br>あなたの気分やシチュエーションに合わせて、最適なお店を見つけましょう。'; ?>
                        </p>
                    </div>
                    <?php if (get_field('hero_image', 'option')) : ?>
                        <div class="hero-section__illustration">
                            <img src="<?php echo esc_url(get_field('hero_image', 'option')['url']); ?>" alt="<?php echo esc_attr(get_field('hero_image', 'option')['alt']); ?>" class="hero-section__illustration-img">
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="hero-section__search">
                    <form action="<?php echo esc_url(get_post_type_archive_link('store')); ?>" method="get" class="hero-search-form">
                        <div class="hero-search-form__input-wrapper">
                            <input type="search" name="s" placeholder="地域名・お店の名前・特徴・気分・目的など" class="hero-search-form__input">
                            <button type="submit" class="hero-search-form__button">検索</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <div class="homepage-content">
        <div class="homepage-main">
            
            <?php // --- おすすめ店舗セクション --- ?>
            <?php if ($show_featured && $featured_stores_query->have_posts()) : ?>
                <section class="featured-stores-section">
                    <div class="container">
                        <h2 class="section-title"><?php echo get_field('featured_section_title', 'option') ?: 'おすすめ'; ?></h2>
                        <p class="section-subtitle"><?php echo get_field('featured_section_subtitle', 'option') ?: '編集部がセレクトした、今注目のお店をご紹介！'; ?></p>
                        
                        <div class="featured-stores-grid">
                            <?php while ($featured_stores_query->have_posts()) : $featured_stores_query->the_post(); ?>
                                <?php
                                $store_id = get_the_ID();
                                $store_title = get_the_title();
                                $store_permalink = get_permalink();
                                $store_thumbnail = get_the_post_thumbnail_url($store_id, 'medium');
                                $store_prefecture = '';
                                
                                $prefecture_terms = get_the_terms($store_id, 'prefecture');
                                if (!empty($prefecture_terms) && !is_wp_error($prefecture_terms)) {
                                    $pref_names = [];
                                    foreach($prefecture_terms as $term) {
                                        if($term->parent != 0) {
                                            $pref_names[] = esc_html($term->name);
                                        }
                                    }
                                    $store_prefecture = implode(', ', $pref_names);
                                }
                                ?>
                                <article class="featured-store-card">
                                    <a href="<?php echo esc_url($store_permalink); ?>" class="featured-store-card__link">
                                        <div class="featured-store-card__image">
                                            <?php if ($store_thumbnail) : ?>
                                                <img src="<?php echo esc_url($store_thumbnail); ?>" alt="<?php echo esc_attr($store_title); ?>">
                                            <?php else : ?>
                                                <div class="featured-store-card__no-image">No Image</div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="featured-store-card__content">
                                            <h3 class="featured-store-card__title"><?php echo esc_html($store_title); ?></h3>
                                            <?php if ($store_prefecture) : ?>
                                                <p class="featured-store-card__location">
                                                    <img src="<?php echo esc_url($icon_base_path . 'pin.png'); ?>" alt="" class="featured-store-card__location-icon">
                                                    <?php echo $store_prefecture; ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </a>
                                </article>
                            <?php endwhile; ?>
                            <?php wp_reset_postdata(); ?>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

            <?php // --- 地域から選ぶセクション --- ?>
            <?php if ($show_regions && !empty($region_terms) && !is_wp_error($region_terms)) : ?>
                <section class="region-section">
                    <div class="container">
                        <h2 class="section-title"><?php echo get_field('regions_section_title', 'option') ?: '地域から選ぶ'; ?></h2>
                        <p class="section-subtitle"><?php echo get_field('regions_section_subtitle', 'option') ?: '全国各地の魅力的なお店を地域別にご紹介'; ?></p>
                        
                        <div class="region-accordion">
                            <?php foreach ($region_terms as $region_term) : ?>
                                <div class="region-accordion__item">
                                    <button class="region-accordion__header" data-region-id="<?php echo esc_attr($region_term->term_id); ?>">
                                        <span class="region-accordion__title"><?php echo esc_html($region_term->name); ?></span>
                                        <span class="region-accordion__icon">▼</span>
                                    </button>
                                    <div class="region-accordion__content" data-region-content="<?php echo esc_attr($region_term->term_id); ?>">
                                        <div class="region-prefectures">
                                            <?php
                                            $prefectures = get_terms(array(
                                                'taxonomy' => 'prefecture',
                                                'parent' => $region_term->term_id,
                                                'hide_empty' => false,
                                                'orderby' => 'name',
                                                'order' => 'ASC'
                                            ));
                                            ?>
                                            <?php if (!empty($prefectures) && !is_wp_error($prefectures)) : ?>
                                                <?php foreach ($prefectures as $prefecture) : ?>
                                                    <a href="<?php echo esc_url(get_post_type_archive_link('store') . '?prefecture_filter=' . $prefecture->slug); ?>" class="region-prefecture-link">
                                                        <?php echo esc_html($prefecture->name); ?>
                                                    </a>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

            <?php // --- ココロで選ぶセクション --- ?>
            <?php if ($show_feelings && !empty($feeling_terms) && !is_wp_error($feeling_terms)) : ?>
                <section class="feeling-section">
                    <div class="container">
                        <h2 class="section-title"><?php echo get_field('feelings_section_title', 'option') ?: 'ココロで選ぶ'; ?></h2>
                        <p class="section-subtitle"><?php echo get_field('feelings_section_subtitle', 'option') ?: 'あなたの気持ちに寄り添う、特別な体験を見つけよう。'; ?></p>
                        
                        <div class="feeling-grid">
                            <?php foreach ($feeling_terms as $feeling_term) : ?>
                                <a href="<?php echo esc_url(get_post_type_archive_link('store') . '?feeling_filter[]=' . $feeling_term->slug . '&active_tab=feeling'); ?>" class="feeling-item">
                                    <div class="feeling-item__icon">
                                        <?php
                                        // カスタムアイコンがACFで設定されている場合
                                        $custom_icon = get_field('feeling_icon', 'feeling_' . $feeling_term->term_id);
                                        if ($custom_icon) {
                                            echo '<img src="' . esc_url($custom_icon['url']) . '" alt="' . esc_attr($feeling_term->name) . '">';
                                        } else {
                                            echo '<img src="' . esc_url($icon_base_path . 'heart-icon.svg') . '" alt="' . esc_attr($feeling_term->name) . '">';
                                        }
                                        ?>
                                    </div>
                                    <span class="feeling-item__text"><?php echo esc_html($feeling_term->name); ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

            <?php // --- シチュエーションで選ぶセクション --- ?>
            <?php if ($show_situations && !empty($situation_terms) && !is_wp_error($situation_terms)) : ?>
                <section class="situation-section">
                    <div class="container">
                        <h2 class="section-title"><?php echo get_field('situations_section_title', 'option') ?: 'シチュエーションで選ぶ'; ?></h2>
                        <p class="section-subtitle"><?php echo get_field('situations_section_subtitle', 'option') ?: '大切な人との時間や、特別な日にぴったりのお店を見つけましょう。'; ?></p>
                        
                        <div class="situation-grid">
                            <?php foreach (array_slice($situation_terms, 0, 6) as $situation_term) : ?>
                                <a href="<?php echo esc_url(get_post_type_archive_link('store') . '?situation_filter[]=' . $situation_term->slug . '&active_tab=situation'); ?>" class="situation-item">
                                    <div class="situation-item__image">
                                        <?php
                                        // カスタム画像がACFで設定されている場合
                                        $custom_image = get_field('situation_image', 'situation_' . $situation_term->term_id);
                                        if ($custom_image) {
                                            echo '<img src="' . esc_url($custom_image['url']) . '" alt="' . esc_attr($situation_term->name) . '">';
                                        } else {
                                            echo '<img src="' . esc_url($images_base_path . 'default-situation.jpg') . '" alt="' . esc_attr($situation_term->name) . '">';
                                        }
                                        ?>
                                        <div class="situation-item__overlay">
                                            <span class="situation-item__text"><?php echo esc_html($situation_term->name); ?></span>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

            <?php // --- ジャンルで選ぶセクション --- ?>
            <?php if ($show_genres && !empty($genre_terms) && !is_wp_error($genre_terms)) : ?>
                <section class="genre-section">
                    <div class="container">
                        <h2 class="section-title"><?php echo get_field('genres_section_title', 'option') ?: 'ジャンルで選ぶ'; ?></h2>
                        <p class="section-subtitle"><?php echo get_field('genres_section_subtitle', 'option') ?: 'カフェから本格ディナーまで、様々なジャンルのお店をご紹介'; ?></p>
                        
                        <div class="genre-grid">
                            <?php foreach ($genre_terms as $genre_term) : ?>
                                <a href="<?php echo esc_url(get_post_type_archive_link('store') . '?genre_filter[]=' . $genre_term->slug . '&active_tab=genre'); ?>" class="genre-item">
                                    <?php echo esc_html($genre_term->name); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

        </div>

        <?php // --- サイドバー（お知らせ・広告欄） --- ?>
        <aside class="homepage-sidebar">
            <?php // TCD広告管理機能の活用 ?>
            <?php if (isset($dp_options['show_ad_top']) && $dp_options['show_ad_top'] && $dp_options['ad_code_top']) : ?>
                <div class="sidebar-ad-section">
                    <h3 class="sidebar-section-title">おすすめ</h3>
                    <div class="sidebar-ad-content">
                        <?php echo $dp_options['ad_code_top']; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php // カスタム広告セクション（アフィリエイト用） ?>
            <?php
            $custom_ads = get_field('homepage_custom_ads', 'option');
            if ($custom_ads && is_array($custom_ads)) :
            ?>
                <div class="sidebar-ad-section">
                    <h3 class="sidebar-section-title">広告</h3>
                    <div class="sidebar-ads-list">
                        <?php foreach ($custom_ads as $ad) : ?>
                            <div class="sidebar-ad-item">
                                <?php if ($ad['ad_image']) : ?>
                                    <a href="<?php echo esc_url($ad['ad_url']); ?>" target="_blank" rel="noopener noreferrer">
                                        <img src="<?php echo esc_url($ad['ad_image']['url']); ?>" alt="<?php echo esc_attr($ad['ad_title']); ?>">
                                    </a>
                                <?php endif; ?>
                                <?php if ($ad['ad_code']) : ?>
                                    <?php echo $ad['ad_code']; ?>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php // TCDのお知らせ機能 ?>
            <?php if (function_exists('tcd_get_news_list')) : ?>
                <div class="sidebar-news-section">
                    <h3 class="sidebar-section-title">お知らせ</h3>
                    <?php tcd_get_news_list(5); ?>
                </div>
            <?php endif; ?>

            <?php // サイドバーウィジェット ?>
            <?php if (is_active_sidebar('homepage-sidebar')) : ?>
                <?php dynamic_sidebar('homepage-sidebar'); ?>
            <?php endif; ?>
        </aside>
    </div>

</div>

<?php get_footer(); ?>