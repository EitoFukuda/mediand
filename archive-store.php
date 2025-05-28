<?php
/**
 * The template for displaying Archive pages for 'store' post type
 * Features Tabbed UI for filtering by taxonomies and keyword.
 *
 * @package medi& GENSEN Child
 */

get_header();

// --- フィルター条件の取得 (GETパラメータから) ---
$selected_prefecture_slug = isset($_GET['prefecture_filter']) ? sanitize_text_field($_GET['prefecture_filter']) : '';
$selected_feelings_slugs   = isset($_GET['feeling_filter']) && is_array($_GET['feeling_filter']) ? array_map('sanitize_text_field', $_GET['feeling_filter']) : array();
$selected_situations_slugs = isset($_GET['situation_filter']) && is_array($_GET['situation_filter']) ? array_map('sanitize_text_field', $_GET['situation_filter']) : array();
$selected_genres_slugs     = isset($_GET['genre_filter']) && is_array($_GET['genre_filter']) ? array_map('sanitize_text_field', $_GET['genre_filter']) : array();
$keyword_search      = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
$active_tab_param    = isset($_GET['active_tab']) ? sanitize_key($_GET['active_tab']) : 'region'; // デフォルトは地域タブ

// --- WP_Queryの引数を組み立てる ---
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$args = array(
    'post_type'      => 'store',
    'posts_per_page' => 9,
    'paged'          => $paged,
    's'              => $keyword_search,
    'tax_query'      => array('relation' => 'AND'),
);

if (!empty($selected_prefecture_slug)) {
    $args['tax_query'][] = array('taxonomy' => 'prefecture', 'field' => 'slug', 'terms' => $selected_prefecture_slug);
}
if (!empty($selected_feelings_slugs)) {
    $args['tax_query'][] = array('taxonomy' => 'feeling', 'field' => 'slug', 'terms' => $selected_feelings_slugs, 'operator' => 'AND');
}
if (!empty($selected_situations_slugs)) {
    $args['tax_query'][] = array('taxonomy' => 'situation', 'field' => 'slug', 'terms' => $selected_situations_slugs, 'operator' => 'AND');
}
if (!empty($selected_genres_slugs)) {
    $args['tax_query'][] = array('taxonomy' => 'genre', 'field' => 'slug', 'terms' => $selected_genres_slugs, 'operator' => 'AND');
}
if (count($args['tax_query']) <= 1) { unset($args['tax_query']); }

$store_query = new WP_Query($args);

// --- 地方タームの取得 ---
$region_terms = get_terms(array('taxonomy' => 'prefecture', 'parent' => 0, 'hide_empty' => false, 'orderby' => 'name', 'order' => 'ASC'));
$icon_base_path = get_stylesheet_directory_uri() . '/assets/icons/';
?>

