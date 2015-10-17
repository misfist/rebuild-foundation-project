<?php


if(! function_exists( 'rebuild_add_rewrite_rules' ) ) {

  function rebuild_add_rewrite_rules() {

   /**
    * The following set of rewrite rules states that if the URL matches this rule,
    * http://rebuild.com/blog/sites/site
    * http://rebuild.com/events/sites/site
    * http://rebuild.com/exhibitions/sites/site
    */

    // Feeds
    // add_rewrite_rule( 
    //     '(events|blog|exhibitions)/sites/([^/]+)/feed/(feed|rdf|rss|rss2|atom)/?$', 
    //     'index.php?post_type=rebuild_$matches[1]&rebuild_site=$matches[2]&feed=$matches[3]', 
    //     'top' );

    // add_rewrite_rule( 
    //     '(events|blog|exhibitions)/sites/([^/]+)/(feed|rdf|rss|rss2|atom)/?$', 
    //     'index.php?post_type=rebuild_$matches[1]&rebuild_site=$matches[2]&feed=$matches[2]', 
    //     'top' ); 

    // // Pagination
    // add_rewrite_rule( 
    //     '(events|blog|exhibitions)/sites/([^/]+)/page/?([0-9]{1,})/?$', 
    //     'index.php?post_type=rebuild_$matches[1]&rebuild_site=$matches[2]&paged=$matches[3]', 
    //     'top' );

    // Base rewrite 
     //rebuild.site/{{post_type}}/{{site_slug}}/
    // add_rewrite_rule( 
    //     'events/?$', 
    //     'index.php?post_type=rebuild_event', 
    //     'top' ); 
    // add_rewrite_rule( 
    //     'exhibitions/?$', 
    //     'index.php?post_type=rebuild_exhibition', 
    //     'top' ); 
    // add_rewrite_rule( 
    //     'locations/?$', 
    //     'index.php?post_type=rebuild_location', 
    //     'top' );
    // add_rewrite_rule( 
    //     'sites/?$', 
    //     'index.php?taxonomy=rebuild_site_category', 
    //     'top' ); 

    // Post type by site and category
    //rebuild.site/{{post_type}}/sites/{{site-slug}}/category/{{category-slug}}/
    // add_rewrite_rule( 
    //     '(events|blog|exhibitions)/sites/([^/]+)/category/([^/]+)/?$', 
    //     'index.php?post_type=rebuild_$matches[1]&rebuild_site=$matches[2]&rebuild_$matches[1]_category=$matches[3]', 
    //     'top' ); 

    // // Post type by site
    // add_rewrite_rule( '(events|blog|exhibitions)/sites/([^/]+)/?$', 
    //     'index.php?post_type=rebuild_$matches[1]&rebuild_site=$matches[2]', 
    //     'top' );

  }

  add_action( 'init', 'rebuild_add_rewrite_rules' );

}



?>