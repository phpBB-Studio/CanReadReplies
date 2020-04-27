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
 * Can Read Replies Display event listener.
 */
class display_listener implements EventSubscriberInterface
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/* @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbbstudio\crr\core\tools */
	protected $tools;

	/**
	 * Constructor.
	 *
	 * @param  \phpbb\auth\auth                     $auth        Auth object
	 * @param  \phpbb\config\config                 $config      Config object
	 * @param  \phpbb\db\driver\driver_interface    $db          Database object
	 * @param  \phpbb\language\language             $language    Language object
	 * @param  \phpbb\template\template             $template    Template object
	 * @param  \phpbb\user                          $user        User object
	 * @param  \phpbbstudio\crr\core\tools          $tools       Custom functions
	 * @return void
	 */
	public function __construct(
		\phpbb\auth\auth $auth,
		\phpbb\config\config $config,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\language\language $language,
		\phpbb\template\template $template,
		\phpbb\user $user,
		\phpbbstudio\crr\core\tools $tools
	)
	{
		$this->auth     = $auth;
		$this->config   = $config;
		$this->db       = $db;
		$this->language = $language;
		$this->template = $template;
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
			/* Topic */
			'core.viewtopic_post_rowset_data'           => 'crr_viewtopic_post_rowset_data',
			'core.viewtopic_modify_post_row'            => 'crr_viewtopic_modify_post_row',
			'core.topic_review_modify_row'              => 'crr_topic_review_modify_row',
			/* MCP */
			'core.mcp_topic_review_modify_row'          => 'crr_mcp_topic_review_modify_row',
			'core.mcp_view_forum_modify_topicrow'       => 'crr_viewforum_modify_topicrow',
			'core.modify_mcp_modules_display_option'    => 'crr_modify_mcp_modules_display_option',
			/* Forums */
			'core.display_forums_modify_template_vars'  => 'crr_display_forums_modify_template_vars',
			'core.viewforum_modify_topicrow'            => 'crr_viewforum_modify_topicrow',
			/* Search */
			'core.search_modify_tpl_ary'                => 'crr_search_modify_tpl_ary',
			/* UCP */
			'core.ucp_pm_compose_quotepost_query_after' => 'crr_ucp_pm_compose_quotepost_query_after',
		];
	}

	/**
	 * Update rowset according to user's choice.
	 *
	 * @event	core.viewtopic_post_rowset_data
	 * @param	\phpbb\event\data	$event		The event object
	 * @return	void
	 */
	public function crr_viewtopic_post_rowset_data(\phpbb\event\data $event)
	{
		$event->update_subarray('rowset_data', 'crr_post', (bool) $event['row']['crr_post']);
	}

	/**
	 * Assign private switch to template.
	 *
	 * @event  core.viewtopic_modify_post_row
	 * @param  \phpbb\event\data    $event    The event object
	 * @return void
	 */
	public function crr_viewtopic_modify_post_row(\phpbb\event\data $event)
	{
		/* The event parameters */
		$row      = $event['row'];
		$post_row = $event['post_row'];

		/* Make the post a 'private reply' if the user is not authorised */
		if (
			$this->tools->check_auths($event['poster_id'], $this->user->data['user_id'])
			&&
			$this->tools->check_private_post($row['post_id'])
		)
		{
			/* If the post is 'anonymous' we need to hide some stuff in the mini-profile next to posts */
			if (!$this->config['crr_private_reply_username'])
			{
				$post_row = array_merge($post_row, [
					'POST_AUTHOR_FULL' => $this->language->lang('CRR_POST_STAFF'),
					'RANK_TITLE'       => false,
					'RANK_IMG'         => false,
					'RANK_IMG_SRC'     => false,
					'POSTER_JOINED'    => false,
					'POSTER_POSTS'     => false,
					'POSTER_AVATAR'    => false,
					'POSTER_WARNINGS'  => false,
					'POSTER_AGE'       => false,
					'CONTACT_USER'     => false,
					'U_PM'             => false,
					'U_EMAIL'          => false,
					'U_JABBER'         => false,
					'SIGNATURE'        => false,
					'EDITED_MESSAGE'   => false,
					'EDIT_REASON'      => false,
					'DELETED_MESSAGE'  => false,
					'DELETE_REASON'    => false,
					'BUMPED_MESSAGE'   => false,
				]);

				/* Clear Custom Profiles */
				$event['cp_row'] = [];
			}

			$post_row = array_merge($post_row, [
				'MESSAGE'           => $this->tools->private_tpl(),
				'POST_DATE'         => $this->language->lang('CRR_POST_CLASSIFIED'),
				'U_QUOTE'           => false,
				'U_EDIT'            => false,
				'U_DELETE'          => false,
				'U_INFO'            => false,
				'U_REPORT'          => false,
				'U_WARN'            => false,
				'S_HAS_ATTACHMENTS' => false,
				'S_POST_HIDDEN'     => (bool) $this->config['crr_private_reply_vtp_hidden'],
			]);
		}

		/* Display Red icon to indicate private posts */
		$post_row = array_merge($post_row, [
			'S_CRR_IS_PRIVATE'   => !empty($row['crr_post']) ? $row['crr_post'] : false,
			'S_CRR_CAN_READ'     => (bool) $this->auth->acl_get('u_phpbbstudio_crr_read_reply'),
			'S_CRR_CAN_READ_OWN' => (bool) ($event['poster_id'] == $this->user->data['user_id']) && $this->auth->acl_get('u_phpbbstudio_crr_read_own_reply'),
		]);

		$event['post_row'] = $post_row;
	}

	/**
	 * Don't show results for CRR posts in MCP if not authorized.
	 *
	 * @event  core.mcp_topic_review_modify_row
	 * @param  \phpbb\event\data    $event    The event object
	 * @return void
	 */
	public function crr_mcp_topic_review_modify_row(\phpbb\event\data $event)
	{
		/* The event parameters */
		$post_row = $event['post_row'];

		/* Make the post a 'private reply' if the user is not authorised */
		if (
			$this->tools->check_auths($event['poster_id'], $this->user->data['user_id'])
			&&
			$this->tools->check_private_post($event['row']['post_id'])
		)
		{
			if (!$this->config['crr_private_reply_username'])
			{
				$post_row = array_merge($post_row, [
					'POST_AUTHOR_FULL' => $this->language->lang('CRR_POST_STAFF'),
				]);
			}

			$post_row = array_merge($post_row, [
				'MESSAGE'           => $this->tools->private_tpl(),
				'POST_DATE'         => $this->language->lang('CRR_POST_CLASSIFIED'),
				'S_HAS_ATTACHMENTS' => false,
				'S_CHECKED'         => false,
				'U_POST_DETAILS'    => false,
			]);
		}

		$event['post_row'] = $post_row;
	}

	/**
	 * Don't show modules for CRR posts in MCP if not authorized.
	 *
	 * @event  core.modify_mcp_modules_display_option
	 * @param  \phpbb\event\data    $event    The event object
	 * @return void
	 */
	public function crr_modify_mcp_modules_display_option(\phpbb\event\data $event)
	{
		/* Throw an error if the user is not authorised */
		if (
			$this->tools->check_auths($event['user_id'], $this->user->data['user_id'])
			&&
			$this->tools->check_private_post($event['post_id'])
		)
		{
			if ($event['mode'] === 'warn_post' || $event['mode'] === 'post_details')
			{
				trigger_error($this->language->lang('CRR_NO_MANAGE'));
			}
			else
			{
				/** @var \p_master $module */
				$module = $event['module'];

				$module->set_display('main', 'post_details', false);
				$module->set_display('warn', 'warn_post', false);
			}
		}
	}

	/**
	 * Don't show results for CRR posts if not authorized.
	 *
	 * @event  core.topic_review_modify_row
	 * @param  \phpbb\event\data    $event    The event object
	 * @return void
	 */
	public function crr_topic_review_modify_row(\phpbb\event\data $event)
	{
		if ($event['mode'] === 'post_review' || $event['mode'] === 'topic_review')
		{
			/* Make the post a 'private reply' if the user is not authorised */
			if (
				$this->tools->check_auths($event['row']['user_id'], $this->user->data['user_id'])
				&&
				$this->tools->check_private_post($event['row']['post_id'])
			)
			{
				if (!$this->config['crr_private_reply_username'])
				{
					$event->update_subarray('post_row', 'POST_AUTHOR_FULL', $this->language->lang('CRR_POST_STAFF'));
				}

				$tpl_ary = [
					'MESSAGE'           => $this->tools->private_tpl(),
					'POST_DATE'         => $this->language->lang('CRR_POST_CLASSIFIED'),
					'POSTER_QUOTE'      => false,
					'S_HAS_ATTACHMENTS' => false,
				];

				foreach ($tpl_ary as $key => $value)
				{
					$event->update_subarray('post_row', $key, $value);
				}
			}
		}
	}

	/**
	 * Do not show last post subject in topic row if not authorized.
	 *
	 * @event  core.viewforum_modify_topicrow
	 * @param  \phpbb\event\data    $event    The event object
	 * @return void
	 */
	public function crr_viewforum_modify_topicrow(\phpbb\event\data $event)
	{
		/* Make the post a 'private reply' if the user is not FULL authorised */
		if (
			$this->tools->check_auths($this->tools->crr_author($event['row']['topic_last_post_id']), $this->user->data['user_id'])
			&&
			$this->tools->check_private_post($event['row']['topic_last_post_id'])
		)
		{
			if (!$this->config['crr_private_reply_username'])
			{
				$event->update_subarray('topic_row', 'LAST_POST_AUTHOR_FULL', $this->language->lang('CRR_POST_STAFF'));
			}

			$event->update_subarray('topic_row', 'LAST_POST_TIME', $this->language->lang('CRR_POST_CLASSIFIED'));
		}
	}

	/**
	 * Do not show last post subject in viewforum if not authorized.
	 *
	 * @event  core.display_forums_modify_template_vars
	 * @param  \phpbb\event\data    $event    The event object
	 * @return void
	 */
	public function crr_display_forums_modify_template_vars(\phpbb\event\data $event)
	{
		/* Make the post a 'private reply' if the user is not FULL authorised */
		if (
			$this->tools->check_auths($this->tools->crr_author($event['row']['forum_last_post_id']), $this->user->data['user_id'])
			&&
			$this->tools->check_private_post($event['row']['forum_last_post_id'])
		)
		{
			if (!$this->config['crr_private_reply_username'])
			{
				$event->update_subarray('forum_row', 'LAST_POSTER_FULL', $this->language->lang('CRR_POST_STAFF'));
			}

			$tpl_ary = [
				'LAST_POST_SUBJECT'           => $this->language->lang('CRR_POST'),
				'LAST_POST_SUBJECT_TRUNCATED' => $this->language->lang('CRR_POST'),
				'LAST_POST_TIME'              => $this->language->lang('CRR_POST_CLASSIFIED'),
			];

			foreach ($tpl_ary as $key => $value)
			{
				$event->update_subarray('forum_row', $key, $value);
			}
		}
	}

	/**
	 * Don't show results for CRR posts if not authorized.
	 * Advanced search facility.
	 *
	 * @event  core.search_modify_tpl_ary
	 * @param  \phpbb\event\data    $event    The event object
	 * @return void
	 */
	public function crr_search_modify_tpl_ary(\phpbb\event\data $event)
	{
		if ($event['show_results'] === 'posts')
		{
			/* Make the post a 'private reply' when searching, if the user is not authorised */
			if (
				$this->tools->check_auths($event['row']['poster_id'], $this->user->data['user_id'])
				&&
				$this->tools->check_private_post($event['row']['post_id'])
			)
			{
				if (!$this->config['crr_private_reply_username'])
				{
					$event->update_subarray('tpl_ary', 'POST_AUTHOR_FULL', $this->language->lang('CRR_POST_STAFF'));
				}

				$tpl_array = [
					'MESSAGE'   => $this->tools->private_tpl(),
					'POST_DATE' => $this->language->lang('CRR_POST_CLASSIFIED'),
				];

				foreach ($tpl_array as $key => $value)
				{
					$event->update_subarray('tpl_ary', $key, $value);
				}
			}
		}

		if ($event['show_results'] === 'topics')
		{
			/* Make the last post information as a 'private reply' when searching, if the user is not authorised */
			if (
				$this->tools->check_auths($event['row']['topic_last_poster_id'], $this->user->data['user_id'])
				&&
				$this->tools->check_private_post($event['row']['topic_last_post_id'])
			)
			{
				$tpl_array = [
					'LAST_POST_TIME'        => $this->language->lang('CRR_POST_CLASSIFIED'),
					'LAST_POST_AUTHOR_FULL' => $this->language->lang('CRR_POST_STAFF'),
				];

				foreach ($tpl_array as $key => $value)
				{
					$event->update_subarray('tpl_ary', $key, $value);
				}
			}
		}
	}

	/**
	 * Don't use PM on CRR posts if not authorized.
	 *
	 * @event  core.ucp_pm_compose_quotepost_query_after
	 * @param  \phpbb\event\data    $event    The event object
	 * @return void
	 */
	public function crr_ucp_pm_compose_quotepost_query_after(\phpbb\event\data $event)
	{
		/* Do not PM a 'private reply' if the user is not authorised */
		if (
			$this->tools->check_auths($this->tools->crr_author($event['msg_id']), $this->user->data['user_id'])
			&&
			$this->tools->check_private_post($event['msg_id'])
		)
		{
			if (in_array($event['action'], ['quote', 'quotepost', 'forward']))
			{
				trigger_error($this->language->lang('CRR_NO_MANAGE'));
			}
		}
	}
}
