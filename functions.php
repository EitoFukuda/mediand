<?php
/**
 * medi& GENSEN Child Theme functions and definitions
 *
 * @package medi& GENSEN Child
 */

// 親テーマとの依存関係を明確にし、エラーハンドリングを強化
function medi_gensen_child_enqueue_assets() {
    // 親テーマのstyle.cssを読み込み
    wp_enqueue_style(
        'gensen_tcd050-parent-style',
        get_template_directory_uri() . '/style.css',
        array(),
        wp_get_theme(get_template())->get('Version')
    );

    // 子テーマのstyle.cssを読み込み
    wp_enqueue_style(
        'gensen_tcd050-child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array('gensen_tcd050-parent-style'),
        wp_get_theme()->get('Version')
    );

    // jQueryを確実に読み込む
    wp_enqueue_script('jquery');

    // 背景装飾JavaScriptをすべてのページで読み込み
    wp_enqueue_script(
        'background-decorations',
        get_stylesheet_directory_uri() . '/js/background-decorations.js',
        array('jquery'),
        wp_get_theme()->get('Version'),
        true
    );

    // トップページでのJavaScript読み込み
    if (is_front_page() || is_home()) {
        wp_enqueue_script(
            'medi-homepage-js',
            get_stylesheet_directory_uri() . '/js/homepage.js',
            array('jquery'),
            wp_get_theme()->get('Version'),
            true
        );
        
        // ローカライゼーション（必要に応じて）
        wp_localize_script('medi-homepage-js', 'mediHomepage', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('medi_homepage_nonce'),
            'store_archive_url' => get_post_type_archive_link('store')
        ));
    }

    // 店舗一覧ページでのみJavaScriptを読み込み
    if (is_post_type_archive('store')) {
        wp_enqueue_script(
            'archive-store-filters',
            get_stylesheet_directory_uri() . '/js/archive-store-filters.js',
            array('jquery'),
            wp_get_theme()->get('Version'),
            true
        );
        
        // Ajax用のローカライゼーション
        wp_localize_script('archive-store-filters', 'store_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('store_filter_nonce')
        ));
    }

    // 店舗詳細ページでのJavaScript
    if (is_singular('store')) {
        wp_enqueue_script(
            'single-store-js',
            get_stylesheet_directory_uri() . '/js/single-store.js',
            array('jquery'),
            wp_get_theme()->get('Version'),
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'medi_gensen_child_enqueue_assets');

/**
 * TCDオプション値の安全な取得用ヘルパー関数
 * 
 * @param string $key 取得したいキー
 * @param mixed $default デフォルト値
 * @return mixed
 */
if (!function_exists('get_safe_tcd_option')) {
    function get_safe_tcd_option($key, $default = '') {
        global $dp_options;
        
        // オプションが読み込まれていない場合は読み込む
        if (!$dp_options) {
            $dp_options = get_desing_plus_option();
        }
        
        // オプションが配列でない場合はデフォルト値を返す
        if (!is_array($dp_options)) {
            return $default;
        }
        
        // キーが存在する場合はその値を、存在しない場合はデフォルト値を返す
        return isset($dp_options[$key]) ? $dp_options[$key] : $default;
    }
}

/**
 * デバッグ情報の安全な出力
 * 
 * @param string $message メッセージ
 * @param mixed $data データ（オプション）
 */
if (!function_exists('medi_debug_log')) {
    function medi_debug_log($message, $data = null) {
        if (defined('WP_DEBUG') && WP_DEBUG && current_user_can('administrator')) {
            if ($data !== null) {
                error_log('[medi& Debug] ' . $message . ': ' . print_r($data, true));
            } else {
                error_log('[medi& Debug] ' . $message);
            }
        }
    }
}

/**
 * TCDテーマオプションの初期化チェック
 */
function medi_check_tcd_options() {
    global $dp_options;
    
    if (!function_exists('get_desing_plus_option')) {
        medi_debug_log('TCD GENSEN theme function get_desing_plus_option() not found');
        return false;
    }
    
    if (!$dp_options) {
        $dp_options = get_desing_plus_option();
        if (is_array($dp_options)) {
            medi_debug_log('TCD options loaded', array_keys($dp_options));
        }
    }
    
    return is_array($dp_options);
}

// 初期化時にTCDオプションをチェック
add_action('init', 'medi_check_tcd_options');

/**
 * フロントページでのみデバッグ情報を非表示にする
 */
function medi_disable_frontend_debug() {
    if (!is_admin() && !current_user_can('administrator')) {
        // 非管理者には警告を表示しない
        error_reporting(E_ERROR | E_PARSE);
    }
}
add_action('template_redirect', 'medi_disable_frontend_debug');

/**
 * Enqueue Stagewise Toolbar in development mode.
 */
function medi_gensen_child_enqueue_stagewise_toolbar() {
    // WP_DEBUG を開発モードの判定に使用します。
    if (defined('WP_DEBUG') && WP_DEBUG) {
        // Stagewiseツールバー本体のスクリプトをエンキュー
        wp_enqueue_script(
            'stagewise-toolbar-main',
            'PATH_TO_STAGEWISE_TOOLBAR_JS', // ここを修正してください
            array(), // 依存関係なし
            null,    // バージョン (パッケージに依存)
            true     // フッターで読み込み
        );

        // Stagewise初期化スクリプトをエンキュー
        wp_enqueue_script(
            'stagewise-init',
            get_stylesheet_directory_uri() . '/js/stagewise-init.js',
            array('stagewise-toolbar-main'), // ツールバー本体の後に読み込む
            wp_get_theme()->get('Version'),
            true
        );
    }
}
add_action( 'wp_enqueue_scripts', 'medi_gensen_child_enqueue_stagewise_toolbar', 999 );

/**
 * ナビゲーションメニューの登録
 */
function medi_gensen_child_register_nav_menus() {
    register_nav_menus(array(
        'primary' => esc_html__('Primary Navigation', 'gensen_tcd050-child'),
        'footer_category_menu' => esc_html__('Footer Category Menu', 'gensen_tcd050-child'),
        'footer_sitemap_menu' => esc_html__('Footer Sitemap Menu', 'gensen_tcd050-child'),
        'footer_support_menu' => esc_html__('Footer Support Menu', 'gensen_tcd050-child'),
    ));
}
add_action('after_setup_theme', 'medi_gensen_child_register_nav_menus');

/**
 * テーマサポートの追加
 */
function medi_gensen_child_theme_support() {
    // 投稿サムネイルサポート
    add_theme_support('post-thumbnails');
    
    // カスタムロゴサポート
    add_theme_support('custom-logo', array(
        'height' => 100,
        'width' => 400,
        'flex-height' => true,
        'flex-width' => true,
    ));
    
    // HTMLサポート
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));
}
add_action('after_setup_theme', 'medi_gensen_child_theme_support');

