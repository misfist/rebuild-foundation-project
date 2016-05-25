<?php
/**
 * Include that handles all of the RSVP'ing for a given set of attendees
 *
 * @since 2.1.3
 */

function rsvp_pro_handlersvp(&$output, &$text) {
	global $wpdb;
  	global $rsvpId;
  	$thankYouPrimary = "";
  	$thankYouAssociated = array();
  	$mainRsvpStatus = "";
  	$isNewAttendee = rsvp_pro_is_rsvp_new_attendee();

  	foreach($_POST as $key=>$val) {
    	$rsvp_saved_form_vars[$key] = $val;
  	}

  	if($isNewAttendee) {
  		if(rsvp_pro_frontend_max_limit_for_all_events()) {
  			return rsvp_pro_handle_max_limit_reached_message($rsvpId);
  		}	
  
		if(empty($_POST['attendeeFirstName']) || 
			((rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_LAST_NAME_NOT_REQUIRED) != "Y") && empty($_POST['attendeeLastName']))) {
				return rsvp_pro_handlenewattendee($output, $text);
		}

		// Insert in the record here and then proceed on like an existing attendee
		$wpdb->insert(PRO_ATTENDEES_TABLE, array("rsvpDate" => date("Y-m-d"), 
                                       "firstName" => $_POST['attendeeFirstName'], 
                                       "lastName"  => $_POST['attendeeLastName'], 
                                       "rsvpEventID" => $rsvpId), 
				array("%s", "%s", "%s", "%d"));
			
  		$attendeeID = $wpdb->insert_id;
  	} else {
  		$attendeeID = $_POST['attendeeID'];
  	}


	if($attendeeID > 0) {

		$attendeeSql = "SELECT * FROM ".PRO_ATTENDEES_TABLE." WHERE id = %d AND rsvpEventID = %d";
    	
    	$attendee = $wpdb->get_row($wpdb->prepare($attendeeSql, $attendeeID, $rsvpId));
    	// Get Attendee first name
    	$thankYouPrimary = $wpdb->get_var($wpdb->prepare("SELECT firstName FROM ".PRO_ATTENDEES_TABLE." WHERE id = %d AND rsvpEventID = %d", $attendeeID, $rsvpId));
    
	    if(isset($_POST['mainRsvp'])) {    
	  		// update their information and what not....
	  		if(strToUpper($_POST['mainRsvp']) == "Y") {
	  			$rsvpStatus = "Yes";
	  		} else if(strToUpper($_POST['mainRsvp']) == "W") {
	  			$rsvpStatus = "Waitlist";
	  		} else {
	  			$rsvpStatus = "No";
	  		}

	  		if(rsvp_pro_frontend_max_limit_hit($rsvpId)) {
	  			if(($attendee->rsvpStatus != "Yes") && ($rsvpStatus == "Yes")) {
	  				$rsvpStatus = "No";
	  			}
	  		}
	      	$mainRsvpStatus = $rsvpStatus;
	    
	  	  	$wpdb->update(PRO_ATTENDEES_TABLE, array("rsvpDate" => date("Y-m-d"), 
	  					"rsvpStatus" => $rsvpStatus, 
	  					"note" => $_POST['rsvp_note']), 
						array("id" => $attendeeID, "rsvpEventID" => $rsvpId), 
						array("%s", "%s", "%s"), 
						array("%d", "%d"));
	    }
    
	    if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_HIDE_EMAIL_FIELD) != "Y") {
			  $wpdb->update(PRO_ATTENDEES_TABLE, array("note" => $_POST['rsvp_note'],
	            "email" => $_POST['mainEmail']), 
							array("id" => $attendeeID, "rsvpEventID" => $rsvpId), 
							array("%s", "%s"), 
							array("%d", "%d"));
	    }
	    rsvp_pro_add_passcode_if_needed($attendee, $attendeeID);

    	// Refresh $attendee record because a lot could have changed
    	$attendee = $wpdb->get_row($wpdb->prepare($attendeeSql, $attendeeID, $rsvpId));

    	rsvp_pro_handleSuffixAndSalutation($attendeeID, "main"); 
    	rsvp_pro_handleSubEvents($attendeeID, "main");
		rsvp_pro_handleAdditionalQuestions($attendeeID, "mainquestion");
																				
		$sql = "SELECT id, firstName, rsvpStatus FROM ".PRO_ATTENDEES_TABLE." 
		 	WHERE (id IN (SELECT attendeeID FROM ".PRO_ASSOCIATED_ATTENDEES_TABLE." WHERE associatedAttendeeID = %d) 
				OR id in (SELECT associatedAttendeeID FROM ".PRO_ASSOCIATED_ATTENDEES_TABLE." WHERE attendeeID = %d)) AND 
        rsvpEventID = %d";
		$associations = $wpdb->get_results($wpdb->prepare($sql, $attendeeID, $attendeeID, $rsvpId));
		foreach($associations as $a) {
	      	if(isset($_POST['attending'.$a->id]) && 
	      		(($_POST['attending'.$a->id] == "Y") || ($_POST['attending'.$a->id] == "N") || ($_POST['attending'.$a->id] == "NoResponse"))) {
	        	$thankYouAssociated[] = $a->firstName;

				if($_POST['attending'.$a->id] == "Y") {
					$rsvpStatus = "Yes";
				} else if($_POST['attending'.$a->id] == "W") {
					$rsvpStatus = "Waitlist";
		        } else if($_POST['attending'.$a->id] == "NoResponse") {
		          $rsvpStatus = "NoResponse";
				} else {
					$rsvpStatus = "No";
				}

				if(rsvp_pro_frontend_max_limit_hit($rsvpId)) {
		  			if(($attendee->rsvpStatus != "Yes") && ($rsvpStatus == "Yes")) {
		  				$rsvpStatus = "No";
		  			}
		  		}
		        $wpdb->update(PRO_ATTENDEES_TABLE, array("rsvpDate" => date("Y-m-d"), 
								"rsvpStatus" => $rsvpStatus),
								array("id" => $a->id, "rsvpEventID" => $rsvpId), 
								array("%s", "%s"), 
								array("%d", "%d"));
		  	}
      
			if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_HIDE_EMAIL_FIELD) != "Y") {
				$wpdb->update(PRO_ATTENDEES_TABLE, array("email" => $_POST['attending'.$a->id."Email"]),
								array("id" => $a->id, "rsvpEventID" => $rsvpId), 
								array("%s"), 
								array("%d", "%d"));
			}
      
      		rsvp_pro_handleSuffixAndSalutation($a->id, "attending".$a->id);
			rsvp_pro_handleAdditionalQuestions($a->id, $a->id."question");
      		rsvp_pro_handleSubEvents($a->id, $a->id."Existing");
		} // foreach($associations as $a) {
					
		if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_HIDE_ADDITIONAL) != "Y") {
      		$numGuests = get_number_additional($rsvpId, $attendee);
			if(is_numeric($_POST['additionalRsvp']) && ($_POST['additionalRsvp'] > 0)) {
				for($i = 1; $i <= $_POST['additionalRsvp']; $i++) {
          
					if(($i <= $numGuests) && 
					   !empty($_POST['newAttending'.$i.'FirstName']) && 
					   !empty($_POST['newAttending'.$i.'LastName'])) {		
               
       					if($_POST['newAttending'.$i] == "Y") {
       						$rsvpStatus = "Yes";
       					} else if($_POST['newAttending'.$i] == "W") {
       						$rsvpStatus = "Waitlist";
               			} else if($_POST['newAttending'.$i] == "NoResponse") {
                 			$rsvpStatus = "NoResponse";
       					} else {
       						$rsvpStatus = "No";
       					}

       					if(rsvp_pro_frontend_max_limit_hit($rsvpId)) {
			  				if($rsvpStatus == "Yes") {
			  					$rsvpStatus = "No";
			  				}
			  			}
            			$thankYouAssociated[] = $_POST['newAttending'.$i.'FirstName'];
						$wpdb->insert(PRO_ATTENDEES_TABLE, array("firstName" => trim($_POST['newAttending'.$i.'FirstName']), 
										"lastName" => trim($_POST['newAttending'.$i.'LastName']), 
                    					"email" => trim($_POST['newAttending'.$i.'Email']), 
										"rsvpDate" => date("Y-m-d"), 
										"rsvpStatus" => $rsvpStatus, 
										"additionalAttendee" => "Y", 
                    					"rsvpEventID" => $rsvpId), 
										array('%s', '%s', '%s', '%s', '%s', '%s', '%d'));
						$newAid = $wpdb->insert_id;

						$newAttendee = $wpdb->get_row($wpdb->prepare($attendeeSql, $newAid, $rsvpId));
						rsvp_pro_add_passcode_if_needed($newAttendee, $newAid);
            			rsvp_pro_handleSuffixAndSalutation($newAid, "newAttending".$i);
						rsvp_pro_handleAdditionalQuestions($newAid, $i.'question');
            			rsvp_pro_handleSubEvents($newAid, $i);
            			rsvp_pro_copy_event_permissions($newAid, $attendeeID);
            
						// Add associations for this new user
						$wpdb->insert(PRO_ASSOCIATED_ATTENDEES_TABLE, array("attendeeID" => $newAid, 
											"associatedAttendeeID" => $attendeeID), 
											array("%d", "%d"));
						$wpdb->query($wpdb->prepare("INSERT INTO ".PRO_ASSOCIATED_ATTENDEES_TABLE."(attendeeID, associatedAttendeeID)
																				 SELECT ".$newAid.", associatedAttendeeID 
																				 FROM ".PRO_ASSOCIATED_ATTENDEES_TABLE." 
																				 WHERE attendeeID = %d", $attendeeID));
					}
				}
			}
		} // if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_HIDE_ADDITIONAL) != "Y") {
		
    	rsvp_pro_handleGroupQuestions($attendeeID);
    	rsvp_pro_auto_rsvp_attendees($rsvpId);
    	rsvp_pro_frontend_handle_email_notifications($attendeeID, $rsvpId);    
    
    	if($isNewAttendee) {
    		return rsvp_pro_handle_output($text, 
    									  rsvp_pro_frontend_new_attendee_thankyou($thankYouPrimary, 
    									  										  $thankYouAssociated, 
    									  										  $mainRsvpStatus, 
    									  										  $attendee->passcode));
    	} else {
    		return rsvp_pro_handle_output($text, rsvp_pro_frontend_thankyou($thankYouPrimary, 
																		$thankYouAssociated, 
																		$mainRsvpStatus, 
																		$attendeeID));
    	}
	} else {
		return rsvp_pro_handle_output($text, rsvp_pro_frontend_greeting());
	} // if($attendeeID > 0) {
}

