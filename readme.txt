=== OliForge FAQ ===
Contributors: oliforge
Tags: faq, accordion, schema, shortcode, elementor
Requires at least: 6.5
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 0.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

FAQ sets as a custom post type with a shortcode, a Gutenberg block and an Elementor widget. Adds FAQPage structured data.

== Description ==

OliForge FAQ keeps every FAQ as its own post, so you can reuse one set of questions on any page, post or template.

* **FAQ post type** - create a set under FAQ, then add questions and answers in a sortable list. Basic HTML is allowed in answers.
* **Shortcode** - `[oliforge_faq id="123" open="none|first|all" schema="yes|no"]`.
* **Gutenberg block** - the "OliForge FAQ" block renders on the server, so the editor preview matches the front end. No build step is needed.
* **Elementor widget** - the "OliForge FAQ" widget (category OliForge) with typography and color controls. It appears only when Elementor is active.
* **FAQPage structured data** - one `FAQPage` JSON-LD block is printed per page, as search engines expect. Use `schema="no"` to turn it off.
* **Accessible by default** - answers are native `<details>` / `<summary>` accordions, so they work without JavaScript and with the keyboard.

The FAQ list shows each set's number of questions and a one-click copy button for its shortcode.

== Installation ==

1. Upload the `oliforge-faq` folder to `/wp-content/plugins/`, or install the ZIP from Plugins > Add New.
2. Activate the plugin.
3. Go to FAQ > Add New, add your questions and publish.
4. Place the FAQ with the shortcode shown in the sidebar, the "OliForge FAQ" block, or the Elementor widget.

== Frequently Asked Questions ==

= Can I show the same FAQ in several places? =

Yes. The FAQ is a separate post; use its ID in the shortcode, block or widget wherever you need it.

= Why is only one FAQPage schema printed per page? =

Search engines expect a single FAQPage per URL. If you place several FAQs on one page, the first one outputs the schema. Use `schema="no"` on the others.

= Does it work without Elementor? =

Yes. The Elementor widget is registered only when Elementor is active.

= Where is my data stored? =

Each FAQ is a post of type `oliforge_faq`; questions and answers are stored as post meta. Nothing is sent to external services.

= What happens to my FAQs when I delete the plugin? =

They are kept by default. To remove all FAQs on uninstall, add `define( 'OLIFORGE_FAQ_REMOVE_DATA', true );` to wp-config.php before deleting the plugin.

== Changelog ==

= 0.1.0 =
* First release: FAQ post type, shortcode, Gutenberg block, Elementor widget and FAQPage structured data.

== Upgrade Notice ==

= 0.1.0 =
First release.

== Privacy ==

The plugin does not collect, store or send personal data about visitors.
