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
 
 

class ContentBlocks extends \Controller
{

	// add frontend stylesheets to backend
	public function addPageLayoutToBE ($objTemplate)
	{

		// add the contentblocks backend stylesheets
		if (TL_MODE == 'BE')
		{
	
			if ($objTemplate->getName() == 'be_main' && Input::get('table') == 'tl_content' && !\Input::get('act'))
			{
				
				// Make sure TL_USER_CSS is set
				if (!is_array($GLOBALS['TL_USER_CSS']))
				{
					$GLOBALS['TL_USER_CSS'] = array();
				}

				if (is_array($GLOBALS['TL_CB_CSS']))
				{
					$GLOBALS['TL_USER_CSS'] = array_merge($GLOBALS['TL_USER_CSS'], $GLOBALS['TL_CB_CSS']);
				}

				// combine stylesheets
				$objTemplate->stylesheets = $this->replaceDynamicScriptTags('[[TL_CSS]]');
				
			}

		}
	}

	
	// register new content elements (for content element class assignment)
	public function loadAndRegisterBlockElements ()
	{
		
		$this->import("Database");

		if ($this->Database->tableExists("tl_content_blocks"))
		{		
			// get all elements in db
			$arrElements 	= $this->Database->prepare("SELECT * FROM tl_content_blocks ORDER BY sorting ASC")
											->execute()
											->fetchAllAssoc();	
		}
			
		if ($arrElements === null)
		{
			return;
		}

		// generate array with elements 
		foreach ($arrElements as $arrElement)
		{
			$arrCTE['ctb'][$arrElement['alias']] = 'ContentBlockElement';			
		}

		array_insert($GLOBALS['TL_CTE'], 0, $arrCTE); // add to standard content elements		

	}

	
	// register new content elements (for content element selection)
	public function loadAndRegisterElementsWithGroups ($strTable)
	{
		if ($strTable != 'tl_content' || TL_MODE == 'FE')
		{
			return;
		}

		$this->import("Database");

		if ($this->Database->tableExists("tl_content_blocks"))
		{		
			// get all content block elements from db
			$arrElements 	= $this->Database->prepare("SELECT * FROM tl_content_blocks ORDER BY pid, sorting ASC")
											->execute()
											->fetchAllAssoc();	
		}
			
		if ($arrElements === null)
		{
			return;
		}


		// generate array with elements 
		foreach ($arrElements as $arrElement)
		{
			// group
			if ($arrElement['type'] == 'group')
			{
				$strGroup = $arrElement['alias'];
				$arrLANG[$arrElement['alias']] = $arrElement['title'];	

				if (!isset($arrCTB[$arrElement['pid']]))
				{
					$arrCTB[$arrElement['pid']] = array();
				}
				
			}
			else
			{
				if (!isset($arrCTB[$arrElement['pid']]))
				{
					$strGroup = 'ctb';
				}
				
				$arrCTE[$strGroup][$arrElement['alias']] = 'ContentBlockElement';
				
				if (!$arrElement['invisible'])
				{
					$arrCTB[$arrElement['pid']][$strGroup][$arrElement['alias']] = 'ContentBlockElement';					
				}
				
				$arrCBI[$arrElement['alias']] = $arrElement['singleSRC'];
				$arrLANG[$arrElement['alias']] = array($arrElement['title'],$arrElement['description']);
				
				// set as default element type
				if ($arrElement['defaultType'])
				{
					$GLOBALS['TL_CTB_DEFAULT'][$arrElement['pid']] = $arrElement['alias'];
				}
				elseif (!isset($GLOBALS['TL_CTB_DEFAULT'][$arrElement['pid']]))
				{
					$GLOBALS['TL_CTB_DEFAULT'][$arrElement['pid']] = $arrElement['alias'];
				}
			}
		}
	
		// add to registry
		$GLOBALS['TL_CTB'] = $arrCTB; // new content block elements
		$GLOBALS['TL_CTB_IMG'] = $arrCBI; // new content block images

		array_insert($GLOBALS['TL_CTE'], 0, $arrCTE); // add to standard content elements
		
		array_insert($GLOBALS['TL_LANG']['CTE'], 0, $arrLANG); // add to language 


	}

	
	/**
	 * Get the theme ID for an article
	 *
	 * @param string  $strTable The name of the table (article or news) 
	 * @param integer $intId    An article or a news article ID
	 *
	 * @return integer The theme ID
	 */
	public static function getThemeId ($strTable, $intId)
	{
		if ($strTable == 'tl_article')
		{
			$objArticle = \ArticleModel::findById($intId);
			$objPage = \PageModel::findWithDetails($objArticle->pid);
			$objLayout = \LayoutModel::findById($objPage->layout);	
			return $objLayout->pid;
		}
		elseif($strTable == 'tl_news')
		{
			$objNews = \NewsModel::findById($intId);
			$objPage = \PageModel::findWithDetails($objNews->getRelated('pid')->jumpTo);
			$objLayout = \LayoutModel::findById($objPage->layout);	
			return $objLayout->pid;
		}
		else
		{
			// HOOK: custom method to discover the theme
			if (isset($GLOBALS['TL_HOOKS']['getThemeId']) && is_array($GLOBALS['TL_HOOKS']['getThemeId']))
			{
				foreach ($GLOBALS['TL_HOOKS']['getThemeId'] as $callback)
				{
					$this->import($callback[0]);
					$intId = $this->{$callback[0]}->{$callback[1]}($strTable, $intId);
				}
			}
			return $intId;
		}
	
	}

	
}