/**
 * Checks to see whether we are handling a new attendee or not. 
 * 
 * @return boolean | true if it is a new attendee, false otherwise
 */
function rsvp_pro_is_rsvp_new_attendee() {
	
	if(is_numeric($_POST['attendeeID']) && ($_POST['attendeeID'] > 0)) {
		return false;
	}

	return true;
}

function rsvp_pro_add_passcode_if_needed($attendee, $attendeeID) {
	global $rsvpId, $wpdb; 

	if(rsvp_pro_require_passcode($rsvpId) && empty($attendee->passcode)) {
    	$length = 6;
    
    	if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_PASSWORD_LENGTH) > 0) {
      		$length = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_PASSWORD_LENGTH);
    	}
    	$rsvpPassword = trim(rsvp_pro_generate_passcode($length));
		$wpdb->update(PRO_ATTENDEES_TABLE, 
						array("passcode" => $rsvpPassword), 
						array("id"=>$attendeeID), 
						array("%s"), 
						array("%d"));
	}
}

function rsvp_pro_handleSuffixAndSalutation($attendeeId, $formPrefix) {
  global $rsvpId;
  global $wpdb;
  
  if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_SHOW_SALUTATION) == "Y") {
		$wpdb->update(PRO_ATTENDEES_TABLE, 
									array("salutation" => trim($_POST[$formPrefix.'Salutation'])),
									array("id" => $attendeeId), 
									array("%s"), 
									array("%d"));
  }
  
  if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_SHOW_SUFFIX) == "Y") {
		$wpdb->update(PRO_ATTENDEES_TABLE, 
									array("suffix" => trim($_POST[$formPrefix.'Suffix'])),
									array("id" => $attendeeId), 
									array("%s"), 
									array("%d"));
  }
}

