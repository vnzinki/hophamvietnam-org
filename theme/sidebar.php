<aside class="col-side sidebar">
    <?php if (is_active_sidebar('sidebar-main')) : ?>
        <?php dynamic_sidebar('sidebar-main'); ?>
    <?php else : ?>
        <div class="widget-box">
            <div class="widget-header">
                <span class="widget-icon">✦</span>
                <h3>Bài Viết Đọc Nhiều</h3>
            </div>
            <?php
            $popular = hopham_get_popular_posts(5);
            if ($popular->have_posts()) :
                while ($popular->have_posts()) : $popular->the_post();
            ?>
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
            <?php
                endwhile;
            endif;
            wp_reset_postdata();
            ?>
        </div>
    <?php endif; ?>
</aside>
