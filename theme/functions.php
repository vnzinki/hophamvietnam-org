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
        'https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,400;0,700;1,400;1,700&display=swap',
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
        'before_widget' => '<div id="%1$s" class="widget-box widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<div class="widget-header"><span class="widget-icon">✦</span><h3>',
        'after_title'   => '</h3></div>',
    ]);
}
add_action('widgets_init', 'hopham_widgets_init');

/* Translate default widget titles to Vietnamese */
function hopham_translate_widget_titles($title) {
    $translations = [
        'Recent Comments' => 'Bình Luận Gần Đây',
        'Recent Posts'    => 'Bài Viết Mới',
        'Categories'      => 'Danh Mục',
        'Archives'        => 'Lưu Trữ',
        'Meta'            => 'Quản Trị',
        'Search'          => 'Tìm Kiếm',
        'Pages'           => 'Trang',
        'Calendar'        => 'Lịch',
        'Tag Cloud'       => 'Thẻ',
    ];
    return $translations[$title] ?? $title;
}
add_filter('widget_title', 'hopham_translate_widget_titles');

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
    $query = new WP_Query([
        'posts_per_page'      => $count,
        'meta_key'            => 'hopham_post_views',
        'orderby'             => 'meta_value_num',
        'order'               => 'DESC',
        'post_status'         => 'publish',
        'ignore_sticky_posts' => true,
    ]);

    // Fallback to recent posts if no viewed posts found
    if (!$query->have_posts()) {
        $query = new WP_Query([
            'posts_per_page'      => $count,
            'post_status'         => 'publish',
            'ignore_sticky_posts' => true,
        ]);
    }

    return $query;
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

/* ═══════════════════════════════════════════════════════════
   SEO — Meta Tags, Open Graph, Twitter Cards, JSON-LD
   ═══════════════════════════════════════════════════════════ */

define('HOPHAM_SITE_NAME', 'Họ Phạm Việt Nam');
define('HOPHAM_DEFAULT_DESC', 'Trang thông tin dòng họ Phạm Việt Nam — kết nối, bảo tồn và phát huy truyền thống văn hóa, lịch sử dòng họ Phạm.');
define('HOPHAM_LOCALE', 'vi_VN');

/**
 * Generate meta description from content context.
 */
function hopham_get_meta_description() {
    // Front page always uses default description
    if (is_front_page() || is_home()) {
        return HOPHAM_DEFAULT_DESC;
    }

    if (is_singular()) {
        global $post;
        if (!empty($post->post_excerpt)) {
            return wp_strip_all_tags($post->post_excerpt);
        }
        $content = wp_strip_all_tags(strip_shortcodes($post->post_content));
        $content = preg_replace('/\s+/', ' ', trim($content));
        // Skip if content looks like shortcode remnants
        if (empty($content) || mb_strpos($content, '[') === 0) {
            return HOPHAM_DEFAULT_DESC;
        }
        return mb_substr($content, 0, 160, 'UTF-8') . (mb_strlen($content, 'UTF-8') > 160 ? '…' : '');
    }

    if (is_category() || is_tag() || is_tax()) {
        $desc = term_description();
        if ($desc) return wp_strip_all_tags($desc);
    }

    if (is_author()) {
        $author = get_queried_object();
        if ($author && !empty($author->description)) {
            return wp_strip_all_tags($author->description);
        }
    }

    if (is_search()) {
        return 'Kết quả tìm kiếm cho "' . get_search_query() . '" trên ' . HOPHAM_SITE_NAME;
    }

    return HOPHAM_DEFAULT_DESC;
}

/**
 * Get the best image URL for social sharing.
 */
function hopham_get_og_image() {
    if (is_singular() && has_post_thumbnail()) {
        $img = wp_get_attachment_image_src(get_post_thumbnail_id(), 'hopham-hero');
        if ($img) return $img[0];
    }

    // Fallback: site logo
    $logo_id = get_theme_mod('custom_logo');
    if ($logo_id) {
        $img = wp_get_attachment_image_src($logo_id, 'full');
        if ($img) return $img[0];
    }

    return HOPHAM_URI . '/assets/images/logo.png';
}

/**
 * Output SEO meta tags in <head>.
 */
function hopham_seo_meta_tags() {
    $desc     = hopham_get_meta_description();
    $og_image = hopham_get_og_image();
    $url      = is_singular() ? get_permalink() : home_url($_SERVER['REQUEST_URI'] ?? '/');

    // Determine og:type
    if (is_front_page()) {
        $og_type = 'website';
    } elseif (is_single()) {
        $og_type = 'article';
    } else {
        $og_type = 'website';
    }

    // Build title
    if (is_singular()) {
        $og_title = get_the_title();
    } elseif (is_category()) {
        $og_title = single_cat_title('', false);
    } elseif (is_tag()) {
        $og_title = single_tag_title('', false);
    } elseif (is_search()) {
        $og_title = 'Tìm kiếm: ' . get_search_query();
    } else {
        $og_title = get_bloginfo('name');
    }

    echo "\n<!-- SEO — Họ Phạm Việt Nam -->\n";

    // Meta description
    if ($desc) {
        printf('<meta name="description" content="%s">' . "\n", esc_attr($desc));
    }

    // Canonical URL
    if (is_singular()) {
        printf('<link rel="canonical" href="%s">' . "\n", esc_url(get_permalink()));
    }

    // Open Graph
    printf('<meta property="og:locale" content="%s">' . "\n", esc_attr(HOPHAM_LOCALE));
    printf('<meta property="og:type" content="%s">' . "\n", esc_attr($og_type));
    printf('<meta property="og:title" content="%s">' . "\n", esc_attr($og_title));
    if ($desc) {
        printf('<meta property="og:description" content="%s">' . "\n", esc_attr($desc));
    }
    printf('<meta property="og:url" content="%s">' . "\n", esc_url($url));
    printf('<meta property="og:site_name" content="%s">' . "\n", esc_attr(HOPHAM_SITE_NAME));
    printf('<meta property="og:image" content="%s">' . "\n", esc_url($og_image));

    // Article-specific OG tags
    if (is_single()) {
        printf('<meta property="article:published_time" content="%s">' . "\n", esc_attr(get_the_date('c')));
        printf('<meta property="article:modified_time" content="%s">' . "\n", esc_attr(get_the_modified_date('c')));
        $cats = get_the_category();
        if ($cats) {
            printf('<meta property="article:section" content="%s">' . "\n", esc_attr($cats[0]->name));
        }
        $tags = get_the_tags();
        if ($tags) {
            foreach ($tags as $tag) {
                printf('<meta property="article:tag" content="%s">' . "\n", esc_attr($tag->name));
            }
        }
    }

    // Twitter Card
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    printf('<meta name="twitter:title" content="%s">' . "\n", esc_attr($og_title));
    if ($desc) {
        printf('<meta name="twitter:description" content="%s">' . "\n", esc_attr($desc));
    }
    printf('<meta name="twitter:image" content="%s">' . "\n", esc_url($og_image));

    echo "<!-- /SEO -->\n";
}
add_action('wp_head', 'hopham_seo_meta_tags', 1);

