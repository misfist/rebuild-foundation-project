<?php
  function rsvp_pro_update_database($wpdb) {
  	$installed_ver = get_option("rsvp_pro_db_version");
  	$table = $wpdb->prefix."rsvpAttendees";
  	if($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) {
  		$sql = "CREATE TABLE ".$table." (
  		`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  		`firstName` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
  		`lastName` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
  		`rsvpDate` DATE NULL ,
  		`rsvpStatus` ENUM( 'Yes', 'No', 'NoResponse' ) NOT NULL DEFAULT 'NoResponse',
  		`note` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL,
  		`kidsMeal` ENUM( 'Y', 'N' ) NOT NULL DEFAULT 'N',
  		`additionalAttendee` ENUM( 'Y', 'N' ) NOT NULL DEFAULT 'N',
  		`veggieMeal` ENUM( 'Y', 'N' ) NOT NULL DEFAULT 'N', 
  		`personalGreeting` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL , 
      `email` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
  		);";
  		$wpdb->query($sql);
  	}
  	$table = $wpdb->prefix."rsvpAssociatedAttendees";
  	if($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) {
  		$sql = "CREATE TABLE ".$table." (
  		`attendeeID` INT NOT NULL ,
  		`associatedAttendeeID` INT NOT NULL
  		);";
  		$wpdb->query($sql);
  		$sql = "ALTER TABLE `".$table."` ADD INDEX ( `attendeeID` ) ";
  		$wpdb->query($sql);
  		$sql = "ALTER TABLE `".$table."` ADD INDEX ( `associatedAttendeeID` )";
  		$wpdb->query($sql);
  	}				
  	add_option("rsvp_pro_db_version", "4");
	
  	if((int)$installed_ver < 2) {
  		$table = $wpdb->prefix."rsvpAttendees";
  		$sql = "ALTER TABLE ".$table." ADD `personalGreeting` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ;";
  		$wpdb->query($sql);
  		update_option( "rsvp_pro_db_version", RSVP_PRO_DB_VERSION);
  	}
	
  	if((int)$installed_ver < 4) {
  		$table = $wpdb->prefix."rsvpProCustomQuestions";
  		$sql = "ALTER TABLE ".$table." ADD `sortOrder` INT NOT NULL DEFAULT '99';";
  		$wpdb->query($sql);
  		update_option( "rsvp_pro_db_version", RSVP_PRO_DB_VERSION);
  	}
	
  	$table = $wpdb->prefix."rsvpProCustomQuestions";
  	if($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) {
  		$sql = " CREATE TABLE $table (
  		`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  		`question` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,
  		`questionTypeID` INT NOT NULL, 
  		`sortOrder` INT NOT NULL DEFAULT '99', 
  		`permissionLevel` ENUM( 'public', 'private' ) NOT NULL DEFAULT 'public', 
      `grouping` VARCHAR(30) NOT NULL, 
      `rsvpEventID` INT NOT NULL
  		);";
  		$wpdb->query($sql);
  	}
	
  	$table =  $wpdb->prefix."rsvpProQuestionTypes";
  	if($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) {
  		$sql = " CREATE TABLE $table (
  		`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  		`questionType` VARCHAR( 100 ) NOT NULL , 
  		`friendlyName` VARCHAR(100) NOT NULL 
  		);";
  		$wpdb->query($sql);
		
  		$wpdb->insert($table, array("questionType" => "shortAnswer", "friendlyName" => "Short Answer"), array('%s', '%s'));
  		$wpdb->insert($table, array("questionType" => "multipleChoice", "friendlyName" => "Multiple Choice"), array('%s', '%s'));
  		$wpdb->insert($table, array("questionType" => "longAnswer", "friendlyName" => "Long Answer"), array('%s', '%s'));
  		$wpdb->insert($table, array("questionType" => "dropdown", "friendlyName" => "Drop Down"), array('%s', '%s'));
  		$wpdb->insert($table, array("questionType" => "radio", "friendlyName" => "Radio"), array('%s', '%s'));
  	} else if((int)$installed_ver < 6) {
  		$wpdb->insert($table, array("questionType" => "radio", "friendlyName" => "Radio"), array('%s', '%s'));
  	}
	
  	$table = $wpdb->prefix."rsvpProCustomQuestionAnswers";
  	if($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) {
  		$sql = "CREATE TABLE $table (
  		`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  		`questionID` INT NOT NULL, 
  		`answer` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL
  		);";
  		$wpdb->query($sql);
  	}
	
  	$table = $wpdb->prefix."rsvpAttendeeAnswers";
  	if($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) {
  		$sql = "CREATE TABLE $table (
  		`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  		`questionID` INT NOT NULL, 
  		`answer` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL, 
  		`attendeeID` INT NOT NULL 
  		);";
  		$wpdb->query($sql);
  	}
	
  	$table = $wpdb->prefix."rsvpProCustomQuestionAttendees";
  	if($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) {
  		$sql = "CREATE TABLE $table (
  		`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  		`questionID` INT NOT NULL ,
  		`attendeeID` INT NOT NULL
  		);";
  		$wpdb->query($sql);
  	}
	
  	if((int)$installed_ver < 5) {
  		$table = QUESTIONS_TABLE;
  		$sql = "ALTER TABLE `$table` ADD `permissionLevel` ENUM( 'public', 'private' ) NOT NULL DEFAULT 'public';";
  		$wpdb->query($sql);
  	}
	
  	if((int)$installed_ver < 9) {
  		rsvp_pro_install_passcode_field();
  	}
  
    if((int)$installed_ver < 10) {
      // create the rsvpEvents table
    	$table = $wpdb->prefix."rsvpEvents";
    	if($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table) {
        $sql = "CREATE TABLE `$table` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `eventName` mediumtext NOT NULL,
          `open_date` DATE NOT NULL ,
          `close_date` DATE NOT NULL ,
          `options` TEXT NOT NULL ,
          PRIMARY KEY (`id`)
        );";
        $wpdb->query($sql);
      }
    
      // Add in the association between rsvpEvents and attendees
      $table = $wpdb->prefix."rsvpAttendees";
      $sql = "ALTER TABLE `$table` ADD `rsvpEventID` INT NOT NULL ";
      $wpdb->query($sql);
    
      $table = $wpdb->prefix."rsvpProCustomQuestions";
      $sql = "ALTER TABLE `$table` ADD `rsvpEventID` INT NOT NULL ";
      $wpdb->query($sql);
    
    }
    
    $table = $wpdb->prefix."rsvpAttendees";
    if((int)$installed_ver < 11 || ($wpdb->get_var("SHOW COLUMNS FROM `$table` LIKE 'email'") != "email")) {
  		$sql = "ALTER TABLE ".$table." ADD `email` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;";
  		$wpdb->query($sql);
  		update_option( "rsvp_pro_db_version", RSVP_PRO_DB_VERSION);
    }
    
    $table = $wpdb->prefix."rsvpProCustomQuestions";
    if((int)$installed_ver < 12) {
      $sql = "ALTER TABLE ".$table." ADD `grouping` VARCHAR(30) NOT NULL ;";
      $wpdb->query($sql);
    }
    
    $table = $wpdb->prefix."rsvpAttendees";
    if(((int)$installed_ver < 16) || ($wpdb->get_var("SHOW COLUMNS FROM `$table` LIKE 'numGuests'") != "numGuests")) {
  		$sql = "ALTER TABLE ".$table." ADD `numGuests` INT NULL;";
  		$wpdb->query($sql);
    }
    
    $table = $wpdb->prefix."rsvpEvents";
    if(((int)$installed_ver < 17) || ($wpdb->get_var("SHOW COLUMNS FROM `$table` LIKE 'parentEventID'") != "parentEventID")) {
  		$sql = "ALTER TABLE ".$table." ADD `parentEventID` INT NULL;";
  		$wpdb->query($sql);
    }
    
    $table = $wpdb->prefix."rsvpAttendeeSubEvents";
    if(((int)$installed_ver < 18) || ($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table)) {
  		$sql = "CREATE TABLE ".$table." (
  		`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  		`rsvpDate` DATE NULL ,
  		`rsvpStatus` ENUM( 'Yes', 'No', 'NoResponse' ) NOT NULL DEFAULT 'NoResponse',
  		`kidsMeal` ENUM( 'Y', 'N' ) NOT NULL DEFAULT 'N',
  		`veggieMeal` ENUM( 'Y', 'N' ) NOT NULL DEFAULT 'N', 
      `rsvpEventID` INT NOT NULL, 
      `rsvpAttendeeID` INT NOT NULL
  		);";
  		$wpdb->query($sql);
    }
    
    $table = $wpdb->prefix."rsvpProCustomQuestions";
    if(((int)$installed_ver < 19) || ($wpdb->get_var("SHOW COLUMNS FROM `$table` LIKE 'required'") != "required")) {
  		$sql = "ALTER TABLE ".$table." ADD `required` ENUM( 'Y', 'N' ) NOT NULL DEFAULT 'N';";
  		$wpdb->query($sql);
    }
    
    $table = $wpdb->prefix."rsvpAttendees";
    if(((int)$installed_ver < 20) || ($wpdb->get_var("SHOW COLUMNS FROM `$table` LIKE 'suffix'") != "suffix")) {
  		$sql = "ALTER TABLE ".$table." ADD `suffix` VARCHAR(10) NULL;";
  		$wpdb->query($sql);
    }
    if(((int)$installed_ver < 20) || ($wpdb->get_var("SHOW COLUMNS FROM `$table` LIKE 'salutation'") != "salutation")) {
  		$sql = "ALTER TABLE ".$table." ADD `salutation` VARCHAR(10) NULL;";
  		$wpdb->query($sql);
    }
    
    $table = $wpdb->prefix."rsvpEventAttendees";
    if(((int)$installed_ver < 21) || ($wpdb->get_var("SHOW TABLES LIKE '$table'") != $table)) {
  		$sql = "CREATE TABLE ".$table." (
  		`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
      `rsvpEventID` INT NOT NULL, 
      `rsvpAttendeeID` INT NOT NULL
  		);";
  		$wpdb->query($sql);
    }
    
    $table = $wpdb->prefix."rsvpEvents";
    if(((int)$installed_ver < 22) || ($wpdb->get_var("SHOW COLUMNS FROM `$table` LIKE 'event_access'") != "event_access")) {
  		$sql = "ALTER TABLE ".$table." ADD `event_access` VARCHAR(25) NULL;";
  		$wpdb->query($sql);
    }
    
    if((int)$installed_ver < 23) {
      $table = $wpdb->prefix."rsvpAttendeeAnswers";
      $sql = "ALTER TABLE ".$table." ADD INDEX `attendee_question` (`questionID`, `attendeeID`) COMMENT '';";
      $wpdb->query($sql);
      
      $table = $wpdb->prefix."rsvpAttendees";

      $sql = "ALTER TABLE ".$table." ADD INDEX `rsvpEventId` (`rsvpEventID`) COMMENT '';";
      $wpdb->query($sql);
    }

    if((int)$installed_ver < 24) {
      $table = $wpdb->prefix."rsvpAttendees";

      $sql = "ALTER TABLE ".$table." CHANGE `rsvpStatus` `rsvpStatus` ENUM('Yes','No','NoResponse','Waitlist') NOT NULL DEFAULT 'NoResponse';";
      $wpdb->query($sql);
    }

    if((int)$installed_ver < 25) {
      $table = $wpdb->prefix."rsvpAttendeeSubEvents";

      $sql = "ALTER TABLE ".$table." CHANGE `rsvpStatus` `rsvpStatus` ENUM('Yes','No','NoResponse','Waitlist') NOT NULL DEFAULT 'NoResponse';";
      $wpdb->query($sql);
    }    

    $table = $wpdb->prefix."rsvpProCustomQuestionAnswers";
    if(((int)$installed_ver < 26) || ($wpdb->get_var("SHOW COLUMNS FROM `$table` LIKE 'defaultAnswer'") != "defaultAnswer")) {
      $sql = "ALTER TABLE ".$table." ADD `defaultAnswer` VARCHAR(1) NULL;";
      $wpdb->query($sql);
    }

    $table = $wpdb->prefix."rsvpAttendees";
    if(((int)$installed_ver < 27) || ($wpdb->get_var("SHOW COLUMNS FROM `$table` LIKE 'nicknames'") != "nicknames")) {
      $sql = "ALTER TABLE ".$table." ADD `nicknames` VARCHAR(250) NULL;";
      $wpdb->query($sql);
    }
    
  	update_option( "rsvp_pro_db_version", RSVP_PRO_DB_VERSION);
  }  // function rsvp_pro_update_database($wpdb) {
  
	function rsvp_pro_install_passcode_field() {
		global $wpdb;
		$table = PRO_ATTENDEES_TABLE;
		$sql = "SHOW COLUMNS FROM `$table` LIKE 'passcode'";
		if(!$wpdb->get_results($sql)) {
			$sql = "ALTER TABLE `$table` ADD `passcode` VARCHAR(50) NOT NULL DEFAULT '';";
			$wpdb->query($sql);
		}
	}
?>