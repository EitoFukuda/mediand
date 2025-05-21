<?php
/**
 * medi& GENSEN Child Theme functions and definitions
 *
 * @package medi& GENSEN Child
 */

/**
 * Enqueue scripts and styles.
 */
function medi_gensen_child_enqueue_assets() { // 関数名をスタイルとスクリプト両方を読み込むものに変更
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
        // もしフッターメニューなども作る場合はここに追加
        // 'footer_nav' => esc_html__( 'Footer Navigation', 'gensen_tcd050-child' ),
    ) );
}
add_action( 'after_setup_theme', 'medi_gensen_child_register_nav_menus' );


/**
 * サイト内検索の対象にカスタム投稿タイプ 'store' を追加する
 *
 * @param WP_Query $query WordPress のクエリオブジェクト.
 */
function medi_gensen_child_expand_search_query( $query ) {
    // 管理画面の検索や、メインクエリでない場合は何もしない
    if ( is_admin() || ! $query->is_main_query() ) {
        return;
    }

    // 検索結果ページの場合のみ処理
    if ( $query->is_search() ) {
        $post_types_to_search = $query->get('post_type');

        if ( empty($post_types_to_search) ) {
            $post_types_to_search = array('post', 'store'); // 標準の投稿と店舗を検索
        } elseif ( is_string($post_types_to_search) ) {
            $post_types_to_search = array($post_types_to_search);
            if ( !in_array('store', $post_types_to_search) ) {
                $post_types_to_search[] = 'store';
            }
        } elseif ( is_array($post_types_to_search) ) {
            if ( !in_array('store', $post_types_to_search) ) {
                $post_types_to_search[] = 'store';
            }
        }

        if (!empty($post_types_to_search)) {
            $query->set( 'post_type', $post_types_to_search );
        }
    }
}
add_action( 'pre_get_posts', 'medi_gensen_child_expand_search_query', 11 ); // 優先度を少し遅らせる

// これ以降に他のPHPコードを記述する場合は、この下に追記します。
// ファイルの最後は ?> で閉じる必要はありません (PHPのみのファイルの場合推奨)。