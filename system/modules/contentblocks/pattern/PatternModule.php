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

 
class PatternModule extends \Pattern
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
		$objModule = \ModuleModel::findByPk($this->module);
		return '<span>' . $objModule->name . ' (ID ' . $objModule->id . ')</span>';
	}

	/**
	 * Generate data for the frontend template 
	 */
	public function compile()
	{
		$objModule = \ModuleModel::findByPk($this->module);
	
		if ($objModule === null)
		{
			return;
		}
		
		$strClass = \Module::findClass($objModule->type);
		
		if (!class_exists($strClass))
		{
			return;
		}
	
		$objModule->typePrefix = 'ce_';

		/** @var \Module $objModule */
		$objModule = new $strClass($objModule);
		
		parent::compile($objModule->generate());
		
	}


	
}
