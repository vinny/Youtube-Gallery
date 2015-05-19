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

if (!$auth->acl_get('u_video_view_full'))
{
	trigger_error($user->lang['UNAUTHED']);
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

// Initial var setup
$video_id	= request_var('id', 0);
$video_cat_id = request_var('cid', 0);

$sql_start = request_var('start', 0);
$sql_limit = request_var('limit', $config['videos_per_page']);

$template->assign_block_vars('navlinks', array(
	'FORUM_NAME' 	=> ($user->lang['VIDEO_INDEX']),
	'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}video/index.$phpEx"),	
));

$pagination_url = append_sid("{$phpbb_root_path}video/viewcategory.$phpEx", "cid=$video_cat_id");

$sql_ary = array(
	'SELECT'	=> 'v.*, ct.video_cat_id, u.username,u.user_colour,u.user_id',
	'FROM'		=> array(
		VIDEO_TABLE			=> 'v',
		VIDEO_CAT_TABLE		=> 'ct',
		USERS_TABLE			=> 'u',
	),
	'WHERE'		=> 'v.video_cat_id = ' . (int) $video_cat_id . ' AND ct.video_cat_id = ' . (int) $video_cat_id . ' AND u.user_id = v.user_id',
	'ORDER_BY'	=> 'v.video_id DESC',
);
$sql = $db->sql_build_query('SELECT', $sql_ary);
$result = $db->sql_query_limit($sql, $sql_limit, $sql_start);
	
while ($row = $db->sql_fetchrow($result))
{
	$template->assign_block_vars('video', array(
		'VIDEO_TITLE'		=> $row['video_title'],
		'VIDEO_VIEWS'		=> $row['video_views'],
		'U_CAT'				=> append_sid("{$phpbb_root_path}video/viewcategory.$phpEx", 'cid=' . $row['video_cat_id']),
		'VIDEO_TIME'		=> $user->format_date($row['create_time']),
		'VIDEO_ID'			=> censor_text($row['video_id']),
		'U_VIEW_VIDEO'		=> append_sid("{$phpbb_root_path}video/viewvideo.$phpEx", 'id=' . $row['video_id']),
		'U_POSTER'			=> append_sid("{$phpbb_root_path}memberlist.$phpEx", array('mode' => 'viewprofile', 'u' => $row['user_id'])),
		'USERNAME'			=> get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
		'S_VIDEO_THUMBNAIL'	=> 'http://img.youtube.com/vi/' . censor_text($row['youtube_id']) . '/default.jpg'
	));
}
$db->sql_freeresult($result);

// We need another query for the video count
$sql = 'SELECT COUNT(*) as video_count FROM ' . VIDEO_TABLE . ' WHERE video_cat_id = ' . (int)$video_cat_id;
$result = $db->sql_query($sql);
$videorow['video_count'] = $db->sql_fetchfield('video_count');
$db->sql_freeresult($result);

//Start pagination
$template->assign_vars(array(
	'PAGINATION'		=> generate_pagination($pagination_url, $videorow['video_count'], $sql_limit, $sql_start),
	'PAGE_NUMBER'		=> on_page($videorow['video_count'], $sql_limit, $sql_start),
	'TOTAL_VIDEOS'		=> ($videorow['video_count'] == 1) ? $user->lang['LIST_VIDEO'] : sprintf($user->lang['LIST_VIDEOS'], $videorow['video_count']),
));
//End pagination

$sql = 'SELECT * FROM ' . VIDEO_CAT_TABLE . ' WHERE video_cat_id = ' . (int) $video_cat_id;
$result = $db->sql_query($sql);
$row = $db->sql_fetchrow($result);
$db->sql_freeresult($result);
$template->assign_vars(array(
	'CAT_NAME'			=> $row['video_cat_title'],
));

$l_title = ($user->lang['VIEW_CAT'] . ' - ' . $row['video_cat_title']);

$template->assign_block_vars('navlinks', array(
	'FORUM_NAME' 	=> ($user->lang['VIEW_CAT'] . ' - ' . $row['video_cat_title']),
));

// Output page
page_header($l_title, false);

$template->set_filenames(array(
	'body' => 'video/video_cat.html')
);

page_footer();

?>