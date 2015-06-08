<?php
/**
 *
 * @author _Vinny_ (http://www.suportephpbb.com.br/) vinnykun@hotmail.com
 * @version $Id$
 * @copyright (c) 2015
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

/**
 * @ignore
 */
define('UMIL_AUTO', true);
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);

include($phpbb_root_path . 'common.' . $phpEx);
$user->session_begin();
$auth->acl($user->data);
$user->setup();


if (!file_exists($phpbb_root_path . 'umil/umil_auto.' . $phpEx))
{
	trigger_error('Please download the latest UMIL (Unified MOD Install Library) from: <a href="http://www.phpbb.com/mods/umil/">phpBB.com/mods/umil</a>', E_USER_ERROR);
}

// The name of the mod to be displayed during installation.
$mod_name = 'VIDEO_INDEX';

/*
* The name of the config variable which will hold the currently installed version
* UMIL will handle checking, setting, and updating the version itself.
*/
$version_config_name = 'video_version';


// The language file which will be included when installing
$language_file = 'mods/info_acp_video';

/*
* The array of versions and actions within each.
* You do not need to order it a specific way (it will be sorted automatically), however, you must enter every version, even if no actions are done for it.
*
* You must use correct version numbering.  Unless you know exactly what you can use, only use X.X.X (replacing X with an integer).
* The version numbering must otherwise be compatible with the version_compare function - http://php.net/manual/en/function.version-compare.php
*/
$versions = array(
	'1.0.0' => array(
		'config_add' => array(
			array('enable_video', true),
			array('enable_video_share', true),
			array('video_width', '640'),
			array('video_height', '390'),
		),

		// Alright, now lets add some modules to the ACP
		'module_add' => array(
			// First, lets add a new category named ACP_CAT_TEST_MOD to ACP_CAT_DOT_MODS
			array('acp', 'ACP_CAT_DOT_MODS', 'ACP_VIDEO'),

			// Now we will add the settings and features modes from the acp_board module to the ACP_CAT_TEST_MOD category using the "automatic" method.
			array('acp', 'ACP_VIDEO', array(
					'module_basename'		=> 'video',
					'modes'					=> array('cat', 'settings'),
				),
			),
		),

		// Now add the table
		'table_add' => array(
			array(VIDEO_TABLE, array(
				'COLUMNS' => array(
					'video_id'		=> array('UINT:11', NULL, 'auto_increment'),
					'video_url'		=> array('VCHAR:255', ''),
					'video_title'	=> array('VCHAR:255', ''),
					'video_cat_id'	=> array('UINT', 0),
					'username'		=> array('VCHAR:255', ''),
					'user_id'		=> array('VCHAR:255', ''),
					'youtube_id'	=> array('VCHAR:255', ''),
					'create_time'	=> array('TIMESTAMP', 0),
				),
				'PRIMARY_KEY'	=> 'video_id',
			)),
			array(VIDEO_CAT_TABLE, array(
				'COLUMNS' => array(
					'video_cat_id'		=> array('UINT:11', NULL, 'auto_increment'),
					'video_cat_title'	=> array('VCHAR:255', ''),
				),
				'PRIMARY_KEY'	=> 'video_cat_id',
			)),
		),

		'table_insert'	=> array(
			array(VIDEO_CAT_TABLE, array(
				array(
					'video_cat_id'		=> 1,
					'video_cat_title'	=> 'Uncategorized',
					),
				)),
		),
	),

	'1.0.1' => array(
	// Lets remove a config setting
	'config_remove' => array(
		array('enable_video'),
		array('enable_video_share'),
		),

	// Lets add a new column to the phpbb_videos and topics table
		'table_column_add' => array(
			array(VIDEO_TABLE, 'video_views', array('MTEXT_UNI', '')),
		),

		'permission_add' => array(
			array('u_video_view_full',	true),
			array('u_video_view',		true),
			array('u_video_delete',		true),
			array('u_video_post',		true),
		),
		'permission_set' => array(
			array('REGISTERED', 
				array('u_video_view_full',
						'u_video_view',
						'u_video_post',
					),
				'group',
			),
		),
	),

	'1.0.2' => array(
		'config_add' => array(
			array('google_api_key'),
			array('videos_per_page', '10'),
		),
	),

	'1.0.3' => array(
		'config_add' => array(
			array('enable_video_comments', true),
			array('comments_per_page', '10'),
		),

		// Now add the table
		'table_add' => array(
			array(VIDEO_CMNTS_TABLE, array(
				'COLUMNS' => array(
					'cmnt_id'			=> array('UINT', NULL, 'auto_increment'),
					'cmnt_video_id'		=> array('UINT', 0),
					'cmnt_poster_id'	=> array('UINT', 0),
					'cmnt_text'			=> array('TEXT_UNI', ''),
					'create_time'		=> array('TIMESTAMP', 0),
					'bbcode_uid'		=> array('VCHAR:8', ''),
					'bbcode_bitfield'	=> array('VCHAR:255', ''),
					'bbcode_options'	=> array('UINT', 0),
				),
				'PRIMARY_KEY'	=> 'cmnt_id',
			)),
		),
	),

	'1.0.4' => array(
		'config_add' => array(
			array('enable_comments'),
			array('comments_per_page', '10'),
		),

		'permission_add' => array(
			array('u_video_comment',	true),
			array('u_video_comment_delete',		true),
		),
	),
);

// Include the UMIL Auto file, it handles the rest
include($phpbb_root_path . 'umil/umil_auto.' . $phpEx);

?>