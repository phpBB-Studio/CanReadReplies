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

class m1_install_permissions extends \phpbb\db\migration\migration
{
	/**
	 * Assign migration file dependencies for this migration.
	 *
	 * @return array		Array of migration files
	 * @access public
	 * @static
	 */
	public static function depends_on()
	{
		return ['\phpbb\db\migration\data\v32x\v328'];
	}

	/**
	 * Update data stored in the database during extension installation.
	 *
	 * @return array		Array of data update instructions
	 * @access public
	 */
	public function update_data()
	{
		return [
			/* Default NO */

			/* Can edit private replies if can read them */
			['permission.add', ['u_phpbbstudio_crr_edit_reply']],
			['permission.permission_unset', ['REGISTERED', 'u_phpbbstudio_crr_edit_reply', 'group']],

			/* Can read OWN replies */
			['permission.add', ['u_phpbbstudio_crr_read_own_reply']],
			['permission.permission_unset', ['REGISTERED', 'u_phpbbstudio_crr_read_own_reply', 'group']],

			/* Can read ALL replies */
			['permission.add', ['u_phpbbstudio_crr_read_reply']],
			['permission.permission_unset', ['REGISTERED', 'u_phpbbstudio_crr_read_reply', 'group']],

			/* Can set to private reply only own posts */
			['permission.add', ['u_phpbbstudio_crr_set_reply']],
			['permission.permission_unset', ['REGISTERED', 'u_phpbbstudio_crr_set_reply', 'group']],
		];
	}
}
