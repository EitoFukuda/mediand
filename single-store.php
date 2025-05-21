<?php
/**
 * The template for displaying all single 'store' posts (カスタム投稿タイプ: store)
 * This version reflects a fresh start focusing on structure and dynamic content.
 *
 * @package medi& GENSEN Child
 */

get_header(); // サイト共通ヘッダーを読み込み

// --- データ取得: ページ全体で使う情報を先に取得 ---
$store_id = get_the_ID();
$store_title = get_the_title($store_id);
$icon_base_path = get_stylesheet_directory_uri() . '/assets/icons/'; // アイコン画像フォルダへのパス

// --- ヒーローセクション用データ ---
$post_thumbnail_id = get_post_thumbnail_id($store_id);
$background_image_url = $post_thumbnail_id ? wp_get_attachment_image_url($post_thumbnail_id, 'full') : '';
$prefecture_terms = get_the_terms($store_id, 'prefecture'); // タクソノミースラッグ: 'prefecture'
$prefecture_display = '';
if (!empty($prefecture_terms) && !is_wp_error($prefecture_terms)) {
    $prefecture_names = array_map(function($term) { return esc_html($term->name); }, $prefecture_terms);
    $prefecture_display = implode(', ', $prefecture_names);
}
// SNSリンクとアイコンの定義 (ACFフィールド名とアイコンファイル名を指定)
$hero_sns_definitions = [
    'instagram' => ['field' => 'store_instagram_url', 'icon' => 'instagram_icon.svg', 'alt' => 'Instagram'],
    'x'         => ['field' => 'store_x_url',         'icon' => 'X_icon.png',         'alt' => 'X'],
    'facebook'  => ['field' => 'store_facebook_url',  'icon' => 'facebook_icon.png',  'alt' => 'Facebook'],
    'tiktok'    => ['field' => 'store_tiktok_url',    'icon' => 'tiktok_icon.png',    'alt' => 'TikTok'],
];

// --- 2カラムセクション: 左カラム用データ ---
$recommended_photo_data = get_field('store_recommended_photo', $store_id); // ACFフィールド名: store_recommended_photo
$recommended_photo_url = '';
if ($recommended_photo_data) {
    if (is_array($recommended_photo_data) && isset($recommended_photo_data['url'])) { $recommended_photo_url = $recommended_photo_data['url']; }
    elseif (is_string($recommended_photo_data)) { $recommended_photo_url = $recommended_photo_data; }
}
$catchphrase_title = get_field('store_catchphrase_title', $store_id); // ACFフィールド名: store_catchphrase_title
// キャッチコピー本文はループ内で a_content() を使用

// --- 2カラムセクション: 右カラム用データ (ACFフィールド名はご自身のものに要確認) ---
$phone_number = get_field('store_phone_number', $store_id);       // 例: store_phone_number
$reservation_url = get_field('store_reservation_url', $store_id); // 例: store_reservation_site_url
$access_summary = get_field('store_access_method', $store_id);    // 例: store_access_method
$business_hours = get_field('store_business_hours', $store_id); // 例: store_business_hours
$closed_days = get_field('store_closed_days', $store_id);       // 例: store_closed_days
$payment_methods_value = get_field('store_payment_methods', $store_id);
$wifi_available_value = get_field('store_wifi_available', $store_id); // ラジオボタン: あり/なし

// --- アイキャッチ2 ---
$eyecatch_2_data = get_field('store_eyecatch_2', $store_id); // ACFフィールド名: store_eyecatch_2
$eyecatch_2_url = '';
if ($eyecatch_2_data) {
    if (is_array($eyecatch_2_data) && isset($eyecatch_2_data['url'])) { $eyecatch_2_url = $eyecatch_2_data['url']; }
    elseif (is_string($eyecatch_2_data)) { $eyecatch_2_url = $eyecatch_2_data; }
}

// --- 店舗情報 詳細テーブル用データ (ACFフィールド名はご自身のものに要確認) ---
$store_detail_fields = [
    // '店舗名' は get_the_title() で表示
    // 'ジャンル' は get_the_terms() で表示
    'store_phone_number'        => 'お問い合わせ',
    'store_address'             => '住所',
    'store_access_method'       => '交通手段',
    'store_business_hours'      => '営業時間',
    'store_closed_days'         => '定休日',
    'store_payment_methods'     => '支払い方法',
    'store_price_range'         => '価格帯',
    'store_seat_count'          => '座席数',
    'store_has_private_room'    => '個室',
    'store_has_parking'         => '駐車場',
    'store_special_points'      => 'こだわりポイント',
    'store_menu_details'        => 'メニュー情報',
];

// --- Map用 ---
$address_for_map = get_field('store_address', $store_id); // ACFフィールド名: store_address

?>

