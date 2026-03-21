<?php get_header(); ?>

<?php
// Hero section: featured posts slider + latest news sidebar
$featured = new WP_Query([
    'posts_per_page' => 4,
    'post_status'    => 'publish',
    'meta_key'       => '_thumbnail_id',
]);
$latest = new WP_Query([
    'posts_per_page' => 3,
    'post_status'    => 'publish',
    'offset'         => 4,
]);
?>

<!-- ══════ HERO SECTION ══════ -->
<section class="hero-section">
    <div class="container hero-grid">
        <div class="hero-slider">
            <?php if ($featured->have_posts()) : $i = 0; ?>
                <div class="slider-track">
                    <?php while ($featured->have_posts()) : $featured->the_post(); ?>
                        <div class="slide <?php echo $i === 0 ? 'active' : ''; ?>">
                            <a href="<?php the_permalink(); ?>">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('hopham-hero', ['class' => 'slide-img']); ?>
                                <?php else : ?>
                                    <div class="slide-img slide-placeholder"></div>
                                <?php endif; ?>
                                <div class="slide-caption">
                                    <h2><?php the_title(); ?></h2>
                                </div>
                            </a>
                        </div>
                    <?php $i++; endwhile; ?>
                </div>
                <div class="slider-dots">
                    <?php for ($d = 0; $d < $i; $d++) : ?>
                        <button class="dot <?php echo $d === 0 ? 'active' : ''; ?>" data-slide="<?php echo $d; ?>"></button>
                    <?php endfor; ?>
                </div>
            <?php endif; wp_reset_postdata(); ?>
        </div>

        <div class="hero-news">
            <?php if ($latest->have_posts()) : ?>
                <?php while ($latest->have_posts()) : $latest->the_post(); ?>
                    <article class="news-item">
                        <a href="<?php the_permalink(); ?>">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('hopham-thumb', ['class' => 'news-thumb']); ?>
                            <?php endif; ?>
                            <div class="news-info">
                                <h4><?php the_title(); ?></h4>
                                <time datetime="<?php echo get_the_date('c'); ?>">
                                    <?php echo hopham_vn_date(); ?>
                                </time>
                            </div>
                        </a>
                    </article>
                <?php endwhile; ?>
            <?php endif; wp_reset_postdata(); ?>
        </div>
    </div>
</section>

<?php
// Người Họ Phạm section — posts from 'nguoi-ho-pham' category (fallback to latest)
$nguoi_cat = get_category_by_slug('nguoi-ho-pham');
$nguoi_args = [
    'posts_per_page' => 2,
    'post_status'    => 'publish',
];
if ($nguoi_cat) {
    $nguoi_args['cat'] = $nguoi_cat->term_id;
}
$nguoi_posts = new WP_Query($nguoi_args);

$popular = hopham_get_popular_posts(4);
?>

<!-- ══════ NGƯỜI HỌ PHẠM + POPULAR ══════ -->
<section class="section-nguoi">
    <div class="container two-col">
        <div class="col-main">
            <div class="section-header">
                <h2 class="section-title"><span class="title-deco">❧</span> Người Họ Phạm</h2>
            </div>
            <?php if ($nguoi_posts->have_posts()) : ?>
                <?php while ($nguoi_posts->have_posts()) : $nguoi_posts->the_post(); ?>
                    <article class="card card-horizontal">
                        <?php if (has_post_thumbnail()) : ?>
                            <a href="<?php the_permalink(); ?>" class="card-thumb">
                                <?php the_post_thumbnail('hopham-card'); ?>
                            </a>
                        <?php endif; ?>
                        <div class="card-body">
                            <h3 class="card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                            <div class="card-excerpt"><?php the_excerpt(); ?></div>
                            <a href="<?php the_permalink(); ?>" class="read-more">Thêm một ngày →</a>
                        </div>
                    </article>
                <?php endwhile; ?>
            <?php else : ?>
                <p class="no-posts">Chưa có bài viết nào trong mục này.</p>
            <?php endif; wp_reset_postdata(); ?>
        </div>

        <aside class="col-side">
            <div class="widget-box popular-posts">
                <div class="widget-header">
                    <span class="widget-icon">✦</span>
                    <h3>Bài Viết Đọc Nhiều</h3>
                </div>
                <?php if ($popular->have_posts()) : ?>
                    <?php while ($popular->have_posts()) : $popular->the_post(); ?>
                        <article class="mini-card">
                            <a href="<?php the_permalink(); ?>">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('hopham-thumb', ['class' => 'mini-thumb']); ?>
                                <?php endif; ?>
                                <div class="mini-info">
                                    <h4><?php the_title(); ?></h4>
                                    <time><?php echo hopham_vn_date(); ?></time>
                                </div>
                            </a>
                        </article>
                    <?php endwhile; ?>
                <?php endif; wp_reset_postdata(); ?>
                <a href="<?php echo esc_url(get_permalink(get_option('page_for_posts'))); ?>" class="widget-more">Xem thêm →</a>
            </div>
        </aside>
    </div>
