<?php
/**
 * medi& GENSEN Child Theme functions and definitions
 *
 * @package medi& GENSEN Child
 */

/**
 * Enqueue scripts and styles.
 */
function medi_gensen_child_enqueue_assets() {
    // 親テーマのstyle.cssを読み込みます
    wp_enqueue_style(
        'gensen_tcd050-parent-style', // ハンドル名
        get_template_directory_uri() . '/style.css' // 親テーマのstyle.cssのパス
    );

    // 子テーマのstyle.cssを読み込みます
    wp_enqueue_style(
        'gensen_tcd050-child-style', // ハンドル名
        get_stylesheet_directory_uri() . '/style.css', // 子テーマのstyle.cssのパス
        array( 'gensen_tcd050-parent-style' ), // 依存関係: 親テーマのスタイルより後に読み込む
        wp_get_theme()->get('Version') // 子テーマのバージョン (style.cssで定義)
    );

    // WordPressコアのjQueryを確実に読み込む (通常は依存関係で自動だが念のため)
    wp_enqueue_script('jquery');

    // カスタムJavaScriptファイルの読み込み (archive-store.phpのタブUIとフィルター用)
    // is_post_type_archive('store') で、店舗一覧ページでのみ読み込むように条件分岐
    if ( is_post_type_archive('store') ) {
        wp_enqueue_script(
            'archive-store-filters', // スクリプトのハンドル名
            get_stylesheet_directory_uri() . '/js/archive-store-filters.js', // JSファイルのパス
            array('jquery'), // jQueryに依存する
            wp_get_theme()->get('Version'), // バージョン番号
            true // true にすると </body> の直前に読み込まれる (推奨)
        );
    }
}
// この関数を 'wp_enqueue_scripts' アクションフックに登録
add_action( 'wp_enqueue_scripts', 'medi_gensen_child_enqueue_assets' );


/**
 * Register navigation menus.
 */
function medi_gensen_child_register_nav_menus() {
    register_nav_menus( array(
        'primary' => esc_html__( 'Primary Navigation', 'gensen_tcd050-child' ),
        // フッター用のメニュー位置 (footer.php の実装に合わせて調整)
        'footer_category_menu'  => esc_html__( 'Footer Category Menu', 'gensen_tcd050-child' ),
        'footer_sitemap_menu'   => esc_html__( 'Footer Sitemap Menu', 'gensen_tcd050-child' ),
        'footer_support_menu'   => esc_html__( 'Footer Support Menu', 'gensen_tcd050-child' ),
    ) );
}
add_action( 'after_setup_theme', 'medi_gensen_child_register_nav_menus' );


/**
 * サイト内検索のクエリをカスタマイズする
 * - GENSENテーマのカスタム検索フォームからのキーワードおよびタクソノミー絞り込みに対応
 * - WordPress標準の検索ウィジェットなどからの検索も考慮
 *
 * @param WP_Query $query WordPress のクエリオブジェクト.
 */