function rsvp_pro_handleSubEvents($attendeeID, $formName) {
  global $wpdb;
  global $rsvpId;
  
  $sql = "SELECT e.id, se.id AS attendeeRecId, se.rsvpStatus 
                FROM ".PRO_EVENT_TABLE." e 
                LEFT JOIN ".PRO_ATTENDEE_SUB_EVENTS_TABLE." se ON se.rsvpEventID = e.id AND se.rsvpAttendeeID = %d 
                WHERE e.parentEventID = %d";
  $subevents = $wpdb->get_results($wpdb->prepare($sql, $attendeeID, $rsvpId));
  foreach($subevents as $se) {
    if(isset($_POST[$formName."RsvpSub".$se->id])) {
      $rsvpStatus = (strToUpper($_POST[$formName."RsvpSub".$se->id]) == "Y") ? "Yes" : "No";
      if(strToUpper($_POST[$formName."RsvpSub".$se->id]) == "W") {
      	$rsvpStatus = "Waitlist";
      }
      
	  // Handle the case when the limit is hit but people want to RSVP as yes, don't allow it...
  	  if(rsvp_pro_frontend_max_limit_hit($se->id)) {
  	  	if(($se->rsvpStatus != "Yes") && ($rsvpStatus == "Yes")) {
  	  		$rsvpStatus = "No";
  	  	}
  	  }

      if($se->attendeeRecId > 0) {
  		  $wpdb->update(PRO_ATTENDEE_SUB_EVENTS_TABLE, 
          array("rsvpStatus" => $rsvpStatus, 
          		"rsvpDate" => date("Y-m-d")), 
    			array("id" => $se->attendeeRecId, "rsvpEventID" => $se->id), 
    			array("%s", "%s"), 
    			array("%d", "%d"));
      } else {
				$wpdb->insert(PRO_ATTENDEE_SUB_EVENTS_TABLE, array("rsvpAttendeeID" => $attendeeID, 
                           "rsvpStatus" => $rsvpStatus, 
                           "rsvpEventID" => $se->id, 
                           "rsvpDate" => date("Y-m-d")), 
							array('%d', '%s', '%d', '%s'));
      }
    }
  }
}

