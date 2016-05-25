<?php 
$rsvp_form_action = htmlspecialchars(rsvp_pro_getCurrentPageURL());
$placeholderText = "";
$rsvpId = 0;
// Variable to let us know if this plugin has already been ran on the page and if so we can exit
$rsvp_has_been_ran = false; 

$rsvp_saved_form_vars = array();

$rsvp_saved_form_vars['mainRsvp'] = "";
$rsvp_saved_form_vars['rsvp_note'] = "";
$rsvp_first_name = "";
$rsvp_last_name = "";
$rsvp_passcode = "";
$hasSubEvents = false;
$attendeeCount = array();

function rsvp_pro_handle_output ($intialText, $rsvpText) {	
  global $placeholderText;
  
  $rsvpText = "<a name=\"rsvpArea\" id=\"rsvpArea\"></a>".$rsvpText;
  remove_filter("the_content", "wpautop");
	return str_replace($placeholderText, $rsvpText, $intialText);
}

function rsvp_pro_placeholder_found($text) {
  global $placeholderText;
  global $wpdb; 
  global $rsvpId; 
  
  if(strpos($text, "[rsvp-pro-pluginhere-") !== false) {
    
    // parse it out and try and get the number....
    $startPosition = strpos($text, "[rsvp-pro-pluginhere-");
    $endPosition = strpos($text, "]", $startPosition);
    $placeholderText = substr($text, $startPosition, ($endPosition - $startPosition) + 1);
    
    // Grab the ID..
    $tmpId = str_replace("[rsvp-pro-pluginhere-", "", $placeholderText);
    $tmpId = str_replace("]", "", $tmpId);
    
    // Check to see if the id is valid.
    $table = $wpdb->prefix."rsvpEvents";
    $id = $wpdb->get_var($wpdb->prepare("SELECT id FROM $table WHERE id = %d", $tmpId));
    if(isset($id) && ($id > 0)) {
      $rsvpId = $id;
      return true;
    }
  }
  
  return false;
}

function rsvp_pro_frontend_handler($text) {
	global $wpdb; 
  	global $rsvpId;
  	global $rsvp_has_been_ran; 
  	$rsvp_form_action = htmlspecialchars(rsvp_pro_getCurrentPageURL());
  
	if (!rsvp_pro_placeholder_found($text)) return $text;

  	if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_DONT_USE_HASH) != "Y") {
	    $rsvp_form_action .= "#rsvpArea";
  	}

  /*
   * Kind of a hack to make sure that the plugin only runs once. We do this because some other plugins or 
   * themes like to manually parse and run through short-codes multiple times. 
   */
  if($rsvp_has_been_ran) {
    return $text;
  } else {
    $rsvp_has_been_ran = true;
  }

  $passcodeOptionEnabled = (rsvp_pro_require_passcode($rsvpId)) ? true : false;

	// See if we should allow people to RSVP, etc...
	$openDate = rsvp_pro_get_event_information($rsvpId, RSVP_PRO_INFO_OPEN_DATE); 
	$closeDate = rsvp_pro_get_event_information($rsvpId, RSVP_PRO_INFO_CLOSE_DATE);
	if((strtotime($openDate) !== false) && (strtotime($openDate) > time())) {
		$messageOpenText = __("I am sorry but the ability to RSVP for our event won't open till <strong>%s</strong>", "rsvp-pro-plugin");
		if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_OPEN_DATE_TEXT) != "") {
			$messageOpenText = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_OPEN_DATE_TEXT);
		}

		return rsvp_pro_handle_output($text, sprintf(RSVP_PRO_START_PARA.$messageOpenText.RSVP_PRO_END_PARA, date_i18n( get_option( 'date_format'),strtotime($openDate))));
	} 
	
	if((strtotime($closeDate) !== false) && (strtotime($closeDate) < time())) {
		$messagePassedText = __("The deadline to RSVP for this event has passed.", "rsvp-pro-plugin");
		if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_CLOSE_DATE_TEXT) != "") {
			$messagePassedText = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_CLOSE_DATE_TEXT);
		}

		return rsvp_pro_handle_output($text, $messagePassedText);
	}
	
	if(isset($_POST['rsvpStep'])) {
		$output = "";
		switch(strtolower($_POST['rsvpStep'])) {
      		case("newattendee"):
        		return rsvp_pro_handlenewattendee($output, $text);
        		break;
      		case("addattendee"):
        		//return rsvp_pro_handleNewRsvp($output, $text);
        		return rsvp_pro_handlersvp($output, $text);
        		break;
			case("handlersvp") :
				$output = rsvp_pro_handlersvp($output, $text);
				if(!empty($output)) 
					return $output;
				break;
      		case("wizardattendee"):
        		$output = rsvp_pro_frontend_wizard_form(-1, "handleRsvp", $text);
        		if(!empty($output))
          			return $output;
        		break;
			case("editattendee") :
				$output = rsvp_pro_editAttendee($output, $text);
				if(!empty($output)) 
					return $output;
				break;
			case("foundattendee") :
        		$output = rsvp_pro_foundAttendee($output, $text);
				if(!empty($output)) 
					return $output;
				break;
			case("find") :
				$output = rsvp_pro_find($output, $text);
				if(!empty($output))
					return $output;
				break;
			case("newsearch"):
			default:
				$tmpAttendeeId = rsvp_pro_autologin_frontend($rsvpId);
				if($tmpAttendeeId > 0) {
					$_POST['attendeeID'] = $tmpAttendeeId;
					return rsvp_pro_foundAttendee($output, $text);
				} else {
					return rsvp_pro_handle_output($text, rsvp_pro_frontend_greeting());	
				}
				
				break;
		}
	} else {
	    if((isset($_REQUEST['firstName']) && isset($_REQUEST['lastName'])) || (rsvp_pro_require_only_passcode_to_register($rsvpId) && isset($_REQUEST['passcode']))) {
	      	$output = "";
	      	return rsvp_pro_find($output, $text);
	    } else {
	    	$tmpAttendeeId = rsvp_pro_autologin_frontend($rsvpId);
			if($tmpAttendeeId > 0) {
				$_POST['attendeeID'] = $tmpAttendeeId;
				return rsvp_pro_foundAttendee($output, $text);
			} else {
				return rsvp_pro_handle_output($text, rsvp_pro_frontend_greeting());
			}
	    }
	}
}

function rsvp_pro_handlenewattendee($output, $text) {
  $output = RSVP_PRO_START_CONTAINER;
	if(trim(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_WELCOME_TEXT)) != "") {
		$output .= RSVP_PRO_START_PARA.trim(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_WELCOME_TEXT)).RSVP_PRO_END_PARA;
	} else {
		$output .= RSVP_PRO_START_PARA.__("There are a few more questions we need to ask you if you could please fill them out below to finish up the RSVP process.", 'rsvp-pro-plugin').RSVP_PRO_END_PARA;
	}
  $output .= rsvp_pro_frontend_main_form(0, "addAttendee");
  $output .= RSVP_PRO_END_CONTAINER;
  
  return rsvp_pro_handle_output($text, $output);
}

function rsvp_pro_frontend_prompt_to_edit($attendee) {
	global $rsvpId;
  $rsvp_form_action = htmlspecialchars(rsvp_pro_getCurrentPageURL());
	$prompt = RSVP_PRO_START_CONTAINER; 
	$editGreeting = __("Hi %s it looks like you have already RSVP'd. Would you like to edit your reservation?", 'rsvp-pro-plugin');
	if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_EDIT_PROMPT_TEXT) != "") {
		$editGreeting = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_EDIT_PROMPT_TEXT);
	}
  	$yesText = __("Yes", 'rsvp-pro-plugin');
  	$noText = __("No", 'rsvp-pro-plugin');

  	if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_YES_TEXT) != "") {
  		$yesText = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_YES_TEXT);
  	}

  	if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_NO_TEXT) != "") {
  		$noText = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_NO_TEXT);
  	}

	$prompt .= sprintf(RSVP_PRO_START_PARA.$editGreeting.RSVP_PRO_END_PARA, 
                     htmlspecialchars(stripslashes($attendee->firstName." ".$attendee->lastName)));
	$prompt .= "<form method=\"post\" action=\"$rsvp_form_action\">\r\n
								<input type=\"hidden\" name=\"attendeeID\" value=\"".$attendee->id."\" />
								<input type=\"hidden\" name=\"rsvpStep\" id=\"rsvpStep\" value=\"editattendee\" />
								<input type=\"submit\" value=\"".esc_attr($yesText)."\" onclick=\"document.getElementById('rsvpStep').value='editattendee';\" />
								<input type=\"submit\" value=\"".esc_attr($noText)."\" onclick=\"document.getElementById('rsvpStep').value='newsearch';\"  />
							</form>\r\n";
  $prompt .= RSVP_PRO_END_CONTAINER;
	return $prompt;
}

function rsvp_pro_frontend_main_form($attendeeID, $rsvpStep = "handleRsvp") {
  global $wpdb, $rsvp_saved_form_vars;
  global $rsvpId, $my_plugin_file, $hasSubEvents; 
  $rsvp_form_action = htmlspecialchars(rsvp_pro_getCurrentPageURL());

  if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_FRONTEND_WIZARD) == "Y") {
    return rsvp_pro_frontend_wizard_form($attendeeID, $rsvpStep);
  } else {
    return rsvp_pro_frontend_old_form($attendeeID, $rsvpStep);
  }
}

