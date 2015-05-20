<?php
/**
*
* @package phpBB3
* @version $Id$
* @copyright (c) 2015 Vinny
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
include($phpbb_root_path . 'includes/functions_posting.' . $phpEx);
include($phpbb_root_path . 'includes/functions_user.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('mods/info_acp_video');

$video_id	= request_var('id', 0);

if (!$auth->acl_get('u_video_view_full'))
{
	trigger_error($user->lang['UNAUTHED']);
}

if (!$auth->acl_get('u_video_view'))
{
	trigger_error($user->lang['VIDEO_UNAUTHED']);
}

if (!$config['google_api_key'])
{	
	if ($auth->acl_get('a_'))
	{
		trigger_error($user->lang['NO_KEY_ADMIN']);
	}
	else
	{
		trigger_error($user->lang['NO_KEY_USER']);
	}
}

// Update video view... but only for humans
if (isset($user->data['session_page']) && !$user->data['is_bot'])
{
	$sql = 'UPDATE ' . VIDEO_TABLE . '
		SET video_views = video_views + 1
			WHERE video_id = ' . $video_id;
	$db->sql_query($sql);
}

$template->assign_block_vars('navlinks', array(
	'FORUM_NAME' 	=> ($user->lang['VIDEO_INDEX']),
	'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}video/index.$phpEx"),	
));
	
$sql = 'SELECT v.*, u.*
	FROM ' . VIDEO_TABLE . ' v, ' . USERS_TABLE . ' u
	WHERE v.video_id = ' . (int) $video_id . '
		AND u.user_id = v.user_id
		ORDER BY v.video_id DESC';
$result = $db->sql_query($sql);
$row = $db->sql_fetchrow($result);
$db->sql_freeresult($result);

if (!$row)
{
	$meta_info = append_sid("{$phpbb_root_path}video/index.{$phpEx}");
	meta_refresh(3, $meta_info);
	trigger_error('INVALID_VIDEO');
}

$page_title 	= $row['video_title'];
$user_id 		= $row['user_id'];
$flash_status	= $config['allow_post_flash'] ? true : false;
$delete_allowed = ($auth->acl_get('a_') or $auth->acl_get('m_') || ($user->data['is_registered'] && $user->data['user_id'] == $row['user_id'] && $auth->acl_get('u_video_delete')));

$template->assign_vars(array(
	'VIDEO_ID'			=> censor_text($row['video_id']),
	'VIDEO_TITLE'		=> censor_text($row['video_title']),
	'VIDEO_VIEWS'		=> $row['video_views'],
	'USERNAME'			=> get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
	'YOUTUBE_ID'		=> censor_text($row['youtube_id']),
	'VIDEO_TIME'		=> $user->format_date($row['create_time']),
	'VIDEO_LINK' 		=> generate_board_url() . "/video/viewvideo.$phpEx?" . 'id=' . $row['video_id'],
	'VIDEO_LINK_FLASH'	=> 'http://www.youtube.com/v/' . $row['youtube_id'],
	'YOUTUBE_VIDEO'		=> 'https://www.youtube.com/watch?v=' . $row['youtube_id'],
	'S_BBCODE_FLASH'	=> $flash_status,
	'FLASH_STATUS'		=> ($flash_status) ? $user->lang['FLASH_IS_ON'] : $user->lang['FLASH_IS_OFF'],
	'S_VIDEO_WIDTH'		=> $config['video_width'],
	'S_VIDEO_HEIGHT'	=> $config['video_height'],
	'U_DELETE'			=> append_sid("{$phpbb_root_path}video/posting.$phpEx", 'mode=delete&amp;id=' . $row['video_id']),
	'S_DELETE_ALLOWED'	=> $delete_allowed,
	'U_USER_VIDEOS' 	=> append_sid(generate_board_url() ."/video/search.$phpEx", 'search_id=v&amp;u=' . $row['user_id']),
));

// Count the videos user video ...
$sql = 'SELECT COUNT(video_id) AS total_videos FROM ' . VIDEO_TABLE . ' WHERE user_id = ' . (int) $user_id;
$result = $db->sql_query($sql);
$total_videos = (int) $db->sql_fetchfield('total_videos');
$db->sql_freeresult($result);

$template->assign_vars(array(
	'TOTAL_VIDEOS'		=> $total_videos,
));

$template->assign_block_vars('navlinks', array(
	'FORUM_NAME' 	=> ($user->lang['VIEW_VIDEO'] . ' - ' . $page_title),
));

// Output page
page_header($user->lang['VIEW_VIDEO'] . ' - ' . $page_title, false);

$template->set_filenames(array(
	'body' => 'video/video_view.html')
);

page_footer();

?>