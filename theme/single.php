<?php get_header(); ?>

<?php while (have_posts()) : the_post(); ?>
<article <?php post_class('single-post'); ?>>
    <div class="container">
        <div class="single-layout">
            <div class="col-main">
                <header class="entry-header">
                    <h1 class="entry-title"><?php the_title(); ?></h1>
                    <div class="entry-meta">
                        <time datetime="<?php echo get_the_date('c'); ?>"><?php echo hopham_vn_date(); ?></time>
                        <span class="meta-sep">|</span>
                        <span class="entry-author"><?php the_author(); ?></span>
                        <?php if (has_category()) : ?>
                            <span class="meta-sep">|</span>
                            <span class="entry-cats"><?php the_category(', '); ?></span>
                        <?php endif; ?>
                    </div>
                </header>

                <?php if (has_post_thumbnail()) : ?>
                    <div class="entry-thumbnail">
                        <?php the_post_thumbnail('large'); ?>
                    </div>
                <?php endif; ?>

                <div class="entry-content">
                    <?php the_content(); ?>
                    <?php
                    wp_link_pages([
                        'before' => '<div class="page-links">Trang:',
                        'after'  => '</div>',
                    ]);
                    ?>
                </div>

                <footer class="entry-footer">
                    <?php if (has_tag()) : ?>
                        <div class="entry-tags">
                            <strong>Thẻ:</strong> <?php the_tags('', ', '); ?>
                        </div>
                    <?php endif; ?>
                </footer>

                <nav class="post-navigation">
                    <div class="nav-links">
                        <?php
                        $prev = get_previous_post();
                        $next = get_next_post();
                        ?>
                        <?php if ($prev) : ?>
                            <a href="<?php echo get_permalink($prev); ?>" class="nav-prev">
                                <span class="nav-label">← Bài trước</span>
                                <span class="nav-title"><?php echo get_the_title($prev); ?></span>
                            </a>
                        <?php endif; ?>
                        <?php if ($next) : ?>
                            <a href="<?php echo get_permalink($next); ?>" class="nav-next">
                                <span class="nav-label">Bài sau →</span>
                                <span class="nav-title"><?php echo get_the_title($next); ?></span>
                            </a>
                        <?php endif; ?>
                    </div>
                </nav>

                <?php if (comments_open() || get_comments_number()) : ?>
                    <?php comments_template(); ?>
                <?php endif; ?>
            </div>
            <?php get_sidebar(); ?>
        </div>
    </div>
</article>
<?php endwhile; ?>

<?php get_footer(); ?>
