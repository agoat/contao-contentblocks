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

namespace Contao;



class ContentBlockTemplate extends \FrontendTemplate
{
	
	/**
	 * Add CSS to template
	 *
	 * @return string The template markup
	 */
	public function addCSS ($strCSS, $strType='scss', $bolStatic=true)
	{
		if ($strCSS == '')
		{
			return;
		}
		
		if (!in_array($strType, array('css', 'scss' , 'less')))
		{
			return;
		}
		
		if (!$bolStatic && $strType == 'css')
		{
			$strKey = substr(md5($strType . $strCSS), 0, 12);
			$strPath = 'assets/css/' . $strKey . '.' . $strType;
			
			// Write to a temporary file in the assets folder
			if (!file_exists($strPath))
			{
				$objFile = new \File($strPath, true);
				$objFile->write($strCSS);
				$objFile->close();
			}
			
			// add file path to TL_USER_CSS
			$GLOBALS[TL_USER_CSS][] = $strPath;
		
			return;
		}
		// add to combined CSS string
		$GLOBALS['TL_CTB_' . $strType] .= $strCSS;
	}

	
}