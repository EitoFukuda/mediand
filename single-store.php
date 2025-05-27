<?php
/**
 * The template for displaying all single 'store' posts
 * Fixed layout version matching Figma design
 *
 * @package medi& GENSEN Child
 */

get_header();

// --- データ取得 ---
$store_id = get_the_ID();
$store_title = get_the_title($store_id);
$icon_base_path = get_stylesheet_directory_uri() . '/assets/icons/';

// --- ヒーローセクション用データ ---
$post_thumbnail_id = get_post_thumbnail_id($store_id);
$background_image_url = $post_thumbnail_id ? wp_get_attachment_image_url($post_thumbnail_id, 'full') : '';
$prefecture_terms = get_the_terms($store_id, 'prefecture');
$prefecture_display = '';
if (!empty($prefecture_terms) && !is_wp_error($prefecture_terms)) {
    $pref_names = [];
    foreach($prefecture_terms as $term) {
        if($term->parent != 0) { // 親がある場合のみ（都道府県のみ）
            $pref_names[] = esc_html($term->name);
        }
    }
    $prefecture_display = implode(', ', $pref_names);
}

// SNSリンク定義
$hero_sns_definitions = [
    'instagram' => ['field' => 'store_instagram_url', 'icon' => 'instagram_icon.svg', 'alt' => 'Instagram'],
    'x'         => ['field' => 'store_x_url',         'icon' => 'X_icon.png',         'alt' => 'X'],
    'facebook'  => ['field' => 'store_facebook_url',  'icon' => 'facebook_icon.png',  'alt' => 'Facebook'],
    'tiktok'    => ['field' => 'store_tiktok_url',    'icon' => 'tiktok_icon.png',    'alt' => 'TikTok'],
];

// --- コンテンツ用データ ---
$recommended_photo_data = get_field('store_recommended_photo', $store_id);
$recommended_photo_url = '';
if ($recommended_photo_data) {
    if (is_array($recommended_photo_data) && isset($recommended_photo_data['url'])) {
        $recommended_photo_url = $recommended_photo_data['url'];
    } elseif (is_string($recommended_photo_data)) {
        $recommended_photo_url = $recommended_photo_data;
    }
}

$catchphrase_title = get_field('store_catchphrase_title', $store_id);
$reservation_url = get_field('store_reservation_url', $store_id);
$access_summary = get_field('store_access', $store_id);
$business_hours = get_field('store_hours', $store_id);
$closed_days = get_field('store_holiday', $store_id);
$payment_methods_value = get_field('store_payment_methods', $store_id);
$wifi_available_value = get_field('store_wifi_available', $store_id);

// --- 詳細情報用データ ---
$store_detail_fields = [
    'store_phone_number'        => 'お問い合わせ',
    'store_address'             => '住所',
    'store_access'              => '交通手段',
    'store_hours'               => '営業時間',
    'store_holiday'             => '定休日',
    'store_payment_methods'     => '支払い方法',
    'store_price_range'         => '価格帯',
    'store_seat_count'          => '座席数',
    'store_has_private_room'    => '個室',
    'store_has_parking'         => '駐車場',
    'store_special_points'      => 'こだわりポイント',
    'store_menu_details'        => 'メニュー情報',
];

$address_for_map = get_field('store_address', $store_id);
?>

<?php // --- ヒーローセクション（SNS改善版） --- ?>
<div class="store-hero <?php echo $background_image_url ? 'has-background-image' : 'no-background-image'; ?>"
    <?php if ($background_image_url) : ?>
        style="background-image: url('<?php echo esc_url($background_image_url); ?>');"
    <?php endif; ?>
