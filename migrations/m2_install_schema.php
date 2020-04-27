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

class m2_install_schema extends \phpbb\db\migration\migration
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
	 * Add the CRR extension schema to the database.
	 *
	 * @return array		Array of table schema
	 * @access public
	 */
	public function update_schema()
	{
		return [
			'add_columns'	=> [
				$this->table_prefix . 'posts' => [
					'crr_post'			=>	['BOOL', 0],
				],
			],
		];
	}

	/**
	 * Drop the CRR schema from the database.
	 *
	 * @return array		Array of table schema
	 * @access public
	 */
	public function revert_schema()
	{
		return [
			'drop_columns'	=> [
				$this->table_prefix . 'posts'	=> [
					'crr_post',
				],
			],
		];
	}
}
