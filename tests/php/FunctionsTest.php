<?php
/**
 * Unit tests for theme/functions.php
 *
 * Covers:
 *   - hopham_translate_widget_titles()
 *   - hopham_excerpt_length() / hopham_excerpt_more()
 *   - hopham_document_title_parts() / hopham_document_title_separator()
 *   - hopham_get_post_views() / hopham_set_post_views()
 *   - hopham_vn_date()
 *   - hopham_track_post_views()
 *   - hopham_get_meta_description()
 *   - hopham_get_og_image()
 *   - hopham_get_breadcrumbs()
 */

declare(strict_types=1);

use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\TestCase;

/**
 * Base test case that sets up / tears down Brain Monkey for each test.
 */
abstract class HophamTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Monkey\setUp();
    }

    protected function tearDown(): void
    {
        Monkey\tearDown();
        parent::tearDown();
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// hopham_translate_widget_titles
// ─────────────────────────────────────────────────────────────────────────────

class TranslateWidgetTitlesTest extends HophamTestCase
{
    /** @dataProvider knownTitlesProvider */
    public function test_known_titles_are_translated(string $english, string $vietnamese): void
    {
        $this->assertSame($vietnamese, hopham_translate_widget_titles($english));
    }

    public function knownTitlesProvider(): array
    {
        return [
            ['Recent Comments', 'Bình Luận Gần Đây'],
            ['Recent Posts',    'Bài Viết Mới'],
            ['Categories',      'Danh Mục'],
            ['Archives',        'Lưu Trữ'],
            ['Meta',            'Quản Trị'],
            ['Search',          'Tìm Kiếm'],
            ['Pages',           'Trang'],
            ['Calendar',        'Lịch'],
            ['Tag Cloud',       'Thẻ'],
        ];
    }

    public function test_unknown_title_is_returned_unchanged(): void
    {
        $this->assertSame('Custom Widget', hopham_translate_widget_titles('Custom Widget'));
    }

    public function test_empty_string_is_returned_unchanged(): void
    {
        $this->assertSame('', hopham_translate_widget_titles(''));
    }

    public function test_partial_match_is_not_translated(): void
    {
        // 'Recent' alone should not match 'Recent Posts'
        $this->assertSame('Recent', hopham_translate_widget_titles('Recent'));
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// hopham_excerpt_length / hopham_excerpt_more
// ─────────────────────────────────────────────────────────────────────────────

class ExcerptFiltersTest extends HophamTestCase
{
    public function test_excerpt_length_returns_30(): void
    {
        $this->assertSame(30, hopham_excerpt_length(55));
    }

    public function test_excerpt_length_ignores_original_value(): void
    {
        $this->assertSame(30, hopham_excerpt_length(0));
        $this->assertSame(30, hopham_excerpt_length(999));
    }

    public function test_excerpt_more_returns_ellipsis(): void
    {
        $this->assertSame('…', hopham_excerpt_more(''));
    }

    public function test_excerpt_more_ignores_original_more_string(): void
    {
        $this->assertSame('…', hopham_excerpt_more('[more]'));
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// hopham_document_title_parts / hopham_document_title_separator
// ─────────────────────────────────────────────────────────────────────────────

class DocumentTitleTest extends HophamTestCase
{
    public function test_separator_is_em_dash(): void
    {
        $this->assertSame('—', hopham_document_title_separator());
    }

    public function test_title_parts_removes_tagline(): void
    {
        $input  = ['title' => 'My Post', 'tagline' => 'Site Tagline', 'site' => 'My Site'];
        $result = hopham_document_title_parts($input);
        $this->assertSame('', $result['tagline']);
    }

    public function test_title_parts_preserves_other_keys(): void
    {
        $input  = ['title' => 'Test', 'tagline' => 'old', 'site' => 'Blog'];
        $result = hopham_document_title_parts($input);
        $this->assertSame('Test', $result['title']);
        $this->assertSame('Blog', $result['site']);
    }

    public function test_title_parts_works_when_tagline_missing(): void
    {
        $input  = ['title' => 'Test'];
        $result = hopham_document_title_parts($input);
        $this->assertSame('', $result['tagline']);
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// hopham_get_post_views / hopham_set_post_views
// ─────────────────────────────────────────────────────────────────────────────

class PostViewsTest extends HophamTestCase
{
    public function test_get_post_views_returns_integer_count(): void
    {
        Functions\when('get_post_meta')->justReturn('42');
        $this->assertSame(42, hopham_get_post_views(1));
    }

    public function test_get_post_views_returns_zero_when_no_meta(): void
    {
        Functions\when('get_post_meta')->justReturn('');
        $this->assertSame(0, hopham_get_post_views(99));
    }

    public function test_get_post_views_returns_zero_for_falsy_meta(): void
    {
        Functions\when('get_post_meta')->justReturn(false);
        $this->assertSame(0, hopham_get_post_views(7));
    }

    public function test_set_post_views_increments_count(): void
    {
        Functions\when('get_post_meta')->justReturn('5');

        Functions\expect('update_post_meta')
            ->once()
            ->with(10, 'hopham_post_views', 6);

        hopham_set_post_views(10);
        $this->addToAssertionCount(1);
    }

    public function test_set_post_views_starts_at_one_when_no_prior_count(): void
    {
        Functions\when('get_post_meta')->justReturn('');

        Functions\expect('update_post_meta')
            ->once()
            ->with(3, 'hopham_post_views', 1);

        hopham_set_post_views(3);
        $this->addToAssertionCount(1);
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// hopham_vn_date
// ─────────────────────────────────────────────────────────────────────────────

class VnDateTest extends HophamTestCase
{
    public function test_returns_formatted_vietnamese_date(): void
    {
        // 2024-03-15  →  "15 tháng 3 2024"
        $timestamp = mktime(0, 0, 0, 3, 15, 2024);

        Functions\when('get_the_ID')->justReturn(1);
        Functions\when('get_the_time')->justReturn($timestamp);

        $result = hopham_vn_date(1);
        $this->assertSame('15 tháng 3 2024', $result);
    }

    public function test_uses_get_the_id_when_no_post_id_given(): void
    {
        $timestamp = mktime(0, 0, 0, 12, 1, 2023);

        Functions\when('get_the_ID')->justReturn(5);
        Functions\when('get_the_time')->justReturn($timestamp);

        $result = hopham_vn_date();
        $this->assertSame('1 tháng 12 2023', $result);
    }

    /** @dataProvider monthProvider */
    public function test_all_months_translated(int $month, string $expected): void
    {
        $timestamp = mktime(0, 0, 0, $month, 1, 2024);

        Functions\when('get_the_ID')->justReturn(1);
        Functions\when('get_the_time')->justReturn($timestamp);

        $result = hopham_vn_date(1);
        $this->assertStringContainsString($expected, $result);
    }

    public function monthProvider(): array
    {
        return [
            [1,  'tháng 1'],
            [2,  'tháng 2'],
            [3,  'tháng 3'],
            [4,  'tháng 4'],
            [5,  'tháng 5'],
            [6,  'tháng 6'],
            [7,  'tháng 7'],
            [8,  'tháng 8'],
            [9,  'tháng 9'],
            [10, 'tháng 10'],
            [11, 'tháng 11'],
            [12, 'tháng 12'],
        ];
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// hopham_track_post_views
// ─────────────────────────────────────────────────────────────────────────────

class TrackPostViewsTest extends HophamTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Reset superglobals
        $_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (Human Browser)';
        $_COOKIE = [];
    }

    protected function tearDown(): void
    {
        $_SERVER['HTTP_USER_AGENT'] = '';
        $_COOKIE = [];
        parent::tearDown();
    }

    public function test_does_nothing_when_not_single(): void
    {
        Functions\when('is_single')->justReturn(false);

        // update_post_meta must NOT be called
        Functions\expect('update_post_meta')->never();

        hopham_track_post_views();
        $this->addToAssertionCount(1);
    }

    public function test_does_nothing_in_admin(): void
    {
        Functions\when('is_single')->justReturn(true);
        Functions\when('is_admin')->justReturn(true);

        Functions\expect('update_post_meta')->never();

        hopham_track_post_views();
        $this->addToAssertionCount(1);
    }

    public function test_does_nothing_for_logged_in_user(): void
    {
        Functions\when('is_single')->justReturn(true);
        Functions\when('is_admin')->justReturn(false);
        Functions\when('is_user_logged_in')->justReturn(true);

        Functions\expect('update_post_meta')->never();

        hopham_track_post_views();
        $this->addToAssertionCount(1);
    }

    public function test_does_nothing_when_user_agent_is_empty(): void
    {
        $_SERVER['HTTP_USER_AGENT'] = '';

        Functions\when('is_single')->justReturn(true);
        Functions\when('is_admin')->justReturn(false);
        Functions\when('is_user_logged_in')->justReturn(false);

        Functions\expect('update_post_meta')->never();

        hopham_track_post_views();
        $this->addToAssertionCount(1);
    }

    /** @dataProvider botUserAgentProvider */
    public function test_does_nothing_for_bots(string $ua): void
    {
        $_SERVER['HTTP_USER_AGENT'] = $ua;

        Functions\when('is_single')->justReturn(true);
        Functions\when('is_admin')->justReturn(false);
        Functions\when('is_user_logged_in')->justReturn(false);

        Functions\expect('update_post_meta')->never();

        hopham_track_post_views();
        $this->addToAssertionCount(1);
    }

    public function botUserAgentProvider(): array
    {
        return [
            ['Googlebot/2.1'],
            ['Mozilla/5.0 (compatible; bingbot/2.0)'],
            ['Yahoo! Slurp'],
            ['Feedfetcher-Google'],
            ['crawler/1.0'],
            ['SpiderBot'],
        ];
    }

    public function test_does_nothing_when_viewed_cookie_set(): void
    {
        global $post;
        $post     = new stdClass();
        $post->ID = 42;

        $_COOKIE['hopham_viewed_42'] = '1';

        Functions\when('is_single')->justReturn(true);
        Functions\when('is_admin')->justReturn(false);
        Functions\when('is_user_logged_in')->justReturn(false);

        Functions\expect('update_post_meta')->never();

        hopham_track_post_views();
        $this->addToAssertionCount(1);

        // Restore global
        $post = null;
    }

    public function test_increments_view_count_for_real_visitor(): void
    {
        global $post;
        $post     = new stdClass();
        $post->ID = 7;

        Functions\when('is_single')->justReturn(true);
        Functions\when('is_admin')->justReturn(false);
        Functions\when('is_user_logged_in')->justReturn(false);
        Functions\when('get_post_meta')->justReturn('3');
        // setcookie() cannot send HTTP headers in CLI/PHPUnit context; stub it out
        Functions\when('setcookie')->justReturn(true);

        Functions\expect('update_post_meta')
            ->once()
            ->with(7, 'hopham_post_views', 4);

        hopham_track_post_views();
        $this->addToAssertionCount(1);

        $post = null;
    }

    public function test_does_nothing_when_post_id_is_empty(): void
    {
        global $post;
        $post = new stdClass();
        // No ID property

        Functions\when('is_single')->justReturn(true);
        Functions\when('is_admin')->justReturn(false);
        Functions\when('is_user_logged_in')->justReturn(false);

        Functions\expect('update_post_meta')->never();

        hopham_track_post_views();
        $this->addToAssertionCount(1);

        $post = null;
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// hopham_get_meta_description
// ─────────────────────────────────────────────────────────────────────────────

class MetaDescriptionTest extends HophamTestCase
{
    public function test_returns_default_desc_on_front_page(): void
    {
        Functions\when('is_front_page')->justReturn(true);
        Functions\when('is_home')->justReturn(false);

        $result = hopham_get_meta_description();
        $this->assertSame(HOPHAM_DEFAULT_DESC, $result);
    }

    public function test_returns_default_desc_on_home_page(): void
    {
        Functions\when('is_front_page')->justReturn(false);
        Functions\when('is_home')->justReturn(true);

        $result = hopham_get_meta_description();
        $this->assertSame(HOPHAM_DEFAULT_DESC, $result);
    }

    public function test_returns_post_excerpt_when_singular_with_excerpt(): void
    {
        global $post;
        $post               = new stdClass();
        $post->post_excerpt = 'This is a custom excerpt.';
        $post->post_content = 'Long content here.';

        Functions\when('is_front_page')->justReturn(false);
        Functions\when('is_home')->justReturn(false);
        Functions\when('is_singular')->justReturn(true);
        Functions\when('wp_strip_all_tags')->returnArg();

        $result = hopham_get_meta_description();
        $this->assertSame('This is a custom excerpt.', $result);

        $post = null;
    }

    public function test_falls_back_to_post_content_when_no_excerpt(): void
    {
        global $post;
        $post               = new stdClass();
        $post->post_excerpt = '';
        $post->post_content = 'This is the post content body text.';

        Functions\when('is_front_page')->justReturn(false);
        Functions\when('is_home')->justReturn(false);
        Functions\when('is_singular')->justReturn(true);
        Functions\when('wp_strip_all_tags')->returnArg();
        Functions\when('strip_shortcodes')->returnArg();

        $result = hopham_get_meta_description();
        $this->assertStringContainsString('This is the post content body text.', $result);

        $post = null;
    }

    public function test_truncates_long_content_to_160_chars(): void
    {
        global $post;
        $post               = new stdClass();
        $post->post_excerpt = '';
        $post->post_content = str_repeat('a', 200);

        Functions\when('is_front_page')->justReturn(false);
        Functions\when('is_home')->justReturn(false);
        Functions\when('is_singular')->justReturn(true);
        Functions\when('wp_strip_all_tags')->returnArg();
        Functions\when('strip_shortcodes')->returnArg();

        $result = hopham_get_meta_description();
        // 160 chars + '…'
        $this->assertSame(mb_strlen($result, 'UTF-8'), 161);
        $this->assertStringEndsWith('…', $result);

        $post = null;
    }

    public function test_does_not_append_ellipsis_when_content_fits(): void
    {
        global $post;
        $post               = new stdClass();
        $post->post_excerpt = '';
        $post->post_content = str_repeat('b', 100);

        Functions\when('is_front_page')->justReturn(false);
        Functions\when('is_home')->justReturn(false);
        Functions\when('is_singular')->justReturn(true);
        Functions\when('wp_strip_all_tags')->returnArg();
        Functions\when('strip_shortcodes')->returnArg();

        $result = hopham_get_meta_description();
        $this->assertStringNotContainsString('…', $result);

        $post = null;
    }

    public function test_returns_default_desc_for_shortcode_only_content(): void
    {
        global $post;
        $post               = new stdClass();
        $post->post_excerpt = '';
        $post->post_content = '[gallery ids="1,2,3"]';

        Functions\when('is_front_page')->justReturn(false);
        Functions\when('is_home')->justReturn(false);
        Functions\when('is_singular')->justReturn(true);
        Functions\when('wp_strip_all_tags')->returnArg();
        // strip_shortcodes removes the shortcode leaving empty string
        Functions\when('strip_shortcodes')->justReturn('');

        $result = hopham_get_meta_description();
        $this->assertSame(HOPHAM_DEFAULT_DESC, $result);

        $post = null;
    }

    public function test_returns_term_description_for_category(): void
    {
        Functions\when('is_front_page')->justReturn(false);
        Functions\when('is_home')->justReturn(false);
        Functions\when('is_singular')->justReturn(false);
        Functions\when('is_category')->justReturn(true);
        Functions\when('term_description')->justReturn('<p>Category about history.</p>');
        Functions\when('wp_strip_all_tags')->returnArg(1);

        $result = hopham_get_meta_description();
        $this->assertStringContainsString('Category about history.', $result);
    }

    public function test_returns_search_description_on_search_page(): void
    {
        Functions\when('is_front_page')->justReturn(false);
        Functions\when('is_home')->justReturn(false);
        Functions\when('is_singular')->justReturn(false);
        Functions\when('is_category')->justReturn(false);
        Functions\when('is_tag')->justReturn(false);
        Functions\when('is_tax')->justReturn(false);
        Functions\when('is_author')->justReturn(false);
        Functions\when('is_search')->justReturn(true);
        Functions\when('get_search_query')->justReturn('Phạm Văn');

        $result = hopham_get_meta_description();
        $this->assertStringContainsString('Phạm Văn', $result);
    }

    public function test_returns_default_desc_as_final_fallback(): void
    {
        Functions\when('is_front_page')->justReturn(false);
        Functions\when('is_home')->justReturn(false);
        Functions\when('is_singular')->justReturn(false);
        Functions\when('is_category')->justReturn(false);
        Functions\when('is_tag')->justReturn(false);
        Functions\when('is_tax')->justReturn(false);
        Functions\when('is_author')->justReturn(false);
        Functions\when('is_search')->justReturn(false);

        $result = hopham_get_meta_description();
        $this->assertSame(HOPHAM_DEFAULT_DESC, $result);
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// hopham_get_og_image
// ─────────────────────────────────────────────────────────────────────────────

class OgImageTest extends HophamTestCase
{
    public function test_returns_post_thumbnail_when_singular_with_thumbnail(): void
    {
        Functions\when('is_singular')->justReturn(true);
        Functions\when('has_post_thumbnail')->justReturn(true);
        Functions\when('get_post_thumbnail_id')->justReturn(5);
        Functions\when('wp_get_attachment_image_src')
            ->justReturn(['https://example.com/photo.jpg', 800, 500]);

        $result = hopham_get_og_image();
        $this->assertSame('https://example.com/photo.jpg', $result);
    }

    public function test_falls_back_to_custom_logo_when_no_thumbnail(): void
    {
        Functions\when('is_singular')->justReturn(true);
        Functions\when('has_post_thumbnail')->justReturn(false);
        Functions\when('get_theme_mod')->justReturn(3);
        Functions\when('wp_get_attachment_image_src')
            ->justReturn(['https://example.com/logo.png', 80, 80]);

        $result = hopham_get_og_image();
        $this->assertSame('https://example.com/logo.png', $result);
    }

    public function test_falls_back_to_default_logo_path_when_no_logo_attachment(): void
    {
        Functions\when('is_singular')->justReturn(false);
        Functions\when('get_theme_mod')->justReturn(0); // no custom logo
        Functions\when('has_post_thumbnail')->justReturn(false);

        $result = hopham_get_og_image();
        $this->assertStringEndsWith('/assets/images/logo.png', $result);
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// hopham_get_breadcrumbs
// ─────────────────────────────────────────────────────────────────────────────

class BreadcrumbsTest extends HophamTestCase
{
    public function test_returns_empty_array_on_front_page(): void
    {
        Functions\when('home_url')->justReturn('https://example.com/');
        Functions\when('is_singular')->justReturn(false);
        Functions\when('is_category')->justReturn(false);
        Functions\when('is_tag')->justReturn(false);
        Functions\when('is_search')->justReturn(false);

        $result = hopham_get_breadcrumbs();
        $this->assertSame([], $result);
    }

    public function test_singular_post_with_category_returns_three_crumbs(): void
    {
        $cat          = new stdClass();
        $cat->name    = 'Lịch Sử';
        $cat->term_id = 2;

        Functions\when('home_url')->justReturn('https://example.com/');
        Functions\when('is_singular')->justReturn(true);
        Functions\when('get_the_category')->justReturn([$cat]);
        Functions\when('get_category_link')->justReturn('https://example.com/lich-su/');
        Functions\when('get_the_title')->justReturn('Bài viết test');
        Functions\when('get_permalink')->justReturn('https://example.com/bai-viet/');

        $result = hopham_get_breadcrumbs();
        $this->assertCount(3, $result);
        $this->assertSame('Trang chủ', $result[0]['name']);
        $this->assertSame('Lịch Sử',   $result[1]['name']);
        $this->assertSame('Bài viết test', $result[2]['name']);
    }

    public function test_singular_post_without_category_returns_two_crumbs(): void
    {
        Functions\when('home_url')->justReturn('https://example.com/');
        Functions\when('is_singular')->justReturn(true);
        Functions\when('get_the_category')->justReturn([]);
        Functions\when('get_the_title')->justReturn('Page Title');
        Functions\when('get_permalink')->justReturn('https://example.com/page/');

        $result = hopham_get_breadcrumbs();
        $this->assertCount(2, $result);
        $this->assertSame('Page Title', $result[1]['name']);
    }

    public function test_category_archive_returns_two_crumbs(): void
    {
        Functions\when('home_url')->justReturn('https://example.com/');
        Functions\when('is_singular')->justReturn(false);
        Functions\when('is_category')->justReturn(true);
        Functions\when('single_cat_title')->justReturn('Dòng Họ');
        Functions\when('get_queried_object_id')->justReturn(3);
        Functions\when('get_category_link')->justReturn('https://example.com/dong-ho/');

        $result = hopham_get_breadcrumbs();
        $this->assertCount(2, $result);
        $this->assertSame('Dòng Họ', $result[1]['name']);
    }

    public function test_tag_archive_returns_two_crumbs(): void
    {
        Functions\when('home_url')->justReturn('https://example.com/');
        Functions\when('is_singular')->justReturn(false);
        Functions\when('is_category')->justReturn(false);
        Functions\when('is_tag')->justReturn(true);
        Functions\when('single_tag_title')->justReturn('pham-toc');
        Functions\when('get_queried_object_id')->justReturn(4);
        Functions\when('get_tag_link')->justReturn('https://example.com/tag/pham-toc/');

        $result = hopham_get_breadcrumbs();
        $this->assertCount(2, $result);
        $this->assertSame('pham-toc', $result[1]['name']);
    }

    public function test_search_results_return_two_crumbs(): void
    {
        Functions\when('home_url')->justReturn('https://example.com/');
        Functions\when('is_singular')->justReturn(false);
        Functions\when('is_category')->justReturn(false);
        Functions\when('is_tag')->justReturn(false);
        Functions\when('is_search')->justReturn(true);
        Functions\when('get_search_query')->justReturn('họ Phạm');
        Functions\when('get_search_link')->justReturn('https://example.com/?s=h%E1%BB%8D+Ph%E1%BA%A1m');

        $result = hopham_get_breadcrumbs();
        $this->assertCount(2, $result);
        $this->assertStringContainsString('họ Phạm', $result[1]['name']);
    }

    public function test_breadcrumb_items_have_name_and_url_keys(): void
    {
        Functions\when('home_url')->justReturn('https://example.com/');
        Functions\when('is_singular')->justReturn(false);
        Functions\when('is_category')->justReturn(true);
        Functions\when('single_cat_title')->justReturn('Test');
        Functions\when('get_queried_object_id')->justReturn(1);
        Functions\when('get_category_link')->justReturn('https://example.com/test/');

        $result = hopham_get_breadcrumbs();
        foreach ($result as $crumb) {
            $this->assertArrayHasKey('name', $crumb);
            $this->assertArrayHasKey('url', $crumb);
        }
    }
}
