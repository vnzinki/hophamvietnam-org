<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
    <!-- Dragon watermark — Eastern Dragon (Wikimedia Commons, CC0 Public Domain) -->
    <div class="header-dragon-watermark" aria-hidden="true">
        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/dragon-eastern-art.svg'); ?>" alt="">
    </div>

    <!-- Gold ornamental frame -->
    <div class="header-frame" aria-hidden="true">
        <div class="frame-corner frame-corner-tl"></div>
        <div class="frame-corner frame-corner-tr"></div>
        <div class="frame-corner frame-corner-bl"></div>
        <div class="frame-corner frame-corner-br"></div>
    </div>

    <!-- Vietnamese Dragon ornaments (Wikimedia Commons, CC-BY-SA 4.0, by Goran tek-en) -->
    <div class="header-dragon header-dragon-left" aria-hidden="true">
        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/vietnamese-dragon-gold.svg'); ?>" alt="">
    </div>
    <div class="header-dragon header-dragon-right" aria-hidden="true">
        <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/vietnamese-dragon-gold.svg'); ?>" alt="">
    </div>

    <!-- Central temple plaque -->
    <div class="temple-plaque">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="site-logo">
            <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/logo.png'); ?>" alt="<?php bloginfo('name'); ?>" class="logo-img">
            <span class="logo-text"><?php bloginfo('name'); ?></span>
        </a>
    </div>

    <!-- Gold ornamental divider -->
    <div class="header-divider" aria-hidden="true">
        <span class="divider-dot"></span>
        <span class="divider-line"></span>
        <span class="divider-diamond"></span>
        <span class="divider-line"></span>
        <span class="divider-dot"></span>
    </div>

</header>

<!-- Main navigation — separate from header, sticky on scroll -->
<nav class="main-nav">
    <div class="nav-inner">
        <button class="menu-toggle" aria-label="Menu">
            <span></span><span></span><span></span>
        </button>
        <?php
        wp_nav_menu([
            'theme_location' => 'primary',
            'container'      => false,
            'menu_class'     => 'nav-menu',
            'fallback_cb'    => 'hopham_fallback_menu',
        ]);
        ?>
        <button class="nav-search-toggle" aria-label="Tìm kiếm">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M15.5 14h-.79l-.28-.27A6.471 6.471 0 0016 9.5 6.5 6.5 0 109.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/></svg>
        </button>
    </div>

    <div class="search-overlay" id="searchOverlay">
        <div class="search-overlay-inner">
            <?php get_search_form(); ?>
            <button class="search-close" aria-label="Đóng">&times;</button>
        </div>
    </div>
</nav>

<main class="site-main">
<?php

function hopham_fallback_menu() {
    $cats = [
        'thong-bao'       => 'Thông Báo',
        'hoat-dong'       => 'Hoạt Động',
        'nguoi-ho-pham'   => 'Người Họ Phạm',
        'van-to-tam-tong' => 'Vấn Tổ Tầm Tông',
        'thu-vien'        => 'Thư Viện',
    ];
    echo '<ul class="nav-menu">';
    echo '<li class="current-menu-item"><a href="' . esc_url(home_url('/')) . '">Trang Chủ</a></li>';
    foreach ($cats as $slug => $label) {
        $cat = get_category_by_slug($slug);
        $url = $cat ? get_category_link($cat->term_id) : '#';
        echo '<li><a href="' . esc_url($url) . '">' . esc_html($label) . '</a></li>';
    }
    echo '<li><a href="' . esc_url(home_url('/lien-he/')) . '">Liên Hệ</a></li>';
    echo '</ul>';
}