/**
 * 検索クエリのカスタマイズ（エラーハンドリング強化版）
 */
function medi_gensen_child_customize_search_query($query) {
    if (is_admin() || !$query->is_main_query()) {
        return;
    }

    if ($query->is_search()) {
        // 検索対象投稿タイプの設定
        $current_post_types = $query->get('post_type');
        if (empty($current_post_types)) {
            $query->set('post_type', array('store', 'post', 'page'));
        }

        // タクソノミーフィルターの処理
        $tax_query_conditions = $query->get('tax_query');
        if (!is_array($tax_query_conditions)) {
            $tax_query_conditions = array('relation' => 'AND');
        }

        // 各タクソノミーフィルターの処理
        $taxonomies = array(
            'prefecture_filter' => 'prefecture',
            'genre_filter' => 'genre',
            'feeling_filter' => 'feeling',
            'situation_filter' => 'situation'
        );

        foreach ($taxonomies as $param => $taxonomy) {
            if (!empty($_GET[$param])) {
                $terms = $_GET[$param];
                
                // 配列の場合と文字列の場合を処理
                if (is_array($terms)) {
                    $terms = array_map('sanitize_text_field', $terms);
                    $operator = 'AND';
                } else {
                    $terms = sanitize_text_field($terms);
                    $operator = 'IN';
                }

                // 既に同じタクソノミーの条件がある場合はスキップ
                $has_taxonomy = false;
                foreach ($tax_query_conditions as $condition) {
                    if (isset($condition['taxonomy']) && $condition['taxonomy'] === $taxonomy) {
                        $has_taxonomy = true;
                        break;
                    }
                }

                if (!$has_taxonomy && !empty($terms)) {
                    $tax_query_conditions[] = array(
                        'taxonomy' => $taxonomy,
                        'field' => 'slug',
                        'terms' => $terms,
                        'operator' => $operator,
                    );
                }
            }
        }

        // tax_queryを設定
        if (count($tax_query_conditions) > 1) {
            $query->set('tax_query', $tax_query_conditions);
        }
    }
}
add_action('pre_get_posts', 'medi_gensen_child_customize_search_query', 15);

