<?php get_header(); ?>

<div class="container page-404">
    <div class="error-content">
        <h1>404</h1>
        <p>Trang bạn tìm kiếm không tồn tại.</p>
        <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary">← Về trang chủ</a>
    </div>
</div>

<?php get_footer(); ?>
