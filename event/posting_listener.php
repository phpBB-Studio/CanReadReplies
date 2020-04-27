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
 * Can Read Replies Posting event listener.
 */
class posting_listener implements EventSubscriberInterface
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\config\config */
	protected $config;

	/* @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbbstudio\crr\core\tools */
	protected $tools;

	/**
	 * Constructor.
	 *
	 * @param  \phpbb\auth\auth               $auth        Auth object
	 * @param  \phpbb\config\config           $config      Config object
	 * @param  \phpbb\language\language       $language    Language object
	 * @param  \phpbb\request\request         $request     Request object
	 * @param  \phpbb\user                    $user        User object
	 * @param  \phpbbstudio\crr\core\tools    $tools       Custom functions
	 * @return void
	 */
	public function __construct(
		\phpbb\auth\auth $auth,
		\phpbb\config\config $config,
		\phpbb\language\language $language,
		\phpbb\request\request $request,
		\phpbb\user $user,
		\phpbbstudio\crr\core\tools $tools
	)
	{
		$this->auth     = $auth;
		$this->config   = $config;
		$this->language = $language;
		$this->request  = $request;
		$this->user     = $user;
		$this->tools    = $tools;
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
			'core.posting_modify_template_vars'			=> 'crr_posting_modify_template_vars',
			'core.posting_modify_submit_post_before'	=> 'crr_posting_modify_submit_post_before',
			'core.submit_post_modify_sql_data'			=> 'crr_submit_post_modify_sql_data',
			'core.modify_posting_auth'					=> 'crr_modify_posting_auth',
		];
	}

	/**
	 * Display the Can Read Replies checkbox.
	 *
	 * @event  core.posting_modify_template_vars
	 * @param  \phpbb\event\data    $event    The event object
	 * @return void
	 */
	public function crr_posting_modify_template_vars(\phpbb\event\data $event)
	{
		if ($this->common_checks($event['mode'], $event['post_data']))
		{
			$event->update_subarray(
				'page_data',
				'S_CRR_POST',
				isset($event['post_data']['crr_post'])
				? (bool) $event['post_data']['crr_post']
				: (bool) $this->config['crr_private_reply_checkbox']
			);
		}
	}

	/**
	 * Add the Can Read Replies status to the post data.
	 *
	 * @event  core.posting_modify_submit_post_before
	 * @param  \phpbb\event\data    $event    The event object
	 * @return void
	 */
	public function crr_posting_modify_submit_post_before(\phpbb\event\data $event)
	{
		if ($this->common_checks($event['mode'], $event['post_data']))
		{
			$event->update_subarray('data', 'crr_post', $this->request->is_set_post('crr_post'));
		}
	}

	/**
	 * Add the Can Read Replies status to the post's SQL data.
	 *
	 * @event  core.submit_post_modify_sql_data
	 * @param  \phpbb\event\data    $event    The event object
	 * @return void
	 */
	public function crr_submit_post_modify_sql_data(\phpbb\event\data $event)
	{
		if ($event['post_mode'] !== 'post')
		{
			if (isset($event['data']['crr_post']))
			{
				$sql_data = $event['sql_data'];

				$sql_data[POSTS_TABLE]['sql']['crr_post'] = (bool) $event['data']['crr_post'];

				$event['sql_data'] = $sql_data;
			}
		}
	}

	/**
	 * Private replies' management are allowed to who is somehow authorised.
	 *
	 * @event  core.modify_posting_auth
	 * @param  \phpbb\event\data    $event    The event object
	 * @return void
	 */
	public function crr_modify_posting_auth($event)
	{
		if (in_array($event['mode'], ['quote', 'edit', 'delete', 'soft_delete']))
		{
			/* Throw an error if the user is not authorised */
			if (
				$this->tools->check_auths($event['post_data']['poster_id'], $this->user->data['user_id'])
				&&
				$this->tools->check_private_post($event['post_id'])
			)
			{
				trigger_error($this->language->lang('CRR_NO_MANAGE'));
			}
		}
	}

	/**
	 * Perform common checks for Can Read Replies.
	 *
	 * @param  string    $mode    The post mode
	 * @param  array     $data    The post data
	 * @return bool               Whether or not CRR should perform actions
	 */
	protected function common_checks($mode, array $data)
	{
		if (in_array($mode, ['post', 'delete', 'bump', 'smilies', 'popup']))
		{
			return false;
		}
		else if ($mode === 'edit' && $data['topic_first_post_id'] === $data['post_id'])
		{
			return false;
		}
		else if ($mode === 'edit' && !$this->auth->acl_get('u_phpbbstudio_crr_edit_reply'))
		{
			return false;
		}
		else if (!$this->auth->acl_get('u_phpbbstudio_crr_set_reply'))
		{
			return false;
		}

		return true;
	}
}