/**
 * カスタムサイドバーの登録
 */
function medi_register_sidebars() {
    register_sidebar(array(
        'name' => 'トップページサイドバー',
        'id' => 'homepage-sidebar',
        'description' => 'トップページの右側に表示されるウィジェットエリア',
        'before_widget' => '<div id="%1$s" class="sidebar-widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="sidebar-widget-title">',
        'after_title' => '</h3>',
    ));

    register_sidebar(array(
        'name' => '店舗詳細サイドバー',
        'id' => 'store-detail-sidebar',
        'description' => '店舗詳細ページのサイドバー',
        'before_widget' => '<div id="%1$s" class="sidebar-widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="sidebar-widget-title">',
        'after_title' => '</h3>',
    ));
}
add_action('widgets_init', 'medi_register_sidebars');

/**
 * カスタム画像サイズの追加
 */
function medi_custom_image_sizes() {
    add_image_size('store-thumbnail', 400, 300, true);
    add_image_size('store-hero', 1200, 600, true);
    add_image_size('store-card', 350, 200, true);
}
add_action('after_setup_theme', 'medi_custom_image_sizes');

/**
 * Ajax フィルター処理
 */
function medi_ajax_store_filter() {
    // Nonceチェック
    if (!wp_verify_nonce($_POST['nonce'], 'store_filter_nonce')) {
        wp_die('Security check failed');
    }

    $args = array(
        'post_type' => 'store',
        'posts_per_page' => 9,
        'paged' => isset($_POST['page']) ? intval($_POST['page']) : 1,
    );

    // フィルター条件の処理
    if (!empty($_POST['filters'])) {
        $filters = $_POST['filters'];
        $tax_query = array('relation' => 'AND');

        foreach ($filters as $taxonomy => $terms) {
            if (!empty($terms)) {
                $tax_query[] = array(
                    'taxonomy' => sanitize_text_field($taxonomy),
                    'field' => 'slug',
                    'terms' => array_map('sanitize_text_field', $terms),
                    'operator' => 'IN',
                );
            }
        }

        if (count($tax_query) > 1) {
            $args['tax_query'] = $tax_query;
        }
    }

    $query = new WP_Query($args);
    
    ob_start();
    
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            // 店舗カードのテンプレートパーツを読み込み
            get_template_part('template-parts/store-card');
        }
        wp_reset_postdata();
    } else {
        echo '<p class="no-stores-found">条件に合う店舗が見つかりませんでした。</p>';
    }
    
    $html = ob_get_clean();
    
    wp_send_json_success(array(
        'html' => $html,
        'found_posts' => $query->found_posts,
        'max_pages' => $query->max_num_pages,
    ));
}
add_action('wp_ajax_store_filter', 'medi_ajax_store_filter');
add_action('wp_ajax_nopriv_store_filter', 'medi_ajax_store_filter');

/**
 * SNSシェアボタンのショートコード
 */
