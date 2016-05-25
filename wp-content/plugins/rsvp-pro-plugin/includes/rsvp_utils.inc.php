<?php
/* 
  Note: I don't really like to have a utils file but this is a first 
  step to refactoring the many different functions that are being 
  used in this plugin.
*/

/*
 * Description: Database setup for the rsvp plug-in.  
 */
function rsvp_pro_database_setup() {
  global $wpdb;
  
	rsvp_pro_update_database($wpdb);
}

function rsvp_pro_get_event_option($eventId, $option_name) {
  global $wpdb;
  global $rsvp_options;
  $value = "";
  
  if(is_array($rsvp_options)) {
    $options = $rsvp_options;
  } else {
    $options = $wpdb->get_var($wpdb->prepare("SELECT options FROM ".PRO_EVENT_TABLE." WHERE id = %d", $eventId));
    if(!empty($options)) {
      $options = json_decode($options, true);
    }
    $rsvp_options = $options;
  }
  if(is_array($options) && isset($options[$option_name])) {
    if(is_array($options[$option_name])) {
      $value = $options[$option_name];
    } else {
      $value = stripslashes($options[$option_name]);  
    }
  }
  return $value;
}

function rsvp_pro_get_sub_event_option($subEventId, $option_name) {
  global $wpdb;
  global $rsvp_sub_options;
  $value = "";
  
  if(isset($rsvp_sub_options[$subEventId]) && is_array($rsvp_sub_options[$subEventId])) {
    $options = $rsvp_sub_options[$subEventId];
  } else {
    $options = $wpdb->get_var($wpdb->prepare("SELECT options FROM ".PRO_EVENT_TABLE." WHERE id = %d", $subEventId));
    if(!empty($options)) {
      $options = json_decode($options, true);
    }
    $rsvp_sub_options[$subEventId] = $options;
  }
  if(is_array($options) && isset($options[$option_name])) {
    if(is_array($options[$option_name])) {
      $value = $options[$option_name];
    } else {
      $value = stripslashes($options[$option_name]);  
    }
  }
  return $value;
}

function rsvp_pro_get_event_information($eventId, $informationKey) {
  global $wpdb;
  global $event_information;
  
  if((count($event_information) <= 0) || ($event_information[RSVP_PRO_INFO_EVENT_ID] != $eventId)) {
      $info = $wpdb->get_results($wpdb->prepare("SELECT id, eventName, open_date, close_date, event_access 
                                                 FROM ".PRO_EVENT_TABLE." WHERE id = %d", $eventId));
      $event_information[RSVP_PRO_INFO_EVENT_NAME] = $info[0]->eventName;
      $event_information[RSVP_PRO_INFO_OPEN_DATE] = date("m/d/Y", strtotime($info[0]->open_date));
      $event_information[RSVP_PRO_INFO_CLOSE_DATE] = date("m/d/Y", strtotime($info[0]->close_date));
      $event_information[RSVP_PRO_INFO_EVENT_ID] = $info[0]->id;
      $event_information[RSVP_PRO_INFO_EVENT_ACCESS] = $info[0]->event_access;
  }
  
  return $event_information[$informationKey];
}

function rsvp_pro_require_passcode($rsvpId) {
  
  if((rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_OPEN_REGISTRATION) == "Y") && (rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_RSVP_OPEN_NO_PASSCODE) == "Y")) {
    return false;
  }
  
  if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_PASSCODE) == "Y") {
    return true;
  }  
  
  if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_OPEN_REGISTRATION) == "Y") {
    return true;
  }
  
  if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_ONLY_PASSCODE) == "Y") {
    return true;
  }
  
  return false;
}

function rsvp_pro_require_only_passcode_to_register($rsvpId) {
  return (rsvp_pro_get_event_option($rsvpId, RSVP_PRO_ONLY_PASSCODE) == "Y");
}

function rsvp_pro_require_unique_passcode($rsvpId) {
  return rsvp_pro_require_only_passcode_to_register($rsvpId);
}

