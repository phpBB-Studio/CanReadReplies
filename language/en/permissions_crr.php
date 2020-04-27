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
	'ACL_CAT_PHPBB_STUDIO'	=> 'phpBB Studio',

	'ACL_U_PHPBBSTUDIO_CRR_EDIT_REPLY'     => '<strong>CRR</strong> - Can edit the private status of replies',
	'ACL_U_PHPBBSTUDIO_CRR_READ_OWN_REPLY' => '<strong>CRR</strong> - Can read <em>own</em> private replies',
	'ACL_U_PHPBBSTUDIO_CRR_READ_REPLY'     => '<strong>CRR</strong> - Can read <strong>all</strong> private replies',
	'ACL_U_PHPBBSTUDIO_CRR_SET_REPLY'      => '<strong>CRR</strong> - Can set <em>own</em> replies as private',
]);
