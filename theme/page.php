<?php get_header(); ?>

<div class="container single-layout">
    <div class="col-main">
        <?php while (have_posts()) : the_post(); ?>
            <article <?php post_class('page-content'); ?>>
                <header class="entry-header">
                    <h1 class="entry-title"><?php the_title(); ?></h1>
                </header>
                <?php if (has_post_thumbnail()) : ?>
                    <div class="entry-thumbnail">
                        <?php the_post_thumbnail('large'); ?>
                    </div>
                <?php endif; ?>
                <div class="entry-content">
                    <?php the_content(); ?>
                </div>
            </article>
        <?php endwhile; ?>
    </div>
    <?php get_sidebar(); ?>
</div>

<?php get_footer(); ?>
