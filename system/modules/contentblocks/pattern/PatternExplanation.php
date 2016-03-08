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

 
class PatternExplanation extends \Pattern
{

	
	/**
	 * generate the DCA construct
	 */
	public function construct()
	{
		
		// an explanation field

		parent::construct('explanation', array
		(
			'inputType' =>	'explanation',
			'eval'		=>	array
			(
				'explanation'	=>	\StringUtil::toHtml5($this->explanation), 
			)
		), false);
	
	}


	/**
	 * prepare a field view for the backend
	 *
	 * @param array $arrAttributes An optional attributes array
	 */
	public function view()
	{
		return '<div style="padding-top:10px;">' . \StringUtil::toHtml5($this->explanation) . '</div>';
	}


	/**
	 * prepare the values for the frontend template
	 *
	 * @param array $arrAttributes An optional attributes array
	 */	
	public function compile()
	{
		return;
	}
}
