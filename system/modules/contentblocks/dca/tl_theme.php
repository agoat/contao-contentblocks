<?php

// content block edit button
array_insert($GLOBALS['TL_DCA']['tl_theme']['list']['operations'], 3, array
(
	'ctb' => array
	(
		'label'               => &$GLOBALS['TL_LANG']['tl_theme']['ctb'],
		'href'                => 'table=tl_content_blocks',
		'icon'                => 'system/modules/contentblocks/assets/elements.png',
		//'button_callback'     => array('tl_theme', 'editCss')
	)
));

// allow tl_content_blocks table
$GLOBALS['TL_DCA']['tl_theme']['config']['ctable'][] = 'tl_content_blocks';