>
    <div class="store-hero__overlay"></div>
    
    <?php // SNSフローティングボタン ?>
    <?php
    $has_sns = false;
    ob_start();
    foreach ($hero_sns_definitions as $sns_key => $sns_item) {
        if (get_field($sns_item['field'], $store_id)) {
            $has_sns = true;
            $url = get_field($sns_item['field'], $store_id);
            ?>
            <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer" 
               class="store-hero__sns-float store-hero__sns-float--<?php echo $sns_key; ?>" 
               title="<?php echo esc_attr($sns_item['alt']); ?>">
                <span class="sns-float-bg"></span>
                <img src="<?php echo esc_url($icon_base_path . $sns_item['icon']); ?>" 
                     alt="<?php echo esc_attr($sns_item['alt']); ?>" 
                     class="store-hero__sns-icon">
                <span class="sns-float-ripple"></span>
            </a>
            <?php
        }
    }
    $sns_icons_html = ob_get_clean();
    
    if ($has_sns) : ?>
        <div class="store-hero__sns-container">
            <?php echo $sns_icons_html; ?>
        </div>
    <?php endif; ?>
    
    <div class="store-hero__content">
        <div class="container">
        <div class="store-hero__main-info">
        <h1 class="store-hero__name"><?php echo esc_html($store_title); ?></h1>
    
        <?php if ($prefecture_display) : ?>
            <div class="store-hero__location">
                <img src="<?php echo esc_url($icon_base_path . 'pin.png'); ?>" alt="地域" class="store-hero__location-icon">
                <span class="store-hero__location-text"><?php echo $prefecture_display; ?></span>
            </div>
        <?php endif; ?>
        
        <?php
        // SNSアイコンの表示判定とループ
        $has_sns_hero = false;
        ob_start(); // アイコン出力を一旦バッファに保存
        foreach ($hero_sns_definitions as $sns_item) {
            if (get_field($sns_item['field'], $store_id)) {
                $has_sns_hero = true;
                $url = get_field($sns_item['field'], $store_id);
                ?>
                <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer" class="store-hero__social-link" title="<?php echo esc_attr($sns_item['alt']); ?>">
                    <img src="<?php echo esc_url($icon_base_path . $sns_item['icon']); ?>" alt="<?php echo esc_attr($sns_item['alt']); ?>" class="store-hero__social-icon">
                </a>
                <?php
            }
        }
        $sns_icons_html = ob_get_clean();

        if ($has_sns_hero) : ?>
            <div class="store-hero__social">
                <?php echo $sns_icons_html; // バッファしたアイコンHTMLを出力 ?>
            </div>
        <?php endif; ?>
    </div>
        </div>
    </div>
</div>

