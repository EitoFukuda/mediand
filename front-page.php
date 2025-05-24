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
    
<?php // --- カスタムヒーローセクション --- ?>
<section class="medi-hero-section">
    <?php
    // TCDテーマオプションからヘッダーコンテンツを取得
    global $dp_options;
    if (!$dp_options) $dp_options = get_desing_plus_option();
    
    // ヘッダーコンテンツタイプを確認
    $header_content_type = isset($dp_options['header_content_type']) ? $dp_options['header_content_type'] : 'type1';
    
    // 動画の場合
    if ($header_content_type === 'type2' && isset($dp_options['header_video']) && $dp_options['header_video']) : ?>
        <video class="medi-hero-video" autoplay muted loop playsinline>
            <source src="<?php echo esc_url(wp_get_attachment_url($dp_options['header_video'])); ?>" type="video/mp4">
        </video>
    <?php 
    // 画像スライダーの場合
    elseif ($header_content_type === 'type1' && isset($dp_options['slider_image'])) : ?>
        <div class="medi-hero-slider">
            <?php 
            for ($i = 1; $i <= 5; $i++) {
                if (!empty($dp_options['slider_image' . $i])) {
                    $image_url = wp_get_attachment_url($dp_options['slider_image' . $i]);
                    if ($image_url) {
                        echo '<div class="medi-hero-slide" style="background-image: url(' . esc_url($image_url) . ');"></div>';
                    }
                }
            }
            ?>
        </div>
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
                <form action="<?php echo esc_url(get_post_type_archive_link('store')); ?>" method="get" class="medi-hero-search-form">
                    <div class="medi-hero-search-wrapper">
                        <div class="medi-hero-search-selects">
                            <?php // 都道府県ドロップダウン ?>
                            <select name="prefecture_filter" class="medi-hero-select">
                                <option value="">都道府県を選択</option>
                                <?php
                                $prefectures = get_terms(array(
                                    'taxonomy' => 'prefecture',
                                    'hide_empty' => false,
                                    'parent' => !0, // 親ターム（地方）を除外
                                    'orderby' => 'name',
                                    'order' => 'ASC'
                                ));
                                foreach($prefectures as $pref) :
                                ?>
                                    <option value="<?php echo esc_attr($pref->slug); ?>"><?php echo esc_html($pref->name); ?></option>
                                <?php endforeach; ?>
                            </select>
                            
                            <?php // ジャンルドロップダウン ?>
                            <select name="genre_filter[]" class="medi-hero-select">
                                <option value="">ジャンルを選択</option>
                                <?php
                                $genres = get_terms(array(
                                    'taxonomy' => 'genre',
                                    'hide_empty' => false,
                                    'orderby' => 'name',
                                    'order' => 'ASC'
                                ));
                                foreach($genres as $genre) :
                                ?>
                                    <option value="<?php echo esc_attr($genre->slug); ?>"><?php echo esc_html($genre->name); ?></option>
                                <?php endforeach; ?>
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
                        // カードのコードは既存のものを使用
                        ?>
                        <div class="medi-recommend-slide">
                            <!-- 既存のカードコード -->
                        </div>
                        <?php
                    endwhile;
                    wp_reset_postdata();
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
                '北海道・東北',
                '関東',
                '中部',
                '関西',
                '中国・四国',
                '九州・沖縄'
            );
            
            $region_terms = get_terms(array(
                'taxonomy' => 'prefecture',
                'parent' => 0,
                'hide_empty' => false
            ));
            
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
            
            if (!empty($ordered_regions)) :
                foreach ($ordered_regions as $region_term) :
                    // 既存のアコーディオンコード
                endforeach;
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
                            <?php if ($feeling_icon) : ?>
                                <img src="<?php echo esc_url($feeling_icon['url']); ?>" alt="<?php echo esc_attr($feeling_term->name); ?>">
                            <?php else : ?>
                                <div class="default-feeling-icon">💝</div>
                            <?php endif; ?>
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
                            <?php if ($situation_image) : ?>
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
        // Ad Inserterの広告を表示
        if (function_exists('ai_content')) {
            // 広告位置1-4
            for ($i = 1; $i <= 4; $i++) {
                echo '<div class="medi-sidebar-section">';
                echo ai_content($i);
                echo '</div>';
            }
        }
        ?>
        
        <?php // サイドバーウィジェット ?>
        <?php if (is_active_sidebar('homepage-sidebar')) : ?>
            <?php dynamic_sidebar('homepage-sidebar'); ?>
        <?php endif; ?>
    </div>
</aside>

</div>

<?php get_footer(); ?>