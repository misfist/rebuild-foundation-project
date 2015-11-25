=== RSVP Pro Plugin ===
Contributors: mdedev
Tags: rsvp, reserve, wedding, guestlist
Requires at least: 3.0.0
Tested up to: 4.3.0
Stable tag: 2.0.0

Easy to use rsvp plugin to handle multiple events. 

== Description ==

Multi-event plugin based off of our free RSVP plugin.

The admin functionality allows you to do the following things:

* Specify the opening and close date to rsvp 
* Specify a custom greeting
* Specify the RSVP yes and no text
* Specify the kids meal verbiage
* Specify the vegetarian meal verbiage 
* Specify the text for the note question
* Enter in a custom thank you
* Create a custom message / greeting for each guest
* Import a guest list from an excel sheet (column #1 is the first name, column #2 is the last name, column #3 associated attendees, column #4 custom greeting)
* Export the guest list
* Add, edit and delete guests
* Associate guests with other guests
* Create custom questions that can be asked by each attendee
* Have questions be asked to all guests or limit the question to specific attendees
* Specify email notifications to happen whenever someone rsvps

If there are any improvements or modifications you would like to see in the plugin please feel free to contact me at (mike AT mde DASH dev.com) and 
I will see if I can get them into the plugin for you.  

Available CSS Stylings: 

* rsvpPlugin - ID of the main RSVP Container. Each RSVP step will be wrapped in this container 
* rsvpParagraph - Class name that is used for all paragraph tags on the front end portion of the RSVP
* rsvpFormField - Class for divs that surround a given form input, which is a combination of a label and at least one form input (could be multiple form inputs)
* rsvpAdditionalAttendee - Class for the div container that holds each additional RSVP attendee you are associated with
* additionalRsvpContainer - The container that holds the plus sign that allows for people to add additional attendees
* rsvpCustomGreeting - ID for the custom greeting div that shows up if that option is enabled
* rsvpBorderTop - Class for setting a top border on certain divs in the main input form
* rsvpCheckboxCustomQ - Class for the div that surrounds each custom question checkbox
* rsvpRadioCustomQ - Class for the div that surrounds each custom question radio buttons 
* rsvpClear - A class for div elements that we want to use to set clear both. Currently used only next to rsvpCheckboxCustomQs as they are floated
* rsvpRsvpQuestionArea - A class for the RSVP attending question
* rsvpRsvpGreeting - A class for the "Will you be attending" question in the wizard area

Prefill Attendee:

Go to the page associated with the RSVP form and add to the querystring the following parameters.

* firstName - For the person's first name
* lastName - For the person's last name
* passcode - If passcode is enabled and/or required this will need to be added as well 

For example if you have a page that is /rsvp for domain example.com your URL might look like - http://www.example.com/rsvp?firstName=test&lastName=test 

== Installation ==

1. Upload the `rsvp-pro-plugin` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Specify your license and activate it 
1. Add and/or import your attendees to the plugin and set the options you want
1. Create a blank page and add in the short code it will look like `[rsvp-pluginhere-X]` on the page

== Changelog ==

= 2.0.0 = 
* Made it so the "no" custom message shows up for new attendees 

= 1.9.9 = 
* Made it so the custom thank you shows up for new attendees
* Added in an option to hide the RSVP status on the public attendee list
* Added in an option to show custom questions on the public attendee list
* Added an option to change the welcome back text 
* Added an option to change the "unable to find" user text 

= 1.9.8 = 
* Small change to make sure all rsvp yes/no questions used the customizable text

= 1.9.7 = 
* Added in the rsvpRadioCustomQ CSS class for custom questions that are radio buttons
* Added an option to change the suffix label on the front-end
* Added an option to change the salutation label on the front-end
* Added an option to change the email label on the front-end
* Added an option to change the associated attendee greetings 
* Made it so the labels when adding an additional guest matches customized label options
* Added in a general text area for pieces of text like: yes, no, first name, etc... 
* Added in options for if a direct match was not found but similar people were

= 1.9.6 = 
* Fixed a warning that occurs for custom text for the "add additional" people message
* Added a German translation of the plugin compliments of Wilhelm! 

= 1.9.5 = 
* Changed the email notifications to make sure people have access to the main event when the confirmation email goes out
* Small translation fixes and an updated pot

= 1.9.4 = 
* Fixed a bug where associated attendees were not showing the correct results in the wizard form 
* Fixed a bug where the associated attendees were not showing correctly for sub-events when the main event was select 
* Fixed a bug where the sub-event was not recording the rsvp date 
* Added an the ability to see all of the attendees regardless of the main events permission setting 
* Fixed a small bug related to edit the prompt when a main event has no attendees who have RSVP'd but the attendee has RSVP'd for a sub-event

= 1.9.3 = 
* Fixed an issue with the wizard always showing the veggie and kids meal questions
* Made the RSVP date sortable on the attendee list 
* Made the RSVP status counts work correctly with select events 
* If an attendee can't invite any more people the "invite more" area is not displayed

= 1.9.2. = 
* Added the event name to all admin screens so it is easier to remember what event you are working on
* Added event name as a tag for the mass email feature 
* Added the ability to copy an event
* Cleaned up the admin settings area so it is easier to use 
* Added the ability to change all of the text for the greeting page
* Added the ability to change the edit confirmation prompt

= 1.9.1 = 
* Removed the default children and vegetarian meal questions as custom questions can be used for this
* Fixed a bug in the multiple step form not saving multiple choice questions correctly

= 1.9.0 = 
* Small bug fix for multiple choice questions, there was a warning when you save them if no items were checked

= 1.8.9 = 
* Changed the event settings form so that only the general tab was displayed when creating an event 
* Fixed a bug in the multi-step form where the Yes/No verbiage would not show up on sub-events 

= 1.8.8 = 
* Changed the check for sending out attendee notification checks, it no longer requires the email input to exist on the form

= 1.8.7 = 
* Some small text changes for the attendee notification emails

= 1.8.6 = 
* Surfaced the id on the custom questions admin listing 
* Removed the option to disable using the specified notification email as a from address
* Moved all email settings to the notifications tab
* Added an option to specify a custom from email address for notifications
* Added an option specify a subject for the attendee email confirmations that are sent out
* Added an option to CC all associated attendees with the RSVP confirmation
* Added an option to BCC a specified set of email addresses
* Changed the custom questions to show up under each sub-event
* Changed the email notification to attendees so it uses the yes/no verbiage set in settings
* Added in the main event name to the event confirmation email sent to attendees

= 1.8.5 = 
* Changed the setting role limiting to not effect administrators

= 1.8.4 = 
* Changed the plugin so if you network activate the plugin and have a multi-site license the license code is valid for the whole network
* Added in an option to specify the roles that could access the configuration areas like settings, import, export, etc... 

= 1.8.3 = 
* Made it so the "add additional button" text can be customized
* Made it so the "Need to modify your registration? Start with the below form." text can be customized
* Hid the veggie and kids meal top counts when those options are hidden

= 1.8.2 = 
* Changed the "add additional button" to be a button instead of an image that could not be styled or changed 

= 1.8.1 = 
* Fixed an issue where associated users could not be deassociated when you went to the associated record, you could only deassociate from the attendee record that you associated with. 
* Fixed an issue with a missing closing tag that caused layout issues 

= 1.8.0 = 
* Pointed the plug-in to the new site 

= 1.7.9 = 
* Added in an alternative first name field to handle when people use different first names

= 1.7.8 =
* Added in the option to auto-login WP users if their email address is found in an attendee list 

= 1.7.7 = 
* Added in the ability to specify the sort order for the public attendee list

= 1.7.6 = 
* Added the ability to set a possible answer as default for custom questions

= 1.7.5 = 
* Added in an option for the public guest list to only show people with a specific rsvp reply

= 1.7.4 = 
* Made it so the RSVP question verbiage option is displayed for sub-events

= 1.7.3 = 
* Changed the attendee list so it is sorted by the first name
* Added soundex for fuzzy search for last names and first names

= 1.7.2 = 
* Fixed an issue with the options of hiding the children's and vegetarian options were not working on sub-events when the events were in "wizard" mode

= 1.7.1 = 
* Changed the export to have the first and last name in different columns 

= 1.7.0 = 
* Added the waitlist option to go along with the max event cap 

= 1.6.9 = 
* Added in an option to modify the open and close date messages

= 1.6.8 = 
* Added code to change newlines to breaks in the mass email to go along with the HTML formatting change

= 1.6.6 = 
* Made the mass email send as html to be the same as the notification front-end emails 

= 1.6.5 = 
* Added a change to the mass email functionality. That is magic quotes are enabled we strip the slashes before sending the email.
* Hid the email field on the attendee list 
* Surfaced the RSVP date on the attendee list
* Added the ability to set a max attendee limit on events 

= 1.6.4 = 
* Added an anchor name for the attendee list (rsvpAttendees)
* Fixed an issue with the import process not handling unicode characters when trying to associate attendees

= 1.6.3 = 
* Added the ability to send custom question values in the mass email feature

= 1.6.2 = 
* Fixed a bug where the custom welcome was not showing up when someone wanted to add a new attendee 
* Fixed a bug where the "do not scroll" option was not being correctly toggled
* Added the option to change the text for the RSVP button 
* Added the option to change the text for the Next button (affects wizard mode only)
* Changed the email notifications to send in HTML 
* Changed the email notifications to not include read only custom questions

= 1.6.1 = 
* Added the option to customize the text for the "New Attendee Registration" button

= 1.6.0 = 
* Added the following fields to the import functionality: RSVP status, number of guests allowed, veggie meal, children's meal and note
* Made it so the first and last name fields are required for new attendees

= 1.5.9 = 
* Small fix to handle url parsing better

= 1.5.8 = 
* Small fix to correct the problem of additional attendees having slashes in front of apostrophes on the front-end

= 1.5.7 = 
* Added in a change to prevent the RSVP code from running multiple times when plugins or other themes try to force multiple runs

= 1.5.6 = 
* Made it so when duplicate names are found a screen is displayed giving the users a choice

= 1.5.5 = 
* Fixed a bug where the text customization for the guest count wasn't parsing the %d like the stock text is

= 1.5.4 = 
* Added back in bulk actions for sub-events so people can easily delete and mass message for sub-events

= 1.5.3 = 
* Fixed a small bug in the wizard where if a user did not have a record it would not return the user correctly
* Fixed another small bug in the wizard when a user has access to no events

= 1.5.2 = 
* Added the ability to add in variables to the mass email functionality
* Added two new question types hidden and read only 

= 1.5.1 = 
* Fixed some problems around having access to the main event or not. It would lead to information not always being saved correctly.

= 1.5.0 = 
* Added an additional CSS class to help with more customization

= 1.4.9 = 
* Added some extra CSS classes in and updated the guidelines for it

= 1.4.8 = 
* Fixed a bug where if a user has an answer related to a question they no longer have an access to it gets cleared when the user RSVPs

= 1.4.7 = 
* Query fix for sub-events and pulling attendee information

= 1.4.6 = 
* Typo fix that surfaced from 1.4.5 around the wizard UI

= 1.4.5 = 
* Fixed a bug where the associated attendees would show all associated attendees and not just the ones who have access to the event
* Added in a message of "Will X be attending" for the main attendee for the wizard form

= 1.4.4 = 
* Fixed a bug related to adding additional attendees, depending on the situation the count would be incorrect when saving not allowing all attendees to be saved.
* Changed the default text around adding attendees to show the number left
* Changed the adding of additional attendees to auto-associate them with events if the person adding them had access
* Changed the add attendees functionality in the wizard to match closer with the wizard

= 1.4.3 = 
* Made some changes to speed up exports for large attendees
* Added in some database table indexes to also help speed up the plugin
* Added the option to switch the UI to have a wizard format instead of all the questions on one long form 

= 1.4.2 =
* Added an option to make the RSVP fields (yes/no/no response) required
* Added an option to make it so the open registration functionality doesn't require a passcode
* Fixed a small bug with events when the event_access field was null and it would not show the attendee list in the admin page

= 1.4.1 = 
* Added the ability to make the email field required
* Small bug fix from the 1.4.0 release that made it not work for all versions of PHP

= 1.4.0 = 
* Added the ability to set access to sub-events so only a limited set of attendees can access sub-events

= 1.3.9 = 
* Fixed a bug where importing in with custom questions would yield duplicate answers
* Changed the add attendee front-end button so it would not pre-populate values based on the main attendee

= 1.3.8 = 
* Added in the ability to have "No Response" for associated attendees

= 1.3.7 = 
* Changed the default short codes to be the actual short code 'rsvppro'
* Added rsvpDate to the export and attendee list areas
* Added in search to the attendee list page
* Added in pagination to the attendee list page

= 1.3.6 = 
* Added the ability to change the size of the passcode 
* Moved email and fields around for main questions to make the fields a little more usable
* Changed the import duplication checking to include the email or passcode
* Made it so you can import custom question values 

= 1.3.5 = 
* Fixed a bug when adding a custom question answer and the value was zero

= 1.3.4 = 
* Fixed a small layout bug in the settings area that occurred with 1.3.3 

= 1.3.3 = 
* Added the ability to specify a suffix and salutation
* Changed how the additional RSVPs are calculated. It is now based off the # of allowed additional attendees - # of associated attendees. 

= 1.3.2 = 
* Added the ability to make custom questions required
* Added the ability to specify different text if the person says "no" when RSVP'ing 

= 1.3.1 = 
* Added the ability to have a public facing attendees list

= 1.3.0 = 
* Added an option to turn off the smart searching when a user is not found on the front-end 
* Added the ability to have sub-events so people can RSVP for multiple events at once

= 1.2.9 = 
* Fixed a small bug that happens in the JavaScript when you have a custom question that has a pipe (|) in it
* Added some more front-end styling to deal with themes making the RSVP form unusable in some cases

= 1.2.8 = 
* Made it easier to select attendees for associated attendees and private questions
* Added in a confirmation prompt when deleting an event

= 1.2.7 = 
* Set some default styling for the front-end forms, this is to prevent themes from hiding form elements that should be displayed 
* Made it so you can pass in the passcode in the querystring when the passcode only option is enabled
* Fixed a bug that was causing imports to not always work especially after the first or second try

= 1.2.6 = 
* Added the email to the export file
* Added in different not found text when the passcode only option is enabled 

= 1.2.5 = 
* Made it so admins can edit all attendees information 

= 1.2.4 = 
* Changed how group and single questions were being displayed so the sorting would work as expected.
* Fixed a bug where all custom questions where showing up in all of the attendee grid
* Hid the personal greeting column in the manage attendees section 
* Added in the option to specify number of additional guests per attendee, you can find this on the attendee form 

= 1.2.3 = 
* Fixed a typo in the import form text that did not specify the import file format correctly (it was missing the email field from the format)
* Fixed a bug with private questions in the import functionality

= 1.2.2 = 
* Fixed a bug where the email text wasn't saving in the options area

= 1.2.1 = 
* Fixed a bug with the custom question sort order not saving 

= 1.2.0 = 
* Fixed a bug with the email notifications including questions from all events
* Cleaned up the UI on the RSVP settings page
* Added an option so people can't edit their RSVP after it has been submitted
* Added the passcode to the emails that get sent out when people RSVP
* Added in a from email address when sending a message to attendees

= 1.1.9 = 
* Fixed a problem with slashes being added to apostrophes
* Made it so updates can be polled to make it easier to update the plugin
* Sync'd up all of the email notifications so custom questions and answers are sent
* Changed the "edit" link in the admin area to "settings" 
* Ability to send messages to all or selected attendees

= 1.1.8 =
* Changed the shortcode to rsvp-pro-pluginhere so it didn't conflict with the free version
* Misc bug-fixes

= 1.1.7 = 
* Changed menu name to RSVP Pro
* Added in the ability to migrate from RSVP free
* Renamed the custom questions table to be RSVP pro specific 

= 1.1.6 = 
* Bug fixes with database initialization 

= 1.1.5 = 
* Added in the rsvppro short code, required attribute is "id", it should look something like [rsvppro id="1"] 
* Added the ability to skip the first step if the querystring parameters existed for the form values. 

= 1.1.0 = 
* Added the ability to have questions to be asked only once for the whole group of associated attendees
* Misc bug fixes

= 1.0.0 = 
* Initial release of the multiple event version of the plugin
