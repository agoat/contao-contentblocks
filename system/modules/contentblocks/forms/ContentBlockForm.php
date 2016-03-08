<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @license LGPL-3.0+
 */


namespace Contao;
 
 

/**
 * Pattern class
 *
 * @property integer $id
 * @property integer $pid


 *
 * @author Arne Stappen
 */
class ContentBlockForm extends \Form
{
	/**
	 * Don´t remove anything. 
	 *
	 * @return string
	 */
	public function generate()
	{
		return \Hybrid::generate();
	}


}
