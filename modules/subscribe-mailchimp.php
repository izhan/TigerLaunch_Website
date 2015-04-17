<?php

// =============================================
// CONFIGURATIONS
// =============================================

// Authentication
$api_key         = '42d4ed2d8f3437382f8fd8bcafdc9ef6-us9'; // Find on your Account Settings > Extras > API Keys
$list_id         = 'c5b930de81'; // Find on your List > Settings

// Validation messages
$error_messages   = array(
	'List_AlreadySubscribed' => 'The email you entered is already subscribed.',
	'Email_NotExists'        => 'The email you entered is invalid.',
	'else'                   => 'An error occurred.',
);
$success_message = 'Success!';

// =============================================
// BEGIN SUBSCRIBE PROCESS
// =============================================

// Form's values
$email = $_REQUEST['email'];

// Initiate API object
require_once( 'class.mailchimp-api.php' );
$mailchimp = new MailChimp( $api_key );

// Request parameters
$config  = array(
	'id'                => $list_id,
	'email'             => array( 'email' => $email ),
	'merge_vars'        => NULL,
	'email_type'        => 'html',
	'double_optin'      => true,
	'update_existing'   => false,
	'replace_interests' => true,
	'send_welcome'      => false,
);

// Send request
// http://apidocs.mailchimp.com/api/2.0/lists/subscribe.php
$result = $mailchimp->call( 'lists/subscribe', $config );

if ( array_key_exists( 'status', $result ) && $result['status'] == 'error' ) {
	// If error occurs
	$result['message'] = array_key_exists( $result['name'], $error_messages ) ? $error_messages[ $result['name'] ] : $error_messages['else'];
} else {
	// If success
	$result['message'] = $success_message;
}

// Send output
if ( ! empty( $_REQUEST[ 'ajax' ] ) ) {
	// called via AJAX
	echo json_encode( $result );
} else {
	// no AJAX
	if ( array_key_exists( 'status', $result ) && $result['status'] == 'error' ) {
		echo 'Error: ' . $result['error'];
	} else {
		echo 'Success';
	}
}