function rsvp_pro_waitlist_enabled($rsvpId) {
  if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_ENABLE_WAITLIST) == "Y") {
    return true;
  }

  return false;
}

/**
 * This generates a random 6 character passcode to be used for guests when the option is enabled.
 */
function rsvp_pro_generate_passcode($length = 6) {
  $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
	$passcode = "";

  for ($p = 0; $p < $length; $p++) {
      $passcode .= $characters[mt_rand(0, strlen($characters))];
  }

  return $passcode;
}

function rsvp_pro_is_free_rsvp_installed() {
  global $wpdb;
  
  $table = $wpdb->prefix."attendees";
	return ($wpdb->get_var("SHOW TABLES LIKE '$table'") == $table);
}

function rsvp_pro_update_db_check() {
  global $wpdb;
	if(get_option("rsvp_pro_db_version") != RSVP_PRO_DB_VERSION) {
    require_once("rsvp_db_setup.inc.php");
    rsvp_pro_update_database($wpdb);
	}
}

/*
This function checks to see if the page is running over SSL/HTTPs and will return the proper HTTP protocol.

Postcondition: The caller will receive the proper HTTP protocol to use at the beginning of a URL. 
*/
function rsvp_pro_getHttpProtocol() {
	if(isset($_SERVER['HTTPS'])  && (trim($_SERVER['HTTPS']) != "")) {
		return "https";
	}
	return "http";
}

function rsvp_pro_getCurrentPageURL() {
   $url = get_site_url();
   $server_info = parse_url($url);
   $pageURL = $server_info['scheme']."://";
   $domain = $server_info['host'];
   if ($_SERVER["SERVER_PORT"] != "80") {
    $pageURL .= $domain.":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
   } else {
    $pageURL .= $domain.$_SERVER["REQUEST_URI"];
   }
   return $pageURL;
}

function rsvp_pro_humanize_rsvp_status($rsvpStatus) {
  
  if(strtolower($rsvpStatus) == "noresponse") {
    return __("No Response", "rsvp-pro-plugin");
  } else if(strtolower($rsvpStatus) == "yes") {
    return __("Yes", "rsvp-pro-plugin");
  } else if(strtolower($rsvpStatus) == "no") {
    return __("No", "rsvp-pro-plugin");
  } else if(strtolower($rsvpStatus) == "waitlist") {
    return __("Waitlist", "rsvp-pro-plugin");
  }
  
  return $rsvpStatus;
}

function rsvp_pro_get_event_status($rsvpStatus, $eventId, $isSubEvent) {
  $function = "rsvp_pro_get_event_option";

  if($isSubEvent) {
    $function = "rsvp_pro_get_sub_event_option";
  }

  if(strtolower($rsvpStatus) == "yes") {
    $tmp = $function($eventId, RSVP_PRO_OPTION_YES_VERBIAGE);
    if(empty($tmp)) {
      $tmp = rsvp_pro_humanize_rsvp_status($rsvpStatus);
    }

    return $tmp;
  } 
  elseif(strtolower($rsvpStatus) == "no") {
    $tmp = $function($eventId, RSVP_PRO_OPTION_NO_VERBIAGE);
    if(empty($tmp)) {
      $tmp = rsvp_pro_humanize_rsvp_status($rsvpStatus);
    }

    return $tmp;
  }

  return rsvp_pro_humanize_rsvp_status($rsvpStatus);
}

function rsvp_pro_get_salutation_options($rsvpId) {
  $salutations = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_SALUTATIONS);
  if(empty($salutations)) {
    $salutations = RSVP_PRO_DEFAULT_SALUTATION;
  }
  $salutations = explode("||", $salutations);
  
  return $salutations;
}

function rsvp_pro_is_sub_or_parent_event($rsvpId) {
  global $wpdb;
  
  $sql = "SELECT COUNT(*) FROM ".PRO_EVENT_TABLE." WHERE parentEventID = %d OR (id = %d AND parentEventID > 0)";
  $count = $wpdb->get_var($wpdb->prepare($sql, $rsvpId, $rsvpId));
  if($count > 0) {
    return true;
  } else {
    return false;
  }
}

