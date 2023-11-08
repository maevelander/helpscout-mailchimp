<?php

include 'helpscout-apps/src/HelpScoutApp/DynamicApp.php';
include 'mailchimp-api/src/MailChimp.php';

use HelpScoutApp\DynamicApp;
use DrewM\MailChimp;

// Update these API keys with your own

$hs_app = new HelpScoutApp\DynamicApp( 'YOUR-HELPSCOUT-SECRET-KEY' );
$mc     = new MailChimp\MailChimp( 'YOUR-MAILCHIMP-API-KEY' );

// Check the Help Scout signature, then fetch the customers details from Mailchimp Profile

if ( $hs_app->isSignatureValid() ) {
	$customer = $hs_app->getCustomer();

	$list_id = 'YOUR-MAILCHIMP-LIST-ID'; // Update this list ID with your own

	$email = $customer->getEmail();
	
	$hash = $mc->subscriberHash( $email );
	$data = $mc->get( "lists/$list_id/members/$hash" );

// Fetch profile field data from Mailchimp. Replace these with your own fields 

	$username			= ( $data['merge_fields']['UNAME'] ) ? $data['merge_fields']['UNAME'] : 'No Data to Show';
	$aid				= ( $data['merge_fields']['AID'] ) ? $data['merge_fields']['AID'] : 'No Data to Show';
	$location           = ( $data['merge_fields']['LOCATION'] ) ? $data['merge_fields']['LOCATION'] : 'No Data to Show';
	$language           = ( $data['merge_fields']['LANGUAGE'] ) ? $data['merge_fields']['LANGUAGE'] : 'No Data to Show';
	$last_activity		= ( $data['merge_fields']['LACTIVITY'] ) ? $data['merge_fields']['LACTIVITY'] : 'No Data to Show';
	$console			= ( $data['merge_fields']['CTYPE'] ) ? $data['merge_fields']['CTYPE'] : 'No Data to Show';
	$os					= ( $data['merge_fields']['PLATFORM'] ) ? $data['merge_fields']['PLATFORM'] : 'No Data to Show';

// Mailchimp stores date in US format so lets convert to AU format

	if ( $last_activity == 'No Data to Show' ) {
		$formatted_au_date = 'No Data to Show';
	} else {
		$au_date           = new DateTime( $last_activity );
		$formatted_au_date = $au_date->format( "d/m/Y" );
	}

// Display the profile in Help Scout Sidebar with some basic styling 

	$html = array(
		'<p>Username</p>',
		'<h4>' . $username . '</h4>',
		'<p>Activision ID</p>',
		'<h4>' . $aid . '</h4>',
		'<p>Location</p>',
		'<h4>' . $location . '</h4>',
		'<p>Language</p>',
		'<h4>' . $language . '</h4>',
		'<p>Last Activity</p>',
		'<h4>' . $formatted_au_date . '</h4>',
		'<p>Console</p>',
		'<h4>' . $console . '</h4>',
		'<p>Operating System</p>',
		'<h4>' . $os . '</h4>'
	);


	echo $hs_app->getResponse( $html );
}
