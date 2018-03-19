=== Test Plugin ===
Contributors: s-apoorva
Tags: wordpressp, category, post, banner, image, hyperlink, cutom banner, custom banner set 
Donate link: http://www.ranosys.com/
Requires at least: 4.0
Tested up to: 4.8
Requires PHP: 5.6
Stable tag: 0.1
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Place a custom banner and link at the top of your category and post pages. Easily update the image through your category edit page.

== Description ==
Place a custom banner and link at the top of your category and post pages. Easily update the image through your category edit page.

Features:

* No configuration needed, just install and start using
* Supports an image for each category
* Custom post category positions with a template tag, shortcode for including the image in a custom area in your custom category template and post template.
* Support a link for each category / banner image

Getting Started:

1. From the sidebar click 'Posts' -> 'Categories'
2. Select an individual category from your list
3. Paste the url or upload the image of the banner you'd like to use into the "Banner Image Url" field
4. That's it, your banner should now be displaying on your category page.
5. Remove image by deleting the url from the edit category page.

You can hide the automatic image placement by unchecking "Automatically insert banner above main content of category page" while on posts to display its necessary to check the second checkbox.

You can then place the template tag wp_show_category_banner() in your custom category template to customize the position of the image in your markup. Note - this tag must be placed on category templates - not posts templates.

For posts template copy and paste shortcode do_shortcode( '[category_post_banner]' ) at desired location.

[Plugin's Official Documentation and Support Page](https://github.com/s-apoorva/WP-Category-Banner-Plugin)

== Installation ==
Automatic WordPress Installation

1. Log-in to your WordPress Site
2. Under the plugin sidebar tab, click ‘Add New’
3. Search for ‘Wordpress Category Banner'
4. Install and Activate the Plugin
5. Start uploading a banner image from 'Posts' -> 'Categories' -> (Select category)

Manual Installation

1. Download the latest version of the plugin from WordPress page
2. Uncompress the file
3. Upload the uncompressed directory to ‘/wp-content/plugins/’ via FTP
4. Active the plugin from your WordPress backend ‘Plugins -> Installed Plugins’
5. Start uploading a banner image from 'Posts' -> 'Categories' -> (Select category)

== Frequently Asked Questions ==
= A question that someone might have =
An answer to that question.

= What about foo bar? =
Answer to foo bar dilemma.

== Screenshots ==
1. The screenshot description corresponds to screenshot-1.(png|jpg|jpeg|gif).
2. The screenshot description corresponds to screenshot-2.(png|jpg|jpeg|gif).
3. The screenshot description corresponds to screenshot-3.(png|jpg|jpeg|gif).

== Changelog ==

= 0.1 =
This version fixes a security related bug. Upgrade immediately.
