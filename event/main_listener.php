<?php
/**
 *
 * Can Read Replies. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, phpBB Studio, https://www.phpbbstudio.com
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbstudio\crr\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Can Read Replies Main event listener.
 */
class main_listener implements EventSubscriberInterface
{
	/* @var \phpbb\language\language */
	protected $language;

	/**
	 * Constructor.
	 *
	 * @param \phpbb\language\language    $language    Language object
	 */
	public function __construct(\phpbb\language\language $language)
	{
		$this->language = $language;
	}

	/**
	 * Assign functions defined in this class to event listeners in the core.
	 *
	 * @static
	 * @return array
	 */
	public static function getSubscribedEvents()
	{
		return [
			'core.user_setup_after'          => 'crr_load_language',
			'core.permissions'               => 'crr_add_permissions',
			'core.acp_board_config_edit_add' => 'crr_acp_post_config',
		];
	}

	/**
	 * Load common language files after the user setup.
	 *
	 * @event  core.user_setup_after
	 * @return void
	 */
	public function crr_load_language()
	{
		$this->language->add_lang('crr_common', 'phpbbstudio/crr');
	}

	/**
	 * Add permissions to the ACP -> Permissions settings page.
	 *
	 * @event  core.permissions
	 * @param  \phpbb\event\data    $event    Event object
	 * @return void
	 */
	public function crr_add_permissions(\phpbb\event\data $event)
	{
		$categories = $event['categories'];
		$permissions = $event['permissions'];

		if (empty($categories['phpbb_studio']))
		{
			$categories['phpbb_studio'] = 'ACL_CAT_PHPBB_STUDIO';

			$event['categories'] = $categories;
		}

		$perms = [
			'u_phpbbstudio_crr_edit_reply',
			'u_phpbbstudio_crr_read_own_reply',
			'u_phpbbstudio_crr_read_reply',
			'u_phpbbstudio_crr_set_reply',
		];

		foreach ($perms as $permission)
		{
			$permissions[$permission] = ['lang' => 'ACL_' . utf8_strtoupper($permission), 'cat' => 'phpbb_studio'];
		}

		$event['permissions'] = $permissions;
	}

	/**
	 * Add CRR settings to the ACP.
	 *
	 * @event core.acp_board_config_edit_add
	 * @param  \phpbb\event\data    $event    The event object
	 * @return void
	 */
	public function crr_acp_post_config(\phpbb\event\data $event)
	{
		if ($event['mode'] === 'post' && array_key_exists('legend1', $event['display_vars']['vars']))
		{
			/* Load our language file only if necessary */
			$this->language->add_lang('crr_common', 'phpbbstudio/crr');

			$display_vars = $event['display_vars'];

			/* Set configs */
			$crr_config_vars = [
				'legend_crr'                   => 'CRR_SETTINGS',
				'crr_private_reply_checkbox'   => ['lang' => 'CRR_POSTING_CHECKBOX', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true],
				'crr_private_reply_username'   => ['lang' => 'CRR_PVT_REPLY_USERNAME', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true],
				'crr_private_reply_vtp_hidden' => ['lang' => 'CRR_PVT_REPLY_HIDDEN', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true],
			];

			/* Validate configs */
			$display_vars['vars'] = phpbb_insert_config_array($display_vars['vars'], $crr_config_vars, ['before' => 'legend1']);

			$event['display_vars'] = $display_vars;
		}
	}
}