function rsvp_pro_frontend_wizard_form($attendeeID, $rsvpStep = "handleRsvp", $text = "") {
	global $wpdb, $rsvp_saved_form_vars;
  global $rsvpId, $my_plugin_file, $hasSubEvents;

  $rsvp_form_action = htmlspecialchars(rsvp_pro_getCurrentPageURL());
  
  if(($attendeeID < 0) && isset($_POST['attendeeID'])) {
		$attendee = $wpdb->get_row($wpdb->prepare("SELECT id, firstName, lastName, rsvpStatus 
													FROM ".PRO_ATTENDEES_TABLE." 
													WHERE id = %d AND rsvpEventID = %d", $_POST['attendeeID'], $rsvpId));
    
    if(($attendee != null) && ($attendee->id > 0)) {
      $attendeeID = $attendee->id;
    }
  }
  
  $form = "";
	$attendee = $wpdb->get_row($wpdb->prepare("SELECT id, firstName, lastName, email, rsvpStatus, note, additionalAttendee, personalGreeting, numGuests, suffix, salutation   
																						 FROM ".PRO_ATTENDEES_TABLE." 
																						 WHERE id = %d AND rsvpEventID = %d", $attendeeID, $rsvpId));
  
  $currentEventID = 0;
  $wizardStep = "personalInfo";
  $rsvpStep = "wizardattendee";
  $nextWizardStep = "eventInfo";
  $eventIds = array();
  $sql = "SELECT id FROM ".PRO_EVENT_TABLE." e WHERE (id = %d OR parentEventID = %d)  
     AND ((IFNULL(e.event_access, '".RSVP_PRO_OPEN_EVENT_ACCESS."') != '".RSVP_PRO_PRIVATE_EVENT_ACCESS."') OR (%d IN (SELECT rsvpAttendeeID FROM ".PRO_EVENT_ATTENDEE_TABLE." WHERE rsvpEventID = e.id))) ORDER BY id ASC";
  $events = $wpdb->get_results($wpdb->prepare($sql, $rsvpId, $rsvpId, $attendeeID));
  // Load the array of event ids...
  foreach($events as $e) {
    $eventIds[] = $e->id;
  }
  $needToSave = false;
  
  if(count($eventIds) <= 0) {
    $needToSave = true;
  }
  
  if(isset($_POST['wizardStep']) && !empty($_POST['wizardStep'])) {
    $wizardStep = $_POST['wizardStep'];
    if($wizardStep == "personalInfo") {
      // Next step should be event info...
      $nextWizardStep = "eventInfo";
    } elseif($wizardStep == "eventInfo") {
      if(isset($_POST['currentEventID']) && ($_POST['currentEventID'] > 0) && is_numeric($_POST['currentEventID'])) {
        $currentEventID = $_POST['currentEventID'];
      }
      
      $foundNextId = false;
      // Loop through the array and see if we are at the end or what...
      for($i = 0; $i < count($eventIds); $i++) {
        if($eventIds[$i] > $currentEventID) {
          $currentEventID = $eventIds[$i];
          $foundNextId = true;
          if($i == (count($eventIds) - 1)) {
            $needToSave = true;
          }
          break;
        }
      }
      
      $nextWizardStep = "eventInfo";
    }
  }
  
  if($needToSave) {
    if($attendeeID <= 0) {
      $rsvpStep = "addattendee";
    } else {
      $rsvpStep = "handlersvp";
    }
  }
	$form = "<form id=\"rsvpForm\" name=\"rsvpForm\" method=\"post\" action=\"$rsvp_form_action\" autocomplete=\"off\">\r\n";
	$form .= "	<input type=\"hidden\" name=\"attendeeID\" value=\"".$attendeeID."\" />\r\n";
	$form .= "	<input type=\"hidden\" name=\"rsvpStep\" value=\"$rsvpStep\" />\r\n";
  $form .= "	<input type=\"hidden\" name=\"wizardStep\" value=\"$nextWizardStep\" />\r\n";
  $form .= "	<input type=\"hidden\" name=\"currentEventID\" value=\"$currentEventID\" />\r\n";
  
  $reservedItems = array("attendeeID", "rsvpStep", "wizardStep", "currentEventID");
  foreach($_POST as $key=>$val) {
    if(!in_array($key, $reservedItems)) {
      if(is_array($_POST[$key])) {
      	foreach($val as $v) {
      		$form .= " <input type=\"hidden\" name=\"".esc_html($key)."[]\" value=\"".esc_html($v)."\" />\r\n";		
      	}
      } else {
      	$form .= " <input type=\"hidden\" name=\"".esc_html($key)."\" value=\"".esc_html($val)."\" />\r\n";	
      }
    }
  }
  
  if($wizardStep != "personalInfo") {
    
    if($wizardStep == "eventInfo") {
      $form .= rsvp_pro_frontend_wizard_eventForm($currentEventID, $attendeeID);
    } else {
      $form .= rsvp_pro_frontend_wizard_personalInfo($attendee, $attendeeID);
    }
  } else {
    // Default first page....
  	$form .= rsvp_pro_frontend_wizard_personalInfo($attendee, $attendeeID);
  }
  
  $buttonText = __("Next", "rsvp-pro-plugin");
  if(trim(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_NEXT_BUTTON_TEXT)) != "") {
    $buttonText = stripslashes(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_NEXT_BUTTON_TEXT));
  }
  
  $form .= RSVP_PRO_START_PARA."<input type=\"submit\" value=\"$buttonText\" />".RSVP_PRO_END_PARA;
  $form .= "</form>\r\n";
  return $form;
}

function rsvp_pro_frontend_old_form($attendeeID, $rsvpStep = "handleRsvp") {
    global $wpdb, $rsvp_saved_form_vars;
  	global $rsvpId, $my_plugin_file, $hasSubEvents; 

    $rsvp_form_action = htmlspecialchars(rsvp_pro_getCurrentPageURL());
  
	$attendee = $wpdb->get_row($wpdb->prepare("SELECT id, firstName, lastName, email, rsvpStatus, note, additionalAttendee, personalGreeting, numGuests, suffix, salutation   
																						 FROM ".PRO_ATTENDEES_TABLE." 
																						 WHERE id = %d AND rsvpEventID = %d", $attendeeID, $rsvpId));
	$sql = "SELECT id FROM ".PRO_ATTENDEES_TABLE." 
	 	WHERE (id IN (SELECT attendeeID FROM ".PRO_ASSOCIATED_ATTENDEES_TABLE." WHERE associatedAttendeeID = %d) 
			OR id in (SELECT associatedAttendeeID FROM ".PRO_ASSOCIATED_ATTENDEES_TABLE." WHERE attendeeID = %d)) 
			 AND rsvpEventID = %d";
	$newRsvps = $wpdb->get_results($wpdb->prepare($sql, $attendeeID, $attendeeID, $rsvpId));

  	$waitlistText = __("Waitlist", "rsvp-pro-plugin");
  	$noResponseText = __("No Response", 'rsvp-pro-plugin');
  	if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_NO_RESPONSE_TEXT) != "") {
  		$noResponseText = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_NO_RESPONSE_TEXT);
  	}
  	$numGuests = get_number_additional($rsvpId, $attendee);
  
	$yesVerbiage = ((trim(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_YES_VERBIAGE)) != "") ? rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_YES_VERBIAGE) : 
		__("Yes, of course I will be there! Who doesn't like family, friends, weddings, and a good time?", 'rsvp-pro-plugin'));
	$noVerbiage = ((trim(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_NO_VERBIAGE)) != "") ? rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_NO_VERBIAGE) : 
			__("Um, unfortunately, there is a Star Trek marathon on that day that I just cannot miss.", 'rsvp-pro-plugin'));
	$noteVerbiage = ((trim(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_NOTE_VERBIAGE)) != "") ? rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_NOTE_VERBIAGE) : 
		__("If you have any <strong style=\"color:red;\">food allergies</strong>, please indicate what they are in the &quot;notes&quot; section below.  Or, if you just want to send us a note, please feel free.  If you have any questions, please send us an email.", 'rsvp-pro-plugin'));
	$waitlistVerbiage = __("The event is full but we can add you to the waitlist.", "rsvp-pro-plugin");
	if(trim(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_WAITLIST_VERBIAGE)) != "") {
		$waitlistVerbiage = trim(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_WAITLIST_VERBIAGE));
	}
    
	$form = "<form id=\"rsvpForm\" name=\"rsvpForm\" method=\"post\" action=\"$rsvp_form_action\" autocomplete=\"off\">\r\n";
	$form .= "	<input type=\"hidden\" name=\"attendeeID\" value=\"".$attendeeID."\" />\r\n";
	$form .= "	<input type=\"hidden\" name=\"rsvpStep\" value=\"$rsvpStep\" />\r\n";
	
	if(!empty($attendee->personalGreeting)) {
		$form .= rsvp_pro_BeginningFormField("rsvpCustomGreeting", "").nl2br(stripslashes($attendee->personalGreeting)).RSVP_PRO_END_FORM_FIELD;
	}
  
  // New Attendee fields when open registration is allowed 
  if($attendeeID <= 0) {
  	$firstNameLabel = __("First Name: ", 'rsvp-pro-plugin');
  	if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_FIRST_NAME_LABEL) != "") {
		$firstNameLabel = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_FIRST_NAME_LABEL);
	}

	$lastNameLabel = __("Last Name: ", 'rsvp-pro-plugin');
	if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_LAST_NAME_LABEL) != "") {
		$lastNameLabel = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_LAST_NAME_LABEL);
	}

    $form .= RSVP_PRO_START_PARA;
    $form .= rsvp_pro_BeginningFormField("", "").
      "<label for=\"attendeeFirstName\">".$firstNameLabel."</label>".
      "<input type=\"text\" name=\"attendeeFirstName\" id=\"attendeeFirstName\" value=\"".esc_html($rsvp_saved_form_vars['attendeeFirstName'])."\" />".
      RSVP_PRO_END_FORM_FIELD;
    $form .= RSVP_PRO_END_PARA;
    
    $form .= RSVP_PRO_START_PARA;
    $form .= rsvp_pro_BeginningFormField("", "").
      "<label for=\"attendeeLastName\">".$lastNameLabel."</label>".
      "<input type=\"text\" name=\"attendeeLastName\" id=\"attendeeLastName\" value=\"".esc_html($rsvp_saved_form_vars['attendeeLastName'])."\" />".
      RSVP_PRO_END_FORM_FIELD;
    $form .= RSVP_PRO_END_PARA;
  }
  
	$form .= RSVP_PRO_START_PARA;
	if(trim(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_QUESTION)) != "") {
		$form .= trim(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_QUESTION));
	} else {
		$form .= __("So, how about it?", 'rsvp-pro-plugin');
	}
  
  if(does_user_have_access_to_event($rsvpId, $attendeeID)) : 
    $requiredRsvp = "";
    if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_RSVP_REQUIRED) == "Y") {
      $requiredRsvp = " required";
    }
    
    if(rsvp_pro_frontend_max_limit_hit($rsvpId)) {
    	$form .= rsvp_pro_handle_max_limit_reached_message($rsvpId);
    	$form .= rsvp_pro_handle_waitlist_message($rsvpId);
    	$requiredRsvp .= " disabled=\"true\"";
    }

  	$form .= RSVP_PRO_END_PARA.
      rsvp_pro_BeginningFormField("", "rsvpRsvpQuestionArea").
      "<input type=\"radio\" name=\"mainRsvp\" value=\"Y\" id=\"mainRsvpY\" ".((($attendee->rsvpStatus == "Yes") || ($rsvp_saved_form_vars['mainRsvp'] == "Y")) ? "checked=\"checked\"" : "")." $requiredRsvp /> <label for=\"mainRsvpY\">".$yesVerbiage."</label>".
      RSVP_PRO_END_FORM_FIELD.
      rsvp_pro_BeginningFormField("", "rsvpRsvpQuestionArea").
        "<input type=\"radio\" name=\"mainRsvp\" value=\"N\" id=\"mainRsvpN\" ".((($attendee->rsvpStatus == "No") || ($rsvp_saved_form_vars['mainRsvp'] == "N")) ? "checked=\"checked\"" : "")." /> ".
        "<label for=\"mainRsvpN\">".$noVerbiage."</label>".
      RSVP_PRO_END_FORM_FIELD;

    if((rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_ENABLE_WAITLIST) == "Y") && rsvp_pro_frontend_max_limit_hit($rsvpId)) {
		$form .= rsvp_pro_BeginningFormField("", "rsvpRsvpQuestionArea").
	        "<input type=\"radio\" name=\"mainRsvp\" value=\"W\" id=\"mainRsvpWaitlist\" ".((($attendee->rsvpStatus == "Waitlist") || ($rsvp_saved_form_vars['mainRsvp'] == "W")) ? "checked=\"checked\"" : "")." /> ".
	        "<label for=\"mainRsvpWaitlist\">".$waitlistVerbiage."</label>".
	      RSVP_PRO_END_FORM_FIELD;    	
    }
  
  endif; // if(does_user_have_access_to_event($rsvpId, $attendeeID)) : 
    
  	if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_SHOW_SALUTATION) == "Y") {
  		$label = __("Salutation", 'rsvp-pro-plugin');
  		if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_SALUTATION_LABEL) != "") {
  			$label = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_SALUTATION_LABEL);
  		}

    	$form .= rsvp_pro_BeginningFormField("", "rsvpBorderTop").
      	RSVP_PRO_START_PARA."<label for=\"mainSalutation\">".$label."</label>".RSVP_PRO_END_PARA.
        	"<select name=\"mainSalutation\" id=\"mainSalutation\" size=\"1\"><option value=\"\">--</option>";

      	$salutations = rsvp_pro_get_salutation_options($rsvpId);
      	foreach($salutations as $s) {
        	$form .= "<option value=\"".esc_html($s)."\" ".(($s == $attendee->salutation) ? "selected=\"selected\"" : "").">".esc_html($s)."</option>";
      	}
    
      	$form .= "</select>".    
      	RSVP_PRO_END_FORM_FIELD;
  	}

  	if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_SHOW_SUFFIX) == "Y") {
  		$label = __("Suffix", 'rsvp-pro-plugin');
  		if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_SUFFIX_LABEL) != "") {
  			$label = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_SUFFIX_LABEL);
  		}
	    $form .= rsvp_pro_BeginningFormField("", "rsvpBorderTop").
	      RSVP_PRO_START_PARA."<label for=\"mainSuffix\">".$label."</label>".RSVP_PRO_END_PARA.
	        "<input type=\"text\" name=\"mainSuffix\" id=\"mainSuffix\" value=\"".esc_html($attendee->suffix)."\" />".
	      RSVP_PRO_END_FORM_FIELD;
  	}

  	if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_HIDE_EMAIL_FIELD) != "Y") {
  		$label = __("Email Address", 'rsvp-pro-plugin');
  		if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_EMAIL_LABEL) != "") {
  			$label = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_EMAIL_LABEL);
  		}
	    $form .= rsvp_pro_BeginningFormField("", "rsvpBorderTop").
	      RSVP_PRO_START_PARA."<label for=\"mainEmail\">".$label."</label>".RSVP_PRO_END_PARA.
	        "<input type=\"text\" name=\"mainEmail\" id=\"mainEmail\" value=\"".htmlspecialchars($attendee->email)."\" ".
	          ((rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_EMAIL_REQUIRED) == "Y") ? "required" : "").
	          " />".
	      RSVP_PRO_END_FORM_FIELD;
  	}
  
  
  	$form .= rsvp_pro_buildSubEventMainForm($attendeeID, "main");
	$form .= rsvp_pro_buildAdditionalQuestions($attendeeID, "main", true);
  
  	// Add in group questions
  	$form .= rsvp_pro_BeginningFormField("", "rsvpBorderTop");
  	$form .= RSVP_PRO_END_FORM_FIELD;
	
	if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_HIDE_NOTE) != "Y") {
  		$form .= RSVP_PRO_START_PARA.$noteVerbiage.RSVP_PRO_END_PARA.
      	rsvp_pro_BeginningFormField("", "").
        	"<textarea name=\"rsvp_note\" id=\"rsvp_note\" rows=\"7\" cols=\"50\">".((!empty($attendee->note)) ? $attendee->note : $rsvp_saved_form_vars['rsvp_note'])."</textarea>".RSVP_PRO_END_FORM_FIELD;
	
  	}
	
	$sql = "SELECT id, firstName, lastName, email, personalGreeting, rsvpStatus, salutation, suffix FROM ".PRO_ATTENDEES_TABLE." 
	 	WHERE (id IN (SELECT attendeeID FROM ".PRO_ASSOCIATED_ATTENDEES_TABLE." WHERE associatedAttendeeID = %d) 
			OR id in (SELECT associatedAttendeeID FROM ".PRO_ASSOCIATED_ATTENDEES_TABLE." WHERE attendeeID = %d) OR 
      id IN (SELECT waa1.attendeeID FROM ".PRO_ASSOCIATED_ATTENDEES_TABLE." waa1 
           INNER JOIN ".PRO_ASSOCIATED_ATTENDEES_TABLE." waa2 ON waa2.attendeeID = waa1.attendeeID  OR 
                                                     waa1.associatedAttendeeID = waa2.attendeeID 
           WHERE waa2.associatedAttendeeID = %d AND waa1.attendeeID <> %d)) AND rsvpEventID = %d";
	
	$associations = $wpdb->get_results($wpdb->prepare($sql, $attendeeID, $attendeeID, $attendeeID, $attendeeID, $rsvpId));
	if(count($associations) > 0) {
		$message = __("The following people are associated with you.  At this time you can RSVP for them as well.", 'rsvp-pro-plugin');
		if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_ASSOCIATED_MESSAGE) != "") {
			$message = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_ASSOCIATED_MESSAGE);
		}

		$form .= "<h3>".$message."</h3>";
		foreach($associations as $a) {
      		if($a->id != $attendeeID) {
  				$form .= "<div class=\"rsvpAdditionalAttendee\">\r\n";
        		$form .= "<div class=\"rsvpAdditionalAttendeeQuestions\">\r\n";
        
        		if(does_user_have_access_to_event($rsvpId, $a->id)) {
          			$requiredRsvp = "";
		        	if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_RSVP_REQUIRED) == "Y") {
		            	$requiredRsvp = " required";
		        	}

          			if(rsvp_pro_frontend_max_limit_hit($rsvpId)) {
	    				$form .= rsvp_pro_handle_max_limit_reached_message($rsvpId);
	    				$form .= rsvp_pro_handle_waitlist_message($rsvpId);
	    				$requiredRsvp .= " disabled=\"true\"";
	      			}
          
          			$greeting = __(" Will %s be attending?", 'rsvp-pro-plugin');
          			if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_ASSOCIATED_ATTENDEE_GREETING) != "") {
          				$greeting = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_ASSOCIATED_ATTENDEE_GREETING);
          			}

          			if(strpos($greeting, "%s") !== false) {
          				$greeting = sprintf($greeting, esc_html(stripslashes($a->firstName." ".$a->lastName)));
          			}

    				$form .= rsvp_pro_BeginningFormField("", "rsvpRsvpGreeting")."<h4>".$greeting."</h4>".RSVP_PRO_END_FORM_FIELD.
              			rsvp_pro_BeginningFormField("", "rsvpRsvpQuestionArea").
                  			"<input type=\"radio\" name=\"attending".$a->id."\" value=\"Y\" id=\"attending".$a->id."Y\" ".(($a->rsvpStatus == "Yes") ? "checked=\"checked\"" : "" )." $requiredRsvp /> ".
                  			"<label for=\"attending".$a->id."Y\">$yesVerbiage</label>".
                    		RSVP_PRO_END_FORM_FIELD.
                  		rsvp_pro_BeginningFormField("", "rsvpRsvpQuestionArea").
    							"<input type=\"radio\" name=\"attending".$a->id."\" value=\"N\" id=\"attending".$a->id."N\" ".(($a->rsvpStatus == "No") ? "checked=\"checked\"" : "" )." /> ".
                  		"<label for=\"attending".$a->id."N\">$noVerbiage</label>".RSVP_PRO_END_FORM_FIELD;

          			if((rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_ENABLE_WAITLIST) == "Y") && rsvp_pro_frontend_max_limit_hit($rsvpId)) {
						$form .= rsvp_pro_BeginningFormField("", "rsvpRsvpQuestionArea").
					        "<input type=\"radio\" name=\"mainRsvp\" value=\"W\" id=\"mainRsvpWaitlist\" ".((($attendee->rsvpStatus == "Waitlist") || ($rsvp_saved_form_vars['mainRsvp'] == "W")) ? "checked=\"checked\"" : "")." /> ".
					        "<label for=\"mainRsvpWaitlist\">".$waitlistVerbiage."</label>".
					      RSVP_PRO_END_FORM_FIELD;    	
		    		}

		          	if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_SHOW_NORESPONSE_FOR_ASSOCIATED) == "Y") {
		            	$form .= rsvp_pro_BeginningFormField("", "rsvpRsvpQuestionArea")."<input type=\"radio\" name=\"attending".$a->id."\" value=\"NoResponse\" id=\"attending".$a->id."NoResponse\" ".(($a->rsvpStatus == "NoResponse") ? "checked=\"checked\"" : "" )." /> ".
		                  "<label for=\"attending".$a->id."NoResponse\">$noResponseText</label>".RSVP_PRO_END_FORM_FIELD;
		          	}
			
	    			if(!empty($a->personalGreeting)) {
	    				$form .= RSVP_PRO_START_PARA.nl2br($a->personalGreeting).RSVP_PRO_END_PARA;
	    			}
			
			
			        if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_SHOW_SALUTATION) == "Y") {
			          	$label = __("Salutation", 'rsvp-pro-plugin');
				  		if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_SALUTATION_LABEL) != "") {
				  			$label = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_SALUTATION_LABEL);
				  		}

			            $form .= rsvp_pro_BeginningFormField("", "rsvpBorderTop").
			              RSVP_PRO_START_PARA."<label for=\"attending".$a->id."Salutation\">".$label."</label>".RSVP_PRO_END_PARA.
			                "<select name=\"attending".$a->id."Salutation\" id=\"attending".$a->id."Salutation\" size=\"1\"><option value=\"\">--</option>";
			              $salutations = rsvp_pro_get_salutation_options($rsvpId);
			          	foreach($salutations as $s) {
			            	$form .= "<option value=\"".esc_html($s)."\" ".(($s == $a->salutation) ? "selected=\"selected\"" : "").">".esc_html($s)."</option>";
			          	}
			      
			            $form .= "</select>".    
			            RSVP_PRO_END_FORM_FIELD;
			        }
  
		            if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_SHOW_SUFFIX) == "Y") {
			          	$label = __("Suffix", 'rsvp-pro-plugin');
				  		if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_SUFFIX_LABEL) != "") {
				  			$label = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_SUFFIX_LABEL);
				  		}

			            $form .= rsvp_pro_BeginningFormField("", "rsvpBorderTop").
			              RSVP_PRO_START_PARA."<label for=\"attending".$a->id."Suffix\">".$label."</label>".RSVP_PRO_END_PARA.
			                "<input type=\"text\" name=\"attending".$a->id."Suffix\" id=\"attending".$a->id."Suffix\" value=\"".esc_html($a->suffix)."\" />".
			              RSVP_PRO_END_FORM_FIELD;
		            }
      
			        if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_HIDE_EMAIL_FIELD) != "Y") {
			          	$label = __("Email Address", 'rsvp-pro-plugin');
				  		if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_EMAIL_LABEL) != "") {
				  			$label = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_EMAIL_LABEL);
				  		}

			            $form .= rsvp_pro_BeginningFormField("", "rsvpBorderTop").
			              RSVP_PRO_START_PARA."<label for=\"attending".$a->id."Email\">".$label."</label>".RSVP_PRO_END_PARA.
			                "<input type=\"text\" name=\"attending".$a->id."Email\" id=\"attending".$a->id."Email\" value=\"".htmlspecialchars($a->email)."\" ".
			            ((rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_EMAIL_REQUIRED) == "Y") ? "required" : "")." />".
			              RSVP_PRO_END_FORM_FIELD;
			        }
        		} else {
          			$form .= rsvp_pro_BeginningFormField("", "").RSVP_PRO_START_PARA.sprintf(__("%s's events.", 'rsvp-pro-plugin'), htmlspecialchars($a->firstName." ".$a->lastName)).RSVP_PRO_END_PARA;
        		}// if(does_user_have_access_to_event($rsvpId, $a->id)): 
        		$form .= rsvp_pro_buildSubEventMainForm($a->id, $a->id."Existing");
  				$form .= rsvp_pro_buildAdditionalQuestions($a->id, $a->id);
        		$form .= "</div>\r\n"; //-- rsvpAdditionalAttendeeQuestions
  				$form .= "</div>\r\n";
		  	} // if($a->id != ...)
    	} // foreach($associations...)
	}
	
	if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_HIDE_ADDITIONAL) != "Y") {
    
	    $availableGuestCount = $numGuests - count($newRsvps);

	    if($availableGuestCount < 0) {
	    	$availableGuestCount = 0;
	    }
    	if($availableGuestCount > 0) {
		    $text = sprintf(__("You currently can invite <span id=\"numAvailableToAdd\">%d</span> more people.", 'rsvp-pro-plugin'), $availableGuestCount);
	    
	    	$additionalVerbiageText = trim(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_ADD_ADDITIONAL_VERBIAGE));
		    if($additionalVerbiageText != "") {
		    	if((strpos($additionalVerbiageText, "%d") !== false) || (strpos($additionalVerbiageText, "%s") !== false)) {
		    		$text = sprintf($additionalVerbiageText, $availableGuestCount);	
		    	} else {
		    		$text = $additionalVerbiageText;
		    	}
		    }
	    
	    	$buttonText = __("Add Additional Guests", "rsvp-pro-plugin");
	    	if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_ADD_ATTENDEE_BUTTON_TEXT) != "") {
	    		$buttonText = stripslashes(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_ADD_ATTENDEE_BUTTON_TEXT));
	    	}
			$form .= "<h3>$text</h3>\r\n";

			$form .= "<div id=\"additionalRsvpContainer\">\r\n
									<input type=\"hidden\" name=\"additionalRsvp\" id=\"additionalRsvp\" value=\"".count($newRsvps)."\" />
									<div style=\"text-align:right\" id=\"addRsvp\"><button>$buttonText</button></div>".
								"</div>";
		}
	}
	
  $buttonText = __("RSVP", "rsvp-pro-plugin");
  if(trim(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_RSVP_BUTTON_TEXT)) != "") {
    $buttonText = stripslashes(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_RSVP_BUTTON_TEXT));
  }
	$form .= RSVP_PRO_START_PARA."<input type=\"submit\" value=\"$buttonText\" />".RSVP_PRO_END_PARA;
	
	$form .= "</form>\r\n";
	rsvp_pro_output_additional_js($rsvpId, $attendee, $attendeeID);
  
	return $form;
}

