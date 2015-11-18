<?php
/*
Plugin Name: Disable Theme and Plugin Editor
Plugin URI: http://wordpress.org/plugins/disable-theme-and-plugin-editor/
Description: Disable Theme and Plugin Editors from WordPress Admin Panel for security reasons 
Author: Farzad Setoode
Version: 1.1
License: GPLv2
*/

if ( !defined( 'DISALLOW_FILE_EDIT' ) ) {
    define( 'DISALLOW_FILE_EDIT', TRUE );
}
?>