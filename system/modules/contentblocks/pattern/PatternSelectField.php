<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-201 Leo Feyer
 * 
 * @package   Wrapper 
 * @author    Arne Stappen 
 * @license   LGPL 
 * @copyright A. Stappen (2011-2015)
 */

 
namespace Contao;

 
class PatternSelectField extends \Pattern
{


	/**
	 * generate the DCA construct
	 */
	public function construct()
	{

		$class = ($this->classClr) ? 'w50 clr' : 'w50';

		// Generate options
		$strGroup = '-';
		foreach (deserialize($this->options) as $arrOption)
		{
			if ($arrOption['group'])
			{
				$strGroup = $arrOption['label'];
				continue;
			}	
			
			if ($arrOption['default'])
			{
				$default = $arrOption['value'];
			}	
			
			$arrOptions[$strGroup][$arrOption['value']] = $arrOption['label'];
			//$arrOptions[$strGroup][] = $arrOption['value'];

		}		


		parent::construct('selectField', array
		(
			'inputType' 	=>	'select',
			'label'			=>	array($this->label, $this->description),
			'default'		=>	$default,
			'options'		=>	$arrOptions,
			'eval'			=>	array
			(
				'mandatory'		=>	($this->mandatory) ? true : false, 
				'tl_class'		=>	$class,
			),
		));
		
	}
	

	/**
	 * Generate backend output
	 */
	public function view()
	{
		$strPreview = '<div class="" style="padding-top:10px;"><h3 style="margin: 0;"><label>' . $this->label . '</label></h3>';
		$strPreview .= '<select class="tl_select" style="width: 412px;">';

		$options = deserialize($this->options);
		foreach (deserialize($this->options) as $option)
		{
			if ($option['group'])
			{
				if ($blnOpenGroup)
				{
					$strPreview .= '</optgroup>';
				}
				
				$strPreview .= '<optgroup label="&nbsp;' . specialchars($option['label']) . '">';
				$blnOpenGroup = true;
				continue;
			}
					
			$strPreview .= '<option value="' . specialchars($option['value']) . '"' . (($option['default']) ? ' selected' : '') . '>' . specialchars($option['label']) . '</option>';
		}

		if ($blnOpenGroup)
		{
			$strPreview .= '</optgroup>';
		}

		$strPreview .= '</select><p title="" class="tl_help tl_tip">' . $this->description . '</p></div>';
		return $strPreview;	
	}


	/**
	 * prepare data for the frontend template 
	 */
	public function compile()
	{
		
		parent::compile($this->Value->selectField);
		
	}
	

}