function medi_social_share_shortcode($atts) {
    $atts = shortcode_atts(array(
        'title' => get_the_title(),
        'url' => get_permalink(),
    ), $atts);

    $title = urlencode($atts['title']);
    $url = urlencode($atts['url']);

    ob_start();
    ?>
    <div class="social-share-buttons">
        <a href="https://twitter.com/intent/tweet?text=<?php echo $title; ?>&url=<?php echo $url; ?>" target="_blank" class="share-button twitter">
            <span>Twitter</span>
        </a>
        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $url; ?>" target="_blank" class="share-button facebook">
            <span>Facebook</span>
        </a>
        <a href="https://line.me/R/msg/text/?<?php echo $title; ?>%20<?php echo $url; ?>" target="_blank" class="share-button line">
            <span>LINE</span>
        </a>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('social_share', 'medi_social_share_shortcode');

/**
 * パンくずリスト
 */
function medi_breadcrumb() {
    if (is_home() || is_front_page()) {
        return;
    }

    echo '<nav class="breadcrumb" aria-label="breadcrumb">';
    echo '<ol class="breadcrumb-list">';
    echo '<li class="breadcrumb-item"><a href="' . home_url() . '">ホーム</a></li>';

    if (is_post_type_archive('store')) {
        echo '<li class="breadcrumb-item active" aria-current="page">店舗一覧</li>';
    } elseif (is_singular('store')) {
        echo '<li class="breadcrumb-item"><a href="' . get_post_type_archive_link('store') . '">店舗一覧</a></li>';
        echo '<li class="breadcrumb-item active" aria-current="page">' . get_the_title() . '</li>';
    } elseif (is_category()) {
        echo '<li class="breadcrumb-item active" aria-current="page">' . single_cat_title('', false) . '</li>';
    } elseif (is_single()) {
        $category = get_the_category();
        if (!empty($category)) {
            echo '<li class="breadcrumb-item"><a href="' . get_category_link($category[0]->term_id) . '">' . $category[0]->name . '</a></li>';
        }
        echo '<li class="breadcrumb-item active" aria-current="page">' . get_the_title() . '</li>';
    } elseif (is_page()) {
        echo '<li class="breadcrumb-item active" aria-current="page">' . get_the_title() . '</li>';
    }

    echo '</ol>';
    echo '</nav>';
}

/**
 * カスタム広告ウィジェット
 */
class Medi_Ad_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'medi_ad_widget',
            'medi& 広告ウィジェット',
            array('description' => 'アフィリエイト広告を表示するウィジェット')
        );
    }
    
    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }
        
        if (!empty($instance['ad_code'])) {
            echo '<div class="ad-widget-content">' . $instance['ad_code'] . '</div>';
        }
        
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : '';
        $ad_code = !empty($instance['ad_code']) ? $instance['ad_code'] : '';
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">タイトル:</label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('ad_code')); ?>">広告コード:</label>
            <textarea class="widefat" rows="10" id="<?php echo esc_attr($this->get_field_id('ad_code')); ?>" name="<?php echo esc_attr($this->get_field_name('ad_code')); ?>"><?php echo esc_textarea($ad_code); ?></textarea>
        </p>
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['ad_code'] = (!empty($new_instance['ad_code'])) ? $new_instance['ad_code'] : '';
        return $instance;
    }
}

function medi_register_widgets() {
    register_widget('Medi_Ad_Widget');
}
add_action('widgets_init', 'medi_register_widgets');

/**
 * セキュリティヘッダーの追加
 */
function medi_security_headers() {
    if (!is_admin()) {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('X-XSS-Protection: 1; mode=block');
    }
}
add_action('wp_loaded', 'medi_security_headers');

/**
 * デバッグ情報の追加（開発環境のみ）
 */
if (WP_DEBUG) {
    function medi_debug_info() {
        if (current_user_can('administrator')) {
            echo '<!-- Debug: Theme loaded successfully -->';
        }
    }
    add_action('wp_footer', 'medi_debug_info');
}

/**
 * TCDテーマオプションとの統合支援
 */
function medi_tcd_integration_support() {
    // TCDのテーマオプションが利用可能な場合の処理
    global $dp_options;
    if ($dp_options) {
        // 必要に応じてTCDオプションを子テーマでカスタマイズ
        add_filter('tcd_slider_options', 'medi_customize_tcd_slider');
        add_filter('tcd_header_options', 'medi_customize_tcd_header');
    }
}
add_action('init', 'medi_tcd_integration_support');

/**
 * TCDスライダーのカスタマイズ
 */
function medi_customize_tcd_slider($options) {
    // 必要に応じてスライダーオプションをカスタマイズ
    return $options;
}

/**
 * TCDヘッダーのカスタマイズ
 */
function medi_customize_tcd_header($options) {
    // 必要に応じてヘッダーオプションをカスタマイズ
    return $options;
}

/**
 * 管理画面でのスタイル調整
 */
function medi_admin_styles() {
    echo '<style>
        .acf-field-group-title { color: #C77DC7; }
        .toplevel_page_medi-site-settings .wp-menu-image:before { color: #C77DC7 !important; }
    </style>';
}
add_action('admin_head', 'medi_admin_styles');
?>