function rsvp_pro_buildSubEventMainForm($attendeeId, $baseName) {
  global $wpdb, $rsvpId;
  
  $output = "";
  $sql = "SELECT eventName, e.id, se.rsvpStatus 
    FROM ".PRO_EVENT_TABLE." e 
    LEFT JOIN ".PRO_ATTENDEE_SUB_EVENTS_TABLE." se ON se.rsvpEventID = e.id AND se.rsvpAttendeeID = %d
    WHERE parentEventID = %d";
  $subEvents = $wpdb->get_results($wpdb->prepare($sql, $attendeeId, $rsvpId));
  if(count($subEvents) > 0) {
    foreach($subEvents as $se) {      
      if(does_user_have_access_to_event($se->id, $attendeeId)): 
        $output .= "<h4>".__("RSVP for ", "rsvp-pro-plugin").esc_html($se->eventName)."</h4>";
      
        $yesText = __("Yes", 'rsvp-pro-plugin');
        $noText  = __("No", 'rsvp-pro-plugin'); 
        $waitlistText = __("Waitlist", "rsvp-pro-plugin");
      
      	$yesSubVerbiage = ((trim(rsvp_pro_get_sub_event_option($se->id, RSVP_PRO_OPTION_YES_VERBIAGE)) != "") ? rsvp_pro_get_sub_event_option($se->id, RSVP_PRO_OPTION_YES_VERBIAGE) : 
      		sprintf(__("Yes, I will attend %s", 'rsvp-pro-plugin'), $se->eventName));
      	$noSubVerbiage = ((trim(rsvp_pro_get_sub_event_option($se->id, RSVP_PRO_OPTION_NO_VERBIAGE)) != "") ? rsvp_pro_get_sub_event_option($se->id, RSVP_PRO_OPTION_NO_VERBIAGE) : 
      			sprintf(__("No, I will not be able to attend %s", 'rsvp-pro-plugin'), $se->eventName));
      	$waitlistVerbiage = sprintf(__("Add me to the waitlist for even %s", "rsvp-pro-plugin"), $se->eventName);
      	if(trim(rsvp_pro_get_sub_event_option($se->id, RSVP_PRO_OPTION_WAITLIST_VERBIAGE)) != "") {
      		$waitlistVerbiage = sprintf(trim(rsvp_pro_get_sub_event_option($se->id, RSVP_PRO_OPTION_WAITLIST_VERBIAGE)), $se->eventName);
      	}
          
        $requiredRsvp = "";
        if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_RSVP_REQUIRED) == "Y") {
          $requiredRsvp = " required";
        }

        if(rsvp_pro_frontend_max_limit_hit($se->id)) {
	    	$output .= rsvp_pro_handle_max_limit_reached_message($se->id);
	    	$otuput .= rsvp_pro_handle_waitlist_message($se->id);
	    	$requiredRsvp .= " disabled=\"true\"";
	    }

	    if(trim(rsvp_pro_get_sub_event_option($se->id, RSVP_PRO_OPTION_QUESTION)) != "") {
			$output .= RSVP_PRO_START_PARA.trim(rsvp_pro_get_sub_event_option($se->id, RSVP_PRO_OPTION_QUESTION)).RSVP_PRO_END_PARA;
		}
          
      	$output .= rsvp_pro_BeginningFormField("", "rsvpRsvpQuestionArea").
          "<input type=\"radio\" name=\"".$baseName."RsvpSub".$se->id."\" value=\"Y\" id=\"".$baseName."RsvpSub".$se->id."Y\" ".((($se->rsvpStatus == "Yes") || ($rsvp_saved_form_vars["$baseName".$se->id] == "Y")) ? "checked=\"checked\"" : "")." $requiredRsvp /> <label for=\"".$baseName."RsvpSub".$se->id."Y\">".$yesSubVerbiage."</label>".
          RSVP_PRO_END_FORM_FIELD.
          rsvp_pro_BeginningFormField("", "rsvpRsvpQuestionArea").
            "<input type=\"radio\" name=\"".$baseName."RsvpSub".$se->id."\"\" value=\"N\" id=\"".$baseName."RsvpSub".$se->id."N\" ".((($se->rsvpStatus == "No") || ($rsvp_saved_form_vars["$baseName".$se->id] == "N")) ? "checked=\"checked\"" : "")." /> ".
            "<label for=\"".$baseName."RsvpSub".$se->id."N\">".$noSubVerbiage."</label>".
          RSVP_PRO_END_FORM_FIELD;

        if((rsvp_pro_get_sub_event_option($se->id, RSVP_PRO_OPTION_ENABLE_WAITLIST) == "Y") && rsvp_pro_frontend_max_limit_hit($se->id)) {
        	$output .= rsvp_pro_BeginningFormField("", "rsvpRsvpQuestionArea").
	            "<input type=\"radio\" name=\"".$baseName."RsvpSub".$se->id."\"\" value=\"W\" id=\"".$baseName."RsvpSub".$se->id."W\" ".((($se->rsvpStatus == "Waitlist") || ($rsvp_saved_form_vars["$baseName".$se->id] == "W")) ? "checked=\"checked\"" : "")." /> ".
	            "<label for=\"".$baseName."RsvpSub".$se->id."W\">".$waitlistVerbiage."</label>".
	          RSVP_PRO_END_FORM_FIELD;
        }
      endif; // if(does_user_have_access_to_event($se->id, $attendeeId)): 
    } // foreach($subEvents as $se) { 
  }
  
  return $output;
}