<div id="primary" class="content-area archive-store-content-area">
    <main id="main" class="site-main archive-store-main">

        <header class="page-header archive-store-header">
            <h1 class="page-title"><?php echo esc_html(get_post_type_object('store')->labels->name); // 「店舗」投稿タイプの名前を表示 ?></h1>
        </header>

        <form role="search" method="get" class="store-search-form" action="<?php echo esc_url(get_post_type_archive_link('store')); ?>">
            <input type="hidden" name="active_tab" value="<?php echo esc_attr($active_tab_param); ?>" class="active-tab-input"> <?php // JSでタブ状態をURLに反映させるため ?>

            <div class="store-search-tabs">
                <ul class="store-search-tabs__list">
                    <li class="store-search-tabs__item <?php if($active_tab_param === 'region') echo 'is-active'; ?>" data-tab-target="#filter-region"><a href="#filter-region">地域から選ぶ</a></li>
                    <li class="store-search-tabs__item <?php if($active_tab_param === 'feeling') echo 'is-active'; ?>" data-tab-target="#filter-feeling"><a href="#filter-feeling">ココロで選ぶ</a></li>
                    <li class="store-search-tabs__item <?php if($active_tab_param === 'situation') echo 'is-active'; ?>" data-tab-target="#filter-situation"><a href="#filter-situation">シチュエーションで選ぶ</a></li>
                    <li class="store-search-tabs__item <?php if($active_tab_param === 'genre') echo 'is-active'; ?>" data-tab-target="#filter-genre"><a href="#filter-genre">ジャンルで選ぶ</a></li>
                    <li class="store-search-tabs__item <?php if($active_tab_param === 'keyword') echo 'is-active'; ?>" data-tab-target="#filter-keyword"><a href="#filter-keyword">キーワード検索</a></li>
                </ul>
            </div>

            <div class="store-search-filter-panels">
                <div id="filter-region" class="store-search-filter-panel <?php if($active_tab_param === 'region') echo 'is-active'; ?>">
                <div class="filter-region__regions">
    <?php 
    // 地方の順序を定義（トップページと同じ）
    $region_order = array(
        '北海道/東北', '関東', '中部', '近畿', '関西', '中国', '四国', '九州', '沖縄'
    );
    
    if (!empty($region_terms) && !is_wp_error($region_terms)) :
        // 順序通りに並び替え
        $ordered_regions = array();
        foreach($region_order as $region_name) {
            foreach($region_terms as $term) {
                if($term->name === $region_name || strpos($term->name, $region_name) !== false) {
                    $ordered_regions[] = $term;
                    break;
                }
            }
        }
        
        // 順序にない地方を末尾に追加
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
        
        foreach ($ordered_regions as $region_term) : ?>
            <button type="button" class="filter-button filter-button--region" data-region-id="<?php echo esc_attr($region_term->term_id); ?>" data-region-slug="<?php echo esc_attr($region_term->slug); ?>">
                <?php echo esc_html($region_term->name); ?>
            </button>
        <?php endforeach; 
    else: ?>
        <p>地方が登録されていません。</p>
    <?php endif; ?>
</div>
<div class="filter-region__prefectures-wrapper">
    <div class="filter-region__prefectures-placeholder">地方を選択してください</div>
    <?php
    if (!empty($ordered_regions)) {
        foreach ($ordered_regions as $region_term) {
            echo '<div class="filter-region__prefecture-group" data-parent-region-id="' . esc_attr($region_term->term_id) . '" style="display:none;">';
            $prefs_in_region = get_terms(array('taxonomy' => 'prefecture', 'parent' => $region_term->term_id, 'hide_empty' => false, 'orderby' => 'name', 'order' => 'ASC'));
            if (!empty($prefs_in_region) && !is_wp_error($prefs_in_region)) {
                foreach ($prefs_in_region as $pref_term) {
                    $is_pref_selected = ($selected_prefecture_slug === $pref_term->slug);
                    echo '<label class="filter-button filter-button--radio ' . ($is_pref_selected ? 'is-selected' : '') . '">';
                    echo '<input type="radio" name="prefecture_filter" value="' . esc_attr($pref_term->slug) . '" ' . checked($is_pref_selected, true, false) . '>';
                    echo esc_html($pref_term->name);
                    echo '</label>';
                }
            } else { echo '<p class="no-prefs-message">この地方の都道府県は登録されていません。</p>';}
            echo '</div>';
        }
    }
    ?>
