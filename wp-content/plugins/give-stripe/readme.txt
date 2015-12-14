=== Give - Stripe Gateway ===
Contributors: wordimpress, dlocc, webdevmattcrom
Tags: donations, donation, ecommerce, e-commerce, fundraising, fundraiser, paymill, gateway
Requires at least: 3.8
Tested up to: 4.3.2
Stable tag: 1.2
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Stripe Gateway Add-on for Give

== Description ==

This plugin requires the Give plugin activated to function properly. When activated, it adds a payment gateway for stripe.com.

== Installation ==

= Minimum Requirements =

* WordPress 3.8 or greater
* PHP version 5.3 or greater
* MySQL version 5.0 or greater
* Some payment gateways require fsockopen support (for IPN access)

= Automatic installation =

Automatic installation is the easiest option as WordPress handles the file transfers itself and you don't need to leave your web browser. To do an automatic install of Give, log in to your WordPress dashboard, navigate to the Plugins menu and click Add New.

In the search field type "Give" and click Search Plugins. Once you have found the plugin you can view details about it such as the the point release, rating and description. Most importantly of course, you can install it by simply clicking "Install Now".

= Manual installation =

The manual installation method involves downloading our donation plugin and uploading it to your server via your favorite FTP application. The WordPress codex contains [instructions on how to do this here](http://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).

= Updating =

Automatic updates should work like a charm; as always though, ensure you backup your site just in case.

== Changelog ==

= 1.2 =
* Fix: Preapproved Stripe payments updated to properly show buttons within the Transactions' "Preapproval" column
* Fix: Increased statement_descriptor value limit from 15 to 22 characters

= 1.1 =
* New: Plugin activation banner with links to important links such as support, docs, and settings
* New: CC expiration field updated to be a singular field rather than two select fields
* Improved code organization and inline documentation
* Improved admin donation form validation
* Improved i18n (internationalization)
* Fix: Bug with Credit Cards with an expiration date more than 10 years
* Fix: Remove unsupported characters from statement_descriptor.
* Fix: Error refunding charges directly from within the transaction "Update Payment" modal

= 1.0 =
* Initial plugin release. Yippee!
