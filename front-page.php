<?php
/**
 * The template for displaying the front page (homepage)
 * TCD GENSEN integrated version with custom sections
 * 
 * @package medi& GENSEN Child
 */

get_header();

// --- TCD GENSEN テーマオプション取得 ---
global $dp_options;
if (!$dp_options) $dp_options = get_desing_plus_option();

// アイコン・画像パス
$icon_base_path = get_stylesheet_directory_uri() . '/assets/icons/';
$images_base_path = get_stylesheet_directory_uri() . '/assets/images/';
?>

<div class="homepage-wrapper">
    
    <?php // --- TCDのスライダー/ヒーローセクション表示 --- ?>
    <?php if (isset($dp_options['show_index_slider']) && $dp_options['show_index_slider']) : ?>
        <?php get_template_part('template-parts/header-slider'); ?>
    <?php else : ?>
        <?php // フォールバック：独自ヒーローセクション ?>
        <section class="medi-hero-section">
            <div class="medi-hero-section__bg-shapes">
                <div class="shape shape-1"></div>
                <div class="shape shape-2"></div>
                <div class="shape shape-3"></div>
            </div>
            <div class="container">
                <div class="medi-hero-section__content">
                    <div class="medi-hero-section__text">
                        <h1 class="medi-hero-section__title">
                            <span class="title-sns">SNS</span>
                            <span class="title-kara">から</span>
                            <span class="title-real">リアルへ</span>
                        </h1>
                        <p class="medi-hero-section__description">
                            SNSで見つけた素敵なお店を、実際に体験してみませんか？<br>
                            あなたの気分やシチュエーションに合わせて、最適なお店を見つけましょう。
                        </p>
                    </div>
                    <div class="medi-hero-section__illustration">
                        <!-- イラスト用のSVGまたは画像 -->
                        <div class="illustration-placeholder">
                            <div class="character character-1"></div>
                            <div class="character character-2"></div>
                            <div class="floating-elements">
                                <div class="element element-1"></div>
                                <div class="element element-2"></div>
                                <div class="element element-3"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="medi-hero-section__search">
                    <form action="<?php echo esc_url(get_post_type_archive_link('store')); ?>" method="get" class="medi-hero-search-form">
                        <div class="medi-hero-search-form__wrapper">
                            <input type="search" name="s" placeholder="地域名・お店の名前・特徴・気分・目的など" class="medi-hero-search-form__input">
                            <button type="submit" class="medi-hero-search-form__button">検索</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <div class="homepage-content-wrapper">
        
        <?php // --- おすすめセクション --- ?>
        <section class="medi-recommend-section">
            <div class="container">
                <h2 class="medi-section-title">おすすめ</h2>
                <p class="medi-section-subtitle">編集部がセレクトした、今注目のお店をご紹介！</p>
                
                <div class="medi-recommend-grid">
                    <?php
                    // おすすめ店舗の取得（最新4件をフォールバック）
                    $recommend_query = new WP_Query(array(
                        'post_type' => 'store',
                        'posts_per_page' => 4,
                        'orderby' => 'date',
                        'order' => 'DESC'
                    ));
                    
                    if ($recommend_query->have_posts()) :
                        while ($recommend_query->have_posts()) : $recommend_query->the_post();
                            $store_id = get_the_ID();
                            $store_title = get_the_title();
                            $store_permalink = get_permalink();
                            $store_thumbnail = get_the_post_thumbnail_url($store_id, 'medium');
                            
                            // 都道府県取得
                            $prefecture_terms = get_the_terms($store_id, 'prefecture');
                            $prefecture_display = '';
                            if (!empty($prefecture_terms) && !is_wp_error($prefecture_terms)) {
                                $pref_names = [];
                                foreach($prefecture_terms as $term) {
                                    if($term->parent != 0) {
                                        $pref_names[] = esc_html($term->name);
                                    }
                                }
                                $prefecture_display = implode(', ', $pref_names);
                            }
                            
                            // ジャンル取得
                            $genre_terms = get_the_terms($store_id, 'genre');
                            $genre_display = '';
                            if (!empty($genre_terms) && !is_wp_error($genre_terms)) {
                                $genre_names = [];
                                foreach(array_slice($genre_terms, 0, 2) as $term) {
                                    $genre_names[] = esc_html($term->name);
                                }
                                $genre_display = implode(', ', $genre_names);
                            }
                    ?>
                            <article class="medi-recommend-card">
                                <a href="<?php echo esc_url($store_permalink); ?>" class="medi-recommend-card__link">
                                    <div class="medi-recommend-card__image">
                                        <?php if ($store_thumbnail) : ?>
                                            <img src="<?php echo esc_url($store_thumbnail); ?>" alt="<?php echo esc_attr($store_title); ?>">
                                        <?php else : ?>
                                            <div class="medi-recommend-card__no-image">No Image</div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="medi-recommend-card__content">
                                        <h3 class="medi-recommend-card__title"><?php echo esc_html($store_title); ?></h3>
                                        <?php if ($prefecture_display) : ?>
                                            <p class="medi-recommend-card__location">
                                                <img src="<?php echo esc_url($icon_base_path . 'pin.png'); ?>" alt="" class="location-icon">
                                                <?php echo $prefecture_display; ?>
                                            </p>
                                        <?php endif; ?>
                                        <?php if ($genre_display) : ?>
                                            <div class="medi-recommend-card__tags">
                                                <?php foreach(array_slice($genre_terms, 0, 2) as $term) : ?>
                                                    <span class="tag"><?php echo esc_html($term->name); ?></span>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </a>
                            </article>
                    <?php 
                        endwhile; 
                        wp_reset_postdata();
                    endif;
                    ?>
                </div>
            </div>
        </section>

        <?php // --- 地域から選ぶセクション --- ?>
        <section class="medi-region-section">
            <div class="container">
                <h2 class="medi-section-title">地域から選ぶ</h2>
                <p class="medi-section-subtitle">全国各地の魅力的なお店を地域別にご紹介</p>
                
                <div class="medi-region-accordion">
                    <?php
                    $region_terms = get_terms(array(
                        'taxonomy' => 'prefecture',
                        'parent' => 0,
                        'hide_empty' => false,
                        'orderby' => 'name',
                        'order' => 'ASC'
                    ));
                    
                    if (!empty($region_terms) && !is_wp_error($region_terms)) :
                        foreach ($region_terms as $region_term) :
                    ?>
                            <div class="medi-region-accordion__item">
                                <button class="medi-region-accordion__header" data-region-id="<?php echo esc_attr($region_term->term_id); ?>">
                                    <span class="medi-region-accordion__title"><?php echo esc_html($region_term->name); ?></span>
                                    <span class="medi-region-accordion__icon">▼</span>
                                </button>
                                <div class="medi-region-accordion__content" data-region-content="<?php echo esc_attr($region_term->term_id); ?>">
                                    <div class="medi-region-prefectures">
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
                                                <a href="<?php echo esc_url(get_post_type_archive_link('store') . '?prefecture_filter=' . $prefecture->slug); ?>" class="medi-region-prefecture-link">
                                                    <?php echo esc_html($prefecture->name); ?>
                                                </a>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                    <?php 
                        endforeach;
                    endif;
                    ?>
                </div>
            </div>
        </section>

        <?php // --- ココロで選ぶセクション --- ?>
        <section class="medi-feeling-section">
            <div class="container">
                <h2 class="medi-section-title">ココロで選ぶ</h2>
                <p class="medi-section-subtitle">あなたの気持ちに寄り添う、特別な体験を見つけよう。</p>
                
                <div class="medi-feeling-grid">
                    <?php
                    $feeling_terms = get_terms(array(
                        'taxonomy' => 'feeling',
                        'hide_empty' => false,
                        'orderby' => 'name',
                        'order' => 'ASC'
                    ));
                    
                    if (!empty($feeling_terms) && !is_wp_error($feeling_terms)) :
                        foreach ($feeling_terms as $feeling_term) :
                    ?>
                            <a href="<?php echo esc_url(get_post_type_archive_link('store') . '?feeling_filter[]=' . $feeling_term->slug . '&active_tab=feeling'); ?>" class="medi-feeling-item">
                                <div class="medi-feeling-item__icon">
                                    <?php
                                    // カスタムアイコンがある場合は使用、なければデフォルト
                                    $custom_icon = get_field('feeling_icon', 'feeling_' . $feeling_term->term_id);
                                    if ($custom_icon) {
                                        echo '<img src="' . esc_url($custom_icon['url']) . '" alt="' . esc_attr($feeling_term->name) . '">';
                                    } else {
                                        // デフォルトアイコンを使用
                                        echo '<div class="default-feeling-icon">💝</div>';
                                    }
                                    ?>
                                </div>
                                <span class="medi-feeling-item__text"><?php echo esc_html($feeling_term->name); ?></span>
                            </a>
                    <?php 
                        endforeach;
                    endif;
                    ?>
                </div>
            </div>
        </section>

        <?php // --- シチュエーションで選ぶセクション --- ?>
        <section class="medi-situation-section">
            <div class="container">
                <h2 class="medi-section-title">シチュエーションで選ぶ</h2>
                <p class="medi-section-subtitle">大切な人との時間や、特別な日にぴったりのお店を見つけましょう。</p>
                
                <div class="medi-situation-grid">
                    <?php
                    $situation_terms = get_terms(array(
                        'taxonomy' => 'situation',
                        'hide_empty' => false,
                        'orderby' => 'name',
                        'order' => 'ASC'
                    ));
                    
                    if (!empty($situation_terms) && !is_wp_error($situation_terms)) :
                        foreach (array_slice($situation_terms, 0, 6) as $situation_term) :
                    ?>
                            <a href="<?php echo esc_url(get_post_type_archive_link('store') . '?situation_filter[]=' . $situation_term->slug . '&active_tab=situation'); ?>" class="medi-situation-item">
                                <div class="medi-situation-item__image">
                                    <?php
                                    $custom_image = get_field('situation_image', 'situation_' . $situation_term->term_id);
                                    if ($custom_image) {
                                        echo '<img src="' . esc_url($custom_image['url']) . '" alt="' . esc_attr($situation_term->name) . '">';
                                    } else {
                                        echo '<div class="default-situation-bg"></div>';
                                    }
                                    ?>
                                    <div class="medi-situation-item__overlay">
                                        <span class="medi-situation-item__text"><?php echo esc_html($situation_term->name); ?></span>
                                    </div>
                                </div>
                            </a>
                    <?php 
                        endforeach;
                    endif;
                    ?>
                </div>
            </div>
        </section>

        <?php // --- ジャンルで選ぶセクション --- ?>
        <section class="medi-genre-section">
            <div class="container">
                <h2 class="medi-section-title">ジャンルで選ぶ</h2>
                <p class="medi-section-subtitle">カフェから本格ディナーまで、様々なジャンルのお店をご紹介</p>
                
                <div class="medi-genre-grid">
                    <?php
                    $genre_terms = get_terms(array(
                        'taxonomy' => 'genre',
                        'hide_empty' => false,
                        'orderby' => 'name',
                        'order' => 'ASC'
                    ));
                    
                    if (!empty($genre_terms) && !is_wp_error($genre_terms)) :
                        foreach ($genre_terms as $genre_term) :
                    ?>
                            <a href="<?php echo esc_url(get_post_type_archive_link('store') . '?genre_filter[]=' . $genre_term->slug . '&active_tab=genre'); ?>" class="medi-genre-item">
                                <?php echo esc_html($genre_term->name); ?>
                            </a>
                    <?php 
                        endforeach;
                    endif;
                    ?>
                </div>
            </div>
        </section>

    </div>

    <?php // --- サイドバー（広告欄） --- ?>
    <aside class="medi-sidebar">
        <div class="container">
            <?php // TCD広告管理機能の活用 ?>
            <?php if (isset($dp_options['show_ad_top']) && $dp_options['show_ad_top'] && $dp_options['ad_code_top']) : ?>
                <div class="medi-sidebar-section">
                    <h3 class="medi-sidebar-title">広告</h3>
                    <div class="medi-sidebar-content">
                        <?php echo $dp_options['ad_code_top']; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php // サイドバーウィジェット ?>
            <?php if (is_active_sidebar('homepage-sidebar')) : ?>
                <?php dynamic_sidebar('homepage-sidebar'); ?>
            <?php endif; ?>
        </div>
    </aside>

</div>

<?php get_footer(); ?>