function rsvp_pro_revtrievePreviousAnswer($attendeeID, $questionID) {
	global $wpdb;
	$answers = "";
	if(($attendeeID > 0) && ($questionID > 0)) {
		$rs = $wpdb->get_results($wpdb->prepare("SELECT answer FROM ".PRO_ATTENDEE_ANSWERS." WHERE questionID = %d AND attendeeID = %d", $questionID, $attendeeID));
		if(count($rs) > 0) {
			$answers = stripslashes($rs[0]->answer);
		}
	}
	
	return $answers;
}

function rsvp_pro_createQuestionInputs($attendeeID, $prefix, $questions) {
  	global $wpdb;
  	$output = "";
  
	if(count($questions) > 0) {
		foreach($questions as $q) {
      		if(does_user_have_access_to_event($q->rsvpEventID, $attendeeID)): 
  				$oldAnswer = rsvp_pro_revtrievePreviousAnswer($attendeeID, $q->id);
			
  				$output .= rsvp_pro_BeginningFormField("", "").RSVP_PRO_START_PARA.stripslashes($q->question).RSVP_PRO_END_PARA;
				
  				if($q->questionType == QT_MULTI) {
  					$oldAnswers = explode("||", $oldAnswer);
					
  					$answers = $wpdb->get_results($wpdb->prepare("SELECT id, answer, defaultAnswer FROM ".PRO_QUESTION_ANSWERS_TABLE." WHERE questionID = %d", $q->id));
  					if(count($answers) > 0) {
  						$i = 0;
  						foreach($answers as $a) {
			                $requiredAttribute = "";
			                if(($i == 0) && ($q->required == "Y")) {
			                  $requiredAttribute = "required";
			                }
			                if(empty($oldAnswer) && ($a->defaultAnswer == "Y")) {
			                	$oldAnswers = array($a->id);
			                }

  							$output .= rsvp_pro_BeginningFormField("", "rsvpCheckboxCustomQ")."<input type=\"checkbox\" name=\"".$prefix."question".$q->id."[]\" id=\"".$prefix."question".$q->id.$a->id."\" value=\"".$a->id."\" "
  							  .((in_array(stripslashes($a->answer), $oldAnswers)) ? " checked=\"checked\"" : "")." $requiredAttribute />".
                  			  "<label for=\"".$prefix."question".$q->id.$a->id."\">".stripslashes($a->answer)."</label>\r\n".RSVP_PRO_END_FORM_FIELD;
  							$i++;
  						}
              			$output .= "<div class=\"rsvpClear\">&nbsp;</div>\r\n";
  					}
  				} else if ($q->questionType == QT_DROP) {
					
  					$output .= "<select name=\"".$prefix."question".$q->id."\" size=\"1\" ".(($q->required == "Y") ? "required" : "").">\r\n".
  						"<option value=\"\">--</option>\r\n";
  					$answers = $wpdb->get_results($wpdb->prepare("SELECT id, answer, defaultAnswer FROM ".PRO_QUESTION_ANSWERS_TABLE." WHERE questionID = %d", $q->id));
  					if(count($answers) > 0) {
  						foreach($answers as $a) {
  							if(empty($oldAnswer) && ($a->defaultAnswer == "Y")) {
			                	$oldAnswer = $a->answer;
			                }
  							$output .= "<option value=\"".$a->id."\" ".((stripslashes($a->answer) == $oldAnswer) ? " selected=\"selected\"" : "").">".stripslashes($a->answer)."</option>\r\n";
  						}
  					}
  					$output .= "</select>\r\n";
  				} else if ($q->questionType == QT_LONG) {
  					$output .= "<textarea name=\"".$prefix."question".$q->id."\" rows=\"5\" cols=\"35\" ".(($q->required == "Y") ? "required" : "").">".htmlspecialchars($oldAnswer)."</textarea>";
  				} else if ($q->questionType == QT_RADIO) {
  					$answers = $wpdb->get_results($wpdb->prepare("SELECT id, answer, defaultAnswer FROM ".PRO_QUESTION_ANSWERS_TABLE." WHERE questionID = %d", $q->id));
  					if(count($answers) > 0) {
  						$i = 0;
  						foreach($answers as $a) {
			                $requiredAttribute = "";
			                if(($i == 0) && ($q->required == "Y")) {
			                  $requiredAttribute = "required";
			                }

			                if(empty($oldAnswer) && ($a->defaultAnswer == "Y")) {
			                	$oldAnswer = $a->answer;
			                }
              
  							$output .= rsvp_pro_BeginningFormField("", "rsvpRadioCustomQ")."<input type=\"radio\" name=\"".$prefix."question".$q->id."\" id=\"".$prefix."question".$q->id.$a->id."\" value=\"".$a->id."\" "
  							  .((stripslashes($a->answer) == $oldAnswer) ? " checked=\"checked\"" : "")." $requiredAttribute /> ".
                			  "<label for=\"".$prefix."question".$q->id.$a->id."\">".stripslashes($a->answer)."</label>\r\n".RSVP_PRO_END_FORM_FIELD;
  							$i++;
  						}
  						$output .= "<div class=\"rsvpClear\">&nbsp;</div>\r\n";
  					}
          		} else if ($q->questionType == QT_READ_ONLY) {
            		$output .= RSVP_PRO_START_PARA.esc_html($oldAnswer).RSVP_PRO_END_PARA;
  				} else {
  					// normal text input
  					$output .= "<input type=\"text\" name=\"".$prefix."question".$q->id."\" value=\"".htmlspecialchars($oldAnswer)."\" size=\"25\" ".(($q->required == "Y") ? "required" : "")." />";
  				}
				
  			$output .= RSVP_PRO_END_FORM_FIELD;
      		endif;
		}
	}
  
  return $output;
}

function rsvp_pro_buildAdditionalQuestions($attendeeID, $prefix, $includeGroupQuestions = false) {
	global $wpdb, $rsvp_saved_form_vars;
  global $rsvpId;
  
	$output = "<div class=\"rsvpCustomQuestions\">";
	
	$sql = "SELECT q.id, q.question, questionType, q.sortOrder, q.required, e.id AS rsvpEventID FROM ".PRO_QUESTIONS_TABLE." q 
					INNER JOIN ".PRO_QUESTION_TYPE_TABLE." qt ON qt.id = q.questionTypeID 
          INNER JOIN ".PRO_EVENT_TABLE." e ON e.id = q.rsvpEventID 
					WHERE (q.permissionLevel = 'public' 
					  OR (q.permissionLevel = 'private' AND q.id IN (SELECT questionID FROM ".PRO_QUESTION_ATTENDEES_TABLE." WHERE attendeeID = $attendeeID)))
            AND q.rsvpEventID = $rsvpId AND qt.questionType <> 'hidden' ";
  $unionSql = " UNION SELECT q.id, q.question, questionType, q.sortOrder, q.required, e.id AS rsvpEventID FROM ".PRO_QUESTIONS_TABLE." q 
    INNER JOIN ".PRO_QUESTION_TYPE_TABLE." qt ON qt.id = q.questionTypeID 
    INNER JOIN ".PRO_EVENT_TABLE." e ON e.id = q.rsvpEventID 
    WHERE e.parentEventID = $rsvpId AND 
    (q.permissionLevel = 'public' 
    					  OR (q.permissionLevel = 'private' AND q.id IN (SELECT questionID FROM ".PRO_QUESTION_ATTENDEES_TABLE." WHERE attendeeID = $attendeeID))) AND qt.questionType <> 'hidden' ";
  if(!$includeGroupQuestions) {
    $sql .= "  AND grouping <> '".RSVP_PRO_QG_MULTI."' ";
    $unionSql .= "  AND grouping <> '".RSVP_PRO_QG_MULTI."' ";
  }
  $unionSql .= " ORDER BY sortOrder";
  
  $questions = $wpdb->get_results($sql.$unionSql);
  $output .= rsvp_pro_createQuestionInputs($attendeeID, $prefix, $questions);
  
	return $output."</div>";
}