function rsvp_pro_is_sub_event($rsvpId) {
  global $wpdb;

  $sql = "SELECT COUNT(*) FROM ".PRO_EVENT_TABLE." WHERE id = %d AND parentEventID > 0";
  $count = $wpdb->get_var($wpdb->prepare($sql, $rsvpId));
  if($count > 0) {
    return true;
  } else {
    return false;
  } 
}

function rsvp_pro_is_parent_event($rsvpId) {
  global $wpdb;
  
  $sql = "SELECT COUNT(*) FROM ".PRO_EVENT_TABLE." WHERE parentEventID = %d OR (id = %d AND IFNULL(parentEventID, 0) = 0)";
  $count = $wpdb->get_var($wpdb->prepare($sql, $rsvpId, $rsvpId));
  
  if($count > 0) {
    return true;
  } else {
    return false;
  }
}

function rsvp_pro_event_access_type($rsvpId) {
  global $wpdb;
  $accessType = RSVP_PRO_OPEN_EVENT_ACCESS;
  
  $tmpAccessType = $wpdb->get_var($wpdb->prepare("SELECT event_access FROM ".PRO_EVENT_TABLE." WHERE id = %d", $rsvpId));
  if($tmpAccessType == RSVP_PRO_PRIVATE_EVENT_ACCESS) {
    $accessType = RSVP_PRO_PRIVATE_EVENT_ACCESS;
  }
  
  return $accessType;
}

function does_user_have_access_to_event($rsvpId, $attendeeId) {
  global $wpdb;
  $haveAccess = false;
  
  $accessType = rsvp_pro_event_access_type($rsvpId);
  if($accessType == RSVP_PRO_OPEN_EVENT_ACCESS) {
    $haveAccess = true;
  } else {
    // Select access does the person have access?
    $uid = $wpdb->get_var($wpdb->prepare("SELECT rsvpAttendeeID FROM ".PRO_EVENT_ATTENDEE_TABLE." WHERE rsvpAttendeeID = %d AND rsvpEventID = %d",
                          $attendeeId, 
                          $rsvpId));
    if($uid > 0) {
      $haveAccess = true;
    }
  }
  
  return $haveAccess;
}

function get_event_name($eventId) {
  return rsvp_pro_get_event_information($eventId, RSVP_PRO_INFO_EVENT_NAME);
}

function get_number_additional($rsvpId, $attendee = null) {
  $numGuests = 3;
  if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_NUM_ADDITIONAL_GUESTS) != "") {
    $numGuests = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_NUM_ADDITIONAL_GUESTS);
    if(!is_numeric($numGuests) || ($numGuests < 0)) {
      $numGuests = 3;
    }
  }

  if(($attendee != null) && (($attendee->numGuests) > 0)) {
    $numGuests = $attendee->numGuests;
  }
  
  return $numGuests;
}

function rsvp_is_addslashes_enabled() {
  return get_magic_quotes_gpc();
}

function rsvp_pro_is_network_activated() {

  // Makes sure the plugin is defined before trying to use it
  if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
      require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
  }
  return is_plugin_active_for_network("rsvp-pro-plugin/wp-rsvp.php");
}

function rsvp_pro_admin_user_has_access_to_settings($rsvpId) {
  global $current_user;

  // This is a hack but since this gets used in an environment where it 
  // could be checking multiple events at the same time and by default rsvp_pro_get_event_option assumes
  // in the current context to only be looking at one main event
  $roles = rsvp_pro_get_sub_event_option($rsvpId, RSVP_PRO_OPTION_ADMIN_ROLES);
  if ( current_user_can( 'manage_options' ) ) {
    return true;
  }

  if(empty($roles) || (count($roles) <= 0)) {
    return true;
  } else {
    foreach($roles as $role) {
      if(in_array(strtolower($role), $current_user->roles)) {
        return true;
      }
    }

    return false;
  }

  return false;
}