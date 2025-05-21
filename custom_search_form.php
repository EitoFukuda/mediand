<?php
global $dp_options;
if ( ! $dp_options ) $dp_options = get_desing_plus_option();

// ★ 子テーマでの変更点：常に 'store' を検索対象とし、追加のタクソノミーフィルターを持つようにする
$search_post_type_target = 'store';

$search_forms = array('columns' => 0);

// キーワード検索の準備 (親テーマのオプションを流用)
if ($dp_options['show_search_form_keywords']) {
    $search_forms['search_keywords']['placeholder'] = $dp_options['search_form_keywords_placeholder'] ? $dp_options['search_form_keywords_placeholder'] : __('キーワードで検索', 'gensen_tcd050-child');
    $search_forms['columns']++;
}

// ★ 子テーマでの変更点：都道府県プルダウンの準備
$taxonomy_prefecture = 'prefecture'; // CPT UI で作成した都道府県タクソノミーのスラッグ
if (taxonomy_exists($taxonomy_prefecture)) {
    $search_forms['search_prefecture']['slug'] = $taxonomy_prefecture;
    $search_forms['search_prefecture']['placeholder'] = __('すべての都道府県', 'gensen_tcd050-child');
    $search_forms['search_prefecture']['exclude'] = array(); // 除外するタームIDがあれば
    $search_forms['columns']++;
}

// ★ 子テーマでの変更点：ジャンルプルダウンの準備
$taxonomy_genre = 'genre'; // CPT UI で作成したジャンルタクソノミーのスラッグ
if (taxonomy_exists($taxonomy_genre)) {
    $search_forms['search_genre']['slug'] = $taxonomy_genre;
    $search_forms['search_genre']['placeholder'] = __('すべてのジャンル', 'gensen_tcd050-child');
    $search_forms['search_genre']['exclude'] = array();
    $search_forms['columns']++;
}

// フォームアクションと隠しフィールド
if (!empty($search_forms['columns'])) {
    // ★ 子テーマでの変更点：検索結果は常にサイトのルート (検索結果ページ search.php が使われる)
    $search_forms['form_action'] = home_url('/');
    // ★ 子テーマでの変更点：検索対象の投稿タイプを 'store' に固定
    $search_forms['form_action_hidden']['post_type'] = $search_post_type_target;
}

if (!empty($search_forms['form_action']) && !empty($search_forms['columns'])) :
?>
    <form action="<?php echo esc_url($search_forms['form_action']); ?>" method="get" class="columns-<?php echo esc_attr($search_forms['columns'] + 1); ?>">
<?php
    // 隠しフィールドの出力
    if (!empty($search_forms['form_action_hidden'])) {
        foreach($search_forms['form_action_hidden'] as $key => $value) {
            echo '    <input type="hidden" name="'.esc_attr($key).'" value="'.esc_attr($value).'" />';
        }
    }

    // キーワード入力 (name="s" に変更)
    if (!empty($search_forms['search_keywords'])) :
?>
     <div class="header_search_inputs header_search_keywords">
      <input type="text" id="custom_site_search_keywords" name="s" placeholder="<?php echo esc_attr($search_forms['search_keywords']['placeholder']); ?>" value="<?php echo get_search_query(); ?>" />
     </div>
<?php
    endif;

    // 都道府県プルダウン
    if (!empty($search_forms['search_prefecture']['slug'])) :
?>
     <div class="header_search_inputs">
<?php
            wp_dropdown_categories(array(
                'show_option_all'    => $search_forms['search_prefecture']['placeholder'],
                'taxonomy'           => $search_forms['search_prefecture']['slug'],
                'name'               => $search_forms['search_prefecture']['slug'], // ★ name属性をタクソノミースラッグに
                'orderby'            => 'name',
                'hierarchical'       => true,
                'show_count'         => false,
                'hide_empty'         => false,
                'value_field'        => 'slug', // ★ valueにはスラッグを使用
                'selected'           => isset($_GET[$search_forms['search_prefecture']['slug']]) ? sanitize_text_field($_GET[$search_forms['search_prefecture']['slug']]) : '',
            ));
?>
     </div>
<?php
    endif;

    // ジャンルプルダウン
    if (!empty($search_forms['search_genre']['slug'])) :
?>
     <div class="header_search_inputs">
<?php
            wp_dropdown_categories(array(
                'show_option_all'    => $search_forms['search_genre']['placeholder'],
                'taxonomy'           => $search_forms['search_genre']['slug'],
                'name'               => $search_forms['search_genre']['slug'], // ★ name属性をタクソノミースラッグに
                'orderby'            => 'name',
                'hierarchical'       => true,
                'show_count'         => false,
                'hide_empty'         => false,
                'value_field'        => 'slug', // ★ valueにはスラッグを使用
                'selected'           => isset($_GET[$search_forms['search_genre']['slug']]) ? sanitize_text_field($_GET[$search_forms['search_genre']['slug']]) : '',
            ));
?>
     </div>
<?php
    endif;
?>
     <div class="header_search_inputs header_search_button">
      <input type="submit" id="header_search_submit" value="<?php echo esc_attr($dp_options['search_form_button_text'] ? $dp_options['search_form_button_text'] : __('Search', 'tcd-w') ); ?>" />
     </div>
    </form>
<?php
endif;
?>