function medi_gensen_child_customize_search_query( $query ) {
    // 管理画面の検索や、メインクエリでない場合は何もしない
    if ( is_admin() || ! $query->is_main_query() ) {
        return;
    }

    // 検索結果ページの場合のみ処理
    if ( $query->is_search() ) {

        // --- 検索対象投稿タイプの設定 ---
        // GENSENのカスタム検索フォーム (子テーマで編集したもの) からは
        // <input type="hidden" name="post_type" value="store"> が送信される想定。
        // これにより、GENSENのフォーム経由では 'store' のみが検索対象になる。

        // もし、WordPress標準の検索ウィジェットなど、post_type を指定しない検索も考慮し、
        // その場合に 'store' を含めたい場合は、ここで調整する。
        // ただし、GENSENのフォームで post_type=store が明示的に指定されていれば、
        // ここで他の投稿タイプを追加すると、GENSENフォームの意図と異なる結果になる可能性あり。
        // 一旦、フォームからの post_type 指定を尊重する形とし、
        // $query->get('post_type') が空の場合のみデフォルトを設定する。

        $current_post_types = $query->get('post_type');
        if ( empty($current_post_types) ) {
            // フォームから post_type の指定がない場合 (例: 標準検索ウィジェット)
            // ここで検索対象にしたい投稿タイプを指定する
            $query->set('post_type', array('store', 'post', 'page')); // 例: 店舗、投稿、固定ページを検索
        }
        // GENSENのフォームから 'store' が指定されていれば、上記のifは通らないので、'store' のみが対象になる。


        // --- タクソノミーフィルターの処理 (GENSENの検索フォーム用) ---
        // GENSENの検索フォーム (custom_search_form.php を子テーマで編集したもの) から
        // name="prefecture" や name="genre" で選択されたタームのスラッグが送信される想定。

        $tax_query_conditions = $query->get('tax_query'); // 既存のtax_queryを取得
        if ( !is_array($tax_query_conditions) ) {
            $tax_query_conditions = array();
        }
        // 複数のタクソノミー条件はANDで結ぶ
        if (empty($tax_query_conditions['relation'])) {
            $tax_query_conditions['relation'] = 'AND';
        }

        // 都道府県プルダウンの値 (フォームの name="prefecture" を想定)
        if ( !empty($_GET['prefecture']) ) {
            $tax_query_conditions[] = array(
                'taxonomy' => 'prefecture', // 都道府県タクソノミーのスラッグ
                'field'    => 'slug',
                'terms'    => sanitize_text_field($_GET['prefecture']),
            );
        }

        // ジャンルプルダウンの値 (フォームの name="genre" を想定)
        if ( !empty($_GET['genre']) ) {
            $tax_query_conditions[] = array(
                'taxonomy' => 'genre', // ジャンルタクソノミーのスラッグ
                'field'    => 'slug',
                'terms'    => sanitize_text_field($_GET['genre']),
            );
        }

        // --- archive-store.php のタブUIからのフィルターも考慮する場合 ---
        // (これらのパラメータは archive-store.php のフォームから送信される)
        if ( !empty($_GET['feeling_filter']) && is_array($_GET['feeling_filter']) ) {
             $tax_query_conditions[] = array(
                'taxonomy' => 'feeling',
                'field'    => 'slug',
                'terms'    => array_map('sanitize_text_field', $_GET['feeling_filter']),
                'operator' => 'AND', // feeling タブ内で選択されたものはAND条件
            );
        }
        if ( !empty($_GET['situation_filter']) && is_array($_GET['situation_filter']) ) {
             $tax_query_conditions[] = array(
                'taxonomy' => 'situation',
                'field'    => 'slug',
                'terms'    => array_map('sanitize_text_field', $_GET['situation_filter']),
                'operator' => 'AND',
            );
        }
        // ジャンルは GENSENフォームと archive-store.php のタブの両方から来る可能性があるので注意
        // GENSENフォームの name="genre" と archive-store.php の name="genre_filter[]" が衝突しないように
        // あるいは、どちらか一方のフォームのみを使用するように設計を統一する。
        // ここでは、archive-store.php からの genre_filter[] も受け取れるようにしておく。
        if ( !empty($_GET['genre_filter']) && is_array($_GET['genre_filter']) && empty($_GET['genre']) ) { // GENSENフォームのgenreが空の場合のみ
             $tax_query_conditions[] = array(
                'taxonomy' => 'genre',
                'field'    => 'slug',
                'terms'    => array_map('sanitize_text_field', $_GET['genre_filter']),
                'operator' => 'AND',
            );
        }


        // tax_queryに実際に条件が追加された場合のみセットする
        if (count($tax_query_conditions) > 1 || (isset($tax_query_conditions[0]) && !empty($tax_query_conditions[0]))) {
            $query->set( 'tax_query', $tax_query_conditions );
        }
    }
}
// 親テーマの sort_pre_get_posts との実行順を考慮し、優先度を調整 (11より大きくても良い)
add_action( 'pre_get_posts', 'medi_gensen_child_customize_search_query', 15 );

// functions.phpに以下を追加

/**
 * ACF オプションページの設定
 */
if( function_exists('acf_add_options_page') ) {
    
    // メインオプションページ
    acf_add_options_page(array(
        'page_title'    => 'medi& サイト設定',
        'menu_title'    => 'サイト設定',
        'menu_slug'     => 'medi-site-settings',
        'capability'    => 'edit_posts',
        'icon_url'      => 'dashicons-admin-customizer',
        'position'      => 30,
    ));
    
    // サブページ：トップページ設定
    acf_add_options_sub_pageS(array(
        'page_title'    => 'トップページ設定',
        'menu_title'    => 'トップページ',
        'parent_slug'   => 'medi-site-settings',
    ));
    
    // サブページ：広告・お知らせ設定
    acf_add_options_sub_pageS(array(
        'page_title'    => '広告・お知らせ設定',
        'menu_title'    => '広告・お知らせ',
        'parent_slug'   => 'medi-site-settings',
    ));
}

/**
 * カスタムサイドバーの登録
 */
function medi_register_sidebars() {
    register_sidebar(array(
        'name'          => 'トップページサイドバー',
        'id'            => 'homepage-sidebar',
        'description'   => 'トップページの右側に表示されるウィジェットエリア',
        'before_widget' => '<div id="%1$s" class="sidebar-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="sidebar-widget-title">',
        'after_title'   => '</h3>',
    ));
}
add_action('widgets_init', 'medi_register_sidebars');

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
// これ以降に他のPHPコードを記述する場合は、この下に追記します。
?>

