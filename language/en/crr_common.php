<?php
/**
 *
 * Can Read Replies. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, phpBB Studio, https://www.phpbbstudio.com
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = [];
}

/*
 * Some characters you may want to copy&paste:
 * ’ » “ ” …
 */
$lang = array_merge($lang, [
	// ACP
	'CRR_SETTINGS'						=> 'phpBB Studio - Can Read replies',

	'CRR_POSTING_CHECKBOX'				=> 'Privacy checkbox',
	'CRR_POSTING_CHECKBOX_EXPLAIN'		=> 'Whether or not we want users to have the checkbox <strong>“checked”</strong> by default',
	'CRR_PVT_REPLY_USERNAME'			=> 'Privacy username',
	'CRR_PVT_REPLY_USERNAME_EXPLAIN'	=> 'Whether or not we want the author username of private replies to be shown by default',
	'CRR_PVT_REPLY_HIDDEN'				=> 'Collapse replies',
	'CRR_PVT_REPLY_HIDDEN_EXPLAIN'		=> 'Whether or not we want the private replies to be collapsed by default in view topic',

	// Front side
	'CRR_POST'				=> 'Private reply',
	'CRR_POST_EXPLAIN'		=> '<em>(Will be visible only to authorised people)</em>',

	'CRR_PRIVATE_REPLY'		=> 'This is a private reply',
	'CRR_CLASSIFIED_REPLY'	=> 'The content of this post is classified.',
	'CRR_CLASSIFIED_BY'		=> 'Author',
	'CRR_POST_STAFF'		=> '<strong>Staff</strong>',
	'CRR_POST_CLASSIFIED'	=> '<em>- Classified -</em>',

	// Errors
	'CRR_NO_MANAGE'			=> 'Ooops! You are not authorised to manage private replies!',
]);
