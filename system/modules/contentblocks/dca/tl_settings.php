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


// legacy mode
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] = str_replace('maintenanceMode', 'maintenanceMode;{elements_legend},overwriteCTE', $GLOBALS['TL_DCA']['tl_settings']['palettes']['default']);

// more file types
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] = str_replace('validImageTypes', 'validImageTypes,validVideoTypes,validAudioTypes', $GLOBALS['TL_DCA']['tl_settings']['palettes']['default']);



// fields
$GLOBALS['TL_DCA']['tl_settings']['fields']['overwriteCTE'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['overwriteCTE'],
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'w50'),
);
$GLOBALS['TL_DCA']['tl_settings']['fields']['loadCSSintoBE'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['loadCSSintoBE'],
	'inputType'               => 'checkbox',
	'eval'                    => array('tl_class'=>'w50'),
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['validVideoTypes'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['validVideoTypes'],
	'inputType'               => 'text',
	'eval'                    => array('tl_class'=>'w50'),
	'save_callback' => array
	(
		array('tl_settings_change', 'checkVideoTypes')
	)
);

$GLOBALS['TL_DCA']['tl_settings']['fields']['validAudioTypes'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_settings']['validAudioTypes'],
	'inputType'               => 'text',
	'eval'                    => array('tl_class'=>'w50'),
	'save_callback' => array
	(
		array('tl_settings_change', 'checkAudioTypes')
	)
);		




class tl_settings_change extends Backend
{
	
	public function checkVideoTypes ($varValue)
	{
		if (trim($varValue) == '')
		{
			$varValue = 'mp4,m4v,mov,wmv,webm,ogv';
		}
		
		return $varValue;
	}
	
	public function checkAudioTypes ($varValue)
	{
		if (trim($varValue) == '')
		{
			$varValue = 'm4a,mp3,wma,mpeg,wav,ogg';
		}
		
		return $varValue;
	}
	
	
}