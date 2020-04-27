<?php
/**
 *
 * Can Read Replies. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, phpBB Studio, https://www.phpbbstudio.com
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbstudio\crr\migrations;

class m4_update_configuration extends \phpbb\db\migration\migration
{
	/**
	 * Assign migration file dependencies for this migration.
	 *
	 * @return array		Array of migration files
	 * @access public
	 * @static
	 */
	static public function depends_on()
	{
		return ['\phpbbstudio\crr\migrations\m3_install_configuration'];
	}

	/**
	 * Update or delete data stored in the database during extension installation.
	 *
	 * @return array		Array of data update instructions.
	 * @access public
	 */
	public function update_data()
	{
		return [
			['config.add', ['crr_private_reply_vtp_hidden', 0]],
		];
	}
}