/**
 * Output JSON-LD structured data.
 */
function hopham_seo_jsonld() {
    $schemas = [];

    // Organization schema (every page)
    $schemas[] = [
        '@type' => 'Organization',
        'name'  => HOPHAM_SITE_NAME,
        'url'   => home_url('/'),
        'logo'  => HOPHAM_URI . '/assets/images/logo.png',
    ];

    // WebSite schema with search action (every page)
    $schemas[] = [
        '@type'           => 'WebSite',
        'name'            => HOPHAM_SITE_NAME,
        'url'             => home_url('/'),
        'description'     => HOPHAM_DEFAULT_DESC,
        'inLanguage'      => 'vi',
        'potentialAction' => [
            '@type'       => 'SearchAction',
            'target'      => home_url('/?s={search_term_string}'),
            'query-input' => 'required name=search_term_string',
        ],
    ];

    // BreadcrumbList
    $breadcrumbs = hopham_get_breadcrumbs();
    if (!empty($breadcrumbs)) {
        $items = [];
        foreach ($breadcrumbs as $i => $crumb) {
            $items[] = [
                '@type'    => 'ListItem',
                'position' => $i + 1,
                'name'     => $crumb['name'],
                'item'     => $crumb['url'],
            ];
        }
        $schemas[] = [
            '@type'           => 'BreadcrumbList',
            'itemListElement' => $items,
        ];
    }

    // Article schema (single posts)
    if (is_single()) {
        global $post;
        $article = [
            '@type'            => 'Article',
            'headline'         => get_the_title(),
            'url'              => get_permalink(),
            'datePublished'    => get_the_date('c'),
            'dateModified'     => get_the_modified_date('c'),
            'author'           => [
                '@type' => 'Person',
                'name'  => get_the_author(),
            ],
            'publisher'        => [
                '@type' => 'Organization',
                'name'  => HOPHAM_SITE_NAME,
                'logo'  => [
                    '@type' => 'ImageObject',
                    'url'   => HOPHAM_URI . '/assets/images/logo.png',
                ],
            ],
            'mainEntityOfPage' => get_permalink(),
        ];

        if (has_post_thumbnail()) {
            $img = wp_get_attachment_image_src(get_post_thumbnail_id(), 'large');
            if ($img) {
                $article['image'] = [
                    '@type'  => 'ImageObject',
                    'url'    => $img[0],
                    'width'  => $img[1],
                    'height' => $img[2],
                ];
            }
        }

        $desc = hopham_get_meta_description();
        if ($desc) {
            $article['description'] = $desc;
        }

        $schemas[] = $article;
    }

    // Output
    $graph = [
        '@context' => 'https://schema.org',
        '@graph'   => $schemas,
    ];

    echo '<script type="application/ld+json">' . "\n";
    echo wp_json_encode($graph, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    echo "\n</script>\n";
}
add_action('wp_head', 'hopham_seo_jsonld', 2);

/**
 * Build breadcrumb array for structured data.
 */
function hopham_get_breadcrumbs() {
    $crumbs = [['name' => 'Trang chủ', 'url' => home_url('/')]];

    if (is_singular()) {
        $cats = get_the_category();
        if ($cats) {
            $crumbs[] = ['name' => $cats[0]->name, 'url' => get_category_link($cats[0]->term_id)];
        }
        $crumbs[] = ['name' => get_the_title(), 'url' => get_permalink()];
    } elseif (is_category()) {
        $crumbs[] = ['name' => single_cat_title('', false), 'url' => get_category_link(get_queried_object_id())];
    } elseif (is_tag()) {
        $crumbs[] = ['name' => single_tag_title('', false), 'url' => get_tag_link(get_queried_object_id())];
    } elseif (is_search()) {
        $crumbs[] = ['name' => 'Tìm kiếm: ' . get_search_query(), 'url' => get_search_link()];
    }

    return count($crumbs) > 1 ? $crumbs : [];
}

/**
 * Customize document title separator and structure.
 */
function hopham_document_title_parts($title) {
    $title['tagline'] = '';
    return $title;
}
add_filter('document_title_parts', 'hopham_document_title_parts');

function hopham_document_title_separator() {
    return '—';
}
add_filter('document_title_separator', 'hopham_document_title_separator');
