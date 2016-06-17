<?php

// =============================================
// CONFIGURATIONS
// =============================================

// Authentication
$api_key         = 'bc29271bfca4097bd65ce2e7273cedec-us6'; // Find on your Account Settings > Extras > API Keys
$list_id         = '446a58d6b2'; // Find on your List > Settings

// Validation messages
$error_messages   = array(
	'List_AlreadySubscribed' => 'Este correo electrónico ya está suscrito.',
	'Email_NotExists'        => 'Correo electrónico no válido.',
	'else'                   => 'Ha ocurrido un error.',
);



$success_message = 'Gracias! Lo hemos agregado a nuestra lista de contactos y atenderemos su petición en breve.';

// =============================================
// BEGIN SUBSCRIBE PROCESS
// =============================================

// Form's values
$email = isset( $_REQUEST['email'] ) ? $_REQUEST['email'] : '';


$merge_vars = array('FNAME'=>$_REQUEST["name"], 'LCOMMENT'=>$_REQUEST["comments"], 'LSUBJECT'=>$_REQUEST["subject"], 'PHONE'=>$_REQUEST["phone"], 'COMPANY'=>$_REQUEST["company"]);

// Initiate API object
require_once( '../php/mailchimp/class.mailchimp-api.php' );
$mailchimp = new MailChimp( $api_key );

// Request parameters
$config  = array(
	'id'                => $list_id,
	'email'             => array( 'email' => $email ),
	'merge_vars'        => $merge_vars,
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
		echo $success_message;
	}
}