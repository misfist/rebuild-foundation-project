<?php
/**
 * Additional Attendee JS Functions 
 *
 * @package rsvp-pro
 * @author Swim or Die Software
 * @since 2.1.6
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Outputs the JavaScript for the normal attendee form process
 * 
 * @param  int $rsvpId     [description]
 * @param  result $attendee   [description]
 * @param  int $attendeeID [description]
 * @return none
 */
function rsvp_pro_output_additional_js($rsvpId, $attendee, $attendeeID) {
	if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_HIDE_ADDITIONAL) != "Y") {
    	$yesText = __("Yes", 'rsvp-pro-plugin');
    	$noText  = __("No", 'rsvp-pro-plugin'); 
    	$waitlistText = __("Waitlist", "rsvp-pro-plugin");
    	$noResponseText = __("No Response", "rsvp-pro-plugin");
    	$hasAccessToMainEvent = does_user_have_access_to_event($rsvpId, $attendeeID);
    	$salutation = __("Salutation", 'rsvp-pro-plugin');
    	$firstName = __("First name", 'rsvp-pro-plugin');
    	$lastName = __("Last name", 'rsvp-pro-plugin');
    	$suffix = __("Suffix", 'rsvp-pro-plugin');
    	$email = __("Email address", 'rsvp-pro-plugin');
    	$removeButton = __("Remove Guest", "rsvp-pro-plugin");

	    if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_YES_VERBIAGE) != "") {
	    	$yesText = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_YES_VERBIAGE);
	    }

	    if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_NO_VERBIAGE) != "") {
	    	$noText = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_NO_VERBIAGE);
	    }

	    if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_WAITLIST_VERBIAGE) != "") {
	    	$waitlistText = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_WAITLIST_VERBIAGE);
	    }

	    if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_NO_RESPONSE_TEXT) != "") {
	    	$noResponseText = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_NO_RESPONSE_TEXT);
	    }

	    if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_SALUTATION_LABEL) != "") {
	    	$salutation = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_SALUTATION_LABEL);
	    }

	    if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_FIRST_NAME_LABEL) != "") {
	    	$firstName = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_FIRST_NAME_LABEL);
	    }

	    if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_LAST_NAME_LABEL) != "") {
	    	$lastName = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_LAST_NAME_LABEL);
	    }

	    if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_SUFFIX_LABEL) != "") {
	    	$suffix = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_SUFFIX_LABEL);
	    }

	    if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_EMAIL_LABEL) != "") {
	    	$email = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_EMAIL_LABEL);
	    }

	    if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_REMOVE_ATTENDEE_BUTTON_TEXT) != "") {
	    	$removeButton = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_REMOVE_ATTENDEE_BUTTON_TEXT);
	    }

	    $greeting = __("Will this person be attending?", 'rsvp-pro-plugin');
		if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_ADDITIONAL_GREETING_TEXT) != "") {
			$greeting = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_ADDITIONAL_GREETING_TEXT);
		}


	    $numGuests = 3;
	    if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_NUM_ADDITIONAL_GUESTS) != "") {
	      $numGuests = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_NUM_ADDITIONAL_GUESTS);
	      if(!is_numeric($numGuests) || ($numGuests < 0)) {
	        $numGuests = 3;
	      }
	    }
    
	    if(($attendee != null) && ($attendee->numGuests > 0)) {
	      $numGuests = $attendee->numGuests;
	    }
    
    	ob_start();
    	?>
			<script type="text/javascript" language="javascript">
				function handleAddRsvpClick() {
					var numAdditional = jQuery("#additionalRsvp").val();
					numAdditional++;
					if(numAdditional > <?php echo $numGuests; ?>) {
						alert('<?php echo esc_html__("You have already added ".$numGuests." additional rsvp\'s you can add no more.", 'rsvp-pro-plugin'); ?>');
					} else {
						jQuery("#additionalRsvpContainer").append("<div class=\"rsvpAdditionalAttendee\">" + 
                        	"<div class=\"rsvpAdditionalAttendeeQuestions\">" + 
                        <?php
                        if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_SHOW_SALUTATION) == "Y") {    
                        ?>
  							"<div class=\"rsvpFormField\">" + 
                          	"	<p class=\"rsvpParagraph\"><label for=\"newAttending" + numAdditional + "Salutation\"><?php echo esc_html($salutation); ?></label></p>" + 
  							"  	<select name=\"newAttending" + numAdditional + "Salutation\" id=\"newAttending" + numAdditional + "Salutation\" size=\"1\"><option value=\"\">--</option>" + 
  							<?php
                            $salutations = rsvp_pro_get_salutation_options($rsvpId);
                            foreach($salutations as $s) {
                            ?>
                            	"<option value=\"<?php echo esc_html($s); ?>\"><?php echo esc_html($s); ?></option>" + 
                            <?php
                            }
                            ?>
                            
                        	"</select></div>" + 
                        <?php
                        }
                        ?>
                        
						"<div class=\"rsvpFormField\">" + 
                        "	<p class=\"rsvpParagraph\"><label for=\"newAttending" + numAdditional + "FirstName\"><?php echo esc_html($firstName); ?></label></p>" + 
						" 	<input type=\"text\" name=\"newAttending" + numAdditional + "FirstName\" id=\"newAttending" + numAdditional + "FirstName\" />" + 
						"</div>" + 
						"<div class=\"rsvpFormField\">" + 
                        "	<p class=\"rsvpParagraph\"><label for=\"newAttending" + numAdditional + "LastName\"><?php echo esc_html($lastName); ?></label></p>" + 
						"  <input type=\"text\" name=\"newAttending" + numAdditional + "LastName\" id=\"newAttending" + numAdditional + "LastName\" />" + 
                        "</div>" + 
                        
                        <?php 
                        if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_SHOW_SUFFIX) == "Y") {
                        ?>
  							"<div class=\"rsvpFormField\">" + 
                          	"	<p class=\"rsvpParagraph\"><label for=\"newAttending" + numAdditional + "Suffix\"><?php echo esc_html($suffix); ?></label></p>" + 
  							"  <input type=\"text\" name=\"newAttending" + numAdditional + "Suffix\" id=\"newAttending" + numAdditional + "Suffix\" />" + 
                          	"</div>" + 
                        <?php
                        }
                        if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_HIDE_EMAIL_FIELD) != "Y") {
                        ?>
  							"<div class=\"rsvpFormField\">" + 
                          	"	<p class=\"rsvpParagraph\"><label for=\"newAttending" + numAdditional + "Email\"><?php echo esc_html($email); ?></label></p>" + 
  							"  <input type=\"text\" name=\"newAttending" + numAdditional + "Email\" id=\"newAttending" + numAdditional + "Email\" <?php echo ((rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_EMAIL_REQUIRED) == "Y") ? "required" : ""); ?> />" + 
                          	"</div>" + 
                        <?php
                        }
                        
                        if($hasAccessToMainEvent) {
                          $required = "";
                          if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_RSVP_REQUIRED) == "Y") {
                            $required = " required";
                          }

                          if(rsvp_pro_frontend_max_limit_hit($rsvpId)) {
                          	$required .= " disabled=\\\"true\\\"";
                          }
                        ?>
  							"<div class=\"rsvpFormField rsvpRsvpGreeting\">" + 
  								"<h4><?php echo addslashes(str_replace("\r\n", "", nl2br($greeting))); ?></h4>" + 
                              	"<div class=\"rsvpFormField rsvpRsvpQuestionArea\">" + 
  									"<input type=\"radio\" name=\"newAttending" + numAdditional + "\" value=\"Y\" id=\"newAttending" + numAdditional + "Y\" <?php echo $required; ?> />" + 
  									"<label for=\"newAttending" + numAdditional + "Y\"><?php echo addslashes($yesText); ?></label></div> " + 
                              	"<div class=\"rsvpFormField rsvpRsvpQuestionArea\">" + 
  									"<input type=\"radio\" name=\"newAttending" + numAdditional + "\" value=\"N\" id=\"newAttending" + numAdditional + "N\"> <label for=\"newAttending" + numAdditional + "N\"><?php echo addslashes($noText); ?></label></div>" + 
  						<?php
  						if((rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_ENABLE_WAITLIST) == "Y") && rsvp_pro_frontend_max_limit_hit($rsvpId)) {
  						?>
  							"<div class=\"rsvpFormField rsvpRsvpQuestionArea\">" + 
                            "<input type=\"radio\" name=\"newAttending" + numAdditional + "\" value=\"W\" id=\"newAttending" + numAdditional + "Waitlist\"> <label for=\"newAttending" + numAdditional + "Waitlist\"><?php echo esc_html($waitlistText); ?></label></div>" +
                        <?php
  						}

                        if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_SHOW_NORESPONSE_FOR_ASSOCIATED) == "Y") {
                        ?>
                            "<div class=\"rsvpFormField rsvpRsvpQuestionArea\">" + 
                            "<input type=\"radio\" name=\"newAttending" + numAdditional + "\" value=\"NoResponse\" id=\"newAttending" + numAdditional + "NoResponse\"> <label for=\"newAttending" + numAdditional + "NoResponse\"><?php echo esc_html($noResponseText); ?></label></div>" + 
                        <?php
                        }
                        ?>
                            
  						"</div>" + 

  						<?php						
                        } // if ($hasAccessToMain...)
                        $tmpVar = str_replace("\r\n", "", str_replace("||", "\"", addSlashes(rsvp_pro_buildSubEventMainForm(0, "|| + numAdditional + ||"))));
                        ?>

                        "<?php echo $tmpVar; ?>" + 
                        <?php
						$tmpVar = str_replace("\r\n", "", str_replace("||", "\"", addSlashes(rsvp_pro_buildAdditionalQuestions(0, "|| + numAdditional + ||"))));
						?>

						"<?php echo $tmpVar; ?>" + 
                        "<p><button onclick=\"removeAdditionalRSVP(this);\"><?php echo esc_html($removeButton); ?></button></p>" + 
						"</div>");
						jQuery("#additionalRsvp").val(numAdditional);
                    	jQuery("#numAvailableToAdd").text(<?php echo $numGuests; ?> - numAdditional);
                    	jQuery(document).resize();
					}
				}
                
                function removeAdditionalRSVP(rsvp) {
					var numAdditional = jQuery("#additionalRsvp").val();
					numAdditional--;
                  	jQuery(rsvp).parent().parent().remove();
                  	jQuery("#additionalRsvp").val(numAdditional);
                  	jQuery("#numAvailableToAdd").text(<?php echo $numGuests; ?> - numAdditional);
                  	jQuery(document).resize();
                }
			</script>
    <?php
    	echo ob_get_clean();
	}
}

