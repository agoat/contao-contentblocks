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

 
class PatternComment extends \Pattern
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
		// nothing to show
		return;
	}

	/**
	 * Generate data for the frontend template 
	 */
	public function compile()
	{
		$this->import('Comments');

		$objConfig = new \stdClass();
		
		$objConfig->perPage = $this->com_perPage;
		$objConfig->order = 'ascending';
		$objConfig->template = $this->com_template;
		$objConfig->requireLogin = $this->com_requireLogin;
		$objConfig->disableCaptcha = $this->com_disableCaptcha;
		$objConfig->bbcode = $this->com_bbcode;
		$objConfig->moderate = $this->com_moderate;
		$this->class = 'comments';
				
		$this->Comments->addCommentsToTemplate($objTemplate = new \FrontendTemplate('ce_comments'), $objConfig, 'tl_content', $this->cid, $GLOBALS['TL_ADMIN_EMAIL']);
		$objTemplate->class = 'comments';
		
		parent::compile($objTemplate->parse());		
		
	}


	
}