</div>
                </div>

                <div id="filter-feeling" class="store-search-filter-panel <?php if($active_tab_param === 'feeling') echo 'is-active'; ?>">
                    <?php $feeling_terms = get_terms(array('taxonomy' => 'feeling', 'hide_empty' => false, 'orderby' => 'name', 'order' => 'ASC')); ?>
                    <?php if (!empty($feeling_terms) && !is_wp_error($feeling_terms)) : foreach ($feeling_terms as $term) : ?>
                        <label class="filter-button filter-button--checkbox <?php if (in_array($term->slug, $selected_feelings_slugs)) echo 'is-selected'; ?>">
                            <input type="checkbox" name="feeling_filter[]" value="<?php echo esc_attr($term->slug); ?>" <?php checked(in_array($term->slug, $selected_feelings_slugs)); ?>>
                            <?php echo esc_html($term->name); ?>
                        </label>
                    <?php endforeach; else: ?><p>「ココロで選ぶ」項目が登録されていません。</p><?php endif; ?>
                </div>

                <div id="filter-situation" class="store-search-filter-panel <?php if($active_tab_param === 'situation') echo 'is-active'; ?>">
                    <?php $situation_terms = get_terms(array('taxonomy' => 'situation', 'hide_empty' => false, 'orderby' => 'name', 'order' => 'ASC')); ?>
                    <?php if (!empty($situation_terms) && !is_wp_error($situation_terms)) : foreach ($situation_terms as $term) : ?>
                        <label class="filter-button filter-button--checkbox <?php if (in_array($term->slug, $selected_situations_slugs)) echo 'is-selected'; ?>">
                            <input type="checkbox" name="situation_filter[]" value="<?php echo esc_attr($term->slug); ?>" <?php checked(in_array($term->slug, $selected_situations_slugs)); ?>>
                            <?php echo esc_html($term->name); ?>
                        </label>
                    <?php endforeach; else: ?><p>「シチュエーションで選ぶ」項目が登録されていません。</p><?php endif; ?>
                </div>

                <div id="filter-genre" class="store-search-filter-panel <?php if($active_tab_param === 'genre') echo 'is-active'; ?>">
    <div class="filter-genre__categories">
        <?php 
        // ジャンル親タームを取得
        $genre_parent_terms = get_terms(array(
            'taxonomy' => 'genre',
            'parent' => 0,
            'hide_empty' => false,
            'orderby' => 'name',
            'order' => 'ASC'
        ));
        
        if (!empty($genre_parent_terms) && !is_wp_error($genre_parent_terms)) :
            foreach ($genre_parent_terms as $parent_term) : ?>
                <button type="button" class="filter-button filter-button--genre-parent" data-genre-id="<?php echo esc_attr($parent_term->term_id); ?>" data-genre-slug="<?php echo esc_attr($parent_term->slug); ?>">
                    <?php echo esc_html($parent_term->name); ?>
                </button>
            <?php endforeach; 
        else: ?>
            <p>ジャンルカテゴリが登録されていません。</p>
        <?php endif; ?>
    </div>
    
    <div class="filter-genre__subcategories-wrapper">
        <div class="filter-genre__subcategories-placeholder">ジャンルカテゴリを選択してください</div>
        <?php
        if (!empty($genre_parent_terms)) {
            foreach ($genre_parent_terms as $parent_term) {
                echo '<div class="filter-genre__subcategory-group" data-parent-genre-id="' . esc_attr($parent_term->term_id) . '" style="display:none;">';
                $child_genres = get_terms(array('taxonomy' => 'genre', 'parent' => $parent_term->term_id, 'hide_empty' => false, 'orderby' => 'name', 'order' => 'ASC'));
                if (!empty($child_genres) && !is_wp_error($child_genres)) {
                    foreach ($child_genres as $child_term) {
                        $is_genre_selected = in_array($child_term->slug, $selected_genres_slugs);
                        echo '<label class="filter-button filter-button--checkbox ' . ($is_genre_selected ? 'is-selected' : '') . '">';
                        echo '<input type="checkbox" name="genre_filter[]" value="' . esc_attr($child_term->slug) . '" ' . checked($is_genre_selected, true, false) . '>';
                        echo esc_html($child_term->name);
                        echo '</label>';
                    }
                } else { 
                    echo '<p class="no-genres-message">このカテゴリのジャンルは登録されていません。</p>';
                }
                echo '</div>';
            }
        }
        ?>
    </div>
