<?php
/**
 *
 * Can Read Replies. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, phpBB Studio, https://www.phpbbstudio.com
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbstudio\crr;

/**
 * Can Read Replies Extension base.
 */
class ext extends \phpbb\extension\base
{
	/**
	 * Check whether this extension can be enabled.
	 *
	 * @return bool
	 */
	public function is_enableable()
	{
		if (!(phpbb_version_compare(PHPBB_VERSION, '3.2.8', '>=') && phpbb_version_compare(PHPBB_VERSION, '4.0.0@dev', '<')))
		{
			if (phpbb_version_compare(PHPBB_VERSION, '3.3.0@dev', '<'))
			{
				$user = $this->container->get('user');
				$user->add_lang_ext('phpbbstudio/crr', 'ext_require');

				$lang = $user->lang;

				$lang['EXTENSION_NOT_ENABLEABLE'] .= '<br>' . $user->lang('ERROR_PHPBB_VERSION', '3.2.8', '4.0.0@dev');

				$user->lang = $lang;

				return false;
			}

			if (phpbb_version_compare(PHPBB_VERSION, '3.3.0@dev', '>'))
			{
				$language= $this->container->get('language');
				$language->add_lang('ext_require', 'phpbbstudio/crr');

				return $language->lang('ERROR_PHPBB_VERSION', '3.2.8', '4.0.0@dev');
			}
		}

		return true;
	}
}
