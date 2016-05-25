<?php
function handleImportRow($row, $headerRow, $numCols, &$count, $eventID) {
	global $wpdb;

	$passcodeLength = 6;
	if(rsvp_pro_get_event_option($eventID, RSVP_PRO_OPTION_PASSWORD_LENGTH) > 0) {
		$passcodeLength = rsvp_pro_get_event_option($eventID, RSVP_PRO_OPTION_PASSWORD_LENGTH);
	}
	$salutation = trim($row[0]);
	$fName = trim($row[1]);
    $fName = mb_convert_encoding($fName, 'UTF-8', mb_detect_encoding($fName, 'UTF-8, ISO-8859-1', true));
          
	$lName = trim($row[2]);
    $lName = mb_convert_encoding($lName, 'UTF-8', mb_detect_encoding($lName, 'UTF-8, ISO-8859-1', true));
    
    $suffix = trim($row[3]);
    $email = trim($row[4]);
	$personalGreeting = (isset($row[6])) ? $personalGreeting = $row[6] : "";
    $passcode = (isset($row[7])) ? $row[7] : "";
    $rsvpStatus = "noresponse";
    if(isset($row[8])) {
    	$tmpStatus = strtolower($row[8]);
        if(($tmpStatus == "yes") || ($tmpStatus == "no")) {
        	$rsvpStatus = $tmpStatus;
        }
    }
    
    $numGuests = "";
    if(isset($row[9]) && is_numeric($row[9]) && ($row[9] >= 0)) {
    	$numGuests = $row[9];
    }

    $note = (isset($row[10])) ? $row[10] : "";
    $primaryAttendee = (strToUpper($row[11]) == "Y") ? "Y" : "N";
	if(!empty($fName) && !empty($lName)) {
		$sql = "SELECT id FROM ".PRO_ATTENDEES_TABLE." 
				WHERE firstName = %s AND lastName = %s AND rsvpEventID = %d AND (email = %s OR passcode = %s) ";
		$res = $wpdb->get_results($wpdb->prepare($sql, $fName, $lName, $eventID, $email, $passcode));
		if(count($res) == 0) {
        	if($passcode == "") {
            	$passcode = rsvp_pro_generate_passcode($passcodeLength); 
            }

			$wpdb->insert(PRO_ATTENDEES_TABLE, array("firstName" 		=> $fName, 
													 "lastName" 		=> $lName,
                                                     "email"            => $email, 
													 "personalGreeting" => $personalGreeting, 
                                                     "passcode"         => $passcode,
                                                     "salutation"       => $salutation, 
                                                     "suffix"           => $suffix,  
                                                     "rsvpEventID"      => $eventID, 
                                                     "rsvpStatus"       => $rsvpStatus, 
                                                     "numGuests"        => $numGuests, 
                                                     "note"             => $note, 
                                                     "primaryAttendee" 	=> $primaryAttendee), 
							array('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s'));
							$count++;
		}
            
    	if($numCols >= 6) {
    		// There must be associated users so let's associate them
    		// Get the user's id 
			$sql = "SELECT id FROM ".PRO_ATTENDEES_TABLE." 
				 	WHERE firstName = %s AND lastName = %s AND rsvpEventID = %d";
			$res = $wpdb->get_results($wpdb->prepare($sql, $fName, $lName, $eventID));
			if((count($res) > 0) && isset($row[5])) {
				$userId = $res[0]->id;
						
				// Deal with the assocaited users...
				$associatedUsers = explode(",", trim($row[5]));
				if(is_array($associatedUsers)) {
					foreach($associatedUsers as $au) {
						$user = explode(" ", trim($au), 2);
						// Three cases, they didn't enter in all of the information, user exists or doesn't.  
						// If user exists associate the two users
						// If user does not exist add the user and then associate the two
						if(is_array($user) && (count($user) == 2)) {
							$sql = "SELECT id FROM ".PRO_ATTENDEES_TABLE." 
								 	WHERE firstName = %s AND lastName = %s AND rsvpEventID = %d";
							$userRes = $wpdb->get_results($wpdb->prepare($sql, mb_convert_encoding(trim($user[0]), 'UTF-8', mb_detect_encoding(trim($user[0]), 'UTF-8, ISO-8859-1', true)), mb_convert_encoding(trim($user[1]), 'UTF-8', mb_detect_encoding(trim($user[1]), 'UTF-8, ISO-8859-1', true)), $eventID));
							if(count($userRes) > 0) {
								$newUserId = $userRes[0]->id;
							} else {
								// Insert them and then we can associate them...
								$wpdb->insert(PRO_ATTENDEES_TABLE, array("firstName" => mb_convert_encoding(trim($user[0]), 'UTF-8', mb_detect_encoding(trim($user[0]), 'UTF-8, ISO-8859-1', true)), "lastName" => mb_convert_encoding(trim($user[1]), 'UTF-8', mb_detect_encoding(trim($user[1]), 'UTF-8, ISO-8859-1', true)), "rsvpEventID" => $eventID), array('%s', '%s', '%d'));
								$newUserId = $wpdb->insert_id;
								$count++;
							}
									
							$wpdb->insert(PRO_ASSOCIATED_ATTENDEES_TABLE, array("attendeeID" => $newUserId, 
																				"associatedAttendeeID" => $userId), 
																		  array("%d", "%d"));
																														
							$wpdb->insert(PRO_ASSOCIATED_ATTENDEES_TABLE, array("attendeeID" => $userId, 
																				"associatedAttendeeID" => $newUserId), 
																		  array("%d", "%d"));
						} // if(is_array($user)...
					} // foreach($associated..
				} // if(is_array...
			} // if((count($res) > 0)...
    	} // if($numCols >= 6) {
        
        if($numCols >= 13) {
            $private_questions = array();
            $question_values = array();
            for($qid = 12; $qid <= $numCols; $qid++) {
            	$pqid = str_replace("pq_", "", $headerRow[$qid]);
                if(is_numeric($pqid)) {
                	$private_questions[$qid] = $pqid;
                }
            }

            for($qid = 12; $qid <= $numCols; $qid++) {
            	$cqid = str_replace("cq_", "", $headerRow[$qid]);
                if(is_numeric($cqid)) {
                  	$question_values[$qid] = $cqid;
                }
            }
          
            if((count($private_questions) > 0) || (count($question_values) > 0)) {
  				// Get the user's id 
  				$sql = "SELECT id FROM ".PRO_ATTENDEES_TABLE." 
  					 	WHERE firstName = %s AND lastName = %s AND rsvpEventID = %d AND (email = %s OR passcode = %s) ";
  				$res = $wpdb->get_results($wpdb->prepare($sql, $fName, $lName, $eventID, $email, $passcode));
  				if(count($res) > 0) {
  					$userId = $res[0]->id;
                	foreach($private_questions as $key => $val) {
                    	if(strToUpper($row[$key]) == "Y") {
                      		$wpdb->insert(PRO_QUESTION_ATTENDEES_TABLE, array("attendeeID" => $userId, 
                                                                    		  "questionID" => $val), 
                                                              			array("%d", "%d"));
                    	}
                  	}
              
                  	foreach($question_values as $key => $val) {
                    	$wpdb->insert(PRO_ATTENDEE_ANSWERS, array("attendeeID" => $userId, 
                                                              	  "questionID" => $val, 
                                                              	  "answer" => trim($row[$key])), 
                                                       		array("%d", "%d", "%s"));
                  	}
                }
            } // if(count($priv...))
        } // if($data->sheets[0]['numCols'] >= 9)....
	}
}