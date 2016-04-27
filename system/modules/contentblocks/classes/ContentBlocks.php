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
			if ($objTemplate->getName() == 'be_main' && Input::get('table') == 'tl_content')
			{
				
				// Make sure TL_USER_CSS is set
				if (!is_array($GLOBALS['TL_USER_CSS']))
				{
					$GLOBALS['TL_USER_CSS'] = array();
				}

				if (\Input::get('do') && \Input::get('id'))
				{
					 // in copy mode the id is not the article but the content
					$intLayoutId = $this->getLayoutId('tl_'.\Input::get('do'), (\Input::get('mode') == 'copy') ? \ContentModel::findById(\Input::get('id'))->pid : \Input::get('id'));
				}

				$objLayout = \LayoutModel::findById($intLayoutId);

				if ($objLayout !== null)
				{
					$objFile = \FilesModel::findByPk($objLayout->backendCSS);
	
					if ($objFile !== null)
					{
						$GLOBALS['TL_USER_CSS'][] = $objFile->path . '|static';
					}
				}
				
				// add extra content block css
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
		$GLOBALS['TL_CTB'] = $arrCTB; // content block elements
		$GLOBALS['TL_CTB_IMG'] = $arrCBI; // content block images
		
		$GLOBALS['TL_CTE_LEGACY'] = $GLOBALS['TL_CTE']; // save contao standard content elements for legacy support
		array_insert($GLOBALS['TL_CTE'], 0, $arrCTE); // add to content elements array
		
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
	public static function getLayoutId ($strTable, $intId)
	{
		if ($strTable == 'tl_article')
		{
			$objArticle = \ArticleModel::findById($intId);

			if ($objArticle === null)
			{
				return false;
			}
			
			$objPage = \PageModel::findWithDetails($objArticle->pid);

			if ($objPage === null)
			{
				return false;
			}

			return $objPage->layout;
			
		}
		elseif($strTable == 'tl_news')
		{
			$objNews = \NewsModel::findById($intId);

			if ($objNews === null)
			{
				return false;
			}
			
			$objPage = \PageModel::findWithDetails($objNews->getRelated('pid')->jumpTo);

			if ($objPage === null)
			{
				return false;
			}
			
			return $objPage->layout;
			
		}
		else
		{
			// HOOK: custom method to discover the theme
			if (isset($GLOBALS['TL_HOOKS']['getLayoutId']) && is_array($GLOBALS['TL_HOOKS']['getLayoutId']))
			{
				foreach ($GLOBALS['TL_HOOKS']['getLayoutId'] as $callback)
				{
					$this->import($callback[0]);
					$intId = $this->{$callback[0]}->{$callback[1]}($strTable, $intId);
				}
			}
			return $intId;
		}
	
	}

	
}