function rsvp_pro_find(&$output, &$text) {
	global $wpdb, $rsvp_first_name, $rsvp_last_name, $rsvp_passcode;
  	global $rsvpId;

  $rsvp_form_action = htmlspecialchars(rsvp_pro_getCurrentPageURL());
	$passcodeOptionEnabled = (rsvp_pro_require_passcode($rsvpId)) ? true : false;
  $emailLookupEnabled = (rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_ATTENDEE_LOOKUP_VIA_EMAIL) == "Y") ? true : false;
	$passcodeOnlyOption = (rsvp_pro_require_only_passcode_to_register($rsvpId)) ? true : false;
	$lastNameNotRequired = (rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_LAST_NAME_NOT_REQUIRED) == "Y") ? true : false;
	$rsvp_first_name = $_REQUEST['firstName'];
	$rsvp_last_name = $_REQUEST['lastName'];
	$passcode = "";
	if(isset($_REQUEST['passcode'])) {
		$passcode = $_REQUEST['passcode'];
		$rsvp_passcode = $_REQUEST['passcode'];
	}
				
	$firstName = $_REQUEST['firstName'];
	$lastName = $_REQUEST['lastName'];
				
	if(!$passcodeOnlyOption && !$emailLookupEnabled && ((strlen($firstName) <= 1) || (!$lastNameNotRequired && (strlen($lastName) <= 1)))) {
		$output = "<p class=\"rsvpParagraph\" style=\"color:red\">".__("A first and last name must be specified", 'rsvp-pro-plugin')."</p>\r\n";
		$output .= rsvp_pro_frontend_greeting();
					
		return rsvp_pro_handle_output($text, $output);
	}

	$baseWhere = " (SOUNDEX(firstName) = SOUNDEX(%s) OR (FIND_IN_SET(%s, nicknames) > 0)) AND SOUNDEX(lastName) = SOUNDEX(%s) ";

	if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_PARTIAL_MATCH_USER_SEARCH) == "Y") {
		$baseWhere = " (firstName LIKE '%%%s%%' OR (FIND_IN_SET(%s, nicknames) > 0)) AND lastName LIKE '%%%s%%' ";
	}

	if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_DISABLE_USER_SEARCH) == "Y") {
		$baseWhere = " (firstName = %s OR (FIND_IN_SET(%s, nicknames) > 0)) AND lastName = %s ";		
	}
				
	// Try to find the user.
	if($passcodeOptionEnabled) {
	    if($passcodeOnlyOption) {
	  		$attendee = $wpdb->get_results($wpdb->prepare("SELECT id, firstName, lastName, rsvpStatus, 
	  				 									 (SELECT COUNT(*) FROM ".PRO_ATTENDEE_SUB_EVENTS_TABLE." WHERE rsvpStatus <> 'NoResponse' AND rsvpAttendeeID = a.id) AS subStatus, primaryAttendee   
														 FROM ".PRO_ATTENDEES_TABLE." a 
														 WHERE passcode = %s AND rsvpEventID = %d  ORDER BY primaryAttendee DESC", $passcode, $rsvpId));
	    } else {
        if($emailLookupEnabled) {
          $attendee = $wpdb->get_results($wpdb->prepare("SELECT id, firstName, lastName, rsvpStatus, email, suffix, salutation , 
                               (SELECT COUNT(*) FROM ".PRO_ATTENDEE_SUB_EVENTS_TABLE." WHERE rsvpStatus <> 'NoResponse' AND rsvpAttendeeID = a.id) AS subStatus, primaryAttendee   
                             FROM ".PRO_ATTENDEES_TABLE." a 
                             WHERE email = %s AND passcode = %s AND rsvpEventID = %d  ORDER BY primaryAttendee DESC", $_REQUEST['email'], $passcode, $rsvpId));
        } else {
          $attendee = $wpdb->get_results($wpdb->prepare("SELECT id, firstName, lastName, rsvpStatus, email, suffix, salutation , 
                               (SELECT COUNT(*) FROM ".PRO_ATTENDEE_SUB_EVENTS_TABLE." WHERE rsvpStatus <> 'NoResponse' AND rsvpAttendeeID = a.id) AS subStatus, primaryAttendee   
                             FROM ".PRO_ATTENDEES_TABLE." a 
                             WHERE $baseWhere AND passcode = %s AND rsvpEventID = %d  ORDER BY primaryAttendee DESC", $firstName, $firstName, $lastName, $passcode, $rsvpId));
        }
	    }
  }  elseif ($emailLookupEnabled) {
    $attendee = $wpdb->get_results($wpdb->prepare("SELECT id, firstName, lastName, rsvpStatus, email, suffix, salutation , 
                           (SELECT COUNT(*) FROM ".PRO_ATTENDEE_SUB_EVENTS_TABLE." WHERE rsvpStatus <> 'NoResponse' AND rsvpAttendeeID = a.id) AS subStatus, primaryAttendee   
                           FROM ".PRO_ATTENDEES_TABLE." a 
                           WHERE email = %s AND rsvpEventID = %d ORDER BY primaryAttendee DESC", $_REQUEST['email'], $rsvpId));
	} else {
		$attendee = $wpdb->get_results($wpdb->prepare("SELECT id, firstName, lastName, rsvpStatus, email, suffix, salutation , 
													 (SELECT COUNT(*) FROM ".PRO_ATTENDEE_SUB_EVENTS_TABLE." WHERE rsvpStatus <> 'NoResponse' AND rsvpAttendeeID = a.id) AS subStatus, primaryAttendee   
													 FROM ".PRO_ATTENDEES_TABLE." a 
													 WHERE $baseWhere AND rsvpEventID = %d ORDER BY primaryAttendee DESC", $firstName, $firstName, $lastName, $rsvpId));
	}
  
	if($attendee != null) {
	    if((count($attendee) > 1) && ($attendee[0]->primaryAttendee != "Y")) {
	      $output = "<div>\r\n";
	      
	      $multipleText = __("we found multiple people with that name, please select your record", "rsvp-pro-plugin");

	      if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_MULTIPLE_MATCHES_TEXT) != "") {
	      	$multipleText = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_MULTIPLE_MATCHES_TEXT);
	      }

	      $output .= RSVP_PRO_START_PARA.__("Hi ", "rsvp-pro-plugin").esc_html(stripslashes($attendee[0]->firstName." ".$attendee[0]->lastName)).
	        " ".$multipleText.RSVP_PRO_END_PARA;
	      
	      foreach($attendee as $a) {
	        $output .= RSVP_PRO_START_PARA;
	        $output .= "<form method=\"post\" action=\"{$rsvp_form_action}\">";
	        $output .= "  <input type=\"hidden\" value=\"foundattendee\" name=\"rsvpStep\" />";
	        $output .= "  <input type=\"hidden\" name=\"attendeeID\" value=\"".$a->id."\" />";
	        $output .= esc_html(stripslashes($a->salutation." ".$a->firstName." ".$a->lastName." ".$a->suffix));
	        
	        if(!empty($a->email)) {
	          $output .= " - ".esc_html(stripslashes($a->email));
	        }
	        
	        $output .= " <input type=\"submit\" value=\"".__("RSVP", "rsvp-pro-plugin")."\" />";
	        $output .= "</form>".RSVP_PRO_END_PARA;
	      }
	      
	      return rsvp_pro_handle_output($text, $output."</div>\r\n");
	    } else {
      		$attendee = $attendee[0];
  			// hey we found something, we should move on and print out any associated users and let them rsvp
  			$output = "<div>\r\n";
  			if((strtolower($attendee->rsvpStatus) == "noresponse") && ($attendee->subStatus <= 0)) {
  				$output .= RSVP_PRO_START_PARA."Hi ".htmlspecialchars(stripslashes($attendee->firstName." ".$attendee->lastName))."!".RSVP_PRO_END_PARA;
						
  				if(trim(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_WELCOME_TEXT)) != "") {
  					$output .= RSVP_PRO_START_PARA.trim(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_WELCOME_TEXT)).RSVP_PRO_END_PARA;
  				} else {
  					$output .= RSVP_PRO_START_PARA.__("There are a few more questions we need to ask you if you could please fill them out below to finish up the RSVP process.", 'rsvp-pro-plugin').RSVP_PRO_END_PARA;
  				}
						
  				$output .= rsvp_pro_frontend_main_form($attendee->id);
  			} else {
  				$output .= rsvp_pro_frontend_prompt_to_edit($attendee);
  			}
  			return rsvp_pro_handle_output($text, $output."</div>\r\n");
    	}
	} // if($attendee != null) {
				
	// We did not find anyone let's try and do a rough search
	$attendees = null;
  
	if(!$passcodeOptionEnabled && (rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_DISABLE_USER_SEARCH) != "Y")) {
		for($i = 3; $i >= 1; $i--) {
			$truncFirstName = rsvp_pro_chomp_name($firstName, $i);
			$attendees = $wpdb->get_results("SELECT id, firstName, lastName, rsvpStatus FROM ".PRO_ATTENDEES_TABLE." 
																			 WHERE SOUNDEX(lastName) = SOUNDEX('".esc_sql($lastName)."') AND firstName LIKE '".esc_sql($truncFirstName)."%' AND rsvpEventId = $rsvpId");
			if(count($attendees) > 0) {
				$fuzzyText = __("We could not find an exact match but could any of the below entries be you?", 'rsvp-pro-plugin');

				if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_FUZZY_MATCH_TEXT) != "") {
					$fuzzyText = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_FUZZY_MATCH_TEXT);
				}
				$output = RSVP_PRO_START_PARA."<strong>".$fuzzyText."</strong>".RSVP_PRO_END_PARA;
				foreach($attendees as $a) {
					$output .= "<form method=\"post\" action=\"$rsvp_form_action\">\r\n
									<input type=\"hidden\" name=\"rsvpStep\" value=\"foundattendee\" />\r\n
									<input type=\"hidden\" name=\"attendeeID\" value=\"".$a->id."\" />\r\n
									<p class=\"rsvpParagraph\" style=\"text-align:left;\">\r\n
							".htmlspecialchars($a->firstName." ".$a->lastName)." 
							<input type=\"submit\" value=\"RSVP\" />\r\n
							</p>\r\n</form>\r\n";
				}
				return rsvp_pro_handle_output($text, $output);
			} else {
				$i = strlen($truncFirstName);
			}
		}
	}
  
  	$notFoundText = RSVP_PRO_START_PARA."<strong>".sprintf(__('We were unable to find anyone with a name of %1$s %2$s', 'rsvp-pro-plugin'), htmlspecialchars($firstName), htmlspecialchars($lastName))."</strong>".RSVP_PRO_END_PARA;
  	if($passcodeOnlyOption) {
    	$notFoundText = RSVP_PRO_START_PARA.'<strong>'.__('We were unable to find anyone with that passcode', 'rsvp-pro-plugin').'</strong>'.RSVP_PRO_END_PARA;
  	}

  	if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_UNABLE_TO_FIND_TEXT) != "") {
  		$notFoundText = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_UNABLE_TO_FIND_TEXT);
  	}

	$notFoundText .= rsvp_pro_frontend_greeting();
	return rsvp_pro_handle_output($text, $notFoundText);
}

function rsvp_pro_editAttendee(&$output, &$text) {
	global $wpdb;
  	global $rsvpId;
	
	if(is_numeric($_POST['attendeeID']) && ($_POST['attendeeID'] > 0)) {
		// Try to find the user.
		$attendee = $wpdb->get_row($wpdb->prepare("SELECT id, firstName, lastName, rsvpStatus 
													FROM ".PRO_ATTENDEES_TABLE." 
													WHERE id = %d AND rsvpEventID = %d", $_POST['attendeeID'], $rsvpId));
		if($attendee != null) {
			$welcomeBackText = __("Welcome back", 'rsvp-pro-plugin');

			if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_WELCOME_BACK_TEXT) != "") {
				$welcomeBackText = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_WELCOME_BACK_TEXT);
			}

			$output .= RSVP_PRO_START_CONTAINER;
			$output .= RSVP_PRO_START_PARA.$welcomeBackText." ".htmlspecialchars($attendee->firstName." ".$attendee->lastName)."!".RSVP_PRO_END_PARA;
			$output .= rsvp_pro_frontend_main_form($attendee->id);
			return rsvp_pro_handle_output($text, $output.RSVP_PRO_END_CONTAINER);
		}
	}
}

function rsvp_pro_foundAttendee(&$output, &$text) {
	global $wpdb;
  	global $rsvpId;
	
	if(is_numeric($_POST['attendeeID']) && ($_POST['attendeeID'] > 0)) {
		$attendee = $wpdb->get_row($wpdb->prepare("SELECT id, firstName, lastName, rsvpStatus, 
													(SELECT COUNT(*) FROM ".PRO_ATTENDEE_SUB_EVENTS_TABLE." WHERE rsvpStatus <> 'NoResponse' AND rsvpAttendeeID = a.id) AS subStatus   
													 FROM ".PRO_ATTENDEES_TABLE." a 
													 WHERE id = %d AND rsvpEventID = %d", $_POST['attendeeID'], $rsvpId));
		if($attendee != null) {
			$output = RSVP_PRO_START_CONTAINER;
			if((strtolower($attendee->rsvpStatus) == "noresponse") && ($attendee->subStatus <= 0)) {
				$output .= RSVP_PRO_START_PARA.__("Hi", 'rsvp-pro-plugin')." ".htmlspecialchars(stripslashes($attendee->firstName." ".$attendee->lastName))."!".RSVP_PRO_END_PARA;
							
				if(trim(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_WELCOME_TEXT)) != "") {
					$output .= RSVP_PRO_START_PARA.trim(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_WELCOME_TEXT)).RSVP_PRO_END_PARA;
				} else {
					$output .= RSVP_PRO_START_PARA.__("There are a few more questions we need to ask you if you could please fill them out below to finish up the RSVP process.", 'rsvp-pro-plugin').RSVP_PRO_END_PARA;
				}
												
				$output .= rsvp_pro_frontend_main_form($attendee->id);
			} else {
				$output .= rsvp_pro_frontend_prompt_to_edit($attendee);
			}
			return rsvp_pro_handle_output($text, $output.RSVP_PRO_END_CONTAINER);
		} 
					
		return rsvp_pro_handle_output($text, rsvp_pro_frontend_greeting());
	} else {
		return rsvp_pro_handle_output($text, rsvp_pro_frontend_greeting());
	}
}
	
function rsvp_pro_frontend_thankyou_calendar_links($attendeeID) {
	global $wpdb, $rsvpId;
	$output = "";

	if((rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_SHOW_CALENDAR_LINK) == "Y") && 
		does_user_have_access_to_event($rsvpId, $attendeeID)) {
		$linkText = __("Add to your calendar.", "rsvp-pro-plugin");
		if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_CALENDAR_LINK_TEXT) != "") {
			$linkText = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_CALENDAR_LINK_TEXT);
		}
		$output .= RSVP_PRO_START_PARA;
		$output .= "<a href=\"".site_url()."?rsvp_calendar_download=".$rsvpId."\">".$linkText."</a>";
		$output .= RSVP_PRO_END_PARA;
	}

	// Get the sub-events calendar information...
	$sql = "SELECT id, eventName FROM ".PRO_EVENT_TABLE." WHERE parentEventID = %d";
	$subevents = $wpdb->get_results($wpdb->prepare($sql, $rsvpId));
	foreach($subevents as $se) {
		if((rsvp_pro_get_sub_event_option($se->id, RSVP_PRO_OPTION_SHOW_CALENDAR_LINK) == "Y") && 
			does_user_have_access_to_event($se->id, $attendeeID)) {
			$linkText = __("Add to your calendar.", "rsvp-pro-plugin");
			if(rsvp_pro_get_sub_event_option($se->id, RSVP_PRO_OPTION_CALENDAR_LINK_TEXT) != "") {
				$linkText = rsvp_pro_get_sub_event_option($se->id, RSVP_PRO_OPTION_CALENDAR_LINK_TEXT);
			}

			$output .= RSVP_PRO_START_PARA;
			$output .= "<a href=\"".site_url()."?rsvp_calendar_download=".$se->id."\">".$linkText."</a>";
			$output .= RSVP_PRO_END_PARA;
		}
	}

	return $output;
}