<div class="store-content-wrapper">
    <div class="container">
        <?php while (have_posts()) : the_post(); ?>
            
            <?php // --- メイン2カラムセクション --- ?>
            <section class="store-main-content">
                <div class="store-main-content__inner">
                    
                    <?php // --- 左カラム --- ?>
                    <div class="store-main-content__left">
                        <?php if ($recommended_photo_url) : ?>
                            <div class="store-recommended-photo">
                                <img src="<?php echo esc_url($recommended_photo_url); ?>" alt="<?php echo esc_attr($store_title); ?>のおすすめ写真" class="store-recommended-photo__image">
                            </div>
                        <?php endif; ?>

                        <div class="store-catchphrase">
                            <?php if ($catchphrase_title) : ?>
                                <h2 class="store-catchphrase__title"><?php echo esc_html($catchphrase_title); ?></h2>
                            <?php endif; ?>
                            <div class="store-catchphrase__content">
                                <?php
                                if (get_the_content()) {
                                    the_content();
                                } elseif ($catchphrase_title) {
                                    echo '<p>店舗の魅力的な紹介文がここに入ります。</p>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <?php // --- 右カラム --- ?>
                    <div class="store-main-content__right">
                        <div class="store-info-card">
                            <?php if ($reservation_url) : ?>
                                <div class="store-info-card__reservation">
                                    <h3 class="store-info-card__title">予約サイト</h3>
                                    <a href="<?php echo esc_url($reservation_url); ?>" target="_blank" rel="noopener noreferrer" class="store-reservation-btn">
                                        予約する
                                    </a>
                                </div>
                            <?php endif; ?>

                            <div class="store-info-card__details">
                                <?php if ($access_summary) : ?>
                                    <div class="store-info-item">
                                        <h4 class="store-info-item__title">アクセス</h4>
                                        <p class="store-info-item__content"><?php echo nl2br(esc_html($access_summary)); ?></p>
                                    </div>
                                <?php endif; ?>

                                <?php if ($business_hours) : ?>
                                    <div class="store-info-item">
                                        <h4 class="store-info-item__title">営業時間</h4>
                                        <p class="store-info-item__content"><?php echo nl2br(esc_html($business_hours)); ?></p>
                                    </div>
                                <?php endif; ?>

                                <?php if ($closed_days) : ?>
                                    <div class="store-info-item">
                                        <h4 class="store-info-item__title">定休日</h4>
                                        <p class="store-info-item__content"><?php echo esc_html($closed_days); ?></p>
                                    </div>
                                <?php endif; ?>

                                <?php if ($payment_methods_value) : ?>
                                    <div class="store-info-item">
                                        <h4 class="store-info-item__title">お支払い方法</h4>
                                        <div class="store-info-item__content">
                                            <?php if (is_array($payment_methods_value)) : ?>
                                                <ul class="payment-methods-list">
                                                    <?php foreach ($payment_methods_value as $method) : ?>
                                                        <li><?php echo esc_html($method); ?></li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php else : ?>
                                                <p><?php echo nl2br(esc_html($payment_methods_value)); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if ($wifi_available_value) : ?>
                                    <div class="store-info-item">
                                        <h4 class="store-info-item__title">Wi-Fi</h4>
                                        <p class="store-info-item__content"><?php echo esc_html($wifi_available_value); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <?php // --- 基本情報セクション --- ?>
<section class="store-basic-info">
    <h2 class="store-section-title">基本情報</h2>
    <dl class="store-details-table">
        <div class="store-details-row">
            <dt class="store-details-term">店舗名</dt>
            <dd class="store-details-desc"><?php echo esc_html($store_title); ?></dd>
        </div>
        
        <div class="store-details-row">
            <dt class="store-details-term">ジャンル</dt>
            <dd class="store-details-desc">
                <?php
                $genre_terms = get_the_terms($store_id, 'genre');
                if (!empty($genre_terms) && !is_wp_error($genre_terms)) {
                    echo esc_html(implode(', ', array_map(function($term) { return $term->name; }, $genre_terms)));
                } else {
                    echo '未設定';
                }
                ?>
            </dd>
        </div>
        
        <?php
        // 詳細情報用のフィールド定義を拡張
        $store_detail_fields = array(
            'store_phone_number'        => 'お問い合わせ',
            'store_address'             => '住所',
            'store_access'              => '交通手段',
            'store_hours'               => '営業時間',
            'store_holiday'             => '定休日',
            'store_payment_methods'     => '支払い方法',
            'store_price_range'         => '価格帯',
            'store_seat_count'          => '座席数',
            'store_has_private_room'    => '個室',
            'store_has_parking'         => '駐車場',
            'store_special_points'      => 'こだわりポイント',
            'store_menu_details'        => 'メニュー情報',
        );
        
        foreach ($store_detail_fields as $field_name => $label) :
            $value = get_field($field_name);
            if (isset($value) && $value !== '' && !(is_array($value) && empty($value))) :
        ?>
                <div class="store-details-row">
                    <dt class="store-details-term"><?php echo esc_html($label); ?></dt>
                    <dd class="store-details-desc">
                        <?php
                        if (is_array($value)) {
                            echo esc_html(implode(', ', $value));
                        } elseif (in_array($field_name, ['store_has_private_room', 'store_has_parking'])) {
                            // 真偽値フィールドの処理
                            if ($value === true || in_array(strtolower($value), ['あり', '有り']) || $value === 1 || $value === '1') {
                                echo '有り';
                            } elseif ($value === false || in_array(strtolower($value), ['なし', '無し']) || $value === 0 || $value === '0') {
                                echo '無し';
                            } else {
                                echo nl2br(esc_html($value));
                            }
                        } else {
                            echo nl2br(esc_html($value));
                        }
                        ?>
                    </dd>
                </div>
        <?php
            endif;
        endforeach;
        ?>
    </dl>
</section>

            <?php // --- マップセクション --- ?>
            <?php if ($address_for_map) : ?>
                <section class="store-map">
                    <h2 class="store-section-title">Map</h2>
                    <div class="store-map__container">
                        <iframe width="100%" height="400" style="border:0; border-radius: 12px;" loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade" src="https://maps.google.co.jp/maps?output=embed&q=<?php echo urlencode($address_for_map); ?>"></iframe>
                    </div>
                </section>
            <?php endif; ?>

            <?php // --- 関連記事セクション --- ?>
            <section class="store-related">
                <h2 class="store-section-title">関連記事</h2>
                <div class="store-related__grid">
                    <?php
                    $current_post_id = get_the_ID();
                    $prefecture_terms_for_related = get_the_terms($current_post_id, 'prefecture');

                    if ($prefecture_terms_for_related && !is_wp_error($prefecture_terms_for_related)) {
                        $prefecture_slugs = array_map(function($term) { return $term->slug; }, $prefecture_terms_for_related);
                        $related_args = array(
                            'post_type' => 'store',
                            'posts_per_page' => 4,
                            'post__not_in' => array($current_post_id),
                            'tax_query' => array(
                                array(
                                    'taxonomy' => 'prefecture',
                                    'field' => 'slug',
                                    'terms' => $prefecture_slugs,
                                )
                            ),
                            'orderby' => 'rand',
                        );
                        $related_query = new WP_Query($related_args);
                        
                        if ($related_query->have_posts()) :
                            while ($related_query->have_posts()) : $related_query->the_post();
                                $related_id = get_the_ID();
                                $related_title = get_the_title();
                                $related_permalink = get_permalink();
                                $related_thumbnail = get_the_post_thumbnail_url($related_id, 'medium');
                                
                                // 都道府県取得
                                $related_prefecture_terms = get_the_terms($related_id, 'prefecture');
                                $related_prefecture_text = '';
                                if (!empty($related_prefecture_terms) && !is_wp_error($related_prefecture_terms)) {
                                    $pref_names = [];
                                    foreach($related_prefecture_terms as $term) {
                                        if($term->parent != 0) {
                                            $pref_names[] = esc_html($term->name);
                                        }
                                    }
                                    $related_prefecture_text = implode(', ', $pref_names);
                                }
                                
                                $related_genre_terms = get_the_terms($related_id, 'genre');
                        ?>
                                <article class="related-store-card">
                                    <a href="<?php echo esc_url($related_permalink); ?>" class="related-store-card__link">
                                        <div class="related-store-card__image">
                                            <?php if ($related_thumbnail) : ?>
                                                <img src="<?php echo esc_url($related_thumbnail); ?>" alt="<?php echo esc_attr($related_title); ?>">
                                            <?php else : ?>
                                                <div class="related-store-card__no-image">画像なし</div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="related-store-card__content">
                                            <h3 class="related-store-card__title"><?php echo esc_html($related_title); ?></h3>
                                            <?php if ($related_prefecture_text) : ?>
                                                <p class="related-store-card__location">
                                                    <img src="<?php echo esc_url($icon_base_path . 'pin.png'); ?>" alt="" class="related-store-card__location-icon">
                                                    <?php echo $related_prefecture_text; ?>
                                                </p>
                                            <?php endif; ?>
                                            <?php if (!empty($related_genre_terms) && !is_wp_error($related_genre_terms)) : ?>
                                                <div class="related-store-card__tags">
                                                    <?php foreach (array_slice($related_genre_terms, 0, 2) as $term) : ?>
                                                        <span class="related-store-card__tag"><?php echo esc_html($term->name); ?></span>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </a>
                                </article>
                        <?php
                            endwhile;
                            wp_reset_postdata();
                        else :
                            echo '<p class="no-related-stores">同じ都道府県の関連記事はありません。</p>';
                        endif;
                    } else {
                        echo '<p class="no-related-stores">関連記事を表示するための地域情報がありません。</p>';
                    }
                    ?>
    <aside class="medi-sidebar">
            <div class="container"> <?php // サイドバー用のコンテナは削除または調整したほうが見栄えが良い場合があります ?>
                <?php if (is_active_sidebar('homepage-sidebar')) : ?>
                    <?php dynamic_sidebar('homepage-sidebar'); ?>
                <?php endif; ?>
            </div>
        </aside>

                </div>
            </section>

        <?php endwhile; ?>
    </div>
</div>

<?php get_footer(); ?>