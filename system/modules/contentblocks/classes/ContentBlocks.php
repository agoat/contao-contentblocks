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

		// try to add frontend stylesheets
		if (TL_MODE == 'BE')
		{
			if (\Config::get('loadCSSintoBE') && $objTemplate->getName() == 'be_main' && Input::get('table') == 'tl_content' && !\Input::get('act'))
			{
				$strStylesheets = '';

				
				if (\Input::get('do') == 'article' && \Input::get('id') )
				{
					$objArticle = \ArticleModel::findByPk(\Input::get('id'));
					$objPage = \PageModel::findWithDetails($objArticle->pid);
					$objLayout = \LayoutModel::findById($objPage->layout);	
				}
				elseif (\Input::get('do') == 'news' && \Input::get('id'))
				{
					$objNews = \NewsModel::findById(\Input::get('id'));
					$objPage = \PageModel::findWithDetails($objNews->getRelated('pid')->jumpTo);
					$objLayout = \LayoutModel::findById($objPage->layout);	
				}
				
				// make objPage global
				$GLOBALS['objPage'] = $objPage;
				
				// Make sure TL_USER_CSS is set
				if (!is_array($GLOBALS['TL_USER_CSS']))
				{
					$GLOBALS['TL_USER_CSS'] = array();
				}
				
				
				$arrStyleSheets = deserialize($objLayout->stylesheet);
				// Here should come the routine to load the styles from tl_stylesheet
				
				
				$arrExternal = deserialize($objLayout->external);

				// External style sheets
				if (!empty($arrExternal) && is_array($arrExternal))
				{
					// Consider the sorting order (see #5038)
					if ($objLayout->orderExt != '')
					{
						$tmp = deserialize($objLayout->orderExt);
						
						if (!empty($tmp) && is_array($tmp))
						{
							// Remove all values
							$arrOrder = array_map(function(){}, array_flip($tmp));
							
							// Move the matching elements to their position in $arrOrder
							foreach ($arrExternal as $k=>$v)
							{
								if (array_key_exists($v, $arrOrder))
								{
									$arrOrder[$v] = $v;
									unset($arrExternal[$k]);
								}
							}
							
							// Append the left-over style sheets at the end
							if (!empty($arrExternal))
							{
								$arrOrder = array_merge($arrOrder, array_values($arrExternal));
							}
							
							// Remove empty (unreplaced) entries
							$arrExternal = array_values(array_filter($arrOrder));
							unset($arrOrder);
						}
					}
					
					// Get the file entries from the database
					$objFiles = \FilesModel::findMultipleByUuids($arrExternal);
					if ($objFiles !== null)
					{
						$arrFiles = array();
						
						while ($objFiles->next())
						{
							if (file_exists(TL_ROOT . '/' . $objFiles->path))
							{
								$arrFiles[] = $objFiles->path . '|static';
							}
						}
						
						// Inject the external style sheets before or after the internal ones (see #6937)
						if ($objLayout->loadingOrder == 'external_first')
						{
							array_splice($GLOBALS['TL_USER_CSS'], 0, 0, $arrFiles);
						}
						else
						{
							array_splice($GLOBALS['TL_USER_CSS'], count($GLOBALS['TL_USER_CSS']), 0, $arrFiles);
						}
					}
				}

				if (is_array($GLOBALS['TL_CB_CSS']))
				{
					$GLOBALS['TL_USER_CSS'] = array_merge($GLOBALS['TL_USER_CSS'], $GLOBALS['TL_CB_CSS']);
				}

				// add the main.css to overwrite styles again to the right backend theme style
				$GLOBALS['TL_USER_CSS'][] = 'system/themes/'. $objTemplate->theme .'/main.css|static';
				$GLOBALS['TL_USER_CSS'][] = 'system/modules/contentblocks/assets/main.css|static';
				
				$objTemplate->stylesheets = $this->replaceDynamicScriptTags('[[TL_CSS]]');
				
			}

		}
	}

	
	// add custom block stylesheets
	public function addContentBlockCSS ($objTemplate)
	{
		// add custom content block css to frontend page
		if (TL_MODE == 'FE' && strpos($objTemplate->getName(), 'fe_') !== false)
		{
			if (is_array($GLOBALS['TL_CB_CSS']))
			{
				$GLOBALS['TL_USER_CSS'] = array_merge($GLOBALS['TL_USER_CSS'], $GLOBALS['TL_CB_CSS']);
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


	
}

