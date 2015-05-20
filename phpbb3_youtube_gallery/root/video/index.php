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

// Initial var setup
$sql_start = request_var('start', 0);
$sql_limit = request_var('limit', $config['videos_per_page']);

// Determine board url - we may need it later
$board_url = generate_board_url() . '/';
$web_path = (defined('PHPBB_USE_BOARD_URL_PATH') && PHPBB_USE_BOARD_URL_PATH) ? $board_url : $phpbb_root_path;

$template->assign_vars(array(
	//'S_NEW_VIDEO'	 		=> $auth->acl_get('u_video_post') ? true : false,
	'U_VIDEO'				=> append_sid("{$phpbb_root_path}video/index.$phpEx"),
));

// Google API key is set up?
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

//Listing categories
$sql = 'SELECT * FROM ' . VIDEO_CAT_TABLE . " ORDER BY video_cat_id";
$result = $db->sql_query($sql);
while($row = $db->sql_fetchrow($result))
{
	$template->assign_block_vars('videocat', array(
		'VIDEO_CAT_ID'		=> $row['video_cat_id'],
		'VIDEO_CAT_TITLE'	=> $row['video_cat_title'],
		'U_CAT'				=> append_sid("{$phpbb_root_path}video/viewcategory.$phpEx", 'cid=' . $row['video_cat_id']),
	));
}

// Count the videos ...
$sql = 'SELECT COUNT(video_id) AS total_videos FROM ' . VIDEO_TABLE;
$result = $db->sql_query($sql);
$total_videos = (int) $db->sql_fetchfield('total_videos');
$db->sql_freeresult($result);

// Count the videos categories ...
$sql = 'SELECT COUNT(video_cat_id) AS total_categories FROM ' . VIDEO_CAT_TABLE ;
$result = $db->sql_query($sql);
$total_categories = (int) $db->sql_fetchfield('total_categories');
$db->sql_freeresult($result);

$l_total_video_s 	= ($total_videos == 0) ? 'TOTAL_VIDEO_ZERO' : 'TOTAL_VIDEOS_OTHER';
$l_total_category_s = ($total_categories == 0) ? 'TOTAL_CATEGORY_ZERO' : 'TOTAL_CATEGORIES_OTHER';

$template->assign_vars(array(
	'U_VIDEO_SUBMIT' 	=> append_sid("{$phpbb_root_path}video/posting.$phpEx"),
	'BUTTON_VIDEO_NEW'	=> "{$web_path}styles/" . rawurlencode($user->theme['imageset_path']) . '/imageset/' . $user->lang_name .'/button_video_new.gif',
	'TOTAL_VIDEOS'		=> sprintf($user->lang[$l_total_video_s], $total_videos),
	'TOTAL_CATEGORIES'	=> sprintf($user->lang[$l_total_category_s], $total_categories),
	'U_MY_VIDEOS'		=> append_sid("{$phpbb_root_path}video/search.$phpEx", 'search_id=ego'),
));

$sql_limit = ($sql_limit > $config['videos_per_page']) ? $config['videos_per_page'] : $sql_limit;
$pagination_url = append_sid("{$phpbb_root_path}video/index.$phpEx");

$sql = $db->sql_build_query('SELECT', array(
	'SELECT'	=> 'v.*,
	ct.video_cat_title,ct.video_cat_id,
	u.username,u.user_colour,u.user_id',
	'FROM'		=> array(
		VIDEO_TABLE			=> 'v',
		VIDEO_CAT_TABLE		=> 'ct',
		USERS_TABLE			=> 'u',
	),
	'WHERE'		=> 'ct.video_cat_id = v.video_cat_id AND u.user_id = v.user_id',
	'ORDER_BY'	=> 'v.video_id DESC',
));

$result = $db->sql_query_limit($sql, $sql_limit, $sql_start);

while ($row = $db->sql_fetchrow($result))
{
	$template->assign_block_vars('video', array(
		'VIDEO_TITLE'		=> censor_text($row['video_title']),
		'VIDEO_CAT_ID'		=> $row['video_cat_id'],
		'VIDEO_CAT_TITLE'	=> $row['video_cat_title'],
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
$sql = 'SELECT COUNT(*) as video_count FROM ' . VIDEO_TABLE;
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

$template->assign_block_vars('navlinks', array(
	'FORUM_NAME' 	=> ($user->lang['VIDEO_INDEX']),
	'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}video/index.$phpEx"),	
));

// Output page
page_header($user->lang['VIDEO_INDEX'], false);

$template->set_filenames(array(
	'body' => 'video/video_body.html')
);

page_footer();

?>