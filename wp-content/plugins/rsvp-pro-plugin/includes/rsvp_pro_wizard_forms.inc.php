<?php
function rsvp_pro_frontend_wizard_eventForm($currentEventID, $attendeeID) {
  global $wpdb; 
  global $rsvpId;
  $form ="";
  $isMainEvent = rsvp_pro_is_parent_event($currentEventID);
  $function = "rsvp_pro_get_event_option";
  if($isMainEvent) {
  	$attendee = $wpdb->get_row($wpdb->prepare("SELECT id, firstName, lastName, email, rsvpStatus, note, kidsMeal, additionalAttendee, veggieMeal, personalGreeting, numGuests, suffix, salutation   
  																						 FROM ".PRO_ATTENDEES_TABLE." 
  																						 WHERE id = %d AND rsvpEventID = %d", $attendeeID, $currentEventID));
  } else {
  	$attendee = $wpdb->get_row($wpdb->prepare("SELECT a.id, firstName, lastName, email, ase.rsvpStatus, note, ase.kidsMeal, additionalAttendee, ase.veggieMeal, personalGreeting, numGuests, suffix, salutation   
  																						 FROM ".PRO_ATTENDEES_TABLE." a 
                                               LEFT JOIN ".PRO_ATTENDEE_SUB_EVENTS_TABLE." ase ON ase.rsvpAttendeeID = a.id  AND ase.rsvpEventID = %d
  																						 WHERE a.id = %d", $currentEventID, $attendeeID));
    $function = "rsvp_pro_get_sub_event_option";
  }
	
                                             
	$yesVerbiage = ((trim($function($currentEventID, RSVP_PRO_OPTION_YES_VERBIAGE)) != "") ? $function($currentEventID, RSVP_PRO_OPTION_YES_VERBIAGE) : 
		__("Yes, of course I will be there! Who doesn't like family, friends, weddings, and a good time?", 'rsvp-pro-plugin'));
	$noVerbiage = ((trim($function($currentEventID, RSVP_PRO_OPTION_NO_VERBIAGE)) != "") ? $function($currentEventID, RSVP_PRO_OPTION_NO_VERBIAGE) : 
			__("Um, unfortunately, there is a Star Trek marathon on that day that I just cannot miss.", 'rsvp-pro-plugin'));
  $waitlistVerbiage = __("The event is full but we can add you to the waitlist.", "rsvp-pro-plugin");
  if(trim($function($currentEventID, RSVP_PRO_OPTION_WAITLIST_VERBIAGE)) != "") {
    $waitlistVerbiage = trim($function($currentEventID, RSVP_PRO_OPTION_WAITLIST_VERBIAGE));
  }

  $noResponseText = __("No Response", 'rsvp-pro-plugin');
  if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_NO_RESPONSE_TEXT) != "") {
    $noResponseText = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_NO_RESPONSE_TEXT);
  }

  if(does_user_have_access_to_event($currentEventID, $attendeeID)) : 
    $mainFirstName = $attendee->firstName;
    $mainLastName = $attendee->lastName;
    
    if(isset($_POST['attendeeFirstName']) && !empty($_POST['attendeeFirstName'])) {
      $mainFirstName = $_POST['attendeeFirstName'];
    }
    if(isset($_POST['attendeeLastName']) && !empty($_POST['attendeeLastName'])) {
      $mainLastName = $_POST['attendeeLastName'];
    }

    $eventTitleText = __("RSVP for", "rsvp-pro-plugin");
    if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_MULTI_EVENT_TITLE) != "") {
      $eventTitleText = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_MULTI_EVENT_TITLE);
    }
    $form .= "<h3>".$eventTitleText." ".esc_html(get_event_name($currentEventID))."</h3>";
    
    $form .= RSVP_PRO_START_PARA;
    if(trim($function($currentEventID, RSVP_PRO_OPTION_QUESTION)) != "") {
      $form .= trim($function($currentEventID, RSVP_PRO_OPTION_QUESTION));
    } else {
      $form .= __("So, how about it?", 'rsvp-pro-plugin');
    }
    $form .= RSVP_PRO_END_PARA;

    $requiredRsvp = "";
    
    if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_RSVP_REQUIRED) == "Y") {
      $requiredRsvp = " required";
    }
  
    if(rsvp_pro_frontend_max_limit_hit($currentEventID)) {
      $form .= rsvp_pro_handle_max_limit_reached_message($currentEventID);
      $form .= rsvp_pro_handle_waitlist_message($currentEventID);
      $requiredRsvp .= " disabled=\"true\"";
    }

    $mainName = "mainRsvp";
    if(!$isMainEvent) {
      $mainName = "mainRsvpSub{$currentEventID}";
    }

    $greeting = __(" Will %s be attending?", 'rsvp-pro-plugin');
    if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_ASSOCIATED_ATTENDEE_GREETING) != "") {
      $greeting = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_ASSOCIATED_ATTENDEE_GREETING);
    }

    if(strpos($greeting, "%s") !== false) {
      $greeting = sprintf($greeting, esc_html(stripslashes($mainFirstName." ".$mainLastName)));
    }

  	$form .= rsvp_pro_BeginningFormField("", "rsvpRsvpGreeting").RSVP_PRO_START_PARA.$greeting.RSVP_PRO_END_PARA.
      RSVP_PRO_END_PARA.
      rsvp_pro_BeginningFormField("", "rsvpRsvpQuestionArea").
      "<input type=\"radio\" name=\"{$mainName}\" value=\"Y\" id=\"{$mainName}Y\" ".((($attendee->rsvpStatus == "Yes") || ($rsvp_saved_form_vars[$mainName] == "Y")) ? "checked=\"checked\"" : "")." $requiredRsvp /> <label for=\"{$mainName}Y\">".$yesVerbiage."</label>".
      RSVP_PRO_END_FORM_FIELD.
      rsvp_pro_BeginningFormField("", "rsvpRsvpQuestionArea").
        "<input type=\"radio\" name=\"{$mainName}\" value=\"N\" id=\"{$mainName}N\" ".((($attendee->rsvpStatus == "No") || ($rsvp_saved_form_vars[$mainName] == "N")) ? "checked=\"checked\"" : "")." /> ".
        "<label for=\"{$mainName}N\">".$noVerbiage."</label>".
      RSVP_PRO_END_FORM_FIELD;

    if(($function($currentEventID, RSVP_PRO_OPTION_ENABLE_WAITLIST) == "Y") && rsvp_pro_frontend_max_limit_hit($currentEventID)) {
      $form .= rsvp_pro_BeginningFormField("", "rsvpRsvpQuestionArea").
            "<input type=\"radio\" name=\"{$mainName}\" value=\"W\" id=\"{$mainName}Waitlist\" ".(($a->rsvpStatus == "Waitlist") ? "checked=\"checked\"" : "" )." /> ".
            "<label for=\"{$mainName}Waitlist\">".$waitlistVerbiage."</label>".
          RSVP_PRO_END_FORM_FIELD;      
    }
    
  	$form .= rsvp_pro_buildAdditionalQuestionsForEvent($attendeeID, "main", $currentEventID, true);

  	if($isMainEvent) {
      $sql = "SELECT id, firstName, lastName, email, personalGreeting, rsvpStatus, salutation, suffix FROM ".PRO_ATTENDEES_TABLE." 
        WHERE (id IN (SELECT attendeeID FROM ".PRO_ASSOCIATED_ATTENDEES_TABLE." WHERE associatedAttendeeID = %d) 
          OR id in (SELECT associatedAttendeeID FROM ".PRO_ASSOCIATED_ATTENDEES_TABLE." WHERE attendeeID = %d) OR 
          id IN (SELECT waa1.attendeeID FROM ".PRO_ASSOCIATED_ATTENDEES_TABLE." waa1 
               INNER JOIN ".PRO_ASSOCIATED_ATTENDEES_TABLE." waa2 ON waa2.attendeeID = waa1.attendeeID  OR 
                                                         waa1.associatedAttendeeID = waa2.attendeeID 
               WHERE waa2.associatedAttendeeID = %d AND waa1.attendeeID <> %d)) AND rsvpEventID = %d";
      $associations = $wpdb->get_results($wpdb->prepare($sql, $attendeeID, $attendeeID, $attendeeID, $attendeeID, $rsvpId));
    } else {
      $sql = "SELECT a.id, a.firstName, a.lastName, a.email, a.personalGreeting, ase.rsvpStatus, a.salutation, a.suffix 
        FROM ".PRO_ATTENDEES_TABLE." a 
        LEFT JOIN ".PRO_ATTENDEE_SUB_EVENTS_TABLE." ase ON ase.rsvpAttendeeID = a.id  AND ase.rsvpEventID = %d
        WHERE (a.id IN (SELECT attendeeID FROM ".PRO_ASSOCIATED_ATTENDEES_TABLE." WHERE associatedAttendeeID = %d) 
          OR a.id in (SELECT associatedAttendeeID FROM ".PRO_ASSOCIATED_ATTENDEES_TABLE." WHERE attendeeID = %d) OR 
          a.id IN (SELECT waa1.attendeeID FROM ".PRO_ASSOCIATED_ATTENDEES_TABLE." waa1 
               INNER JOIN ".PRO_ASSOCIATED_ATTENDEES_TABLE." waa2 ON waa2.attendeeID = waa1.attendeeID  OR 
                                                         waa1.associatedAttendeeID = waa2.attendeeID 
               WHERE waa2.associatedAttendeeID = %d AND waa1.attendeeID <> %d)) AND a.rsvpEventID = %d";
      $associations = $wpdb->get_results($wpdb->prepare($sql, $currentEventID, $attendeeID, $attendeeID, $attendeeID, $attendeeID, $rsvpId));
    }

  	if(count($associations) > 0) {
      $attendeeForm = "";

  		foreach($associations as $a) {
        if(($a->id != $attendeeID) && does_user_have_access_to_event($currentEventID, $a->id)) {      
          $attendeeForm .= "<div class=\"rsvpAdditionalAttendee\">\r\n";
          $attendeeForm .= "<div class=\"rsvpAdditionalAttendeeQuestions\">\r\n";

          $requiredRsvp = "";
          if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_RSVP_REQUIRED) == "Y") {
            $requiredRsvp = " required";
          }
          
          $mainName = "attending".$a->id;
          if(!$isMainEvent) {
            $mainName = $a->id."ExistingRsvpSub".$currentEventID;
          }

          $greeting = __(" Will %s be attending?", 'rsvp-pro-plugin');
          if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_ASSOCIATED_ATTENDEE_GREETING) != "") {
            $greeting = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_ASSOCIATED_ATTENDEE_GREETING);
          }

          if(strpos($greeting, "%s") !== false) {
            $greeting = sprintf($greeting, esc_html(stripslashes($a->firstName." ".$a->lastName)));
          }

    			$attendeeForm .= rsvp_pro_BeginningFormField("", "rsvpRsvpGreeting").RSVP_PRO_START_PARA.$greeting.RSVP_PRO_END_PARA;

          if(rsvp_pro_frontend_max_limit_hit($currentEventID)) {
            $attendeeForm .= rsvp_pro_handle_max_limit_reached_message($currentEventID);
            $attendeeForm .= rsvp_pro_handle_waitlist_message($currentEventID);
            $requiredRsvp .= " disabled=\"true\"";
          }
          $attendeeForm .= rsvp_pro_BeginningFormField("", "rsvpRsvpQuestionArea").
                  "<input type=\"radio\" name=\"{$mainName}\" value=\"Y\" id=\"{$mainName}Y\" ".(($a->rsvpStatus == "Yes") ? "checked=\"checked\"" : "" )." $requiredRsvp /> ".
                  "<label for=\"{$mainName}Y\">$yesVerbiage</label>".
                    RSVP_PRO_END_FORM_FIELD.
                    rsvp_pro_BeginningFormField("", "rsvpRsvpQuestionArea").
                    "<input type=\"radio\" name=\"{$mainName}\" value=\"N\" id=\"{$mainName}N\" ".(($a->rsvpStatus == "No") ? "checked=\"checked\"" : "" )." /> ".
                  "<label for=\"{$mainName}N\">$noVerbiage</label>".RSVP_PRO_END_FORM_FIELD;

          if(($function($currentEventID, RSVP_PRO_OPTION_ENABLE_WAITLIST) == "Y") && rsvp_pro_frontend_max_limit_hit($currentEventID)) {
            $attendeeForm .= rsvp_pro_BeginningFormField("", "rsvpRsvpQuestionArea").
                  "<input type=\"radio\" name=\"{$mainName}\" value=\"W\" id=\"{$mainName}Waitlist\" ".(($a->rsvpStatus == "Waitlist") ? "checked=\"checked\"" : "" )." /> ".
                  "<label for=\"{$mainName}Waitlist\">".$waitlistVerbiage."</label>".
                RSVP_PRO_END_FORM_FIELD;      
          }

          if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_SHOW_NORESPONSE_FOR_ASSOCIATED) == "Y") {
            $attendeeForm .= rsvp_pro_BeginningFormField("", "rsvpRsvpQuestionArea").
              "<input type=\"radio\" name=\"{$mainName}\" value=\"NoResponse\" id=\"{$mainName}NoResponse\" ".(($a->rsvpStatus == "NoResponse") ? "checked=\"checked\"" : "" )." /> ".
                  "<label for=\"{$mainName}NoResponse\">$noResponseText</label>".
              RSVP_PRO_END_FORM_FIELD;
          }
          $attendeeForm .= RSVP_PRO_END_FORM_FIELD;
	
    			if(!empty($a->personalGreeting)) {
    				$attendeeForm .= RSVP_PRO_START_PARA.nl2br($a->personalGreeting).RSVP_PRO_END_PARA;
    			}
            
    			$attendeeForm .= rsvp_pro_buildAdditionalQuestionsForEvent($a->id, $a->id, $currentEventID);
          $attendeeForm .= "</div>\r\n"; //-- rsvpAdditionalAttendeeQuestions
    			$attendeeForm .= "</div>\r\n";
  		  } // if($a->id != ...)
      } // foreach($associations...)

      if(!empty($attendeeForm)) {
        $message = __("The following people are associated with you.  At this time you can RSVP for them as well.", 'rsvp-pro-plugin');
        if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_ASSOCIATED_MESSAGE) != "") {
          $message = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_ASSOCIATED_MESSAGE);
        }
        $form .= "<h3>".$message."</h3>";
        $form .= $attendeeForm;
      }
  	}
    
  	if(isset($_POST['additionalRsvp']) && is_numeric($_POST['additionalRsvp']) && ($_POST['additionalRsvp'] > 0)) {
  		for($i = 1; $i <= $_POST['additionalRsvp']; $i++) {
        if(isset($_POST['newAttending'.$i.'FirstName']) && isset($_POST['newAttending'.$i.'LastName'])) {
          $firstName = $_POST['newAttending'.$i.'FirstName'];
          $lastName = $_POST['newAttending'.$i.'LastName'];
    			$form .= "<div class=\"rsvpAdditionalAttendee\">\r\n";
          $form .= "<div class=\"rsvpAdditionalAttendeeQuestions\">\r\n";
    
          if(does_user_have_access_to_event($currentEventID, $attendeeID)) {
            $requiredRsvp = "";
            if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_RSVP_REQUIRED) == "Y") {
              $requiredRsvp = " required";
            }
          
            $mainName = "newAttending".$i;
            if(!$isMainEvent) {
              $mainName = $i."RsvpSub".$currentEventID;
            }

            $greeting = __(" Will %s be attending?", 'rsvp-pro-plugin');
            if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_ASSOCIATED_ATTENDEE_GREETING) != "") {
              $greeting = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_ASSOCIATED_ATTENDEE_GREETING);
            }

            if(strpos($greeting, "%s") !== false) {
              $greeting = sprintf($greeting, esc_html(stripslashes($firstName." ".$lastName)));
            }

      			$form .= rsvp_pro_BeginningFormField("", "rsvpRsvpGreeting").RSVP_PRO_START_PARA.$greeting.RSVP_PRO_END_PARA.
                    rsvp_pro_BeginningFormField("", "rsvpRsvpQuestionArea").
                    "<input type=\"radio\" name=\"{$mainName}\" value=\"Y\" id=\"{$mainName}Y\" $requiredRsvp /> ".
                    "<label for=\"{$mainName}Y\">$yesVerbiage</label>".RSVP_PRO_END_FORM_FIELD.
                    rsvp_pro_BeginningFormField("", "rsvpRsvpQuestionArea").
      							"<input type=\"radio\" name=\"{$mainName}\" value=\"N\" id=\"{$mainName}N\" /> ".
                    "<label for=\"{$mainName}N\">$noVerbiage</label>".RSVP_PRO_END_FORM_FIELD;

            if(($function($currentEventID, RSVP_PRO_OPTION_ENABLE_WAITLIST) == "Y") && rsvp_pro_frontend_max_limit_hit($rsvpId)) {
              $form .= rsvp_pro_BeginningFormField("", "rsvpRsvpQuestionArea").
                    "<input type=\"radio\" name=\"{$mainName}\" value=\"W\" id=\"{$mainName}Waitlist\" /> ".
                    "<label for=\"{$mainName}Waitlist\">".$waitlistVerbiage."</label>".
                  RSVP_PRO_END_FORM_FIELD;      
            }

            if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_SHOW_NORESPONSE_FOR_ASSOCIATED) == "Y") {
              $form .= rsvp_pro_BeginningFormField("", "rsvpRsvpQuestionArea").
                "<input type=\"radio\" name=\"{$mainName}\" value=\"NoResponse\" id=\"{$mainName}NoResponse\" /> ".
                    "<label for=\"{$mainName}NoResponse\">$noResponseText</label>".RSVP_PRO_END_FORM_FIELD;
            }
            $form .= RSVP_PRO_END_FORM_FIELD;
          } 
          
    			$form .= rsvp_pro_buildAdditionalQuestionsForEvent($attendeeID, $i, $currentEventID);
          $form .= "</div>\r\n"; //-- rsvpAdditionalAttendeeQuestions
    			$form .= "</div>\r\n";
        }
      } // for($i = 1; $i <= $_POST['additionalRsvp']; $i++) {
  	}
  endif; // if(does_user_have_access_to_event($rsvpId, $attendeeID)) : 
    
  return $form;
}

