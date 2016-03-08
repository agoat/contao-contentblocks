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

 
class PatternForm extends \Pattern
{


	/**
	 * generate the DCA construct
	 */
	public function construct()
	{
		// nothing to select
		return;
	}
	

	/**
	 * Generate backend output
	 */
	public function view()
	{
		$objForm = new \Form($this);
		return '<span>' . $objForm->title . ' (ID ' . $objForm->id . ')</span>';
	}

	/**
	 * Generate data for the frontend template 
	 */
	public function compile()
	{
		// call the form class
		$objForm = new \Form($this);
		$objForm->formTemplate = $this->formTemplate;
		
		parent::compile($objForm->generate());		
	}


	
}
