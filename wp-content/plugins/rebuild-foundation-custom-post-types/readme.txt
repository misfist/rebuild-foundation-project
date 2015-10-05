=== Rebuild Foundation Custom Post Types ===
Contributors: Pea
Tags: plugin, custom post type
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.en.html

== Description ==

This plugin registers custom post types. It also registers taxonomies and custom field metaboxes.  If featured images are selected, they will be displayed in the column view.

This plugin doesn't change how custom post items are displayed in your theme.  You'll need to add templates for archive-{post_type}.php and single-{post_type}.php if you want to customize the display of portfolio items.

= Post Types =
Sites: `rebuild_sites` 

Custom Taxonomies: `rebuild_sites_category`

Custom Fields
* show_blog_posts (checkbox)
* category (`rebuild_sites_category` taxonomy select)
* gallery
* location
* hours

Usage: 
Fields can be accessed using the function rebuild_site_fields()
e.g. `$site_fields = rebuild_site_fields()`
e.g. `echo "$site_fields['street']"`

Returns:
array(10) {
  ["id"]=>int(267)
  ["street"]=>string(25) ""
  ["street2"]=>string(0) ""
  ["city"]=>string(7) ""
  ["state"]=>string(2) ""
  ["zip"]=>string(0) ""
  ["hours"]=>string(39) ""
  ["gallery"]=>
  array(1) {
    [290]=>string(61) "http://rebuild.site/wp-content/uploads/2015/10/volunteer.jpeg"
  }
  ["category"]=>string(22) ""
  ["show_blog_posts"]=>string(2) "on"
}


Exhibitions: `rebuild_exhibitions`

Custom Taxonomies: n/a (uses build-in taxonomy and `rebuild_sites_category`)

Custom Fields
* site_category
* gallery
* location
* hours
* date_range

== Installation ==

Install and activate.
Two new content types will appear in the left: Sites and Exhibitions