<?php get_header(); ?>

<div class="container archive-layout">
    <div class="col-main">
        <?php if (have_posts()) : ?>
            <header class="page-header">
                <?php
                if (is_category()) {
                    echo '<h1 class="page-title">' . single_cat_title('', false) . '</h1>';
                } elseif (is_tag()) {
                    echo '<h1 class="page-title">Thẻ: ' . single_tag_title('', false) . '</h1>';
                } elseif (is_search()) {
                    echo '<h1 class="page-title">Kết quả tìm kiếm: ' . get_search_query() . '</h1>';
                } else {
                    echo '<h1 class="page-title">Tin Tức</h1>';
                }
                ?>
            </header>
            <div class="posts-list">
                <?php while (have_posts()) : the_post(); ?>
                    <article <?php post_class('card card-horizontal'); ?>>
                        <?php if (has_post_thumbnail()) : ?>
                            <a href="<?php the_permalink(); ?>" class="card-thumb">
                                <?php the_post_thumbnail('hopham-card'); ?>
                            </a>
                        <?php endif; ?>
                        <div class="card-body">
                            <h2 class="card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                            <div class="card-meta">
                                <time datetime="<?php echo get_the_date('c'); ?>"><?php echo hopham_vn_date(); ?></time>
                            </div>
                            <div class="card-excerpt"><?php the_excerpt(); ?></div>
                            <a href="<?php the_permalink(); ?>" class="read-more">Đọc tiếp →</a>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
            <div class="pagination">
                <?php
                the_posts_pagination([
                    'mid_size'  => 2,
                    'prev_text' => '← Trước',
                    'next_text' => 'Sau →',
                ]);
                ?>
            </div>
        <?php else : ?>
            <p class="no-posts">Không tìm thấy bài viết nào.</p>
        <?php endif; ?>
    </div>
    <?php get_sidebar(); ?>
</div>

<?php get_footer(); ?>
