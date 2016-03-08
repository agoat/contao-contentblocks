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
 * Provide methods to show an explanation 
 *
 * @property array   $options
 * @property boolean $multiple
 *
 * @author Leo Feyer <https://github.com/leofeyer>
 */
class Explanation extends \Widget
{


	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'be_widget_rdo';



	/**
	 * Generate the widget and return it as string
	 *
	 * @return string
	 */
	public function generate()
	{
		
		return sprintf('<div class="tl_message clr" style="margin-left:0;margin-right:0;">%s</div>',
							$this->explanation);
							
	}


}
