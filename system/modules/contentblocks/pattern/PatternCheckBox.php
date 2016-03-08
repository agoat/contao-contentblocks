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

 
class PatternCheckBox extends \Pattern
{


	/**
	 * generate the DCA construct
	 */
	public function construct()
	{

		$class = ($this->classClr) ? 'w50 clr m12' : 'w50 m12';
	
		parent::construct('checkBox', array
		(
			'inputType' 	=>	'checkbox',
			'label'			=>	array($this->label, $this->description),
			'eval'			=>	array
			(
				'mandatory'		=>	($this->mandatory) ? true : false, 
				'tl_class'		=>	$class,
			)
		));
		
	}
	

	/**
	 * Generate backend output
	 */
	public function view()
	{
		return '<div class="tl_checkbox_single_container"><input class="tl_checkbox" value="1" type="checkbox"> <label>' . $this->label . '</label><p title="" class="tl_help tl_tip">' . $this->description . '</p></div>';	
	}


	/**
	 * prepare data for the frontend template 
	 */
	public function compile()
	{
		
		parent::compile(($this->Value->checkBox) ? true : false);
		
	}
	
}
