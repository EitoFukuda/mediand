<?php
/**
 * The template for displaying the front page (homepage)
 * Modern futuristic design version with full-screen hero
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

// アイコン・画像パス
$icon_base_path = get_stylesheet_directory_uri() . '/assets/icons/';
$images_base_path = get_stylesheet_directory_uri() . '/assets/images/';
?>

<div class="homepage-wrapper">
    
<?php // --- フルスクリーンヒーローセクション --- ?>
<!-- 40行目付近のヒーローセクション開始タグを変更 -->
<section class="medi-hero-section medi-hero-section--fullscreen medi-hero-section--video">
    <!-- デスクトップ用動画背景 -->
    <div class="medi-hero-video-container">
        <video class="medi-hero-video" autoplay muted playsinline>
            <source src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/front_hero.mp4" type="video/mp4">
            <!-- フォールバック画像 -->
            <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/front_hero.png" alt="ヒーロー画像" />
        </video>
    </div>
    
    <!-- モバイル用ヒーロー画像（変更なし） -->
    <div class="medi-hero-mobile-image">
        <img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/front_hero_mobile.png" alt="ヒーロー画像" />
    </div>
        
        <div class="container medi-hero-section__container">
            
            <!-- 検索フォーム（下部） -->
            <div class="medi-hero-section__search-wrapper">
                <div class="medi-hero-section__search-container">
                    <form action="<?php echo esc_url(home_url('/')); ?>" method="get" class="medi-hero-search-form">
                        <input type="hidden" name="post_type" value="store">
                        
                        <div class="search-form-inner">
                            <div class="search-form-row">
                                <!-- 都道府県セレクト -->
<!-- 都道府県セレクト（階層対応版） -->
<div class="search-form-field">
    <label class="search-form-label">エリア</label>
    <div class="custom-select-wrapper">
        <select name="prefecture_filter" class="custom-select" id="prefecture-select">
            <option value="">都道府県を選択</option>
            <?php
            // 地方の順序を定義
            $region_order = array(
                '北海道/東北' => array('北海道', '青森県', '岩手県', '宮城県', '秋田県', '山形県', '福島県'),
                '関東' => array('茨城県', '栃木県', '群馬県', '埼玉県', '千葉県', '東京都', '神奈川県'),
                '中部' => array('新潟県', '富山県', '石川県', '福井県', '山梨県', '長野県', '岐阜県', '静岡県', '愛知県'),
                '近畿' => array('三重県', '滋賀県', '京都府', '大阪府', '兵庫県', '奈良県', '和歌山県'),
                '中国' => array('鳥取県', '島根県', '岡山県', '広島県', '山口県'),
                '四国' => array('徳島県', '香川県', '愛媛県', '高知県'),
                '九州' => array('福岡県', '佐賀県', '長崎県', '熊本県', '大分県', '宮崎県', '鹿児島県'),
                '沖縄' => array('沖縄県')
            );
            
            // 地方（親ターム）を取得
            $region_terms = get_terms(array(
                'taxonomy' => 'prefecture',
                'hide_empty' => false,
                'parent' => 0,
                'orderby' => 'name',
                'order' => 'ASC'
            ));
            
            if (!is_wp_error($region_terms) && !empty($region_terms)) :
                foreach($region_order as $region_name => $expected_prefs) :
                    // 該当する地方タームを探す
                    $current_region = null;
                    foreach($region_terms as $region) {
                        if($region->name === $region_name || strpos($region->name, $region_name) !== false) {
                            $current_region = $region;
                            break;
                        }
                    }
                    
                    if($current_region) :
                        // 都道府県（子ターム）を取得
                        $prefectures = get_terms(array(
                            'taxonomy' => 'prefecture',
                            'hide_empty' => false,
                            'parent' => $current_region->term_id,
                            'orderby' => 'name',
                            'order' => 'ASC'
                        ));
                        
                        if (!is_wp_error($prefectures) && !empty($prefectures)) :
            ?>
                            <optgroup label="<?php echo esc_attr($region_name); ?>">
                                <?php foreach($prefectures as $pref) : ?>
                                    <option value="<?php echo esc_attr($pref->slug); ?>"><?php echo esc_html($pref->name); ?></option>
                                <?php endforeach; ?>
                            </optgroup>
            <?php 
                        endif;
                    endif;
                endforeach;
            endif;
            ?>
        </select>
        <div class="select-arrow">
            <svg width="12" height="8" viewBox="0 0 12 8" fill="none">
                <path d="M1 1L6 6L11 1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
    </div>
</div>
                                
<!-- ジャンルセレクト（階層対応版） -->
<div class="search-form-field">
    <label class="search-form-label">ジャンル</label>
    <div class="custom-select-wrapper">
        <select name="genre_filter" class="custom-select" id="genre-select">
            <option value="">ジャンルを選択</option>
            <?php
            // ジャンルの親タームを取得
            $genre_parent_terms = get_terms(array(
                'taxonomy' => 'genre',
                'hide_empty' => false,
                'parent' => 0,
                'orderby' => 'name',
                'order' => 'ASC'
            ));
            
            if (!is_wp_error($genre_parent_terms) && !empty($genre_parent_terms)) :
                foreach($genre_parent_terms as $parent_genre) :
                    // 子ジャンルを取得
                    $child_genres = get_terms(array(
                        'taxonomy' => 'genre',
                        'hide_empty' => false,
                        'parent' => $parent_genre->term_id,
                        'orderby' => 'name',
                        'order' => 'ASC'
                    ));
                    
                    if (!is_wp_error($child_genres) && !empty($child_genres)) :
            ?>
                        <optgroup label="<?php echo esc_attr($parent_genre->name); ?>">
                            <?php foreach($child_genres as $child_genre) : ?>
                                <option value="<?php echo esc_attr($child_genre->slug); ?>"><?php echo esc_html($child_genre->name); ?></option>
                            <?php endforeach; ?>
                        </optgroup>
            <?php 
                    else :
                        // 子がない場合は親をそのまま表示
            ?>
                        <option value="<?php echo esc_attr($parent_genre->slug); ?>"><?php echo esc_html($parent_genre->name); ?></option>
            <?php
                    endif;
                endforeach;
            else :
                // 親がない場合は全ジャンルを表示
                $all_genres = get_terms(array(
                    'taxonomy' => 'genre',
                    'hide_empty' => false,
                    'orderby' => 'name',
                    'order' => 'ASC'
                ));
                if (!is_wp_error($all_genres) && !empty($all_genres)) :
                    foreach($all_genres as $genre) :
            ?>
                        <option value="<?php echo esc_attr($genre->slug); ?>"><?php echo esc_html($genre->name); ?></option>
            <?php 
                    endforeach;
                endif;
            endif;
            ?>
        </select>
        <div class="select-arrow">
            <svg width="12" height="8" viewBox="0 0 12 8" fill="none">
                <path d="M1 1L6 6L11 1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
    </div>
</div>
                                
                                <!-- キーワード検索 -->
                                <div class="search-form-field search-form-field--keyword">
                                    <label class="search-form-label">キーワード</label>
                                    <div class="search-input-wrapper">
                                        <input type="search" name="s" placeholder="店名・特徴・気分など" class="search-input" />
                                        <div class="search-input-icon">
                                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                <path d="M9 17A8 8 0 1 0 9 1a8 8 0 0 0 0 16Z" stroke="currentColor" stroke-width="2"/>
                                                <path d="M17 17L13 13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- 検索ボタン -->
                            <div class="search-form-button-wrapper">
                                <button type="submit" class="search-form-button">
                                    <span class="button-text">検索する</span>
                                    <span class="button-glow"></span>
                                    <div class="button-particles">
                                        <span class="particle particle-1"></span>
                                        <span class="particle particle-2"></span>
                                        <span class="particle particle-3"></span>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- スクロールインジケーター -->
            <div class="scroll-indicator">
                <div class="scroll-indicator__text">Scroll Down</div>
                <div class="scroll-indicator__arrow">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M7 13L12 18L17 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M7 6L12 11L17 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>
        </div>
    </section>

    <!-- モバイル専用検索フォーム -->
    <section class="medi-mobile-search-section">
        <div class="container">
            <div class="medi-hero-section__search-container">
                <form action="<?php echo esc_url(home_url('/')); ?>" method="get" class="medi-hero-search-form">
                    <input type="hidden" name="post_type" value="store">
                    <div class="search-form-inner">
                        <div class="search-form-row">
                            <!-- 都道府県セレクト（階層対応版） -->
                            <div class="search-form-field">
                                <label class="search-form-label">エリア</label>
                                <div class="custom-select-wrapper">
                                    <select name="prefecture_filter" class="custom-select" id="prefecture-select-mobile">
                                        <option value="">都道府県を選択</option>
                                        <?php
                                        // 地方の順序を定義
                                        $region_order = array(
                                            '北海道/東北' => array('北海道', '青森県', '岩手県', '宮城県', '秋田県', '山形県', '福島県'),
                                            '関東' => array('茨城県', '栃木県', '群馬県', '埼玉県', '千葉県', '東京都', '神奈川県'),
                                            '中部' => array('新潟県', '富山県', '石川県', '福井県', '山梨県', '長野県', '岐阜県', '静岡県', '愛知県'),
                                            '近畿' => array('三重県', '滋賀県', '京都府', '大阪府', '兵庫県', '奈良県', '和歌山県'),
                                            '中国' => array('鳥取県', '島根県', '岡山県', '広島県', '山口県'),
                                            '四国' => array('徳島県', '香川県', '愛媛県', '高知県'),
                                            '九州' => array('福岡県', '佐賀県', '長崎県', '熊本県', '大分県', '宮崎県', '鹿児島県'),
                                            '沖縄' => array('沖縄県')
                                        );
                                        $region_terms = get_terms(array(
                                            'taxonomy' => 'prefecture',
                                            'hide_empty' => false,
                                            'parent' => 0,
                                            'orderby' => 'name',
                                            'order' => 'ASC'
                                        ));
                                        if (!is_wp_error($region_terms) && !empty($region_terms)) :
                                            foreach($region_order as $region_name => $expected_prefs) :
                                                $current_region = null;
                                                foreach($region_terms as $region) {
                                                    if($region->name === $region_name || strpos($region->name, $region_name) !== false) {
                                                        $current_region = $region;
                                                        break;
                                                    }
                                                }
                                                if($current_region) :
                                                    $prefectures = get_terms(array(
                                                        'taxonomy' => 'prefecture',
                                                        'hide_empty' => false,
                                                        'parent' => $current_region->term_id,
                                                        'orderby' => 'name',
                                                        'order' => 'ASC'
                                                    ));
                                                    if (!is_wp_error($prefectures) && !empty($prefectures)) :
                            ?>
                                                    <optgroup label="<?php echo esc_attr($region_name); ?>">
                                                        <?php foreach($prefectures as $pref) : ?>
                                                            <option value="<?php echo esc_attr($pref->slug); ?>"><?php echo esc_html($pref->name); ?></option>
                                                        <?php endforeach; ?>
                                                    </optgroup>
                            <?php 
                                                    endif;
                                                endif;
                                            endforeach;
                                        endif;
                                        ?>
                                    </select>
                                    <div class="select-arrow">
                                        <svg width="12" height="8" viewBox="0 0 12 8" fill="none">
                                            <path d="M1 1L6 6L11 1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <!-- ジャンルセレクト（階層対応版） -->
                            <div class="search-form-field">
                                <label class="search-form-label">ジャンル</label>
                                <div class="custom-select-wrapper">
                                    <select name="genre_filter" class="custom-select" id="genre-select-mobile">
                                        <option value="">ジャンルを選択</option>
                                        <?php
                                        $genre_parent_terms = get_terms(array(
                                            'taxonomy' => 'genre',
                                            'hide_empty' => false,
                                            'parent' => 0,
                                            'orderby' => 'name',
                                            'order' => 'ASC'
                                        ));
                                        if (!is_wp_error($genre_parent_terms) && !empty($genre_parent_terms)) :
                                            foreach($genre_parent_terms as $parent_genre) :
                                                $child_genres = get_terms(array(
                                                    'taxonomy' => 'genre',
                                                    'hide_empty' => false,
                                                    'parent' => $parent_genre->term_id,
                                                    'orderby' => 'name',
                                                    'order' => 'ASC'
                                                ));
                                                if (!is_wp_error($child_genres) && !empty($child_genres)) :
                            ?>
                                            <optgroup label="<?php echo esc_attr($parent_genre->name); ?>">
                                                <?php foreach($child_genres as $child_genre) : ?>
                                                    <option value="<?php echo esc_attr($child_genre->slug); ?>"><?php echo esc_html($child_genre->name); ?></option>
                                                <?php endforeach; ?>
                                            </optgroup>
                            <?php 
                                            else :
                            ?>
                                                <option value="<?php echo esc_attr($parent_genre->slug); ?>"><?php echo esc_html($parent_genre->name); ?></option>
                            <?php
                                            endif;
                                        endforeach;
                                    else :
                                        $all_genres = get_terms(array(
                                            'taxonomy' => 'genre',
                                            'hide_empty' => false,
                                            'orderby' => 'name',
                                            'order' => 'ASC'
                                        ));
                                        if (!is_wp_error($all_genres) && !empty($all_genres)) :
                                            foreach($all_genres as $genre) :
                            ?>
                                                <option value="<?php echo esc_attr($genre->slug); ?>"><?php echo esc_html($genre->name); ?></option>
                            <?php 
                                            endforeach;
                                        endif;
                                    endif;
                                    ?>
                                    </select>
                                    <div class="select-arrow">
                                        <svg width="12" height="8" viewBox="0 0 12 8" fill="none">
                                            <path d="M1 1L6 6L11 1" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <!-- キーワード検索 -->
                            <div class="search-form-field search-form-field--keyword">
                                <label class="search-form-label">キーワード</label>
                                <div class="search-input-wrapper">
                                    <input type="search" name="s" placeholder="店名・特徴・気分など" class="search-input" />
                                    <div class="search-input-icon">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                            <path d="M9 17A8 8 0 1 0 9 1a8 8 0 0 0 0 16Z" stroke="currentColor" stroke-width="2"/>
                                            <path d="M17 17L13 13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="search-form-button-wrapper">
                            <button type="submit" class="search-form-button">
                                <span class="button-text">検索する</span>
                                <span class="button-glow"></span>
                                <div class="button-particles">
                                    <span class="particle particle-1"></span>
                                    <span class="particle particle-2"></span>
                                    <span class="particle particle-3"></span>
                                </div>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <?php // --- メインコンテンツエリア --- ?>
    <div class="homepage-content-wrapper">
        <div class="main-content-with-sidebar">
            <div class="main-content-area">
        
            <?php // --- 新着店舗セクション（カルーセルスライダー版） --- ?>
<section class="medi-recommend-section medi-section--enhanced">
    <div class="container">
        <div class="medi-section-header">
            <h2 class="medi-section-title medi-section-title--glow">
                <span class="title-text">新着店舗</span>
                <span class="title-decoration"></span>
            </h2>
            <p class="medi-section-subtitle">最新の登録店舗をご紹介！話題のスポットをいち早くチェック</p>
        </div>
        
        <div class="medi-recommend-slider-wrapper">
            <button type="button" class="medi-slider-nav medi-slider-prev" id="recommendPrev" aria-label="前へ">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
            
            <div class="medi-recommend-slider-container">
                <div class="medi-recommend-slider" id="recommendSlider">
                <?php
$recommend_query = new WP_Query(array(
    'post_type' => 'store',
    'posts_per_page' => 6,  // 10から8に変更
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
                            
                            $genre_terms = get_the_terms($store_id, 'genre');
                    ?>
                            <div class="medi-recommend-slide">
                                <article class="medi-recommend-card medi-recommend-card--enhanced">
                                    <a href="<?php echo esc_url($store_permalink); ?>" class="medi-recommend-card__link">
                                        <div class="medi-recommend-card__image-wrapper">
                                            <div class="medi-recommend-card__image">
                                                <?php if ($store_thumbnail) : ?>
                                                    <img src="<?php echo esc_url($store_thumbnail); ?>" alt="<?php echo esc_attr($store_title); ?>">
                                                <?php else : ?>
                                                    <div class="medi-recommend-card__no-image">
                                                        <div class="no-image-icon">
                                                            <svg width="48" height="48" viewBox="0 0 48 48" fill="none">
                                                                <rect x="8" y="8" width="32" height="24" rx="2" stroke="currentColor" stroke-width="2"/>
                                                                <circle cx="16" cy="18" r="3" stroke="currentColor" stroke-width="2"/>
                                                                <path d="M28 25L32 21L40 29V32H8V25L12 21L16 25" stroke="currentColor" stroke-width="2"/>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="card-glow-effect"></div>
                                        </div>
                                        
                                        <div class="medi-recommend-card__content">
                                            <h3 class="medi-recommend-card__title"><?php echo esc_html($store_title); ?></h3>
                                            
                                            <?php if ($prefecture_display) : ?>
                                                <p class="medi-recommend-card__location">
                                                    <span class="location-icon">📍</span>
                                                    <?php echo $prefecture_display; ?>
                                                </p>
                                            <?php endif; ?>
                                            
                                            <?php if ($genre_terms && !is_wp_error($genre_terms)) : ?>
                                                <div class="medi-recommend-card__tags">
                                                    <?php foreach(array_slice($genre_terms, 0, 2) as $term) : ?>
                                                        <span class="tag tag--glow"><?php echo esc_html($term->name); ?></span>
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
                        <div class="no-content-message">
                            <p>まだ店舗が登録されていません。</p>
                        </div>
                    <?php
                    endif;
                    ?>
                </div>
            </div>
            
            <button type="button" class="medi-slider-nav medi-slider-next" id="recommendNext" aria-label="次へ">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </div>
    </div>
</section>

                <?php // --- 地域から選ぶセクション --- ?>
                <section class="medi-region-section medi-section--enhanced">
                    <div class="container">
                        <div class="medi-section-header">
                            <h2 class="medi-section-title medi-section-title--glow">
                                <span class="title-text">地域から選ぶ</span>
                                <span class="title-decoration"></span>
                            </h2>
                            <p class="medi-section-subtitle">全国各地の魅力的なお店を地域別にご紹介</p>
                        </div>
                        
                        <div class="medi-region-accordion medi-region-accordion--enhanced">
                            <?php
                            $region_order = array(
                                '北海道/東北', '関東', '中部', '近畿', '関西', '中国', '四国', '九州', '沖縄'
                            );
                            
                            $region_terms = get_terms(array(
                                'taxonomy' => 'prefecture',
                                'parent' => 0,
                                'hide_empty' => false
                            ));
                            
                            if (!is_wp_error($region_terms) && !empty($region_terms)) :
                                $ordered_regions = array();
                                foreach($region_order as $region_name) {
                                    foreach($region_terms as $term) {
                                        if($term->name === $region_name || strpos($term->name, $region_name) !== false) {
                                            $ordered_regions[] = $term;
                                            break;
                                        }
                                    }
                                }
                                
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
                                            <span class="medi-region-accordion__icon">
                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                    <path d="M4 6L8 10L12 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </span>
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

                <?php // --- その他のセクション（ココロで選ぶ、シチュエーション、ジャンル）--- ?>
                <section class="medi-situation-section medi-section--enhanced">
    <div class="container">
        <div class="medi-section-header">
            <h2 class="medi-section-title medi-section-title--glow">
                <span class="title-text">シチュエーションで選ぶ</span>
                <span class="title-decoration"></span>
            </h2>
            <p class="medi-section-subtitle">大切な人との時間や、特別な日にぴったりのお店を見つけましょう。</p>
        </div>
        
        <div class="medi-situation-grid medi-grid--enhanced">
        <?php 
$situation_terms = get_terms(array(
    'taxonomy' => 'situation',
    'hide_empty' => true,
    'orderby' => 'count',
    'order' => 'DESC',
    'number' => 8
));
            
            if (!empty($situation_terms) && !is_wp_error($situation_terms)) : 
                foreach ($situation_terms as $term) : 
            ?>
                    <a href="<?php echo esc_url(get_post_type_archive_link('store') . '?situation_filter[]=' . $term->slug . '&active_tab=situation'); ?>" class="medi-situation-item medi-item--enhanced">
                        <div class="medi-situation-item__image">
                            <?php 
                            $image = get_field('situation_image', 'situation_' . $term->term_id);
                            if ($image && is_array($image) && isset($image['url'])) : ?>
                                <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($term->name); ?>">
                            <?php else : ?>
                                <div class="default-situation-bg"></div>
                            <?php endif; ?>
                        </div>
                        <div class="medi-situation-item__overlay">
                            <span class="medi-situation-item__text"><?php echo esc_html($term->name); ?></span>
                        </div>
                    </a>
            <?php 
                endforeach; 
            else : 
            ?>
                <p class="no-terms-message">シチュエーション項目が登録されていません。</p>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php
// 残りのセクションも同様に近未来デザインを適用
$sections = [
    'feeling' => ['title' => 'ココロで選ぶ', 'subtitle' => 'あなたの気持ちに寄り添う、特別な体験を見つけよう。'],               
    'genre' => ['title' => 'ジャンルで選ぶ', 'subtitle' => 'カフェから本格ディナーまで、様々なジャンルのお店をご紹介']
];
                
                foreach($sections as $section_key => $section_data) :
                    if ($section_key === 'feeling') {
                        $terms = get_terms(array(
                            'taxonomy' => 'feeling',
                            'hide_empty' => true,
                            'orderby' => 'count',
                            'order' => 'DESC',
                            'number' => 12
                        ));
                    } elseif ($section_key === 'genre') {
                        $terms = get_terms(array(
                            'taxonomy' => 'genre',
                            'hide_empty' => true,
                            'orderby' => 'count',
                            'order' => 'DESC',
                            'number' => 20,
                            'child_of' => 0
                        ));
                    } else {
                        $terms = get_terms(array(
                            'taxonomy' => $section_key,
                            'hide_empty' => false,
                            'orderby' => 'name',
                            'order' => 'ASC'
                        ));
                    }
                ?>
                    <section class="medi-<?php echo $section_key; ?>-section medi-section--enhanced">
                        <div class="container">
                            <div class="medi-section-header">
                                <h2 class="medi-section-title medi-section-title--glow">
                                    <span class="title-text"><?php echo $section_data['title']; ?></span>
                                    <span class="title-decoration"></span>
                                </h2>
                                <p class="medi-section-subtitle"><?php echo $section_data['subtitle']; ?></p>
                            </div>
                            
                            <div class="medi-<?php echo $section_key; ?>-grid medi-grid--enhanced">
                                
                                <?php if (!empty($terms) && !is_wp_error($terms)) : ?>
                                    <?php foreach ($terms as $term) : ?>
                                        <a href="<?php echo esc_url(get_post_type_archive_link('store') . '?' . $section_key . '_filter[]=' . $term->slug . '&active_tab=' . $section_key); ?>" class="medi-<?php echo $section_key; ?>-item medi-item--enhanced">
                                            <?php if ($section_key == 'feeling') : ?>
                                                <div class="medi-feeling-item__icon">
                                                    <?php 
                                                    $icon = get_field('feeling_icon', $section_key . '_' . $term->term_id);
                                                    if ($icon && is_array($icon) && isset($icon['url'])) : ?>
                                                        <img src="<?php echo esc_url($icon['url']); ?>" alt="<?php echo esc_attr($term->name); ?>">
                                                    <?php else : ?>
                                                        <div class="default-feeling-icon">💝</div>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                            <span class="medi-<?php echo $section_key; ?>-item__text"><?php echo esc_html($term->name); ?></span>
                                        </a>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <p class="no-terms-message"><?php echo $section_data['title']; ?>項目が登録されていません。</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </section>
                <?php endforeach; ?>

            </div>
            
            <?php // --- 右サイドバー（広告欄） --- ?>
            <aside class="homepage-sidebar">
                <div class="sidebar-sticky">
                    <div class="sidebar-ad-area">
                        <h3 class="sidebar-title">PR</h3>
                        <?php
                        if (function_exists('adinserter')) {
                            echo adinserter(1);
                        }
                        ?>
                    </div>
                    
                    <div class="sidebar-ad-area">
                        <?php
                        if (function_exists('adinserter')) {
                            echo adinserter(2);
                        }
                        ?>
                    </div>
                    
                    <!-- <div class="sidebar-ad-area">
                        <?php
                        if (function_exists('adinserter')) {
                            echo adinserter(3);
                        }
                        ?>
                    </div> -->
                </div>
            </aside>
        </div>
    </div>

    <?php // --- フッター広告エリア --- ?>
    <aside class="medi-sidebar">
        <div class="container">
            <?php 
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

            <?php if (is_active_sidebar('homepage-sidebar')) : ?>
                <?php dynamic_sidebar('homepage-sidebar'); ?>
            <?php endif; ?>
        </div>
    </aside>

</div>

<?php get_footer(); ?>