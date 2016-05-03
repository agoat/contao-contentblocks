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


 /**
 * Register back end module tables
 */
array_push($GLOBALS['BE_MOD']['design']['themes']['tables'], 'tl_content_blocks', 'tl_content_pattern');

 

 /**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['getPageLayout'][] = array('ContentBlocks','loadAndRegisterBlockElements');
$GLOBALS['TL_HOOKS']['loadDataContainer'][] = array('ContentBlocks','loadAndRegisterElementsWithGroups');

$GLOBALS['TL_HOOKS']['loadDataContainer'][] = array('ContentBlocks','setNewsArticleCallbacks');

$GLOBALS['TL_HOOKS']['parseTemplate'][] = array('ContentBlocks','addPageLayoutToBE');

$GLOBALS['TL_HOOKS']['outputFrontendTemplate'][] = array('ContentBlocks','addTemplatesCSS');


/**
 * Content pattern
 */
$GLOBALS['TL_CTP'] = array
(
	'input' => array
	(
		'textfield'		=> 'PatternTextField',
		'textarea'		=> 'PatternTextArea',
		'selectfield'	=> 'PatternSelectField',
		'checkbox'		=> 'PatternCheckBox',
		'filetree'		=> 'PatternFileTree',
		'listwizard'	=> 'PatternListWizard',
		'tablewizard'	=> 'PatternTableWizard',
		'code'			=> 'PatternCode',
	),
	'layout' => array
	(
		'section'		=> 'PatternSection',
		'explanation'	=> 'PatternExplanation',
	),
	'element' => array
	(
		'visibility'	=> 'PatternVisibility',
		'protection'	=> 'PatternProtection',
	),
	'system' => array
	(
		'form'			=> 'PatternForm',
		'module'		=> 'PatternModule',
	),
);



/**
 * system pattern (with no values)
 */
$GLOBALS['TL_SYS_PATTERN'] = array('explanation', 'visibility', 'protection');



/**
 * Back end form fields (widgets)
 */
$GLOBALS['BE_FFL']['explanation'] = 'Explanation';
$GLOBALS['BE_FFL']['visualselect'] = 'VisualSelectMenu';


/**
 * Back end layout style
 */
if(TL_MODE == 'BE')
{
    $GLOBALS['TL_CSS'][] = 'system/modules/contentblocks/assets/contentblocks.css';
}



