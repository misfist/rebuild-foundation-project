<?php 
function rebuild_sites_events_placeholders($replace, $EM_Event, $result){
    global $wp_query, $wp_rewrite;
    switch( $result ){
        case '#_SITE':
            $replace = 'none';
            $sites = get_the_terms($EM_Event->post_id, 'rebuild_sites_category');
            if( is_array( $sites ) && count( $sites ) > 0 ){
                $sites_list = [];
                foreach( $sites as $site ){
                    $link = get_term_link( $site->slug, 'rebuild_sites_category' );
                    if ( is_wp_error( $link ) ) $link = '';
                    $sites_list[] = '<a href="'. $link .'">'. $site->name .'</a>';
                }
                $replace = implode(', ', $sites_list);
            }

            break;
    }
    return $replace;
}
add_filter( 'em_event_output_placeholder','rebuild_sites_events_placeholders',1,3 );

?>