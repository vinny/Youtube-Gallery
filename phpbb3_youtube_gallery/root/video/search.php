<?php
/**
*
* @package phpBB3
* @version $Id$
* @copyright (c) 2013 Vinny
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
$user_id = request_var('u', 0);

$sql_start = request_var('start', 0);
$sql_limit = request_var('limit', 10);

$search_id = request_var('search_id', '');

$template->assign_block_vars('navlinks', array(
	'FORUM_NAME' 	=> ($user->lang['VIDEO_INDEX']),
	'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}video/index.$phpEx"),	
));

switch ($search_id)
{
	case 'ego';
	$template->assign_vars(array(
		'S_SEARCH_USER_VIDEO' 	=> true,
		'U_VIDEO_SEARCH'		=> append_sid("{$phpbb_root_path}video/search.$phpEx"),
	));

	$sql_limit = ($sql_limit > 10) ? 10 : $sql_limit;
	$pagination_url = append_sid("{$phpbb_root_path}video/search.$phpEx", "search_id=ego");

	$sql = 'SELECT v.*, ct.video_cat_title,ct.video_cat_id, u.username,u.user_colour,u.user_id
	FROM ' . VIDEO_TABLE . ' v, ' . VIDEO_CAT_TABLE . ' ct, ' . USERS_TABLE . ' u
	WHERE u.user_id = v.user_id
		AND ct.video_cat_id = v.video_cat_id
		AND u.user_id = ' . (int) $user->data['user_id'] . '
			ORDER BY v.video_id DESC';
	$result = $db->sql_query_limit($sql, $sql_limit, $sql_start);
	$row = $db->sql_fetchrow($result);
	
	/*if (!$row)
	{
		trigger_error('NO_USER_VIDEOS');
	}*/
	while ($row = $db->sql_fetchrow($result))
	{
		$template->assign_block_vars('video', array(
			'VIDEO_TITLE'		=> $row['video_title'],
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
	$sql = 'SELECT COUNT(*) as video_count FROM ' . VIDEO_TABLE . ' WHERE user_id = ' . (int) $user->data['user_id'];
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

	$l_title = ($user->lang['MY_VIDEOS']);
	$template_html = 'video/video_search.html';

	break;

	default:

	break;
}

// Output page
page_header($l_title, false);

$template->set_filenames(array(
	'body' => $template_html)
);

page_footer();

?>