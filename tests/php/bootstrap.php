<?php
/**
 * PHPUnit bootstrap for the Hopham Vietnam theme tests.
 *
 * Sets up Brain\Monkey stubs so that functions.php can be loaded without
 * a live WordPress installation.
 */

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

// Patchwork must be initialised (with patchwork.json already present in the
// project root) before any file that calls native functions we want to stub
// (e.g. setcookie) is included.  Brain\Monkey loads it lazily, so we trigger
// it explicitly here.
require_once __DIR__ . '/../../vendor/brain/monkey/inc/patchwork-loader.php';

// ── Minimal WordPress stubs used by functions.php at load-time ───────────────

if (!defined('ABSPATH')) {
    define('ABSPATH', '/tmp/wp/');
}

// WordPress functions that functions.php calls at the global level
// (i.e. outside any function body, during require/include).
// Most WP functions are mocked per-test via Brain\Monkey; these stubs only
// need to survive the file-include so PHP doesn't fatal.

if (!function_exists('get_template_directory')) {
    function get_template_directory(): string { return '/tmp/theme'; }
}
if (!function_exists('get_template_directory_uri')) {
    function get_template_directory_uri(): string { return 'http://example.com/wp-content/themes/hopham'; }
}
if (!function_exists('get_stylesheet_uri')) {
    function get_stylesheet_uri(): string { return 'http://example.com/wp-content/themes/hopham/style.css'; }
}
if (!function_exists('add_action')) {
    function add_action(string $hook, callable $cb, int $priority = 10, int $args = 1): bool { return true; }
}
if (!function_exists('add_filter')) {
    function add_filter(string $hook, callable $cb, int $priority = 10, int $args = 1): bool { return true; }
}

// ── Load the theme functions ──────────────────────────────────────────────────
require_once __DIR__ . '/../../theme/functions.php';