<?php // --- ヒーローセクション --- ?>
<div class="store-hero <?php echo $background_image_url ? 'has-background-image' : 'no-background-image'; ?>"
    <?php if ($background_image_url) : ?>
        style="background-image: url('<?php echo esc_url($background_image_url); ?>');"
    <?php endif; ?>
>
    <div class="store-hero__overlay"></div>
    <div class="store-hero__content">
        <h1 class="store-hero__name"><?php echo esc_html($store_title); ?></h1>
        <?php if ($prefecture_display) : ?>
            <div class="store-hero__location">
                <img src="<?php echo esc_url($icon_base_path . 'pin.png'); ?>" alt="地域" class="store-hero__location-icon">
                <span class="store-hero__location-text"><?php echo $prefecture_display; ?></span>
            </div>
        <?php endif; ?>
        <?php
        // SNSアイコンを表示するかどうかのフラグ
        $has_sns_hero = false;
        foreach ($hero_sns_definitions as $sns_item_check) { // 重複しない変数名に変更
            if (get_field($sns_item_check['field'], $store_id)) {
                $has_sns_hero = true;
                break;
            }
        }
        ?>
        <?php if ($has_sns_hero) : ?>
            <div class="store-hero__social-wrapper">
                <?php // <p class="store-hero__social-text">公式SNS</p> // 必要であれば表示 ?>
                <div class="store-hero__social">
                    <?php foreach ($hero_sns_definitions as $key => $sns_item) : // 重複しない変数名に変更 ?>
                        <?php $url = get_field($sns_item['field'], $store_id); ?>
                        <?php if ($url) : ?>
                            <a href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener noreferrer" class="store-hero__social-link store-hero__social-link--<?php echo $key; ?>" title="<?php echo esc_attr($sns_item['alt']); ?>">
                                <img src="<?php echo esc_url($icon_base_path . $sns_item['icon']); ?>" alt="<?php echo esc_attr($sns_item['alt']); ?>" class="store-hero__social-icon">
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>