function rsvp_pro_frontend_thankyou($thankYouPrimary, $thankYouAssociated, $rsvpStatus, $attendeeID) {
  	global $rsvpId;
  	$filterParams = array("attendeeID" => $attendeeID, "rsvpStatus" => $rsvpStatus);
  
	$customTy = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_THANKYOU);
  	$customNoVerbiage = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_NOT_COMING);

  	$customTy = apply_filters("rsvp_pro_thank_you_text", $customTy, $filterParams);
  	$customNoVerbiage = apply_filters("rsvp_pro_no_thank_you_text", $customNoVerbiage, $filterParams);
  	
  	if(($rsvpStatus == "No") && !empty($customNoVerbiage)) {
    	return nl2br($customNoVerbiage);
  	} else if(!empty($customTy)) {
  		$output = nl2br($customTy);
  		$output .= rsvp_pro_frontend_thankyou_calendar_links($attendeeID);
  		
		return $output;
	} else {    
    	$tyText = __("Thank you", 'rsvp-pro-plugin');
    	if(!empty($thankYouPrimary)) {
      		$tyText .= " ".htmlspecialchars($thankYouPrimary);
    	}
    	$tyText .= __(" for RSVPing.", 'rsvp-pro-plugin');
    
    	if(count($thankYouAssociated) > 0) {
      		$tyText .= __(" You have also RSVPed for - ", 'rsvp-pro-plugin');
      		foreach($thankYouAssociated as $name) {
        		$tyText .= htmlspecialchars(" ".$name).", ";
      		}
      		$tyText = rtrim(trim($tyText), ",").".";
    	}
    	$output = RSVP_PRO_START_CONTAINER.RSVP_PRO_START_PARA.$tyText.RSVP_PRO_END_PARA;
    	$output .= rsvp_pro_frontend_thankyou_calendar_links($attendeeID);

    	$output .= RSVP_PRO_END_CONTAINER;
		return $output;
	}
}

function rsvp_pro_frontend_new_attendee_thankyou($thankYouPrimary, $thankYouAssociated, $rsvpStatus, $password = "") {
  	global $rsvpId;

  	$customTy = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_THANKYOU);
  	$customNoVerbiage = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_NOT_COMING);

  	$customTy = apply_filters("rsvp_pro_thank_you_text", $customTy, $filterParams);
  	$customNoVerbiage = apply_filters("rsvp_pro_no_thank_you_text", $customNoVerbiage, $filterParams);

  	$thankYouText = "";

  	if(($rsvpStatus == "No") && !empty($customNoVerbiage)) {
    	$thankYouText .= nl2br($customNoVerbiage);
  	} else if(!empty($customTy)) {
		$thankYouText .= nl2br($customTy);
	} else {
		$thankYouText .= __("Thank you ", 'rsvp-pro-plugin');
	  	if(!empty($thankYouPrimary)) {
	    	$thankYouText .= htmlspecialchars($thankYouPrimary);
	  	}
	  	$thankYouText .= __(" for RSVPing.", "rsvp-pro-plugin");	
	}
  	
  	if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_CANT_EDIT) != "Y") {
    	$thankYouText .= "<p>".__("To modify your RSVP just come back ".
                    "to this page and enter in your first and last name.", 'rsvp-pro-plugin');
    	if(!empty($password)) {
      		$thankYouText .= __(" You will also need to know your password which is", 'rsvp-pro-plugin').
                      		" - <strong>$password</strong>";
    	}
    	$thankYouText .= "</p>\r\n";
  	}
  
  	if(count($thankYouAssociated) > 0) {
    	$thankYouText .= __("<br /><br />You have also RSVPed for - ", 'rsvp-pro-plugin');
    	foreach($thankYouAssociated as $name) {
      		$thankYouText .= htmlspecialchars(" ".$name).", ";
    	}
    	$thankYouText = rtrim(trim($thankYouText), ",").".";
  	}

	return RSVP_PRO_START_CONTAINER.RSVP_PRO_START_PARA.$thankYouText.RSVP_PRO_END_PARA.RSVP_PRO_END_CONTAINER;
}

function rsvp_pro_chomp_name($name, $maxLength) {
	for($i = $maxLength; $maxLength >= 1; $i--) {
		if(strlen($name) >= $i) {
			return substr($name, 0, $i);
		}
	}
}

function rsvp_pro_BeginningFormField($id, $additionalClasses) {
  return "<div ".(!empty($id) ? "id=\"$id\"" : "")." class=\"rsvpFormField ".(!empty($additionalClasses) ? $additionalClasses : "")."\">";
}

function rsvp_pro_frontend_greeting() {
 	global $rsvpId;
	global $rsvp_first_name, $rsvp_last_name, $rsvp_passcode;

  $rsvp_form_action = htmlspecialchars(rsvp_pro_getCurrentPageURL());
	$customGreeting = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_GREETING);
	$output = RSVP_PRO_START_PARA.__("Please enter your first and last name to RSVP.", 'rsvp-pro-plugin').RSVP_PRO_END_PARA;
	$firstName = "";
	$lastName = "";
	$passcode = "";
	if(!empty($rsvp_first_name)) {
		$firstName = $rsvp_first_name;
	}
	if(!empty($rsvp_last_name)) {
		$lastName = $rsvp_last_name;
	}
	if(!empty($rsvp_passcode)) {
		$passcode = $rsvp_passcode;
	}
	if(!empty($customGreeting)) {
		$output = RSVP_PRO_START_PARA.nl2br($customGreeting).RSVP_PRO_END_PARA;
	} 
  
  	$output .= RSVP_PRO_START_CONTAINER;

  	if(rsvp_pro_frontend_max_limit_for_all_events()) {
  		return rsvp_pro_handle_max_limit_reached_message($rsvpId);
  	} 
  
	if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_OPEN_REGISTRATION) == "Y") {
	    $buttonText = __("New Attendee Registration", "rsvp-pro-plugin");
	    
	    if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_NEW_ATTENDEE_BUTTON_TEXT) != "") {
	      $buttonText = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_NEW_ATTENDEE_BUTTON_TEXT);
	    }
	    $output .= "<form name=\"rsvpNew\" method=\"post\" id=\"rsvpNew\" action=\"$rsvp_form_action\">\r\n";
	    $output .= "	<input type=\"hidden\" name=\"rsvpStep\" value=\"newattendee\" />";
	      $output .= "<input type=\"submit\" value=\"$buttonText\" />\r\n";
	    $output .= "</form>\r\n";
	    
	    if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_CANT_EDIT) != "Y") {
	      $output .= "<hr />";
	      $tmpText = __("Need to modify your registration? Start with the below form.", "rsvp-pro-plugin");
	      if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_MODIFY_REGISTRATION_TEXT) != "") {
	      	$tmpText = stripslashes(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_MODIFY_REGISTRATION_TEXT));
	      }
	      $output .= RSVP_PRO_START_PARA.$tmpText.RSVP_PRO_END_PARA;
	    }
	}
	if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_CANT_EDIT) != "Y") {
	  	$output .= "<form name=\"rsvp\" method=\"post\" id=\"rsvp\" action=\"$rsvp_form_action\" autocomplete=\"off\">\r\n";
	  	$output .= "	<input type=\"hidden\" name=\"rsvpStep\" value=\"find\" />";
	    if(!rsvp_pro_require_only_passcode_to_register($rsvpId) && 
         (rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_ATTENDEE_LOOKUP_VIA_EMAIL) != "Y")) {
	    	$firstNameLabel = __("First Name", 'rsvp-pro-plugin');
	    	if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_FIRST_NAME_LABEL) != "") {
	    		$firstNameLabel = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_FIRST_NAME_LABEL);
	    	}

	    	$lastNameLabel = __("Last Name", 'rsvp-pro-plugin');
	    	if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_LAST_NAME_LABEL) != "") {
	    		$lastNameLabel = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_LAST_NAME_LABEL);
	    	}

	  	  $output .= rsvp_pro_BeginningFormField("", "")."<label for=\"firstName\">".$firstNameLabel.":</label> 
	  								 <input type=\"text\" name=\"firstName\" id=\"firstName\" size=\"30\" value=\"".esc_html($firstName)."\" class=\"required\" />".RSVP_PRO_END_FORM_FIELD;
	  	  $output .= rsvp_pro_BeginningFormField("", "")."<label for=\"lastName\">".$lastNameLabel.":</label> 
	  								 <input type=\"text\" name=\"lastName\" id=\"lastName\" size=\"30\" value=\"".esc_html($lastName)."\" class=\"".((rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_LAST_NAME_NOT_REQUIRED) != "Y") ? "required" : "")."\" />".RSVP_PRO_END_FORM_FIELD;
	    }

      if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_ATTENDEE_LOOKUP_VIA_EMAIL) == "Y") {
        $emailLabel = __("Email", "rsvp-pro-plugin");
        if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_EMAIL_LABEL) != "") {
          $emailLabel = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_EMAIL_LABEL);
        }

        $output .= rsvp_pro_BeginningFormField("", "")."<label for=\"email\">".esc_html($emailLabel).":</label>
                    <input type=\"text\" name=\"email\" id=\"email\" size=\"30\" class=\"required\" />".RSVP_PRO_END_FORM_FIELD;
      }

	  	if(rsvp_pro_require_passcode($rsvpId)) {
	  		$passcodeLabel = __("Passcode", 'rsvp-pro-plugin');
	  		if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_PASSCODE_LABEL) != "") {
	  			$passcodeLabel = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_PASSCODE_LABEL);
	  		}
	  		$output .= rsvp_pro_BeginningFormField("", "")."<label for=\"passcode\">".$passcodeLabel.":</label> 
	  									 <input type=\"password\" name=\"passcode\" id=\"passcode\" size=\"30\" value=\"".esc_html($passcode)."\" class=\"required\" autocomplete=\"off\" />".RSVP_PRO_END_FORM_FIELD;
	  	}

	  	$buttonText = __("Complete your RSVP!", 'rsvp-pro-plugin');

	  	if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_COMPLETE_BUTTON_TEXT) != "") {
	  		$buttonText = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_COMPLETE_BUTTON_TEXT);
	  	}

	  	$output .= RSVP_PRO_START_PARA."<input type=\"submit\" value=\"".$buttonText."\" />".RSVP_PRO_END_PARA;
	  	$output .= "</form>\r\n";
	}
	$output .= RSVP_PRO_END_CONTAINER;
	return $output;
}

