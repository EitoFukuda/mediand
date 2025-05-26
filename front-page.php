<?php
/**
 * The template for displaying the front page (homepage)
 * TCD GENSEN integrated version with custom sections
 * 
 * @package medi& GENSEN Child
 */

get_header();

// --- TCD GENSEN テーマオプション取得（エラーハンドリング強化版） ---
global $dp_options;
if (!$dp_options) $dp_options = get_desing_plus_option();

// オプション値の安全な取得用ヘルパー関数
function get_safe_option($options, $key, $default = '') {
    return isset($options[$key]) ? $options[$key] : $default;
}

// デバッグ情報の出力を制御（本番環境では削除）
$show_debug = defined('WP_DEBUG') && WP_DEBUG && current_user_can('administrator');

if ($show_debug) {
    // デバッグ情報をHTMLコメントとして出力
    echo "<!-- Debug Info:\n";
    echo "Header Content Type: " . get_safe_option($dp_options, 'header_content_type', 'not_set') . "\n";
    echo "Header BG Image: " . get_safe_option($dp_options, 'header_bg_image', 'not_set') . "\n";
    echo "Header BG Video: " . get_safe_option($dp_options, 'header_bg_video', 'not_set') . "\n";
    echo "-->";
}

// アイコン・画像パス
$icon_base_path = get_stylesheet_directory_uri() . '/assets/icons/';
$images_base_path = get_stylesheet_directory_uri() . '/assets/images/';
?>

