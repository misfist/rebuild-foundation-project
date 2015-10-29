Rebuild Foundation Theme
===

Customized starter theme built using `underscores`.

Template Structure
---------------

Custom post type (site, event, exhibition) listing pages are rendered by archive.php, which calls a partial template (loop-{post_type}) to render each list item.

Single custom post type (site, event, exhibition) pages are rendered by single.php, which calls a partial template (content-{post_type}) to render the content area.

The home page template (page-home.php) renders each custom field associated with the front page. If additional custom fields are added and contain content, they will be rendered.


Functions
---------------

functions.php has not been modified except to add calls to files in /inc. 

/inc/extras.php contains functions that call WP actions and filters to modify content display.

/inc/filters.php contains various filters that are displayed on archive pages to filter post items. 

/inc/helpers.php contains functions that helper functions that are generally only used by other functions.

/inc/template-tags.php contains tags that can be used to display content such as taxonomy links, custom field filters, etc. In general, it contains only functions that render content ( echo or printf );


Core Dependencies
---------------
* Custom plugin for Sites (rebuild-foundation-cpt) [https://github.com/misfist/rebuild-foundation-project](https://github.com/misfist/rebuild-foundation-project)
* Custom plugin for other custom functions (rebuild-foundation-custom) [https://github.com/misfist/rebuild-foundation-project](https://github.com/misfist/rebuild-foundation-project)
* Advanced Custom Fields [https://github.com/elliotcondon/acf](https://github.com/elliotcondon/acf)

File Structure
---------------

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
        - custom-header.php
        - customizer.php
        - event-functions.php
        - extras.php
        - filters.php
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
        - content.php
        - loop-event.php
        - loop-exhibition.php
        - loop-page.php
        - loop-post.php
        - loop-site-event-future.php
        - loop-site-event-past.php
        - loop-site-post.php
        - loop-site.php