function rsvp_pro_retrieveEmailBodyContent($attendeeID, $attendee) {
	global $wpdb, $rsvpId;
	$body = "";
	$hasAccessToMainEvent = does_user_have_access_to_event($rsvpId, $attendeeID);
  	if($hasAccessToMainEvent): 
		$body .= "--== ".rsvp_pro_get_event_information($rsvpId, RSVP_PRO_INFO_EVENT_NAME)." ==--\r\n";
		$body .= "RSVP: ".rsvp_pro_get_event_status($attendee->rsvpStatus, $rsvpId, false)."\r\n";
  	endif; // if($hasAccessToMainEvent...

	if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_SHOW_SALUTATION) == "Y") {
		$body .= "Salutation: ".$attendee->salutation."\r\n";
	}
	  
	if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_SHOW_SUFFIX) == "Y") {
		$body .= "Suffix: ".$attendee->suffix."\r\n";
	}
	  
	if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_HIDE_NOTE) != "Y") {
		$body .= "Note: ".stripslashes($attendee->note)."\r\n";
	}
	  
	if(!empty($attendee->passcode) && rsvp_pro_require_passcode($rsvpId)) {
		$body .= "Passcode: ".stripslashes($attendee->passcode)."\r\n";
	}

  	if($hasAccessToMainEvent): 
		$sql = "SELECT q.id, question, answer, q.sortOrder FROM ".PRO_QUESTIONS_TABLE." q 
	    LEFT JOIN ".PRO_QUESTION_TYPE_TABLE." qt ON qt.id = q.questionTypeID 
			LEFT JOIN ".PRO_ATTENDEE_ANSWERS." ans ON q.id = ans.questionID AND ans.attendeeID = %d 
	    WHERE q.rsvpEventID = %d 
			AND (q.permissionLevel = 'public' 
			  OR (q.permissionLevel = 'private' AND q.id IN (SELECT questionID FROM ".PRO_QUESTION_ATTENDEES_TABLE." WHERE attendeeID = %d)))
	    AND qt.questionType NOT IN ('readonly', 'hidden') 
	    ORDER BY sortOrder, id";
		$aRs = $wpdb->get_results($wpdb->prepare($sql, $attendeeID, $rsvpId, $attendeeID));
		if(count($aRs) > 0) {
			foreach($aRs as $a) {
	      		$body .= stripslashes($a->question).": ".stripslashes($a->answer)."\r\n";
			}
		}
  	endif; 

	$sql = "SELECT eventName, e.id, rsvpStatus 
		FROM ".PRO_EVENT_TABLE." e 
		INNER JOIN ".PRO_ATTENDEE_SUB_EVENTS_TABLE." se ON se.rsvpEventID = e.id AND se.rsvpAttendeeID = %d 
		WHERE e.parentEventID = %d AND ((e.event_access != '".RSVP_PRO_PRIVATE_EVENT_ACCESS."') OR 
		se.rsvpAttendeeID IN (SELECT rsvpAttendeeID FROM ".PRO_EVENT_ATTENDEE_TABLE." WHERE rsvpEventID = e.id))";
  	$subevents = $wpdb->get_results($wpdb->prepare($sql, $attendeeID, $rsvpId));
  	foreach($subevents as $se) {
	    $body .= "\r\n--== ".$se->eventName." ==--\r\n";
	    $body .= "RSVP: ".rsvp_pro_get_event_status($se->rsvpStatus, $se->id, true)."\r\n";

	    $sql = "SELECT q.id, question, answer, q.sortOrder FROM ".PRO_QUESTIONS_TABLE." q 
	    LEFT JOIN ".PRO_QUESTION_TYPE_TABLE." qt ON qt.id = q.questionTypeID 
			LEFT JOIN ".PRO_ATTENDEE_ANSWERS." ans ON q.id = ans.questionID AND ans.attendeeID = %d 
	    WHERE q.rsvpEventID = %d 
			AND (q.permissionLevel = 'public' 
			  OR (q.permissionLevel = 'private' AND q.id IN (SELECT questionID FROM ".PRO_QUESTION_ATTENDEES_TABLE." WHERE attendeeID = %d)))
	    AND qt.questionType NOT IN ('readonly', 'hidden') 
	    ORDER BY sortOrder, id";
		$aRs = $wpdb->get_results($wpdb->prepare($sql, $attendeeID, $se->id, $attendeeID));
		if(count($aRs) > 0) {
			foreach($aRs as $a) {
	      		$body .= stripslashes($a->question).": ".stripslashes($a->answer)."\r\n";
			}
		}
  	}

	$sql = "SELECT firstName, lastName, rsvpStatus, id, passcode, note, salutation, suffix FROM ".PRO_ATTENDEES_TABLE." 
	 	WHERE id IN (SELECT attendeeID FROM ".PRO_ASSOCIATED_ATTENDEES_TABLE." WHERE associatedAttendeeID = %d) 
			OR id in (SELECT associatedAttendeeID FROM ".PRO_ASSOCIATED_ATTENDEES_TABLE." WHERE attendeeID = %d) 
      AND rsvpEventID = %d";

	$associations = $wpdb->get_results($wpdb->prepare($sql, $attendeeID, $attendeeID, $rsvpId));
  	if(count($associations) > 0) {
		foreach($associations as $a) {
		  	$hasAccessToMainEvent = does_user_have_access_to_event($rsvpId, $a->id);
	      	$body .= "\r\n\r\n--== Associated Attendees ==--\r\n";
	      	$body .= stripslashes($a->firstName." ".$a->lastName);
	      	if($hasAccessToMainEvent) {
	        	$body .= " RSVP: ".rsvp_pro_get_event_status($a->rsvpStatus, $rsvpId, false)."\r\n";
	      	}
      
	      	if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_SHOW_SALUTATION) == "Y") {
	        	$body .= "Salutation: ".$a->salutation."\r\n";
	      	}
  
      		if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_SHOW_SUFFIX) == "Y") {
        		$body .= "Suffix: ".$a->suffix."\r\n";
      		}

      		if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_HIDE_NOTE) != "Y") {
        		$body .= "Note: ".stripslashes($a->note)."\r\n";
      		}
  
      		if(!empty($a->passcode) && rsvp_pro_require_passcode($rsvpId)) {
        		$body .= "Passcode: ".stripslashes($a->passcode)."\r\n";
      		}

	      	if($hasAccessToMainEvent): 
	      		// Get Questions...
				$sql = "SELECT q.id, question, answer, q.sortOrder FROM ".PRO_QUESTIONS_TABLE." q 
	        			LEFT JOIN ".PRO_QUESTION_TYPE_TABLE." qt ON qt.id = q.questionTypeID 
						LEFT JOIN ".PRO_ATTENDEE_ANSWERS." ans ON q.id = ans.questionID AND ans.attendeeID = %d 
	        			WHERE q.rsvpEventID = %d 
	    					AND (q.permissionLevel = 'public' 
	    		  			OR (q.permissionLevel = 'private' AND q.id IN (SELECT questionID FROM ".PRO_QUESTION_ATTENDEES_TABLE." WHERE attendeeID = %d)))
	        			AND qt.questionType NOT IN ('readonly', 'hidden') 
	        			ORDER BY sortOrder, id";
				$aRs = $wpdb->get_results($wpdb->prepare($sql, $a->id, $rsvpId, $a->id));
				if(count($aRs) > 0) {
					foreach($aRs as $ans) {
	          			$body .= stripslashes($ans->question).": ".stripslashes($ans->answer)."\r\n";
					}
	        		$body .= "\r\n";
				}
	      	endif; 

      		$sql = "SELECT eventName, e.id, rsvpStatus 
        			FROM ".PRO_EVENT_TABLE." e 
        			INNER JOIN ".PRO_ATTENDEE_SUB_EVENTS_TABLE." se ON se.rsvpEventID = e.id AND se.rsvpAttendeeID = %d 
        			WHERE e.parentEventID = %d AND ((e.event_access != '".RSVP_PRO_PRIVATE_EVENT_ACCESS."') OR 
        				se.rsvpAttendeeID IN (SELECT rsvpAttendeeID FROM ".PRO_EVENT_ATTENDEE_TABLE." WHERE rsvpEventID = e.id))";
      		$subevents = $wpdb->get_results($wpdb->prepare($sql, $a->id, $rsvpId));
      		foreach($subevents as $se) {
		        $body .= "\r\n--== ".$se->eventName." ==--\r\n";
		        $body .= "RSVP: ".rsvp_pro_get_event_status($se->rsvpStatus, $se->id, true)."\r\n";
        
		        $sql = "SELECT q.id, question, answer, q.sortOrder FROM ".PRO_QUESTIONS_TABLE." q 
		        LEFT JOIN ".PRO_QUESTION_TYPE_TABLE." qt ON qt.id = q.questionTypeID 
						LEFT JOIN ".PRO_ATTENDEE_ANSWERS." ans ON q.id = ans.questionID AND ans.attendeeID = %d 
		        WHERE q.rsvpEventID = %d 
		    		AND (q.permissionLevel = 'public' 
		    		  OR (q.permissionLevel = 'private' AND q.id IN (SELECT questionID FROM ".PRO_QUESTION_ATTENDEES_TABLE." WHERE attendeeID = %d)))
		        AND qt.questionType NOT IN ('readonly', 'hidden') 
		        		ORDER BY sortOrder, id";
				$aRs = $wpdb->get_results($wpdb->prepare($sql, $a->id, $se->id, $a->id));
				if(count($aRs) > 0) {
					foreach($aRs as $ans) {
          				$body .= stripslashes($ans->question).": ".stripslashes($ans->answer)."\r\n";
					}
        			$body .= "\r\n";
				}
      		}
		}
  	}
  
  	return $body;
}

function rsvp_pro_attendeelist_frontend_handler($rsvpId) {
  global $wpdb; 
  
  $hasSubEvents = false;
  $hideRsvpStatus = false;
  $output = "";
  $subeventCount = 0;
  $questions = null;
  $qIds = array();

  if(is_array(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_ATTENDEE_LIST_CUSTOM_QUESTIONS)) && 
  	 (count(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_ATTENDEE_LIST_CUSTOM_QUESTIONS)) > 0)) {
  	$ids = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_ATTENDEE_LIST_CUSTOM_QUESTIONS);

  	// Get the valid question IDs
  	foreach($ids as $id) {
  		if(is_numeric($id) && ($id > 0)) {
  			$qIds[] = $id;
  		}
  	}
  	
  	$sql = "SELECT id, question FROM ".PRO_QUESTIONS_TABLE." WHERE rsvpEventID = %d AND id IN (".implode(",", $qIds).")";
  	$questions = $wpdb->get_results($wpdb->prepare($sql, $rsvpId));
  }

  // Check to see if it has subevents and if so handle the query slightly differently
  $sql = "SELECT eventName, id FROM ".PRO_EVENT_TABLE." WHERE parentEventID = %d ORDER BY id";
  $subevents = $wpdb->get_results($wpdb->prepare($sql, $rsvpId));
  if(count($subevents) > 0) {
    $hasSubEvents = true;
  }
  $fieldList = "SELECT a.id, firstName, lastName, a.rsvpStatus, e.eventName ";
  $sql = " FROM ".PRO_ATTENDEES_TABLE." a
    INNER JOIN ".PRO_EVENT_TABLE." e ON e.id = a.rsvpEventId ";
    
  foreach($subevents as $se) {
    $fieldList .= ", se".$subeventCount.".eventName AS se".$subeventCount."EventName, sea".$subeventCount.".rsvpStatus AS sea".$subeventCount."RsvpStatus ";
    
    $sql .= " LEFT JOIN ".PRO_EVENT_TABLE." se$subeventCount ON se".$subeventCount.".id = ".$se->id.
            " LEFT JOIN ".PRO_ATTENDEE_SUB_EVENTS_TABLE." sea$subeventCount ON sea".$subeventCount.".rsvpEventId = ".$se->id." AND sea".$subeventCount.".rsvpAttendeeID = a.id ";
    
    $subeventCount++;
  }
    
  $sql .= " WHERE a.rsvpEventId = %d ";

  if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_ATTENDEE_LIST_FILTER) != "") {
  	$sql .= " AND a.rsvpStatus = '".esc_sql(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_ATTENDEE_LIST_FILTER))."' ";
  }

  $sortBy = "a.firstName";

  if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_ATTENDEE_LIST_SORT_ORDER) != "") {
  	$sortBy = "a.".rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_ATTENDEE_LIST_SORT_ORDER);
  }

  if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_ATTENDEE_LIST_HIDE_STATUS) == "Y") {
  	$hideRsvpStatus = true;
  }

  $sql .= " ORDER BY $sortBy ";
  $attendees = $wpdb->get_results($wpdb->prepare($fieldList.$sql, $rsvpId));
  if(count($attendees) > 0) {
    $output .= "<a name=\"rsvpAttendees\"></a>";
    $output .= "<table>
      <thead>
        <tr>
          <th>".__("Name", "rsvp-pro-plugin")."</th>";

    if(!$hideRsvpStatus) {
        $output .= "<th>".__("RSVP Status", "rsvp-pro-plugin")."</th>";
    }

    if(($questions != null) && (count($questions) > 0)) {
    	foreach($questions as $q) {
    		$output .= "<th>".esc_html($q->question)."</th>\r\n";
    	}
    }
    $output .= "</tr>
      </thead>
      <tbody>";
    foreach($attendees as $a) {
        $output .= "<tr>\r\n
          		<td>".esc_html(stripslashes($a->firstName." ".$a->lastName))."</td>\r\n";

    	if(!$hideRsvpStatus) {
     		$output.= "<td>";
      		if($hasSubEvents) {
        		$output .= esc_html($a->eventName)." - ".rsvp_pro_humanize_rsvp_status($a->rsvpStatus);
        		for($i = 0; $i < $subeventCount; $i++) {
          			$status = $a->{"sea".$i."RsvpStatus"};
          			if(empty($status)) {
            			$status = "NoResponse";
          			}
          			$output .= "<br />\r\n
            		&nbsp; ".esc_html($a->{"se".$i."EventName"})." - ".rsvp_pro_humanize_rsvp_status($status);
        		}
      		} else {
        		$output .= rsvp_pro_humanize_rsvp_status($a->rsvpStatus);
      		}
    		$output .= "</td>\r\n";
    	}

    	if(($questions != null) && (count($questions) > 0)) {
    		$sql = "SELECT aa.answer FROM ".PRO_QUESTIONS_TABLE." q 
    				LEFT JOIN ".PRO_ATTENDEE_ANSWERS." aa ON aa.questionID = q.id AND attendeeID = %d 
    				WHERE q.id IN(".implode(",", $qIds).")";
    		$answers = $wpdb->get_results($wpdb->prepare($sql, $a->id));
    		foreach($answers as $ans) {
    			$output .= "<td>".(($ans->answer != "") ? esc_html(stripslashes($ans->answer)) : "&nbsp;")."</td>\r\n";
    		}
    	}
        $output .= "</tr>\r\n";
    }
    $output .= "</tbody>
    		</table>";
  }  
  
  return $output;
}

/* 
 * Checks to see if the max limit is set and if so returns true or false depending on if the limit is hit
 */
function rsvp_pro_frontend_max_limit_hit($rsvpEventId) {
	global $wpdb, $attendeeCount;

	$maxLimit = array();
	$isSubEvent = rsvp_pro_is_sub_event($rsvpEventId);
	$function = "rsvp_pro_get_event_option";

	if($isSubEvent) {
		$function = "rsvp_pro_get_sub_event_option";
	}

	$tmpLimit = $function($rsvpEventId, RSVP_PRO_OPTION_EVENT_COUNT_LIMIT);

	if(($function($rsvpEventId, RSVP_PRO_OPTION_ENABLE_WAITLIST) == "Y") && ($function($rsvpEventId, RSVP_PRO_OPTION_WAITLIST_YES_UNAVAILABLE) == "Y") && ($function($rsvpEventId, RSVP_PRO_OPTION_WAITLIST_PERM_SWITCH) == "Y")) {
		return true;
	}
	
	if(is_numeric($tmpLimit) && ($tmpLimit > 0)) {
		$maxLimit[$rsvpEventId] = $tmpLimit;	
	}
	
	if(count($maxLimit) > 0) {
		// We will cache the attendee count per request to minimize query hits. This isn't great 
		// as it could cause a race condition but this is a trade-off we will have to take
		if(!isset($attendeeCount[$rsvpEventId]) || ($attendeeCount[$rsvpEventId] <= 0)) {
			if($isSubEvent) {
				$sql = "SELECT COUNT(*) FROM ".PRO_ATTENDEE_SUB_EVENTS_TABLE." ase
				JOIN ".PRO_ATTENDEES_TABLE." a ON a.id = ase.rsvpAttendeeID 
				WHERE ase.rsvpStatus = 'Yes' AND ase.rsvpEventID = %d";	
			} else {
				$sql = "SELECT COUNT(*) FROM ".PRO_ATTENDEES_TABLE." WHERE rsvpStatus = 'Yes' AND rsvpEventID = %d";	
			}
			
			$attendeeCount[$rsvpEventId] = $wpdb->get_var($wpdb->prepare($sql, $rsvpEventId));
		}

		if($attendeeCount[$rsvpEventId] >= $maxLimit[$rsvpEventId]) {
			if(($function($rsvpEventId, RSVP_PRO_OPTION_ENABLE_WAITLIST) == "Y") && ($function($rsvpEventId, RSVP_PRO_OPTION_WAITLIST_PERM_SWITCH) == "Y") && ($function($rsvpEventId, RSVP_PRO_OPTION_WAITLIST_YES_UNAVAILABLE) != "Y")) {
				rsvp_pro_set_yes_unavailable($rsvpEventId);
			}
			return true;
		} else {
			return false;
		}
	}

	return false;
}

