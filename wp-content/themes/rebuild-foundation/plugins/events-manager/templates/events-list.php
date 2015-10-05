<?php
/*
 * Default Events List Template
 * This page displays a list of events, called during the em_content() if this is an events list page.
 * You can override the default display settings pages by copying this file to yourthemefolder/plugins/events-manager/templates/ and modifying it however you need.
 * You can display events however you wish, there are a few variables made available to you:
 * 
 * $args - the args passed onto EM_Events::output()
 * 
 */
$args = apply_filters('em_content_events_args', $args);

// event category = events

$args['format_header'] = '';


$args['format'] = '
<article id="event-#_EVENTID" class="post event">
    <div class="site-name">#_SITE</div>
    <div class="event-title">#_EVENTLINK</div>
    <div class="event-date" style="background-image:url(#_EVENTIMAGEURL)"><time datetime="2011-01-12">#F #j</time></div>
</article>
';

// event category = exhibitions

if( get_option('dbem_css_evlist') ) echo "<div class='css-events-list'>";


echo EM_Events::output( $args );

if( get_option('dbem_css_evlist') ) echo "</div>";
