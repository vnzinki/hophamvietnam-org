<?php
/**
 * Họ Phạm Việt Nam — Theme Functions
 */

define('HOPHAM_VERSION', '1.0.0');
define('HOPHAM_DIR', get_template_directory());
define('HOPHAM_URI', get_template_directory_uri());

/* ─── Theme Setup ─────────────────────────────────────────── */
function hopham_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption']);
    add_theme_support('custom-logo', [
        'height'      => 80,
        'width'       => 80,
        'flex-height' => true,
        'flex-width'  => true,
    ]);
    add_theme_support('automatic-feed-links');

    register_nav_menus([
        'primary' => __('Menu Chính', 'hopham-vietnam'),
        'footer'  => __('Menu Footer', 'hopham-vietnam'),
    ]);

    add_image_size('hopham-hero', 800, 500, true);
    add_image_size('hopham-card', 400, 260, true);
    add_image_size('hopham-thumb', 100, 100, true);
}
add_action('after_setup_theme', 'hopham_setup');

/* ─── Enqueue Styles & Scripts ────────────────────────────── */
function hopham_enqueue() {
    // Google Fonts
    wp_enqueue_style(
        'hopham-google-fonts',
        'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Noto+Sans:wght@400;500;600;700&family=Noto+Serif:wght@400;700&display=swap',
        [],
        null
    );

    wp_enqueue_style('hopham-main', HOPHAM_URI . '/assets/css/main.css', [], HOPHAM_VERSION);
    wp_enqueue_style('hopham-style', get_stylesheet_uri(), ['hopham-main'], HOPHAM_VERSION);

    wp_enqueue_script('hopham-main', HOPHAM_URI . '/assets/js/main.js', [], HOPHAM_VERSION, true);
}
add_action('wp_enqueue_scripts', 'hopham_enqueue');

/* ─── Sidebar / Widget Areas ──────────────────────────────── */
function hopham_widgets_init() {
    register_sidebar([
        'name'          => __('Sidebar Chính', 'hopham-vietnam'),
        'id'            => 'sidebar-main',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ]);
}
add_action('widgets_init', 'hopham_widgets_init');

/* ─── Custom Excerpt Length ───────────────────────────────── */
function hopham_excerpt_length($length) {
    return 30;
}
add_filter('excerpt_length', 'hopham_excerpt_length');

function hopham_excerpt_more($more) {
    return '…';
}
add_filter('excerpt_more', 'hopham_excerpt_more');

/* ─── Post View Counter (simple, no plugin) ───────────────── */
function hopham_get_post_views($post_id) {
    $count = get_post_meta($post_id, 'hopham_post_views', true);
    return $count ? (int) $count : 0;
}

function hopham_set_post_views($post_id) {
    $count = hopham_get_post_views($post_id);
    update_post_meta($post_id, 'hopham_post_views', $count + 1);
}

function hopham_track_post_views() {
    if (!is_single()) return;
    if (is_admin()) return;
    if (defined('DOING_AJAX') && DOING_AJAX) return;
    if (is_user_logged_in()) return;

    // Basic bot detection
    $ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    if (empty($ua) || preg_match('/bot|crawl|spider|slurp|feed/i', $ua)) return;

    global $post;
    if (empty($post->ID)) return;

    // Cookie-based dedup: don't count same visitor twice within 24h
    $cookie_key = 'hopham_viewed_' . $post->ID;
    if (isset($_COOKIE[$cookie_key])) return;
    setcookie($cookie_key, '1', time() + 86400, '/');

    hopham_set_post_views($post->ID);
}
add_action('template_redirect', 'hopham_track_post_views');

/* ─── Helper: Get Popular Posts ───────────────────────────── */
function hopham_get_popular_posts($count = 5) {
    return new WP_Query([
        'posts_per_page' => $count,
        'meta_key'       => 'hopham_post_views',
        'orderby'        => 'meta_value_num',
        'order'          => 'DESC',
        'post_status'    => 'publish',
    ]);
}

/* ─── Helper: Format Vietnamese Date ──────────────────────── */
function hopham_vn_date($post_id = null) {
    if (!$post_id) $post_id = get_the_ID();
    $timestamp = get_the_time('U', $post_id);
    $months_vn = [
        1 => 'tháng 1', 2 => 'tháng 2', 3 => 'tháng 3',
        4 => 'tháng 4', 5 => 'tháng 5', 6 => 'tháng 6',
        7 => 'tháng 7', 8 => 'tháng 8', 9 => 'tháng 9',
        10 => 'tháng 10', 11 => 'tháng 11', 12 => 'tháng 12',
    ];
    $day   = date('j', $timestamp);
    $month = $months_vn[(int) date('n', $timestamp)];
    $year  = date('Y', $timestamp);
    return "$day $month $year";
}
