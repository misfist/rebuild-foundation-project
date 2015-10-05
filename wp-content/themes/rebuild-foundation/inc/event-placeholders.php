<?php 

/**
 * Create a placeholder tag for site that displays site name and link.
 */
function rebuild_sites_site_link_placeholder( $replace, $EM_Event, $result ){
    global $wp_query, $wp_rewrite;
    switch( $result ){
        case '#_SITE':
            $replace = 'none';
            $site_term = wp_get_post_terms( $EM_Event->post_id, 'rebuild_sites_category', array( 'fields' => 'slugs' ) );

            if( $site_term ) {

                $sites_args = array (
                    'post_type' => 'rebuild_sites',
                    'posts_per_page' => 1,
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'rebuild_sites_category',
                            'field'    => 'slug',
                            'terms'    => $site_term[0]
                        ),
                    ),
                );

            }

            $sites = get_posts( $sites_args );

            // If there is a short name, use that, otherwise use full name
            $site_name = get_post_meta( $sites[0]->ID, '_rebuild_site_short_name', true ) ? get_post_meta( $sites[0]->ID, '_rebuild_site_short_name', true ) : $sites[0]->post_title;

            $replace = '<a href="' . post_permalink( $sites[0]->ID ) . '">' . $site_name . '</a>';

            break;
    }
    return $replace;
}
add_filter( 'em_event_output_placeholder','rebuild_sites_site_link_placeholder',1,3 );


/**
 * Create a placeholder tag for site that displays site name only.
 */
function rebuild_sites_site_name_placeholder( $replace, $EM_Event, $result ){
    global $wp_query, $wp_rewrite;
    switch( $result ){
        case '#_SITENAME':
            $replace = 'none';
            $site_term = wp_get_post_terms( $EM_Event->post_id, 'rebuild_sites_category', array( 'fields' => 'slugs' ) );

            if( $site_term ) {

                $sites_args = array (
                    'post_type' => 'rebuild_sites',
                    'posts_per_page' => 1,
                    'tax_query' => array(
                        array(
                            'taxonomy' => 'rebuild_sites_category',
                            'field'    => 'slug',
                            'terms'    => $site_term[0]
                        ),
                    ),
                );

            }

            $sites = get_posts( $sites_args );

            $replace = $sites[0]->post_title;

            break;
    }
    return $replace;
}
add_filter( 'em_event_output_placeholder','rebuild_sites_site_name_placeholder',1,3 );

?>