function rsvp_pro_handleAdditionalQuestions($attendeeID, $formName, $isAdmin = false) {
	global $wpdb;
  global $rsvpId;
	
  if($isAdmin) {
  	$wpdb->query($wpdb->prepare("DELETE FROM ".PRO_ATTENDEE_ANSWERS." WHERE attendeeID = %d AND 
        questionID IN (SELECT q.id FROM ".PRO_QUESTIONS_TABLE." q 
                       INNER JOIN ".PRO_EVENT_TABLE." e ON e.id = q.rsvpEventID 
                       WHERE (e.id = $rsvpId OR e.parentEventID = $rsvpId) AND 
                         (IFNULL(e.event_access, '".RSVP_PRO_PRIVATE_EVENT_ACCESS."') != '".RSVP_PRO_PRIVATE_EVENT_ACCESS."' OR (%d IN (SELECT rsvpAttendeeID FROM ".PRO_EVENT_ATTENDEE_TABLE." WHERE rsvpEventID = e.id)))) ", $attendeeID, $attendeeID));
  } else {
  	$wpdb->query($wpdb->prepare("DELETE FROM ".PRO_ATTENDEE_ANSWERS." WHERE attendeeID = %d AND 
        questionID IN (SELECT q.id FROM ".PRO_QUESTIONS_TABLE." q 
                       INNER JOIN ".PRO_EVENT_TABLE." e ON e.id = q.rsvpEventID 
                       INNER JOIN ".PRO_QUESTION_TYPE_TABLE." qt ON qt.id = q.questionTypeID 
                       WHERE (e.id = $rsvpId OR e.parentEventID = $rsvpId) AND 
                         qt.questionType NOT IN ('hidden', 'readonly') AND 
                         (IFNULL(e.event_access, '".RSVP_PRO_PRIVATE_EVENT_ACCESS."') != '".RSVP_PRO_PRIVATE_EVENT_ACCESS."' 
                         OR (%d IN (SELECT rsvpAttendeeID FROM ".PRO_EVENT_ATTENDEE_TABLE." WHERE rsvpEventID = e.id)))) ", $attendeeID, $attendeeID));
  }       
	$qRs = $wpdb->get_results($wpdb->prepare("SELECT q.id, questionType, q.sortOrder FROM ".PRO_QUESTIONS_TABLE." q 
					INNER JOIN ".PRO_QUESTION_TYPE_TABLE." qt ON qt.id = q.questionTypeID 
          WHERE q.rsvpEventID = %d AND grouping <> '".RSVP_PRO_QG_MULTI."' 
          UNION 
          SELECT q.id, questionType, q.sortOrder FROM ".PRO_QUESTIONS_TABLE." q 
          INNER JOIN ".PRO_QUESTION_TYPE_TABLE." qt ON qt.id = q.questionTypeID 
          INNER JOIN ".PRO_EVENT_TABLE." e ON e.id = q.rsvpEventID 
          WHERE e.parentEventID = %d AND grouping <> '".RSVP_PRO_QG_MULTI."' 
					ORDER BY sortOrder", $rsvpId, $rsvpId));
          
	if(count($qRs) > 0) {
		foreach($qRs as $q) {
			if(isset($_POST[$formName.$q->id]) && !empty($_POST[$formName.$q->id])) {
				if($q->questionType == QT_MULTI) {
					$selectedAnswers = "";
					$aRs = $wpdb->get_results($wpdb->prepare("SELECT id, answer FROM ".PRO_QUESTION_ANSWERS_TABLE." WHERE questionID = %d", $q->id));
					if((count($aRs) > 0) && is_array($_POST[$formName.$q->id])) {
						foreach($aRs as $a) {
							if(in_array($a->id, $_POST[$formName.$q->id])) {
								$selectedAnswers .= ((strlen($selectedAnswers) == "0") ? "" : "||").stripslashes($a->answer);
							}
						}
					}
					
					if(!empty($selectedAnswers)) {
						$wpdb->insert(PRO_ATTENDEE_ANSWERS, array("attendeeID" => $attendeeID, 
																									 "answer" => stripslashes($selectedAnswers), 
																									 "questionID" => $q->id), 
																						 array('%d', '%s', '%d'));
					}
				} else if (($q->questionType == QT_DROP) || ($q->questionType == QT_RADIO)) {
					$aRs = $wpdb->get_results($wpdb->prepare("SELECT id, answer FROM ".PRO_QUESTION_ANSWERS_TABLE." WHERE questionID = %d", $q->id));
					if(count($aRs) > 0) {
						foreach($aRs as $a) {
							if($a->id == $_POST[$formName.$q->id]) {
								$wpdb->insert(PRO_ATTENDEE_ANSWERS, array("attendeeID" => $attendeeID, 
																											 "answer" => stripslashes($a->answer), 
																											 "questionID" => $q->id), 
																								 array('%d', '%s', '%d'));
								break;
							}
						}
					}
				} else {
					$wpdb->insert(PRO_ATTENDEE_ANSWERS, array("attendeeID" => $attendeeID, 
																								 "answer" => $_POST[$formName.$q->id], 
																								 "questionID" => $q->id), 
																					 array('%d', '%s', '%d'));
				}
			}
		}
	}
}

function rsvp_pro_handleGroupQuestions($attendeeID, $formName = "mainquestion") {
	global $wpdb;
  global $rsvpId;
  
  // Get associated attendees....                      
	$sql = "SELECT id FROM ".PRO_ATTENDEES_TABLE." 
	 	WHERE id IN (SELECT attendeeID FROM ".PRO_ASSOCIATED_ATTENDEES_TABLE." WHERE associatedAttendeeID = %d) 
			OR id in (SELECT associatedAttendeeID FROM ".PRO_ASSOCIATED_ATTENDEES_TABLE." WHERE attendeeID = %d)";
      
  $associatedAttendees = $wpdb->get_results($wpdb->prepare($sql, $attendeeID, $attendeeID));
  foreach($associatedAttendees as $aa) {
    $sql = "DELETE FROM ".PRO_ATTENDEE_ANSWERS." WHERE attendeeID = %d AND questionID IN (SELECT id FROM ".PRO_QUESTIONS_TABLE." WHERE grouping = '".RSVP_PRO_QG_MULTI."')";
    $wpdb->query($wpdb->prepare($sql, $aa->id));   
  }
	
	$qRs = $wpdb->get_results($wpdb->prepare("SELECT q.id, questionType, q.sortOrder FROM ".PRO_QUESTIONS_TABLE." q 
					INNER JOIN ".PRO_QUESTION_TYPE_TABLE." qt ON qt.id = q.questionTypeID 
          WHERE q.rsvpEventID = %d AND q.grouping = '".RSVP_PRO_QG_MULTI."' 
          UNION 
          SELECT q.id, questionType, q.sortOrder FROM ".PRO_QUESTIONS_TABLE." q 
          INNER JOIN ".PRO_QUESTION_TYPE_TABLE." qt ON qt.id = q.questionTypeID 
          INNER JOIN ".PRO_EVENT_TABLE." e ON e.id = q.rsvpEventID 
          WHERE e.parentEventID = %d AND grouping = '".RSVP_PRO_QG_MULTI."' 
					ORDER BY sortOrder", $rsvpId, $rsvpId));
	if(count($qRs) > 0) {
		foreach($qRs as $q) {
			if(isset($_POST[$formName.$q->id]) && !empty($_POST[$formName.$q->id])) {
				if($q->questionType == QT_MULTI) {
					$selectedAnswers = "";
					$aRs = $wpdb->get_results($wpdb->prepare("SELECT id, answer FROM ".PRO_QUESTION_ANSWERS_TABLE." WHERE questionID = %d", $q->id));
					if(count($aRs) > 0) {
						foreach($aRs as $a) {
							if(in_array($a->id, $_POST[$formName.$q->id])) {
								$selectedAnswers .= ((strlen($selectedAnswers) == "0") ? "" : "||").stripslashes($a->answer);
							}
						}
					}
					
					if(!empty($selectedAnswers)) {
						$wpdb->insert(PRO_ATTENDEE_ANSWERS, array("attendeeID" => $attendeeID, 
																	"answer" => stripslashes($selectedAnswers), 
																	"questionID" => $q->id), 
															array('%d', '%s', '%d'));
			            foreach($associatedAttendees as $aa) {
	   						$wpdb->insert(PRO_ATTENDEE_ANSWERS, array("attendeeID" => $aa->id, 
	   																  "answer" => stripslashes($selectedAnswers), 
	   																  "questionID" => $q->id), 
	   															array('%d', '%s', '%d'));
			            }
					}
				} else if (($q->questionType == QT_DROP) || ($q->questionType == QT_RADIO)) {
					$aRs = $wpdb->get_results($wpdb->prepare("SELECT id, answer FROM ".PRO_QUESTION_ANSWERS_TABLE." WHERE questionID = %d", $q->id));
					if(count($aRs) > 0) {
						foreach($aRs as $a) {
							if($a->id == $_POST[$formName.$q->id]) {
								$wpdb->insert(PRO_ATTENDEE_ANSWERS, array("attendeeID" => $attendeeID, 
																		 "answer" => stripslashes($a->answer), 
																		 "questionID" => $q->id), 
																	array('%d', '%s', '%d'));
                                                 
				                foreach($associatedAttendees as $aa) {
				       						$wpdb->insert(PRO_ATTENDEE_ANSWERS, array("attendeeID" => $aa->id, 
				       																"answer" => stripslashes($a->answer), 
				       																"questionID" => $q->id), 
				       															array('%d', '%s', '%d'));
				                }
								break;
							}
						}
					}
				} else {
					$wpdb->insert(PRO_ATTENDEE_ANSWERS, array("attendeeID" => $attendeeID, 
															"answer" => $_POST[$formName.$q->id], 
															"questionID" => $q->id), 
														array('%d', '%s', '%d'));
		          	foreach($associatedAttendees as $aa) {
	   					$wpdb->insert(PRO_ATTENDEE_ANSWERS, array("attendeeID" => $aa->id, 
	   															"answer" => $_POST[$formName.$q->id], 
	   															"questionID" => $q->id), 
	   														array('%d', '%s', '%d'));
		          	}
				}
			}
		}
	}
}

function rsvp_pro_auto_rsvp_attendees($rsvpEventId) {
	global $wpdb;

	if((rsvp_pro_get_event_option($rsvpEventId, RSVP_PRO_OPTION_ENABLE_WAITLIST) == "Y") && (rsvp_pro_get_event_option($rsvpEventId, RSVP_PRO_OPTION_WAITLIST_AUTO_CHANGE) == "Y")) {

		// Get limit and counts...
		$limit = rsvp_pro_get_event_option($rsvpEventId, RSVP_PRO_OPTION_EVENT_COUNT_LIMIT);
		$yesCount = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM ".PRO_ATTENDEES_TABLE." WHERE rsvpStatus = 'Yes' AND rsvpEventID = %d", $rsvpEventId));
		if($limit > $yesCount) {
			$numToAutoYes = ($limit - $yesCount);
			// Auto set anyone to "yes" that is currently "waitlisted" and we have not hit the limit
			$sql = "SELECT id FROM ".PRO_ATTENDEES_TABLE." 
					WHERE rsvpStatus = 'Waitlist' AND rsvpEventID = %d 
					ORDER BY rsvpDate DESC, id";
			$attendees = $wpdb->get_results($wpdb->prepare($sql, $rsvpEventId));
			foreach($attendees as $a) {
				if($numToAutoYes > 0) {
					$numToAutoYes--;
					$wpdb->update(PRO_ATTENDEES_TABLE, 
								  array("rsvpStatus" => "Yes"), 
								  array("id" => $a->id), 
								  array("%s"), 
								  array("%d"));
					// Send notification...
					rsvp_pro_send_waitlist_status_change_notification($rsvpEventId, $a->id);
				}
			}
		}
	}
	// Look for the main event and see if there are any people that should be auto "yes"d
	// 		- Send notifications
	// Look at the sub-events and see if there are any people that should be auto "yes"d
	// 		- Send notifications
	$sql = "SELECT id FROM ".PRO_EVENT_TABLE." WHERE parentEventID = %d";
	$subEvents = $wpdb->get_results($wpdb->prepare($sql, $rsvpEventId));
	foreach($subEvents as $se) {
		if((rsvp_pro_get_sub_event_option($se->id, RSVP_PRO_OPTION_ENABLE_WAITLIST) == "Y") && (rsvp_pro_get_sub_event_option($se->id, RSVP_PRO_OPTION_WAITLIST_AUTO_CHANGE) == "Y")) {

			// Get limit and counts...
			$limit = rsvp_pro_get_sub_event_option($se->id, RSVP_PRO_OPTION_EVENT_COUNT_LIMIT);
			$yesCount = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM ".PRO_ATTENDEE_SUB_EVENTS_TABLE." WHERE rsvpStatus = 'Yes' AND rsvpEventID = %d", $se->id));
			if($limit > $yesCount) {
				$numToAutoYes = ($limit - $yesCount);
				// Auto set anyone to "yes" that is currently "waitlisted" and we have not hit the limit
				$sql = "SELECT rsvpAttendeeID FROM ".PRO_ATTENDEE_SUB_EVENTS_TABLE." 
						WHERE rsvpStatus = 'Waitlist' AND rsvpEventID = %d 
						ORDER BY rsvpDate DESC, id";
				$attendees = $wpdb->get_results($wpdb->prepare($sql, $se->id));
				foreach($attendees as $a) {
					if($numToAutoYes > 0) {
						$numToAutoYes--;
						$wpdb->update(PRO_ATTENDEE_SUB_EVENTS_TABLE, 
									  array("rsvpStatus" => "Yes"), 
									  array("rsvpAttendeeID" => $a->rsvpAttendeeID), 
									  array("%s"), 
									  array("%d"));
						// Send notification...
						rsvp_pro_send_waitlist_status_change_notification($se->id, $a->rsvpAttendeeID);
					}
				}
			}
		}
	}
}

/*
 * This function was created to fulfill the requirement that when a new additional attendee is added 
 * they copy the events the other main attendee has access to. The logic is that if the main user has 
 * access to the event and they are adding an additional user they should have access as well...
*/
function rsvp_pro_copy_event_permissions($copyToAttendeeID, $copyFromAttendeeID) {
  global $wpdb;
  
  $sql = "INSERT INTO ".PRO_EVENT_ATTENDEE_TABLE."(rsvpEventID, rsvpAttendeeID) 
    SELECT rsvpEventID, %d FROM ".PRO_EVENT_ATTENDEE_TABLE." 
    WHERE rsvpAttendeeID = %d";
    
  $wpdb->query($wpdb->prepare($sql, $copyToAttendeeID, $copyFromAttendeeID));
}