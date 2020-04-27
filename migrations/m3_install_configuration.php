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

class m3_install_configuration extends \phpbb\db\migration\migration
{
	/**
	 * Check if the migration is effectively installed (entirely optional).
	 *
	 * @return bool			True if this migration is installed, false otherwise.
	 * @access public
	 */
	public function effectively_installed()
	{
		return isset($this->config['crr_private_reply_checkbox']);
	}

	/**
	 * Assign migration file dependencies for this migration.
	 *
	 * @return array		Array of migration files
	 * @access public
	 * @static
	 */
	static public function depends_on()
	{
		return ['\phpbb\db\migration\data\v32x\v328'];
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
			['config.add', ['crr_private_reply_checkbox', 0]],
			['config.add', ['crr_private_reply_username', 0]],
		];
	}
}