<div id="primary" class="content-area content-area--single-store">
    <main id="main" class="site-main site-main--single-store" role="main">

        <?php
        // WordPressループ開始
        while ( have_posts() ) :
            the_post(); // グローバルな $post オブジェクトを設定 (get_field などが正しく動作するためにループ内で呼び出す)
        ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('store-article'); ?>>

                <?php // --- 2カラムセクション (おすすめ写真 + キャッチコピー / 店舗サイドバー情報) --- ?>
                <div class="store-article__two-column-section store-article__section">

                    <div class="store-article__left-column">
                        <?php if ($recommended_photo_url) : ?>
                            <section class="store-recommended-photo">
                                <img src="<?php echo esc_url($recommended_photo_url); ?>" alt="<?php echo esc_attr($store_title); ?>のおすすめ写真" class="store-recommended-photo__image">
                            </section>
                        <?php endif; ?>

                        <section class="store-catchphrase">
                            <?php if ($catchphrase_title) : ?>
                                <h2 class="store-catchphrase__title store-article__section-title-sub"><?php echo esc_html($catchphrase_title); ?></h2>
                            <?php endif; ?>
                            <div class="store-catchphrase__detail entry-content">
                                <?php
                                // WordPress標準エディターの内容 (キャッチコピー詳細として使用)
                                if (get_the_content()) { // ループ内なので引数なしでOK
                                    the_content();
                                } elseif ($catchphrase_title) {
                                    echo '<p>（ここに店舗の魅力的な紹介文が入ります）</p>';
                                }
                                ?>
                            </div>
                        </section>
                    </div>

                    <div class="store-article__right-column">
                        <section class="store-sidebar-widget">
                            <?php // 電話番号は画像にないため一旦コメントアウト ?>
                            <?php /*
                            <h3 class="store-sidebar-widget__title">お店について</h3>
                            <?php if ($phone_number) : ?>
                                <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9]/', '', $phone_number)); ?>" class="store-sidebar-widget__phone">
                                    <?php echo esc_html($phone_number); ?>
                                </a>
                            <?php endif; ?>
                            */ ?>
                            
                            <?php if ($reservation_url) : ?>
                                <div class="store-info-section store-info-section--reservation"> <?php // 予約セクション用のクラスを追加 ?>
                                    <h4 class="store-info-section__title">予約サイト</h4>
                                    <a href="<?php echo esc_url($reservation_url); ?>" target="_blank" rel="noopener noreferrer" class="store-sidebar-widget__button button button--reservation">
                                        予約する
                                    </a>
                                </div>
                            <?php endif; ?>

                            <div class="store-info-section">
                                <h4 class="store-info-section__title">アクセス</h4>
                                <?php if ($access_summary) : ?><p class="store-info-section__content"><?php echo nl2br(esc_html($access_summary)); ?></p><?php else: ?><p class="store-info-section__content">アクセス情報はありません。</p><?php endif; ?>
                            </div>
                            <div class="store-info-section">
                                <h4 class="store-info-section__title">営業時間</h4>
                                <?php if ($business_hours) : ?><p class="store-info-section__content"><?php echo nl2br(esc_html($business_hours)); ?></p><?php else: ?><p class="store-info-section__content">営業時間情報はありません。</p><?php endif; ?>
                            </div>
                            <div class="store-info-section">
                                <h4 class="store-info-section__title">定休日</h4>
                                <?php if ($closed_days) : ?><p class="store-info-section__content"><?php echo esc_html($closed_days); ?></p><?php else: ?><p class="store-info-section__content">定休日情報はありません。</p><?php endif; ?>
                            </div>
                            <div class="store-info-section">
                                <h4 class="store-info-section__title">お支払い方法</h4>
                                <?php if ($payment_methods_value) { if (is_array($payment_methods_value)) { echo '<ul class="store-info-section__list">'; foreach ($payment_methods_value as $method) { echo '<li>' . esc_html($method) . '</li>'; } echo '</ul>'; } else { echo '<p class="store-info-section__content">' . nl2br(esc_html($payment_methods_value)) . '</p>'; } } else { echo '<p class="store-info-section__content">お支払い方法情報はありません。</p>'; } ?>
                            </div>
                            <div class="store-info-section">
                                <h4 class="store-info-section__title">Wi-Fi</h4>
                                <?php /* <img src="<?php echo esc_url($icon_base_path . 'wifi_icon.png'); ?>" alt="Wi-Fi" class="wifi-icon"> */ ?>
                                <?php if ($wifi_available_value) : ?><p class="store-info-section__content"><?php echo esc_html($wifi_available_value); ?></p><?php else: ?><p class="store-info-section__content">Wi-Fi情報はありません。</p><?php endif; ?>
                            </div>
                        </section>
                    </div>
                </div>


                <?php // --- アイキャッチ2 表示セクション --- ?>
                <?php if ($eyecatch_2_url) : ?>
                <section class="store-article__eyecatch2 store-article__section">
                    <img src="<?php echo esc_url($eyecatch_2_url); ?>" alt="<?php echo esc_attr($store_title); ?> イメージ" class="store-eyecatch2__image">
                </section>
                <?php endif; ?>


                <?php // --- 店舗情報 詳細テーブルセクション --- ?>
                <section class="store-article__details store-article__section">
                    <h2 class="store-article__section-title">基本情報</h2>
                    <dl class="store-details-list">
                        <div class="store-details-list__item">
                            <dt class="store-details-list__term">店舗名</dt>
                            <dd class="store-details-list__description"><?php echo esc_html($store_title); // ループ内で取得したタイトルを使用 ?></dd>
                        </div>
                        <div class="store-details-list__item">
                            <dt class="store-details-list__term">ジャンル</dt>
                            <dd class="store-details-list__description">
                                <?php
                                $genre_terms_detail = get_the_terms($store_id, 'genre'); // ループ内なので $store_id は現在の投稿ID
                                if (!empty($genre_terms_detail) && !is_wp_error($genre_terms_detail)) {
                                    echo esc_html(implode(', ', array_map(function($term) { return $term->name; }, $genre_terms_detail)));
                                } else { echo '未設定'; }
                                ?>
                            </dd>
                        </div>
                        <?php
                        foreach ($store_detail_fields as $field_name => $label) :
                            $value = get_field($field_name); // ループ内なので投稿IDの指定は省略可能
                            if (isset($value) && $value !== '' && !(is_array($value) && empty($value))) :
                        ?>
                                <div class="store-details-list__item">
                                    <dt class="store-details-list__term"><?php echo esc_html($label); ?></dt>
                                    <dd class="store-details-list__description">
                                        <?php
                                        if (is_array($value)) { echo esc_html(implode(', ', $value)); }
                                        elseif ($field_name === 'store_has_private_room' || $field_name === 'store_has_parking') {
                                            if ($value === true || strtolower($value) === 'あり' || strtolower($value) === '有り' || $value === 1 || $value === '1') { echo '有り'; }
                                            elseif ($value === false || strtolower($value) === 'なし' || strtolower($value) === '無し' || $value === 0 || $value === '0') { echo '無し'; }
                                            else { echo nl2br(esc_html($value)); }
                                        } else { echo nl2br(esc_html($value)); }
                                        ?>
                                    </dd>
                                </div>
                        <?php
                            endif;
                        endforeach;
                        ?>
                    </dl>
                </section>


                <?php // --- Mapセクション --- ?>
                <?php if ($address_for_map) : ?>
                <section class="store-article__map store-article__section">
                    <h2 class="store-article__section-title">Map</h2>
                    <div class="map-container">
                        <iframe width="100%" height="450" style="border:0; border-radius: 8px;" loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade" src="https://maps.google.co.jp/maps?output=embed&q=<?php echo urlencode($address_for_map); ?>"></iframe>
                    </div>
                </section>
                <?php endif; ?>


                <?php // --- 関連記事セクション --- ?>
                <section class="store-article__related-posts store-article__section">
                    <h2 class="store-article__section-title">関連記事</h2>
                    <div class="related-posts__container">
                        <?php
                        $current_post_id_for_related = get_the_ID();
                        $prefecture_terms_for_related_loop = get_the_terms($current_post_id_for_related, 'prefecture');

                        if ($prefecture_terms_for_related_loop && !is_wp_error($prefecture_terms_for_related_loop)) {
                            $prefecture_slugs_for_related = array_map(function($term) { return $term->slug; }, $prefecture_terms_for_related_loop);
                            $related_args = array(
                                'post_type' => 'store', 'posts_per_page' => 4,
                                'post__not_in' => array($current_post_id_for_related),
                                'tax_query' => array( array( 'taxonomy' => 'prefecture', 'field' => 'slug', 'terms' => $prefecture_slugs_for_related, ) ),
                                'orderby' => 'rand', // ランダム表示
                            );
                            $related_query = new WP_Query($related_args);
                            if ($related_query->have_posts()) :
                                while ($related_query->have_posts()) : $related_query->the_post(); // ここでグローバル $post が上書きされる
                                    // 関連記事ループ内の情報を取得
                                    $related_id_in_loop = get_the_ID(); // 新しい投稿のID
                                    $related_title_in_loop = get_the_title();
                                    $related_permalink_in_loop = get_permalink();
                                    $related_thumbnail_url_in_loop = get_the_post_thumbnail_url($related_id_in_loop, 'medium');
                                    $related_prefecture_terms_in_loop = get_the_terms($related_id_in_loop, 'prefecture');
                                    $related_prefecture_text_in_loop = '';
                                    if (!empty($related_prefecture_terms_in_loop) && !is_wp_error($related_prefecture_terms_in_loop)) {
                                        $pref_names_in_loop = [];
                                        foreach($related_prefecture_terms_in_loop as $pref_term_item_loop){
                                            if($pref_term_item_loop->parent != 0){ $pref_names_in_loop[] = esc_html($pref_term_item_loop->name); }
                                        }
                                        if(!empty($pref_names_in_loop)) $related_prefecture_text_in_loop = implode(', ', $pref_names_in_loop);
                                    }
                                    $genre_terms_related_in_loop = get_the_terms($related_id_in_loop, 'genre');
                            ?>
                                    <article class="related-post__item">
                                        <a href="<?php echo esc_url($related_permalink_in_loop); ?>" class="related-post__link">
                                            <div class="related-post__thumbnail-wrapper">
                                                <?php if ($related_thumbnail_url_in_loop) : ?><img src="<?php echo esc_url($related_thumbnail_url_loop); ?>" alt="<?php echo esc_attr($related_title_in_loop); ?>" class="related-post__thumbnail"><?php else : ?><div class="related-post__thumbnail-placeholder">画像なし</div><?php endif; ?>
                                            </div>
                                            <div class="related-post__content">
                                                <h3 class="related-post__title"><?php echo esc_html($related_title_in_loop); ?></h3>
                                                <?php if ($related_prefecture_text_in_loop) : ?><p class="related-post__location"><img src="<?php echo esc_url($icon_base_path . 'pin.png'); ?>" alt="" class="related-post__location-icon"><?php echo $related_prefecture_text_in_loop; ?></p><?php endif; ?>
                                                <?php if (!empty($genre_terms_related_in_loop) && !is_wp_error($genre_terms_related_in_loop)) : ?>
                                                    <div class="store-tags">
                                                        <?php foreach (array_slice($genre_terms_related_in_loop, 0, 2) as $term) : ?>
                                                            <span class="store-tag"><?php echo esc_html($term->name); ?></span>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </a>
                                    </article>
                            <?php
                                endwhile;
                                wp_reset_postdata(); // カスタムクエリの後に必須
                            else :
                                echo '<p>同じ都道府県の関連記事はありません。</p>';
                            endif;
                        } else {
                            echo '<p>関連記事を表示するための地域情報がありません。</p>';
                        }
                        ?>
                    </div>
                </section>

                <footer class="store-article__entry-footer entry-footer">
                    <?php
                    edit_post_link( sprintf( esc_html__( 'Edit %s', 'gensen_tcd050-child' ), '<span class="screen-reader-text">' . get_the_title() . '</span>' ), '<span class="edit-link">', '</span>' );
                    ?>
                </footer>

            </article><?php endwhile; // End of the WordPress loop. ?>

    </main></div><?php get_footer(); // サイト共通フッターを読み込み ?>