function rsvp_pro_wizard_output_additional_js($rsvpId, $attendee, $attendeeID) {
  $form = "";
  
	if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_HIDE_ADDITIONAL) != "Y") {
	    $numGuests = get_number_additional($rsvpId, $attendee);
	    $salutation = __("Salutation", 'rsvp-pro-plugin');
	    $firstName = __("First name", 'rsvp-pro-plugin');
	    $lastName = __("Last name", 'rsvp-pro-plugin');
	    $suffix = __("Suffix", 'rsvp-pro-plugin');
	    $email = __("Email address", 'rsvp-pro-plugin');
	    $removeGuest = __("Remove Guest", "rsvp-pro-plugin");
	    
	    if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_SALUTATION_LABEL) != "") {
	      $salutation = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_SALUTATION_LABEL);
	    }

	    if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_FIRST_NAME_LABEL) != "") {
	      $firstName = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_FIRST_NAME_LABEL);
	    }

	    if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_LAST_NAME_LABEL) != "") {
	      $lastName = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_LAST_NAME_LABEL);
	    }

	    if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_SUFFIX_LABEL) != "") {
	      $suffix = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_SUFFIX_LABEL);
	    }

	    if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_EMAIL_LABEL) != "") {
	      $email = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_EMAIL_LABEL);
	    }

	    if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_REMOVE_ATTENDEE_BUTTON_TEXT) != "") {
	      $removeGuest = rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_REMOVE_ATTENDEE_BUTTON_TEXT);
	    }
	    ob_start();
	?>
		<script type="text/javascript" language="javascript">
			function handleAddWizardRsvpClick() {
				var numAdditional = jQuery("#additionalRsvp").val();
				numAdditional++;
				if(numAdditional > <?php echo $numGuests; ?>) {
					alert('<?php esc_html__("You have already added ".$numGuests." additional rsvp\'s you can add no more.", 'rsvp-pro-plugin'); ?>');
				} else {
					jQuery("#additionalRsvpContainer").append("<div class=\"rsvpAdditionalAttendee\">" + 
                        "<div class=\"rsvpAdditionalAttendeeQuestions\">" + 
                    <?php 
                        if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_SHOW_SALUTATION) == "Y") {
                    ?>
  						"<div class=\"rsvpFormField\">" + 
                        "	<p class=\"rsvpParagraph\"><label for=\"newAttending" + numAdditional + "Salutation\"><?php esc_html($salutation); ?></label></p>" + 
  						"   <select name=\"newAttending" + numAdditional + "Salutation\" id=\"newAttending" + numAdditional + "Salutation\" size=\"1\"><option value=\"\">--</option>" + 
  						<?php 
                            $salutations = rsvp_pro_get_salutation_options($rsvpId);
                            foreach($salutations as $s) {
                        ?>
                              "<option value=\"<?php echo esc_html($s); ?>\"><?php echo esc_html($s); ?></option>" + 
                        <?php
                            }
                        ?>
                        "	</select></div>" + 
                        <?php 
                        }
                        ?>

                        "<div class=\"rsvpFormField\">" + 
                        "	<p class=\"rsvpParagraph\"><label for=\"newAttending" + numAdditional + "FirstName\"><?php echo esc_html($firstName); ?></label></p>" + 
						"  	<input type=\"text\" name=\"newAttending" + numAdditional + "FirstName\" id=\"newAttending" + numAdditional + "FirstName\" />" +
						"</div>" + 
						"<div class=\"rsvpFormField\">" +
                        "	<p class=\"rsvpParagraph\"><label for=\"newAttending" + numAdditional + "LastName\"><?php echo esc_html($lastName); ?></label></p>" + 
						"  	<input type=\"text\" name=\"newAttending" + numAdditional + "LastName\" id=\"newAttending" + numAdditional + "LastName\" />" + 
                        "</div>" + 

                        <?php
                        if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_SHOW_SUFFIX) == "Y") {
                        ?>
  						"<div class=\"rsvpFormField\">" + 
                        "	<p class=\"rsvpParagraph\"><label for=\"newAttending" + numAdditional + "Suffix\"><?php echo esc_html($suffix); ?></label></p>" + 
  						"  <input type=\"text\" name=\"newAttending" + numAdditional + "Suffix\" id=\"newAttending" + numAdditional + "Suffix\" />" + 
                        "</div>" +
                        <?php
                        }

                        if(rsvp_pro_get_event_option($rsvpId, RSVP_PRO_HIDE_EMAIL_FIELD) != "Y") {
                        ?>
  						"<div class=\"rsvpFormField\">" + 
                        "	<p class=\"rsvpParagraph\"><label for=\"newAttending" + numAdditional + "Email\"><?php echo esc_html($email); ?></label></p>" + 
  						"  <input type=\"text\" name=\"newAttending" + numAdditional + "Email\" id=\"newAttending" + numAdditional + "Email\" <?php echo ((rsvp_pro_get_event_option($rsvpId, RSVP_PRO_OPTION_EMAIL_REQUIRED) == "Y") ? "required" : ""); ?> />" + 
                        "</div>" + 
                        <?php 
                        }
                        ?>
												
						"<p><button onclick=\"removeAdditionalRSVP(this);\"><?php echo esc_html($removeGuest); ?></button></p>" + 
						"</div>");
						jQuery("#additionalRsvp").val(numAdditional);
                    	jQuery("#numAvailableToAdd").text(<?php echo $numGuests; ?> - numAdditional);
                    	jQuery(document).resize();
					}
				}
                
                function removeAdditionalRSVP(rsvp) {
					var numAdditional = jQuery("#additionalRsvp").val();
					numAdditional--;
                  	jQuery(rsvp).parent().parent().remove();
                  	jQuery("#additionalRsvp").val(numAdditional);
                  	jQuery("#numAvailableToAdd").text(<?php echo $numGuests; ?> - numAdditional);
                  	jQuery(document).resize();
                }
			</script>
    <?php          
    	echo ob_get_clean();
	}
}
