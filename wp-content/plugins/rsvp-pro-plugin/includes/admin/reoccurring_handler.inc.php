<?php
	function rsvp_pro_handle_reoccurring_events() {
		global $wpdb;

		$sql = "SELECT id, eventName, open_date, close_date, repeatStartDate, 
				repeatEndDate, repeatFrequency, repeatFrequencyType, eventLength, 
				eventLengthType, currentRepeatEndDate 
				FROM ".PRO_EVENT_TABLE." 
				WHERE (parentEventID = 0) AND (repeatStartDate IS NOT NULL)";
		$events = $wpdb->get_results($sql);

		foreach($events as $event) {
			$currTime = time();
			if((strtotime($event->repeatStartDate) <= $currTime) && 
				((strtotime($event->repeatEndDate) >= $currTime) || ($event->repeatEndDate == "0000-00-00")) && 
				($event->repeatFrequencyType != "")) {

				// Check for currentRepeatEndDate and use that for the calculation...otherwise run the calculation ourselves...
				$currentRepeatEndDate = "";
				if(strtotime($event->currentRepeatEndDate)) {
					$currentRepeatEndDate = strtotime($event->currentRepeatEndDate);
				} else {
					$currentRepeatEndDate = strtotime("+".$event->repeatFrequency." ".$event->repeatFrequencyType, strtotime($event->repeatStartDate));
				}
				
				if($currentRepeatEndDate <= $currTime) {
					if(rsvp_pro_get_event_option($event->id, RSVP_PRO_OPTION_REPEAT_DONT_SAVE_EVENTS) != "Y") {
						rsvp_pro_reoccuring_copy($event->id);
					}
					rsvp_pro_reoccuring_delete_attendees($event->id);

					// Generate new current end date. We have to make sure the new current end date is past the current time in the case 
					// where people set it in the past. 
					$newEndDate = $currentRepeatEndDate;
					while($newEndDate < $currTime) {
						$newEndDate = strtotime("+".$event->repeatFrequency." ".$event->repeatFrequencyType, $newEndDate);
					}

					// We use this information to calculate the event start and end date
					$newEventStartDate = strtotime("-".$event->repeatFrequency." ".$event->repeatFrequencyType, $newEndDate);
					$newEventEndDate = strtotime("+".$event->eventLength." ".$event->eventLengthType, $newEventStartDate);
					
					// Update event start and end date 
					// Update currentRepeatEndDate 	
					$wpdb->update(PRO_EVENT_TABLE, 
    							array("currentRepeatEndDate" => date("Y-m-d", $newEndDate),
    								  "open_date" => date("Y-m-d", $newEventStartDate),
    								  "close_date" => date("Y-m-d", $newEventEndDate)),
    							array("id" => $event->id), 
    							array("%s", "%s", "%s"), 
    							array("%d"));
				}
			}
		}
	}

	function rsvp_pro_reoccuring_copy($eventId) {
		global $wpdb;

		$newEventName = "";
		$sql = "SELECT eventName, open_date FROM ".PRO_EVENT_TABLE." WHERE id = %d";
		$event = $wpdb->get_row($wpdb->prepare($sql, $eventId));
		if($event) {
			$newEventName = stripslashes($event->eventName)." - ".date_i18n( get_option( 'date_format'), strtotime($event->open_date));
			rsvp_pro_admin_handle_copy_event($eventId, $newEventName, true, true);	
		}
	}

	function rsvp_pro_reoccuring_delete_attendees($eventId) {
		global $wpdb;

		$wpdb->query($wpdb->prepare("DELETE aa.* FROM ".PRO_ASSOCIATED_ATTENDEES_TABLE." aa 
                                   INNER JOIN ".PRO_ATTENDEES_TABLE." a ON a.id = aa.attendeeID 
                                   WHERE a.rsvpEventID = %d", $eventId));
                                   
      	$wpdb->query($wpdb->prepare("DELETE aa.* FROM ".PRO_ATTENDEE_ANSWERS." aa 
                                   INNER JOIN ".PRO_ATTENDEES_TABLE." a ON a.id = aa.attendeeID 
                                   WHERE a.rsvpEventID = %d", $eventId));
                                   
      	$wpdb->query($wpdb->prepare("DELETE ".PRO_ATTENDEE_SUB_EVENTS_TABLE." WHERE rsvpEventID = %d", $eventId)); 
      
      	$wpdb->query($wpdb->prepare("DELETE FROM ".PRO_ATTENDEES_TABLE." WHERE rsvpEventID = %d", $eventId));

	}
