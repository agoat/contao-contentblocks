<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @package   ReadMore
 * @author    Arne
 * @license   GNU/LGPL
 * @copyright aGoat 2015
 */


 /**
 * Register back end modules tables
 */
array_push($GLOBALS['BE_MOD']['design']['themes']['tables'], 'tl_content_blocks', 'tl_content_pattern', 'tl_content_value');

 

 /**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['getPageLayout'][] = array('ContentBlocks','loadAndRegisterBlockElements');
$GLOBALS['TL_HOOKS']['loadDataContainer'][] = array('ContentBlocks','loadAndRegisterElementsWithGroups');

$GLOBALS['TL_HOOKS']['parseTemplate'][] = array('ContentBlocks','addPageLayoutToBE');
$GLOBALS['TL_HOOKS']['parseTemplate'][] = array('ContentBlocks','addContentBlockCSS');



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



