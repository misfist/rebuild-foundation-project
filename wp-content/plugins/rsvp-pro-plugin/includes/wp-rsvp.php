<?php
  $rsvp_options = "";
  $rsvp_sub_options = array();
  $event_information = array();
  
	if((isset($_GET['page']) && (strToLower($_GET['page']) == 'rsvp-admin-export')) || 
		 (isset($_POST['rsvp-bulk-action']) && (strToLower($_POST['rsvp-bulk-action']) == "export")) || 
     (isset($_GET['page']) && (strToLower($_GET['page']) == "rsvp-pro-top-level") && (strToLower($_GET['action']) == "export"))) {
		add_action('init', 'rsvp_pro_admin_export');
	}
  
  // retrieve our license key from the DB
  $license_key = trim( get_option( 'rsvp_pro_license_key' ) ); 

  // setup the updater
  $rsvp_updater = new RSVP_PRO_SL_Plugin_Updater( RSVP_PRO_STORE_URL, RSVP_PRO_PLUGIN_FILE, array(
  	'version' 	=> '2.1.8',		// current version number
  	'license' 	=> $license_key,	// license key (used get_option above to retrieve from DB)
  	'item_name' => RSVP_PRO_ITEM_NAME,	// name of this plugin
  	'author' 	=> 'Swim or Die Software',	// author of this plugin
  	'url'     => home_url()
  ) );
  
  function rsvp_pro_admin_events() {
    global $wpdb;

    if(wp_next_scheduled("rsvp_pro_reoccurring_events") === false) {
      rsvp_pro_scheduler();
    }
    
    $action = "";
    if(isset($_REQUEST['action']) && !empty($_REQUEST['action'])) {
      $action = $_REQUEST['action'];
    }
    
    if(rsvp_pro_check_license() != "valid") {
      ?>
        <div class="error">
          <p><?php _e("License is invalid, please activate your license in Plugins -> RSVP Pro Plugin License", "rsvp-pro-plugin"); ?></p>
        </div>
      <?php
    }
    
    $table =  $wpdb->prefix."rsvpEvents";
    if($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) {
      update_option( "rsvp_pro_db_version", 0);
      rsvp_pro_database_setup();
    }
    
    if(!isset($_GET['eventID']) || !is_numeric($_GET['eventID']) || ($_GET['eventID'] <= 0)) {
      $action = "";
    }
    
    if($_POST['rsvp-bulk-action'] == "mass_email") {
      $action = "mass_email";
    }
    
    if($action == "delete") {
      rsvp_pro_admin_delete_event($_GET['eventID']);
    } else if($action == "attendees") {
      rsvp_pro_admin_guestlist($_GET['eventID']);
    } else if(($action == "modify_attendee") && isset($_GET['eventID']) && ($_GET['eventID'] > 0)) {
      rsvp_pro_admin_guest($_GET['eventID']);
    } else if($action == "import") {
      rsvp_pro_admin_import($_GET['eventID']);
    } else if($action == "custom_questions") {
      rsvp_pro_admin_questions($_GET['eventID']);
    } else if($action == "modify_custom_question") {
      rsvp_pro_admin_custom_question($_GET['eventID']);
    } else if($action == "import_from_free") {
      rsvp_pro_admin_import_from_free($_GET['eventID']);
    } else if($action == "mass_email") {
      rsvp_pro_admin_mass_email($_GET['eventID']);
    } else if($action == "copy") {
      rsvp_pro_admin_copy_event($_GET['eventID']);
    } else if($action == "all_attendees") {
      rsvp_pro_admin_guestlist($_GET['eventID'], true);
    } else {
      // Display events
      rsvp_pro_admin_eventList();
    }
  }

  function rsvp_pro_set_passcode($eventID) {
    global $wpdb;
    
		rsvp_pro_install_passcode_field();
    $length = 6;
    
    if(rsvp_pro_get_event_option($eventID, RSVP_PRO_OPTION_PASSWORD_LENGTH) > 0) {
      $length = rsvp_pro_get_event_option($eventID, RSVP_PRO_OPTION_PASSWORD_LENGTH);
    }
			
		$sql = "SELECT id, passcode FROM ".PRO_ATTENDEES_TABLE." WHERE passcode = '' AND rsvpEventID = $eventID";
		$attendees = $wpdb->get_results($sql);
		foreach($attendees as $a) {
			$wpdb->update(PRO_ATTENDEES_TABLE, 
										array("passcode" => rsvp_pro_generate_passcode($length)), 
										array("id" => $a->id), 
										array("%s"), 
										array("%d"));
		}
  }
  
  function rsvp_pro_admin_delete_event($eventID) {
    global $wpdb;
    
    $sql = "SELECT eventName FROM ".PRO_EVENT_TABLE." WHERE id = %d";
    $events = $wpdb->get_results($wpdb->prepare($sql, $eventID));
    if(count($events) <= 0) {
      rsvp_pro_admin_eventList();
      return;
    }

    if(!rsvp_pro_admin_user_has_access_to_settings($eventID)) {
      rsvp_pro_admin_eventList();
      return;
    }
    
		if((count($_POST) > 0) && (wp_verify_nonce($_POST['_wpnonce'], 'rsvp_delete_event') !== false)) {
			check_admin_referer('rsvp_delete_event');  
      rsvp_pro_delete_event($eventID);
      ?>
        <div id="message" class="updated"><p class="updated"><?php _e("Event deleted", "rsvp-pro-plugin"); ?></p></div>
      <?php
      rsvp_pro_admin_eventList();
    } else { 
    
    ?>
      <div class="wrap">
  			<form name="deleteEvent" method="post">
  				<?php wp_nonce_field('rsvp_delete_event'); ?>
          <p><?php _e("Are you sure you want to delete ", "rsvp-pro-plugin"); ?> <?php echo $events[0]->eventName; ?>?</p>
  				<p class="submit">
  					<input type="submit" class="button-primary" value="<?php _e('Delete'); ?>" />
  				</p>
        </form>
      </div>
    <?php
    } // if(count($_POST) > 0) {
  }

  function rsvp_pro_admin_manage_event() {
    global $wpdb;
    if(isset($_GET['id']) && !rsvp_pro_admin_user_has_access_to_settings($_GET['id'])) {
      rsvp_pro_admin_eventList();
      return;
    }
    // TODO: Rewrite this
    require_once("admin/manage_event_form.inc.php");
  }
  
  function rsvp_pro_delete_event($eventID) {
    global $wpdb;
    
    if(is_numeric($eventID) && ($eventID > 0)) {
      // delete custom answers
      $wpdb->query($wpdb->prepare("DELETE ca.* FROM ".PRO_QUESTION_ANSWERS_TABLE." ca  
                    INNER JOIN ".PRO_QUESTIONS_TABLE." cq ON cq.id = ca.questionID 
                    WHERE cq.rsvpEventID = %d", $eventID));
    
      $wpdb->query($wpdb->prepare("DELETE ca.* FROM ".PRO_QUESTION_ATTENDEES_TABLE." ca  
                    INNER JOIN ".PRO_QUESTIONS_TABLE." cq ON cq.id = ca.questionID 
                    WHERE cq.rsvpEventID = %d", $eventID));
    
      // delete custom questions 
      $wpdb->query($wpdb->prepare("DELETE FROM ".PRO_QUESTIONS_TABLE." WHERE rsvpEventID = %d", $eventID));
      
      // delete attendees
      $wpdb->query($wpdb->prepare("DELETE aa.* FROM ".PRO_ASSOCIATED_ATTENDEES_TABLE." aa 
                                   INNER JOIN ".PRO_ATTENDEES_TABLE." a ON a.id = aa.attendeeID 
                                   WHERE a.rsvpEventID = %d", $eventID));
                                   
      $wpdb->query($wpdb->prepare("DELETE aa.* FROM ".PRO_ATTENDEE_ANSWERS." aa 
                                   INNER JOIN ".PRO_ATTENDEES_TABLE." a ON a.id = aa.attendeeID 
                                   WHERE a.rsvpEventID = %d", $eventID));
                                   
      $wpdb->query($wpdb->prepare("DELETE ".PRO_ATTENDEE_SUB_EVENTS_TABLE." WHERE rsvpEventID = %d", $eventID)); 
      
      $wpdb->query($wpdb->prepare("DELETE FROM ".PRO_ATTENDEES_TABLE." WHERE rsvpEventID = %d", $eventID));
      
      // delete events
      $wpdb->delete(PRO_EVENT_ATTENDEE_TABLE, array("rsvpEventID" => $eventID));
      
      $wpdb->query($wpdb->prepare("DELETE FROM ".PRO_EVENT_TABLE." WHERE id = %d", $eventID));
      
      $wpdb->query($wpdb->prepare("UPDATE ".PRO_EVENT_TABLE." SET parentEventID = 0 WHERE parentEventID = %d", $eventID));
      
      
    }
  }
  
  function rsvp_pro_admin_eventList() {
    global $wpdb;
    
    $freeInstalled = rsvp_pro_is_free_rsvp_installed();
  ?>
  	<div class="wrap">	
  		<div id="icon-edit" class="icon32"><br /></div>	
  		<h2><?php _e("List of Events", "rsvp-pro-plugin"); ?></h2>
      <?php
        if(isset($_GET['add']) && ($_GET['add'] == "success")) {
      ?>
        <div id="message" class="updated"><p class="updated"><?php _e("Event added successfully", "rsvp-pro-plugin"); ?></p></div>
      <?php 
        } else if(isset($_GET['edit']) && ($_GET['edit'] == "success")) {
      ?>
        <div id="message" class="updated"><p class="updated"><?php _e("Event updated successfully", "rsvp-pro-plugin"); ?></p></div>
      <?php
        }
      ?>
  		<form method="post" id="rsvp-form" enctype="multipart/form-data">
  			<input type="hidden" id="rsvp-bulk-action" name="rsvp-bulk-action" />
  			<input type="hidden" id="sortValue" name="sortValue" value="<?php echo htmlentities($sort, ENT_QUOTES); ?>" />
  			<input type="hidden" name="exportSortDirection" value="<?php echo htmlentities($sortDirection, ENT_QUOTES); ?>" />
  			<div class="tablenav">
  				<div class="clear"></div>
  			</div>
      </form>
  		<table class="widefat post fixed striped" cellspacing="0">
  			<thead>
  				<tr>
            <th width="75"><?php _e("Event ID", "rsvp-pro-plugin"); ?></th>
            <th><?php _e("Event Name", "rsvp-pro-plugin"); ?></th>
            <th><?php _e("Short Code", "rsvp-pro-plugin"); ?></th>
            <th><?php _e("Attendees", "rsvp-pro-plugin"); ?></th>
          </tr>
        </thead>
        <?php
          $sql = "SELECT id, eventName, open_date, close_date FROM ".PRO_EVENT_TABLE.
            " WHERE parentEventID IS NULL OR parentEventID = 0";
          $events = $wpdb->get_results($sql);
          foreach($events as $event) {
            $delete_nonce = wp_create_nonce("rsvp-pro-top-level");
        ?>
          <tr class="format-standard hentry category-uncategorized  iedit author-self" valign="top">
            <td>
              <?php echo $event->id; ?>
            </td>
          	<td class="post-title page-title column-title">
              <strong><a class="row-title" href="<?php echo admin_url('admin.php?page=rsvp-pro-admin-manage-event&id='.$event->id);  ?>" title="Edit “<?php echo htmlspecialchars($event->eventName); ?>”">
                <?php echo htmlspecialchars($event->eventName); ?></a></strong>
              <?php 
              if(rsvp_pro_admin_user_has_access_to_settings($event->id)) {
              ?>
              <div class="row-actions">
                <span class="edit"><a href="<?php echo admin_url('admin.php?page=rsvp-pro-admin-manage-event&id='.$event->id);  ?>" title="Edit this item"><?php _e("Settings", "rsvp-pro-plugin"); ?></a> | </span>
                <span class="trash"><a class="submitdelete" title="Delete this event" href="<?php 
                echo admin_url("admin.php?page=rsvp-pro-top-level&action=delete&eventID=".$event->id."&_wpnonce=".$delete_nonce); ?>"><?php _e("Delete", "rsvp-pro-plugin"); ?></a> | </span>
                <span class="export"><a href="<?php echo admin_url('admin.php?page=rsvp-pro-top-level&action=export&eventID='.$event->id); ?>"><?php _e("Export Attendees", "rsvp-pro-plugin"); ?></a> | </span>
                <span class="import"><a href="<?php echo admin_url('admin.php?page=rsvp-pro-top-level&action=import&eventID='.$event->id); ?>"><?php _e("Import Attendees", "rsvp-pro-plugin"); ?></a> | </span>
                <span class="create_attendee"><a href="<?php echo admin_url('admin.php?page=rsvp-pro-top-level&action=modify_attendee&eventID='.$event->id); ?>"><?php _e("Add Attendee", "rsvp-pro-plugin"); ?></a> | </span>
                <span class="customQ"><a href="<?php echo admin_url('admin.php?page=rsvp-pro-top-level&action=custom_questions&eventID='.$event->id); ?>"><?php _e("Custom Questions", "rsvp-pro-plugin"); ?></a> | </span>
                <span class="copyEvent"><a href="<?php echo admin_url('admin.php?page=rsvp-pro-top-level&action=copy&eventID='.$event->id); ?>"><?php _e("Copy Event", "rsvp-pro-plugin"); ?></a> </span>
                <?php
                if($freeInstalled) {
                ?>
                  <span class="importFromFree"> | <a href="<?php echo admin_url('admin.php?page=rsvp-pro-top-level&action=import_from_free&eventID='.$event->id); ?>"><?php _e("Import from Free RSVP", "rsvp-pro-plugin"); ?></a> </span>
                <?php
                }
                ?>
                <span class="massEmail"> | <a href="<?php echo admin_url('admin.php?page=rsvp-pro-top-level&action=mass_email&eventID='.$event->id); ?>"><?php _e("Send Message", "rsvp-pro-plugin"); ?></a> | </span>
                <span class="allAttendees"> <a href="<?php echo admin_url('admin.php?page=rsvp-pro-top-level&action=all_attendees&eventID='.$event->id); ?>"><?php _e("All Attendees", "rsvp-pro-plugin"); ?></a> </span>
              </div>
              <?php
              }
              ?>
            </td>
            <td class="short-code">[rsvppro id="<?php echo $event->id; ?>"]<br />
              [rsvppro-attendeelist id=&quot;<?php echo $event->id; ?>&quot;]</td>
            <td><a href="<?php echo admin_url("admin.php?page=rsvp-pro-top-level&action=attendees&eventID=".$event->id); ?>" title="Manage Attendees"><?php _e("Manage Attendees", "rsvp-pro-plugin"); ?></a></td>
          </tr>
        <?php
            // Check for sub-events 
            $sql = "SELECT id, eventName FROM ".PRO_EVENT_TABLE." WHERE parentEventID = %d";
            $subEvents = $wpdb->get_results($wpdb->prepare($sql, $event->id));
            if(count($subEvents) > 0) {
              ?>
                <tr class="format-standard hentry category-uncategorized  iedit author-self" valign="top">
                  <td></td>
                  <td colspan="3">
                    <table class="widefat post fixed" cellspacing="0">
                			<thead>
                				<tr>
                          <th width="75"><?php _e("Event ID", "rsvp-pro-plugin"); ?></th>
                          <th><?php _e(sprintf("Sub-Events for <strong>%s</strong>", $event->eventName), "rsvp-pro-plugin"); ?></th>
                          <th><?php _e("Attendees", "rsvp-pro-plugin"); ?></th>
                        </tr>
                      </thead>
                      <?php
                      foreach($subEvents as $se) {
                      ?>
                        <tr>
                          <td><?php echo $se->id; ?></td>
                          <td><a class="row-title" href="<?php echo admin_url('admin.php?page=rsvp-pro-admin-manage-event&id='.$se->id);  ?>" title="Edit “<?php echo esc_html($se->eventName); ?>”"><?php echo esc_html($se->eventName); ?></a>
                          <?php 
                          if(rsvp_pro_admin_user_has_access_to_settings($se->id)) {
                          ?>
                            <div class="row-actions">
                              <span class="edit"><a href="<?php echo admin_url('admin.php?page=rsvp-pro-admin-manage-event&id='.$se->id);  ?>" title="Edit this item"><?php _e("Settings", "rsvp-pro-plugin"); ?></a> | </span>
                              <span class="trash"><a class="submitdelete" title="Delete this event" href="<?php 
                              echo admin_url("admin.php?page=rsvp-pro-top-level&action=delete&eventID=".$se->id."&_wpnonce=".$delete_nonce); ?>"><?php _e("Delete", "rsvp-pro-plugin"); ?></a> | </span>
                              <span class="export"><a href="<?php echo admin_url('admin.php?page=rsvp-pro-top-level&action=export&eventID='.$se->id); ?>"><?php _e("Export Attendees", "rsvp-pro-plugin"); ?></a> | </span>
                              <span class="customQ"><a href="<?php echo admin_url('admin.php?page=rsvp-pro-top-level&action=custom_questions&eventID='.$se->id); ?>"><?php _e("Custom Questions", "rsvp-pro-plugin"); ?></a> | </span>
                              <span class="copyEvent"><a href="<?php echo admin_url('admin.php?page=rsvp-pro-top-level&action=copy&eventID='.$se->id); ?>"><?php _e("Copy Event", "rsvp-pro-plugin"); ?></a> </span>
                            </div>
                          <?php
                          }
                          ?>
                          </td>
                          <td><a href="<?php echo admin_url("admin.php?page=rsvp-pro-top-level&action=attendees&eventID=".$se->id); ?>" title="Manage Attendees"><?php _e("Manage Attendees", "rsvp-pro-plugin"); ?></a></td>
                        </tr>
                        
                      <?php
                      }
                      ?>
                    </table>
                  </td>
                </tr>
              <?php
            }
          }
        ?>
      </table>
    </div>
  <?php
  }
  
	function rsvp_pro_admin_guestlist($eventID, $showAll = false) {
		global $wpdb;		
    $isSubEvent = false;
    $subEventID = 0;
    
    if($eventID <= 0) {
      rsvp_pro_admin_eventList();
      exit;
    }
    
    // Check to see if there is a parent event...
    $sql = "SELECT parentEventID FROM ".PRO_EVENT_TABLE." WHERE id = %d";
    $parentEventID = $wpdb->get_var($wpdb->prepare($sql, $eventID));
    
    if($parentEventID > 0) {
      $subEventID = $eventID;
      $isSubEvent = true;
    }
    
    // Pagination work 
    $pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
    $limit = 25; // number of rows in page
    $limitOptions = array(25, 50, 100, "all");
    if(in_array($_GET['pagesize'], $limitOptions)) {
      $limit = $_GET['pagesize'];
    }
    $offset = ( $pagenum - 1 ) * $limit;
    
    if($limit != "all") {
      $totalEventID = $eventID;
      if($isSubEvent) {
        $totalEventID = $parentEventID;
      }
      $totalQuery = $wpdb->prepare("SELECT COUNT(`id`) FROM ".PRO_ATTENDEES_TABLE." WHERE rsvpEventID = %d", $totalEventID);
      
      if(isset($_GET['s']) && !empty($_GET['s'])) {
        $totalQuery = $wpdb->prepare("SELECT COUNT(`id`) FROM ".PRO_ATTENDEES_TABLE." 
          WHERE rsvpEventID = %d AND (firstName LIKE '%%%s%%' OR lastName LIKE '%%%s%%' OR email LIKE '%%%s%%')", 
          $totalEventID, $_GET['s'], $_GET['s'], $_GET['s']);
      }
      $total = $wpdb->get_var($totalQuery);
      
      $num_of_pages = ceil( $total / $limit );
      $page_links = paginate_links( array(
          'base' => add_query_arg( 'pagenum', '%#%' ),
          'format' => '',
          'prev_text' => __( '&laquo;', 'text-domain' ),
          'next_text' => __( '&raquo;', 'text-domain' ),
          'total' => $num_of_pages,
          'current' => $pagenum
      ) );
    }
		
		rsvp_pro_install_passcode_field();
		if((count($_POST) > 0) && ($_POST['rsvp-bulk-action'] == "delete") && (is_array($_POST['attendee']) && (count($_POST['attendee']) > 0))) {
			foreach($_POST['attendee'] as $attendee) {
				if(is_numeric($attendee) && ($attendee > 0)) {
					$wpdb->query($wpdb->prepare("DELETE FROM ".PRO_ASSOCIATED_ATTENDEES_TABLE." WHERE attendeeID = %d OR associatedAttendeeID = %d", 
																			$attendee, 
																			$attendee));
					$wpdb->query($wpdb->prepare("DELETE FROM ".PRO_ATTENDEES_TABLE." WHERE id = %d", 
																			$attendee));

          $wpdb->query($wpdb->prepare("DELETE FROM ".PRO_ATTENDEE_SUB_EVENTS_TABLE." WHERE rsvpAttendeeID = %d", 
                                      $attendee));
				}
			}
		}
		
		$sql = "SELECT a.id, firstName, lastName, rsvpStatus, note, ". 
           "additionalAttendee, personalGreeting, passcode, email, rsvpDate ".
           "FROM ".PRO_ATTENDEES_TABLE." a 
           INNER JOIN ".PRO_EVENT_TABLE." e ON e.id = a.rsvpEventID 
           WHERE rsvpEventID = %d  AND ((IFNULL(e.event_access, '".RSVP_PRO_OPEN_EVENT_ACCESS."') != '".RSVP_PRO_PRIVATE_EVENT_ACCESS."') OR (a.id IN (SELECT rsvpAttendeeID FROM ".PRO_EVENT_ATTENDEE_TABLE." WHERE rsvpEventID = e.id)))";

    if($isSubEvent) {
  		$sql = "SELECT a.id, firstName, lastName, IFNULL(se.rsvpStatus, 'NoResponse') AS rsvpStatus, note, ". 
             "additionalAttendee, personalGreeting, passcode, email, se.rsvpDate ".
             "FROM ".PRO_ATTENDEES_TABLE." a 
              INNER JOIN ".PRO_EVENT_TABLE." e ON e.id = %d 
             LEFT JOIN ".PRO_ATTENDEE_SUB_EVENTS_TABLE." se ON se.rsvpAttendeeID = a.id AND se.rsvpEventID = %d 
            WHERE a.rsvpEventID = %d AND ((IFNULL(e.event_access, '".RSVP_PRO_OPEN_EVENT_ACCESS."') != '".RSVP_PRO_PRIVATE_EVENT_ACCESS."') OR (a.id IN (SELECT rsvpAttendeeID FROM ".PRO_EVENT_ATTENDEE_TABLE." WHERE rsvpEventID = e.id)))";
    } else if($showAll) {
      $sql = "SELECT a.id, firstName, lastName, rsvpStatus, note, ". 
           "additionalAttendee, personalGreeting, passcode, email, rsvpDate ".
           "FROM ".PRO_ATTENDEES_TABLE." a 
           INNER JOIN ".PRO_EVENT_TABLE." e ON e.id = a.rsvpEventID 
           WHERE rsvpEventID = %d ";      
    }
    
    if(isset($_GET['s']) && !empty($_GET['s'])) {
      $sql .= " AND (firstName LIKE '%%%s%%' OR lastName LIKE '%%%s%%' OR email LIKE '%%%s%%') ";
    }
		
		$orderBy = " lastName, firstName";
		if(isset($_GET['sort'])) {
			if(strToLower($_GET['sort']) == "rsvpstatus") {
        $orderBy = " rsvpStatus ";
        if($isSubEvent) {
          $orderBy = " se.rsvpStatus ";
        }
				$orderBy .= ((strtolower($_GET['sortDirection']) == "desc") ? "DESC" : "ASC") .", ".$orderBy;
      } else if(strToLower($_GET['sort']) == "rsvpdate") {
        $orderBy = " rsvpDate ";
        if($isSubEvent) {
          $orderBy = " se.rsvpDate ";
        }
        $orderBy .= ((strtolower($_GET['sortDirection']) == "desc") ? "DESC" : "ASC") .", ".$orderBy;
			} else if(strToLower($_GET['sort']) == "attendee") {
				$direction = ((strtolower($_GET['sortDirection']) == "desc") ? "DESC" : "ASC");
				$orderBy = " lastName $direction, firstName $direction";
			}	else if(strToLower($_GET['sort']) == "additional") {
				$orderBy = " additionalAttendee ".((strtolower($_GET['sortDirection']) == "desc") ? "DESC" : "ASC") .", ".$orderBy;
			}	else if(strToLower($_GET['sort']) == "passcode") {
        $orderBy = " passcode ".((strtolower($_GET['sortDirection']) == "desc") ? "DESC" : "ASC") .", ".$orderBy;
      }
		}
		$sql .= " ORDER BY ".$orderBy;

    if($limit != "all") {
      $sql .= " LIMIT $offset, $limit ";  
    }
    
    if($isSubEvent) {
      if(isset($_GET['s']) && !empty($_GET['s'])) {
        $attendees = $wpdb->get_results($wpdb->prepare($sql, $subEventID, $subEventID, $parentEventID, $_GET['s'], $_GET['s'], $_GET['s']));
      } else {
		    $attendees = $wpdb->get_results($wpdb->prepare($sql, $subEventID, $subEventID, $parentEventID));
      }
    } else {
      if(isset($_GET['s']) && !empty($_GET['s'])) {
        $attendees = $wpdb->get_results($wpdb->prepare($sql, $eventID, $_GET['s'], $_GET['s'], $_GET['s']));
      } else {
        $attendees = $wpdb->get_results($wpdb->prepare($sql, $eventID));
      }
    }
		$sort = "";
		$sortDirection = "asc";
		if(isset($_GET['sort'])) {
			$sort = $_GET['sort'];
		}
		
		if(isset($_GET['sortDirection'])) {
			$sortDirection = $_GET['sortDirection'];
		}
	?>
		<script type="text/javascript" language="javascript">
			jQuery(document).ready(function() {
				jQuery("#cb").click(function() {
					if(jQuery("#cb").attr("checked")) {
						jQuery("input[name='attendee[]']").attr("checked", "checked");
					} else {
						jQuery("input[name='attendee[]']").removeAttr("checked");
					}
				});
			});
		</script>
		<div class="wrap">	
			<div id="icon-edit" class="icon32"><br /></div>	
			<h2><?php _e("List of current attendees", "rsvp-pro-plugin"); ?> - <?php echo get_event_name($eventID); ?></h2>
			<div class="alignright actions">
        <?php if(isset($_GET['s']) && !empty($_GET['s'])):  ?>
        <div>Searching for <strong><?php echo esc_html($_GET['s']); ?></strong> - 
          <a href="<?php echo admin_url("admin.php?page=rsvp-pro-top-level&action=attendees&eventID=$eventID"); ?>">clear search</a></div>
        <?php endif; ?>
				<form method="get" action="<?php echo admin_url("admin.php"); ?>">
          <input type="text" name="s" id="s" />
          <input type="submit" value="Search Attendees" class="button" />
          <input type="hidden" name="page" value="rsvp-pro-top-level" />
          <input type="hidden" name="action" value="attendees" />
          <input type="hidden" name="eventID" value="<?php echo $eventID; ?>" />
        </form>
			</div>
      <?php
      if ( $page_links && ($limit != "all") ) {
      ?>
          <div class="tablenav">
            <div class="alignright" style="margin: 1em 0">
              <div class="alignleft">
                <form action="<?php echo admin_url("admin.php?page=rsvp-pro-top-level&action=attendees&eventID=$eventID"); ?>">
                  <input type="hidden" name="page" value="rsvp-pro-top-level" />
                  <input type="hidden" name="action" value="attendees" />
                  <input type="hidden" name="eventID" value="<?php echo $eventID; ?>" />
                <label>
                  <?php _e("Results per page", "rsvp-pro-plugin"); ?>
                  <select name="pagesize" size="1" class="pagesize-selector">
                    <option value="25" <?php echo ($limit == 25) ? "selected=\"selected\"" : ""; ?> ><?php _e("25", "rsvp-pro-plugin"); ?></option>
                    <option value="50" <?php echo ($limit == 50) ? "selected=\"selected\"" : ""; ?>><?php _e("50", "rsvp-pro-plugin"); ?></option>
                    <option value="100" <?php echo ($limit == 100) ? "selected=\"selected\"" : ""; ?>><?php _e("100", "rsvp-pro-plugin"); ?></option>
                    <option value="all" <?php echo ($limit == "all") ? "selected=\"selected\"" : ""; ?>><?php _e("All", "rsvp-pro-plugin"); ?></option>
                  </select>
                </label>
                </form>
              </div>
              <div class="tablenav-pages"><?php echo $page_links; ?></div>
            </div>
          </div>
      <?php
      }
      ?>
      <div class="clear"></div>
			<form method="post" id="rsvp-form" enctype="multipart/form-data" action="<?php echo admin_url("admin.php?page=rsvp-pro-top-level&action=attendees&eventID=$eventID"); ?>">
				<input type="hidden" id="rsvp-bulk-action" name="rsvp-bulk-action" />
				<input type="hidden" id="sortValue" name="sortValue" value="<?php echo htmlentities($sort, ENT_QUOTES); ?>" />
				<input type="hidden" name="exportSortDirection" value="<?php echo htmlentities($sortDirection, ENT_QUOTES); ?>" />
				<div class="tablenav">
					<div class="alignleft actions">
						<select id="rsvp-action-top" name="rsvpAction">
							<option value="" selected="selected"><?php _e('Bulk Actions', 'rsvp-pro-plugin'); ?></option>
							<option value="delete"><?php _e('Delete', 'rsvp-pro-plugin'); ?></option>
              <option value="mass_email"><?php _e('Send Message', 'rsvp-pro-plugin'); ?></option>
						</select>
						<input type="submit" value="<?php _e('Apply', 'rsvp-pro-plugin'); ?>" name="doaction" id="doaction" class="button-secondary action" onclick="document.getElementById('rsvp-bulk-action').value = document.getElementById('rsvp-action-top').value;" />
						<input type="submit" value="<?php _e('Export Attendees', 'rsvp-pro-plugin'); ?>" name="exportButton" id="exportButton" class="button-secondary action" onclick="document.getElementById('rsvp-bulk-action').value = 'export';" />
            
            <a href="<?php echo admin_url("admin.php?page=rsvp-pro-top-level&action=modify_attendee&eventID=$eventID"); ?>"><?php _e("Add Attendee", "rsvp-pro-plugin"); ?></a>
            |
            <a href="<?php echo admin_url("admin.php?page=rsvp-pro-top-level"); ?>"><?php _e("Event List", "rsvp-pro-plugin"); ?></a>
					</div>
					<?php
          if($isSubEvent) {
            $baseSql = "SELECT COUNT(*) 
              FROM ".PRO_ATTENDEES_TABLE." a
              LEFT JOIN ".PRO_ATTENDEE_SUB_EVENTS_TABLE." se ON se.rsvpAttendeeID = a.id AND se.rsvpEventID = %d 
              LEFT JOIN ".PRO_EVENT_TABLE." e ON e.id = se.rsvpEventID 
              WHERE ((IFNULL(e.event_access, '".RSVP_PRO_OPEN_EVENT_ACCESS."') != '".RSVP_PRO_PRIVATE_EVENT_ACCESS."') OR (a.id IN (SELECT rsvpAttendeeID FROM ".PRO_EVENT_ATTENDEE_TABLE." WHERE rsvpEventID = e.id))) AND ";
            
            $sql = $baseSql." se.rsvpStatus = 'Yes' AND a.rsvpEventID = %d";
						$yesResults = $wpdb->get_var($wpdb->prepare($sql, $subEventID, $parentEventID));
            
            $sql = $baseSql." se.rsvpStatus = 'No' AND a.rsvpEventID = %d";
						$noResults = $wpdb->get_var($wpdb->prepare($sql, $subEventID, $parentEventID));
            
            $sql = $baseSql." (se.rsvpStatus = 'NoResponse' OR se.rsvpStatus IS NULL) AND a.rsvpEventID = %d";
            if(rsvp_pro_get_event_information($subEventID, RSVP_PRO_INFO_EVENT_ACCESS) == RSVP_PRO_PRIVATE_EVENT_ACCESS) {
              $sql = "SELECT COUNT(*) FROM ".PRO_EVENT_ATTENDEE_TABLE." ea 
                LEFT JOIN ".PRO_ATTENDEE_SUB_EVENTS_TABLE." se ON se.rsvpAttendeeID = ea.rsvpAttendeeID
                JOIN ".PRO_ATTENDEES_TABLE." a ON a.id = ea.rsvpAttendeeID 
                WHERE (se.rsvpStatus IS NULL OR se.rsvpStatus = 'NoResponse') AND ea.rsvpEventID = %d AND a.rsvpEventID = %d";
            }

						$noResponseResults = $wpdb->get_var($wpdb->prepare($sql, $subEventID, $parentEventID));

            $sql = $baseSql." se.rsvpStatus = 'Waitlist' AND a.rsvpEventID = %d";
            $waitListCount = $wpdb->get_var($wpdb->prepare($sql, $subEventID, $parentEventID));
            
          } else {
            $baseSql = "SELECT COUNT(*) FROM ".PRO_ATTENDEES_TABLE." a 
              JOIN ".PRO_EVENT_TABLE." e ON e.id = a.rsvpEventID 
              WHERE ((IFNULL(e.event_access, '".RSVP_PRO_OPEN_EVENT_ACCESS."') != '".RSVP_PRO_PRIVATE_EVENT_ACCESS."') OR (a.id IN (SELECT rsvpAttendeeID FROM ".PRO_EVENT_ATTENDEE_TABLE." WHERE rsvpEventID = e.id))) ";

						$yesResults = $wpdb->get_var($wpdb->prepare($baseSql." AND rsvpStatus = 'Yes' AND rsvpEventID = %d", $eventID));
						$noResults = $wpdb->get_var($wpdb->prepare($baseSql." AND rsvpStatus = 'No' AND rsvpEventID = %d", $eventID));
						$noResponseResults = $wpdb->get_var($wpdb->prepare($baseSql." AND rsvpStatus = 'NoResponse' AND rsvpEventID = %d", $eventID));
            $waitListCount = $wpdb->get_var($wpdb->prepare($baseSql." AND rsvpStatus = 'Waitlist' AND rsvpEventID = %d", $eventID));
          }
					?>
          <?php if(!$showAll) : ?>
					<div class="alignright"><?php _e("RSVP Count", "rsvp-pro-plugin"); ?> -  
						<?php _e("Yes", "rsvp-pro-plugin"); ?>: <strong><?php echo $yesResults; ?></strong> &nbsp; &nbsp;  &nbsp; &nbsp; 
						<?php _e("No", "rsvp-pro-plugin"); ?>: <strong><?php echo $noResults; ?></strong> &nbsp; &nbsp;  &nbsp; &nbsp; 
            <?php if(rsvp_pro_waitlist_enabled($eventID)) : ?>
              <?php _e("Waitlist", "rsvp-pro-plugin"); ?>: <strong><?php echo $waitListCount; ?></strong> &nbsp; &nbsp;  &nbsp; &nbsp; 
            <?php endif; ?>

						<?php _e("No Response", "rsvp-pro-plugin"); ?>: <strong><?php echo $noResponseResults; ?></strong> &nbsp; &nbsp;  &nbsp; &nbsp; 

					</div>
          <?php endif; ?>
					<div class="clear"></div>
				</div>
			<table class="widefat post fixed" cellspacing="0">
				<thead>
					<tr>
						<th scope="col" class="manage-column column-cb check-column" style=""><input type="checkbox" id="cb" /></th>
						<th scope="col" id="attendeeName" class="manage-column column-title" style=""><?php _e("Attendee", "rsvp-pro-plugin"); ?></a> &nbsp;
							<a href="<?php echo admin_url("admin.php?page=rsvp-pro-top-level&action=attendees&eventID=$eventID&sort=attendee&amp;sortDirection=asc");?>">
								<img src="<?php echo plugins_url( "uparrow".((($sort == "attendee") && ($sortDirection == "asc")) ? "_selected" : "").".gif", RSVP_PRO_PLUGIN_FILE); ?>" width="11" height="9" 
									alt="Sort Ascending Attendee Status" title="Sort Ascending Attendee Status" border="0"></a> &nbsp;
							<a href="<?php echo admin_url("admin.php?page=rsvp-pro-top-level&action=attendees&eventID=$eventID&sort=attendee&amp;sortDirection=desc");?>">
								<img src="<?php echo plugins_url( "downarrow".((($sort == "attendee") && ($sortDirection == "desc")) ? "_selected" : "").".gif", RSVP_PRO_PLUGIN_FILE); ?>" width="11" height="9" 
									alt="Sort Descending Attendee Status" title="Sort Descending Attendee Status" border="0"></a>
						</th>			
            <!--<th scope="col" id="rsvpEmail" class="manage-column column-title"><?php echo __("Email", 'rsvp-pro-plugin'); ?></th>-->
						<th scope="col" id="rsvpStatus" class="manage-column column-title" style=""><?php _e("RSVP Status", "rsvp-pro-plugin"); ?> &nbsp;
							<a href="<?php echo admin_url("admin.php?page=rsvp-pro-top-level&action=attendees&eventID=$eventID&sort=rsvpStatus&amp;sortDirection=asc");?>">
								<img src="<?php echo plugins_url( "uparrow".((($sort == "rsvpStatus") && ($sortDirection == "asc")) ? "_selected" : "").".gif", RSVP_PRO_PLUGIN_FILE); ?>" width="11" height="9" 
									alt="Sort Ascending RSVP Status" title="Sort Ascending RSVP Status" border="0"></a> &nbsp;
							<a href="<?php echo admin_url("admin.php?page=rsvp-pro-top-level&action=attendees&eventID=$eventID&sort=rsvpStatus&amp;sortDirection=desc");?>">
								<img src="<?php echo plugins_url( "downarrow".((($sort == "rsvpStatus") && ($sortDirection == "desc")) ? "_selected" : "").".gif", RSVP_PRO_PLUGIN_FILE); ?>" width="11" height="9" 
									alt="Sort Descending RSVP Status" title="Sort Descending RSVP Status" border="0"></a>
						</th>
            <th scope="col" id="rsvpDate" class="manage-column column-title"><?php echo __("RSVP Date", 'rsvp-pro-plugin'); ?> &nbsp; 
              <a href="<?php echo admin_url("admin.php?page=rsvp-pro-top-level&action=attendees&eventID=$eventID&sort=rsvpDate&amp;sortDirection=asc");?>">
                <img src="<?php echo plugins_url( "uparrow".((($sort == "rsvpDate") && ($sortDirection == "asc")) ? "_selected" : "").".gif", RSVP_PRO_PLUGIN_FILE); ?>" width="11" height="9" 
                  alt="Sort Ascending RSVP Date" title="Sort Ascending RSVP Date" border="0"></a> &nbsp;
              <a href="<?php echo admin_url("admin.php?page=rsvp-pro-top-level&action=attendees&eventID=$eventID&sort=rsvpDate&amp;sortDirection=desc");?>">
                <img src="<?php echo plugins_url( "downarrow".((($sort == "rsvpDate") && ($sortDirection == "desc")) ? "_selected" : "").".gif", RSVP_PRO_PLUGIN_FILE); ?>" width="11" height="9" 
                  alt="Sort Descending RSVP Date" title="Sort Descending RSVP Date" border="0"></a></th>
  
            <?php if(!$isSubEvent) { ?>
						<th scope="col" id="additionalAttendee" class="manage-column column-title" style=""><?php _e("Additional Attendee", "rsvp-pro-plugin"); ?>		 &nbsp;
									<a href="<?php echo admin_url("admin.php?page=rsvp-pro-top-level&action=attendees&eventID=$eventID&sort=additional&amp;sortDirection=asc");?>">
										<img src="<?php echo plugins_url( "uparrow".((($sort == "additional") && ($sortDirection == "asc")) ? "_selected" : "").".gif", RSVP_PRO_PLUGIN_FILE); ?>" width="11" height="9" 
											alt="Sort Ascending Additional Attendees Status" title="Sort Ascending Additional Attendees Status" border="0"></a> &nbsp;
									<a href="<?php echo admin_url("admin.php?page=rsvp-pro-top-level&action=attendees&eventID=$eventID&sort=additional&amp;sortDirection=desc");?>">
										<img src="<?php echo plugins_url( "downarrow".((($sort == "additional") && ($sortDirection == "desc")) ? "_selected" : "").".gif", RSVP_PRO_PLUGIN_FILE); ?>" width="11" height="9" 
											alt="Sort Descending Additional Attendees Status" title="Sort Descending Additional Atttendees Status" border="0"></a>
						</th>
            <?php } ?>
            <?php if(!$isSubEvent) { ?>
						<th scope="col" id="note" class="manage-column column-title" style=""><?php _e("Note", "rsvp-pro-plugin"); ?></th>
            <?php } ?>
						<?php
						if(rsvp_pro_require_passcode($eventID)) {
						?>
							<th scope="col" id="passcode" class="manage-column column-title" style=""><?php _e("Passcode", "rsvp-pro-plugin"); ?>&nbsp; 
              <a href="<?php echo admin_url("admin.php?page=rsvp-pro-top-level&action=attendees&eventID=$eventID&sort=passcode&amp;sortDirection=asc");?>">
                <img src="<?php echo plugins_url( "uparrow".((($sort == "passcode") && ($sortDirection == "asc")) ? "_selected" : "").".gif", RSVP_PRO_PLUGIN_FILE); ?>" width="11" height="9" 
                  alt="Sort Ascending Passcode" title="Sort Ascending Passcode" border="0"></a> &nbsp;
              <a href="<?php echo admin_url("admin.php?page=rsvp-pro-top-level&action=attendees&eventID=$eventID&sort=passcode&amp;sortDirection=desc");?>">
                <img src="<?php echo plugins_url( "downarrow".((($sort == "passcode") && ($sortDirection == "desc")) ? "_selected" : "").".gif", RSVP_PRO_PLUGIN_FILE); ?>" width="11" height="9" 
                  alt="Sort Descending Passcode" title="Sort Descending Passcode" border="0"></a></th>
						<?php
						}
						
						?>
						<?php
              $sql = "SELECT id, question FROM ".PRO_QUESTIONS_TABLE." 
                WHERE rsvpEventID = %d 
                ORDER BY sortOrder, id";
							$qRs = $wpdb->get_results($wpdb->prepare($sql, $eventID));
							if(count($qRs) > 0) {
								foreach($qRs as $q) {
						?>
							<th scope="col" class="manage-column -column-title"><?php echo htmlentities(stripslashes($q->question)); ?></th>
						<?php		
								}
							}
						?>
						<th scope="col" id="associatedAttendees" class="manage-column column-title" style=""><?php _e("Associated Attendees", "rsvp-pro-plugin"); ?></th>
					</tr>
				</thead>
        <tbody>
				<?php
					$i = 0;
					foreach($attendees as $attendee) {
					?>
						<tr class="<?php echo (($i % 2 == 0) ? "alternate" : ""); ?> author-self">
							<th scope="row" class="check-column"><input type="checkbox" name="attendee[]" value="<?php echo $attendee->id; ?>" /></th>						
							<td>
								<a href="<?php echo admin_url("admin.php?page=rsvp-pro-top-level&action=modify_attendee&eventID=$eventID&id=$attendee->id"); ?>">
                  <?php echo htmlentities(stripslashes($attendee->firstName)." ".stripslashes($attendee->lastName)); ?></a>
							</td>
              <!--<td><?php echo htmlspecialchars(stripslashes($attendee->email)); ?></td>-->
							<td><?php echo rsvp_pro_humanize_rsvp_status($attendee->rsvpStatus); ?></td>
              <td><?php echo rsvp_pro_humanize_rsvp_status($attendee->rsvpDate); ?></td>
							
              <?php if(!$isSubEvent) { ?>
							<td><?php 
								if($attendee->rsvpStatus == "NoResponse") {
									echo "--";
								} else {
									echo (($attendee->additionalAttendee == "Y") ? __("Yes", "rsvp-pro-plugin") : __("No", "rsvp-pro-plugin")); 
								}
							?></td>
              <?php } ?>
							
              <?php if(!$isSubEvent) { ?>
							<td><?php echo nl2br(stripslashes(trim($attendee->note))); ?></td>
              <?php } ?>
							<?php
							if(rsvp_pro_require_passcode($eventID)) {
							?>
								<td><?php echo $attendee->passcode; ?></td>
							<?php	
							}
								$sql = "SELECT question, answer FROM ".PRO_QUESTIONS_TABLE." q 
									LEFT JOIN ".PRO_ATTENDEE_ANSWERS." ans ON q.id = ans.questionID AND ans.attendeeID = %d 
                  WHERE rsvpEventID = %d 
									ORDER BY q.sortOrder, q.id";
								$aRs = $wpdb->get_results($wpdb->prepare($sql, $attendee->id, $eventID));
								if(count($aRs) > 0) {
									foreach($aRs as $a) {
							?>
									<td><?php echo htmlentities(str_replace("||", ", ", stripslashes($a->answer))); ?></td>
							<?php
									}
								}
							?>
							<td>
							<?php
                if($isSubEvent) {
                  $sql = "SELECT firstName, lastName FROM ".PRO_ATTENDEES_TABLE." a 
                  LEFT JOIN ".PRO_EVENT_TABLE." e ON e.id = %d 
                  WHERE a.rsvpEventID = %d AND (a.id IN (SELECT attendeeID FROM ".PRO_ASSOCIATED_ATTENDEES_TABLE." WHERE associatedAttendeeID = %d) 
                    OR a.id in (SELECT associatedAttendeeID FROM ".PRO_ASSOCIATED_ATTENDEES_TABLE." WHERE attendeeID = %d)) AND 
                    ( 
                      (IFNULL(e.event_access, '".RSVP_PRO_OPEN_EVENT_ACCESS."') != '".RSVP_PRO_PRIVATE_EVENT_ACCESS."') OR 
                      (a.id IN (SELECT rsvpAttendeeID FROM ".PRO_EVENT_ATTENDEE_TABLE." WHERE rsvpEventID = %d))
                    )";
                  $associations = $wpdb->get_results($wpdb->prepare($sql, $eventID, $parentEventID, $attendee->id, $attendee->id, $eventID));
                } else {
                  $sql = "SELECT firstName, lastName FROM ".PRO_ATTENDEES_TABLE." a 
                  JOIN ".PRO_EVENT_TABLE." e ON e.id = a.rsvpEventID 
                  WHERE (a.id IN (SELECT attendeeID FROM ".PRO_ASSOCIATED_ATTENDEES_TABLE." WHERE associatedAttendeeID = %d) 
                    OR a.id in (SELECT associatedAttendeeID FROM ".PRO_ASSOCIATED_ATTENDEES_TABLE." WHERE attendeeID = %d)) AND 
                    ( 
                      (IFNULL(e.event_access, '".RSVP_PRO_OPEN_EVENT_ACCESS."') != '".RSVP_PRO_PRIVATE_EVENT_ACCESS."') OR 
                      (a.id IN (SELECT rsvpAttendeeID FROM ".PRO_EVENT_ATTENDEE_TABLE." WHERE rsvpEventID = %d))
                    )";
                  if($showAll) {
                    $sql = "SELECT firstName, lastName FROM ".PRO_ATTENDEES_TABLE." a 
                    JOIN ".PRO_EVENT_TABLE." e ON e.id = a.rsvpEventID 
                    WHERE (a.id IN (SELECT attendeeID FROM ".PRO_ASSOCIATED_ATTENDEES_TABLE." WHERE associatedAttendeeID = %d) 
                      OR a.id in (SELECT associatedAttendeeID FROM ".PRO_ASSOCIATED_ATTENDEES_TABLE." WHERE attendeeID = %d)) ";
                    $associations = $wpdb->get_results($wpdb->prepare($sql, $attendee->id, $attendee->id));  
                  } else {
                    $associations = $wpdb->get_results($wpdb->prepare($sql, $attendee->id, $attendee->id, $eventID));  
                  }
                }
								foreach($associations as $a) {
									echo htmlentities(stripslashes($a->firstName." ".$a->lastName))."<br />";
								}
							?>
							</td>
						</tr>
					<?php
						$i++;
					}
				?>
          </tbody>
				</table>
        </form>
        <?php
        if ( $page_links && ($limit != "all") ) {
        ?>
            <div class="tablenav">
              <div class="alignright" style="margin: 1em 0">
                <div class="alignleft">
                  <form action="<?php echo admin_url("admin.php"); ?>">
                    <input type="hidden" name="page" value="rsvp-pro-top-level" />
                    <input type="hidden" name="action" value="attendees" />
                    <input type="hidden" name="eventID" value="<?php echo $eventID; ?>" />
                  <label>
                    <?php _e("Results per page", "rsvp-pro-plugin"); ?>
                    <select name="pagesize" size="1" class="pagesize-selector">
                      <option value="25" <?php echo ($limit == 25) ? "selected=\"selected\"" : ""; ?> ><?php _e("25", "rsvp-pro-plugin"); ?></option>
                      <option value="50" <?php echo ($limit == 50) ? "selected=\"selected\"" : ""; ?>><?php _e("50", "rsvp-pro-plugin"); ?></option>
                      <option value="100" <?php echo ($limit == 100) ? "selected=\"selected\"" : ""; ?>><?php _e("100", "rsvp-pro-plugin"); ?></option>
                      <option value="all" <?php echo ($limit == "all") ? "selected=\"selected\"" : ""; ?>><?php _e("All", "rsvp-pro-plugin"); ?></option>
                    </select>
                  </label>
                  </form>
                </div>
                <div class="tablenav-pages"><?php echo $page_links; ?></div>
              </div>
            </div>
        <?php
        }
        ?>
		</div>
	<?php
	}
	
	function rsvp_pro_admin_export($eventID) {
		global $wpdb;
    if(isset($_GET['eventID']) && ($_GET['eventID'] > 0)) {
      $eventID = $_GET['eventID'];
    }
    
    $isSubEvent = false;
    $parentEventId = $wpdb->get_var($wpdb->prepare("SELECT parentEventID FROM ".PRO_EVENT_TABLE." WHERE id = %d", $eventID));
    if($parentEventId > 0) {
      $isSubEvent = true;
      $subEventID = $eventID;
      $eventID = $parentEventId;
    }
    $queryString = 's=[rsvppro id="'.$eventID.'"]';
    $query = new WP_Query( $queryString );
    if($query->have_posts()) {
      $query->the_post();
      $customLinkBase = get_permalink();
      if(strpos($customLinkBase, "?") !== false) {
        $customLinkBase .= "&";
      } else {
        $customLinkBase .= "?";
      }
      
      if(rsvp_pro_require_only_passcode_to_register($eventID)) {
        $customLinkBase .= "passcode=%s";
      } else {
        $customLinkBase .= "firstName=%s&lastName=%s";
        
        if(rsvp_pro_require_passcode($eventID)) {
          $customLinkBase .= "&passcode=%s";
        }
      }
    }
    wp_reset_postdata();
    
      if($isSubEvent) {
  			$sql = "SELECT a.id, firstName, lastName, IFNULL(se.rsvpStatus, 'NoResponse') AS rsvpStatus, 
                note, additionalAttendee, passcode, email, salutation, suffix, a.rsvpDate, a.primaryAttendee   
  							FROM ".PRO_ATTENDEES_TABLE." a 
                INNER JOIN ".PRO_EVENT_TABLE." e ON e.id = %d 
                LEFT JOIN ".PRO_ATTENDEE_SUB_EVENTS_TABLE." se ON se.rsvpAttendeeID = a.id AND se.rsvpEventID = %d 
               WHERE a.rsvpEventID = %d AND ((IFNULL(e.event_access, '".RSVP_PRO_OPEN_EVENT_ACCESS."') != '".RSVP_PRO_PRIVATE_EVENT_ACCESS."') OR (a.id IN (SELECT rsvpAttendeeID FROM ".PRO_EVENT_ATTENDEE_TABLE." WHERE rsvpEventID = e.id)))" ;
      } else { 
  			$sql = "SELECT a.id, firstName, lastName, rsvpStatus, note, additionalAttendee, 
                passcode, email, salutation, suffix, a.rsvpDate, a.primaryAttendee  
  							FROM ".PRO_ATTENDEES_TABLE." a
                INNER JOIN ".PRO_EVENT_TABLE." e ON e.id = a.rsvpEventID 
                WHERE rsvpEventID = %d AND ((IFNULL(e.event_access, '".RSVP_PRO_OPEN_EVENT_ACCESS."') != '".RSVP_PRO_PRIVATE_EVENT_ACCESS."') OR (a.id IN (SELECT rsvpAttendeeID FROM ".PRO_EVENT_ATTENDEE_TABLE." WHERE rsvpEventID = e.id)))" ;
      }
							
			$orderBy = " lastName, firstName";
			if(isset($_POST['sortValue'])) {
				if(strToLower($_POST['sortValue']) == "rsvpstatus") {
					$orderBy = " rsvpStatus ".((strtolower($_POST['exportSortDirection']) == "desc") ? "DESC" : "ASC") .", ".$orderBy;
				}else if(strToLower($_POST['sortValue']) == "attendee") {
					$direction = ((strtolower($_POST['exportSortDirection']) == "desc") ? "DESC" : "ASC");
					$orderBy = " lastName $direction, firstName $direction";
				}	else if(strToLower($_POST['sortValue']) == "additional") {
					$orderBy = " additionalAttendee ".((strtolower($_POST['exportSortDirection']) == "desc") ? "DESC" : "ASC") .", ".$orderBy;
				}			
			}
			$sql .= " ORDER BY ".$orderBy;
      if($isSubEvent) {
        $preparedSql = $wpdb->prepare($sql, $subEventID, $subEventID, $eventID);
      } else {
        $preparedSql = $wpdb->prepare($sql, $eventID);
      }
			$attendees = $wpdb->get_results($preparedSql);
      
      $showSuffix = false;
      $showSalutation = false;
      if(rsvp_pro_get_event_option($eventID, RSVP_PRO_OPTION_SHOW_SALUTATION) == "Y") {
        $showSalutation = true;
      }
      if(rsvp_pro_get_event_option($eventID, RSVP_PRO_OPTION_SHOW_SUFFIX) == "Y") {
        $showSuffix = true;
      }
      
			$csv = "\"First Name\",\"Last Name\",\"RSVP Status\",\"RSVP Date\",";
      
      if($showSalutation) {
        $csv = "\"Salutation\",".$csv;
      }
      
      if($showSuffix) {
        $csv .= "\"Suffix\",";
      }
			
			$csv .= "\"Additional Attendee\",";
			
      if(rsvp_pro_require_passcode($eventID)) {
        $csv .= "\"Passcode\",";
      }
			$csv .= "\"Note\",\"Email\",\"Primary Attendee\",\"Associated Attendees\"";
			
			$qRs = $wpdb->get_results($wpdb->prepare("SELECT id, question FROM ".PRO_QUESTIONS_TABLE." WHERE rsvpEventID = %d ORDER BY sortOrder, id", ($isSubEvent) ? $subEventID : $eventID));
			if(count($qRs) > 0) {
				foreach($qRs as $q) {
					$csv .= ",\"".stripslashes($q->question)."\"";
				}
			}
      
      if($customLinkBase != "") {
        $csv .= ",\"pre-fill URL\"";
      }
			
			$csv .= "\r\n";
      
			if(isset($_SERVER['HTTP_USER_AGENT']) && preg_match("/MSIE/", $_SERVER['HTTP_USER_AGENT'])) {
				// IE Bug in download name workaround
				ini_set( 'zlib.output_compression','Off' );
			}
			header('Content-Description: RSVP Export');
			header("Content-Type: application/vnd.ms-excel", true);
			header('Content-Disposition: attachment; filename="rsvpEntries.csv"'); 
      echo $csv;
      
			foreach($attendees as $a) {
        $csv = "";
        if($showSalutation) {
          $csv .= "\"".stripslashes($a->salutation)."\",";
        }
				$csv .= "\"".stripslashes($a->firstName)."\",\"".stripslashes($a->lastName)."\",\"".($a->rsvpStatus)."\",\"";
        if(!empty($a->rsvpDate)) {
          $csv .= date("m/d/Y", strToTime($a->rsvpDate));
        }
        $csv .= "\",";
				
        if($showSuffix) {
          $csv .= "\"".stripslashes($a->suffix)."\",";
        }
        
				$csv .= "\"".(($a->additionalAttendee == "Y") ? "Yes" : "No")."\",";
				
        if(rsvp_pro_require_passcode($eventID)) {
          $csv .= "\"".(($a->passcode))."\",";
        }
				$csv .= "\"".(str_replace("\"", "\"\"", stripslashes($a->note)))."\",\"";
        $csv .= stripslashes($a->email)."\",\"";

        $csv .= (($a->primaryAttendee == "Y") ? "Y" : "N")."\",\"";
			
				$sql = "SELECT CONCAT_WS('', firstName, lastName) AS name FROM ".PRO_ATTENDEES_TABLE." a 
          JOIN ".PRO_EVENT_TABLE." e ON e.id = a.rsvpEventID 
				 	WHERE a.id IN (SELECT attendeeID FROM ".PRO_ASSOCIATED_ATTENDEES_TABLE." WHERE associatedAttendeeID = %d) 
						OR a.id in (SELECT associatedAttendeeID FROM ".PRO_ASSOCIATED_ATTENDEES_TABLE." WHERE attendeeID = %d) 
            AND ((IFNULL(e.event_access, '".RSVP_PRO_OPEN_EVENT_ACCESS."') != '".RSVP_PRO_PRIVATE_EVENT_ACCESS."') OR (a.id IN (SELECT rsvpAttendeeID FROM ".PRO_EVENT_ATTENDEE_TABLE." WHERE rsvpEventID = e.id)))";
            
				$associations = $wpdb->get_results($wpdb->prepare($sql, $a->id, $a->id));
				foreach($associations as $assc) {
					$csv .= trim(stripslashes($assc->name))."\r\n";
				}
				$csv .= "\"";

				$qRs = $wpdb->get_results($wpdb->prepare("SELECT q.id, question, answer 
          FROM ".PRO_QUESTIONS_TABLE." q 
          LEFT JOIN ".PRO_ATTENDEE_ANSWERS." a ON a.attendeeID = %d AND questionID = q.id 
        WHERE q.rsvpEventID = %d ORDER BY sortOrder, q.id", $a->id, ($isSubEvent) ? $subEventID : $eventID));
				if(count($qRs) > 0) {
					foreach($qRs as $q) {
						if(!empty($q->answer)) {
							$csv .= ",\"".stripslashes($q->answer)."\"";
						} else {
							$csv .= ",\"\"";
						}
					}
				}
        
        if($customLinkBase != "") {
          if(rsvp_pro_require_only_passcode_to_register($eventID)) {
            $csv .= ",\"".sprintf($customLinkBase, urlencode(stripslashes($a->passcode)))."\"";
          } else if(rsvp_pro_require_passcode($eventID)) {
            $csv .= ",\"".sprintf($customLinkBase, urlencode(stripslashes($a->firstName)), urlencode(stripslashes($a->lastName)), urlencode(stripslashes($a->passcode)))."\"";
          } else {
            $csv .= ",\"".sprintf($customLinkBase, urlencode(stripslashes($a->firstName)), urlencode(stripslashes($a->lastName)))."\"";
          }
        }
				
				$csv .= "\r\n";
        echo $csv;
			}
			exit();
	}
	
	function rsvp_pro_admin_import($eventID) {
		global $wpdb;

    if(!rsvp_pro_admin_user_has_access_to_settings($eventID)) {
      rsvp_pro_admin_eventList();
      return;
    }

		if(count($_FILES) > 0) {
			check_admin_referer('rsvp-import');

      $passcodeLength = 6;
      if(rsvp_pro_get_event_option($eventID, RSVP_PRO_OPTION_PASSWORD_LENGTH) > 0) {
        $passcodeLength = rsvp_pro_get_event_option($eventID, RSVP_PRO_OPTION_PASSWORD_LENGTH);
      }

      // TODO: Rewrite this
      require(RSVP_PRO_PLUGIN_PATH.'/spreadsheet-reader/php-excel-reader/excel_reader2.php');
      require(RSVP_PRO_PLUGIN_PATH.'/spreadsheet-reader/SpreadsheetReader.php');
      require(RSVP_PRO_PLUGIN_PATH."/includes/admin/import_handlers.inc.php");
			
      $data = new SpreadsheetReader($_FILES['importFile']['tmp_name'], $_FILES['importFile']['name']);
      $skipFirstRow = false;
      $numCols = count($data->current());
      $i = 0;
      if($numCols >= 14) {
        // Associating private questions... have to skip the first row
        $skipFirstRow = true;
      }
      
			if($numCols >= 2) {
				$count = 0;
        $headerRow = array();
				foreach($data as $row) {
          if(!$skipFirstRow || ($i > 0)) {
            handleImportRow($row, $headerRow, $numCols, $count, $eventID);
          } else {
            $headerRow = $row;
          }
          $i++;
				}
        
			?>
			<p><strong><?php echo $count; ?></strong> <?php _e("total records were imported", "rsvp-pro-plugin"); ?>.</p>
			<p><?php _e("Continue to the RSVP", "rsvp-pro-plugin"); ?> <a href="admin.php?page=rsvp-pro-top-level"><?php _e("list", "rsvp-pro-plugin"); ?></a></p>
			<?php
			}
		} else {
		?>
      <h3><?php echo get_event_name($_GET['eventID']); ?> Import</h3>
			<form name="rsvp_import" method="post" enctype="multipart/form-data">
				<?php wp_nonce_field('rsvp-import'); ?>
				<p><?php _e("Select an Excel file or CSV in the following format:", "rsvp-pro-plugin"); ?><br />
				Column 1: <strong><?php _e("Salutation", "rsvp-pro-plugin"); ?></strong><br />
        Column 2: <strong><?php _e("First Name", "rsvp-pro-plugin"); ?></strong><br />
        Column 3: <strong><?php _e("Last Name", "rsvp-pro-plugin"); ?></strong><br />
        Column 4: <strong><?php _e("Suffix", "rsvp-pro-plugin"); ?></strong><br />
        Column 5: <strong><?php _e("Email", 'rsvp-plugin'); ?></strong><br /> 
        Column 6: <strong><?php _e("Associated Attendees", "rsvp-pro-plugin"); ?>*</strong><br />
        Column 7: <strong><?php _e("Custom Message", "rsvp-pro-plugin"); ?></strong><br />
        Column 8: <strong><?php _e("Passcode", "rsvp-pro-plugin"); ?></strong><br />
        Column 9: <strong><?php _e("RSVP Status (valid values: yes, no, noresponse)", "rsvp-pro-plugin"); ?></strong><br />
        Column 10: <strong><?php _e("Number of guests allowed for attendee", "rsvp-pro-plugin"); ?></strong><br />
        Column 11: <strong><?php _e("Admin Note", "rsvp-pro-plugin"); ?></strong><br />
        Column 12: <strong><?php _e("Primary Attendee", "rsvp-pro-plugin"); ?></strong><br />
        Columns 13+: <strong><?php _e("Private Question Association", "rsvp-pro-plugin"); ?>**</strong><br />
        Columns 13+: <strong><?php _e("Custom Question Values", "rsvp-pro-plugin"); ?>***</strong>
				</p>
				<p>
				* <?php _e("associated attendees should be separated by a comma it is assumed that the first space encountered will separate the first and last name.", "rsvp-pro-plugin"); ?>
				</p>
        <p>
          ** <?php _e("This can be multiple columns each column is associated with one of the following 
          private questions. If you wish to have the guest associated with the question 
          put a &quot;Y&quot; in the column otherwise put whatever else you want. The header 
          name will be the &quot;private import key&quot; which is also listed below. It 
          has the format of pq_* where * is a number.", "rsvp-pro-plugin"); ?>
        </p>
        <p>
          *** <?php _e("This can be multiple columns each column is associated with one of the following 
          custom questions. The header 
          name will be the &quot;import key&quot; which is also listed below. It 
          has the format of cq_* where * is a number.", "rsvp-pro-plugin"); ?>
          <ul>
          <?php
          $questions = $wpdb->get_results($wpdb->prepare("SELECT id, question, permissionLevel FROM ".PRO_QUESTIONS_TABLE." WHERE rsvpEventID = %d", $eventID));
          foreach($questions as $q) {
          ?>
            <?php if($q->permissionLevel == "private") { ?>
              <li><?php echo htmlspecialchars(stripslashes($q->question)); ?> - pq_<?php echo $q->id; ?></li>
            <?php
            }?>
            <li><?php echo htmlspecialchars(stripslashes($q->question)); ?> value - cq_<?php echo $q->id; ?></li>
          <?php
          }
          ?>
          </ul>
        </p>
				<p><?php _e("A header row is not expected, UNLESS you are associating private questions.", "rsvp-pro-plugin"); ?></p>
				<p><input type="file" name="importFile" id="importFile" /></p>
				<p><input type="submit" value="Import File" name="goRsvp" /></p>
			</form>
		<?php
		}
	}
  
  /*
    Description: Shows the form and handles the processing for sending emails to attendees
  */
  function rsvp_pro_admin_mass_email($eventID) {
    global $wpdb;
    if(isset($_POST['email_subject']) && !empty($_POST['email_subject']) && isset($_POST['email_body']) && !empty($_POST['email_body'])) {
      check_admin_referer('rsvp-email');

      $isSubEvent = false;
      $parentEventId = $wpdb->get_var($wpdb->prepare("SELECT parentEventID FROM ".PRO_EVENT_TABLE." WHERE id = %d", $eventID));
      if($parentEventId > 0) {
        $isSubEvent = true;
        $subEventID = $eventID;
        $eventID = $parentEventId;
      }
      
      $queryString = 's=[rsvppro id="'.$eventID.'"]';
      $query = new WP_Query( $queryString );
      $eventUrl = "";
      if($query->have_posts()) {
        $query->the_post();
        $eventUrl = get_permalink();
      }
      wp_reset_postdata();
      
      if($isSubEvent) {
  			$sql = "SELECT a.id, firstName, lastName, IFNULL(se.rsvpStatus, 'NoResponse') AS rsvpStatus, 
                note, passcode, email, salutation, suffix, a.rsvpDate  
  							FROM ".PRO_ATTENDEES_TABLE." a 
                INNER JOIN ".PRO_EVENT_TABLE." e ON e.id = %d 
                LEFT JOIN ".PRO_ATTENDEE_SUB_EVENTS_TABLE." se ON se.rsvpAttendeeID = a.id AND se.rsvpEventID = %d 
               WHERE a.rsvpEventID = %d AND email <> '' AND ((IFNULL(e.event_access, '".RSVP_PRO_OPEN_EVENT_ACCESS."') != '".RSVP_PRO_PRIVATE_EVENT_ACCESS."') OR (a.id IN (SELECT rsvpAttendeeID FROM ".PRO_EVENT_ATTENDEE_TABLE." WHERE rsvpEventID = e.id)))" ;
      } else { 
  			$sql = "SELECT a.id, firstName, lastName, rsvpStatus, note, passcode, email, salutation, suffix, a.rsvpDate  
  							FROM ".PRO_ATTENDEES_TABLE." a
                INNER JOIN ".PRO_EVENT_TABLE." e ON e.id = a.rsvpEventID 
                WHERE rsvpEventID = %d AND email <> '' AND ((IFNULL(e.event_access, '".RSVP_PRO_OPEN_EVENT_ACCESS."') != '".RSVP_PRO_PRIVATE_EVENT_ACCESS."') OR (a.id IN (SELECT rsvpAttendeeID FROM ".PRO_EVENT_ATTENDEE_TABLE." WHERE rsvpEventID = e.id)))" ;
      }

      if(!empty($_POST['rsvp_status'])) {
        $sql .= " AND a.rsvpStatus = %s ";
      }
      
      if(isset($_POST['attendees'])) {
        $attendees = explode(",", $_POST['attendees']);
        if(count($attendees) > 0) {
          $valid = true;
          // Make sure that all the attendee values passed in are numeric
          foreach($attendees as $a) {
            if(!is_numeric($a) || ($a <= 0)) {
              $valid = false;
            }
          }
          
          if($valid) {
            $sql .= " AND a.id IN (".$_POST['attendees'].")";
          }
        }
      }
      if($isSubEvent) {
        if(!empty($_POST['rsvp_status'])) {
          $preparedSql = $wpdb->prepare($sql, $subEventID, $subEventID, $eventID, $_POST['rsvp_status']);
        } else {
          $preparedSql = $wpdb->prepare($sql, $subEventID, $subEventID, $eventID);
        }
        
      } else {
        if(!empty($_POST['rsvp_status'])) {
          $preparedSql = $wpdb->prepare($sql, $eventID, $_POST['rsvp_status']);
        } else {
          $preparedSql = $wpdb->prepare($sql, $eventID);
        }
      }

      $attendees = $wpdb->get_results($preparedSql);
      $headers = array('Content-Type: text/html; charset=UTF-8');
      if(!empty($_POST['email_from'])) {
        $headers[] = 'From: '.$_POST['email_from']. "\r\n";
      }
      
      foreach($attendees as $a) {
        $subject = rsvp_pro_admin_replaceVariablesForEmail($a, $eventUrl, $_POST['email_subject'], ($isSubEvent) ? $subEventID : $eventID);
        $email_body = rsvp_pro_admin_replaceVariablesForEmail($a, $eventUrl, $_POST['email_body'], ($isSubEvent) ? $subEventID : $eventID);
        $subject    = stripslashes($subject);
        $email_body = nl2br(stripslashes($email_body));

        wp_mail($a->email, $subject, $email_body, $headers);
        ?>
          <p><?php echo stripslashes($a->firstName)." ".stripslashes($a->lastName)." ".__("emailed", "rsvp-pro-plugin");?></p>
        <?php
      }
      ?>
      <br />
      <p><?php _e("Continue to the RSVP", "rsvp-pro-plugin"); ?> <a href="admin.php?page=rsvp-pro-top-level"><?php _e("list", "rsvp-pro-plugin"); ?></a></p>
      <?php
    } else {
      // Handle the case when we are passing in only specific attendees.
      $attendees = array();
      if(isset($_POST['attendee'])) {
        foreach($_POST['attendee'] as $attendee) {
          if(is_numeric($attendee) && ($attendee > 0)) {
            $attendees[] = $attendee;
          }
        }
      }
    ?>
      <h3><?php _e("Send a message for", "rsvp-pro-plugin"); ?> <?php echo get_event_name($eventID); ?></h3>
			<form name="rsvp_email" method="post" action="<?php echo admin_url('admin.php?page=rsvp-pro-top-level&action=mass_email&eventID='.$eventID); ?>">
				<?php wp_nonce_field('rsvp-email'); ?>
        <input type="hidden" name="eventID" value="<?php echo $eventID; ?>" />
        <?php
        if(count($attendees) > 0) {
        ?>
          <input type="hidden" name="attendees" value="<?php echo implode(",", $attendees); ?>" />
        <?php 
        }
        ?>
        <table class="form-table">
          <tbody>
            <tr>
              <th scope="row">
                <label for="email_from"><?php _e("From Address", "rsvp-pro-plugin"); ?></label>
              </th>
              <td>
                <input type="text" name="email_from" id="email_from" value="<?php echo rsvp_pro_get_event_option($eventID, RSVP_PRO_OPTION_NOTIFY_EMAIL); ?>" />
              </td>
            </tr>
            <tr>
              <th scope="row">
                <label for="rsvp_status"><?php _e("Limit to RSVP Status", "rsvp-pro-plugin"); ?></label>
              </th>
              <td>
                <select name="rsvp_status" id="rsvp_status" size="1">
                  <option value="">--</option>
                  <option value="No"><?php _e("No", "rsvp-pro-plugin"); ?></option>
                  <option value="NoResponse"><?php _e("No Response", "rsvp-pro-plugin"); ?></option>
                  <?php if(rsvp_pro_get_event_option($eventID, RSVP_PRO_OPTION_ENABLE_WAITLIST) == "Y"): ?>
                  <option value="Waitlist"><?php _e("Waitlist", "rsvp-pro-plugin"); ?></option>
                <?php endif; ?>
                  <option value="Yes"><?php _e("Yes", "rsvp-pro-plugin"); ?></option>
                </select>
              </td>
            </tr>
            <tr>
              <th scope="row">
                Available attendee data placeholders for subject &amp; message:
              </th>
              <td>
                [[FirstName]]<br />
                [[LastName]]<br /> 
                [[Email]]<br /> 
                [[Passcode]]<br />
                [[EventUrl]]<br />
                [[EventName]]<br />
                [[Attendee_Rsvp_Full_Info]]<br />
                <?php
                $sql = "SELECT q.id, question FROM ".PRO_QUESTIONS_TABLE." q 
                  JOIN ".PRO_QUESTION_TYPE_TABLE." qt ON qt.id = q.questionTypeID 
                  WHERE q.rsvpEventID = %d AND 
                  qt.questionType NOT IN ('hidden', 'readonly')";
                $questions = $wpdb->get_results($wpdb->prepare($sql, $eventID));
                foreach($questions as $q) { ?>
                  [[CustomQ_<?php echo $q->id?>]] - <?php echo esc_html(stripslashes($q->question)); ?>?<br />
                <?php
                }
                ?>
              </td>
            </tr>
            <tr>
              <th scope="row">
                <label for="email_subject"><?php _e("Subject", "rsvp-pro-plugin"); ?></label>
              </th>
              <td>
                <input type="text" name="email_subject" id="email_subject" class="large-text" />
              </td>
            </tr>
            <tr>
              <th scope="row">
                <label for="email_body"><?php _e("Message", "rsvp-pro-plugin"); ?></label>
              </th>
              <td>
                <?php wp_editor( "", "email_body", $settings = array() ); ?> 
              </td>
            </tr>
          </tbody>
        </table>
        <p class="submit"><input type="submit" value="<?php _e("Send Message", "rsvp-pro-plugin"); ?>" class="button-primary" name="sendMessage"></p>
      </form>
    <?php
    }
  }

  function rsvp_pro_admin_handle_copy_event($eventID, $newEventName, $copyOverAttendees = false, $copySubEvents = false) {
    global $wpdb;

    $sql = "SELECT close_date, eventName, event_access, open_date, options, parentEventID FROM ".PRO_EVENT_TABLE." WHERE id = %d";
    $eventToCopy = $wpdb->get_row($wpdb->prepare($sql, $eventID));

    if($eventToCopy) {     
      $wpdb->insert(PRO_EVENT_TABLE, 
                    array(
                        "close_date"    => $eventToCopy->close_date, 
                        "open_date"     => $eventToCopy->open_date, 
                        "eventName"     => $newEventName, 
                        "event_access"  => $eventToCopy->event_access, 
                        "options"       => $eventToCopy->options,
                      ), 
                    array('%s', '%s', '%s', '%s', '%s'));

      $newEventId = $wpdb->insert_id;

      if($newEventId > 0) {
        $oldQuestionIdToNewId = array();
        // Copy over the custom questions... 
        $sql = "SELECT id, grouping, permissionLevel, question, questionTypeID, 
                       required, sortOrder 
                FROM ".PRO_QUESTIONS_TABLE." WHERE rsvpEventID = %d";
        $questions = $wpdb->get_results($wpdb->prepare($sql, $eventID));
        if($questions) {
          foreach($questions as $q) {
            $wpdb->insert(PRO_QUESTIONS_TABLE, 
                          array(
                              "grouping"          => $q->grouping, 
                              "permissionLevel"   => $q->permissionLevel, 
                              "question"          => stripslashes($q->question), 
                              "questionTypeID"    => $q->questionTypeID, 
                              "required"          => $q->required, 
                              "sortOrder"         => $q->sortOrder, 
                              "rsvpEventID"       => $newEventId, 
                            ), 
                            array('%s', '%s', '%s', '%s', '%s', '%s', '%d'));
            $newQuestionId = $wpdb->insert_id;
            $oldQuestionIdToNewId[$q->id] = $newQuestionId;

            $sql = "SELECT answer, defaultAnswer FROM ".PRO_QUESTION_ANSWERS_TABLE." WHERE questionID = %d";
            $answers = $wpdb->get_results($wpdb->prepare($sql, $q->id));
            if($answers) {
              foreach($answers as $a) {
                $wpdb->insert(PRO_QUESTION_ANSWERS_TABLE, 
                              array(
                                "questionID"      => $newQuestionId, 
                                "answer"          => stripslashes($a->answer), 
                                "defaultAnswer"   => $a->defaultAnswer, 
                              ), 
                              array('%d', '%s', '%s'));
              }
            } // if($answers)...
          } // foreach($questions as...
        } // if($questions

        if($copySubEvents) {
          $sql = "SELECT id, close_date, eventName, event_access, open_date, options, parentEventID FROM ".PRO_EVENT_TABLE." WHERE parentEventID = %d";
          $subEvents = $wpdb->get_results($wpdb->prepare($sql, $eventID));
          foreach($subEvents as $se) {
            $wpdb->insert(PRO_EVENT_TABLE, 
                    array(
                        "close_date"    => $se->close_date, 
                        "open_date"     => $se->open_date, 
                        "eventName"     => stripslashes($se->eventName)." - copy", 
                        "event_access"  => $se->event_access, 
                        "options"       => $se->options,
                        "parentEventID" => $newEventId
                      ), 
                    array('%s', '%s', '%s', '%s', '%s', '%d'));
            $subEventId = $wpdb->insert_id;
            if($subEventId > 0) {
              // Copy over the custom questions... 
              $sql = "SELECT id, grouping, permissionLevel, question, questionTypeID, 
                             required, sortOrder 
                      FROM ".PRO_QUESTIONS_TABLE." WHERE rsvpEventID = %d";
              $questions = $wpdb->get_results($wpdb->prepare($sql, $se->id));
              if($questions) {
                foreach($questions as $q) {
                  $wpdb->insert(PRO_QUESTIONS_TABLE, 
                                array(
                                    "grouping"          => $q->grouping, 
                                    "permissionLevel"   => $q->permissionLevel, 
                                    "question"          => stripslashes($q->question), 
                                    "questionTypeID"    => $q->questionTypeID, 
                                    "required"          => $q->required, 
                                    "sortOrder"         => $q->sortOrder, 
                                    "rsvpEventID"       => $subEventId, 
                                  ), 
                                  array('%s', '%s', '%s', '%s', '%s', '%s', '%d'));
                  $newQuestionId = $wpdb->insert_id;
                  $oldQuestionIdToNewId[$q->id] = $newQuestionId;

                  $sql = "SELECT answer, defaultAnswer FROM ".PRO_QUESTION_ANSWERS_TABLE." WHERE questionID = %d";
                  $answers = $wpdb->get_results($wpdb->prepare($sql, $q->id));
                  if($answers) {
                    foreach($answers as $a) {
                      $wpdb->insert(PRO_QUESTION_ANSWERS_TABLE, 
                                    array(
                                      "questionID"      => $newQuestionId, 
                                      "answer"          => stripslashes($a->answer), 
                                      "defaultAnswer"   => $a->defaultAnswer, 
                                    ), 
                                    array('%d', '%s', '%s'));
                    }
                  } // if($answers)...
                } // foreach($questions as...
              } // if($questions
            }
          }
        }

        if($copyOverAttendees) {
          $oldAttendeeIdToNewId = array();
          $sql = "SELECT id, firstName, lastName, note, additionalAttendee, ". 
                  "personalGreeting, rsvpEventID, passcode, email, numGuests, suffix, ".
                  "salutation, nicknames, primaryAttendee ".
                  "FROM ".PRO_ATTENDEES_TABLE." WHERE rsvpEventID = %d";
          $attendees = $wpdb->get_results($wpdb->prepare($sql, $eventID));

          // Copy over attendee
          foreach($attendees as $a) {
            $wpdb->insert(PRO_ATTENDEES_TABLE, 
              array(
                "rsvpEventID"         => $newEventId,  
                "firstName"           => stripslashes($a->firstName), 
                "lastName"            => stripslashes($a->lastName), 
                "note"                => stripslashes($a->note),
                "additionalAttendee"  => stripslashes($a->additionalAttendee),
                "personalGreeting"    => stripslashes($a->personalGreeting),
                "passcode"            => stripslashes($a->passcode),
                "email"               => stripslashes($a->email),
                "numGuests"           => stripslashes($a->numGuests),
                "suffix"              => stripslashes($a->suffix),
                "salutation"          => stripslashes($a->salutation),
                "nicknames"           => stripslashes($a->nicknames),
                "primaryAttendee"     => stripslashes($a->primaryAttendee)
              ),
              array('%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s'));

            $newAttendeeId = $wpdb->insert_id;
            $oldAttendeeIdToNewId[$a->id] = $newAttendeeId;
          }         
          // Copy over associations...
          $sql = "SELECT attendeeID, associatedAttendeeID FROM ".PRO_ASSOCIATED_ATTENDEES_TABLE.
                " WHERE attendeeID IN (".implode(",", array_keys($oldAttendeeIdToNewId)).")";
          $associatedAttendees = $wpdb->get_results($sql);
          foreach($associatedAttendees as $aa) {
            $wpdb->insert(PRO_ASSOCIATED_ATTENDEES_TABLE, 
              array("attendeeID" => $oldAttendeeIdToNewId[$aa->attendeeID], 
                    "associatedAttendeeID" => $oldAttendeeIdToNewId[$aa->associatedAttendeeID]), 
              array('%d', '%d'));
          }

          // Copy over question attendee associations 
          $sql = "SELECT questionID, attendeeID FROM ".PRO_QUESTION_ATTENDEES_TABLE." WHERE questionID IN (".implode(",", array_keys($oldQuestionIdToNewId)).")";
          $questionAttendees = $wpdb->get_results($sql);
          foreach($questionAttendees as $qa) {
            $wpdb->insert(PRO_QUESTION_ATTENDEES_TABLE, 
              array("questionID" => $oldQuestionIdToNewId[$qa->questionID], 
                    "attendeeID" => $oldAttendeeIdToNewId[$qa->attendeeID]), 
              array("%d", "%d"));
          }
          
          // Copy over event access association 
          $sql = "SELECT rsvpEventID, rsvpAttendeeID FROM ".PRO_EVENT_ATTENDEE_TABLE." WHERE rsvpEventID = %d";
          $attendees = $wpdb->get_results($wpdb->prepare($sql, $eventID));
          foreach($attendees as $aa) {
            $wpdb->insert(PRO_EVENT_ATTENDEE_TABLE, 
              array("rsvpEventID" => $newEventId, 
                    "rsvpAttendeeID" => $oldAttendeeIdToNewId[$aa->rsvpAttendeeID]), 
              array('%d', '%d'));
          }
        }
      } // if($newEventId
    }
  }

  /*
   * Function to handle copying of an event, will display a confirmation prompt first and then copy if the user 
   * chooses to proceed. 
   */
  function rsvp_pro_admin_copy_event($eventID) {
    global $wpdb;
    if(isset($_POST['copyEventSubmit'])) {
      check_admin_referer('rsvp-copy');

        $sql = "SELECT close_date, eventName, event_access, open_date, options, parentEventID FROM ".PRO_EVENT_TABLE." WHERE id = %d";
        $eventToCopy = $wpdb->get_row($wpdb->prepare($sql, $eventID));

        if($eventToCopy) { 
          $newEventName = stripslashes($eventToCopy->eventName)." - Copy";
          $copyOverAttendees = false;
          if($_POST['copyOverAttendees'] == "Y") {
            $copyOverAttendees = true;
          }
          rsvp_pro_admin_handle_copy_event($eventID, $newEventName, $copyOverAttendees);
        }
      ?>
        <div id="message" class="updated"><p class="updated"><?php _e("Event copied to ", "rsvp-pro-plugin"); ?><?php echo $newEventName; ?></p></div>
      <?php
      rsvp_pro_admin_eventList();
    } else {
    ?>
      <h3><?php _e("Copy ", "rsvp-pro-plugin"); ?> <?php echo get_event_name($eventID); ?></h3>
      <form name="rsvp_email" method="post" action="<?php echo admin_url('admin.php?page=rsvp-pro-top-level&action=copy&eventID='.$eventID); ?>">
        <?php wp_nonce_field('rsvp-copy'); ?>
        <p><?php _e("Copy all settings and custom questions from ", "rsvp-pro-plugin"); ?>
          <?php echo get_event_name($eventID); ?> <?php _e(" to a new event?", "rsvp-pro-plugin"); ?></p>
        <p><label><?php _e("Copy attendees as well?", "rsvp-pro-plugin"); ?> 
          <input type="checkbox" name="copyOverAttendees" value="Y" /></label></p>
        <p class="submit"><input type="submit" value="<?php _e("Copy Event", "rsvp-pro-plugin"); ?>" name="copyEventSubmit" class="button-primary" /></p>
      </form>
    <?php
    }
  }
  
  function rsvp_pro_admin_replaceVariablesForEmail($attendee, $eventUrl, $stringToReplace, $eventID) {
    global $wpdb;
    global $rsvpId; 
    $rsvpId = $eventID;
    $replacedString = $stringToReplace;
    $replacedString = str_ireplace("[[FirstName]]", stripslashes($attendee->firstName), $replacedString);
    $replacedString = str_ireplace("[[LastName]]", stripslashes($attendee->lastName), $replacedString);
    $replacedString = str_ireplace("[[Email]]", stripslashes($attendee->email), $replacedString);
    $replacedString = str_ireplace("[[Passcode]]", stripslashes($attendee->passcode), $replacedString);
    $replacedString = str_ireplace("[[EventUrl]]", $eventUrl, $replacedString);
    $replacedString = str_ireplace("[[EventName]]", get_event_name($eventID), $replacedString);
    $replacedString = str_ireplace("[[Attendee_Rsvp_Full_Info]]", rsvp_pro_retrieveEmailBodyContent($attendee->id, $attendee), $replacedString);
    $sql = "SELECT q.id, question, a.answer FROM ".PRO_QUESTIONS_TABLE." q 
      JOIN ".PRO_QUESTION_TYPE_TABLE." qt ON qt.id = q.questionTypeID 
      LEFT JOIN ".PRO_ATTENDEE_ANSWERS." a ON a.questionID = q.id AND a.attendeeID = %d
      WHERE q.rsvpEventID = %d AND 
      qt.questionType NOT IN ('hidden', 'readonly') AND
      (q.permissionLevel = 'public' OR (%d IN (SELECT attendeeID FROM ".PRO_QUESTION_ATTENDEES_TABLE." WHERE questionID = q.id))) ";
    $questions = $wpdb->get_results($wpdb->prepare($sql, $attendee->id, $eventID, $attendee->id));
    foreach($questions as $q) {
      if($q->answer != "") {
        $tmpString = stripslashes($q->question).": ".stripslashes($q->answer);
        $replacedString = str_ireplace("[[CustomQ_".$q->id."]]", $tmpString, $replacedString);
      } else {
        $replacedString = str_ireplace("[[CustomQ_".$q->id."]]", "", $replacedString);
      }
    }
    return $replacedString;
  }
	
	function rsvp_pro_admin_guest($eventID) {
		global $wpdb;
    global $rsvpId;
    $rsvpId = $eventID;
    $isSubEvent = false;
    $parentEventID = $wpdb->get_var($wpdb->prepare("SELECT parentEventID FROM ".PRO_EVENT_TABLE." WHERE id = %d", $eventID));
    
    if($parentEventID > 0) {
      $isSubEvent = true;
    }
    
		if((count($_POST) > 0) && 
       (!empty($_POST['firstName']) || $isSubEvent)
      ) {
			check_admin_referer('rsvp_add_guest');
      
      if($isSubEvent) {
  			if(isset($_POST['attendeeID']) && is_numeric($_POST['attendeeID']) && ($_POST['attendeeID'] > 0)) {
          $attendeeId = $_POST['attendeeID'];
          
          $existingId = $wpdb->get_var($wpdb->prepare("SELECT id FROM ".PRO_ATTENDEE_SUB_EVENTS_TABLE." WHERE rsvpAttendeeID = %d AND rsvpEventID = %d", $attendeeId, $eventID));
          
          if($existingId > 0) {
    				$wpdb->update(PRO_ATTENDEE_SUB_EVENTS_TABLE, 
    											array("rsvpStatus" => trim($_POST['rsvpStatus'])),
    											array("id" => $existingId), 
    											array("%s"), 
    											array("%d"));
          } else {
    				$wpdb->insert(PRO_ATTENDEE_SUB_EVENTS_TABLE, array("rsvpStatus" => trim($_POST['rsvpStatus']),
                                                 "rsvpEventID" => $eventID, 
                                                 "rsvpAttendeeID" => $attendeeId), 
    				                               array('%s', '%d', '%d'));
          }
  			}
      } else {
  			$passcode = (isset($_POST['passcode'])) ? $_POST['passcode'] : "";
        $numGuests = null;
        if(isset($_POST['numGuests']) && ($_POST['numGuests'] >= 0) && (is_numeric($_POST['numGuests']))) {
          $numGuests = $_POST['numGuests'];
        }
    
        $primaryAttendee = ($_POST['primaryAttendee'] == "Y") ? "Y" : "N";
  			if(isset($_POST['attendeeID']) && is_numeric($_POST['attendeeID'])) {
  				$wpdb->update(PRO_ATTENDEES_TABLE, 
  											array("firstName" => trim($_POST['firstName']), 
  											      "lastName" => trim($_POST['lastName']), 
                              "nicknames" => trim($_POST['nicknames']), 
  											      "personalGreeting" => trim($_POST['personalGreeting']), 
  														"rsvpStatus" => trim($_POST['rsvpStatus']),
                              "email" => trim($_POST['email']), 
                              "note" => trim($_POST['note']), 
                              "primaryAttendee" => $primaryAttendee,
                              "numGuests" => $numGuests, 
                              "rsvpEventID" => $eventID),
  											array("id" => $_POST['attendeeID']), 
  											array("%s", "%s", "%s", "%s", "%s", "%s", "%s", "%s", "%d", "%d"), 
  											array("%d"));
  				$attendeeId = $_POST['attendeeID'];
  			} else {
  				$wpdb->insert(PRO_ATTENDEES_TABLE, array("firstName" => trim($_POST['firstName']), 
  				                                     "lastName" => trim($_POST['lastName']),
                                               "nicknames" => trim($_POST['nicknames']), 
  																						 "personalGreeting" => trim($_POST['personalGreeting']), 
  																						 "rsvpStatus" => trim($_POST['rsvpStatus']),
                                               "email" => trim($_POST['email']), 
                                               "note" => trim($_POST['note']), 
                                               "primaryAttendee" => $primaryAttendee,
                                               "numGuests" => $numGuests,  
                                               "rsvpEventID" => $eventID), 
  				                               array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d'));
				
  				$attendeeId = $wpdb->insert_id;
  			} // if(isset($_POST['attendeeID']) && is_numeric($_POST['attendeeID'])) 
        
        if(rsvp_pro_get_event_option($eventID, RSVP_PRO_OPTION_SHOW_SALUTATION) == "Y") {
  				$wpdb->update(PRO_ATTENDEES_TABLE, 
  											array("salutation" => trim($_POST['salutation'])),
  											array("id" => $attendeeId), 
  											array("%s"), 
  											array("%d"));
        }
        
        if(rsvp_pro_get_event_option($eventID, RSVP_PRO_OPTION_SHOW_SUFFIX) == "Y") {
  				$wpdb->update(PRO_ATTENDEES_TABLE, 
  											array("suffix" => trim($_POST['suffix'])),
  											array("id" => $attendeeId), 
  											array("%s"), 
  											array("%d"));
        }
		
        $wpdb->query($wpdb->prepare("DELETE FROM ".PRO_ASSOCIATED_ATTENDEES_TABLE." WHERE attendeeId = %d", $attendeeId));
        $wpdb->query($wpdb->prepare("DELETE FROM ".PRO_ASSOCIATED_ATTENDEES_TABLE." WHERE associatedAttendeeID = %d", $attendeeId));
  			if(isset($_POST['associatedAttendees']) && is_array($_POST['associatedAttendees'])) {
  				foreach($_POST['associatedAttendees'] as $aid) {
  					if(is_numeric($aid) && ($aid > 0)) {
              // Make the associations two-way
  						$wpdb->insert(PRO_ASSOCIATED_ATTENDEES_TABLE, array("attendeeID"=>$attendeeId, "associatedAttendeeID"=>$aid), array("%d", "%d"));
              $wpdb->insert(PRO_ASSOCIATED_ATTENDEES_TABLE, array("attendeeID"=>$aid, "associatedAttendeeID"=>$attendeeId), array("%d", "%d"));
  					}
  				}
      
          // See if there are some custom questions that should be associated to this new user....
          $sql = "SELECT id FROM ".PRO_QUESTIONS_TABLE." WHERE id IN (SELECT questionID FROM ".PRO_ATTENDEE_ANSWERS." WHERE attendeeID IN (SELECT associatedAttendeeID FROM ".PRO_ASSOCIATED_ATTENDEES_TABLE." WHERE attendeeID = $attendeeId)) AND grouping = 'multi' AND id NOT IN (SELECT questionID FROM ".PRO_ATTENDEE_ANSWERS." WHERE attendeeID = $attendeeId)";
          $multiQs = $wpdb->get_results($sql);
          if(count($multiQs) > 0) {
            foreach($multiQs as $mq) {
              $sql = "SELECT DISTINCT answer FROM ".PRO_ATTENDEE_ANSWERS." WHERE questionID = ".$mq->id." AND attendeeID IN (SELECT associatedAttendeeID FROM ".PRO_ASSOCIATED_ATTENDEES_TABLE." WHERE attendeeID = $attendeeId)";
              $answer = $wpdb->get_results($sql);
              if(count($answer) > 0) {
    						$wpdb->insert(PRO_ATTENDEE_ANSWERS, array("attendeeID" => $attendeeId, 
    																									 "answer" => stripslashes($answer[0]->answer), 
    																									 "questionID" => $mq->id), 
    																						 array('%d', '%s', '%d'));
              } //if(count($answer) > 0) 
            } //foreach($multiQs as $mq) 
          }
  			} // if(isset($_POST['associatedAttendees']) && is_array($_POST['associatedAttendees'])) 
      } // if($isSubEvent)
      
      rsvp_pro_handleAdditionalQuestions($attendeeId, "question", true);
      rsvp_pro_handleGroupQuestions($attendeeId, "question");
			
			if(rsvp_pro_require_passcode($eventID)) {
				if(empty($passcode)) {
          $length = 6;
    
          if(rsvp_pro_get_event_option($eventID, RSVP_PRO_OPTION_PASSWORD_LENGTH) > 0) {
            $length = rsvp_pro_get_event_option($eventID, RSVP_PRO_OPTION_PASSWORD_LENGTH);
          }
          
					$passcode = rsvp_pro_generate_passcode($length);
				}
				$wpdb->update(PRO_ATTENDEES_TABLE, 
											array("passcode" => trim($passcode)), 
											array("id"=>$attendeeId), 
											array("%s"), 
											array("%d"));
			}
      
		?>
			<p><?php _e("Attendee", "rsvp-pro-plugin"); ?> <?php echo htmlentities(stripslashes($_POST['firstName']." ".$_POST['lastName']));?> <?php _e("has been successfully saved", "rsvp-pro-plugin"); ?></p>
			<p>
				<a href="<?php echo admin_url("admin.php?page=rsvp-pro-top-level&action&action=attendees&eventID=".$eventID); ?>"><?php _e("Continue to Attendee List", "rsvp-pro-plugin"); ?></a> | 
				<a href="<?php echo admin_url("admin.php?page=rsvp-pro-top-level&action=modify_attendee&eventID=".$eventID); ?>"><?php _e("Add Attendee", "rsvp-pro-plugin"); ?></a> 
			</p>
	<?php
		} else {
			$attendee = null;
			$associatedAttendees = array();
			$firstName = "";
			$lastName = "";
			$personalGreeting = "";
			$rsvpStatus = "NoResponse";
			$passcode = "";
      $attendeeID = 0;
      $numGuests = "";
      $email = "";
      $note = "";
      $suffix = "";
      $salutation = "";
      $nicknames = "";
      $primaryAttendee = "N";
      
			
			if(isset($_GET['id']) && is_numeric($_GET['id'])) {
        $sql = "SELECT id, firstName, lastName, personalGreeting, nicknames, 
                rsvpStatus, passcode, numGuests, email, note, suffix, salutation, primaryAttendee     
                FROM ".PRO_ATTENDEES_TABLE." WHERE id = %d AND rsvpEventID = %d";
        
        if($isSubEvent) {
          $sql = "SELECT a.id, firstName, lastName, personalGreeting, nicknames, 
                  se.rsvpStatus, passcode, numGuests, email, note, a.suffix, a.salutation, a.primaryAttendee 
                  FROM ".PRO_ATTENDEES_TABLE." a 
                  LEFT JOIN ".PRO_ATTENDEE_SUB_EVENTS_TABLE." se ON se.rsvpAttendeeID = a.id AND se.rsvpEventID = %d
                  WHERE a.id = %d AND a.rsvpEventID = %d";
        }
        
        if($isSubEvent) {
          $attendee = $wpdb->get_row($wpdb->prepare($sql, $eventID, $_GET['id'], $parentEventID));
        } else {
          $attendee = $wpdb->get_row($wpdb->prepare($sql, $_GET['id'], $eventID));
        }
				
				if($attendee != null) {
          $attendeeID = $attendee->id;
					$firstName = stripslashes($attendee->firstName);
					$lastName = stripslashes($attendee->lastName);
          $nicknames = stripslashes($attendee->nicknames);
					$personalGreeting = stripslashes($attendee->personalGreeting);
					$rsvpStatus = $attendee->rsvpStatus;
					$passcode = stripslashes($attendee->passcode);
          if($attendee->numGuests > 0) {
            $numGuests = $attendee->numGuests;
          }
          $email = stripslashes($attendee->email);
          $note = stripslashes($attendee->note);
          $suffix = stripslashes($attendee->suffix);
          $salutation = stripslashes($attendee->salutation);
          $primaryAttendee = ($attendee->primaryAttendee == "Y") ? "Y" : "N";
            
					
					// Get the associated attendees and add them to an array
					$associations = $wpdb->get_results("SELECT associatedAttendeeID FROM ".PRO_ASSOCIATED_ATTENDEES_TABLE." WHERE attendeeId = ".$attendee->id.
																						 " UNION ".
																						 "SELECT attendeeID FROM ".PRO_ASSOCIATED_ATTENDEES_TABLE." WHERE associatedAttendeeID = ".$attendee->id);
					foreach($associations as $aId) {
						$associatedAttendees[] = $aId->associatedAttendeeID;
					}
				} 
			} 
	?>
      <h3><?php echo get_event_name($eventID); ?></h3>
			<form name="contact" action="<?php echo admin_url("admin.php?page=rsvp-pro-top-level&action=modify_attendee&eventID=$eventID"); ?>" method="post">
        <?php
        if($attendeeID > 0) {
        ?>
        <input type="hidden" name="attendeeID" value="<?php echo $attendeeID; ?>" />
        <?php  
        }
        ?>
				<?php
				if(rsvp_pro_get_event_option($eventID, RSVP_PRO_OPTION_PASSCODE) != "Y") {
				?>
          <input type="hidden" name="passcode" value="<?php echo htmlentities($passcode); ?>" />
        <?php  
        }
        ?>        
				<?php wp_nonce_field('rsvp_add_guest'); ?>
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e('Save'); ?>" />
				</p>
        <?php
        if($isSubEvent) :
        ?>
        <div class="updated">
          <p>You are editing the attendee record for this sub-event not all fields will be editable. To edit all fields not-related
            to this event go to the <a href="<?php echo admin_url("admin.php?page=rsvp-pro-top-level&action=modify_attendee&eventID=$parentEventID&id=$attendeeID"); ?>">main record</a>.</p>
        </div>
        <?php
        endif;
        ?>
				<table class="form-table">
          <tbody>
  					<tr>
  						<th scope="row"><label for="firstName"><?php _e("First Name", "rsvp-pro-plugin"); ?>:</label></th>
  						<td>
                <?php if($isSubEvent) {
                  echo esc_html($firstName);
                } else { ?>
                <input type="text" name="firstName" id="firstName" size="30" value="<?php echo htmlentities($firstName); ?>" /></td>
                <?php } ?>
  					</tr>
            <?php
              if(!$isSubEvent):
            ?>
              <tr>
                <th scope="row"><label for="nicknames"><?php _e("Alternative First Names", "rsvp-pro-plugin"); ?>:</label></th>
                <td>
                  <input type="text" name="nicknames" id="nicknames" size="30" value="<?php echo esc_attr_e($nicknames); ?>" />
                  <br />
                  <span class="description">Separate each alternative name with a comma (i.e. bob,jane)</span>
                </td>
              </tr>
            <?php
              endif;
            ?>
  					<tr>
  						<th scope="row"><label for="lastName"><?php _e("Last Name", "rsvp-pro-plugin"); ?>:</label></th>
  						<td>
                <?php if($isSubEvent) { 
                  echo esc_html($lastName); 
                } else { ?>
                <input type="text" name="lastName" id="lastName" size="30" value="<?php echo htmlentities($lastName); ?>" />
                <?php
                  }
                ?>
              </td>
  					</tr>
            <?php if(!$isSubEvent): ?>
              <?php if(rsvp_pro_get_event_option($eventID, RSVP_PRO_OPTION_SHOW_SALUTATION) == "Y") : ?>
      					<tr>
      						<th scope="row"><label for="salutation"><?php _e("Salutation", "rsvp-pro-plugin"); ?>:</label></th>
      						<td>
                    <select name="salutation" id="salutation">
                      <option value="">--</option>
                      <?php
                      $salutations = rsvp_pro_get_event_option($eventID, RSVP_PRO_OPTION_SALUTATIONS);
                      if(empty($salutations)) {
                        $salutations = RSVP_PRO_DEFAULT_SALUTATION;
                      }
                      $salutations = explode("||", $salutations);
                      foreach($salutations as $s) {
                        ?>
                          <option value="<?php echo esc_html($s); ?>" <?php echo (($s == $salutation) ? " selected=\"selected\"" : "");?>><?php echo esc_html($s); ?></option>
                        <?php
                      }
                      ?>
                      
                    </select>
                  </td>
      					</tr>
              <?php endif; ?>
              <?php if(rsvp_pro_get_event_option($eventID, RSVP_PRO_OPTION_SHOW_SUFFIX) == "Y") : ?>
      					<tr>
      						<th scope="row"><label for="suffix"><?php _e("Suffix", "rsvp-pro-plugin"); ?>:</label></th>
      						<td>
                    <input type="text" name="suffix" id="suffix" size="15" value="<?php echo esc_html($suffix); ?>" />
                  </td>
      					</tr>
              <?php endif; ?>
  					<tr>
  						<th scope="row"><label for="email"><?php _e("Email", "rsvp-pro-plugin"); ?>:</label></th>
  						<td>
                <input type="text" name="email" id="email" size="30" value="<?php echo esc_attr_e($email); ?>" />
              </td>
  					</tr>
            <tr>
              <th scope="row"><label for="primaryAttendee"><?php _e("Primary Attendee", "rsvp-pro-plugin"); ?>:</label></th>
              <td>
                <input type="checkbox" name="primaryAttendee" id="primaryAttendee" value="Y" 
                  <?php echo ($primaryAttendee == "Y") ? "checked=\"checked\"" : ""; ?> />
                <br />
                <span class="description"><?php _e("Primary attendees will be shown first when associated guests try to RSVP", "rsvp-pro-plugin"); ?></span>
              </td>
            </tr>
            <?php endif; ?>
  					<?php
  					if(rsvp_pro_get_event_option($eventID, RSVP_PRO_OPTION_PASSCODE) == "Y") {
              $maxLength = 6;
              if(rsvp_pro_get_event_option($eventID, RSVP_PRO_OPTION_PASSWORD_LENGTH) > 0) {
                $maxLength = rsvp_pro_get_event_option($eventID, RSVP_PRO_OPTION_PASSWORD_LENGTH);
              }
  					?>
  						<tr>
  							<th scope="row"><label for="passcode"><?php _e("Passcode", "rsvp-pro-plugin"); ?>:</label></th>
  							<td>
                  <input type="text" name="passcode" id="passcode" size="30" value="<?php echo htmlentities($passcode); ?>" maxlength="<?php echo $maxLength; ?>" />
                </td>
  						</tr>
  					<?php	
  					}					
  					?>
  					<tr>
  						<th scope="row">
                <label for="rsvpStatus"><?php _e("RSVP Status", "rsvp-pro-plugin"); ?></label>
              </th>
  						<td>
  							<select name="rsvpStatus" id="rsvpStatus" size="1">
  								<option value="NoResponse" <?php
  									echo (($rsvpStatus == "NoResponse") ? " selected=\"selected\"" : "");
  								?>><?php _e("No Response", "rsvp-pro-plugin"); ?></option>
  								<option value="Yes" <?php
  									echo (($rsvpStatus == "Yes") ? " selected=\"selected\"" : "");
  								?>><?php _e("Yes", "rsvp-pro-plugin"); ?></option>									
  								<option value="No" <?php
  									echo (($rsvpStatus == "No") ? " selected=\"selected\"" : "");
  								?>><?php _e("No", "rsvp-pro-plugin"); ?></option>
                  <?php
                  if(rsvp_pro_waitlist_enabled($rsvpId)): 
                  ?>
                      <option value="Waitlist" 
                      <?php echo (($rsvpStatus == "Waitlist") ? " selected=\"selected\"": ""); ?>><?php _e("Waitlist", "rsvp-pro-plugin"); ?></option>
                  <?php endif; ?>
  							</select>
  						</td>
  					</tr>
            <?php if(!$isSubEvent): ?>
  					<tr>
  						<th scope="row"><label for="numGuests"><?php _e("Number of Guests Allowed", "rsvp-pro-plugin"); ?>:</label></th>
  						<td>
                <input type="text" name="numGuests" id="numGuests" size="30" value="<?php echo htmlentities($numGuests); ?>" />
              </td>
  					</tr>
            <?php endif; ?>
            <?php if(!$isSubEvent): ?>
  					<tr>
  						<th scope="row"><label for="personalGreeting"><?php _e("Custom Message", "rsvp-pro-plugin"); ?>:</label></th>
  						<td>
                <textarea name="personalGreeting" id="personalGreeting" rows="5" cols="40"><?php echo htmlentities($personalGreeting); ?></textarea>
              </td>
  					</tr>
  					<tr>
  						<th scope="row"><label for="note"><?php _e("Note", "rsvp-pro-plugin"); ?>:</label></th>
  						<td>
                <textarea name="note" id="note" rows="5" cols="40"><?php echo htmlentities($note); ?></textarea>
              </td>
  					</tr>
  					<tr>
  						<th scope="row"><?php _e("Associated Attendees", "rsvp-pro-plugin"); ?>:</th>
  						<td>
  							<select name="associatedAttendees[]" id="associatedAttendeesSelect" multiple="multiple" size="5">
  								<?php
  									$attendees = $wpdb->get_results($wpdb->prepare("SELECT id, firstName, lastName 
                                                                    FROM ".PRO_ATTENDEES_TABLE." 
                                                                    WHERE rsvpEventID = %d ORDER BY lastName, firstName", $eventID));
  									foreach($attendees as $a) {
  										if($a->id != $attendeeID) {
  								?>
  											<option value="<?php echo $a->id; ?>" 
  															<?php echo ((in_array($a->id, $associatedAttendees)) ? "selected=\"selected\"" : ""); ?>><?php echo htmlentities(stripslashes($a->firstName)." ".stripslashes($a->lastName)); ?></option>
  								<?php
  										}
  									}
  								?>
  							</select>
  						</td>
  					</tr>
            <?php endif; ?>
  				<?php
  				if(($attendee != null) && ($attendee->id > 0)) {
  					$sql = "SELECT question, answer, ans.id AS answerID, q.ID AS questionID, questionType, grouping FROM ".PRO_QUESTIONS_TABLE." q 
  						LEFT JOIN ".PRO_ATTENDEE_ANSWERS." ans ON ans.questionID = q.id AND attendeeID = %d
              INNER JOIN ".PRO_QUESTION_TYPE_TABLE." qt ON qt.ID = q.questionTypeID 
  						WHERE q.rsvpEventID = %d
  						ORDER BY q.sortOrder";
  					$aRs = $wpdb->get_results($wpdb->prepare($sql, $attendee->id, $eventID));
  					if(count($aRs) > 0) {
  				?>
          <tr>
            <th><?php _e("Custom Questions", "rsvp-pro-plugin"); ?></th>
          </tr>
  				<?php
  						foreach($aRs as $a) {
  				?>
  							<tr>
  								<th scope="row"><?php echo stripslashes($a->question); ?></th>
  								<td>
                    <?php
            				if($a->questionType == QT_MULTI) {
            					$oldAnswers = explode("||", stripslashes($a->answer));
					
            					$possibleAnswers = $wpdb->get_results($wpdb->prepare("SELECT id, answer FROM ".PRO_QUESTION_ANSWERS_TABLE." WHERE questionID = %d", $a->questionID));
            					if(count($possibleAnswers) > 0) {
                        ?>
                        <table>
                        <?php
            						foreach($possibleAnswers as $pa) {
                          ?>
                            <tr>
                              <th><label for="question<?php echo $a->questionID.$pa->id; ?>"><?php echo stripslashes($pa->answer); ?></label></th>
                              <td><input type="checkbox" name="question<?php echo $a->questionID; ?>[]" id="question<?php echo $a->questionID.$pa->id; ?>" value="<?php echo $pa->id; ?>" 
            							  <?php echo ((in_array(stripslashes($pa->answer), $oldAnswers)) ? " checked=\"checked\"" : "") ?> /></td>
                            </tr>
                          <?php
            						}
                        ?>
                        </table>
                        <?php
            					}
            				} else if ($a->questionType == QT_DROP) {
                      ?>
            					<select name="question<?php echo $a->questionID; ?>" size="1">
            						<option value="">--</option>
                      <?php   
            					$possibleAnswers = $wpdb->get_results($wpdb->prepare("SELECT id, answer FROM ".PRO_QUESTION_ANSWERS_TABLE." WHERE questionID = %d", $a->questionID));
            					if(count($possibleAnswers) > 0) {
            						foreach($possibleAnswers as $pa) {
                        ?>
            							<option value="<?php echo $pa->id?>" <?php echo ((stripslashes($pa->answer) == stripslashes($a->answer)) ? " selected=\"selected\"" : "") ?>><?php echo stripslashes($pa->answer); ?></option>
                        <?php
            						}
            					}
                      ?>
                      </select>
                    <?php
            				} else if ($a->questionType == QT_LONG) {
                    ?>
                      <textarea name="question<?php echo $a->questionID?>" rows="5" cols="35"><?php echo htmlspecialchars($a->answer)?></textarea>
                    <?php
            				} else if ($a->questionType == QT_RADIO) {
            					$possibleAnswers = $wpdb->get_results($wpdb->prepare("SELECT id, answer FROM ".PRO_QUESTION_ANSWERS_TABLE." WHERE questionID = %d", $a->questionID));
            					if(count($possibleAnswers) > 0) {
                        ?>
                        <table>
                        <?php
            						foreach($possibleAnswers as $pa) {
                        ?>
                          <tr>
                            <th scope="row">
                              <label for="question<?php echo $a->questionID.$pa->id; ?>"><?php echo stripslashes($pa->answer) ?></label>
                            </th>
                            <td>
                              <input type="radio" name="question<?php echo $a->questionID ?>" id="question<?php echo $a->questionID.$pa->id; ?>" value="<?php echo $pa->id ?>" <?php echo ((stripslashes($pa->answer) == stripslashes($a->answer)) ? " checked=\"checked\"" : "") ?> /> 
                            </td>
                          </tr>
                        <?php
            						}
                        ?>
                        </table>
                        <?php
            					}
            				} else {
                    ?>
            					<input type="text" name="question<?php echo $a->questionID ?>" value="<?php echo htmlspecialchars($a->answer) ?>"  />
                    <?php
            				}?>
                    <?php
                    if($a->grouping == RSVP_PRO_QG_MULTI) {
                    ?>
                      <span class="description">Question asked once per associated attendees</span> 
                    <?php
                    }
                    ?>
                  </td>
  							</tr>
  				<?php
  						}
  				?>
  				<?php
  					}
  				}
  				?>
          </tbody>
				</table>
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e('Save'); ?>" />
				</p>
			</form>
<?php
		}
	}
	
	function rsvp_pro_admin_questions($eventID) {
		global $wpdb;
    
    if($eventID < 0) {
      rsvp_pro_admin_events();
      die;
    }

    if(!rsvp_pro_admin_user_has_access_to_settings($eventID)) {
      rsvp_pro_admin_eventList();
      return;
    }
		
		if((count($_POST) > 0) && ($_POST['rsvp-bulk-action'] == "delete") && (is_array($_POST['q']) && (count($_POST['q']) > 0))) {
			foreach($_POST['q'] as $q) {
				if(is_numeric($q) && ($q > 0)) {
					$wpdb->query($wpdb->prepare("DELETE FROM ".PRO_QUESTIONS_TABLE." WHERE id = %d AND rsvpEventID = %d", $q, $eventID));
          $wpdb->query($wpdb->prepare("DELETE FROM ".PRO_QUESTION_ANSWERS_TABLE." WHERE questionID = %d", $q));
					$wpdb->query($wpdb->prepare("DELETE FROM ".PRO_ATTENDEE_ANSWERS." WHERE questionID = %d", $q));
				}
			}
		} else if((count($_POST) > 0) && ($_POST['rsvp-bulk-action'] == "saveSortOrder")) {
			$sql = "SELECT id FROM ".PRO_QUESTIONS_TABLE;
			$sortQs = $wpdb->get_results($sql);
			foreach($sortQs as $q) {
				if(is_numeric($_POST['sortOrder'.$q->id]) && ($_POST['sortOrder'.$q->id] >= 0)) {
					$wpdb->update(PRO_QUESTIONS_TABLE, 
												array("sortOrder" => $_POST['sortOrder'.$q->id]), 
												array("id" => $q->id), 
												array("%d"), 
												array("%d"));
				}
			}
		}
		
		$sql = "SELECT id, question, sortOrder FROM ".PRO_QUESTIONS_TABLE." WHERE rsvpEventID = %d ORDER BY sortOrder ASC";
		$customQs = $wpdb->get_results($wpdb->prepare($sql, $eventID));
	?>
		<script type="text/javascript" language="javascript">
			jQuery(document).ready(function() {
				jQuery("#cb").click(function() {
					if(jQuery("#cb").attr("checked")) {
						jQuery("input[name='q[]']").attr("checked", "checked");
					} else {
						jQuery("input[name='q[]']").removeAttr("checked");
					}
				});
				
				jQuery("#customQuestions").tableDnD({
					onDrop: function(table, row) {
						var rows = table.tBodies[0].rows;
            for (var i=0; i<rows.length; i++) {
                jQuery("#sortOrder" + rows[i].id).val(i);
            }
	        	
					}
				});
			});
		</script>
		<div class="wrap">	
			<div id="icon-edit" class="icon32"><br /></div>	
			<h2><?php echo get_event_name($eventID); ?> <?php _e("Custom questions", "rsvp-pro-plugin"); ?></h2>
			<form method="post" id="rsvp-form">
				<input type="hidden" id="rsvp-bulk-action" name="rsvp-bulk-action" />
				<div class="tablenav">
					<div class="alignleft actions">
						<select id="rsvp-action-top" name="rsvpbulkaction">
							<option value="" selected="selected"><?php _e('Bulk Actions', 'rsvp-pro-plugin'); ?></option>
							<option value="delete"><?php _e('Delete', 'rsvp-pro-plugin'); ?></option>
						</select>
						<input type="submit" value="<?php _e('Apply', 'rsvp-pro-plugin'); ?>" name="doaction" id="doaction" class="button-secondary action" onclick="document.getElementById('rsvp-bulk-action').value = document.getElementById('rsvp-action-top').value;" />
						<input type="submit" value="<?php _e('Save Sort Order', 'rsvp-pro-plugin'); ?>" name="saveSortButton" id="saveSortButton" class="button-secondary action" onclick="document.getElementById('rsvp-bulk-action').value = 'saveSortOrder';" />
            <a href="<?php echo admin_url("admin.php?page=rsvp-pro-top-level&action=modify_custom_question&eventID=$eventID"); ?>">Add Custom Question</a>
					</div>
					<div class="clear"></div>
				</div>
			<table class="widefat post fixed" cellspacing="0" id="customQuestions">
				<thead>
					<tr>
						<th scope="col" class="manage-column column-cb check-column" style=""><input type="checkbox" id="cb" /></th>
						<th scope="col" id="questionCol" class="manage-column column-title" width="50"><?php _e("ID", "rsvp-pro-plugin"); ?></th>			
            <th scope="col" id="questionCol" class="manage-column column-title"><?php _e("Question", "rsvp-pro-plugin"); ?></th> 
					</tr>
				</thead>
        <tbody>
          <?php
          $i = 0;
          foreach($customQs as $q) {
          ?>
            <tr class="<?php echo (($i % 2 == 0) ? "alternate" : ""); ?> author-self" id="<?php echo $q->id; ?>">
              <th scope="row" class="check-column"><input type="checkbox" name="q[]" value="<?php echo $q->id; ?>" /></th>            
              <td><?php echo $q->id; ?></td>
              <td>
                <a href="<?php echo admin_url("admin.php?page=rsvp-pro-top-level&action=modify_custom_question&id=$q->id&eventID=$eventID"); ?>"><?php echo htmlentities(stripslashes($q->question)); ?></a>
                <input type="hidden" name="sortOrder<?php echo $q->id; ?>" id="sortOrder<?php echo $q->id; ?>" value="<?php echo $q->sortOrder; ?>" />
              </td>
            </tr>
          <?php
            $i++;
          }
        ?>  
        </tbody>
			</table>
			</form>
		</div>
	<?php
	}

  function rsvp_pro_get_question_with_answer_type_ids() {
    global $wpdb;

    $ids = array();
    $sql = "SELECT id FROM ".PRO_QUESTION_TYPE_TABLE." 
        WHERE questionType IN ('".QT_MULTI."', '".QT_DROP."', '".QT_RADIO."')";
    $results = $wpdb->get_results($sql);
    foreach($results as $r) {
      $ids[] = (int)$r->id;
    }

    return $ids;
  }
	
	function rsvp_pro_admin_custom_question($eventID) {
		global $wpdb;

    if(!rsvp_pro_admin_user_has_access_to_settings($eventID)) {
      rsvp_pro_admin_eventList();
      return;
    }

		$answerQuestionTypes = rsvp_pro_get_question_with_answer_type_ids();
		$isSubEvent = false;
    $parentEventID = $wpdb->get_var($wpdb->prepare("SELECT parentEventID FROM ".PRO_EVENT_TABLE." WHERE id = %d", $eventID));
    if($parentEventID > 0) {
      $isSubEvent = true;
    }
    
		$radioQuestionType = $wpdb->get_var("SELECT id FROM ".PRO_QUESTION_TYPE_TABLE." WHERE questionType = 'radio'");
		if($radioQuestionType == 0) {
			$wpdb->insert(PRO_QUESTION_TYPE_TABLE, array("questionType" => "radio", "friendlyName" => "Radio"), array('%s', '%s'));
		}
		$hiddeQuestionType = $wpdb->get_var("SELECT id FROM ".PRO_QUESTION_TYPE_TABLE." WHERE questionType = 'hidden'");
		if($hiddeQuestionType == 0) {
			$wpdb->insert(PRO_QUESTION_TYPE_TABLE, array("questionType" => "hidden", "friendlyName" => "Admin Only"), array('%s', '%s'));
		}
		$readonlyQuestionType = $wpdb->get_var("SELECT id FROM ".PRO_QUESTION_TYPE_TABLE." WHERE questionType = 'readonly'");
		if($readonlyQuestionType == 0) {
			$wpdb->insert(PRO_QUESTION_TYPE_TABLE, array("questionType" => "readonly", "friendlyName" => "Read Only"), array('%s', '%s'));
		}
    $isRequired = ($_POST['questionRequired'] == "Y") ? "Y" : "N";
		
		if((count($_POST) > 0) && !empty($_POST['question']) && is_numeric($_POST['questionTypeID'])) {
			check_admin_referer('rsvp_add_custom_question');
			if(isset($_POST['questionID']) && is_numeric($_POST['questionID'])) {
				$wpdb->update(PRO_QUESTIONS_TABLE, 
											array("question" => trim($_POST['question']), 
											      "questionTypeID" => trim($_POST['questionTypeID']), 
														"permissionLevel" => ((trim($_POST['permissionLevel']) == "private") ? "private" : "public"), 
                            "grouping" => (($_POST['questionGrouping'] == RSVP_PRO_QG_MULTI) ? RSVP_PRO_QG_MULTI : RSVP_PRO_QG_SINGLE), 
                            "required" => $isRequired), 
											array("id" => $_POST['questionID']), 
											array("%s", "%d", "%s", "%s", "%s"), 
											array("%d"));
				$questionId = $_POST['questionID'];
				
				$answers = $wpdb->get_results($wpdb->prepare("SELECT id FROM ".PRO_QUESTION_ANSWERS_TABLE." WHERE questionID = %d", $questionId));
				if(count($answers) > 0) {
					foreach($answers as $a) {
						if(isset($_POST['deleteAnswer'.$a->id]) && (strToUpper($_POST['deleteAnswer'.$a->id]) == "Y")) {
							$wpdb->query($wpdb->prepare("DELETE FROM ".PRO_QUESTION_ANSWERS_TABLE." WHERE id = %d", $a->id));
						} elseif(isset($_POST['answer'.$a->id]) && ($_POST['answer'.$a->id] != "")) {
							$wpdb->update(PRO_QUESTION_ANSWERS_TABLE, 
													  array("answer" => trim($_POST['answer'.$a->id]), 
                                  "defaultAnswer" => (($_POST['defaultAnswer'.$a->id] == "Y") ? "Y" : "N")), 
													  array("id"=>$a->id), 
													  array("%s", "%s"), 
													  array("%d"));
						}
					}
				}
			} else {
				$wpdb->insert(PRO_QUESTIONS_TABLE, array("question" => trim($_POST['question']), 
				                                     "questionTypeID" => trim($_POST['questionTypeID']), 
																						 "permissionLevel" => ((trim($_POST['permissionLevel']) == "private") ? "private" : "public"), 
                                             "grouping" => (($_POST['questionGrouping'] == RSVP_PRO_QG_MULTI) ? RSVP_PRO_QG_MULTI : RSVP_PRO_QG_SINGLE), 
                                             "required" => $isRequired, 
                                             "rsvpEventID" => $eventID),  
				                               array('%s', '%d', '%s', '%s', '%s', '%d'));
				$questionId = $wpdb->insert_id;
			}
			
			if(isset($_POST['numNewAnswers']) && is_numeric($_POST['numNewAnswers']) && 
			   in_array($_POST['questionTypeID'], $answerQuestionTypes)) {
				for($i = 0; $i < $_POST['numNewAnswers']; $i++) {
					if(isset($_POST['newAnswer'.$i]) && ($_POST['newAnswer'.$i] != "")) {
						$wpdb->insert(PRO_QUESTION_ANSWERS_TABLE, array("questionID"=>$questionId, 
                                                            "answer"=>$_POST['newAnswer'.$i], 
                                                            "defaultAnswer" => (($_POST['newDefaultAnswer'.$i] == "Y") ? "Y" : "N")));
					}
				}
			}
			
			if(strToLower(trim($_POST['permissionLevel'])) == "private") {
				$wpdb->query($wpdb->prepare("DELETE FROM ".PRO_QUESTION_ATTENDEES_TABLE." WHERE questionID = %d", $questionId));
				if(isset($_POST['attendees']) && is_array($_POST['attendees'])) {
					foreach($_POST['attendees'] as $aid) {
						if(is_numeric($aid) && ($aid > 0)) {
							$wpdb->insert(PRO_QUESTION_ATTENDEES_TABLE, array("attendeeID"=>$aid, "questionID"=>$questionId), array("%d", "%d"));
						}
					}
				}
			}
		?>
			<p><?php _e("Custom Question saved", "rsvp-pro-plugin"); ?></p>
			<p>
				<a href="<?php echo admin_url('admin.php?page=rsvp-pro-top-level&action=custom_questions&eventID='.$eventID); ?>"><?php _e("Continue to Question List", "rsvp-pro-plugin"); ?></a> | 
				<a href="<?php  echo admin_url("admin.php?page=rsvp-pro-top-level&action=modify_custom_question&eventID=$eventID"); ?>"><?php _e("Add another Question", "rsvp-pro-plugin"); ?></a> 
			</p>
		<?php
		} else {
			$questionTypeId = 0;
			$question = "";
			$isNew = true;
			$questionId = 0;
			$permissionLevel = "public";
			$savedAttendees = array();
      $grouping = RSVP_PRO_QG_SINGLE;
      $required = "N";
      
			if(isset($_GET['id']) && is_numeric($_GET['id'])) {
				$qRs = $wpdb->get_results($wpdb->prepare("SELECT id, question, questionTypeID, permissionLevel, grouping, required FROM ".PRO_QUESTIONS_TABLE." WHERE id = %d", $_GET['id']));
				if(count($qRs) > 0) {
					$isNew = false;
					$questionId = $qRs[0]->id;
					$question = stripslashes($qRs[0]->question);
					$permissionLevel = stripslashes($qRs[0]->permissionLevel);
					$questionTypeId = $qRs[0]->questionTypeID;
          $grouping = stripslashes($qRs[0]->grouping);
          $required = ($qRs[0]->required == "Y") ? "Y" : "N";
					
					if($permissionLevel == "private") {
						$aRs = $wpdb->get_results($wpdb->prepare("SELECT attendeeID FROM ".PRO_QUESTION_ATTENDEES_TABLE." WHERE questionID = %d", $questionId));
						if(count($aRs) > 0) {
							foreach($aRs as $a) {
								$savedAttendees[] = $a->attendeeID;
							}
						}
					}
				}
			} 
			
			$sql = "SELECT id, questionType, friendlyName FROM ".PRO_QUESTION_TYPE_TABLE;
			$questionTypes = $wpdb->get_results($sql);
			?>
				<script type="text/javascript">
          var questionTypeId = [<?php 
            foreach($answerQuestionTypes as $aqt) {
              echo "\"".$aqt."\",";
            }
          ?>];

					function addAnswer(counterElement) {
						var currAnswer = jQuery("#numNewAnswers").val();
						if(isNaN(currAnswer)) {
							currAnswer = 0;
						}
				
						var s = "<tr>\r\n"+ 
							"<td align=\"right\" width=\"75\"><label for=\"newAnswer" + currAnswer + "\"><?php _e("Answer", "rsvp-pro-plugin"); ?>:</label></td>\r\n" + 
							"<td><input type=\"text\" name=\"newAnswer" + currAnswer + "\" id=\"newAnswer" + currAnswer + "\" size=\"40\" /><br />\r\n" + 
              "<label><input type=\"checkbox\" name=\"newDefaultAnswer" + currAnswer + "\" value=\"Y\" /><?php _e("Default Answer", "rsvp-pro-plugin"); ?></label>" + 
              "</td>\r\n" + 
						"</tr>\r\n";
						jQuery("#answerContainer").append(s);
						currAnswer++;
						jQuery("#numNewAnswers").val(currAnswer);
						return false;
					}
				
					jQuery(document).ready(function() {
						
						<?php
						if($isNew || !in_array($questionTypeId, $answerQuestionTypes)) {
						 	echo 'jQuery("#answerContainer").hide();';
						}
						
						if($isNew || ($permissionLevel == "public")) {
						?>
							jQuery("#attendeesArea").hide();
						<?php
						}
						?>
						jQuery("#questionType").change(function() {
							var selectedValue = jQuery("#questionType").val();
							if(questionTypeId.indexOf(selectedValue) != -1) {
								jQuery("#answerContainer").show();
							} else {
								jQuery("#answerContainer").hide();
							}
						})
						
						jQuery("#permissionLevel").change(function() {
							if(jQuery("#permissionLevel").val() != "public") {
								jQuery("#attendeesArea").show();
							} else {
								jQuery("#attendeesArea").hide();
							}
						})
					});
				</script>
        <h3><?php echo get_event_name($eventID); ?> Question</h3>
				<form name="contact" method="post">
					<input type="hidden" name="numNewAnswers" id="numNewAnswers" value="0" />
          <?php
          if($questionId > 0) {
          ?>
            <input type="hidden" name="questionID" value="<?php echo $questionId; ?>" />
          <?php
          }
          ?>
					<?php wp_nonce_field('rsvp_add_custom_question'); ?>
					<p class="submit">
						<input type="submit" class="button-primary" value="<?php _e("Save", "rsvp-pro-plugin"); ?>" />
					</p>
					<table id="customQuestions" class="form-table">
						<tr valign="top">
							<th scope="row"><label for="questionGrouping"><?php _e("Question Grouping", "rsvp-pro-plugin"); ?>:</label></th>
							<td align="left"><select name="questionGrouping" id="questionGrouping" size="1">
                <option value="<?php echo RSVP_PRO_QG_SINGLE; ?>" <?php echo ($grouping == RSVP_PRO_QG_SINGLE) ? "selected=\"selected\"": ""; ?>><?php _e("Question asked to everyone", "rsvp-pro-plugin"); ?></option>
                <option value="<?php echo RSVP_PRO_QG_MULTI; ?>" <?php echo ($grouping == RSVP_PRO_QG_MULTI) ? "selected=\"selected\"": ""; ?>><?php _e("Question asked once per associated attendees", "rsvp-pro-plugin"); ?></option>
							</select>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><label for="questionType"><?php _e("Question Type", "rsvp-pro-plugin"); ?>:</label></th>
							<td align="left"><select name="questionTypeID" id="questionType" size="1">
								<?php
									foreach($questionTypes as $qt) {
										echo "<option value=\"".$qt->id."\" ".(($questionTypeId == $qt->id) ? " selected=\"selected\"" : "").">".$qt->friendlyName."</option>\r\n";
									}
								?>
							</select>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><label for="question"><?php _e("Question", "rsvp-pro-plugin"); ?>:</label></th>
							<td align="left"><input type="text" name="question" id="question" size="40" value="<?php echo htmlentities($question); ?>" /></td>
						</tr>
						<tr valign="top">
							<th scope="row"><label for="questionRequired"><?php _e("Is Required?", "rsvp-pro-plugin"); ?>:</label></th>
							<td align="left"><input type="checkbox" name="questionRequired" id="questionRequired" value="Y" <?php echo (($required == "Y") ? "checked=\"checked\"" : ""); ?> /></td>
						</tr>
						<tr>
							<th scope="row"><label for="permissionLevel"><?php _e("Question Permission Level", "rsvp-pro-plugin"); ?>:</label></th>
							<td align="left"><select name="permissionLevel" id="permissionLevel" size="1">
								<option value="public" <?php echo ($permissionLevel == "public") ? " selected=\"selected\"" : ""; ?>><?php _e("Public", "rsvp-pro-plugin"); ?></option>
								<option value="private" <?php echo ($permissionLevel == "private") ? " selected=\"selected\"" : ""; ?>><?php _e("Private", "rsvp-pro-plugin"); ?></option>
							</select></td>
						</tr>
						<tr>
							<td colspan="2">
								<table cellpadding="0" cellspacing="0" border="0" id="answerContainer">
									<tr>
										<th><?php _e("Answers", "rsvp-pro-plugin"); ?></th>
										<th align="right"><a href="#" onclick="return addAnswer();"><?php _e("Add new Answer", "rsvp-pro-plugin"); ?></a></th>
									</tr>
									<?php
									if(!$isNew) {
										$aRs = $wpdb->get_results($wpdb->prepare("SELECT id, answer, defaultAnswer FROM ".PRO_QUESTION_ANSWERS_TABLE." WHERE questionID = %d", $questionId));
										if(count($aRs) > 0) {
											foreach($aRs as $answer) {
										?>
												<tr>
													<td width="75" align="right"><label for="answer<?php echo $answer->id; ?>"><?php _e("Answer", "rsvp-pro-plugin"); ?>:</label></td>
													<td><input type="text" name="answer<?php echo $answer->id; ?>" id="answer<?php echo $answer->id; ?>" size="40" value="<?php echo esc_attr_e(stripslashes($answer->answer)); ?>" />
                              <br />
													    <input type="checkbox" name="deleteAnswer<?php echo $answer->id; ?>" id="deleteAnswer<?php echo $answer->id; ?>" value="Y" /><label for="deleteAnswer<?php echo $answer->id; ?>"><?php _e("Delete", "rsvp-pro-plugin"); ?></label>
                              <br />
                              <label><input type="checkbox" name="defaultAnswer<?php echo $answer->id; ?>" id="defaultAnswer<?php echo $answer->id; ?>" value="Y" <?php echo (($answer->defaultAnswer == "Y") ? "checked=\"checked\"": ""); ?> /><?php _e("Default Answer", "rsvp-pro-plugin"); ?></label>
                          </td>
												</tr>
										<?php
											}
										}
									}
									?>
								</table>
							</td>
						</tr>
						<tr id="attendeesArea">
							<th scope="row"><label for="attendees"><?php _e("Attendees allowed to answer this question", "rsvp-pro-plugin"); ?>:</label></th>
							<td>
								<select name="attendees[]" id="attendeesQuestionSelect" style="height:75px;" multiple="multiple">
								<?php
                  $sql = $wpdb->prepare("SELECT id, firstName, lastName FROM ".PRO_ATTENDEES_TABLE." WHERE rsvpEventID = %d ORDER BY lastName, firstName", $eventID);
                  if($isSubEvent) {
                    $sql = $wpdb->prepare("SELECT id, firstName, lastName FROM ".PRO_ATTENDEES_TABLE." WHERE rsvpEventID = %d ORDER BY lastName, firstName", $parentEventID);
                  }
									$attendees = $wpdb->get_results($sql);
									foreach($attendees as $a) {
								?>
									<option value="<?php echo $a->id; ?>" 
													<?php echo ((in_array($a->id, $savedAttendees)) ? " selected=\"selected\"" : ""); ?>><?php echo htmlentities(stripslashes($a->firstName)." ".stripslashes($a->lastName)); ?></option>
								<?php
									}
								?>
								</select>
							</td>
						</tr>
					</table>
				</form>
		<?php
		}
	}
	
  function rsvp_pro_admin_import_from_free($eventID) {
    global $wpdb;
    
    $sql = "SELECT eventName FROM ".PRO_EVENT_TABLE." WHERE id = %d";
    $events = $wpdb->get_results($wpdb->prepare($sql, $eventID));
    if(count($events) <= 0) {
      rsvp_pro_admin_eventList();
      return;
    }

    if(!rsvp_pro_admin_user_has_access_to_settings($eventID)) {
      rsvp_pro_admin_eventList();
      return;
    }
    
		if(count($_POST) > 0) {
			check_admin_referer('rsvp_import_from_free');      
      
      // we will use this array for when we want to move data over from the old questions 
      // but don't want to look up the new and old question IDs. Just is an "easy" way 
      // to keep a relationship between the old and new records.
      $questionMapping = array(); 
      
      // Import rsvpCustomQuestions 
      $qTable = $wpdb->prefix."rsvpCustomQuestions";
      $sql = "SELECT id, permissionLevel, question, questionTypeID, sortOrder FROM $qTable";
      $customQs = $wpdb->get_results($sql);
      if(count($customQs) > 0) {
        foreach($customQs as $cq) {
  				$wpdb->insert(PRO_QUESTIONS_TABLE, array("question" => trim($cq->question), 
  				                                     "questionTypeID" => trim($cq->questionTypeID), 
  																						 "permissionLevel" => ((trim($cq->permissionLevel) == "private") ? "private" : "public"), 
                                               "sortOrder" => $cq->sortOrder, 
                                               "rsvpEventID" => $eventID),  
  				                               array('%s', '%d', '%s', '%d', '%d'));
  				$questionId = $wpdb->insert_id;
          $questionMapping[$cq->id] = $questionId;
          
          // Import rsvpCustomQuestionAnswers 
          $qaTable = $wpdb->prefix."rsvpCustomQuestionAnswers";
          $sql = "SELECT answer FROM $qaTable WHERE questionID = %d";
          $customQAs = $wpdb->get_results($wpdb->prepare($sql, $cq->id));
          if(count($customQAs)) {
            foreach($customQAs as $qa) {
              $wpdb->insert(PRO_QUESTION_ANSWERS_TABLE, array("questionID"=>$questionId, "answer"=>$qa->answer));
            }
          }
        }
      }
      
      // Similar to the $questionMapping array used above.
      $attendeeMapping = array();
      
      // Import attendees
      $attendeeTable = $wpdb->prefix."attendees";
      $sql = "SELECT firstName, lastName, email, personalGreeting, passcode, additionalAttendee, id, note, rsvpDate, rsvpStatus FROM $attendeeTable";
      $attendees = $wpdb->get_results($sql);
      if(count($attendees) > 0) {
        foreach($attendees as $a) {
  				$wpdb->insert(PRO_ATTENDEES_TABLE, array("firstName" 		=> $a->firstName, 
  																						 "lastName" 				=> $a->lastName,
                                               "email"            => $a->email, 
  																						 "personalGreeting" => $a->personalGreeting, 
                                               "passcode"         => $a->passcode, 
                                               "additionalAttendee" => $a->additionalAttendee, 
                                               "note" => $a->note, 
                                               "rsvpDate" => $a->rsvpDate, 
                                               "rsvpStatus" => $a->rsvpStatus, 
                                               "rsvpEventID"     => $eventID), 
  																			 array('%s', '%s', '%s', '%s', '%s', "%s", "%s", '%s', '%s', '%d'));
          $attendeeId = $wpdb->insert_id;
          $attendeeMapping[$a->id] = $attendeeId;
          
          // Import rsvpCustomQuestionAttendees
          $cqAttendees = $wpdb->prefix."rsvpCustomQuestionAttendees";
          $sql = "SELECT attendeeID, questionID FROM $cqAttendees WHERE attendeeID = %d";
          $customQAttendees = $wpdb->get_results($wpdb->prepare($sql, $a->id));
          if(count($customQAttendees) > 0) {
            foreach($customQAttendees as $cqa) {
              $wpdb->insert(PRO_QUESTION_ATTENDEES_TABLE, array("attendeeID" => $attendeeId, 
                                                                "questionID" => $questionMapping[$cqa->questionID]), 
                                                          array("%d", "%d"));
            }
          }
          
          // Import attendeeAnswers  
          $aaTable = $wpdb->prefix."attendeeAnswers";
          $sql = "SELECT answer, questionID FROM $aaTable WHERE attendeeID = %d";
          $aAnswers = $wpdb->get_results($wpdb->prepare($sql, $a->id));
          if(count($aAnswers) > 0) {
            foreach($aAnswers as $ans) {
              $wpdb->insert(PRO_ATTENDEE_ANSWERS, array("attendeeID" => $attendeeId, 
                                                        "questionID" => $questionMapping[$ans->questionID], 
                                                        "answer" => $ans->answer), 
                                                  array("%d", "%d", "%s"));
            }
          }
        }
      }
      
      // Finally import associatedAttendees 
      $aaTable = $wpdb->prefix."associatedAttendees";
      $sql = "SELECT associatedAttendeeID, attendeeID FROM $aaTable";
      $assocAttendee = $wpdb->get_results($sql);
      if(count($assocAttendee) > 0) {
        foreach($assocAttendee as $aa) {
          $wpdb->insert(PRO_ASSOCIATED_ATTENDEES_TABLE, array("associatedAttendeeID" => $attendeeMapping[$aa->associatedAttendeeID], 
                                                              "attendeeID" => $attendeeMapping[$aa->attendeeID]), 
                                                        array("%d", "%d"));
        }
      }
    ?>
        <p><?php _e("Import complete", "rsvp-pro-plugin"); ?>.</p>
        
        <p>
				<a href="<?php echo admin_url('admin.php?page=rsvp-pro-top-level&action=attendees&eventID='.$eventID); ?>"><?php _e("Continue to Attendee List", "rsvp-pro-plugin"); ?></a>
        </p>
    <?php  
    } else {
    ?>
			<form name="importFromFree" method="post">
				<?php wp_nonce_field('rsvp_import_from_free'); ?>
        <p><?php _e("Import from the free version of the RSVP plugin to event", "rsvp-pro-plugin"); ?> <?php echo $events[0]->eventName; ?>?</p>
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e('Import!'); ?>" />
				</p>
      </form>
    <?php
    } // if(count($_POST) > 0)...
  }

  function rsvp_pro_global_options() {
  ?>
    <div class="wrap">
      <h1><?php echo __("RSVP Pro General Settings", 'rsvp-pro-plugin'); ?></h1>
      <?php
        settings_errors();
      ?>
      <form method="post" action="options.php">
        <?php wp_nonce_field('rsvp_pro_global_settings'); ?>
        <?php settings_fields( 'rsvp-pro-option-group' ); ?>
        <table class="form-table">
          <tbody>
            <tr>
              <th scope="row">
                <label for="<?php echo RSVP_PRO_GLOBAL_OPTION_DELETE_TABLES; ?>"><?php _e("Delete all data on uninstall:", "rsvp-pro-plugin"); ?></label>
              </th>
              <td>
                <input type="checkbox" name="<?php echo RSVP_PRO_GLOBAL_OPTION_DELETE_TABLES; ?>" id="<?php echo RSVP_PRO_GLOBAL_OPTION_DELETE_TABLES; ?>" 
              value="Y" <?php echo ((get_option(RSVP_PRO_GLOBAL_OPTION_DELETE_TABLES) == "Y") ? " checked=\"checked\"" : ""); ?> />
              </td>
            </tr>
            <tr>
              <th scope="row">
                <label for="<?php echo RSVP_PRO_GLOBAL_OPTION_STYLES; ?>"><?php echo __("Custom Styling:", 'rsvp-plugin'); ?></label>
              </th>
              <td>
                <textarea name="<?php echo RSVP_PRO_GLOBAL_OPTION_STYLES; ?>" id="<?php echo RSVP_PRO_GLOBAL_OPTION_STYLES; ?>" rows="20" cols="70" class="large-text code"><?php echo esc_html(get_option(RSVP_PRO_GLOBAL_OPTION_STYLES)); ?></textarea>
              </td>
            </tr>
          </tbody>
        </table>
        <input type="hidden" name="action" value="update" />
        <p class="submit">
          <input type="submit" class="button-primary" value="<?php echo __('Save Changes', 'rsvp-plugin'); ?>" />
        </p>
      </form>
    </div>
  <?php
  }
  
	function rsvp_pro_modify_menu() {
		$page = add_menu_page("RSVP Pro", 
									"RSVP Pro", 
									"publish_posts", 
									"rsvp-pro-top-level", 
									"rsvp_pro_admin_events",
                  plugins_url("images/rsvp_pro_icon_16_x_16.png", RSVP_PRO_PLUGIN_FILE));
    
    $page = add_submenu_page("rsvp-pro-top-level", 
    										 __("Event Management", "rsvp-pro-plugin"),
    										 __("Add event", "rsvp-pro-plugin"),
    										 "publish_posts", 
    										 "rsvp-pro-admin-manage-event",
    										 "rsvp_pro_admin_manage_event");

    $page = add_submenu_page("rsvp-pro-top-level",
                     __('RSVP Pro General settings', "rsvp-pro-plugin"),  //page title
                     __('General settings', "rsvp-pro-plugin"),  //subpage title
                     'publish_posts',  //access
                     'rsvp-pro-global-options',    //current file
                     'rsvp_pro_global_options' //options function above
                     );
    
    if(!rsvp_pro_is_network_activated()) {
      add_plugins_page('RSVP Pro Plugin License', 'RSVP Pro Plugin License', 'manage_options', 'rsvppro-license', 'rsvp_pro_license_page');
    }
	}

  function rsvp_pro_modify_multiesite_menu() {
    add_plugins_page('RSVP Pro Plugin License', 'RSVP Pro Plugin License', 'manage_options', 'rsvppro-license', 'rsvp_pro_license_page');
  }
  
  function rsvp_pro_license_page() {
    if(isset($_POST) && isset($_POST['rsvp_pro_license_key']) && check_admin_referer( 'rsvp_pro_license_nonce', 'rsvp_pro_license_nonce' )) {
      rsvp_pro_update_option_license("rsvp_pro_license_key", $_POST['rsvp_pro_license_key']);
      if(!isset($_POST['rsvp_pro_license_deactivate'])) {
        $_POST['rsvp_pro_license_activate'] = 'Activate License';
        rsvp_pro_activate_license();
      }
    }


    $license 	= rsvp_pro_get_option_license( 'rsvp_pro_license_key' );
  	$status 	= rsvp_pro_get_option_license( 'rsvp_pro_license_status' );
  	?>
  	<div class="wrap">
  		<h1><?php _e('RSVP Pro Plugin License Options'); ?></h1>
      <?php
        
      ?>
  		<form method="post">
	       <?php wp_nonce_field( 'rsvp_pro_license_nonce', 'rsvp_pro_license_nonce' ); ?>
  			<?php settings_fields('rsvppro-license'); ?>
		
  			<table class="form-table">
  				<tbody>
  					<tr valign="top">	
  						<th scope="row" valign="top">
  							<?php _e('License Key'); ?>
  						</th>
  						<td>
  							<input id="rsvp_pro_license_key" name="rsvp_pro_license_key" type="text" class="regular-text" value="<?php esc_attr_e( $license ); ?>" />
  							<label class="description" for="rsvp_pro_license_key"><?php _e('Enter your license key', 'rsvp-pro-plugin'); ?></label>
  						</td>
  					</tr>
  					<?php if( false !== $license ) { ?>
  						<tr valign="top">	
  							<th scope="row" valign="top">
  								<?php _e('Activate License'); ?>
  							</th>
  							<td>
  								<?php if( $status !== false && $status == 'valid' ) { ?>
  									<span style="color:green;"><?php _e('active', 'rsvp-pro-plugin'); ?></span>
  									<input type="submit" class="button-secondary" name="rsvp_pro_license_deactivate" value="<?php _e('Deactivate License', 'rsvp-pro-plugin'); ?>"/>
  								<?php } else { ?>
  									<input type="submit" class="button-secondary" name="rsvp_pro_license_activate" value="<?php _e('Activate License', 'rsvp-pro-plugin'); ?>"/>
  								<?php } ?>
  							</td>
  						</tr>
  					<?php } ?>
  				</tbody>
  			</table>	
  			<?php submit_button(); ?>
	
  		</form>
  	<?php
  }
	
  function rsvp_pro_generate_calendar_invite($rsvpEventId) {
    global $wpdb;

    $sql = "SELECT id, eventName, eventStartDate, eventEndDate, eventLocation, eventDescription 
      FROM ".PRO_EVENT_TABLE." WHERE id = %d";
    $eventInfo = $wpdb->get_row($wpdb->prepare($sql, $rsvpEventId));
    if($eventInfo) {
      $invite = "BEGIN:VCALENDAR\r\n".
        "VERSION:2.0\r\n".
        "METHOD:PUBLISH\r\n".
        "BEGIN:VEVENT\r\n".
        "DTSTART:".date("Ymd\THis\Z",strtotime($eventInfo->eventStartDate))."\r\n".
        "DTEND:".date("Ymd\THis\Z",strtotime($eventInfo->eventEndDate))."\r\n".
        "LOCATION:".stripslashes($eventInfo->eventLocation)."\r\n".
        "TRANSP: OPAQUE\r\n".
        "SEQUENCE:0\r\n".
        "DTSTAMP:".date("Ymd\THis\Z")."\r\n".
        "SUMMARY:".stripslashes($eventInfo->eventName)."\r\n".
        "DESCRIPTION:".stripslashes($eventInfo->eventDescription)."\r\n".
        "PRIORITY:1\r\n".
        "CLASS:PUBLIC\r\n".
        "END:VEVENT\r\n".
      "END:VCALENDAR\r\n";

      header("Content-type:text/calendar");
      header('Content-Disposition: attachment; filename="'.stripslashes($eventInfo->eventName).'.ics"');
      Header('Content-Length: '.strlen($invite));
      Header('Connection: close');
      echo $invite;
      exit();
    }
  }

	function rsvp_pro_register_settings() {	
		wp_register_script('jquery_table_sort', plugins_url('jquery.tablednd_0_5.js',RSVP_PRO_PLUGIN_FILE));
		wp_register_style('jquery_ui_stylesheet', rsvp_pro_getHttpProtocol()."://ajax.microsoft.com/ajax/jquery.ui/1.8.5/themes/redmond/jquery-ui.css");

    register_setting('rsvp-pro-option-group', RSVP_PRO_GLOBAL_OPTION_DELETE_TABLES);
    register_setting('rsvp-pro-option-group', RSVP_PRO_GLOBAL_OPTION_STYLES);
	}
	
	function rsvp_pro_admin_scripts() {
		wp_enqueue_script("jquery_table_sort");
		wp_enqueue_script("jquery-ui-core");
		wp_enqueue_style( 'jquery_ui_stylesheet');
    wp_enqueue_style("rsvp_pro_admin_css");
    
    wp_register_script('jquery_multi_select', plugins_url('multi-select/js/jquery.multi-select.js',RSVP_PRO_PLUGIN_FILE));
    wp_enqueue_script("jquery_multi_select");
    wp_register_style('jquery_multi_select_css', plugins_url("multi-select/css/multi-select.css", RSVP_PRO_PLUGIN_FILE));
    wp_enqueue_style( 'jquery_multi_select_css');
    
    wp_register_script('rsvp_pro_admin', plugins_url('rsvp_plugin_admin.js',RSVP_PRO_PLUGIN_FILE));
    wp_enqueue_script("rsvp_pro_admin");
	}
  add_action( 'admin_enqueue_scripts', 'rsvp_pro_admin_scripts' );
	
	function rsvp_pro_init() {
		wp_register_script('jquery_validate', rsvp_pro_getHttpProtocol()."://ajax.aspnetcdn.com/ajax/jquery.validate/1.15.0/jquery.validate.min.js");
    wp_register_script('rsvp_pro_plugin', plugins_url("rsvp_plugin.js", RSVP_PRO_PLUGIN_FILE));
    wp_register_style('rsvp_pro_css', plugins_url("rsvp_plugin.css", RSVP_PRO_PLUGIN_FILE));
    wp_register_style('rsvp_pro_admin_css', plugins_url("rsvp_admin.css", RSVP_PRO_PLUGIN_FILE));
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery_validate');
    wp_enqueue_script('rsvp_pro_plugin');
    wp_enqueue_style("rsvp_pro_css");
    
		
		load_plugin_textdomain('rsvp-pro-plugin', false, basename( dirname( __FILE__ ) ) . '/languages/' );
	}
	
  function rsvp_pro_shortcode_handler($atts) {
    if(isset($atts['id']) && is_numeric($atts['id'])) {
        return rsvp_pro_frontend_handler("[rsvp-pro-pluginhere-{$atts['id']}]");
    }
    
    return "";
  }
  
  function rsvp_pro_attendeelist_shortcode_handler($atts) {
    if(isset($atts['id']) && is_numeric($atts['id'])) {
      return rsvp_pro_attendeelist_frontend_handler($atts['id']);
    }
    return "";
  }

  function rsvp_pro_scheduler() {
    if (! wp_next_scheduled ( 'rsvp_pro_reoccurring_events' )) {
      wp_schedule_event(time(), 'hourly', 'rsvp_pro_reoccurring_events');
    }
  }

  function rsvp_pro_deactivation() {
    wp_clear_scheduled_hook('rsvp_pro_reoccurring_events');
  }

  function rsvp_pro_add_css() {
    $css = get_option(RSVP_PRO_GLOBAL_OPTION_STYLES);

    if(!empty($css)) {
      $output = "<!-- RSVP Pro Styling -->";
      $output .= "<style type=\"text/css\">".esc_html($css)."</style>";

      echo $output;
    }
  }

  function rsvp_pro_calendar_invite_handler() {
    if ( isset($_GET['rsvp_calendar_download']) && 
         is_numeric($_GET['rsvp_calendar_download']) && 
         ($_GET['rsvp_calendar_download'] > 0)
       ) {
        rsvp_pro_generate_calendar_invite($_GET['rsvp_calendar_download']);
    }
  }
  add_action("init", "rsvp_pro_calendar_invite_handler");
  
  add_shortcode( 'rsvppro', 'rsvp_pro_shortcode_handler' );
  add_shortcode("rsvppro-attendeelist", "rsvp_pro_attendeelist_shortcode_handler");
	
	add_action('admin_menu', 'rsvp_pro_modify_menu');
  if(rsvp_pro_is_network_activated()) {
    add_action('network_admin_menu', 'rsvp_pro_modify_multiesite_menu');
  }
	add_action('admin_init', 'rsvp_pro_register_settings');
	add_action('init', 'rsvp_pro_init');
  add_action("plugins_loaded", "rsvp_pro_update_db_check");
	add_filter('the_content', 'rsvp_pro_frontend_handler');
  add_action('wp_head','rsvp_pro_add_css');
  add_action('rsvp_pro_reoccurring_events', 'rsvp_pro_handle_reoccurring_events');
	register_activation_hook(__FILE__,'rsvp_pro_database_setup');
  register_activation_hook(__FILE__, 'rsvp_pro_scheduler');
  register_deactivation_hook(__FILE__, 'rsvp_pro_deactivation');
?>