</section>

<?php
// Hoạt Động Dòng Họ section
$hoatdong_cat = get_category_by_slug('hoat-dong-dong-ho');
$hoatdong_args = [
    'posts_per_page' => 3,
    'post_status'    => 'publish',
];
if ($hoatdong_cat) {
    $hoatdong_args['cat'] = $hoatdong_cat->term_id;
}
$hoatdong = new WP_Query($hoatdong_args);

$recent_comments = get_comments([
    'number'  => 4,
    'status'  => 'approve',
    'orderby' => 'comment_date_gmt',
    'order'   => 'DESC',
]);
?>

<!-- ══════ HOẠT ĐỘNG DÒNG HỌ + COMMENTS ══════ -->
<section class="section-hoatdong">
    <div class="container two-col">
        <div class="col-main">
            <div class="section-header">
                <h2 class="section-title"><span class="title-deco">❧</span> Hoạt Động Dòng Họ</h2>
            </div>
            <?php if ($hoatdong->have_posts()) : $count = 0; ?>
                <?php while ($hoatdong->have_posts()) : $hoatdong->the_post(); ?>
                    <?php if ($count === 0) : ?>
                        <article class="card card-horizontal card-featured">
                            <?php if (has_post_thumbnail()) : ?>
                                <a href="<?php the_permalink(); ?>" class="card-thumb">
                                    <?php the_post_thumbnail('hopham-card'); ?>
                                </a>
                            <?php endif; ?>
                            <div class="card-body">
                                <h3 class="card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                <div class="card-excerpt"><?php the_excerpt(); ?></div>
                                <a href="<?php the_permalink(); ?>" class="read-more">Thêm một ngày →</a>
                            </div>
                        </article>
                        <div class="card-grid">
                    <?php else : ?>
                        <article class="card card-vertical">
                            <?php if (has_post_thumbnail()) : ?>
                                <a href="<?php the_permalink(); ?>" class="card-thumb">
                                    <?php the_post_thumbnail('hopham-card'); ?>
                                </a>
                            <?php endif; ?>
                            <div class="card-body">
                                <h3 class="card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                <div class="card-excerpt"><?php the_excerpt(); ?></div>
                                <a href="<?php the_permalink(); ?>" class="read-more">Thêm một ngày →</a>
                            </div>
                        </article>
                    <?php endif; $count++; ?>
                <?php endwhile; ?>
                    </div><!-- .card-grid -->
            <?php else : ?>
                <p class="no-posts">Chưa có bài viết nào trong mục này.</p>
            <?php endif; wp_reset_postdata(); ?>
        </div>

        <aside class="col-side">
            <div class="widget-box recent-comments">
                <div class="widget-header">
                    <span class="widget-icon">✦</span>
                    <h3>Bình Luận Gần Đây</h3>
                </div>
                <?php if ($recent_comments) : ?>
                    <?php foreach ($recent_comments as $comment) : ?>
                        <div class="comment-item">
                            <div class="comment-avatar">
                                <?php echo get_avatar($comment, 40); ?>
                            </div>
                            <div class="comment-body">
                                <strong class="comment-author"><?php echo esc_html($comment->comment_author); ?></strong>
                                <p class="comment-text"><?php echo wp_trim_words($comment->comment_content, 15, '…'); ?></p>
                                <span class="comment-count">
                                    <?php
                                    $ccount = get_comments_number($comment->comment_post_ID);
                                    echo $ccount . ' comments';
                                    ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                <a href="#" class="widget-more">Xem thêm →</a>
            </div>
        </aside>
    </div>
</section>

<?php get_footer(); ?>
