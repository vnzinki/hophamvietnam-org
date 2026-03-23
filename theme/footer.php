</main><!-- .site-main -->

<footer class="site-footer">
    <!-- Chim Lạc decorative band — Dong Son bronze drum bird motif -->
    <div class="chim-lac-band" aria-hidden="true"></div>
    <div class="footer-inner">

        <!-- Column 1: About -->
        <div class="footer-col footer-about">
            <h4>Hội Đồng Họ Phạm Việt Nam</h4>
            <p class="footer-tagline">Cổng thông tin chính thức</p>
            <ul class="footer-contact">
                <li>
                    <svg class="footer-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5A2.5 2.5 0 1 1 12 6a2.5 2.5 0 0 1 0 5.5z"/></svg>
                    <span>Số 80 phố Chùa Bộc, P. Quang Trung, Q. Đống Đa, Hà Nội</span>
                </li>
                <li>
                    <svg class="footer-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M22 4L12 13 2 4"/></svg>
                    <span><a href="mailto:cauphamvu@gmail.com">cauphamvu@gmail.com</a></span>
                </li>
                <li>
                    <svg class="footer-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="5" y="2" width="14" height="20" rx="2"/><circle cx="12" cy="18" r="1"/></svg>
                    <span><a href="tel:0913241718">0913 241 718</a></span>
                </li>
            </ul>
        </div>

        <!-- Column 2: People / Contacts -->
        <div class="footer-col footer-people">
            <h4>Ban Biên Tập</h4>
            <div class="footer-person">
                <span class="person-role">Chủ tịch</span>
                <span class="person-name">Ts. Phạm Vũ Câu</span>
                <span class="person-contact">
                    <a href="tel:0913241718">☎ 0913 241 718</a>
                    <a href="mailto:cauphamvu@gmail.com">✉ Email</a>
                </span>
            </div>
            <div class="footer-person">
                <span class="person-role">Tổng biên tập</span>
                <span class="person-name">Phạm Duy Hưng</span>
                <span class="person-contact">
                    <a href="tel:0981188968">☎ 0981 188 968</a>
                    <a href="mailto:tttlhpvn@gmail.com">✉ Email</a>
                </span>
            </div>
        </div>

        <!-- Column 3: Categories -->
        <div class="footer-col footer-links">
            <h4>Danh Mục</h4>
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

    <!-- Footer bottom bar -->
    <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> <strong>Hội Đồng Họ Phạm Việt Nam</strong> — Hoạt động từ tháng 2/2005</p>
        <p class="footer-reuse">Vui lòng ghi nguồn khi trích dẫn thông tin từ trang web này.</p>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
