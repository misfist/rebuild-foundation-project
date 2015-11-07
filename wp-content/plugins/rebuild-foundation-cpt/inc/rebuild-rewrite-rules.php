<?php


if(! function_exists( 'rebuild_add_rewrite_rules' ) ) {

    function rebuild_add_rewrite_rules() {

        //--- Exhibitions
        // http://rebuild.com/exhibitions/scope/{cat}/site/{site-cat}
        // e.g. http://rebuild.site/exhibitions/scope/current/site/stony-island-arts-bank/
        add_rewrite_rule('^exhibitions/scope/([^/]*)/site/([^/]*)/?','index.php?exhibition_category=$matches[1]&site_category=$matches[2]','top');

        // http://rebuild.com/exhibitions/site/{site-cat}/scope/{cat}
        // e.g. http://rebuild.site/exhibitions/site/stony-island-arts-bank/scope/current/
        add_rewrite_rule('^exhibitions/site/([^/]*)/scope/([^/]*)/?','index.php?exhibition_category=$matches[2]&site_category=$matches[1]','top');

        // http://rebuild.com/exhibitions/scope/{cat}
        // e.g. http://rebuild.site/exhibitions/scope/current/
        add_rewrite_rule('^exhibitions/scope/([^/]*)/?','index.php?exhibition_category=$matches[1]','top');

        // http://rebuild.com/exhibitions/site/{site-cat}/scope/{cat}
        // e.g. http://rebuild.site/exhibitions/site/stony-island-arts-bank/scope/current/
        add_rewrite_rule('^exhibitions/site/([^/]*)/?','index.php?site_category=$matches[1]&post_type=exhibition','top');

        //--- Events
        // e.g. http://rebuild.com/events/type/{cat}/site/{site}
        add_rewrite_rule('^events/type/([^/]*)/site/([^/]*)/?','index.php?event_category=$matches[1]&site_category=$matches[2]','top');

        // e.g. http://rebuild.com/events/site/{site}/type/{cat}
        add_rewrite_rule('^events/site/([^/]*)/type/([^/]*)/?','index.php?event_category=$matches[2]&site_category=$matches[1]','top');

        // e.g. http://rebuild.com/events/tag/{cat}/site/{site}
        add_rewrite_rule('^events/tag/([^/]*)/site/([^/]*)/?','index.php?event_tag=$matches[1]&site_category=$matches[2]','top');

        // e.g. http://rebuild.com/events/site/{site}/tag/{cat}
        add_rewrite_rule('^events/site/([^/]*)/tag/([^/]*)/?','index.php?event_tag=$matches[2]&site_category=$matches[1]','top');

        // e.g. http://rebuild.com/events/site/{site}
        add_rewrite_rule('^events/site/([^/]*)/?','index.php?post_type=event&site_category=$matches[1]','top');

        // e.g. http://rebuild.com/events/type/{cat}
        add_rewrite_rule('^events/type/([^/]*)/?','index.php?event_category=$matches[1]','top');

        // e.g. http://rebuild.com/events/tag/{tag}
        add_rewrite_rule('^events/tag/([^/]*)/?','index.php?event_tag=$matches[1]','top');

        //--- Posts

        // e.g. http://rebuild.com/blog/site/{site}/tag/{tag}
        add_rewrite_rule('^blog/site/([^/]*)/tag/([^/]*)/?','index.php?post_type=post&site_category=$matches[1]&post_tag=$matches[2]','top');

        // e.g. http://rebuild.com/blog/site/{site}/category/{cat}
        add_rewrite_rule('^blog/site/([^/]*)/category/([^/]*)/?','index.php?post_type=post&site_category=$matches[1]&category=$matches[2]','top');

        // e.g. http://rebuild.com/blog/tag/{tag}/site/{site}
        add_rewrite_rule('^blog/tag/([^/]*)/site/([^/]*)/?','index.php?post_type=post&site_category=$matches[2]&post_tag=$matches[1]','top');

        // e.g. http://rebuild.com/blog/category/{cat}/site/{site}
        add_rewrite_rule('^blog/category/([^/]*)/site/([^/]*)/?','index.php?post_type=post&site_category=$matches[2]&category=$matches[1]','top');

        // e.g. http://rebuild.com/blog/site/{site}
        add_rewrite_rule('^blog/site/([^/]*)/?','index.php?post_type=post&site_category=$matches[1]','top');

        // e.g. http://rebuild.com/blog/tag/{tag}
        add_rewrite_rule('^blog/tag/([^/]*)/?','index.php?post_type=post&post_tag=$matches[1]','top');

        // e.g. http://rebuild.com/blog/category/{tag}
        add_rewrite_rule('^blog/category/([^/]*)/?','index.php?post_type=post&category=$matches[1]','top');


    }

    add_action( 'init', 'rebuild_add_rewrite_rules' );

}



?>