<div class="homepage-wrapper">
    
    <?php // --- カスタムヒーローセクション --- ?>
    <section class="medi-hero-section" <?php 
        $hero_bg_image = get_safe_option($dp_options, 'hero_background_image', '');
        if ($hero_bg_image) : ?>style="background-image: url('<?php echo esc_url($hero_bg_image); ?>');"<?php endif; ?>>
        
        <?php 
        $hero_video_url = get_safe_option($dp_options, 'hero_video_url', '');
        if ($hero_video_url) : ?>
        <video class="medi-hero-video" autoplay muted loop playsinline>
            <source src="<?php echo esc_url($hero_video_url); ?>" type="video/mp4">
        </video>
        <?php endif; ?>
        
        <div class="medi-hero-overlay"></div>
        
        <div class="container">
            <div class="medi-hero-content">
                <h1 class="medi-hero-title">
                    <span class="title-sns">SNS</span>
                    <span class="title-kara">から</span>
                    <span class="title-real">リアルへ</span>
                </h1>
                <p class="medi-hero-description">
                    SNSで見つけた素敵なお店を、実際に体験してみませんか？<br>
                    あなたの気分やシチュエーションに合わせて、最適なお店を見つけましょう。
                </p>
                
                <div class="medi-hero-search">
                    <form action="<?php echo esc_url(home_url('/')); ?>" method="get" class="medi-hero-search-form">
                        <input type="hidden" name="post_type" value="store">
                        
                        <div class="medi-hero-search-wrapper">
                            <div class="medi-hero-search-selects">
                                <?php // 都道府県ドロップダウン ?>
                                <select name="prefecture" class="medi-hero-select">
                                    <option value="">都道府県を選択</option>
                                    <?php
                                    $prefectures = get_terms(array(
                                        'taxonomy' => 'prefecture',
                                        'hide_empty' => false,
                                        'parent' => !0, // 親タームを除外
                                        'orderby' => 'name',
                                        'order' => 'ASC'
                                    ));
                                    if (!is_wp_error($prefectures) && !empty($prefectures)) :
                                        foreach($prefectures as $pref) :
                                    ?>
                                        <option value="<?php echo esc_attr($pref->slug); ?>"><?php echo esc_html($pref->name); ?></option>
                                    <?php 
                                        endforeach;
                                    endif;
                                    ?>
                                </select>
                                
                                <?php // ジャンルドロップダウン ?>
                                <select name="genre" class="medi-hero-select">
                                    <option value="">ジャンルを選択</option>
                                    <?php
                                    $genres = get_terms(array(
                                        'taxonomy' => 'genre',
                                        'hide_empty' => false,
                                        'orderby' => 'name',
                                        'order' => 'ASC'
                                    ));
                                    if (!is_wp_error($genres) && !empty($genres)) :
                                        foreach($genres as $genre) :
                                    ?>
                                        <option value="<?php echo esc_attr($genre->slug); ?>"><?php echo esc_html($genre->name); ?></option>
                                    <?php 
                                        endforeach;
                                    endif;
                                    ?>
                                </select>
                            </div>
                            
                            <input type="search" name="s" placeholder="キーワードを入力" class="medi-hero-search-input">
                            <button type="submit" class="medi-hero-search-button">検索</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <div class="homepage-content-wrapper">
        
        <?php // --- おすすめセクション（スライダー版） --- ?>
        <section class="medi-recommend-section">
            <div class="container">
                <h2 class="medi-section-title">新着店舗</h2>
                <p class="medi-section-subtitle">最新の登録店舗をご紹介！</p>
                
                <div class="medi-recommend-slider-wrapper">
                    <div class="medi-recommend-slider" id="recommendSlider">
                        <?php
                        // 新着店舗10件を取得
                        $recommend_query = new WP_Query(array(
                            'post_type' => 'store',
                            'posts_per_page' => 10,
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
                        ?>
                                <div class="medi-recommend-slide">
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
                                                <?php if ($genre_terms && !is_wp_error($genre_terms)) : ?>
                                                    <div class="medi-recommend-card__tags">
                                                        <?php foreach(array_slice($genre_terms, 0, 2) as $term) : ?>
                                                            <span class="tag"><?php echo esc_html($term->name); ?></span>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </a>
                                    </article>
                                </div>
                        <?php 
                            endwhile; 
                            wp_reset_postdata();
                        else :
                        ?>
                            <div class="medi-recommend-slide">
                                <p class="no-stores-message">まだ店舗が登録されていません。</p>
                            </div>
                        <?php
                        endif;
                        ?>
                    </div>
                    <button class="medi-slider-nav medi-slider-prev" id="recommendPrev">‹</button>
                    <button class="medi-slider-nav medi-slider-next" id="recommendNext">›</button>
                </div>
            </div>
        </section>

        <?php // --- 地域から選ぶセクション（並び順修正） --- ?>
        <section class="medi-region-section">
            <div class="container">
                <h2 class="medi-section-title">地域から選ぶ</h2>
                <p class="medi-section-subtitle">全国各地の魅力的なお店を地域別にご紹介</p>
                
                <div class="medi-region-accordion">
                    <?php
                    // 地方の順番を定義
                    $region_order = array(
                        '北海道/東北',
                        '関東',
                        '中部',
                        '近畿',
                        '中国',
                        '四国',
                        '九州',
                        '沖縄'
                    );
                    
                    $region_terms = get_terms(array(
                        'taxonomy' => 'prefecture',
                        'parent' => 0,
                        'hide_empty' => false
                    ));
                    
                    if (!is_wp_error($region_terms) && !empty($region_terms)) :
                        // 並び替え処理
                        $ordered_regions = array();
                        foreach($region_order as $region_name) {
                            foreach($region_terms as $term) {
                                if($term->name === $region_name || strpos($term->name, $region_name) !== false) {
                                    $ordered_regions[] = $term;
                                    break;
                                }
                            }
                        }
                        
                        // 見つからなかった地方を追加
                        foreach($region_terms as $term) {
                            $found = false;
                            foreach($ordered_regions as $ordered) {
                                if($ordered->term_id === $term->term_id) {
                                    $found = true;
                                    break;
                                }
                            }
                            if(!$found) {
                                $ordered_regions[] = $term;
                            }
                        }
                        
                        foreach ($ordered_regions as $region_term) :
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
                                        <?php else : ?>
                                            <p class="no-prefectures-message">この地方の都道府県は登録されていません。</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                    <?php 
                        endforeach;
                    else :
                    ?>
                        <p class="no-regions-message">地方が登録されていません。</p>
                    <?php
                    endif;
                    ?>
                </div>
            </div>
        </section>

        <?php // --- ココロで選ぶセクション（アイコン対応） --- ?>
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
                            // ACFでタクソノミーにカスタムフィールドを追加している場合
                            $feeling_icon = get_field('feeling_icon', 'feeling_' . $feeling_term->term_id);
                    ?>
                            <a href="<?php echo esc_url(get_post_type_archive_link('store') . '?feeling_filter[]=' . $feeling_term->slug . '&active_tab=feeling'); ?>" class="medi-feeling-item">
                                <div class="medi-feeling-item__icon">
                                    <?php if ($feeling_icon && is_array($feeling_icon) && isset($feeling_icon['url'])) : ?>
                                        <img src="<?php echo esc_url($feeling_icon['url']); ?>" alt="<?php echo esc_attr($feeling_term->name); ?>">
                                    <?php else : ?>
                                        <div class="default-feeling-icon">💝</div>
                                    <?php endif; ?>
                                </div>
                                <span class="medi-feeling-item__text"><?php echo esc_html($feeling_term->name); ?></span>
                            </a>
                    <?php 
                        endforeach;
                    else :
                    ?>
                        <p class="no-feelings-message">「ココロで選ぶ」項目が登録されていません。</p>
                    <?php
                    endif;
                    ?>
                </div>
            </div>
        </section>

        <?php // --- シチュエーションで選ぶセクション（項目名表示・画像カスタマイズ対応） --- ?>
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
                        foreach ($situation_terms as $situation_term) :
                            // ACFでタクソノミーにカスタムフィールドを追加している場合
                            $situation_image = get_field('situation_image', 'situation_' . $situation_term->term_id);
                    ?>
                            <a href="<?php echo esc_url(get_post_type_archive_link('store') . '?situation_filter[]=' . $situation_term->slug . '&active_tab=situation'); ?>" class="medi-situation-item">
                                <div class="medi-situation-item__image">
                                    <?php if ($situation_image && is_array($situation_image) && isset($situation_image['url'])) : ?>
                                        <img src="<?php echo esc_url($situation_image['url']); ?>" alt="<?php echo esc_attr($situation_term->name); ?>">
                                    <?php else : ?>
                                        <div class="default-situation-bg"></div>
                                    <?php endif; ?>
                                </div>
                                <div class="medi-situation-item__overlay">
                                    <span class="medi-situation-item__text"><?php echo esc_html($situation_term->name); ?></span>
                                </div>
                            </a>
                    <?php 
                        endforeach;
                    else :
                    ?>
                        <p class="no-situations-message">「シチュエーションで選ぶ」項目が登録されていません。</p>
                    <?php
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
                                <span><?php echo esc_html($genre_term->name); ?></span>
                            </a>
                    <?php 
                        endforeach;
                    else :
                    ?>
                        <p class="no-genres-message">ジャンルが登録されていません。</p>
                    <?php
                    endif;
                    ?>
                </div>
            </div>
        </section>

    </div>

    <?php // --- サイドバー（広告欄） --- ?>
    <aside class="medi-sidebar">
        <div class="container">
            <?php 
            // TCD広告管理機能の活用（安全なアクセス）
            $show_ad_top = get_safe_option($dp_options, 'show_ad_top', false);
            $ad_code_top = get_safe_option($dp_options, 'ad_code_top', '');
            
            if ($show_ad_top && $ad_code_top) : 
            ?>
                <div class="medi-sidebar-section">
                    <h3 class="medi-sidebar-title">広告</h3>
                    <div class="medi-sidebar-content">
                        <?php echo $ad_code_top; ?>
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