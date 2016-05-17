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

	
	/**
	 * Insert a template
	 *
	 * @param string $name The template name
	 * @param array  $data An optional data array
	 */
	public function insert($name, array $data=null)
	{
		
		// register the template file (to find the custom templates)
		if (!array_key_exists($name, \TemplateLoader::getFiles()))
		{
			$objTheme = \LayoutModel::findById(\ContentBlocks::getLayoutId($this->ptable, $this->pid))->getRelated('pid');
			
			\TemplateLoader::addFile($name, $objTheme->templates);
		}

		
		/** @var \Template $tpl */
		if ($this instanceof \Template)
		{
			$tpl = new static($name);
		}
		elseif (TL_MODE == 'BE')
		{
			$tpl = new \BackendTemplate($name);
		}
		else
		{
			$tpl = new \FrontendTemplate($name);
		}
		if ($data !== null)
		{
			$tpl->setData($data);
		}
		echo $tpl->parse();
	}
}