function rsvp_pro_frontend_wizard_personalInfo($attendee, $attendeeID) {
  global $wpdb, $rsvpId; 
  global $my_plugin_file;
  
	$noteVerbiage = ((trim(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_NOTE_VERBIAGE)) != "") ? rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_NOTE_VERBIAGE) : 
		__("If you have any <strong style=\"color:red;\">food allergies</strong>, please indicate what they are in the &quot;notes&quot; section below.  Or, if you just want to send us a note, please feel free.  If you have any questions, please send us an email.", 'rsvp-pro-plugin'));
  
	$sql = "SELECT id FROM ".PRO_ATTENDEES_TABLE." 
	 	WHERE (id IN (SELECT attendeeID FROM ".PRO_ASSOCIATED_ATTENDEES_TABLE." WHERE associatedAttendeeID = %d) 
			OR id in (SELECT associatedAttendeeID FROM ".PRO_ASSOCIATED_ATTENDEES_TABLE." WHERE attendeeID = %d)) 
			 AND rsvpEventID = %d";
	$newRsvps = $wpdb->get_results($wpdb->prepare($sql, $attendeeID, $attendeeID, $rsvpId));
  
  $form = "";
  $numGuests = get_number_additional($rsvpId, $attendee);
  
	if(!empty($attendee->personalGreeting)) {
		$form .= rsvp_pro_BeginningFormField("rsvpCustomGreeting", "").nl2br(stripslashes($attendee->personalGreeting)).RSVP_PRO_END_FORM_FIELD;
	}

  // Text for adding additional attendees...
	if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_HIDE_ADDITIONAL) != "Y") {
    $availableGuestCount = $numGuests - count($newRsvps);
    $text = sprintf(__("You currently can invite <span id=\"numAvailableToAdd\">%d</span> more people.", 'rsvp-pro-plugin'), $availableGuestCount);
  
    $additionalVerbiageText = trim(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_ADD_ADDITIONAL_VERBIAGE));
    if($additionalVerbiageText != "") {
      if((strpos($additionalVerbiageText, "%d") !== false) || (strpos($additionalVerbiageText, "%s") !== false)) {
        $text = sprintf($additionalVerbiageText, $availableGuestCount); 
      } else {
        $text = $additionalVerbiageText;
      }
    }
  
    if($availableGuestCount > 0) {
      $buttonText = __("Add Additional Guests", "rsvp-pro-plugin");
      if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_ADD_ATTENDEE_BUTTON_TEXT) != "") {
        $buttonText = stripslashes(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_ADD_ATTENDEE_BUTTON_TEXT));
      }
  		$form .= "<h3>$text</h3>\r\n";
  		$form .= "<div id=\"additionalRsvpContainerText\">\r\n
  								<input type=\"hidden\" name=\"additionalRsvp\" id=\"additionalRsvp\" value=\"".count($newRsvps)."\" />
  								<div style=\"text-align:right\" id=\"addWizardRsvp\"><button>$buttonText</button></div>".
  							"</div>";
    }
	}
  
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
    // New Attendee fields when open registration is allowed 
    if($attendeeID <= 0) {
      $firstNameLabel = __("First Name", 'rsvp-pro-plugin');
      if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_FIRST_NAME_LABEL) != "") {
        $firstNameLabel = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_FIRST_NAME_LABEL);
      }

      $lastNameLabel = __("Last Name", 'rsvp-pro-plugin');
      if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_LAST_NAME_LABEL) != "") {
        $lastNameLabel = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_LAST_NAME_LABEL);
      }

      $form .= RSVP_PRO_START_PARA;
      $form .= rsvp_pro_BeginningFormField("", "").
        "<label for=\"attendeeFirstName\">".$firstNameLabel.":</label>".
        "<input type=\"text\" name=\"attendeeFirstName\" id=\"attendeeFirstName\" value=\"".esc_html($rsvp_saved_form_vars['attendeeFirstName'])."\" />".
        RSVP_PRO_END_FORM_FIELD;
      $form .= RSVP_PRO_END_PARA;
    
      $form .= RSVP_PRO_START_PARA;
      $form .= rsvp_pro_BeginningFormField("", "").
        "<label for=\"attendeeLastName\">".$lastNameLabel.":</label>".
        "<input type=\"text\" name=\"attendeeLastName\" id=\"attendeeLastName\" value=\"".esc_html($rsvp_saved_form_vars['attendeeLastName'])."\" />".
        RSVP_PRO_END_FORM_FIELD;
      $form .= RSVP_PRO_END_PARA;
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
        $form .= RSVP_PRO_END_FORM_FIELD;
	
        $optionsText = __("options.", "rsvp-pro-plugin");
        if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_MULTI_OPTION_TEXT) != "") {
          $optionsText = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_MULTI_OPTION_TEXT);
        }

        $form .= RSVP_PRO_START_PARA.esc_html(stripslashes($a->firstName." ".$a->lastName)." ").$optionsText.RSVP_PRO_END_PARA;
  
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
        $form .= "</div>\r\n"; //-- rsvpAdditionalAttendeeQuestions
		  } // if($a->id != ...)
    } // foreach($associations...)
	}
	$form .= "<div id=\"additionalRsvpContainer\">\r\n
              <input type=\"hidden\" name=\"junkTest\" value=\"\" />
						</div>";
  rsvp_pro_wizard_output_additional_js($rsvpId, $attendee, $attendeeID);
  return $form;
}

