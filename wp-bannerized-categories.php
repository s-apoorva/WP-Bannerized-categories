<?php
/*
  Plugin Name:          WP Bnnerized Categories
  Plugin URI:           http://www.ranosys.com/
  Description:          Place a custom banner and link at the top of your category and post pages. Easily update the image through your category edit page.
  Version:              0.1
  Author:               Apoorva Sharma (Ranosys Technologies Pte. Ltd.)
  Author URI:           https://github.com/s-apoorva
  License:              GPLv3
  License URI:          http://www.gnu.org/licenses/gpl-3.0.html
  Domain Path:          /languages
  GitHub Plugin URI:    https://github.com/s-apoorva/WP-Category-Banner-Plugin
 */

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

if (!class_exists('WP_Category_Banner'))
{

    class WP_Category_Banner
    {

        public function __construct()
        {
            // Add Scripts and styles
            add_action('admin_enqueue_scripts', array($this, 'admin_scripts_and_styles'));

            // Add the fields to the product cateogry taxonomy
            add_action('category_edit_form_fields', array($this, 'wp_cat_taxonomy_custom_fields'), 10, 2);

            // Save the changes made on the product category taxonomy
            add_action('edited_category', array($this, 'wp_cat_save_taxonomy_custom_fields'), 10, 2);

            // Add a banner image based on category taxonomy image, only if it's set to auto show (default)
            add_action('get_the_archive_title', array($this, 'wp_show_category_banner'), 30);
            
            // Add same banner as category banner on each post under that category
            add_shortcode('category_post_banner', array($this, 'wp_show_category_post_banner'));
        }

        /*
         * 	Adds necessary admin scripts
         */

        public function admin_scripts_and_styles()
        {

            // Get current screen attributes
            $screen = get_current_screen();
            if ($screen != null and $screen->id == "edit-category")
            {

                // Adds WP Modal Window References
                wp_enqueue_media();

                // Enque the script
                wp_enqueue_script('wp_admin_script', plugin_dir_url(__FILE__) . 'assets/js/wp-admin.js', array('jquery'), '1.0.0', true
                );

                // Add Style
                wp_enqueue_style(
                        'wp_admin_styles', plugins_url('/assets/css/wp-admin.css', __FILE__)
                );
            }
        }

        /*
         * 	Adds default option values
         */

        public function wp_cat_taxonomy_custom_fields($tag)
        {
            // Check for existing taxonomy meta for the term you're editing
            $t_id = $tag->term_id; // Get the ID of the term you're editing
            $term_meta = get_option("taxonomy_term_$t_id"); // Do the check
            // Get banner image
            if (isset($term_meta['banner_url_id']) and $term_meta['banner_url_id'] != '')
                $banner_id = $term_meta['banner_url_id'];
            else
                $banner_id = null;

            // Get banner link
            if (isset($term_meta['banner_link']) and $term_meta['banner_link'] != '')
                $banner_link = $term_meta['banner_link'];
            else
                $banner_link = null;

            // Get auto display setting.
            if ((isset($term_meta['auto_display_banner']) && $term_meta['auto_display_banner'] == 'on') || !isset($term_meta['auto_display_banner']))
            {
                $auto_display_banner = true;
            } else
            {
                $auto_display_banner = false;
            }

            if ((isset($term_meta['auto_display_post_banner']) && $term_meta['auto_display_post_banner'] == 'on') || !isset($term_meta['auto_display_post_banner']))
            {
                $auto_display_post_banner = true;
            } else
            {
                $auto_display_post_banner = false;
            }
            ?>

            <tr class="form-field banner_url_form_field">
                <th scope="row" valign="top">
                    <label for="banner_url"><?php _e('Banner Image'); ?></label>
                </th>
                <td>
                    <fieldset>
                        <a class='wp_upload_file_button button' uploader_title='Select File' uploader_button_text='Include File'>Upload File</a>
                        <a class='wp_remove_file button'>Remove File</a>
                        <label class="banner_url_label" ><?php if ($banner_id != null) echo basename(wp_get_attachment_url($banner_id)) ?></label>
                    </fieldset>

                    <fieldset>
                        <img class="cat_banner_img_admin" src="<?php if ($banner_id != null) echo wp_get_attachment_url($banner_id) ?>" />
                    </fieldset>

                    <input type="hidden" class='wp_image' name='term_meta[banner_url_id]' value='<?php if ($banner_id != null) echo $banner_id; ?>' />
                </td>
            </tr>

            <tr class="form-field banner_link_form_field">
                <th scope="row" valign="top">
                    <label for="banner_link"><?php _e('Banner Image Link'); ?></label>
                </th>
                <td>
                    <fieldset>
                        <input type="url" name='term_meta[banner_link]' value='<?php if ($banner_link != null) echo $banner_link ?>' />
                        <label class="banner_link_label" for="banner_link"><em>Where users will be directed if they click the banner.</em></label>
                    </fieldset>
                </td>
            </tr>
            <tr class="form-field auto_display_banner">
                <th scope="row" valign="top">
                    <label for="auto_display_banner"><?php _e('Automatically insert banner above main content'); ?></label>
                </th>
                <td>
                    <fieldset>
                        <input name="term_meta[auto_display_banner]" type="checkbox" value="on" class="auto_display_banner" <?php if ($auto_display_banner) echo " checked "; ?>/>
                        <label class="auto_display_banner_label" for="auto_display_banner"><em>If you want to display the banner in a custom spot on your category page, you can deselect this checkbox and use the wp_show_category_banner() in your category template to dictate where it will appear.</em></label>
                    </fieldset>
                </td>
            </tr>
            <tr class="form-field auto_display_post_banner">
                <th scope="row" valign="top">
                    <label for="auto_display_post_banner"><?php _e('Insert banner above main content of post'); ?></label>
                </th>
                <td>
                    <fieldset>
                        <input name="term_meta[auto_display_post_banner]" type="checkbox" value="on" class="auto_display_post_banner" <?php if ($auto_display_post_banner) echo " checked "; ?>/>
                        <label class="auto_display_post_banner_label" for="auto_display_post_banner"><em>If you want to display the banner on your post, you needs the shortcode do_shortcode( '[category_post_banner]' ) in your single.php file of your theme to dictate where it will appear.</em></label>
                    </fieldset>
                </td>
            </tr>
            <?php
        }

        // A callback function to save our extra taxonomy field(s)
        public function wp_cat_save_taxonomy_custom_fields($term_id)
        {
            if (isset($_POST['term_meta']))
            {
                $t_id = $term_id;
                $term_meta = get_option("taxonomy_term_$t_id");
                $posted_term_meta = $_POST['term_meta'];
                if (!isset($posted_term_meta['auto_display_banner']))
                    $posted_term_meta['auto_display_banner'] = 'off';

                if (!isset($posted_term_meta['auto_display_post_banner']))
                    $posted_term_meta['auto_display_post_banner'] = 'off';
                
                $cat_keys = array_keys($posted_term_meta);

                foreach ($cat_keys as $key)
                {
                    if (isset($posted_term_meta[$key]))
                    {
                        $term_meta[$key] = $posted_term_meta[$key];
                    }
                }
                //save the option array
                update_option("taxonomy_term_$t_id", $term_meta);
            }
        }

        // Retreives and print the category banner
        public function wp_show_category_banner()
        {
            global $wp_query;
            // Make sure this is a product category page
            if (is_category())
            {
                $cat_id = $wp_query->queried_object->term_id;
                $term_options = get_option("taxonomy_term_$cat_id");

                if ((isset($term_options['auto_display_banner']) && $term_options['auto_display_banner'] == 'on') || !isset($term_options['auto_display_banner']))
                {
                    // Get the banner image id
                    if ($term_options['banner_url_id'] != '')
                        $url = wp_get_attachment_url($term_options['banner_url_id']);

                    // Exit if the image url doesn't exist
                    if (!isset($url) or $url == false)
                        return;

                    // Get the banner link if it exists
                    if ($term_options['banner_link'] != '')
                        $link = $term_options['banner_link'];

                    // Print Output
                    if (isset($link))
                        echo "<a href='" . $link . "'>";

                    if ($url != false)
                        echo "<img src='" . $url . "' class='category_banner_image' />";

                    if (isset($link))
                        echo "</a>";
                    ?>

                    <h2 class='page-title'><?php echo single_cat_title(); ?></h2>
                    <?php
                }
            }
        }

        // Retreives and print the category banner
        public function wp_show_category_post_banner()
        {
            global $wp_query;
            // Make sure this is a product category page
            if (is_single())
            {
                $cat_id = get_the_category()[0]->cat_ID;

                $term_options = get_option("taxonomy_term_$cat_id");

                if ((isset($term_options['auto_display_post_banner']) && $term_options['auto_display_post_banner'] == 'on') || !isset($term_options['auto_display_post_banner']))
                {
                    // Get the banner image id
                    if ($term_options['banner_url_id'] != '')
                        $url = wp_get_attachment_url($term_options['banner_url_id']);

                    // Exit if the image url doesn't exist
                    if (!isset($url) or $url == false)
                        return;

                    // Get the banner link if it exists
                    if ($term_options['banner_link'] != '')
                        $link = $term_options['banner_link'];

                    // Print Output
                    if (isset($link))
                        echo "<a href='" . $link . "'>";

                    if ($url != false)
                        echo "<img src='" . $url . "' class='category_banner_image' />";

                    if (isset($link))
                        echo "</a>";
                }
            }
        }

    }

}

$WP_Category_Banner = new WP_Category_Banner();

//Shortcode function for displaying banner.
function wp_show_category_banner()
{
    global $WP_Category_Banner;
    $WP_Category_Banner->wp_show_category_banner(); //disable the only show for category tag.
}
