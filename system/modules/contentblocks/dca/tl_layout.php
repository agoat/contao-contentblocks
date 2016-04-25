<?php
 
 /**
 * Contao Open Source CMS - ContentBlocks extension
 *
 * Copyright (c) 2016 Arne Stappen (aGoat)
 *
 *
 * @package   contentblocks
 * @author    Arne Stappen <http://agoat.de>
 * @license	  LGPL-3.0+
 */


// Palettes

$GLOBALS['TL_DCA']['tl_layout']['palettes']['default'] = str_replace('loadingOrder', 'loadingOrder,backendCSS', $GLOBALS['TL_DCA']['tl_layout']['palettes']['default']);


// Fields
$GLOBALS['TL_DCA']['tl_layout']['fields']['backendCSS'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_layout']['backendCSS'],
	'exclude'                 => true,
	'inputType'               => 'fileTree',
	'eval'                    => array('fieldType'=>'radio', 'filesOnly'=>true, 'extensions'=>'css,scss,less', 'tl_class'=>'w50'),
	'sql'                     => "binary(16) NULL"
);