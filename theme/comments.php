<?php
/**
 * Comments Template
 */
if (post_password_required()) return;
?>

<div id="comments" class="comments-area">
    <?php if (have_comments()) : ?>
        <h3 class="comments-title">
            <?php
            $count = get_comments_number();
            printf(
                '%d Bình luận',
                $count
            );
            ?>
        </h3>

        <ol class="comment-list">
            <?php
            wp_list_comments([
                'style'       => 'ol',
                'short_ping'  => true,
                'avatar_size' => 50,
            ]);
            ?>
        </ol>

        <?php if (get_comment_pages_count() > 1) : ?>
            <nav class="comment-navigation">
                <div class="nav-links">
                    <?php previous_comments_link('← Bình luận cũ hơn'); ?>
                    <?php next_comments_link('Bình luận mới hơn →'); ?>
                </div>
            </nav>
        <?php endif; ?>
    <?php endif; ?>

    <?php
    comment_form([
        'title_reply'          => 'Để lại bình luận',
        'label_submit'         => 'Gửi bình luận',
        'comment_notes_before' => '',
    ]);
    ?>
</div>
