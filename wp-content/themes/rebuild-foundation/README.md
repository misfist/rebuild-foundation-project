Rebuild Foundation Theme

Contributors: misfist
Tags: translation-ready, custom-background, theme-options, custom-menu, post-formats

Requires at least: 4.0
Tested up to: 4.4
Stable tag: 1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

===

Customized starter theme built using `underscores`.

Template Structure
---------------

Custom post type (site, event, exhibition, staff) listing pages are rendered by archive.php, which calls a partial template (loop-{post_type}) to render each list item. These custom post types and associated functions are defined in the rebuild-foundation-cpt plugin, which must be activated in order to be rendered (see Core Dependencies).

Single custom post type (site, event, exhibition) pages are rendered by single.php, which calls a partial template (content-{post_type}) to render the content area.

The home page template (page-home.php) renders each custom field associated with the front page. If additional custom fields are added and contain content, they will be rendered. `page-home-static.php` is a static version that can be selected from the home page template list (in the dashboard), if desired.


Functions
---------------

functions.php has not been modified except to add calls to files in /inc. 

`/inc/blog-functions.php` contains the blog query function `function rebuild_posts_query( $site_cat = null, $limit = null )`

`/inc/event-functions.php` contains functions related to the display of the event post type, such as event js script enqueue, building the date query vars based on conditions, pre-get query, cached date queries and various helper functions.

`/inc/extras.php` contains functions that call WP actions and filters to modify content display.

`/inc/filters.php` contains various filters that are displayed on archive pages to filter post items. 

`/inc/helpers.php` contains functions that helper functions that are generally only used by other functions.

`/inc/template-tags.php` contains tags that can be used to display content such as taxonomy links, custom field filters, etc. In general, it contains only functions that render content ( echo or printf );


Core Dependencies
---------------
* Custom plugin for Sites (rebuild-foundation-cpt) [https://github.com/misfist/rebuild-foundation-project](https://github.com/misfist/rebuild-foundation-project)
* Custom plugin for other custom functions (rebuild-foundation-custom) [https://github.com/misfist/rebuild-foundation-project](https://github.com/misfist/rebuild-foundation-project)
* Advanced Custom Fields [https://github.com/elliotcondon/acf](https://github.com/elliotcondon/acf)

File Structure
---------------

```
rebuild-foundation
    - 404.php
    - archive.php
    - assets
        - css
        - img
        - js
    - comments.php
    - fonts
    - footer.php
    - functions.php
    - header.php
    - img
    - inc
        - blog-functions.php
        - custom-header.php
        - customizer.php
        - event-functions.php
        - exhibition-functions.php
        - extras.php
        - filters.php
        - helpers.php
        - jetpack.php
        - template-tags.php
    - index.php
    - languages
    - package.json
    - page-home-template.php
    - page-home.php
    - page.php
    - README.md
    - rtl.css
    - screenshot.png
    - search.php
    - sidebar.php
    - single.php
    - src
        - img
        - js
        - sass
    - style.css
    - style.css.map
    - template-parts
        - content-event.php
        - content-exhibition.php
        - content-home.php
        - content-none.php
        - content-page.php
        - content-post.php
        - content-search.php
        - content-single.php
        - content-site.php
        - content-staff.php
        - content.php
        - gallery-site.php
        - gallery-static.php
        - gallery.php
        - loop-event.php
        - loop-exhibition.php
        - loop-none.php
        - loop-page.php
        - loop-post.php
        - loop-site-event-future.php
        - loop-site-event-past.php
        - loop-site-post.php
        - loop-site.php
        - loop-staff.php
        - social-media.php
```

Changelog
---------------
= 1.0.1 - May 11 2016 =
* Added Residencies section

= 1.0 - December 17 2015 =
* Initial release