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
$video_id	= request_var('id', 0);
$video_url = request_var('video_url', '', true);
$video_title = request_var('video_title', '', true);
$video_cat_id = request_var('cid', 0);
$username = request_var('username', '', true);
$user_id = request_var('user_id', 0);
$youtube_id = request_var('youtube_id', '', true);
$create_time = request_var('create_time', '');
$video_views = request_var('video_views', 0);

// Comments
$cmnt_id = request_var('id', 0);
$cmnt_video_id	= request_var('v', 0);
$cmnt_text = utf8_normalize_nfc(request_var('cmnt_text', '', true));

$mode = request_var('mode', '');
$submit = (isset($_POST['submit'])) ? true : false;
$cancel = (isset($_POST['cancel'])) ? true : false;

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

/**
 * Get youtube video ID from URL
 * From: http://halgatewood.com/php-get-the-youtube-video-id-from-a-youtube-url/
 */
function getYouTubeIdFromURL($url)
{
	$pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i';
	preg_match($pattern, $url, $matches);

	return isset($matches[1]) ? $matches[1] : false;
}
$youtube_id = getYouTubeIdFromURL($video_url);

/**
 * Retrieving video information with Youtube APIv3
 * From: https://github.com/vinny/youtube-api3-sample/blob/master/video.php
 */
$jsonURL = file_get_contents("https://www.googleapis.com/youtube/v3/videos?id={$youtube_id}&key={$config['google_api_key']}&type=video&part=snippet");
$json = json_decode($jsonURL);
if (isset($json->items[0]->snippet))
{
	$video_title = $json->items[0]->snippet->title;
}

$sql_ary = array(
	'video_id'			=> $video_id,
	'video_url'			=> $video_url,
	'video_title'		=> $video_title,
	'video_cat_id'		=> $video_cat_id,
	'username'			=> $username,
	'user_id'			=> $user_id,
	'youtube_id'		=> $youtube_id,
	'create_time'		=> (int) time(),
	'video_views'		=> $video_views,
);

$error = $row = array();
$current_time = time();

$template->assign_block_vars('navlinks', array(
	'FORUM_NAME' 	=> ($user->lang['VIDEO_INDEX']),
	'U_VIEW_FORUM'	=> append_sid("{$phpbb_root_path}video/index.$phpEx"),	
));