function rsvp_pro_buildAdditionalQuestionsForEvent($attendeeID, $prefix, $eventId, $includeGroupQuestions = false) {
	global $wpdb, $rsvp_saved_form_vars;
  
	$output = "<div class=\"rsvpCustomQuestions\">";
	
	$sql = "SELECT q.id, q.question, questionType, q.sortOrder, q.required, e.id AS rsvpEventID FROM ".PRO_QUESTIONS_TABLE." q 
					INNER JOIN ".PRO_QUESTION_TYPE_TABLE." qt ON qt.id = q.questionTypeID 
          INNER JOIN ".PRO_EVENT_TABLE." e ON e.id = q.rsvpEventID 
					WHERE (q.permissionLevel = 'public' 
					  OR (q.permissionLevel = 'private' AND q.id IN (SELECT questionID FROM ".PRO_QUESTION_ATTENDEES_TABLE." WHERE attendeeID = $attendeeID)))
            AND qt.questionType <> 'hidden' 
            AND q.rsvpEventID = %d ";
  if(!$includeGroupQuestions) {
    $sql .= "  AND grouping <> '".RSVP_PRO_QG_MULTI."' ";
  }
            
  $sql .= " ORDER BY sortOrder";
  
  $questions = $wpdb->get_results($wpdb->prepare($sql, $eventId));
  $output .= rsvp_pro_createQuestionInputs($attendeeID, $prefix, $questions);
  
	return $output."</div>";
}
