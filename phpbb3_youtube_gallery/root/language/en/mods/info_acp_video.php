<?php
/**
*
* info_acp_video [English]
*
* @package language
* @version $Id$
* @copyright (c) 2013 Vinny
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/
/**
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* DO NOT CHANGE
*/
if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine

$lang = array_merge($lang, array(
	'VIDEO_INDEX'			=> 'Video Gallery',
	'VIDEO_SELECT_CAT'		=> 'Select a category',
	'VIDEO_SUBMIT'			=> 'Post a new video',
	'VIDEO_URL'				=> 'Enter Video URL',
	'VIDEO_URL_EXPLAIN'		=> '',

	'VIDEOS_TIP'			=> 'Help and Suggestions',
	'VIDEOS_TIPS'			=> '
	<ul>
		<li>Browse to <a href="https://www.youtube.com/">Youtube.com</a>, search your favorite videos.</li>
		<li>Copy the video URL, paste it in the field above, choose the category and submit the form.</li>
		<li>You can use <strong>youtube.com</strong> and <strong>youtu.be</strong>, both are accepted by the MOD.</li>
	</ul>
	<br />
	<strong>Warning: this page isn’t for uploading videos to Youtube!</strong>',

	'UNAUTHED'				=> 'You are not authorised to view this page.',
	'VIDEO_UNAUTHED'		=> 'You are not authorised to view this video.',

	'INVALID_VIDEO'			=> 'The video you selected does not exist.',
	'VIDEO'					=> 'Videos',
	'VIDEO_EXPLAIN'			=> 'View gallery of Youtube videos',
	'VIEW_CAT'				=> 'View Category',
	'VIEW_VIDEO'			=> 'View Video',
	'VIDEO_CAT'				=> 'Category',
	'VIDEO_CATS'			=> 'Categories',
	'VIDEO_CATEGORIES'		=> 'Categories',
	'VIDEO_CREATED'			=> 'This video has been added successfully.',
	'VIDEO_DATE'			=> 'Date',
	'VIDEO_DELETED_SUCCESS'	=> 'This video has been deleted successfully.',
	'PAGE_RETURN'			=> '%sReturn to the videos page%s',
	'RETURN'				=> 'Return to the previous page',

	'DELETE_VIDEO_CONFIRM'	=> 'Are you sure you want to delete this video?',
	'MY_VIDEOS'				=> 'View your videos',
	'RETURN_TO_SEARCH_ADV' 	=> 'Return to advanced search',

	'NEED_VIDEO_URL'		=> 'You must enter a <strong>url</strong> for this video.',
	'NEWEST_VIDEOS'			=> 'Newest Videos',
	'NO_VIDEOS'				=> 'This page has no videos.',
	'NO_CAT_VIDEOS'			=> 'This category has no videos or does not exist.',
	'NO_USER_VIDEOS'		=> 'This user has no videos or does not exist.',
	'NO_CATEGORIES'			=> 'This page has no categories.',
	'SEARCH_VIDEOS'			=> 'Search Videos',
	'TOTAL_CATEGORIES_OTHER'=> 'Total categories <strong>%d</strong>',
	'TOTAL_CATEGORY_ZERO'	=> 'Total categories <strong>0</strong>',
	'TOTAL_VIDEOS'			=> 'Total videos',
	'TOTAL_VIDEOS_OTHER'	=> 'Total videos <strong>%d</strong>',
	'TOTAL_VIDEO_ZERO'		=> 'Total videos <strong>0</strong>',
	'USER_VIDEOS'			=> 'Search user’s videos',
	'NO_KEY_ADMIN'			=> 'Dear board administrator, in order to use Video Gallery, you must set up a <strong>Google Public API key</strong>, go to the Administration Control Panel and follow the instructions.',
	'NO_KEY_USER'			=> 'Dear user, the gallery is unavailable. Please come back later.',

	// ACP
	'ACP_VIDEO'				=> 'Video Gallery',
	'ACP_VIDEO_EXPLAIN'		=> '',
	'ACP_VIDEO_SETTINGS'	=> 'Video Settings',
	'ACP_VIDEO_GENERAL_SETTINGS'	=> 'General Settings',
	'ACP_VIDEO_ENABLE'		=> 'Enable Videos Page',
	'ACP_VIDEO_CATEGORY'	=> 'Video Categories',
	'ACP_VIDEO_HEIGHT'		=> 'Video Height',
	'ACP_VIDEO_WIDTH'		=> 'Video Width',
	'ACP_GOOGLE_KEY'		=> 'Google Public API key',
	'ACP_GOOGLE_KEY_EXPLAIN'=> 'In order to use Video Gallery, you must create a <strong>Google Public API key</strong>. Please, visit <a href="https://console.developers.google.com/">Google Developers Console</a> to generate the key. If you have trouble to generate your key, read the guide <a href="https://developers.google.com/console/help/new/#generatingdevkeys">Google Developers Console Help: API keys</a>. Until you set up your key, the gallery will be unavailable.',
	'ACP_VIDEOS_PER_PAGE'	=> 'Videos per page',

	// ACP Categories
	'ACP_CATEGORY_CREATED'	=> 'This category has been added successfully.',
	'ACP_CATEGORY_DELETE'	=> 'Are you sure you wish to delete this category?',
	'ACP_CATEGORY_DELETED'	=> 'This category has been deleted successfully.',
	'ACP_CATEGORY_EDIT'		=> 'Edit category',
	'ACP_CATEGORY_UPDATED'	=> 'This category has been updated successfully.',
	'ACP_VIDEO_CAT_ADD'		=> 'Add New Category',
	'ACP_VIDEO_CAT_TITLE'	=> 'Category Title',
	'ACP_VIDEO_CAT_TITLE_EXPLAIN'	=> 'Enter the title of the category.',
	'ACP_VIDEO_CAT_TITLE_TITLE'	=> 'You must enter a <strong>title</strong> for this category.',
	'ACP_VIDEO_OVERVIEW'	=> 'Video Categories',
	'ACP_VIDEO_OVERVIEW_EXPLAIN'	=> 'Here you can manage the Video Categories of your board.',

	// Install
	'INSTALL_TEST_CAT'		=> 'Uncategorized',
	
	// View Video
	'FLASH_IS_OFF'			=> '[flash] is <em>OFF</em>',
	'FLASH_IS_ON'			=> '[flash] is <em>ON</em>',
	'VIDEO_ADD_BY'			=> 'Added by',
	'VIDEO_BBCODE'			=> 'BBcode',
	'VIDEO_EMBED'			=> 'Embed Video',
	'VIDEO_LINK'			=> 'Video Link',
	'VIDEO_LINKS'			=> 'Links',
	'VIDEO_LINK_YOUTUBE'	=> 'Youtube Video Link',
	'VIDEO_VIEWS'			=> 'Views',

	// Youtube video text
	'VIDEO_AUTHOR'			=> 'Author',
	'VIDEO_WATCH'			=> 'Watch on YouTube',

	//Pagination
	'LIST_VIDEO'			=> '1 Video',
	'LIST_VIDEOS'			=> '%1$s Videos',
));

?>