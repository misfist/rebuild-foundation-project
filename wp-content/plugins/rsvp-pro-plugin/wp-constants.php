<?php
global $wpdb;

define("PRO_ATTENDEES_TABLE", $wpdb->prefix."rsvpAttendees");
define("PRO_ATTENDEE_SUB_EVENTS_TABLE", $wpdb->prefix."rsvpAttendeeSubEvents");
define("PRO_ASSOCIATED_ATTENDEES_TABLE", $wpdb->prefix."rsvpAssociatedAttendees");
define("PRO_QUESTIONS_TABLE", $wpdb->prefix."rsvpProCustomQuestions");
define("PRO_QUESTION_TYPE_TABLE", $wpdb->prefix."rsvpProQuestionTypes");
define("PRO_ATTENDEE_ANSWERS", $wpdb->prefix."rsvpAttendeeAnswers");
define("PRO_QUESTION_ANSWERS_TABLE", $wpdb->prefix."rsvpProCustomQuestionAnswers");
define("PRO_QUESTION_ATTENDEES_TABLE", $wpdb->prefix."rsvpProCustomQuestionAttendees");
define("PRO_EVENT_TABLE", $wpdb->prefix."rsvpEvents");
define("PRO_EVENT_ATTENDEE_TABLE", $wpdb->prefix."rsvpEventAttendees");
define("EDIT_SESSION_KEY", "RsvpEditAttendeeID");
define("EDIT_QUESTION_KEY", "RsvpEditQuestionID");
define("RSVP_PRO_OPTION_GREETING", "rsvp_custom_greeting");
define("RSVP_PRO_OPTION_THANKYOU", "rsvp_custom_thankyou");
define("RSVP_PRO_OPTION_NOT_COMING", "rsvp_not_coming_verbiage");
define("RSVP_PRO_OPTION_YES_VERBIAGE", "rsvp_yes_verbiage");
define("RSVP_PRO_OPTION_NO_VERBIAGE", "rsvp_no_verbiage");
define("RSVP_PRO_OPTION_WAITLIST_VERBIAGE", "rsvp_waitlist_verbiage");
define("RSVP_PRO_OPTION_KIDS_MEAL_VERBIAGE", "rsvp_kids_meal_verbiage");
define("RSVP_PRO_OPTION_VEGGIE_MEAL_VERBIAGE", "rsvp_veggie_meal_verbiage");
define("RSVP_PRO_OPTION_NOTE_VERBIAGE", "rsvp_note_verbiage");
define("RSVP_PRO_OPTION_HIDE_NOTE", "rsvp_hide_note_field");
define("RSVP_PRO_OPTION_HIDE_VEGGIE", "rsvp_hide_veggie");
define("RSVP_PRO_OPTION_HIDE_KIDS_MEAL", "rsvp_hide_kids_meal");
define("RSVP_PRO_OPTION_HIDE_ADDITIONAL", "rsvp_hide_additional");
define("RSVP_PRO_OPTION_WELCOME_TEXT", "rsvp_custom_welcome");
define("RSVP_PRO_OPTION_QUESTION", "rsvp_custom_question_text");
define("RSVP_PRO_OPTION_CUSTOM_YES_NO", "rsvp_custom_yes_no");
define("RSVP_PRO_OPTION_PASSCODE", "rsvp_passcode");
define("RSVP_PRO_OPTION_OPEN_REGISTRATION", "rsvp_open_registration");
define("RSVP_PRO_OPTION_CANT_EDIT", "rsvp_no_editing");
define("RSVP_PRO_OPTION_DONT_USE_HASH", "rsvp_dont_use_hash");
define("RSVP_PRO_OPTION_ADD_ADDITIONAL_VERBIAGE", "rsvp_add_additional_verbiage");
define("RSVP_PRO_OPTION_SHOW_SALUTATION", "rsvp_show_salutation");
define("RSVP_PRO_OPTION_SALUTATIONS", "rsvp_salutations");
define("RSVP_PRO_OPTION_SHOW_SUFFIX", "rsvp_show_suffix");
define("RSVP_PRO_OPTION_SHOW_NORESPONSE_FOR_ASSOCIATED", "rsvp_show_noresponse");
define("RSVP_PRO_NUM_ADDITIONAL_GUESTS", "rsvp_num_additional_guests");
define("RSVP_PRO_HIDE_EMAIL_FIELD", "rsvp_hide_email_field");
define("RSVP_PRO_ONLY_PASSCODE", "rsvp_only_passcode");
define("RSVP_PRO_OPTION_EMAIL_TEXT", "rsvp_email_text");
define("RSVP_PRO_OPTION_DISABLE_USER_SEARCH", "rsvp_disable_user_search");
define("RSVP_PRO_OPTION_PASSWORD_LENGTH", "rsvp_password_custom_length");
define("RSVP_PRO_OPTION_EMAIL_REQUIRED", "rsvp_require_email");
define("RSVP_PRO_OPTION_RSVP_REQUIRED", "rsvp_values_required");
define("RSVP_PRO_OPTION_RSVP_OPEN_NO_PASSCODE", "rsvp_open_reg_no_passcode");
define("RSVP_PRO_OPTION_FRONTEND_WIZARD", "rsvp_frontend_wizard");
define("RSVP_PRO_OPTION_NEW_ATTENDEE_BUTTON_TEXT", "rsvp_new_attendee_button_text");
define("RSVP_PRO_OPTION_RSVP_BUTTON_TEXT", "rsvp_frontend_button_text");
define("RSVP_PRO_OPTION_NEXT_BUTTON_TEXT", "next_button_text");
define("RSVP_PRO_OPTION_EVENT_COUNT_LIMIT", "rsvp_event_count_limit");
define("RSVP_PRO_OPTION_MAX_COUNT_REACHED_TEXT", "rsvp_max_count_reached_text");
define("RSVP_PRO_OPTION_ENABLE_WAITLIST", "rsvp_enable_waitlist");
define("RSVP_PRO_OPTION_WAITLIST_TEXT", "rsvp_waitlist_text");
define("RSVP_PRO_OPTION_OPEN_DATE_TEXT", "rsvp_open_date_text");
define("RSVP_PRO_OPTION_CLOSE_DATE_TEXT", "rsvp_close_date_text");
define("RSVP_PRO_OPTION_ATTENDEE_LIST_FILTER", "rsvp_attendee_list_filter");
define("RSVP_PRO_OPTION_ATTENDEE_LIST_SORT_ORDER", "rsvp_attendee_list_sort_order");
define("RSVP_PRO_OPTION_ATTENDEE_LIST_HIDE_STATUS", "rsvp_attendee_list_hide_status");
define("RSVP_PRO_OPTION_ATTENDEE_LIST_CUSTOM_QUESTIONS", "rsvp_attendee_list_custom_questions");
define("RSVP_PRO_OPTION_AUTO_LOGIN_ATTENDEE", "rsvp_attendee_auto_login");
define("RSVP_PRO_OPTION_MODIFY_REGISTRATION_TEXT", "rsvp_modify_registration_text");
define("RSVP_PRO_OPTION_ADD_ATTENDEE_BUTTON_TEXT", "rsvp_add_attendee_button_text");
define("RSVP_PRO_OPTION_REMOVE_ATTENDEE_BUTTON_TEXT", "rsvp_remove_attendee_button_text");
define("RSVP_PRO_OPTION_FIRST_NAME_LABEL", "rsvp_first_name_label");
define("RSVP_PRO_OPTION_LAST_NAME_LABEL", "rsvp_last_name_label");
define("RSVP_PRO_OPTION_PASSCODE_LABEL", "rsvp_passcode_label");
define("RSVP_PRO_OPTION_SUFFIX_LABEL", "rsvp_suffix_label");
define("RSVP_PRO_OPTION_SALUTATION_LABEL", "rsvp_salutation_label");
define("RSVP_PRO_OPTION_EMAIL_LABEL", "rsvp_email_label");
define("RSVP_PRO_OPTION_COMPLETE_BUTTON_TEXT", "rsvp_complete_button_text");
define("RSVP_PRO_OPTION_EDIT_PROMPT_TEXT", "rsvp_edit_prompt_text");
define("RSVP_PRO_OPTION_ADMIN_ROLES", "rsvp_pro_admin_roles");
define("RSVP_PRO_OPTION_EMAIL_CC_ASSOCIATED", "rsvp_email_cc_associated");
define("RSVP_PRO_OPTION_EMAIL_BCC_ADDRESS", "rsvp_email_bcc_address");
define("RSVP_PRO_OPTION_EMAIL_SUBJECT", "rsvp_email_subject");
define("RSVP_PRO_OPTION_EMAIL_BODY", "rsvp_email_body");
define("RSVP_PRO_OPTION_EMAIL_FROM", "rsvp_from_address");
define("RSVP_PRO_GUEST_EMAIL_CONFIRMATION", "rsvp_guest_email_confirmation");
define("RSVP_PRO_OPTION_NOTIFY_ON_RSVP", "rsvp_notify_when_rsvp");
define("RSVP_PRO_OPTION_NOTIFY_EMAIL", "rsvp_notify_email_address");
define("RSVP_PRO_OPTION_ASSOCIATED_MESSAGE", "rsvp_associated_message"); 
define("RSVP_PRO_OPTION_ASSOCIATED_ATTENDEE_GREETING", "rsvp_associated_attendee_greeting");
define("RSVP_PRO_OPTION_MULTI_EVENT_TITLE", "multi_event_title");
define("RSVP_PRO_OPTION_MULTI_OPTION_TEXT", "multi_option_text");
define("RSVP_PRO_OPTION_YES_TEXT", "rsvp_yes_text");
define("RSVP_PRO_OPTION_NO_TEXT", "rsvp_no_text");
define("RSVP_PRO_OPTION_NO_RESPONSE_TEXT", "rsvp_no_response_text");
define("RSVP_PRO_OPTION_FUZZY_MATCH_TEXT", "rsvp_no_exact_match_text");
define("RSVP_PRO_OPTION_MULTIPLE_MATCHES_TEXT", "rsvp_multiple_matches_text");
define("RSVP_PRO_OPTION_UNABLE_TO_FIND_TEXT", "rsvp_unable_to_find_text");
define("RSVP_PRO_OPTION_WELCOME_BACK_TEXT", "rsvp_welcome_back_text");
define("RSVP_PRO_DB_VERSION", "27");
define("QT_SHORT", "shortAnswer");
define("QT_MULTI", "multipleChoice");
define("QT_LONG", "longAnswer");
define("QT_DROP", "dropdown");
define("QT_RADIO", "radio");
define("QT_HIDDEN", "hidden");
define("QT_READ_ONLY", "readonly");
define("RSVP_PRO_DEFAULT_SALUTATION", "Mr.||Mrs.||Ms.||Miss||Dr.");
define("RSVP_PRO_QG_SINGLE", "single");
define("RSVP_PRO_QG_MULTI", "multi");
define("RSVP_PRO_OPEN_EVENT_ACCESS", "open"); // Open to all attendees in the parent event
define("RSVP_PRO_PRIVATE_EVENT_ACCESS", "select"); // Only open to select attendees for sub-events
define("RSVP_PRO_START_PARA", "<p class=\"rsvpParagraph\">");
define("RSVP_PRO_END_PARA", "</p>\r\n");
define("RSVP_PRO_START_CONTAINER", "<div id=\"rsvpPlugin\">\r\n");
define("RSVP_PRO_END_CONTAINER", "</div>\r\n");
define("RSVP_PRO_START_FORM_FIELD", "<div class=\"rsvpFormField\">\r\n");
define("RSVP_PRO_END_FORM_FIELD", "</div>\r\n");
define("RSVP_PRO_INFO_EVENT_NAME", "event_name");
define("RSVP_PRO_INFO_OPEN_DATE", "open_date");
define("RSVP_PRO_INFO_CLOSE_DATE", "close_date");
define("RSVP_PRO_INFO_EVENT_ID", "event_id");
define("RSVP_PRO_INFO_EVENT_ACCESS", "event_access");
define('RSVP_PRO_STORE_URL', 'https://www.rsvpproplugin.com' );
define('RSVP_PRO_ITEM_NAME', 'RSVP Pro Plugin' );
?>