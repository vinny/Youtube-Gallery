<?php
/**
*
* Video [Portuguese]
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
	'VIDEO_INDEX'			=> 'Galeria de Vídeo',
	'VIDEO_SELECT_CAT'		=> 'Seleccione a categoria',
	'VIDEO_SUBMIT'			=> 'Coloque um novo vídeo',
	'VIDEO_URL'			=> 'Introduza o URL do vídeo',
	'VIDEO_URL_EXPLAIN'		=> '',

	'VIDEOS_TIP'			=> 'Ajuda e sugestões',
	'VIDEOS_TIPS'			=> '
	<ul>
		<li>Vá a <a href="https://www.youtube.com/">Youtube.com</a>, e escolha o vídeo.</li>
		<li>Copie o URL do vídeo, e coloque no campo acima, escolha a categoria que se enquadre ao vídeo e submeta.</li>
		<li>Pode tanto usar <strong>youtube.com</strong> como <strong>youtu.be</strong>, ambos são aceites.</li>
	</ul>
	<br />
	<strong>Aviso: esta página não é para enviar vídeos para o Youtube!</strong>',

	'UNAUTHED'			=> 'Não tem permissões para ver esta página.',
	'VIDEO_UNAUTHED'		=> 'Não tem permissões para ver este vídeo.',

	'INVALID_VIDEO'			=> 'O vídeo seleccionado não existe.',
	'VIDEO'				=> 'Vídeos',
	'VIDEO_EXPLAIN'			=> 'Ver galeria de vídeos do Youtube',
	'VIEW_CAT'			=> 'Ver Categoria',
	'VIEW_VIDEO'			=> 'Ver Vídeo',
	'VIDEO_CAT'			=> 'Categoria',
	'VIDEO_CATS'			=> 'Categorias',
	'VIDEO_CATEGORIES'		=> 'Categorias',
	'VIDEO_CREATED'			=> 'Este vídeo foi adicionado com sucesso.',
	'VIDEO_DATE'			=> 'Data',
	'VIDEO_DELETED'			=> 'Este vídeo foi removido com sucesso.',
	'PAGE_RETURN'			=>'%sRetroceder à página de vídeos%s',

	'DELETE_VIDEO'			=> 'Tem a certeza que pretende remover este vídeo?',
	'MY_VIDEOS'			=> 'Ver os meus vídeos',

	'NEED_VIDEO_URL'		=> 'Deve introduzir um <strong>url</strong> para este vídeo.',
	'NEWEST_VIDEOS'			=> 'Vídeos Recentes',
	'NO_VIDEOS'			=> 'Esta página não tem vídeos.',
	'NO_CATEGORIES'			=> 'Esta página não tem categorias.',
	'SEARCH_VIDEOS'			=> 'Pesquisar Vídeos',
	'TOTAL_CATEGORIES_OTHER'	=> 'Total de categorias <strong>%d</strong>',
	'TOTAL_CATEGORY_ZERO'		=> 'Sem <strong>nenhuma</strong> categoria',
	'TOTAL_VIDEOS'			=> 'Total de vídeos',
	'TOTAL_VIDEOS_OTHER'		=> 'Total de vídeos <strong>%d</strong>',
	'TOTAL_VIDEO_ZERO'		=> 'Sem <strong>nenhum</strong> vídeo',
	'USER_VIDEOS'			=> 'Pesquisar vídeos do utilizador',

	// ACP
	'ACP_VIDEO'			=> 'Galeria de Vídeo',
	'ACP_VIDEO_EXPLAIN'		=> '',
	'ACP_VIDEO_SETTINGS'		=> 'Configurações de Vídeo',
	'ACP_VIDEO_GENERAL_SETTINGS'	=> 'Configurações Globais',
	'ACP_VIDEO_ENABLE'		=> 'Activar Página de Vídeos',
	'ACP_VIDEO_CATEGORY'		=> 'Categorias de Vídeo',
	'ACP_VIDEO_HEIGHT'		=> 'Altura do Vídeo',
	'ACP_VIDEO_WIDTH'		=> 'Largura do Vídeo',

	// ACP Categories
	'ACP_CATEGORY_CREATED'		=> 'Esta categoria foi adicionada com sucesso.',
	'ACP_CATEGORY_DELETE'		=> 'Tem a certeza que pretende remover esta categoria?',
	'ACP_CATEGORY_DELETED'		=> 'Esta categoria foi removida com sucesso',
	'ACP_CATEGORY_EDIT'		=> 'Editar categoria',
	'ACP_CATEGORY_UPDATED'		=> 'Esta categoria foi actualizada com sucesso!',
	'ACP_VIDEO_CAT_ADD'		=> 'Adicionar Nova Categoria',
	'ACP_VIDEO_CAT_TITLE'		=> 'Título da Categoria',
	'ACP_VIDEO_CAT_TITLE_EXPLAIN'	=> 'Introduza o título da categoria.',
	'ACP_VIDEO_CAT_TITLE_TITLE'	=> 'Tem de introduzir um <strong>título</strong> para esta categoria.',
	'ACP_VIDEO_OVERVIEW'		=> 'Categorias de Vídeos',
	'ACP_VIDEO_OVERVIEW_EXPLAIN'	=> 'Aqui poderá gerir as Categorias de Vídeo do seu fórum.',

	// Install
	'INSTALL_TEST_CAT'		=> 'Nenhuma Categoria',
	
	// View Video
	'FLASH_IS_OFF'			=> '[flash] está <em>DESLIGADO</em>',
	'FLASH_IS_ON'			=> '[flash] está <em>LIGADO</em>',
	'VIDEO_ADD_BY'			=> 'Adicionado por',
	'VIDEO_BBCODE'			=> 'BBcode',
	'VIDEO_EMBED'			=> 'Embed Video',
	'VIDEO_LINK'			=> 'Link do Vídeo',
	'VIDEO_LINKS'			=> 'Links',
	'VIDEO_LINK_YOUTUBE'		=> 'Link Youtube do Vídeo',
	'VIDEO_VIEWS'			=> 'Visualizações',

	// Youtube video text
	'VIDEO_AUTHOR'			=> 'Autor',
	'VIDEO_WATCH'			=> 'Ver no YouTube',

	//Pagination
	'LIST_VIDEO'			=> '1 Vídeo',
	'LIST_VIDEOS'			=> '%1$s Vídeos',
));

?>
