</main><!-- .site-main -->

<footer class="site-footer">
    <div class="footer-dragon-top"></div>
    <div class="footer-inner">
        <div class="footer-col footer-about">
            <h4>Thông tin</h4>
            <div class="footer-info-text">
                <p>&copy; Bản quyền trang web này thuộc về <strong>HỘI ĐỒNG HỌ PHẠM VIỆT NAM</strong>.</p>
                <p>Trụ sở: Số 80 phố Chùa Bộc, Phường Quang Trung, Quận Đống Đa, Thành phố Hà Nội</p>
                <p>Điện thoại: Chủ tịch: Ts. Phạm Vũ Câu 0913241718 - Email: <a href="mailto:cauphamvu@gmail.com">cauphamvu@gmail.com</a></p>
                <p>Tổng biên tập: Phạm Duy Hưng - 0981188968 - Email: <a href="mailto:tttlhpvn@gmail.com">tttlhpvn@gmail.com</a></p>
                <p>- Trang web của Họ Phạm Việt Nam bắt đầu hoạt động từ Tháng 2 năm 2005.</p>
                <p>- Vui lòng để liên kết về nguồn tin nếu bạn xuất bản lại thông tin trên trang web này.</p>
            </div>
        </div>

        <div class="footer-col footer-links">
            <h4>Danh mục</h4>
            <?php
            $footer_cats = get_categories([
                'orderby' => 'count',
                'order'   => 'DESC',
                'number'  => 8,
            ]);
            if ($footer_cats) {
                echo '<ul class="footer-categories">';
                foreach ($footer_cats as $cat) {
                    echo '<li>';
                    echo '<a href="' . esc_url(get_category_link($cat->term_id)) . '">';
                    echo esc_html($cat->name);
                    echo '<span class="cat-count">' . (int) $cat->count . '</span>';
                    echo '</a></li>';
                }
                echo '</ul>';
            }
            ?>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