switch ($mode)
{
	case 'submit':
		$l_title = $user->lang['VIDEO_SUBMIT'];
		$template_html = 'video/video_editor.html';

		if ($video_url == '')
		{
			$meta_info = append_sid("{$phpbb_root_path}video/posting.$phpEx");
			$message = $user->lang['NEED_VIDEO_URL'];

			meta_refresh(3, $meta_info);
			$message .= '<br /><br />' . sprintf($user->lang['PAGE_RETURN'], '<a href="' . $meta_info . '">', '</a>');
			trigger_error($message);
		}
		else
		{
			$db->sql_query('INSERT INTO ' . VIDEO_TABLE .' ' . $db->sql_build_array('INSERT', $sql_ary));
			
			$meta_info = append_sid("{$phpbb_root_path}video/index.$phpEx");
			$message = $user->lang['VIDEO_CREATED'];

			meta_refresh(3, $meta_info);
			$message .= '<br /><br />' . sprintf($user->lang['PAGE_RETURN'], '<a href="' . $meta_info . '">', '</a>');
			trigger_error($message);
		}
	break;

	case 'comment':
		$l_title = ($user->lang['VIDEO_CMNT_SUBMIT']);
		$template_html = 'video/video_cmnt_editor.html';

		if (!$config['enable_comments'])
		{	
			trigger_error($user->lang['COMMENTS_DISABLED']);
		}

		// User is a bot?!
		if ($user->data['is_bot'])
		{
			redirect(append_sid("{$phpbb_root_path}index.$phpEx"));
		}

		// Can post?!
		if (!$auth->acl_get('u_video_comment'))
		{
			trigger_error($user->lang['UNAUTHED']);
		}

		$redirect_url = append_sid("{$phpbb_root_path}video/posting.$phpEx", 'mode=comment&amp;v=' . (int) $video_id);
		// Is a guest?!
		if ($user->data['user_id'] == ANONYMOUS)
		{
			login_box($redirect_url);
		}

		//Settings for comments
		$user->setup('posting');
		display_custom_bbcodes();
		generate_smilies('inline', 0);

		$bbcode_status	= ($config['allow_bbcode']) ? true : false;
		$smilies_status	= ($config['allow_smilies']) ? true : false;
		$img_status		= ($bbcode_status) ? true : false;
		$url_status		= ($config['allow_post_links']) ? true : false;
		$flash_status	= ($bbcode_status && $config['allow_post_flash']) ? true : false;
		$quote_status	= true;
		$video_id		= request_var('v', 0);
		$uid = $bitfield = $options = '';
		$allow_bbcode = $allow_urls = $allow_smilies = true; 
		$s_action = append_sid("{$phpbb_root_path}video/posting.$phpEx", 'mode=comment&amp;v=' . (int) $video_id);
		$s_hidden_fields = '';
		$form_enctype = '';
		add_form_key('postform');

		// Start assigning vars for main posting page ...
		$template->assign_vars(array(
			'VIDEO_ID'				=> (int) $video_id,
			'S_FORM_ENCTYPE'		=> $form_enctype,
			'S_POST_ACTION'			=> $s_action,
			'S_HIDDEN_FIELDS'		=> $s_hidden_fields,
			'ERROR'					=> (sizeof($error)) ? implode('<br />', $error) : '',
			'S_BBCODE_ALLOWED'		=> ($bbcode_status) ? 1 : 0,
			'S_SMILIES_ALLOWED'		=> $smilies_status,
			'S_BBCODE_IMG'			=> $img_status,
			'S_BBCODE_URL'			=> $url_status,
			'S_LINKS_ALLOWED'		=> $url_status,
			'S_BBCODE_QUOTE'		=> $quote_status,
		));

		if (isset($_POST['submit']))
		{
			if (!check_form_key('postform'))
			{
				trigger_error('FORM_INVALID');
			}

			$video_id = request_var('v', 0); // Get video to redirect :D
			$message = request_var('cmnt_text', '', true);
			generate_text_for_storage($message, $uid, $bitfield, $options, $allow_bbcode, $allow_urls, $allow_smilies);
			$data = array(
				'cmnt_video_id'		=> request_var('cmnt_video_id', 0),
				'cmnt_poster_id'	=> $user->data['user_id'],
				'cmnt_text'			=> $message,
				'create_time'		=> time(),
				'bbcode_bitfield'	=> $bitfield,
				'bbcode_uid'		=> $uid,
			);
			$db->sql_query('INSERT INTO ' . VIDEO_CMNTS_TABLE .' ' . $db->sql_build_array('INSERT', $data));
			
			$meta_info = append_sid("{$phpbb_root_path}video/viewvideo.$phpEx", 'id=' . (int) $video_id . '#comments');
			$message = $user->lang['COMMENT_CREATED'];

			meta_refresh(3, $meta_info);
			$message .= '<br /><br />' . sprintf($user->lang['PAGE_RETURN'], '<a href="' . $meta_info . '">', '</a>');
			trigger_error($message);
		}

		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME' 	=> ($user->lang['VIDEO_CMNT_SUBMIT']),
		));
	break;

	case 'delcmnt':
		if (!$auth->acl_get('u_video_comment_delete'))
		{
			trigger_error($user->lang['UNAUTHED']);
		}

		if (confirm_box(true))
		{
			$sql = 'DELETE FROM ' . VIDEO_CMNTS_TABLE . ' WHERE cmnt_id = ' . (int) $cmnt_id;
			$db->sql_query($sql);

			$meta_info = append_sid("{$phpbb_root_path}video/index.$phpEx");
			$message = $user->lang['COMMENT_DELETED_SUCCESS'];
			meta_refresh(3, $meta_info);
			$message .= '<br /><br />' . sprintf($user->lang['PAGE_RETURN'], '<a href="' . $meta_info . '">', '</a>');
			trigger_error($message);
		}
		else
		{
			$s_hidden_fields = build_hidden_fields(array(
				'id'	 	=> $cmnt_id,
				'mode'		=> 'delete')
			);
			confirm_box(false, $user->lang['DELETE_COMMENT_CONFIRM'], $s_hidden_fields);
			$meta_info = append_sid("{$phpbb_root_path}video/viewvideo.$phpEx", 'id=' . $video_id);
			meta_refresh(1, $meta_info);
		}
	break;

	case 'delete':
		if (!$auth->acl_get('u_video_delete'))
		{
			trigger_error($user->lang['UNAUTHED']);
		}

		if (confirm_box(true))
		{
			$sql = 'DELETE FROM ' . VIDEO_TABLE . '
					WHERE video_id = ' . (int) $video_id;
			$db->sql_query($sql);

			$meta_info = append_sid("{$phpbb_root_path}video/index.$phpEx");
			$message = $user->lang['VIDEO_DELETED_SUCCESS'];
			meta_refresh(3, $meta_info);
			$message .= '<br /><br />' . sprintf($user->lang['PAGE_RETURN'], '<a href="' . $meta_info . '">', '</a>');
			trigger_error($message);
		}
		else
		{
			$s_hidden_fields = build_hidden_fields(array(
				'video_id' 	=> $video_id,
				'mode'		=> 'delete')
			);
			confirm_box(false, $user->lang['DELETE_VIDEO_CONFIRM'], $s_hidden_fields);
			$meta_info = append_sid("{$phpbb_root_path}video/viewvideo.$phpEx", 'id=' . $video_id);
			meta_refresh(1, $meta_info);
		}
	break;

	default:
		$l_title = ($user->lang['VIDEO_SUBMIT']);
		$template_html = 'video/video_editor.html';

		// User is a bot?!
		if ($user->data['is_bot'])
		{
			redirect(append_sid("{$phpbb_root_path}index.$phpEx"));
		}

		$redirect_url = append_sid("{$phpbb_root_path}video/posting.$phpEx");

		// Is a guest?!
		if ($user->data['user_id'] == ANONYMOUS)
		{
			login_box($redirect_url);
		}

		// Can post?!
		if (!$auth->acl_get('u_video_post'))
		{
			trigger_error($user->lang['UNAUTHED']);
		}

		$s_action = append_sid("{$phpbb_root_path}video/posting.$phpEx", "mode=submit");
		$s_hidden_fields = '';
		$form_enctype = '';
		add_form_key('postform');

		// List of categories
		$sql = 'SELECT * FROM ' . VIDEO_CAT_TABLE . '
					ORDER BY video_cat_title ASC';
		$result = $db->sql_query($sql);
		
		while ($row = $db->sql_fetchrow($result))
		{
		$template->assign_block_vars('cat', array(
			'VIDEO_CAT_ID'		=> censor_text($row['video_cat_id']),
			'VIDEO_CAT_TITLE'	=> censor_text($row['video_cat_title']),
		));
		}

		// Start assigning vars for main posting page ...
		$template->assign_vars(array(
			'VIDEO_CAT_TITLE'		=> censor_text($row['video_cat_title']),
			'S_USER_ID'				=> $user->data['user_id'],
			'S_USERNAME'			=> $user->data['username'],
			'S_FORM_ENCTYPE'		=> $form_enctype,
			'S_POST_ACTION'			=> $s_action,
			'S_HIDDEN_FIELDS'		=> $s_hidden_fields,
			'ERROR'					=> (sizeof($error)) ? implode('<br />', $error) : '',
		));

		add_form_key('postform');
		if ($submit)
		{
			if (!check_form_key('postform'))
			{
				trigger_error('FORM_INVALID');
			}
		}

		$template->assign_block_vars('navlinks', array(
			'FORUM_NAME' 	=> ($user->lang['VIDEO_SUBMIT']),
		));
	break;
}

// Output page
page_header($l_title, false);

$template->set_filenames(array(
	'body' => $template_html)
);

page_footer();

?>