</div>

                <div id="filter-keyword" class="store-search-filter-panel <?php if($active_tab_param === 'keyword') echo 'is-active'; ?>">
                    <input type="search" class="store-search-filter__keyword-input" placeholder="地域名・お店の名前・特徴・気分・目的など" value="<?php echo esc_attr($keyword_search); ?>" name="s" title="検索キーワード" />
                </div>
            </div>

            <?php if (!empty($selected_prefecture_slug) || !empty($selected_feelings_slugs) || !empty($selected_situations_slugs) || !empty($selected_genres_slugs) || !empty($keyword_search)) : ?>

<?php endif; ?>

            <div class="store-search-form__submit-area">
                <button type="submit" class="store-search-form__submit-button button">この条件で検索</button>
                <a href="<?php echo esc_url(get_post_type_archive_link('store')); ?>" class="store-search-form__reset-button button-alt">条件をリセット</a>
            </div>
        </form>
        

        <?php if ( $store_query->have_posts() ) : ?>
            <div class="store-list-container">
                <?php
                while ( $store_query->have_posts() ) : $store_query->the_post();
                    $store_id_archive = get_the_ID();
                    $permalink_archive = get_permalink();
                    $title_archive = get_the_title();
                    $thumbnail_url_archive = get_the_post_thumbnail_url($store_id_archive, 'medium');
                    $store_features_archive = get_field('store_features', $store_id_archive);
                    $prefecture_terms_archive = get_the_terms($store_id_archive, 'prefecture');
                    $prefecture_display_archive = '';
                    if (!empty($prefecture_terms_archive) && !is_wp_error($prefecture_terms_archive)) {
                        $pref_names_only = [];
                        foreach($prefecture_terms_archive as $pref_term_item){ if($pref_term_item->parent != 0){ $pref_names_only[] = esc_html($pref_term_item->name); } }
                        if(!empty($pref_names_only)) $prefecture_display_archive = implode(', ', $pref_names_only);
                    }
                ?>
                    <article id="post-<?php echo $store_id_archive; ?>" <?php post_class('store-list-item'); ?>>
                        <a href="<?php echo esc_url($permalink_archive); ?>" class="store-list-item__link">
                            <div class="store-list-item__thumbnail-wrapper">
                                <?php if ($thumbnail_url_archive) : ?><img src="<?php echo esc_url($thumbnail_url_archive); ?>" alt="<?php echo esc_attr($title_archive); ?>" class="store-list-item__thumbnail"><?php else : ?><div class="store-list-item__thumbnail-placeholder">画像なし</div><?php endif; ?>
                            </div>
                            <div class="store-list-item__content">
                                <h2 class="store-list-item__title"><?php echo esc_html($title_archive); ?></h2>
                                <?php if ($store_features_archive) : ?><p class="store-list-item__features"><?php echo wp_trim_words(esc_html($store_features_archive), 22, '...'); ?></p><?php endif; ?>
                                <?php if ($prefecture_display_archive) : ?><p class="store-list-item__location"><img src="<?php echo esc_url($icon_base_path . 'pin.png'); ?>" alt="" class="store-list-item__location-icon"><?php echo $prefecture_display_archive; ?></p><?php endif; ?>
                                <span class="store-list-item__details-button button-like">詳細を見る</span>
                            </div>
                        </a>
                    </article>
                <?php endwhile; ?>
            </div>
            <?php
            $big = 999999999;
            echo paginate_links( array(
                'base' => str_replace( $big, '%#%', esc_url(get_pagenum_link( $big ) ) ),
                'format' => '?paged=%#%', 'current' => max( 1, $paged ),
                'total' => $store_query->max_num_pages,
                'prev_text' => '&laquo; 前へ', 'next_text' => '次へ &raquo;', 'type' => 'list',
            ) );
            wp_reset_postdata();
            ?>
        <?php else : ?>
            <p class="no-stores-found">ご指定の条件に合う店舗情報が見つかりませんでした。検索条件を変更してみてください。</p>
        <?php endif; ?>
    </main>
</div>

<?php get_footer(); ?>