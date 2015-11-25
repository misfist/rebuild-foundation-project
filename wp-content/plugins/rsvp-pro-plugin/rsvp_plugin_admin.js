jQuery(document).ready(function(){
  jQuery("#associatedAttendeesSelect").multiSelect(); 
  jQuery("#attendeesQuestionSelect").multiSelect();
  jQuery("#attendeeAccessSelect").multiSelect();
  jQuery("#rsvp_pro_admin_roles").multiSelect();
  jQuery("#rsvp_attendee_list_custom_questions").multiSelect();
  
  jQuery("#parentEventID").change(function() {
    handleParentEventChange();
  })

  jQuery("#rsvp_show_salutation").change(function() {
    handleShowSalutationChange();
  });
  handleShowSalutationChange();
});

function handleParentEventChange() {
  if((jQuery("#parentEventID").val() == "") || (jQuery("#parentEventID").val() == 0)) {
    showAllEventOptionsElements();
  } else {
    hideParentEventOptionsElements();
  }
}

function showAllEventOptionsElements() {
  jQuery(".subEventHide").show();
}

function hideParentEventOptionsElements() {
  jQuery(".subEventHide").hide();
}

function handleShowSalutationChange() {
  if(jQuery("#rsvp_show_salutation").attr("checked")) {
    jQuery("#rsvpProOptionSalutationsContainer").show();
  } else {
    jQuery("#rsvpProOptionSalutationsContainer").hide();
  }
}