/* 
 * Handles the messaging for when a max limit has been reached and we need to stop moving forward. 
 */
function rsvp_pro_handle_max_limit_reached_message($rsvpEventId) {
	$isSubEvent = rsvp_pro_is_sub_event($rsvpEventId);

	$function = "rsvp_pro_get_event_option";

	if($isSubEvent) {
		$function = "rsvp_pro_get_sub_event_option";
	}

	$maxCount = $function($rsvpEventId, RSVP_PRO_OPTION_EVENT_COUNT_LIMIT);
	$limitMessage = __("The maximum limit of %d has been reached for this event.", "rsvp-pro-plugin");
	if($function($rsvpEventId, RSVP_PRO_OPTION_MAX_COUNT_REACHED_TEXT) != "") {
		$limitMessage = $function($rsvpEventId, RSVP_PRO_OPTION_MAX_COUNT_REACHED_TEXT);
	}
	$limitMessage = sprintf($limitMessage, $maxCount);

	$output = RSVP_PRO_START_PARA.$limitMessage.RSVP_PRO_END_PARA;
	return $output;
}

function rsvp_pro_handle_waitlist_message($rsvpEventId) {
	$isSubEvent = rsvp_pro_is_sub_event($rsvpEventId);
	$function = "rsvp_pro_get_event_option";

	if($isSubEvent) {
		$function = "rsvp_pro_get_sub_event_option";
	}
	$message = "";
	if($function($rsvpEventId, RSVP_PRO_OPTION_ENABLE_WAITLIST) == "Y") {
		if($function($rsvpEventId, RSVP_PRO_OPTION_WAITLIST_TEXT) != "") {
			$message = $function($rsvpEventId, RSVP_PRO_OPTION_WAITLIST_TEXT);
		} else {
			$message = __("This event has a waitlist available.", "rsvp-pro-plugin");
		}
	}
	
	return $message;
}

function rsvp_pro_frontend_max_limit_for_all_events() {
	global $wpdb, $rsvpId, $attendeeCount;

	$maxLimits = array();

	$isParentEvent = rsvp_pro_is_parent_event($rsvpId);
	$tmpLimit = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_EVENT_COUNT_LIMIT);
	$subEvents = array();
	if(is_numeric($tmpLimit) && ($tmpLimit > 0) && (rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_ENABLE_WAITLIST) != "Y")) {
		$maxLimits[$rsvpId] = $tmpLimit;
	}

	if($isParentEvent) {
		// Get all the sub-events max limits or if they don't have a max limit 
		// return false right away...
		$subEvents = $wpdb->get_results($wpdb->prepare("SELECT id FROM ".PRO_EVENT_TABLE." WHERE parentEventID = %d", $rsvpId));
		foreach($subEvents as $se) {
			$tmpLimit = rsvp_pro_get_sub_event_option($se->id, RSVP_PRO_OPTION_EVENT_COUNT_LIMIT);
			if(is_numeric($tmpLimit) && ($tmpLimit > 0) && (rsvp_pro_get_sub_event_option($se->id, RSVP_PRO_OPTION_ENABLE_WAITLIST) != "Y")) {
				$maxLimits[$se->id] = $tmpLimit;
			} else {
				// No max limit for this sub-event no need to go on as this event will always 
				// be able to take attendees
				return false;
			}
		}
	}

	if(count($maxLimits) > 0) {
		if(!isset($attendeeCount[$rsvpId]) || ($attendeeCount[$rsvpId] < 0)) {
			$sql = "SELECT COUNT(*) FROM ".PRO_ATTENDEES_TABLE." WHERE rsvpStatus = 'Yes' AND rsvpEventID = %d";
			$attendeeCount[$rsvpId] = $wpdb->get_var($wpdb->prepare($sql, $rsvpId));
			if((rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_ENABLE_WAITLIST) == "Y") && (rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_WAITLIST_PERM_SWITCH) == "Y") && (rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_WAITLIST_YES_UNAVAILABLE)== "Y")) {
				$attendeeCount[$rsvpId] = $maxLimits[$rsvpId];
			}
		}

		if($isParentEvent) {
			foreach($subEvents as $se) {
				$sql = "SELECT COUNT(*) FROM ".PRO_ATTENDEE_SUB_EVENTS_TABLE." ase
				JOIN ".PRO_ATTENDEES_TABLE." a ON a.id = ase.rsvpAttendeeID 
				WHERE ase.rsvpStatus = 'Yes' AND ase.rsvpEventID = %d";	
				$attendeeCount[$se->id] = $wpdb->get_var($wpdb->prepare($sql, $se->id));
				if((rsvp_pro_get_sub_event_option($se->id, RSVP_PRO_OPTION_ENABLE_WAITLIST) == "Y") && (rsvp_pro_get_sub_event_option($se->id, RSVP_PRO_OPTION_WAITLIST_PERM_SWITCH) == "Y") && (rsvp_pro_get_sub_event_option($se->id, RSVP_PRO_OPTION_WAITLIST_YES_UNAVAILABLE)== "Y")) {
					$attendeeCount[$se->id] = $maxLimits[$se->id];
				}
			}
		}
		
		foreach($attendeeCount as $key=>$val) {
			if($maxLimits[$key] > $val) {
				return false;
			}
		}

		return true;
	}

	return false;
}

function rsvp_pro_autologin_frontend($rsvpId) {
	global $wpdb;

	$foundAttendeeId = 0;
	if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_AUTO_LOGIN_ATTENDEE) == "Y") {
		$current_user = wp_get_current_user();
		if ( ($current_user instanceof WP_User) && ($current_user->ID > 0)) {
			$sql = "SELECT id FROM ".PRO_ATTENDEES_TABLE." WHERE email = %s AND rsvpEventID = %d";
			$aId = $wpdb->get_var($wpdb->prepare($sql, $current_user->user_email, $rsvpId));

			if($aId > 0) {
				$foundAttendeeId = $aId;
			}
		}
	}

	return $foundAttendeeId;
}

function rsvp_pro_frontend_handle_email_notifications($attendeeID, $rsvpId) {
	global $wpdb; 

	$email = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_NOTIFY_EMAIL);
    
    if($email == "") {
      $email = get_option("admin_email");
    }
    
	if((rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_NOTIFY_ON_RSVP) == "Y") && ($email != "")) {
		$sql = "SELECT firstName, lastName, rsvpStatus, note, passcode, 
			salutation, suffix FROM ".PRO_ATTENDEES_TABLE." WHERE id= %d AND rsvpEventID = %d";
		$attendee = $wpdb->get_results($wpdb->prepare($sql, $attendeeID, $rsvpId));
		if(count($attendee) > 0) {
			$body = "Hello, \r\n\r\n";
			$attendee = $attendee[0];
			$body .= stripslashes($attendee->firstName)." ".stripslashes($attendee->lastName).
							 " has submitted their RSVP "; 
    		$body .= ".\r\n";
			
    		$body .= rsvp_pro_retrieveEmailBodyContent($attendeeID, $attendee);
    
    		$headers = array('Content-Type: text/html; charset=UTF-8');
			if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_EMAIL_FROM) != "") {
	          $headers[] = 'From: '. rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_EMAIL_FROM);		
	        }
    
			wp_mail($email, "New RSVP Submission", nl2br($body), $headers);
		}
	}
    
    if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_GUEST_EMAIL_CONFIRMATION) == "Y") {
  		$sql = "SELECT firstName, lastName, email, rsvpStatus, passcode, salutation, suffix 
  			FROM ".PRO_ATTENDEES_TABLE." WHERE id= %d AND rsvpEventID = %d AND email != ''";
  		$attendee = $wpdb->get_results($wpdb->prepare($sql, $attendeeID, $rsvpId));

  		if(count($attendee) > 0) {
  			$attendee = $attendee[0];
  			$body = "Hello "; 
  			if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_SHOW_SALUTATION) == "Y") {
  				$body .= stripslashes($attendee->salutation)." ";
  			}
  			$body .= stripslashes($attendee->firstName)." ".stripslashes($attendee->lastName);

  			if((rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_SHOW_SUFFIX) == "Y") && ($attendee->suffix != "")) {
  				$body .= " ".stripslashes($attendee->suffix);
  			}

  			$body .= ", \r\n\r\n";
						
	        if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_EMAIL_TEXT) != "") {
	          $body .= "\r\n";
	          $body .= rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_EMAIL_TEXT);
	          $body .= "\r\n";
	        }
            
	        $body .= rsvp_pro_retrieveEmailBodyContent($attendeeID, $attendee);
        
	        $headers = array('Content-Type: text/html; charset=UTF-8');
	        if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_EMAIL_FROM) != "") {
	          $headers[] = 'From: '. rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_EMAIL_FROM);		
	        }

	        if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_EMAIL_CC_ASSOCIATED) == "Y") {
	        	$sql = "SELECT email FROM ".PRO_ATTENDEES_TABLE." a 
	        		JOIN ".PRO_ASSOCIATED_ATTENDEES_TABLE." aa ON aa.attendeeID = a.id 
	        		WHERE associatedAttendeeID = %d AND a.rsvpEventID = %d AND email != ''";
	        	$aa = $wpdb->get_results($wpdb->prepare($sql, $attendeeID, $rsvpId));
	        	if(count($aa) > 0) {
	        		$ccEmails = "";
	        		foreach($aa as $a) {
	        			$headers[] = 'Cc: '.stripslashes($a->email);
	        		}
	        	}
	        }

	        if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_EMAIL_BCC_ADDRESS) != "") {
	        	$bccs = explode(";", rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_EMAIL_BCC_ADDRESS));
	        	foreach($bccs as $bcc) {
	        		$headers[] = 'Bcc: '.trim($bcc);	
	        	}
	        }

	        $subject = __("RSVP Confirmation", "rsvp-pro-plugin");
	        if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_EMAIL_SUBJECT) != "") {
	        	$subject = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_EMAIL_SUBJECT);
	        }
	        
	        wp_mail($attendee->email, $subject, nl2br($body), $headers);
      }
    }
}

function rsvp_pro_set_yes_unavailable($rsvpEventId) {
	global $wpdb;
	$sql = "SELECT eventName, open_date, close_date, options, parentEventID, event_access  
              FROM ".PRO_EVENT_TABLE." WHERE id = %d";
	$event = $wpdb->get_row($wpdb->prepare($sql, $rsvpEventId));
	$options = json_decode($event->options, true);

	$options[RSVP_PRO_OPTION_WAITLIST_YES_UNAVAILABLE] = "Y";

	$json_options = json_encode($options);
	$wpdb->update(PRO_EVENT_TABLE, array("options" => $json_options), 
                                      array("id"  => $rsvpEventId), 
									  array('%s'), 
                                      array("%d"));
}

function rsvp_pro_send_waitlist_status_change_notification($rsvpEventId, $attendeeId) {
	global $wpdb;
	global $rsvpId;

	$function = "rsvp_pro_get_event_option";
	if(rsvp_pro_is_sub_event($rsvpEventId)) {
		$function = "rsvp_pro_get_sub_event_option";
	}

	$emailText = $function($rsvpEventId, RSVP_PRO_OPTION_WAITLIST_STATUS_CHANGE_EMAIL);
	$emailText = trim($emailText);
	if(empty($emailText)) {
		// Use the default text...
		$emailText = sprintf(__("Your RSVP status has changed from \"Waitlisted\" to \"Yes\" for event %s.", "rsvp-pro-plugin"), get_event_name($rsvpEventId));
	}

	// Get the person's email...
	$email = $wpdb->get_var($wpdb->prepare("SELECT email FROM ".PRO_ATTENDEES_TABLE." WHERE id = %d", $attendeeId));
	if(!empty($email)) {
		$headers = array('Content-Type: text/html; charset=UTF-8');
		if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_EMAIL_FROM) != "") {
          $headers[] = 'From: '. rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_EMAIL_FROM);		
        }

		wp_mail($email, __("RSVP Status Change for ", "rsvp-pro-plugin").get_event_name($rsvpEventId), nl2br($emailText), $headers);
	}
}