<?php
    $buttonText             = "Create Event";
    $opendate               = "";
    $closedate              = "";
    $parentEventID          = 0;
    $eventName              = "";
    $greetingText           = "";
    $welcomeText            = "";
    $rsvpQuestionText       = "";
    $rsvpYesLabel           = "";
    $rsvpNoLabel            = "";
    $rsvpWaitlistLabel      = "";
    $additionalNoteLabel    = "";
    $thankYouText           = "";
    $hideAdditionalGuests   = "N";
    $notifyUser             = "N";
    $notifyEmail            = "";
    $requirePasscode        = "N";
    $hideNote               = "N";
    $rsvpOpenReg            = "N";
    $dontUseHash            = "N";
    $addAdditionalText      = "";
    $guestEmailConfirm      = "N";
    $numAdditionalGuests    = "";
    $hideEmailField         = "N";
    $disableCustomEmailFrom = "N";
    $onlyPasscode           = "N";    
    $rsvpEmailText          = "";
    $rsvpNoEditing          = "N";
    $rsvpDisableUserSearch  = "N";
    $notComingVerbiage      = "";
    $showSuffix             = "N";
    $showSalutation         = "N";
    $salutations            = RSVP_PRO_DEFAULT_SALUTATION;
    $password_length        = 6;
    $showNoResponse         = "N";
    $attendeeAccess         = array();
    $hasMultipleEvents      = false;
    $requireEmail           = "N";
    $requireRsvpValues      = "N";
    $noPasscodeOnOpen       = "N";
    $frontendWizard         = "N";
    $newAttendeeButtonText  = "";
    $rsvpFrontendButtonText = "";
    $nextButtonText         = "";
    $rsvpLimit              = "";
    $rsvpLimitText          = "";
    $rsvpEnableWaitlist     = "N";
    $rsvpWaitlistText       = "";
    $openDateText           = "";
    $closeDateText          = "";
    $attendeelistFilter     = "";
    $attendeelistSortOrder  = "";
    $attendeeListHideStatus = "N";
    $attendeeListCustomQs   = array();
    $attendeeAutoLogin      = "N";
    $modifyRegistrationText = "";
    $addAttendeeButtonText  = "";
    $adminRoles             = array();
    $emailFrom              = "";
    $emailCCAssociated      = "N";
    $emailBCCAddresses      = "";
    $emailSubject           = "";
    $firstNameLabel         = "";
    $lastNameLabel          = "";
    $completeButtonText     = "";
    $passcodeLabel          = "";
    $editPromptText         = "";
    $suffixLabel            = "";
    $salutationLabel        = "";
    $emailLabel             = "";
    $associatedMessage      = "";
    $associatedGreeting     = "";
    $multiEventTitle        = "";
    $multiOptionText        = "";
    $removeAttendeeButton   = "";
    $yesText                = "";
    $noText                 = "";
    $noResponseText         = "";
    $multipleMatchesText    = "";
    $fuzzyMatchText         = "";
    $unableFindText         = "";
    $welcomeBackText        = "";

    
    if(isset($_POST) && (!empty($_POST['eventName']))) {
      $eventName = $_POST['eventName'];
      $opendate = "";
      $closedate = "";
      $parentEventID = 0;
      $eventAccess = $_POST['event_access'];
      $greetingText = $_POST['greeting_text'];
      $welcomeText = $_POST['welcome_text'];
      $rsvpQuestionText = $_POST['rsvp_question_text'];
      $rsvpYesLabel = $_POST['rsvp_yes_label'];
      $rsvpNoLabel = $_POST['rsvp_no_label'];
      $rsvpWaitlistLabel = $_POST[RSVP_PRO_OPTION_WAITLIST_VERBIAGE];
      $additionalNoteLabel = $_POST['additional_note_label'];
      $thankYouText = $_POST['thank_you_text'];
      $hideAdditionalGuests = ($_POST['hide_additional_guests'] == "Y") ? "Y" : "N";
      $notifyUser = ($_POST['notify_user'] == "Y") ? "Y" : "N";
      $notifyEmail = $_POST['notify_email'];
      $requirePasscode = ($_POST['require_passcode'] == "Y") ? "Y" : "N";
      $hideNote = ($_POST['hide_note'] == "Y") ? "Y" : "N";
      $rsvpOpenReg = ($_POST['rsvp_open_registration'] == "Y") ? "Y" : "N";
      $dontUseHash = ($_POST['rsvp_dont_use_hash'] == "Y") ? "Y" : "N";
      $addAdditionalText = $_POST['rsvp_add_additional_verbiage'];
      $guestEmailConfirm = ($_POST['rsvp_guest_email_confirmation'] == "Y") ? "Y" : "N";
      $numAdditionalGuests = $_POST['rsvp_num_additional_guests'];
      $hideEmailField         = ($_POST['rsvp_hide_email_field'] == "Y") ? "Y" : "N";
      $disableCustomEmailFrom = ($_POST['rsvp_disable_custom_from_email'] == "Y") ? "Y" : "N";
      $onlyPasscode           = ($_POST['rsvp_only_passcode'] == "Y") ? "Y" : "N";
      $rsvpEmailText = $_POST[RSVP_PRO_OPTION_EMAIL_TEXT];
      $rsvpNoEditing = ($_POST[RSVP_PRO_OPTION_CANT_EDIT] == "Y") ? "Y" : "N";
      $rsvpDisableUserSearch = ($_POST[RSVP_PRO_OPTION_DISABLE_USER_SEARCH] == "Y") ? "Y" : "N";
      $notComingVerbiage = $_POST[RSVP_PRO_OPTION_NOT_COMING];
      $showSuffix = ($_POST[RSVP_PRO_OPTION_SHOW_SUFFIX] == "Y") ? "Y" : "N";
      $showSalutation = ($_POST[RSVP_PRO_OPTION_SHOW_SALUTATION] == "Y") ? "Y" : "N";
      $showNoResponse = ($_POST[RSVP_PRO_OPTION_SHOW_NORESPONSE_FOR_ASSOCIATED] == "Y") ? "Y" : "N";
      $noPasscodeOnOpen = ($_POST[RSVP_PRO_OPTION_RSVP_OPEN_NO_PASSCODE] == "Y") ? "Y" : "N";
      $requireRsvpValues = ($_POST[RSVP_PRO_OPTION_RSVP_REQUIRED] == "Y") ? "Y" : "N";
      $requireEmail = ($_POST[RSVP_PRO_OPTION_EMAIL_REQUIRED] == "Y") ? "Y" : "N";
      $salutations = str_replace("\r\n", "||", $_POST[RSVP_PRO_OPTION_SALUTATIONS]);
      $frontendWizard = ($_POST[RSVP_PRO_OPTION_FRONTEND_WIZARD] == "Y") ? "Y" : "N";
      $newAttendeeButtonText = $_POST[RSVP_PRO_OPTION_NEW_ATTENDEE_BUTTON_TEXT];
      $rsvpFrontendButtonText = $_POST[RSVP_PRO_OPTION_RSVP_BUTTON_TEXT];
      $nextButtonText = $_POST[RSVP_PRO_OPTION_NEXT_BUTTON_TEXT];
      $rsvpLimitText = $_POST[RSVP_PRO_OPTION_MAX_COUNT_REACHED_TEXT];
      $rsvpEnableWaitlist = ($_POST[RSVP_PRO_OPTION_ENABLE_WAITLIST] == "Y") ? "Y" : "N";
      $rsvpWaitlistText = $_POST[RSVP_PRO_OPTION_WAITLIST_TEXT];
      $openDateText = $_POST[RSVP_PRO_OPTION_OPEN_DATE_TEXT];
      $closeDateText = $_POST[RSVP_PRO_OPTION_CLOSE_DATE_TEXT];
      $attendeeAutoLogin = ($_POST[RSVP_PRO_OPTION_AUTO_LOGIN_ATTENDEE] == "Y") ? "Y" : "N";
      $modifyRegistrationText  = $_POST[RSVP_PRO_OPTION_MODIFY_REGISTRATION_TEXT];
      $addAttendeeButtonText = $_POST[RSVP_PRO_OPTION_ADD_ATTENDEE_BUTTON_TEXT];
      $emailFrom = $_POST[RSVP_PRO_OPTION_EMAIL_FROM];
      $emailCCAssociated = ($_POST[RSVP_PRO_OPTION_EMAIL_CC_ASSOCIATED] == "Y") ? "Y" : "N";
      $emailBCCAddresses = $_POST[RSVP_PRO_OPTION_EMAIL_BCC_ADDRESS];
      $emailSubject = $_POST[RSVP_PRO_OPTION_EMAIL_SUBJECT];
      $firstNameLabel = $_POST[RSVP_PRO_OPTION_FIRST_NAME_LABEL];
      $lastNameLabel = $_POST[RSVP_PRO_OPTION_LAST_NAME_LABEL];
      $completeButtonText = $_POST[RSVP_PRO_OPTION_COMPLETE_BUTTON_TEXT];
      $passcodeLabel = $_POST[RSVP_PRO_OPTION_PASSCODE_LABEL];
      $editPromptText = $_POST[RSVP_PRO_OPTION_EDIT_PROMPT_TEXT];
      $suffixLabel = $_POST[RSVP_PRO_OPTION_SUFFIX_LABEL];
      $salutationLabel = $_POST[RSVP_PRO_OPTION_SALUTATION_LABEL];
      $emailLabel = $_POST[RSVP_PRO_OPTION_EMAIL_LABEL];
      $associatedMessage = $_POST[RSVP_PRO_OPTION_ASSOCIATED_MESSAGE];
      $associatedGreeting = $_POST[RSVP_PRO_OPTION_ASSOCIATED_ATTENDEE_GREETING];
      $multiEventTitle = $_POST[RSVP_PRO_OPTION_MULTI_EVENT_TITLE];
      $multiOptionText = $_POST[RSVP_PRO_OPTION_MULTI_OPTION_TEXT];
      $removeAttendeeButton = $_POST[RSVP_PRO_OPTION_REMOVE_ATTENDEE_BUTTON_TEXT];
      $yesText = $_POST[RSVP_PRO_OPTION_YES_TEXT];
      $noText = $_POST[RSVP_PRO_OPTION_NO_TEXT];
      $noResponseText = $_POST[RSVP_PRO_OPTION_NO_RESPONSE_TEXT];
      $multipleMatchesText = $_POST[RSVP_PRO_OPTION_MULTIPLE_MATCHES_TEXT];
      $fuzzyMatchText = $_POST[RSVP_PRO_OPTION_FUZZY_MATCH_TEXT];
      $attendeeListHideStatus = ($_POST[RSVP_PRO_OPTION_ATTENDEE_LIST_HIDE_STATUS] == "Y") ? "Y" : "N";
      $welcomeBackText = $_POST[RSVP_PRO_OPTION_WELCOME_BACK_TEXT];
      $unableFindText = $_POST[RSVP_PRO_OPTION_UNABLE_TO_FIND_TEXT];

      if(is_array($_POST['adminRoles'])) {
        $adminRoles = $_POST["adminRoles"];
      } elseif(!empty($_POST['adminRoles'])) {
        $adminRoles = explode(",", $_POST['adminRoles']);
      }

      $tmp = $_POST[RSVP_PRO_OPTION_ATTENDEE_LIST_FILTER];
      if(($tmp == "noresponse") || ($tmp == "yes") || ($tmp == "no") || ($tmp == "waitlist")) {
        $attendeelistFilter = $tmp;
      }

      $tmp = $_POST[RSVP_PRO_OPTION_ATTENDEE_LIST_SORT_ORDER];
      if(($tmp == "firstName") || ($tmp == "lastName") || ($tmp == "rsvpStatus")) {
        $attendeelistSortOrder = $tmp;
      }

      if(is_numeric($_POST[RSVP_PRO_OPTION_EVENT_COUNT_LIMIT]) && ($_POST[RSVP_PRO_OPTION_EVENT_COUNT_LIMIT] > 0)) {
        $rsvpLimit = $_POST[RSVP_PRO_OPTION_EVENT_COUNT_LIMIT];  
      }
      
      if(is_numeric($_POST[RSVP_PRO_OPTION_PASSWORD_LENGTH]) && ($_POST[RSVP_PRO_OPTION_PASSWORD_LENGTH] >= 1) && ($_POST[RSVP_PRO_OPTION_PASSWORD_LENGTH] <= 50)) {
        $password_length = $_POST[RSVP_PRO_OPTION_PASSWORD_LENGTH];
      }

      if(is_array($_POST[RSVP_PRO_OPTION_ATTENDEE_LIST_CUSTOM_QUESTIONS])) {
        $attendeeListCustomQs = $_POST[RSVP_PRO_OPTION_ATTENDEE_LIST_CUSTOM_QUESTIONS];
      } else {
        $attendeeListCustomQs = explode(",", $_POST[RSVP_PRO_OPTION_ATTENDEE_LIST_CUSTOM_QUESTIONS]);
      }
      
      $eventID = 0;
      
      if(strtotime($_POST['open_date'])) {
        $opendate = date("Y-m-d", strtotime($_POST['open_date']));
      }
        
      if(strtotime($_POST['close_date'])) {
        $closedate = date("Y-m-d", strtotime($_POST['close_date']));
      }
      
      if(is_numeric($_POST['parentEventID']) && ($_POST['parentEventID'] > 0)) {
        $parentEventID = $_POST['parentEventID'];
      }
      
      $options = array(
        RSVP_PRO_OPTION_GREETING                        => $greetingText, 
        RSVP_PRO_OPTION_WELCOME_TEXT                    => $welcomeText, 
        RSVP_PRO_OPTION_THANKYOU                        => $thankYouText, 
        RSVP_PRO_OPTION_YES_VERBIAGE                    => $rsvpYesLabel, 
        RSVP_PRO_OPTION_NO_VERBIAGE                     => $rsvpNoLabel, 
        RSVP_PRO_OPTION_QUESTION                        => $rsvpQuestionText, 
        RSVP_PRO_OPTION_HIDE_ADDITIONAL                 => $hideAdditionalGuests, 
        RSVP_PRO_OPTION_NOTIFY_ON_RSVP                  => $notifyUser, 
        RSVP_PRO_OPTION_NOTIFY_EMAIL                    => $notifyEmail, 
        RSVP_PRO_OPTION_PASSCODE                        => $requirePasscode, 
        RSVP_PRO_OPTION_HIDE_NOTE                       => $hideNote, 
        RSVP_PRO_OPTION_NOTE_VERBIAGE                   => $additionalNoteLabel, 
        RSVP_PRO_OPTION_OPEN_REGISTRATION               => $rsvpOpenReg, 
        RSVP_PRO_OPTION_DONT_USE_HASH                   => $dontUseHash, 
        RSVP_PRO_OPTION_ADD_ADDITIONAL_VERBIAGE         => $addAdditionalText, 
        RSVP_PRO_GUEST_EMAIL_CONFIRMATION               => $guestEmailConfirm, 
        RSVP_PRO_NUM_ADDITIONAL_GUESTS                  => $numAdditionalGuests,
        RSVP_PRO_HIDE_EMAIL_FIELD                       => $hideEmailField, 
        RSVP_PRO_DISABLE_CUSTOM_EMAIL_FROM              => $disableCustomEmailFrom, 
        RSVP_PRO_ONLY_PASSCODE                          => $onlyPasscode, 
        RSVP_PRO_OPTION_EMAIL_TEXT                      => $rsvpEmailText, 
        RSVP_PRO_OPTION_CANT_EDIT                       => $rsvpNoEditing, 
        RSVP_PRO_OPTION_DISABLE_USER_SEARCH             => $rsvpDisableUserSearch, 
        RSVP_PRO_OPTION_NOT_COMING                      => $notComingVerbiage, 
        RSVP_PRO_OPTION_SHOW_SALUTATION                 => $showSalutation, 
        RSVP_PRO_OPTION_SHOW_SUFFIX                     => $showSuffix, 
        RSVP_PRO_OPTION_SALUTATIONS                     => $salutations, 
        RSVP_PRO_OPTION_PASSWORD_LENGTH                 => $password_length, 
        RSVP_PRO_OPTION_SHOW_NORESPONSE_FOR_ASSOCIATED  => $showNoResponse, 
        RSVP_PRO_OPTION_EMAIL_REQUIRED                  => $requireEmail, 
        RSVP_PRO_OPTION_RSVP_REQUIRED                   => $requireRsvpValues, 
        RSVP_PRO_OPTION_RSVP_OPEN_NO_PASSCODE           => $noPasscodeOnOpen, 
        RSVP_PRO_OPTION_FRONTEND_WIZARD                 => $frontendWizard, 
        RSVP_PRO_OPTION_NEW_ATTENDEE_BUTTON_TEXT        => $newAttendeeButtonText, 
        RSVP_PRO_OPTION_RSVP_BUTTON_TEXT                => $rsvpFrontendButtonText, 
        RSVP_PRO_OPTION_NEXT_BUTTON_TEXT                => $nextButtonText, 
        RSVP_PRO_OPTION_EVENT_COUNT_LIMIT               => $rsvpLimit, 
        RSVP_PRO_OPTION_MAX_COUNT_REACHED_TEXT          => $rsvpLimitText, 
        RSVP_PRO_OPTION_ENABLE_WAITLIST                 => $rsvpEnableWaitlist, 
        RSVP_PRO_OPTION_WAITLIST_TEXT                   => $rsvpWaitlistText, 
        RSVP_PRO_OPTION_OPEN_DATE_TEXT                  => $openDateText, 
        RSVP_PRO_OPTION_CLOSE_DATE_TEXT                 => $closeDateText, 
        RSVP_PRO_OPTION_WAITLIST_VERBIAGE               => $rsvpWaitlistLabel, 
        RSVP_PRO_OPTION_ATTENDEE_LIST_FILTER            => $attendeelistFilter, 
        RSVP_PRO_OPTION_ATTENDEE_LIST_SORT_ORDER        => $attendeelistSortOrder, 
        RSVP_PRO_OPTION_ATTENDEE_LIST_HIDE_STATUS       => $attendeeListHideStatus, 
        RSVP_PRO_OPTION_ATTENDEE_LIST_CUSTOM_QUESTIONS  => $attendeeListCustomQs, 
        RSVP_PRO_OPTION_AUTO_LOGIN_ATTENDEE             => $attendeeAutoLogin, 
        RSVP_PRO_OPTION_MODIFY_REGISTRATION_TEXT        => $modifyRegistrationText, 
        RSVP_PRO_OPTION_ADD_ATTENDEE_BUTTON_TEXT        => $addAttendeeButtonText, 
        RSVP_PRO_OPTION_ADMIN_ROLES                     => $adminRoles, 
        RSVP_PRO_OPTION_EMAIL_FROM                      => $emailFrom, 
        RSVP_PRO_OPTION_EMAIL_CC_ASSOCIATED             => $emailCCAssociated, 
        RSVP_PRO_OPTION_EMAIL_BCC_ADDRESS               => $emailBCCAddresses, 
        RSVP_PRO_OPTION_EMAIL_SUBJECT                   => $emailSubject, 
        RSVP_PRO_OPTION_FIRST_NAME_LABEL                => $firstNameLabel, 
        RSVP_PRO_OPTION_LAST_NAME_LABEL                 => $lastNameLabel, 
        RSVP_PRO_OPTION_COMPLETE_BUTTON_TEXT            => $completeButtonText, 
        RSVP_PRO_OPTION_PASSCODE_LABEL                  => $passcodeLabel, 
        RSVP_PRO_OPTION_EDIT_PROMPT_TEXT                => $editPromptText, 
        RSVP_PRO_OPTION_SUFFIX_LABEL                    => $suffixLabel, 
        RSVP_PRO_OPTION_SALUTATION_LABEL                => $salutationLabel, 
        RSVP_PRO_OPTION_EMAIL_LABEL                     => $emailLabel, 
        RSVP_PRO_OPTION_ASSOCIATED_MESSAGE              => $associatedMessage, 
        RSVP_PRO_OPTION_ASSOCIATED_ATTENDEE_GREETING    => $associatedGreeting, 
        RSVP_PRO_OPTION_MULTI_OPTION_TEXT               => $multiOptionText, 
        RSVP_PRO_OPTION_MULTI_EVENT_TITLE               => $multiEventTitle, 
        RSVP_PRO_OPTION_REMOVE_ATTENDEE_BUTTON_TEXT     => $removeAttendeeButton,
        RSVP_PRO_OPTION_YES_TEXT                        => $yesText, 
        RSVP_PRO_OPTION_NO_TEXT                         => $noText, 
        RSVP_PRO_OPTION_NO_RESPONSE_TEXT                => $noResponseText,  
        RSVP_PRO_OPTION_MULTIPLE_MATCHES_TEXT           => $multipleMatchesText, 
        RSVP_PRO_OPTION_FUZZY_MATCH_TEXT                => $fuzzyMatchText, 
        RSVP_PRO_OPTION_WELCOME_BACK_TEXT               => $welcomeBackText, 
        RSVP_PRO_OPTION_UNABLE_TO_FIND_TEXT             => $unableFindText, 
      );

      $json_options = json_encode($options);

      if(isset($_POST['eventID']) && is_numeric($_POST['eventID']) && ($_POST['eventID'] > 0)) {
        // update existing event
				$wpdb->update(PRO_EVENT_TABLE, array("eventName" 				        => $eventName, 
																				 "open_date" 				        => $opendate,
																				 "close_date"               => $closedate, 
                                         "parentEventID"            => $parentEventID, 
                                         "options"                  => $json_options, 
                                         "event_access"             => $eventAccess), 
                                      array("id"  => $_POST['eventID']), 
																			array('%s', '%s', '%s', '%d', '%s', '%s'), 
                                      array("%d"));
        
        if($requirePasscode == "Y") {
          rsvp_pro_set_passcode($_POST['eventID']);
        }
        
        // Save the access....
        $wpdb->delete(PRO_EVENT_ATTENDEE_TABLE, array("rsvpEventID" => $_POST['eventID']));
        $tmpAttendeeAccess = "";
        if(!is_array($_POST['attendeeAccess']) ) {
          $tmpAttendeeAccess = explode(",", $_POST['attendeeAccess']);
        } else {
          $tmpAttendeeAccess = $_POST['attendeeAccess'];
        }
				foreach($tmpAttendeeAccess as $aid) {
					if(is_numeric($aid) && ($aid > 0)) {
						$wpdb->insert(PRO_EVENT_ATTENDEE_TABLE, array("rsvpAttendeeID"=>$aid, "rsvpEventID"=>$_POST['eventID']), array("%d", "%d"));
					}
				}
        
        wp_redirect( admin_url('admin.php?page=rsvp-pro-top-level&edit=success')); 
        exit;
      } else {
        // new event
				$wpdb->insert(PRO_EVENT_TABLE, array("eventName" 				        => $eventName, 
																				 "open_date" 				        => $opendate,
																				 "close_date"               => $closedate, 
                                         "parentEventID"            => $parentEventID, 
                                         "options"                  => $json_options, 
                                         "event_access"             => $eventAccess), 
																			 array('%s', '%s', '%s', '%d', '%s', '%s'));
           
        $eventId = $wpdb->insert_id;                    
        if($requirePasscode == "Y") {
          rsvp_pro_set_passcode($eventId);
        }
        
        // Save the access....
        $wpdb->delete(PRO_EVENT_ATTENDEE_TABLE, array("rsvpEventID" => $eventId));
        $tmpAttendeeAccess = "";
        if(!is_array($_POST['attendeeAccess']) ) {
          $tmpAttendeeAccess = explode(",", $_POST['attendeeAccess']);
        } else {
          $tmpAttendeeAccess = $_POST['attendeeAccess'];
        }
				foreach($tmpAttendeeAccess as $aid) {
					if(is_numeric($aid) && ($aid > 0)) {
						$wpdb->insert(PRO_EVENT_ATTENDEE_TABLE, array("rsvpAttendeeID"=>$aid, "rsvpEventID"=>$eventId), array("%d", "%d"));
					}
				}
        wp_redirect( admin_url('admin.php?page=rsvp-pro-top-level&add=success')); 
        exit;
      }
      
    }
    if(isset($_GET['id']) && is_numeric($_GET['id']) && ($_GET['id'] > 0)) {
      $sql = "SELECT eventName, open_date, close_date, options, parentEventID, event_access  
              FROM ".PRO_EVENT_TABLE." WHERE id = %d";
              $wpdb->show_errors = true;
      $event = $wpdb->get_row($wpdb->prepare($sql, $_GET['id']));
      if($event) {
        $options = json_decode($event->options, true);
        $buttonText = __("Update Event", "rsvp-pro-plugin");
        $eventID = $_GET['id'];
        $hasMultipleEvents = rsvp_pro_is_sub_or_parent_event($eventID);
        $eventName = $event->eventName;
        $opendate = date("m/d/Y", strtotime($event->open_date));
        $closedate = date("m/d/Y", strtotime($event->close_date));
        $eventAccess = ($event->event_access == RSVP_PRO_PRIVATE_EVENT_ACCESS) ? RSVP_PRO_PRIVATE_EVENT_ACCESS : RSVP_PRO_OPEN_EVENT_ACCESS;
        $parentEventID = $event->parentEventID;
        $greetingText = stripslashes($options[RSVP_PRO_OPTION_GREETING]);
        $welcomeText = stripslashes($options[RSVP_PRO_OPTION_WELCOME_TEXT]);
        $rsvpQuestionText = stripslashes($options[RSVP_PRO_OPTION_QUESTION]);
        $rsvpYesLabel = stripslashes($options[RSVP_PRO_OPTION_YES_VERBIAGE]);
        $rsvpNoLabel = stripslashes($options[RSVP_PRO_OPTION_NO_VERBIAGE]);
        $additionalNoteLabel = stripslashes($options[RSVP_PRO_OPTION_NOTE_VERBIAGE]);
        $thankYouText = stripslashes($options[RSVP_PRO_OPTION_THANKYOU]);
        $hideAdditionalGuests = ($options[RSVP_PRO_OPTION_HIDE_ADDITIONAL] == "Y") ? "Y" : "N";
        $notifyUser = ($options[RSVP_PRO_OPTION_NOTIFY_ON_RSVP] == "Y") ? "Y" : "N";
        $notifyEmail = $options[RSVP_PRO_OPTION_NOTIFY_EMAIL];
        $requirePasscode = ($options[RSVP_PRO_OPTION_PASSCODE] == "Y") ? "Y" : "N";
        $hideNote = ($options[RSVP_PRO_OPTION_HIDE_NOTE] == "Y") ? "Y" : "N";
        $rsvpOpenReg = ($options[RSVP_PRO_OPTION_OPEN_REGISTRATION] == "Y") ? "Y" : "N";
        $dontUseHash = ($options[RSVP_PRO_OPTION_DONT_USE_HASH] == "Y") ? "Y" : "N";
        $addAdditionalText = stripslashes($options[RSVP_PRO_OPTION_ADD_ADDITIONAL_VERBIAGE]);
        $guestEmailConfirm = ($options[RSVP_PRO_GUEST_EMAIL_CONFIRMATION] == "Y") ? "Y" : "N";
        $numAdditionalGuests = $options[RSVP_PRO_NUM_ADDITIONAL_GUESTS];
        $hideEmailField         = ($options[RSVP_PRO_HIDE_EMAIL_FIELD] == "Y") ? "Y" : "N";
        $disableCustomEmailFrom = ($options[RSVP_PRO_DISABLE_CUSTOM_EMAIL_FROM] == "Y") ? "Y" : "N";
        $onlyPasscode           = ($options[RSVP_PRO_ONLY_PASSCODE] == "Y") ? "Y" : "N";
        $rsvpEmailText          = stripslashes($options[RSVP_PRO_OPTION_EMAIL_TEXT]);
        $rsvpNoEditing          = ($options[RSVP_PRO_OPTION_CANT_EDIT] == "Y") ? "Y" : "N";
        $rsvpDisableUserSearch  = ($options[RSVP_PRO_OPTION_DISABLE_USER_SEARCH] == "Y") ? "Y" : "N";
        $notComingVerbiage      = stripslashes($options[RSVP_PRO_OPTION_NOT_COMING]);
        $showSalutation         = ($options[RSVP_PRO_OPTION_SHOW_SALUTATION] == "Y") ? "Y" : "N";
        $showSuffix             = ($options[RSVP_PRO_OPTION_SHOW_SUFFIX] == "Y") ? "Y" : "N";
        $showNoResponse         = ($options[RSVP_PRO_OPTION_SHOW_NORESPONSE_FOR_ASSOCIATED] == "Y") ? "Y" : "N";
        $requireEmail           = ($options[RSVP_PRO_OPTION_EMAIL_REQUIRED] == "Y") ? "Y" : "N";
        $noPasscodeOnOpen       = ($options[RSVP_PRO_OPTION_RSVP_OPEN_NO_PASSCODE] == "Y") ? "Y" : "N";
        $requireRsvpValues      = ($options[RSVP_PRO_OPTION_RSVP_REQUIRED] == "Y") ? "Y" : "N";
        $frontendWizard         = ($options[RSVP_PRO_OPTION_FRONTEND_WIZARD] == "Y") ? "Y" : "N";
        $newAttendeeButtonText  = stripslashes($options[RSVP_PRO_OPTION_NEW_ATTENDEE_BUTTON_TEXT]);
        $rsvpFrontendButtonText = stripslashes($options[RSVP_PRO_OPTION_RSVP_BUTTON_TEXT]);
        $nextButtonText         = stripslashes($options[RSVP_PRO_OPTION_NEXT_BUTTON_TEXT]);
        $rsvpLimit              = $options[RSVP_PRO_OPTION_EVENT_COUNT_LIMIT];
        $rsvpLimitText          = stripslashes($options[RSVP_PRO_OPTION_MAX_COUNT_REACHED_TEXT]);
        $rsvpEnableWaitlist     = ($options[RSVP_PRO_OPTION_ENABLE_WAITLIST] == "Y") ? "Y" : "N";
        $rsvpWaitlistText       = stripslashes($options[RSVP_PRO_OPTION_WAITLIST_TEXT]);
        $openDateText           = stripslashes($options[RSVP_PRO_OPTION_OPEN_DATE_TEXT]);
        $closeDateText          = stripslashes($options[RSVP_PRO_OPTION_CLOSE_DATE_TEXT]);
        $rsvpWaitlistLabel      = stripslashes($options[RSVP_PRO_OPTION_WAITLIST_VERBIAGE]);
        $attendeelistFilter     = stripslashes($options[RSVP_PRO_OPTION_ATTENDEE_LIST_FILTER]);
        $attendeelistSortOrder  = stripslashes($options[RSVP_PRO_OPTION_ATTENDEE_LIST_SORT_ORDER]);
        $attendeeListHideStatus = ($options[RSVP_PRO_OPTION_ATTENDEE_LIST_HIDE_STATUS] == "Y") ? "Y" : "N"; 
        $attendeeAutoLogin      = ($options[RSVP_PRO_OPTION_AUTO_LOGIN_ATTENDEE] == "Y") ? "Y" : "N";
        $modifyRegistrationText = stripslashes($options[RSVP_PRO_OPTION_MODIFY_REGISTRATION_TEXT]);
        $addAttendeeButtonText  = stripslashes($options[RSVP_PRO_OPTION_ADD_ATTENDEE_BUTTON_TEXT]);
        $emailFrom              = stripslashes($options[RSVP_PRO_OPTION_EMAIL_FROM]);
        $emailCCAssociated      = ($options[RSVP_PRO_OPTION_EMAIL_CC_ASSOCIATED] == "Y") ? "Y" : "N";
        $emailBCCAddresses      = stripslashes($options[RSVP_PRO_OPTION_EMAIL_BCC_ADDRESS]);
        $emailSubject           = stripslashes($options[RSVP_PRO_OPTION_EMAIL_SUBJECT]);
        $firstNameLabel         = stripslashes($options[RSVP_PRO_OPTION_FIRST_NAME_LABEL]);
        $lastNameLabel          = stripslashes($options[RSVP_PRO_OPTION_LAST_NAME_LABEL]);
        $completeButtonText     = stripslashes($options[RSVP_PRO_OPTION_COMPLETE_BUTTON_TEXT]);
        $passcodeLabel          = stripslashes($options[RSVP_PRO_OPTION_PASSCODE_LABEL]);
        $editPromptText         = stripslashes($options[RSVP_PRO_OPTION_EDIT_PROMPT_TEXT]);
        $suffixLabel            = stripslashes($options[RSVP_PRO_OPTION_SUFFIX_LABEL]);
        $salutationLabel        = stripslashes($options[RSVP_PRO_OPTION_SALUTATION_LABEL]);
        $emailLabel             = stripslashes($options[RSVP_PRO_OPTION_EMAIL_LABEL]);
        $associatedMessage      = stripslashes($options[RSVP_PRO_OPTION_ASSOCIATED_MESSAGE]);
        $associatedGreeting     = stripslashes($options[RSVP_PRO_OPTION_ASSOCIATED_ATTENDEE_GREETING]);
        $multiEventText         = stripslashes($options[RSVP_PRO_OPTION_MUTLI_ATTENDEE_EVENTS_TEXT]);
        $multiEventTitle        = stripslashes($options[RSVP_PRO_OPTION_MULTI_EVENT_TITLE]);
        $multiOptionText        = stripslashes($options[RSVP_PRO_OPTION_MULTI_OPTION_TEXT]);
        $removeAttendeeButton   = stripslashes($options[RSVP_PRO_OPTION_REMOVE_ATTENDEE_BUTTON_TEXT]);
        $yesText                = stripslashes($options[RSVP_PRO_OPTION_YES_TEXT]);
        $noText                 = stripslashes($options[RSVP_PRO_OPTION_NO_TEXT]);
        $noResponseText         = stripslashes($options[RSVP_PRO_OPTION_NO_RESPONSE_TEXT]);
        $multipleMatchesText    = stripslashes($options[RSVP_PRO_OPTION_MULTIPLE_MATCHES_TEXT]);
        $fuzzyMatchText         = stripslashes($options[RSVP_PRO_OPTION_FUZZY_MATCH_TEXT]);
        $welcomeBackText        = stripslashes($options[RSVP_PRO_OPTION_WELCOME_BACK_TEXT]);
        $unableFindText         = stripslashes($options[RSVP_PRO_OPTION_UNABLE_TO_FIND_TEXT]);

        if(is_array($options[RSVP_PRO_OPTION_ADMIN_ROLES])) {
          $adminRoles = $options[RSVP_PRO_OPTION_ADMIN_ROLES];
        }

        if(is_array($options[RSVP_PRO_OPTION_ATTENDEE_LIST_CUSTOM_QUESTIONS])) {
          $attendeeListCustomQs = $options[RSVP_PRO_OPTION_ATTENDEE_LIST_CUSTOM_QUESTIONS];
        }
        
        if(!empty($options[RSVP_PRO_OPTION_SALUTATIONS])) {
          $salutations = stripslashes($options[RSVP_PRO_OPTION_SALUTATIONS]);
        }
        
        if(is_numeric($options[RSVP_PRO_OPTION_PASSWORD_LENGTH]) && ($options[RSVP_PRO_OPTION_PASSWORD_LENGTH] <= 50)) {
          $password_length = $options[RSVP_PRO_OPTION_PASSWORD_LENGTH];
        }
        
				// Get the associated attendees and add them to an array
				$associations = $wpdb->get_results($wpdb->prepare("SELECT rsvpAttendeeID FROM ".PRO_EVENT_ATTENDEE_TABLE." WHERE rsvpEventID = %d", $eventID));
				foreach($associations as $aId) {
					$attendeeAccess[] = $aId->rsvpAttendeeID;
				}
      }
    }
    
    $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'general';
    ?>
    <?php if($_GET['id'] > 0) : ?>
      <h3><?php echo get_event_name($_GET['id']); ?> <?php _e("Settings", "rsvp-pro-plugin"); ?></h3>
    <?php endif; ?>
    <div class="wrap">
  		<h2 class="nav-tab-wrapper">
  			<a href="<?php echo add_query_arg('tab', 'general', remove_query_arg('settings-updated')); ?>" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>"><?php _e('General', 'rsvp-pro-plugin'); ?></a>
        <?php if(isset($_GET['id']) && is_numeric($_GET['id']) && ($_GET['id'] > 0)) : ?>
    			<a href="<?php echo add_query_arg('tab', 'form', remove_query_arg('settings-updated')); ?>" class="nav-tab <?php echo $active_tab == 'form' ? 'nav-tab-active' : ''; ?>"><?php _e('Front-End', 'rsvp-pro-plugin'); ?></a>
    			<a href="<?php echo add_query_arg('tab', 'text', remove_query_arg('settings-updated')); ?>" class="nav-tab <?php echo $active_tab == 'text' ? 'nav-tab-active' : ''; ?>"><?php _e('Front-End Text', 'rsvp-pro-plugin'); ?></a>
          <?php if($event->parentEventID <= 0): ?>
          <a href="<?php echo add_query_arg('tab', 'attendeelist', remove_query_arg('settings-updated')); ?>" class="nav-tab <?php echo $active_tab == 'attendeelist' ? 'nav-tab-active' : ''; ?>"><?php _e('Public Attendee List', 'rsvp-pro-plugin'); ?></a>
          <a href="<?php echo add_query_arg('tab', 'email', remove_query_arg('settings-updated')); ?>" class="nav-tab <?php echo $active_tab == 'email' ? 'nav-tab-active' : ''; ?>"><?php _e('Notifications', 'rsvp-pro-plugin'); ?></a>
          <?php endif; ?>
        <?php endif; ?>
      </h2>
        
    <div id="tab_containter">
      <form name="rsvpEventForm" id="rsvpEventForm" method="post" action="<?php echo admin_url('admin.php?page=rsvp-pro-admin-manage-event&noheader=true'); ?>">
        <?php
        if($eventID > 0) {
        ?>
          <input type="hidden" name="eventID" id="eventID" value="<?php echo $eventID; ?>" />
        <?php
        }
        
        if($active_tab == "general") : 
        ?>
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_EVENT_COUNT_LIMIT; ?>" value="<?php echo esc_html($rsvpLimit)?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_ENABLE_WAITLIST; ?>" value="<?php echo esc_html($rsvpEnableWaitlist); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_CANT_EDIT; ?>" value="<?php echo htmlspecialchars($rsvpNoEditing); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_AUTO_LOGIN_ATTENDEE; ?>" value="<?php echo htmlspecialchars($attendeeAutoLogin); ?>" />
          <input type="hidden" name="rsvp_num_additional_guests" value="<?php echo htmlspecialchars($numAdditionalGuests); ?>" />
          <input type="hidden" name="require_passcode" value="<?php echo $requirePasscode; ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_PASSWORD_LENGTH; ?>" value="<?php echo $password_length; ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_ONLY_PASSCODE; ?>" value="<?php echo $onlyPasscode; ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_RSVP_OPEN_NO_PASSCODE; ?>" value="<?php echo esc_html($noPasscodeOnOpen); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_OPEN_REGISTRATION; ?>" value="<?php echo $rsvpOpenReg; ?>" />
          <input type="hidden" name="hide_additional_guests" value="<?php echo $hideAdditionalGuests; ?>" />
          <input type="hidden" name="rsvp_question_text" value="<?php echo htmlspecialchars($rsvpQuestionText); ?>" />
          <input type="hidden" name="rsvp_yes_label" value="<?php echo htmlspecialchars($rsvpYesLabel); ?>" />
          <input type="hidden" name="rsvp_no_label" value="<?php echo htmlspecialchars($rsvpNoLabel); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_WAITLIST_VERBIAGE; ?>" value="<?php echo htmlspecialchars($rsvpWaitlistLabel); ?>" />
          <input type="hidden" name="additional_note_label" value="<?php echo htmlspecialchars($additionalNoteLabel); ?>" />
          <input type="hidden" name="hide_note" value="<?php echo $hideNote; ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_DONT_USE_HASH; ?>" value="<?php echo $dontUseHash; ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_HIDE_EMAIL_FIELD; ?>" value="<?php echo $hideEmailField; ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_EMAIL_REQUIRED; ?>" value="<?php echo $requireEmail; ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_ADD_ADDITIONAL_VERBIAGE; ?>" value="<?php echo htmlspecialchars($addAdditionalText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_DISABLE_USER_SEARCH; ?>" value="<?php echo $rsvpDisableUserSearch; ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_FRONTEND_WIZARD; ?>" value="<?php echo $frontendWizard; ?>" />
          <input type="hidden" name="greeting_text" value="<?php echo htmlspecialchars($greetingText); ?>" />
          <input type="hidden" name="welcome_text" value="<?php echo htmlspecialchars($welcomeText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_EMAIL_TEXT; ?>" value="<?php echo htmlspecialchars($rsvpEmailText); ?>" />
          <input type="hidden" name="thank_you_text" value="<?php echo htmlspecialchars($thankYouText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_NOT_COMING; ?>" value="<?php echo esc_html($notComingVerbiage); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_SHOW_SALUTATION ?>" value="<?php echo esc_html($showSalutation); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_SHOW_SUFFIX; ?>" value="<?php echo esc_html($showSuffix); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_SALUTATIONS; ?>" value="<?php echo esc_html($salutations); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_SHOW_NORESPONSE_FOR_ASSOCIATED; ?>" value="<?php echo esc_html($showNoResponse); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_RSVP_REQUIRED; ?>" value="<?php echo esc_html($requireRsvpValues); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_NEW_ATTENDEE_BUTTON_TEXT; ?>" value="<?php echo esc_html($newAttendeeButtonText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_RSVP_BUTTON_TEXT;?>" value="<?php echo esc_html($rsvpFrontendButtonText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_NEXT_BUTTON_TEXT;?>" value="<?php echo esc_html($nextButtonText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_MAX_COUNT_REACHED_TEXT;?>" value="<?php echo esc_html($rsvpLimitText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_WAITLIST_TEXT; ?>" value="<?php echo esc_html($rsvpWaitlistText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_OPEN_DATE_TEXT; ?>" value="<?php echo esc_html($openDateText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_CLOSE_DATE_TEXT; ?>" value="<?php echo esc_html($closeDateText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_MODIFY_REGISTRATION_TEXT; ?>" value="<?php echo esc_attr_e($modifyRegistrationText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_ADD_ATTENDEE_BUTTON_TEXT; ?>" value="<?php echo esc_attr_e($addAttendeeButtonText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_ATTENDEE_LIST_FILTER; ?>" value="<?php echo esc_html($attendeelistFilter); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_ATTENDEE_LIST_SORT_ORDER; ?>" value="<?php echo esc_html($attendeelistSortOrder); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_ATTENDEE_LIST_HIDE_STATUS; ?>" value="<?php echo esc_attr_e($attendeeListHideStatus); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_ATTENDEE_LIST_CUSTOM_QUESTIONS; ?>" value="<?php echo esc_attr_e(implode(",", $attendeeListCustomQs)); ?>" />
          <input type="hidden" name="notify_user" value="<?php echo $notifyUser; ?>" />
          <input type="hidden" name="notify_email" value="<?php echo htmlspecialchars($notifyEmail); ?>" />
          <input type="hidden" name="rsvp_guest_email_confirmation" value="<?php echo $guestEmailConfirm; ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_EMAIL_FROM; ?>" value="<?php echo esc_attr_e($emailFrom); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_EMAIL_CC_ASSOCIATED; ?>" value="<?php echo esc_attr_e($emailCCAssociated); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_EMAIL_BCC_ADDRESS; ?>" value="<?php echo esc_attr_e($emailBCCAddresses); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_EMAIL_SUBJECT; ?>" value="<?php echo esc_attr_e($emailSubject); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_FIRST_NAME_LABEL; ?>" value="<?php echo esc_attr_e($firstNameLabel); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_LAST_NAME_LABEL; ?>" value="<?php echo esc_attr_e($lastNameLabel); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_PASSCODE_LABEL; ?>" value="<?php echo esc_attr_e($passcodeLabel); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_COMPLETE_BUTTON_TEXT; ?>" value="<?php echo esc_attr_e($completeButtonText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_EDIT_PROMPT_TEXT; ?>" value="<?php echo esc_attr_e($editPromptText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_SUFFIX_LABEL; ?>" value="<?php echo esc_attr_e($suffixLabel); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_SALUTATION_LABEL; ?>" value="<?php echo esc_attr_e($salutationLabel); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_EMAIL_LABEL; ?>" value="<?php echo esc_attr_e($emailLabel); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_ASSOCIATED_MESSAGE; ?>" value="<?php echo esc_attr_e($associatedMessage); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_ASSOCIATED_ATTENDEE_GREETING; ?>" value="<?php echo esc_attr_e($associatedGreeting); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_MULTI_EVENT_TITLE; ?>" value="<?php echo esc_attr_e($multiEventTitle); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_MULTI_OPTION_TEXT; ?>" value="<?php echo esc_attr_e($multiOptionText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_REMOVE_ATTENDEE_BUTTON_TEXT; ?>" value="<?php echo esc_attr_e($removeAttendeeButton); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_YES_TEXT; ?>" value="<?php echo esc_attr_e($yesText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_NO_TEXT; ?>" value="<?php echo esc_attr_e($noText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_NO_RESPONSE_TEXT; ?>" value="<?php echo esc_attr_e($noResponseText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_FUZZY_MATCH_TEXT; ?>" value="<?php echo esc_attr_e($fuzzyMatchText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_MULTIPLE_MATCHES_TEXT; ?>" value="<?php echo esc_attr_e($multipleMatchesText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_WELCOME_BACK_TEXT; ?>" value="<?php echo esc_attr_e($welcomeBackText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_UNABLE_TO_FIND_TEXT; ?>" value="<?php echo esc_attr_e($unableFindText); ?>" />
          <?php
          if(!$hasMultipleEvents):
          ?>
            <input type="hidden" name="event_access" value="<?php echo esc_html($eventAccess); ?>" />
            <input type="hidden" name="attendeeAccess" value="<?php echo implode(",", $attendeeAccess); ?>" />
          <?php
          endif;  
          ?>
          <table class="form-table">
            <tbody>
              <tr>
                <th scope="row">
                  <label for="rsvp_eventname"><?php _e("Event Name:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="text" class="regular_text" name="eventName" id="rsvp_eventname" value="<?php echo htmlspecialchars($eventName); ?>" />
                </td>
              </tr>
              <tr class="subEventHide">
                <th scope="row">
                  <label for="open_date"><?php _e("RSVP Open Date:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="text" name="open_date" id="open_date" value="<?php echo htmlspecialchars($opendate); ?>" />
                  <br />
                  <span class="description">mm/dd/yyyy (i.e. 05/05/2015)</span>
                </td>
              </tr>
              <tr class="subEventHide">
                <th scope="row">
                  <label for="close_date"><?php _e("RSVP Close Date:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="text" name="close_date" id="close_date" value="<?php echo htmlspecialchars($closedate); ?>" />
                  <br />
                  <span class="description">mm/dd/yyyy (i.e. 05/05/2015)</span>
                </td>
              </tr>
              <tr>
                <th scope="row">
                  <label for="parentEventID"><?php _e("Parent Event:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <select name="parentEventID" id="parentEventID" size="1">
                    <option value="">--</option>
                    <?php
                      $sql = "SELECT id, eventName FROM ".PRO_EVENT_TABLE." WHERE id <> %d AND (parentEventID IS NULL OR parentEventID = 0)";
                    $events = $wpdb->get_results($wpdb->prepare($sql, $_GET['id']));
                    foreach($events as $e) {
                    ?>
                      <option value="<?php echo $e->id; ?>" <?php echo ($e->id == $parentEventID) ? "selected=\"selected\"" : ""; ?>>
                        <?php echo esc_html($e->eventName); ?></option>  
                    <?php
                    }
                    ?>
                  </select>
                  <br />
                  <span class="description">Setting a parent event will allow you to share attendee lists and RSVP for all the 
                    events on one page.</span>
                </td>
              </tr>
              <tr>
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_OPTION_ADMIN_ROLES; ?>"><?php _e("Specify roles that can access configuration functionality:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <select name="adminRoles[]" id="<?php echo RSVP_PRO_OPTION_ADMIN_ROLES; ?>" multiple="multiple" size="5">
                    <?php
                      $roles = get_editable_roles();
                      foreach($roles as $role) {
                        if(strtolower($role["name"]) != "administrator") {
                    ?>
                          <option value="<?php echo esc_attr_e($role["name"]); ?>" 
                                  <?php echo ((in_array($role["name"], $adminRoles)) ? "selected=\"selected\"" : ""); ?>><?php echo esc_html($role["name"]); ?></option>
                    <?php
                        }
                      }
                    ?>
                  </select>
                  <br />
                  <span class="description">If no one is selected then everyone can access the configuration functionality. Administrators can not be locked out of settings.</span>
                </td>
              </tr>
              <?php
              if($hasMultipleEvents):
              ?>
              <tr>
                <th scope="row">
                  <label for="event_access"><?php _e("Attendee access to event:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <select name="event_access" id="event_access" size="1">
                    <option value="">--</option>
                    <option value="<?php echo RSVP_PRO_OPEN_EVENT_ACCESS; ?>" <?php echo ($eventAccess == RSVP_PRO_OPEN_EVENT_ACCESS) ? "selected=\"selected\"" : ""; ?>>Open</option>
                    <option value="<?php echo RSVP_PRO_PRIVATE_EVENT_ACCESS; ?>" <?php echo ($eventAccess == RSVP_PRO_PRIVATE_EVENT_ACCESS) ? "selected=\"selected\"" : ""; ?>>Select</option>
                  </select>
                  <br>
                  <span class="description">Defines access to an event: <br />
                    <b>Open</b> - all attendees are allowed to RSVP for the event<br /> 
                    <b>Select</b> - only the select attendees are allowed to RSVP for the event</span>
                </td>
              </tr>
              <tr>
                <th scope="row">
                  <label for="attendeeAccessSelect"><?php _e("Attendee Access List:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
    							<select name="attendeeAccess[]" id="attendeeAccessSelect" multiple="multiple" size="5">
    								<?php
    									$attendees = $wpdb->get_results($wpdb->prepare("SELECT id, firstName, lastName 
                                                                      FROM ".PRO_ATTENDEES_TABLE." 
                                                                      WHERE rsvpEventID = %d ORDER BY lastName, firstName", ($parentEventID > 0) ? $parentEventID : $eventID));
    									foreach($attendees as $a) {
    										if($a->id != $attendeeID) {
    								?>
    											<option value="<?php echo $a->id; ?>" 
    															<?php echo ((in_array($a->id, $attendeeAccess)) ? "selected=\"selected\"" : ""); ?>><?php echo esc_html(stripslashes($a->firstName)." ".stripslashes($a->lastName)); ?></option>
    								<?php
    										}
    									}
    								?>
    							</select>
                </td>
              </tr>
              <?php
              endif;
              ?>
            </tbody>
          </table>
        <?php
          endif;
          
          if($active_tab == "form"): 
        ?>
          <input type="hidden" name="eventName" value="<?php echo htmlspecialchars($eventName); ?>" />
          <input type="hidden" name="open_date" value="<?php echo htmlspecialchars($opendate); ?>" />
          <input type="hidden" name="close_date" value="<?php echo htmlspecialchars($closedate); ?>" />
          <input type="hidden" name="parentEventID" id="parentEventID" value="<?php echo htmlspecialchars($parentEventID); ?>" />
          <input type="hidden" name="notify_user" value="<?php echo $notifyUser; ?>" />
          <input type="hidden" name="notify_email" value="<?php echo htmlspecialchars($notifyEmail); ?>" />
          <input type="hidden" name="rsvp_guest_email_confirmation" value="<?php echo $guestEmailConfirm; ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_ADD_ADDITIONAL_VERBIAGE; ?>" value="<?php echo htmlspecialchars($addAdditionalText); ?>" />
          <input type="hidden" name="adminRoles" value="<?php echo esc_attr_e(implode(",", $adminRoles)); ?>" />
          <input type="hidden" name="greeting_text" value="<?php echo htmlspecialchars($greetingText); ?>" />
          <input type="hidden" name="welcome_text" value="<?php echo htmlspecialchars($welcomeText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_EMAIL_TEXT; ?>" value="<?php echo htmlspecialchars($rsvpEmailText); ?>" />
          <input type="hidden" name="thank_you_text" value="<?php echo htmlspecialchars($thankYouText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_NOT_COMING; ?>" value="<?php echo esc_html($notComingVerbiage); ?>" />
          <input type="hidden" name="event_access" value="<?php echo esc_html($eventAccess); ?>" />
          <input type="hidden" name="attendeeAccess" value="<?php echo implode(",", $attendeeAccess); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_NEW_ATTENDEE_BUTTON_TEXT; ?>" value="<?php echo esc_html($newAttendeeButtonText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_RSVP_BUTTON_TEXT; ?>" value="<?php echo esc_html($rsvpFrontendButtonText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_NEXT_BUTTON_TEXT;?>" value="<?php echo esc_html($nextButtonText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_MAX_COUNT_REACHED_TEXT;?>" value="<?php echo esc_html($rsvpLimitText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_WAITLIST_TEXT; ?>" value="<?php echo esc_html($rsvpWaitlistText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_OPEN_DATE_TEXT; ?>" value="<?php echo esc_html($openDateText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_CLOSE_DATE_TEXT; ?>" value="<?php echo esc_html($closeDateText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_ATTENDEE_LIST_FILTER; ?>" value="<?php echo esc_html($attendeelistFilter); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_ATTENDEE_LIST_SORT_ORDER; ?>" value="<?php echo esc_html($attendeelistSortOrder); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_ATTENDEE_LIST_HIDE_STATUS; ?>" value="<?php echo esc_attr_e($attendeeListHideStatus); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_ATTENDEE_LIST_CUSTOM_QUESTIONS; ?>" value="<?php echo esc_attr_e(implode(",", $attendeeListCustomQs)); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_MODIFY_REGISTRATION_TEXT; ?>" value="<?php echo esc_attr_e($modifyRegistrationText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_ADD_ATTENDEE_BUTTON_TEXT; ?>" value="<?php echo esc_attr_e($addAttendeeButtonText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_EMAIL_FROM; ?>" value="<?php echo esc_attr_e($emailFrom); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_EMAIL_CC_ASSOCIATED; ?>" value="<?php echo esc_attr_e($emailCCAssociated); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_EMAIL_BCC_ADDRESS; ?>" value="<?php echo esc_attr_e($emailBCCAddresses); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_EMAIL_SUBJECT; ?>" value="<?php echo esc_attr_e($emailSubject); ?>" />
          <input type="hidden" name="rsvp_question_text" value="<?php echo htmlspecialchars($rsvpQuestionText); ?>" />
          <input type="hidden" name="rsvp_yes_label" value="<?php echo htmlspecialchars($rsvpYesLabel); ?>" />
          <input type="hidden" name="rsvp_no_label" value="<?php echo htmlspecialchars($rsvpNoLabel); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_WAITLIST_VERBIAGE; ?>" value="<?php echo htmlspecialchars($rsvpWaitlistLabel); ?>" />
          <input type="hidden" name="additional_note_label" value="<?php echo htmlspecialchars($additionalNoteLabel); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_FIRST_NAME_LABEL; ?>" value="<?php echo esc_attr_e($firstNameLabel); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_LAST_NAME_LABEL; ?>" value="<?php echo esc_attr_e($lastNameLabel); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_PASSCODE_LABEL; ?>" value="<?php echo esc_attr_e($passcodeLabel); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_COMPLETE_BUTTON_TEXT; ?>" value="<?php echo esc_attr_e($completeButtonText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_EDIT_PROMPT_TEXT; ?>" value="<?php echo esc_attr_e($editPromptText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_SUFFIX_LABEL; ?>" value="<?php echo esc_attr_e($suffixLabel); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_SALUTATION_LABEL; ?>" value="<?php echo esc_attr_e($salutationLabel); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_EMAIL_LABEL; ?>" value="<?php echo esc_attr_e($emailLabel); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_ASSOCIATED_MESSAGE; ?>" value="<?php echo esc_attr_e($associatedMessage); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_ASSOCIATED_ATTENDEE_GREETING; ?>" value="<?php echo esc_attr_e($associatedGreeting); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_MULTI_EVENT_TITLE; ?>" value="<?php echo esc_attr_e($multiEventTitle); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_MULTI_OPTION_TEXT; ?>" value="<?php echo esc_attr_e($multiOptionText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_REMOVE_ATTENDEE_BUTTON_TEXT; ?>" value="<?php echo esc_attr_e($removeAttendeeButton); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_YES_TEXT; ?>" value="<?php echo esc_attr_e($yesText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_NO_TEXT; ?>" value="<?php echo esc_attr_e($noText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_NO_RESPONSE_TEXT; ?>" value="<?php echo esc_attr_e($noResponseText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_FUZZY_MATCH_TEXT; ?>" value="<?php echo esc_attr_e($fuzzyMatchText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_MULTIPLE_MATCHES_TEXT; ?>" value="<?php echo esc_attr_e($multipleMatchesText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_WELCOME_BACK_TEXT; ?>" value="<?php echo esc_attr_e($welcomeBackText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_UNABLE_TO_FIND_TEXT; ?>" value="<?php echo esc_attr_e($unableFindText); ?>" />
          <table class="form-table">
            <tbody>
              <tr>
                <th scope="row">
                  <h3><?php _e("General Form Settings", "rsvp-pro-plugin"); ?></h3>
                </th>
              </tr>

              <tr class="subEventHide">
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_OPTION_FRONTEND_WIZARD; ?>"><?php _e("Frontend has one step per event:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="checkbox" name="<?php echo RSVP_PRO_OPTION_FRONTEND_WIZARD; ?>" id="<?php echo RSVP_PRO_OPTION_FRONTEND_WIZARD; ?>" value="Y" <?php echo (($frontendWizard == "Y") ? " checked=\"checked\"" : ""); ?> />
                </td>
              </tr>
              <tr class="subEventHide">
                <th scope="row">
                  <label for="<?PHP echo RSVP_PRO_OPTION_DONT_USE_HASH; ?>"><?php _e("Remove scrolling to the top of the RSVP form:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="checkbox" name="<?php echo RSVP_PRO_OPTION_DONT_USE_HASH; ?>" id="<?php echo RSVP_PRO_OPTION_DONT_USE_HASH; ?>" value="Y" 
                               <?php echo (($dontUseHash == "Y") ? " checked=\"checked\"" : ""); ?> />
                </td>
              </tr>
              <tr class="subEventHide">
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_OPTION_DISABLE_USER_SEARCH; ?>"><?php _e("Disable fuzzy user search:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="checkbox" name="<?php echo RSVP_PRO_OPTION_DISABLE_USER_SEARCH; ?>" id="<?Php echo RSVP_PRO_OPTION_DISABLE_USER_SEARCH; ?>" value="Y" 
                    <?php echo (($rsvpDisableUserSearch == "Y") ? " checked=\"checked\"" : ""); ?> />
                </td>
              </tr>
              <tr>
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_OPTION_EVENT_COUNT_LIMIT; ?>"><?php _e("Max guest limit for event:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="text" name="<?php echo RSVP_PRO_OPTION_EVENT_COUNT_LIMIT; ?>" id="<?php echo RSVP_PRO_OPTION_EVENT_COUNT_LIMIT; ?>" value="<?php echo esc_html($rsvpLimit); ?>" />
                  <br>
                  <span class="description"><?php _e("Default has no max guest limit for each event", "rsvp-pro-plugin"); ?></span>
                </td>
              </tr>
              <tr>
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_OPTION_ENABLE_WAITLIST; ?>"><?php _e("Allow for a wait list:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="checkbox" name="<?php echo RSVP_PRO_OPTION_ENABLE_WAITLIST; ?>" id="<?php echo RSVP_PRO_OPTION_ENABLE_WAITLIST; ?>" value="Y" <?php echo (($rsvpEnableWaitlist == "Y") ? " checked=\"checked\"" : ""); ?> />
                  <br />
                  <span class="description"><?php _e("Only applies when an event specifies a max guest limit", "rsvp-pro-plugin"); ?></span>
                </td>
              </tr>
              <tr class="subEventHide">
                <th scope="row">
                  <label for="rsvp_num_additional_guests"><?php _e("Number of Additional Guests:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="text" name="rsvp_num_additional_guests" id="rsvp_num_additional_guests" value="<?php echo htmlspecialchars($numAdditionalGuests); ?>" />
                  <br>
                  <span class="description"><?php _e("Default is 3 guests, it is also possible to set different limits for individual attendees", "rsvp-pro-plugin"); ?></span>
                </td>
              </tr>
              <tr class="subEventHide">
                <th scope="row">
                  <label for="hide_additional_guests"><?php _e("No additional guests:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="checkbox" name="hide_additional_guests" id="hide_additional_guests" value="Y" <?php echo (($hideAdditionalGuests == "Y") ? " checked=\"checked\"" : ""); ?> />
                </td>
              </tr>
              <tr class="subEventHide">
                <th scope="row">
                  <label for="<?PHP echo RSVP_PRO_OPTION_OPEN_REGISTRATION; ?>"><?php _e("Allow Open Registration:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="checkbox" name="<?php echo RSVP_PRO_OPTION_OPEN_REGISTRATION; ?>" id="<?php echo RSVP_PRO_OPTION_OPEN_REGISTRATION; ?>" value="Y" 
                     <?php echo (($rsvpOpenReg == "Y") ? " checked=\"checked\"" : ""); ?> />
                  <br>
                  <span class="description">This will force passcodes for attendees</span>
                </td>
              </tr>
              <tr class="subEventHide">
                <th scope="row">
                  <label for="<?PHP echo RSVP_PRO_OPTION_CANT_EDIT; ?>"><?php _e("Attendees can't edit their RSVP:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="checkbox" name="<?php echo RSVP_PRO_OPTION_CANT_EDIT; ?>" id="<?php echo RSVP_PRO_OPTION_CANT_EDIT; ?>" value="Y" 
                     <?php echo (($rsvpNoEditing == "Y") ? " checked=\"checked\"" : ""); ?> />
                  <br>
                  <span class="description">This should only be used in conjunction with open registrations</span>
                </td>
              </tr>
              <tr class="subEventHide">
                <th scope="row">
                  <label for="<?PHP echo RSVP_PRO_OPTION_AUTO_LOGIN_ATTENDEE; ?>"><?php _e("Automatically authenticate logged in WP-Users:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="checkbox" name="<?php echo RSVP_PRO_OPTION_AUTO_LOGIN_ATTENDEE; ?>" id="<?php echo RSVP_PRO_OPTION_AUTO_LOGIN_ATTENDEE; ?>" value="Y" 
                     <?php echo (($attendeeAutoLogin == "Y") ? " checked=\"checked\"" : ""); ?> />
                  <br>
                  <span class="description">Checking this option will automatically skip the user lookup form if they are logged in and the user and attendee emails match</span>
                </td>
              </tr>

              <tr class="subEventHide">
                <th scope="row">
                  <h3><?php _e("Form Field Settings", "rsvp-pro-plugin"); ?></h3>
                </th>
              </tr>

              <tr class="subEventHide">
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_OPTION_SHOW_SALUTATION; ?>"><?php _e("Show Salutations:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="checkbox" name="<?php echo RSVP_PRO_OPTION_SHOW_SALUTATION; ?>" id="<?php echo RSVP_PRO_OPTION_SHOW_SALUTATION; ?>" value="Y" <?php echo (($showSalutation == "Y") ? " checked=\"checked\"" : ""); ?> />
                </td>
              </tr>
              <tr class="subEventHide" id="rsvpProOptionSalutationsContainer">
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_OPTION_SALUTATIONS; ?>"><?php _e("Possible Salutations:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <textarea name="<?php echo RSVP_PRO_OPTION_SALUTATIONS; ?>" id="<?php echo RSVP_PRO_OPTION_SALUTATIONS; ?>" rows="3" cols="60"><?php echo esc_html(str_replace("||", "\r\n", $salutations)); ?></textarea>
                  <br>
                  <span class="description"><?php _e("One salutation per-line", "rsvp-pro-plugin"); ?></span>
                </td>
              </tr>
              <tr class="subEventHide">
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_OPTION_SHOW_SUFFIX; ?>"><?php _e("Show Suffix:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="checkbox" name="<?php echo RSVP_PRO_OPTION_SHOW_SUFFIX; ?>" id="<?php echo RSVP_PRO_OPTION_SHOW_SUFFIX; ?>" value="Y" <?php echo (($showSuffix == "Y") ? " checked=\"checked\"" : ""); ?> />
                </td>
              </tr>
              <tr class="subEventHide">
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_OPTION_SHOW_NORESPONSE_FOR_ASSOCIATED; ?>"><?php _e("Show &quot;No Response&quot; for Associated Attendees:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="checkbox" name="<?php echo RSVP_PRO_OPTION_SHOW_NORESPONSE_FOR_ASSOCIATED; ?>" id="<?php echo RSVP_PRO_OPTION_SHOW_NORESPONSE_FOR_ASSOCIATED; ?>" value="Y" <?php echo (($showNoResponse == "Y") ? " checked=\"checked\"" : ""); ?> />
                </td>
              </tr>
              <tr class="subEventHide">
                <th scope="row">
                  <label for="hide_note"><?php _e("Hide Note Field:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="checkbox" name="hide_note" id="hide_note" value="Y" <?php echo (($hideNote == "Y") ? " checked=\"checked\"" : ""); ?> />
                </td>
              </tr>
              <tr class="subEventHide">
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_HIDE_EMAIL_FIELD; ?>"><?php _e("Hide email field on rsvp form:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="checkbox" name="<?php echo RSVP_PRO_HIDE_EMAIL_FIELD; ?>" id="<?Php echo RSVP_PRO_HIDE_EMAIL_FIELD; ?>" value="Y" 
                    <?php echo (($hideEmailField == "Y") ? " checked=\"checked\"" : ""); ?> />
                </td>
              </tr>
              <tr class="subEventHide">
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_OPTION_EMAIL_REQUIRED; ?>"><?php _e("Require email question:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="checkbox" name="<?php echo RSVP_PRO_OPTION_EMAIL_REQUIRED; ?>" id="<?Php echo RSVP_PRO_OPTION_EMAIL_REQUIRED; ?>" value="Y" 
                    <?php echo (($requireEmail == "Y") ? " checked=\"checked\"" : ""); ?> />
                </td>
              </tr>
              <tr class="subEventHide">
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_OPTION_RSVP_REQUIRED; ?>"><?php _e("Require RSVP question:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="checkbox" name="<?php echo RSVP_PRO_OPTION_RSVP_REQUIRED; ?>" id="<?Php echo RSVP_PRO_OPTION_RSVP_REQUIRED; ?>" value="Y" 
                    <?php echo (($requireRsvpValues == "Y") ? " checked=\"checked\"" : ""); ?> />
                </td>
              </tr>

              <tr class="subEventHide">
                <th scope="row">
                  <h3><?php _e("Passcode Settings", "rsvp-pro-plugin"); ?></h3>
                </th>
              </tr>

              <tr class="subEventHide">
                <th scope="row">
                  <label for="require_passcode"><?php _e("Require a Passcode to RSVP:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="checkbox" name="require_passcode" id="require_passcode" value="Y" <?php echo (($requirePasscode == "Y") ? " checked=\"checked\"" : ""); ?> />
                </td>
              </tr>
              <tr class="subEventHide">
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_OPTION_PASSWORD_LENGTH; ?>"><?php _e("Passcode length:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="number" name="<?php echo RSVP_PRO_OPTION_PASSWORD_LENGTH; ?>" id="<?php echo RSVP_PRO_OPTION_PASSWORD_LENGTH; ?>" value="<?php echo $password_length; ?>" min="1" max="50" />
                  <br>
                  <span class="description"><?php _e("Valid values 1 to 50", "rsvp-pro-plugin"); ?></span>
                </td>
              </tr>
              <tr class="subEventHide">
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_ONLY_PASSCODE; ?>"><?php _e("Just a Passcode to RSVP:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="checkbox" name="<?php echo RSVP_PRO_ONLY_PASSCODE; ?>" id="<?php echo RSVP_PRO_ONLY_PASSCODE; ?>" value="Y" <?php echo (($onlyPasscode == "Y") ? " checked=\"checked\"" : ""); ?> />
                  <br />
                  <span class="description"><?php _e("Requires that passcodes are unique", "rsvp-pro-plugin"); ?></span>
                </td>
              </tr>
              <tr class="subEventHide">
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_OPTION_RSVP_OPEN_NO_PASSCODE; ?>"><?php _e("No passcode required on open registration:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="checkbox" name="<?php echo RSVP_PRO_OPTION_RSVP_OPEN_NO_PASSCODE; ?>" id="<?php echo RSVP_PRO_OPTION_RSVP_OPEN_NO_PASSCODE; ?>" value="Y" <?php echo (($noPasscodeOnOpen == "Y") ? " checked=\"checked\"" : ""); ?> />
                  <br>
                  <span class="description"><?php _e("This will override the requirement of having a passcode for open registration", "rsvp-pro-plugin"); ?></span>
                </td>
              </tr>
            </tbody>
          </table>
        <?php
          endif;
          
          if($active_tab == "text"):
        ?>
          <input type="hidden" name="eventName" value="<?php echo htmlspecialchars($eventName); ?>" />
          <input type="hidden" name="open_date" value="<?php echo htmlspecialchars($opendate); ?>" />
          <input type="hidden" name="close_date" value="<?php echo htmlspecialchars($closedate); ?>" />
          <input type="hidden" name="parentEventID" id="parentEventID" value="<?php echo htmlspecialchars($parentEventID); ?>" />
          <input type="hidden" name="rsvp_num_additional_guests" value="<?php echo htmlspecialchars($numAdditionalGuests); ?>" />
          <input type="hidden" name="require_passcode" value="<?php echo $requirePasscode; ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_PASSWORD_LENGTH; ?>" value="<?php echo $password_length; ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_ONLY_PASSCODE; ?>" value="<?php echo $onlyPasscode; ?>" />
          <input type="hidden" name="hide_additional_guests" value="<?php echo $hideAdditionalGuests; ?>" />
          <input type="hidden" name="notify_user" value="<?php echo $notifyUser; ?>" />
          <input type="hidden" name="notify_email" value="<?php echo htmlspecialchars($notifyEmail); ?>" />
          <input type="hidden" name="rsvp_guest_email_confirmation" value="<?php echo $guestEmailConfirm; ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_OPEN_REGISTRATION; ?>" value="<?php echo $rsvpOpenReg; ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_CANT_EDIT; ?>" value="<?php echo htmlspecialchars($rsvpNoEditing); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_AUTO_LOGIN_ATTENDEE; ?>" value="<?php echo htmlspecialchars($attendeeAutoLogin); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_EVENT_COUNT_LIMIT; ?>" value="<?php echo esc_html($rsvpLimit)?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_ENABLE_WAITLIST; ?>" value="<?php echo esc_html($rsvpEnableWaitlist); ?>" />
          <input type="hidden" name="adminRoles" value="<?php echo esc_attr_e(implode(",", $adminRoles)); ?>" />
          <input type="hidden" name="hide_note" value="<?php echo $hideNote; ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_DONT_USE_HASH; ?>" value="<?php echo $dontUseHash; ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_HIDE_EMAIL_FIELD; ?>" value="<?php echo $hideEmailField; ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_EMAIL_REQUIRED; ?>" value="<?php echo $requireEmail; ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_DISABLE_USER_SEARCH; ?>" value="<?php echo $rsvpDisableUserSearch; ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_SHOW_SALUTATION ?>" value="<?php echo esc_html($showSalutation); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_SHOW_SUFFIX; ?>" value="<?php echo esc_html($showSuffix); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_SALUTATIONS; ?>" value="<?php echo esc_html($salutations); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_SHOW_NORESPONSE_FOR_ASSOCIATED; ?>" value="<?php echo esc_html($showNoResponse); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_FRONTEND_WIZARD; ?>" value="<?php echo $frontendWizard; ?>" />
          <input type="hidden" name="event_access" value="<?php echo esc_html($eventAccess); ?>" />
          <input type="hidden" name="attendeeAccess" value="<?php echo implode(",", $attendeeAccess); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_RSVP_REQUIRED; ?>" value="<?php echo esc_html($requireRsvpValues); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_RSVP_OPEN_NO_PASSCODE; ?>" value="<?php echo esc_html($noPasscodeOnOpen); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_ATTENDEE_LIST_FILTER; ?>" value="<?php echo esc_html($attendeelistFilter); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_ATTENDEE_LIST_SORT_ORDER; ?>" value="<?php echo esc_html($attendeelistSortOrder); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_ATTENDEE_LIST_HIDE_STATUS; ?>" value="<?php echo esc_attr_e($attendeeListHideStatus); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_ATTENDEE_LIST_CUSTOM_QUESTIONS; ?>" value="<?php echo esc_attr_e(implode(",", $attendeeListCustomQs)); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_EMAIL_FROM; ?>" value="<?php echo esc_attr_e($emailFrom); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_EMAIL_CC_ASSOCIATED; ?>" value="<?php echo esc_attr_e($emailCCAssociated); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_EMAIL_BCC_ADDRESS; ?>" value="<?php echo esc_attr_e($emailBCCAddresses); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_EMAIL_SUBJECT; ?>" value="<?php echo esc_attr_e($emailSubject); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_EMAIL_TEXT; ?>" value="<?php echo esc_attr_e($rsvpEmailText); ?>" />
          <table class="form-table">
            <tbody>
              <tr class="subEventHide">
                <th scope="row" colspan="2">
                  <h3><?php _e("General", "rsvp-pro-plugin"); ?></h3>
                </th>
              </tr>

              <tr class="subEventHide">
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_OPTION_FIRST_NAME_LABEL; ?>"><?php _e("&quot;First Name&quot; label:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="text" name="<?php echo RSVP_PRO_OPTION_FIRST_NAME_LABEL; ?>" id="<?php echo RSVP_PRO_OPTION_FIRST_NAME_LABEL; ?>" 
                  value="<?php echo esc_attr_e($firstNameLabel); ?>" size="65" />
                </td>
              </tr>
              <tr class="subEventHide">
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_OPTION_LAST_NAME_LABEL; ?>"><?php _e("&quot;Last Name&quot; label:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="text" name="<?php echo RSVP_PRO_OPTION_LAST_NAME_LABEL; ?>" id="<?php echo RSVP_PRO_OPTION_LAST_NAME_LABEL; ?>" 
                  value="<?php echo esc_attr_e($lastNameLabel); ?>" size="65" />
                </td>
              </tr>
              <tr class="subEventHide">
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_OPTION_YES_TEXT; ?>"><?php _e("&quot;Yes&quot; text:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="text" name="<?php echo RSVP_PRO_OPTION_YES_TEXT; ?>" id="<?php echo RSVP_PRO_OPTION_YES_TEXT; ?>" 
                  value="<?php echo esc_attr_e($yesText); ?>" />
                </td>
              </tr>
              <tr class="subEventHide">
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_OPTION_NO_TEXT; ?>"><?php _e("&quot;No&quot; text:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="text" name="<?php echo RSVP_PRO_OPTION_NO_TEXT; ?>" id="<?php echo RSVP_PRO_OPTION_NO_TEXT; ?>" 
                  value="<?php echo esc_attr_e($noText); ?>" />
                </td>
              </tr>
              <tr class="subEventHide">
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_OPTION_NO_RESPONSE_TEXT; ?>"><?php _e("&quot;No Response&quot; text:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="text" name="<?php echo RSVP_PRO_OPTION_NO_RESPONSE_TEXT; ?>" id="<?php echo RSVP_PRO_OPTION_NO_RESPONSE_TEXT; ?>" 
                  value="<?php echo esc_attr_e($noResponseText); ?>" />
                </td>
              </tr>

              <tr class="subEventHide">
                <th scope="row" colspan="2">
                  <h3><?php _e("Greeting Page", "rsvp-pro-plugin"); ?></h3>
                </th>
              </tr>

              <tr class="subEventHide">
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_OPTION_OPEN_DATE_TEXT; ?>"><?php _e("Message displayed before the event is open for RSVP'ing", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <textarea name="<?php echo RSVP_PRO_OPTION_OPEN_DATE_TEXT; ?>" id="<?php echo RSVP_PRO_OPTION_OPEN_DATE_TEXT; ?>" rows="5" cols="60"><?php echo esc_html($openDateText); ?></textarea>
                  <br />
                  <span class="description"><?php _e("Default is: &quot;I am sorry but the ability to RSVP for our event won't open till &lt;strong&gt;%s&lt;/strong&gt;&quot;", "rsvp-pro-plugin"); ?></span>
                </td>
              </tr>
              <tr class="subEventHide">
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_OPTION_CLOSE_DATE_TEXT; ?>"><?php _e("Message displayed once the event is closed for RSVP'ing", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <textarea name="<?php echo RSVP_PRO_OPTION_CLOSE_DATE_TEXT; ?>" id="<?php echo RSVP_PRO_OPTION_CLOSE_DATE_TEXT; ?>" rows="5" cols="60"><?php echo esc_html($closeDateText); ?></textarea>
                  <br />
                  <span class="description"><?php _e("Default is: &quot;The deadline to RSVP for this event has passed.&quot;", "rsvp-pro-plugin"); ?></span>
                </td>
              </tr>
              <tr class="subEventHide">
                <th scope="row">
                  <label for="greeting_text"><?php _e("Custom greeting:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <textarea name="greeting_text" id="greeting_text" rows="5" cols="60"><?php echo htmlspecialchars($greetingText); ?></textarea>
                  <br>
                  <span class="description"><?php _e("Default is: &quot;Please enter your first and last name to RSVP.&quot;", "rsvp-pro-plugin"); ?></span>
                </td>
              </tr>
              <tr class="subEventHide">
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_OPTION_MODIFY_REGISTRATION_TEXT; ?>"><?php _e("&quot;Modify Attendee&quot; button:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="text" name="<?php echo RSVP_PRO_OPTION_MODIFY_REGISTRATION_TEXT; ?>" id="<?php echo RSVP_PRO_OPTION_MODIFY_REGISTRATION_TEXT; ?>" 
                  value="<?php echo esc_attr_e($modifyRegistrationText); ?>" size="65" />
                  <br>
                  <span class="description"><?php _e("Default text is: &quot;Need to modify your registration? Start with the below form.&quot;", "rsvp-pro-plugin"); ?></span>
                </td>
              </tr>
              <tr class="subEventHide">
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_OPTION_PASSCODE_LABEL; ?>"><?php _e("&quot;Passcode&quot; label:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="text" name="<?php echo RSVP_PRO_OPTION_PASSCODE_LABEL; ?>" id="<?php echo RSVP_PRO_OPTION_PASSCODE_LABEL; ?>" 
                  value="<?php echo esc_attr_e($passcodeLabel); ?>" size="65" />
                </td>
              </tr>
              <tr class="subEventHide">
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_OPTION_NEW_ATTENDEE_BUTTON_TEXT; ?>"><?php _e("&quot;New Attendee&quot; button:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="text" name="<?php echo RSVP_PRO_OPTION_NEW_ATTENDEE_BUTTON_TEXT; ?>" id="<?php echo RSVP_PRO_OPTION_NEW_ATTENDEE_BUTTON_TEXT; ?>" 
                  value="<?php echo esc_attr_e($newAttendeeButtonText); ?>" size="65" />
                  <br>
                  <span class="description"><?php _e("Default text is: &quot;New Attendee Registration&quot;", "rsvp-pro-plugin"); ?></span>
                </td>
              </tr>
              <tr class="subEventHide">
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_OPTION_COMPLETE_BUTTON_TEXT; ?>"><?php _e("&quot;Complete RSVP&quot; button:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="text" name="<?php echo RSVP_PRO_OPTION_COMPLETE_BUTTON_TEXT; ?>" id="<?php echo RSVP_PRO_OPTION_COMPLETE_BUTTON_TEXT; ?>" 
                  value="<?php echo esc_attr_e($completeButtonText); ?>" size="65" />
                  <br>
                  <span class="description"><?php _e("Default text is: &quot;Complete your RSVP!&quot;", "rsvp-pro-plugin"); ?></span>
                </td>
              </tr>
              <tr class="subEventHide">
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_OPTION_UNABLE_TO_FIND_TEXT; ?>"><?php _e("Unable to find:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="text" name="<?php echo RSVP_PRO_OPTION_UNABLE_TO_FIND_TEXT; ?>" id="<?php echo RSVP_PRO_OPTION_UNABLE_TO_FIND_TEXT; ?>" 
                  value="<?php echo esc_attr_e($unableFindText); ?>" size="65" />
                  <br>
                  <span class="description"><?php _e("Default text is: &quot;We were unable to find anyone with a name of...&quot;", "rsvp-pro-plugin"); ?></span>
                </td>
              </tr>


              <tr class="subEventHide">
                <th scope="row" colspan="2">
                  <h3><?php _e("Edit Confirmation Page", "rsvp-pro-plugin"); ?></h3>
                </th>
              </tr>

              <tr class="subEventHide">
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_OPTION_EDIT_PROMPT_TEXT; ?>"><?php _e("Edit prompt:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="text" name="<?php echo RSVP_PRO_OPTION_EDIT_PROMPT_TEXT; ?>" id="<?php echo RSVP_PRO_OPTION_EDIT_PROMPT_TEXT; ?>" 
                  value="<?php echo esc_attr_e($editPromptText); ?>" size="65" />
                  <br>
                  <span class="description"><?php _e("Default text is: &quot;Hi %s it looks like you have already RSVP'd. Would you like to edit your reservation?&quot;", "rsvp-pro-plugin"); ?></span>
                </td>
              </tr>
          
              <tr class="subEventHide">
                <th scope="row" colspan="2">
                  <h3><?php _e("Not found screen", "rsvp-pro-plugin"); ?></h3>
                </th>
              </tr>
              <tr class="subEventHide">
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_OPTION_FUZZY_MATCH_TEXT; ?>"><?php _e("Fuzzy match results text:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="text" name="<?php echo RSVP_PRO_OPTION_FUZZY_MATCH_TEXT; ?>" id="<?php echo RSVP_PRO_OPTION_FUZZY_MATCH_TEXT; ?>" 
                  value="<?php echo esc_attr_e($fuzzyMatchText); ?>" size="65" />
                  <br>
                  <span class="description"><?php _e("Default text is: &quot;We could not find an exact match but could any of the below entries be you?&quot;", "rsvp-pro-plugin"); ?></span>
                </td>
              </tr>  
              <tr class="subEventHide">
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_OPTION_MULTIPLE_MATCHES_TEXT; ?>"><?php _e("Multiple match results text:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="text" name="<?php echo RSVP_PRO_OPTION_MULTIPLE_MATCHES_TEXT; ?>" id="<?php echo RSVP_PRO_OPTION_MULTIPLE_MATCHES_TEXT; ?>" 
                  value="<?php echo esc_attr_e($multipleMatchesText); ?>" size="65" />
                  <br>
                  <span class="description"><?php _e("Default text is: &quot;we found multiple people with that name, please select your record&quot;", "rsvp-pro-plugin"); ?></span>
                </td>
              </tr>     


              <tr class="subEventHide">
                <th scope="row" colspan="2">
                  <h3><?php _e("Main RSVP Page", "rsvp-pro-plugin"); ?></h3>
                </th>
              </tr>

              <tr class="subEventHide">
                <th scope="row">
                  <label for="welcome_text"><?php _e("Custom welcome:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <textarea name="welcome_text" id="welcome_text" rows="5" cols="60"><?php echo htmlspecialchars($welcomeText); ?></textarea>
                  <br>
                  <span class="description"><?php _e("Default is: &quot;There are a few more questions we need to ask you if 
                    you could please fill them out below to finish up the RSVP process.&quot;", "rsvp-pro-plugin"); ?></span>
                </td>
              </tr>
              <tr class="subEventHide">
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_OPTION_WELCOME_BACK_TEXT; ?>"><?php _e("Welcome back:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="text" name="<?php echo RSVP_PRO_OPTION_WELCOME_BACK_TEXT; ?>" id="<?php echo RSVP_PRO_OPTION_WELCOME_BACK_TEXT; ?>" value="<?php echo esc_attr_e($welcomeBackText); ?>" size="65" />
                  <br>
                  <span class="description"><?php _e("Default is: &quot;Welcome back&quot;", "rsvp-pro-plugin"); ?></span>
                </td>
              </tr>
              <tr>
                <th scope="row">
                  <label for="rsvp_question_text"><?php _e("RSVP question verbiage:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="text" name="rsvp_question_text" id="rsvp_question_text" value="<?php echo htmlspecialchars($rsvpQuestionText); ?>" size="65" />
                  <br>
                  <span class="description"><?php _e("Default is: &quot;So, how about it?&quot;", "rsvp-pro-plugin"); ?></span>
                </td>
              </tr>
              <tr>
                <th scope="row">
                  <label for="rsvp_yes_label"><?php _e("RSVP yes:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="text" name="rsvp_yes_label" id="rsvp_yes_label" value="<?php echo htmlspecialchars($rsvpYesLabel); ?>" size="65" />
                  <br>
                  <span class="description"><?php _e("Default is: &quot;Yes, of course I will be there! Who doesn't like family, friends, weddings, and a good time?&quot;", "rsvp-pro-plugin"); ?></span>
                </td>
              </tr>
              <tr>
                <th scope="row">
                  <label for="rsvp_no_label"><?php _e("RSVP no:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="text" name="rsvp_no_label" id="rsvp_no_label" value="<?php echo htmlspecialchars($rsvpNoLabel); ?>" size="65" />
                  <br>
                  <span class="description"><?php _e("Default is: &quot;Um, unfortunately, there is a Star Trek marathon on that day that I just cannot miss.&quot;", "rsvp-pro-plugin"); ?></span>
                </td>
              </tr>
              <tr>
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_OPTION_WAITLIST_VERBIAGE; ?>"><?php _e("RSVP waitlist:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="text" name="<?php echo RSVP_PRO_OPTION_WAITLIST_VERBIAGE; ?>" id="<?php echo RSVP_PRO_OPTION_WAITLIST_VERBIAGE; ?>" value="<?php echo esc_attr_e($rsvpWaitlistLabel); ?>" size="65" />
                  <br>
                  <span class="description"><?php _e("Default is: &quot;The event is full but we can add you to the waitlist.&quot;", "rsvp-pro-plugin"); ?></span>
                </td>
              </tr>
              <tr class="subEventHide">
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_OPTION_SALUTATION_LABEL; ?>"><?php _e("&quot;Salutation&quot; label:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="text" name="<?php echo RSVP_PRO_OPTION_SALUTATION_LABEL; ?>" id="<?php echo RSVP_PRO_OPTION_SALUTATION_LABEL; ?>" value="<?php echo esc_attr_e($salutationLabel); ?>" />
                  <br>
                  <span class="description"><?php _e("Default is: &quot;Salutation&quot;", "rsvp-pro-plugin"); ?></span>
                </td>
              </tr>
              <tr class="subEventHide">
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_OPTION_SUFFIX_LABEL; ?>"><?php _e("&quot;Suffix&quot; label:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="text" name="<?php echo RSVP_PRO_OPTION_SUFFIX_LABEL; ?>" id="<?php echo RSVP_PRO_OPTION_SUFFIX_LABEL; ?>" value="<?php echo esc_attr_e($suffixLabel); ?>" />
                  <br>
                  <span class="description"><?php _e("Default is: &quot;Suffix&quot;", "rsvp-pro-plugin"); ?></span>
                </td>
              </tr>
              <tr class="subEventHide">
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_OPTION_EMAIL_LABEL; ?>"><?php _e("&quot;Email&quot; label:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="text" name="<?php echo RSVP_PRO_OPTION_EMAIL_LABEL; ?>" id="<?php echo RSVP_PRO_OPTION_EMAIL_LABEL; ?>" value="<?php echo esc_attr_e($emailLabel); ?>" />
                  <br>
                  <span class="description"><?php _e("Default is: &quot;Email address&quot;", "rsvp-pro-plugin"); ?></span>
                </td>
              </tr>
              <tr class="subEventHide">
                <th scope="row">
                  <label for="additional_note_label"><?php _e("Note question:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <textarea name="additional_note_label" id="additional_note_label" rows="3" cols="60"><?php echo htmlspecialchars($additionalNoteLabel); ?></textarea>
                  <br>
                  <span class="description"><?php _e("Default is: &quot;If you have any <strong style=\"color:red;\">food allergies</strong>, please indicate what 
                  they are in the 'notes' section below.  Or, if you just want to send us a note, please feel free. If you 
                  have any questions, please send us an email.&quot;", "rsvp-pro-plugin"); ?></span>
                </td>
              </tr>
              <tr class="subEventHide" class="subEventHide">
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_OPTION_RSVP_BUTTON_TEXT; ?>"><?php _e("&quot;RSVP&quot; button:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
        					<input type="text" name="<?php echo RSVP_PRO_OPTION_RSVP_BUTTON_TEXT; ?>" id="<?php echo RSVP_PRO_OPTION_RSVP_BUTTON_TEXT; ?>" 
        					value="<?php echo htmlspecialchars($rsvpFrontendButtonText); ?>" size="65" />
                  <br>
                  <span class="description"><?php _e("Default text is: &quot;RSVP&quot;", "rsvp-pro-plugin"); ?></span>
                </td>
              </tr>
              <tr class="subEventHide">
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_OPTION_NEXT_BUTTON_TEXT; ?>"><?php _e("&quot;Next&quot; button:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
        					<input type="text" name="<?php echo RSVP_PRO_OPTION_NEXT_BUTTON_TEXT; ?>" id="<?php echo RSVP_PRO_OPTION_NEXT_BUTTON_TEXT; ?>" 
        					value="<?php echo esc_attr_e($nextButtonText); ?>" size="65" />
                  <br>
                  <span class="description"><?php _e("Default text is: &quot;Next&quot; (this only applies to when the multiple step option is enabled)", "rsvp-pro-plugin"); ?></span>
                </td>
              </tr>
              <tr class="subEventHide">
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_OPTION_ADD_ADDITIONAL_VERBIAGE; ?>"><?php _e("Add additional:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="text" name="<?php echo RSVP_PRO_OPTION_ADD_ADDITIONAL_VERBIAGE; ?>" id="<?php echo RSVP_PRO_OPTION_ADD_ADDITIONAL_VERBIAGE; ?>" 
                  value="<?php echo stripslashes(esc_attr_e($addAdditionalText)); ?>" size="65" />
                  <br>
                  <span class="description"><?php _e("Default is: &quot;You currently can invite &lt;span id=\"numAvailableToAdd\"&gt;%d&lt;/span&gt; more people.&quot;", "rsvp-pro-plugin"); ?></span>
                </td>
              </tr>
              <tr class="subEventHide">
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_OPTION_ASSOCIATED_ATTENDEE_GREETING; ?>"><?php _e("Associated attendee greeting:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
        					<input type="text" name="<?php echo RSVP_PRO_OPTION_ASSOCIATED_ATTENDEE_GREETING; ?>" id="<?php echo RSVP_PRO_OPTION_ASSOCIATED_ATTENDEE_GREETING; ?>" 
        					value="<?php echo stripslashes(esc_attr_e($associatedGreeting)); ?>" size="65" />
                  <br>
                  <span class="description"><?php _e("Default is: &quot;Will %s be attending?&quot;", "rsvp-pro-plugin"); ?></span>
                </td>
              </tr>
              <tr class="subEventHide">
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_OPTION_ADD_ATTENDEE_BUTTON_TEXT; ?>"><?php _e("New attendee button:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="text" name="<?php echo RSVP_PRO_OPTION_ADD_ATTENDEE_BUTTON_TEXT; ?>" id="<?php echo RSVP_PRO_OPTION_ADD_ATTENDEE_BUTTON_TEXT; ?>" 
                  value="<?php echo esc_attr_e($addAttendeeButtonText); ?>" size="65" />
                  <br>
                  <span class="description"><?php _e("Default is: &quot;Add Additional Guests&quot;", "rsvp-pro-plugin"); ?></span>
                </td>
              </tr>
              <tr class="subEventHide">
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_OPTION_REMOVE_ATTENDEE_BUTTON_TEXT; ?>"><?php _e("Remove attendee button:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="text" name="<?php echo RSVP_PRO_OPTION_REMOVE_ATTENDEE_BUTTON_TEXT; ?>" id="<?php echo RSVP_PRO_OPTION_REMOVE_ATTENDEE_BUTTON_TEXT; ?>" 
                  value="<?php echo esc_attr_e($removeAttendeeButton); ?>" size="65" />
                  <br>
                  <span class="description"><?php _e("Default is: &quot;Remove Guest&quot;", "rsvp-pro-plugin"); ?></span>
                </td>
              </tr>
              <tr>
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_OPTION_MAX_COUNT_REACHED_TEXT; ?>"><?php _e("Message when the max guest limit is reached:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <textarea name="<?php echo RSVP_PRO_OPTION_MAX_COUNT_REACHED_TEXT; ?>" id="<?php echo RSVP_PRO_OPTION_MAX_COUNT_REACHED_TEXT; ?>" rows="5" cols="60"><?php echo esc_html($rsvpLimitText); ?></textarea>
                  <br />
					        <span class="description"><?php _e("Default is: &quot;The maximum limit of %d has been reached for this event.&quot;", "rsvp-pro-plugin"); ?></span>                  
                </td>
              </tr>
              <tr>
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_OPTION_WAITLIST_TEXT; ?>"><?php _e("Waitlist text:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <textarea name="<?php echo RSVP_PRO_OPTION_WAITLIST_TEXT; ?>" id="<?php echo RSVP_PRO_OPTION_WAITLIST_TEXT; ?>" rows="5" cols="60"><?php echo esc_html($rsvpWaitlistText); ?></textarea>
                  <br />
                  <span class="description"><?php _e("Default is: &quot;This event has a waitlist available.&quot;", "rsvp-pro-plugin"); ?></span>
                </td>
              </tr>
              <tr class="subEventHide">
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_OPTION_ASSOCIATED_MESSAGE; ?>"><?php _e("Associated RSVP message:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <textarea name="<?php echo RSVP_PRO_OPTION_ASSOCIATED_MESSAGE; ?>" id="<?php echo RSVP_PRO_OPTION_ASSOCIATED_MESSAGE; ?>" rows="5" cols="60"><?php echo esc_html($associatedMessage); ?></textarea>
                  <br />
                  <span class="description"><?php _e("Default is: &quot;The following people are associated with you.  At this time you can RSVP for them as well.&quot;", "rsvp-pro-plugin"); ?></span>
                </td>
              </tr>

              <tr class="subEventHide">
                <th scope="row" colspan="2">
                  <h3><?php _e("One Step Per Event", "rsvp-pro-plugin"); ?></h3>
                </th>
              </tr>
              <tr class="subEventHide">
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_OPTION_MULTI_EVENT_TITLE; ?>"><?php _e("Event title text:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="text" name="<?php echo RSVP_PRO_OPTION_MULTI_EVENT_TITLE; ?>" id="<?php echo RSVP_PRO_OPTION_MULTI_EVENT_TITLE;?>" value="<?php echo esc_attr_e($multiEventTitle); ?>" />
                  <br />
                  <span class="description"><?php _e("Default is: &quot;RSVP for&quot;", "rsvp-pro-plugin"); ?></span>
                </td>
              </tr>
              <tr class="subEventHide">
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_OPTION_MULTI_OPTION_TEXT; ?>"><?php _e("&quot;Option&quot; text:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="text" name="<?php echo RSVP_PRO_OPTION_MULTI_OPTION_TEXT; ?>" id="<?php echo RSVP_PRO_OPTION_MULTI_OPTION_TEXT;?>" value="<?php echo esc_attr_e($multiOptionText); ?>" />
                </td>
              </tr>


              <tr class="subEventHide">
                <th scope="row" colspan="2">
                  <h3><?php _e("Confirmation Page", "rsvp-pro-plugin"); ?></h3>
                </th>
              </tr>

              <tr class="subEventHide">
                <th scope="row">
                  <label for="thank_you_text"><?php _e("Custom thank you:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <textarea name="thank_you_text" id="thank_you_text" rows="5" cols="60"><?php echo esc_html($thankYouText); ?></textarea>
                  <br>
                  <span class="description"><?php _e("Default is: &quot;Thank you for RSVPing&quot;", "rsvp-pro-plugin"); ?></span>
                </td>
              </tr>
              <tr class="subEventHide">
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_OPTION_NOT_COMING; ?>"><?php _e("Thank You when an attendee RSVPs with &quot;no&quot;:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <textarea name="<?php echo RSVP_PRO_OPTION_NOT_COMING; ?>" id="<?php echo RSVP_PRO_OPTION_NOT_COMING; ?>" rows="5" cols="60"><?php echo esc_html($notComingVerbiage); ?></textarea>
                </td>
              </tr>

            </tbody>
          </table>

        <?php
          endif;

          if($active_tab == "attendeelist"):
        ?>
          <input type="hidden" name="eventName" value="<?php echo htmlspecialchars($eventName); ?>" />
          <input type="hidden" name="open_date" value="<?php echo htmlspecialchars($opendate); ?>" />
          <input type="hidden" name="close_date" value="<?php echo htmlspecialchars($closedate); ?>" />
          <input type="hidden" name="parentEventID" id="parentEventID" value="<?php echo htmlspecialchars($parentEventID); ?>" />
          <input type="hidden" name="rsvp_num_additional_guests" value="<?php echo htmlspecialchars($numAdditionalGuests); ?>" />
          <input type="hidden" name="require_passcode" value="<?php echo $requirePasscode; ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_PASSWORD_LENGTH; ?>" value="<?php echo $password_length; ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_ONLY_PASSCODE; ?>" value="<?php echo $onlyPasscode; ?>" />
          <input type="hidden" name="hide_additional_guests" value="<?php echo $hideAdditionalGuests; ?>" />
          <input type="hidden" name="notify_user" value="<?php echo $notifyUser; ?>" />
          <input type="hidden" name="notify_email" value="<?php echo htmlspecialchars($notifyEmail); ?>" />
          <input type="hidden" name="rsvp_guest_email_confirmation" value="<?php echo $guestEmailConfirm; ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_OPEN_REGISTRATION; ?>" value="<?php echo $rsvpOpenReg; ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_CANT_EDIT; ?>" value="<?php echo htmlspecialchars($rsvpNoEditing); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_AUTO_LOGIN_ATTENDEE; ?>" value="<?php echo htmlspecialchars($attendeeAutoLogin); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_EVENT_COUNT_LIMIT; ?>" value="<?php echo esc_html($rsvpLimit)?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_ENABLE_WAITLIST; ?>" value="<?php echo esc_html($rsvpEnableWaitlist); ?>" />
          <input type="hidden" name="adminRoles" value="<?php echo esc_attr_e(implode(",", $adminRoles)); ?>" />
          <input type="hidden" name="rsvp_question_text" value="<?php echo htmlspecialchars($rsvpQuestionText); ?>" />
          <input type="hidden" name="rsvp_yes_label" value="<?php echo htmlspecialchars($rsvpYesLabel); ?>" />
          <input type="hidden" name="rsvp_no_label" value="<?php echo htmlspecialchars($rsvpNoLabel); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_WAITLIST_VERBIAGE; ?>" value="<?php echo htmlspecialchars($rsvpWaitlistLabel); ?>" />
          <input type="hidden" name="additional_note_label" value="<?php echo htmlspecialchars($additionalNoteLabel); ?>" />
          <input type="hidden" name="hide_note" value="<?php echo $hideNote; ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_DONT_USE_HASH; ?>" value="<?php echo $dontUseHash; ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_HIDE_EMAIL_FIELD; ?>" value="<?php echo $hideEmailField; ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_EMAIL_REQUIRED; ?>" value="<?php echo $requireEmail; ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_DISABLE_USER_SEARCH; ?>" value="<?php echo $rsvpDisableUserSearch; ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_SHOW_SALUTATION ?>" value="<?php echo esc_html($showSalutation); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_SHOW_SUFFIX; ?>" value="<?php echo esc_html($showSuffix); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_SALUTATIONS; ?>" value="<?php echo esc_html($salutations); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_SHOW_NORESPONSE_FOR_ASSOCIATED; ?>" value="<?php echo esc_html($showNoResponse); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_FRONTEND_WIZARD; ?>" value="<?php echo $frontendWizard; ?>" />
          <input type="hidden" name="event_access" value="<?php echo esc_html($eventAccess); ?>" />
          <input type="hidden" name="attendeeAccess" value="<?php echo implode(",", $attendeeAccess); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_NEW_ATTENDEE_BUTTON_TEXT; ?>" value="<?php echo esc_html($newAttendeeButtonText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_RSVP_BUTTON_TEXT; ?>" value="<?php echo esc_html($rsvpFrontendButtonText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_NEXT_BUTTON_TEXT;?>" value="<?php echo esc_html($nextButtonText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_MAX_COUNT_REACHED_TEXT;?>" value="<?php echo esc_html($rsvpLimitText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_WAITLIST_TEXT; ?>" value="<?php echo esc_html($rsvpWaitlistText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_OPEN_DATE_TEXT; ?>" value="<?php echo esc_html($openDateText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_CLOSE_DATE_TEXT; ?>" value="<?php echo esc_html($closeDateText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_MODIFY_REGISTRATION_TEXT; ?>" value="<?php echo esc_attr_e($modifyRegistrationText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_ADD_ATTENDEE_BUTTON_TEXT; ?>" value="<?php echo esc_attr_e($addAttendeeButtonText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_ATTENDEE_LIST_FILTER; ?>" value="<?php echo esc_html($attendeelistFilter); ?>" />
          <input type="hidden" name="greeting_text" value="<?php echo htmlspecialchars($greetingText); ?>" />
          <input type="hidden" name="welcome_text" value="<?php echo htmlspecialchars($welcomeText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_EMAIL_TEXT; ?>" value="<?php echo htmlspecialchars($rsvpEmailText); ?>" />
          <input type="hidden" name="thank_you_text" value="<?php echo htmlspecialchars($thankYouText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_NOT_COMING; ?>" value="<?php echo esc_html($notComingVerbiage); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_ADD_ADDITIONAL_VERBIAGE; ?>" value="<?php echo htmlspecialchars($addAdditionalText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_RSVP_REQUIRED; ?>" value="<?php echo esc_html($requireRsvpValues); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_EMAIL_FROM; ?>" value="<?php echo esc_attr_e($emailFrom); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_EMAIL_CC_ASSOCIATED; ?>" value="<?php echo esc_attr_e($emailCCAssociated); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_EMAIL_BCC_ADDRESS; ?>" value="<?php echo esc_attr_e($emailBCCAddresses); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_EMAIL_SUBJECT; ?>" value="<?php echo esc_attr_e($emailSubject); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_FIRST_NAME_LABEL; ?>" value="<?php echo esc_attr_e($firstNameLabel); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_LAST_NAME_LABEL; ?>" value="<?php echo esc_attr_e($lastNameLabel); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_PASSCODE_LABEL; ?>" value="<?php echo esc_attr_e($passcodeLabel); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_COMPLETE_BUTTON_TEXT; ?>" value="<?php echo esc_attr_e($completeButtonText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_EDIT_PROMPT_TEXT; ?>" value="<?php echo esc_attr_e($editPromptText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_SUFFIX_LABEL; ?>" value="<?php echo esc_attr_e($suffixLabel); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_SALUTATION_LABEL; ?>" value="<?php echo esc_attr_e($salutationLabel); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_EMAIL_LABEL; ?>" value="<?php echo esc_attr_e($emailLabel); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_ASSOCIATED_MESSAGE; ?>" value="<?php echo esc_attr_e($associatedMessage); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_ASSOCIATED_ATTENDEE_GREETING; ?>" value="<?php echo esc_attr_e($associatedGreeting); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_MULTI_EVENT_TITLE; ?>" value="<?php echo esc_attr_e($multiEventTitle); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_MULTI_OPTION_TEXT; ?>" value="<?php echo esc_attr_e($multiOptionText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_REMOVE_ATTENDEE_BUTTON_TEXT; ?>" value="<?php echo esc_attr_e($removeAttendeeButton); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_YES_TEXT; ?>" value="<?php echo esc_attr_e($yesText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_NO_TEXT; ?>" value="<?php echo esc_attr_e($noText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_NO_RESPONSE_TEXT; ?>" value="<?php echo esc_attr_e($noResponseText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_FUZZY_MATCH_TEXT; ?>" value="<?php echo esc_attr_e($fuzzyMatchText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_MULTIPLE_MATCHES_TEXT; ?>" value="<?php echo esc_attr_e($multipleMatchesText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_WELCOME_BACK_TEXT; ?>" value="<?php echo esc_attr_e($welcomeBackText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_UNABLE_TO_FIND_TEXT; ?>" value="<?php echo esc_attr_e($unableFindText); ?>" />
          <table class="form-table">
            <tbody>
              <tr>
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_OPTION_ATTENDEE_LIST_FILTER; ?>"><?php _e("Only show a specific rsvp status on public attendee list:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <select name="<?php echo RSVP_PRO_OPTION_ATTENDEE_LIST_FILTER; ?>" id="<?php echo RSVP_PRO_OPTION_ATTENDEE_LIST_FILTER; ?>">
                    <option value="">--</option>
                    <option value="yes" <?php echo (($attendeelistFilter == "yes") ? "selected=\"selected\"": ""); ?>><?php _e("Yes", "rsvp-pro-plugin"); ?></option>
                    <option value="no" <?php echo (($attendeelistFilter == "no") ? "selected=\"selected\"": ""); ?>><?php _e("No", "rsvp-pro-plugin"); ?></option>
                    <option value="noresponse" <?php echo (($attendeelistFilter == "noresponse") ? "selected=\"selected\"": ""); ?>><?php _e("No Response", "rsvp-pro-plugin"); ?></option>
                    <option value="waitlist" <?php echo (($attendeelistFilter == "waitlist") ? "selected=\"selected\"": ""); ?>><?php _e("Waitlist", "rsvp-pro-plugin"); ?></option>
                  </select>
                </td>
              </tr>
              <tr>
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_OPTION_ATTENDEE_LIST_SORT_ORDER; ?>"><?php _e("Attendee list sort order:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <select name="<?php echo RSVP_PRO_OPTION_ATTENDEE_LIST_SORT_ORDER; ?>" id="<?php echo RSVP_PRO_OPTION_ATTENDEE_LIST_SORT_ORDER; ?>">
                    <option value="">--</option>
                    <option value="firstName" <?php echo (($attendeelistSortOrder == "firstName") ? "selected=\"selected\"": ""); ?>><?php _e("First Name", "rsvp-pro-plugin"); ?></option>
                    <option value="lastName" <?php echo (($attendeelistSortOrder == "lastName") ? "selected=\"selected\"": ""); ?>><?php _e("Last Name", "rsvp-pro-plugin"); ?></option>
                    <option value="rsvpStatus" <?php echo (($attendeelistSortOrder == "rsvpStatus") ? "selected=\"selected\"": ""); ?>><?php _e("RSVP Status", "rsvp-pro-plugin"); ?></option>
                  </select>
                  <br />
                  <span class="description"><?php _e("Default sort order is: First Name", "rsvp-pro-plugin"); ?></span>
                </td>
              </tr>
              <tr>
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_OPTION_ATTENDEE_LIST_HIDE_STATUS; ?>"><?php _e("Hide RSVP status from list:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="checkbox" name="<?php echo RSVP_PRO_OPTION_ATTENDEE_LIST_HIDE_STATUS; ?>" id="<?php echo RSVP_PRO_OPTION_ATTENDEE_LIST_HIDE_STATUS; ?>" value="Y" <?php 
                    if($attendeeListHideStatus == "Y") {
                      echo " checked=\"checked\"";
                    }
                  ?> />
                </td>
              </tr>
              <tr>
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_OPTION_ATTENDEE_LIST_CUSTOM_QUESTIONS; ?>"><?php _e("Show custom questions in list:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <select name="<?php echo RSVP_PRO_OPTION_ATTENDEE_LIST_CUSTOM_QUESTIONS; ?>[]" id="<?php echo RSVP_PRO_OPTION_ATTENDEE_LIST_CUSTOM_QUESTIONS; ?>" multiple="multiple" size="5">
                    <?php
                      $questions = $wpdb->get_results($wpdb->prepare("SELECT id, question 
                                                                      FROM ".PRO_QUESTIONS_TABLE." 
                                                                      WHERE rsvpEventID = %d ORDER BY question", ($parentEventID > 0) ? $parentEventID : $eventID));
                      foreach($questions as $q) {
                    ?>
                          <option value="<?php echo $q->id; ?>" 
                                  <?php echo ((in_array($q->id, $attendeeListCustomQs)) ? "selected=\"selected\"" : ""); ?>><?php echo esc_html(stripslashes($q->question)); ?></option>
                    <?php
                      }
                    ?>
                  </select>
                </td>
              </tr>
            </tbody>
          </table>
        <?php
          endif; 

          if($active_tab == "email"): 
        ?>
          <input type="hidden" name="eventName" value="<?php echo htmlspecialchars($eventName); ?>" />
          <input type="hidden" name="open_date" value="<?php echo htmlspecialchars($opendate); ?>" />
          <input type="hidden" name="close_date" value="<?php echo htmlspecialchars($closedate); ?>" />
          <input type="hidden" name="parentEventID" id="parentEventID" value="<?php echo htmlspecialchars($parentEventID); ?>" />
          <input type="hidden" name="rsvp_num_additional_guests" value="<?php echo htmlspecialchars($numAdditionalGuests); ?>" />
          <input type="hidden" name="require_passcode" value="<?php echo $requirePasscode; ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_PASSWORD_LENGTH; ?>" value="<?php echo $password_length; ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_ONLY_PASSCODE; ?>" value="<?php echo $onlyPasscode; ?>" />
          <input type="hidden" name="hide_additional_guests" value="<?php echo $hideAdditionalGuests; ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_OPEN_REGISTRATION; ?>" value="<?php echo $rsvpOpenReg; ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_CANT_EDIT; ?>" value="<?php echo htmlspecialchars($rsvpNoEditing); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_AUTO_LOGIN_ATTENDEE; ?>" value="<?php echo htmlspecialchars($attendeeAutoLogin); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_EVENT_COUNT_LIMIT; ?>" value="<?php echo esc_html($rsvpLimit)?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_ENABLE_WAITLIST; ?>" value="<?php echo esc_html($rsvpEnableWaitlist); ?>" />
          <input type="hidden" name="adminRoles" value="<?php echo esc_attr_e(implode(",", $adminRoles)); ?>" />
          <input type="hidden" name="rsvp_question_text" value="<?php echo htmlspecialchars($rsvpQuestionText); ?>" />
          <input type="hidden" name="rsvp_yes_label" value="<?php echo htmlspecialchars($rsvpYesLabel); ?>" />
          <input type="hidden" name="rsvp_no_label" value="<?php echo htmlspecialchars($rsvpNoLabel); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_WAITLIST_VERBIAGE; ?>" value="<?php echo htmlspecialchars($rsvpWaitlistLabel); ?>" />
          <input type="hidden" name="additional_note_label" value="<?php echo htmlspecialchars($additionalNoteLabel); ?>" />
          <input type="hidden" name="hide_note" value="<?php echo $hideNote; ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_DONT_USE_HASH; ?>" value="<?php echo $dontUseHash; ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_HIDE_EMAIL_FIELD; ?>" value="<?php echo $hideEmailField; ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_EMAIL_REQUIRED; ?>" value="<?php echo $requireEmail; ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_DISABLE_USER_SEARCH; ?>" value="<?php echo $rsvpDisableUserSearch; ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_SHOW_SALUTATION ?>" value="<?php echo esc_html($showSalutation); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_SHOW_SUFFIX; ?>" value="<?php echo esc_html($showSuffix); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_SALUTATIONS; ?>" value="<?php echo esc_html($salutations); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_SHOW_NORESPONSE_FOR_ASSOCIATED; ?>" value="<?php echo esc_html($showNoResponse); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_FRONTEND_WIZARD; ?>" value="<?php echo $frontendWizard; ?>" />
          <input type="hidden" name="event_access" value="<?php echo esc_html($eventAccess); ?>" />
          <input type="hidden" name="attendeeAccess" value="<?php echo implode(",", $attendeeAccess); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_NEW_ATTENDEE_BUTTON_TEXT; ?>" value="<?php echo esc_html($newAttendeeButtonText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_RSVP_BUTTON_TEXT; ?>" value="<?php echo esc_html($rsvpFrontendButtonText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_NEXT_BUTTON_TEXT;?>" value="<?php echo esc_html($nextButtonText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_MAX_COUNT_REACHED_TEXT;?>" value="<?php echo esc_html($rsvpLimitText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_WAITLIST_TEXT; ?>" value="<?php echo esc_html($rsvpWaitlistText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_OPEN_DATE_TEXT; ?>" value="<?php echo esc_html($openDateText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_CLOSE_DATE_TEXT; ?>" value="<?php echo esc_html($closeDateText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_MODIFY_REGISTRATION_TEXT; ?>" value="<?php echo esc_attr_e($modifyRegistrationText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_ADD_ATTENDEE_BUTTON_TEXT; ?>" value="<?php echo esc_attr_e($addAttendeeButtonText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_ATTENDEE_LIST_FILTER; ?>" value="<?php echo esc_html($attendeelistFilter); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_ATTENDEE_LIST_HIDE_STATUS; ?>" value="<?php echo esc_attr_e($attendeeListHideStatus); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_ATTENDEE_LIST_CUSTOM_QUESTIONS; ?>" value="<?php echo esc_attr_e(implode(",", $attendeeListCustomQs)); ?>" />
          <input type="hidden" name="greeting_text" value="<?php echo htmlspecialchars($greetingText); ?>" />
          <input type="hidden" name="welcome_text" value="<?php echo htmlspecialchars($welcomeText); ?>" />
          <input type="hidden" name="thank_you_text" value="<?php echo htmlspecialchars($thankYouText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_NOT_COMING; ?>" value="<?php echo esc_html($notComingVerbiage); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_ADD_ADDITIONAL_VERBIAGE; ?>" value="<?php echo htmlspecialchars($addAdditionalText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_RSVP_REQUIRED; ?>" value="<?php echo esc_html($requireRsvpValues); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_ATTENDEE_LIST_FILTER; ?>" value="<?php echo esc_html($attendeelistFilter); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_ATTENDEE_LIST_SORT_ORDER; ?>" value="<?php echo esc_html($attendeelistSortOrder); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_FIRST_NAME_LABEL; ?>" value="<?php echo esc_attr_e($firstNameLabel); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_LAST_NAME_LABEL; ?>" value="<?php echo esc_attr_e($lastNameLabel); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_PASSCODE_LABEL; ?>" value="<?php echo esc_attr_e($passcodeLabel); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_COMPLETE_BUTTON_TEXT; ?>" value="<?php echo esc_attr_e($completeButtonText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_EDIT_PROMPT_TEXT; ?>" value="<?php echo esc_attr_e($editPromptText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_SUFFIX_LABEL; ?>" value="<?php echo esc_attr_e($suffixLabel); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_SALUTATION_LABEL; ?>" value="<?php echo esc_attr_e($salutationLabel); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_EMAIL_LABEL; ?>" value="<?php echo esc_attr_e($emailLabel); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_ASSOCIATED_MESSAGE; ?>" value="<?php echo esc_attr_e($associatedMessage); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_ASSOCIATED_ATTENDEE_GREETING; ?>" value="<?php echo esc_attr_e($associatedGreeting); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_MULTI_EVENT_TITLE; ?>" value="<?php echo esc_attr_e($multiEventTitle); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_MULTI_OPTION_TEXT; ?>" value="<?php echo esc_attr_e($multiOptionText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_REMOVE_ATTENDEE_BUTTON_TEXT; ?>" value="<?php echo esc_attr_e($removeAttendeeButton); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_YES_TEXT; ?>" value="<?php echo esc_attr_e($yesText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_NO_TEXT; ?>" value="<?php echo esc_attr_e($noText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_NO_RESPONSE_TEXT; ?>" value="<?php echo esc_attr_e($noResponseText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_FUZZY_MATCH_TEXT; ?>" value="<?php echo esc_attr_e($fuzzyMatchText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_MULTIPLE_MATCHES_TEXT; ?>" value="<?php echo esc_attr_e($multipleMatchesText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_WELCOME_BACK_TEXT; ?>" value="<?php echo esc_attr_e($welcomeBackText); ?>" />
          <input type="hidden" name="<?php echo RSVP_PRO_OPTION_UNABLE_TO_FIND_TEXT; ?>" value="<?php echo esc_attr_e($unableFindText); ?>" />
          <table class="form-table">
            <tbody>
              <tr>
                <th scope="row">
                  <label for="notify_user"><?php _e("Notify When Guest RSVPs:", "rsvp-pro-plugin"); ?></label> 
                </th>
                <td>
                  <input type="checkbox" name="notify_user" id="notify_user" value="Y" <?php echo (($notifyUser == "Y") ? " checked=\"checked\"" : ""); ?> />
                </td>
              </tr>
              <tr>
                <th scope="row">
                  <label for="notify_email"><?php _e("Admin notification email:", "rsvp-pro-plugin"); ?></label> 
                </th>
                <td>
                  <input type="text" name="notify_email" id="notify_email" value="<?php echo esc_attr_e($notifyEmail); ?>" class="regular-text" />
                </td>
              </tr>
              <tr>
                <th scope="row">
                  <label for="rsvp_guest_email_confirmation"><?php _e("Email guests when RSVP is completed:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="checkbox" name="rsvp_guest_email_confirmation" id="rsvp_guest_email_confirmation" value="Y" 
                  <?php echo (($guestEmailConfirm == "Y") ? " checked=\"checked\"" : ""); ?> />
                </td>
              </tr>
              <tr class="subEventHide">
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_OPTION_EMAIL_TEXT; ?>"><?php _e("Email Text: <br />Sent to guests in confirmation, at top of email", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <textarea name="<?php echo RSVP_PRO_OPTION_EMAIL_TEXT; ?>" id="<?php echo RSVP_PRO_OPTION_EMAIL_TEXT; ?>" rows="5" cols="60"><?php echo esc_html($rsvpEmailText); ?></textarea>
                </td>
              </tr>
              <tr>
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_OPTION_EMAIL_FROM; ?>"><?php _e("Email address notifications could come from?", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="text" name="<?php echo RSVP_PRO_OPTION_EMAIL_FROM; ?>" id="<?php echo RSVP_PRO_OPTION_EMAIL_FROM; ?>" value="<?php esc_attr_e($emailFrom); ?>"  class="regular-text" />
                  <br />
                  <span class="description"><?php _e("Note: depending on your web host they might block emails with this setting turned on.<br />
                   Examples of expected data: test@test.com or &quot;Test Bob&quot; &lt;test@test.com&gt;", "rsvp-pro-plugin"); ?></span>
                </td>
              </tr>
              <tr>
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_OPTION_EMAIL_CC_ASSOCIATED; ?>"><?php _e("CC associated attendees", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="checkbox" name="<?php echo RSVP_PRO_OPTION_EMAIL_CC_ASSOCIATED; ?>" id="<?php echo RSVP_PRO_OPTION_EMAIL_CC_ASSOCIATED; ?>" value="Y" 
                    <?php echo (($emailCCAssociated == "Y") ? " checked=\"checked\"": ""); ?> />
                </td>
              </tr>
              <tr>
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_OPTION_EMAIL_BCC_ADDRESS; ?>"><?php _e("Email addresses to BCC when attendee RSVPs:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="text" name="<?php echo RSVP_PRO_OPTION_EMAIL_BCC_ADDRESS; ?>" id="<?php echo RSVP_PRO_OPTION_EMAIL_BCC_ADDRESS; ?>" value="<?php echo esc_attr_e($emailBCCAddresses); ?>"  class="regular-text" />
                  <br />
                  <span class="description"><?php _e("Separate each email address with a semicolon (;)", "rsvp-pro-plugin"); ?>
                </td>
              </tr>
              <tr>
                <th scope="row">
                  <label for="<?php echo RSVP_PRO_OPTION_EMAIL_SUBJECT; ?>"><?php _e("Email subject for the attendee email:", "rsvp-pro-plugin"); ?></label>
                </th>
                <td>
                  <input type="text" name="<?php echo RSVP_PRO_OPTION_EMAIL_SUBJECT; ?>" id="<?php echo RSVP_PRO_OPTION_EMAIL_SUBJECT; ?>" value="<?php echo esc_attr_e($emailSubject); ?>" class="regular-text" />
                  <br />
                  <span class="description"><?php _e("Default is: &quot;RSVP Confirmation&quot;", "rsvp-pro-plugin"); ?></span>
                </td>
              </tr>
            </tbody>
          </table>
        <?php
          endif;
        ?>
        <p class="submit"><input type="submit" value="<?php echo $buttonText; ?>" class="button-primary" /></p>
      </form>
      <script type="text/javascript">
        handleParentEventChange();
      </script>
      </div>
    </div>