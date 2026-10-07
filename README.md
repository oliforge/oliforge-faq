# OliForge FAQ

FAQ sets as a custom post type. Each FAQ post holds questions and answers; place it with a shortcode, a Gutenberg block or an Elementor widget. Outputs `FAQPage` structured data.

**Version:** 0.1.0 · **Requires:** WordPress 6.5+, PHP 7.4+

## Usage

1. Create a set under **FAQ → Add New** and add questions and answers (drag to reorder, basic HTML allowed in answers).
2. Show it:
   - Shortcode: `[oliforge_faq id="123" open="none|first|all" schema="yes|no"]`
   - Gutenberg: the **OliForge FAQ** block (pick the FAQ in the sidebar).
   - Elementor: the **OliForge FAQ** widget (category *OliForge*).

Only one `FAQPage` JSON-LD block is printed per page, as Google expects. Use `schema="no"` to disable it.

## Admin

The FAQ list and editor use the shared OliForge admin look (header, orange buttons, branded table) and show a copy-able shortcode per FAQ.

## Files

- `oliforge-faq.php` bootstrap
- `includes/` plugin class and Elementor widget
- `blocks/faq/` block.json and editor script (no build step)
- `assets/` front-end, admin and copy-button assets
