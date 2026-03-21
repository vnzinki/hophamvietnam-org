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
    <div class="header-inner">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="site-logo">
            <span class="logo-icon">☸</span>
            <span class="logo-text"><?php bloginfo('name'); ?></span>
        </a>
    </div>
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
        </div>
    </nav>
</header>

<main class="site-main">
<?php

function hopham_fallback_menu() {
    echo '<ul class="nav-menu">';
    echo '<li class="current-menu-item"><a href="' . esc_url(home_url('/')) . '">Trang Chủ</a></li>';
    echo '<li><a href="#">Tin Tức</a></li>';
    echo '<li><a href="#">Người Họ Phạm</a></li>';
    echo '<li><a href="#">Văn Hòa</a></li>';
    echo '<li><a href="#">Gia Phả</a></li>';
    echo '<li><a href="#">Thư Viện</a></li>';
    echo '<li><a href="#">Liên Hệ</a></li>';
    echo '</ul>';
}
