<?php
/**
 * Cogent Error Mailer
 *
 * @author    Shawn Dean <sdean@cogentco.com>
 * @package   cogent/errormail
 */

return [

	/*
	|--------------------------------------------------------------------------
	| Enable emailing errors
	|--------------------------------------------------------------------------
	|
	| Should we email error traces?
	|
	*/
	'enabled' => true,

	/*
	|--------------------------------------------------------------------------
	| Force emailing
	|--------------------------------------------------------------------------
	|
	| Should we force emails, even when debugging is enabled?
	|
	*/
	'force' => true,

	/*
	|--------------------------------------------------------------------------
	| Error email recipients
	|--------------------------------------------------------------------------
	|
	| Email stack traces to these addresses.
	|
	| For a single recipient, the format can just be
	|   'to' => [ 'address' => 'janedoe@cogentco.com', 'name' => 'Jane Doe' ],
	|
	| For multiple recipients, just specify an array of individual recipients:
	|   'to' =>
	|   [
	|      [ 'address' => 'janedoe@cogentco.com', 'name' => 'Jane Doe' ],
	|      [ 'address' => 'johndoe@cogentco.com', 'name' => 'John Doe' ],
	|   ],
	|
	*/
	'to' => [ ],

	/*
	|--------------------------------------------------------------------------
	| Email error templates
	|--------------------------------------------------------------------------
	|
	| Specify the subject and body templates to use for the email.
	|
	*/
	'subjectTemplate' => 'errormail::email.subject',
	'htmlTemplate'    => 'errormail::email.html',
	'plainTemplate'   => 'errormail::email.plain',

	/*
	|--------------------------------------------------------------------------
	| Date/Time Formatting
	|--------------------------------------------------------------------------
	|
	| Customize the format of the date/time displayed on the email.
	|
	*/
	'dateFormat' => 'Y-m-d H:i:s.u',

	/*
	|--------------------------------------------------------------------------
	| Exceptions
	|--------------------------------------------------------------------------
	|
	| List of fully qualified class name exceptions to NOT send emails.
	|
	*/
    'ignoreExceptions' =>
	[
		'Symfony\Component\HttpKernel\Exception\NotFoundHttpException',
	],

	/*
	|--------------------------------------------------------------------------
	| Exception Throttling
	|--------------------------------------------------------------------------
	|
	| A throller exists to prevent the same exceptions from being emailed over and over.
	|
	| Two configuration options available:
	|  1) The number of seconds that pass since the last exception of the
	|     same type before the exception will be emailed to recipients.
	|     ( enter -1 to disable throttling )
	|
	|  2) The path to store the JSON information needed by the throller.
	|
	*/
	'throttleAge' => -1,
	'storagePath' => storage_path('meta/errormail.json'),

];