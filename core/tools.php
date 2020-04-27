<?php
/**
 *
 * Can Read Replies. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, phpBB Studio, https://www.phpbbstudio.com
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbstudio\crr\core;

/**
 * Can Read Replies helper class.
 */
class tools
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/* @var \phpbb\language\language */
	protected $language;

	/** @var string Posts table */
	protected $posts_table;

	/**
	 * Constructor.
	 *
	 * @param  \phpbb\auth\auth                     $auth          Auth object
	 * @param  \phpbb\db\driver\driver_interface    $db            Database object
	 * @param  \phpbb\language\language             $language      Language object
	 * @param  string                               $posts_tabl    Posts table
	 * @return void
	 */
	public function __construct(
		\phpbb\auth\auth $auth,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\language\language $language,
		string $posts_table
	)
	{
		$this->auth        = $auth;
		$this->db          = $db;
		$this->language    = $language;

		$this->posts_table = $posts_table;
	}

	/**
	 * Finds the author of a post.
	 *
	 * @param  int    $post_id      The post identifier
	 * @return int    $is_author    The Author identifier
	 */
	public function crr_author($post_id)
	{
		if (empty($post_id))
		{
			return false;
		}

		$sql = 'SELECT poster_id
			FROM ' . $this->posts_table . '
			WHERE post_id = ' . (int) $post_id;
		$result = $this->db->sql_query_limit($sql, 1);
		$is_author = $this->db->sql_fetchfield('poster_id');
		$this->db->sql_freeresult($result);

		return (int) $is_author;
	}

	/**
	 * Check if the post is a CRR post (a private reply).
	 *
	 * @param  in    $post_id    The post identifier
	 * @return bool              Whether or not the post is private
	 */
	public function check_private_post($post_id)
	{
		if (empty($post_id))
		{
			return false;
		}

		$sql = 'SELECT crr_post
			FROM ' . $this->posts_table . '
			WHERE post_id = ' . (int) $post_id;
		$result = $this->db->sql_query_limit($sql, 1);
		$is_private = $this->db->sql_fetchfield('crr_post');
		$this->db->sql_freeresult($result);

		return (bool) $is_private;
	}

	/**
	 * Returns the HTML markup for the private message replacement.
	 *
	 * @return string    Formatted HTML with the replacement
	 */
	public function private_tpl()
	{
		return '<div class="post reported">
				<i class="icon fa-user-secret fa-fw icon-red" aria-hidden="true"></i> '
				. $this->language->lang('CRR_PRIVATE_REPLY')
				. ' <i class="icon fa-reply fa-fw icon-red" aria-hidden="true"></i>'
				. '<br><br>'
				. $this->language->lang('CRR_CLASSIFIED_REPLY')
				. '</div>';
	}

	/**
	 * Performs check-in against current user ID.
	 *
	 * @param  int    $poster_id          The poster identifier
	 * @param  int    $current_user_id    The current user identifier
	 * @return bool                       Whether the current user is authorised.
	 */
	public function check_auths($poster_id, $current_user_id)
	{
		return (
			$poster_id != $current_user_id && !$this->auth->acl_get('u_phpbbstudio_crr_read_reply')
			||
			$poster_id == $current_user_id && !$this->auth->acl_get('u_phpbbstudio_crr_read_own_reply')
		);
	}
}
