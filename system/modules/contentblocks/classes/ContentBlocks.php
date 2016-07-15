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

		// add the contentblocks backend style sheets
		if (TL_MODE == 'BE')
		{
			if ($objTemplate->getName() == 'be_main' && Input::get('table') == 'tl_content')
			{
				// Make sure TL_USER_CSS is set
				if (!is_array($GLOBALS['TL_USER_CSS']))
				{
					$GLOBALS['TL_USER_CSS'] = array();
				}

				// Make sure TL_JAVASCRIPT is set
				if (!is_array($GLOBALS['TL_JAVASCRIPT']))
				{
					$GLOBALS['TL_JAVASCRIPT'] = array();
				}

				if (\Input::get('do') && \Input::get('id'))
				{
					$intLayoutId = $this->getLayoutId('tl_'.\Input::get('do'), \Input::get('id')); 
					
					// sometimes the id is not the parent table but the content table id
					if (!$intLayoutId)
					{
						$intLayoutId = $this->getLayoutId('tl_'.\Input::get('do'), \ContentModel::findById(\Input::get('id'))->pid); 
					}
				}

				$objLayout = \LayoutModel::findById($intLayoutId);

				if ($objLayout === null)
				{
					return;
				}
				
				// add backend CSS
				$arrBackendCSS = deserialize($objLayout->backendCSS);
				
				if (!empty($arrBackendCSS) && is_array($arrBackendCSS))
				{
					// Consider the sorting order (see #5038)
					if ($objLayout->orderBackendCSS != '')
					{
						$tmp = deserialize($objLayout->orderBackendCSS);
						
						if (!empty($tmp) && is_array($tmp))
						{
							// Remove all values
							$arrOrder = array_map(function(){}, array_flip($tmp));
							
							// Move the matching elements to their position in $arrOrder
							foreach ($arrBackendCSS as $k=>$v)
							{
								if (array_key_exists($v, $arrOrder))
								{
									$arrOrder[$v] = $v;
									unset($arrBackendCSS[$k]);
								}
							}
							
							// Append the left-over style sheets at the end
							if (!empty($arrBackendCSS))
							{
								$arrOrder = array_merge($arrOrder, array_values($arrBackendCSS));
							}
							
							// Remove empty (unreplaced) entries
							$arrBackendCSS = array_values(array_filter($arrOrder));
							unset($arrOrder);
						}
					}
					
					// Get the file entries from the database
					$objFiles = \FilesModel::findMultipleByUuids($arrBackendCSS);
					
					if ($objFiles !== null)
					{
						while ($objFiles->next())
						{
							if (file_exists(TL_ROOT . '/' . $objFiles->path))
							{
								$GLOBALS['TL_USER_CSS'][] = $objFiles->path . '|static';
							}
						}
					}

					unset($objFiles);
				}
				
				// add backend JS
				$arrBackendJS = deserialize($objLayout->backendJS);
				
				if (!empty($arrBackendJS) && is_array($arrBackendJS))
				{
					// Consider the sorting order (see #5038)
					if ($objLayout->orderBackendJS != '')
					{
						$tmp = deserialize($objLayout->orderBackendJS);
						
						if (!empty($tmp) && is_array($tmp))
						{
							// Remove all values
							$arrOrder = array_map(function(){}, array_flip($tmp));
							
							// Move the matching elements to their position in $arrOrder
							foreach ($arrBackendJS as $k=>$v)
							{
								if (array_key_exists($v, $arrOrder))
								{
									$arrOrder[$v] = $v;
									unset($arrBackendJS[$k]);
								}
							}
							
							// Append the left-over style sheets at the end
							if (!empty($arrBackendJS))
							{
								$arrOrder = array_merge($arrOrder, array_values($arrBackendJS));
							}
							
							// Remove empty (unreplaced) entries
							$arrBackendJS = array_values(array_filter($arrOrder));
							unset($arrOrder);
						}
					}
					
					// Get the file entries from the database
					$objFiles = \FilesModel::findMultipleByUuids($arrBackendJS);
					
					if ($objFiles !== null)
					{
						while ($objFiles->next())
						{
							if (file_exists(TL_ROOT . '/' . $objFiles->path))
							{
								$GLOBALS['TL_JAVASCRIPT'][] = $objFiles->path . '|static';
							}
						}
					}

					unset($objFiles);
				}
	

				// add content block template js
				$this->addContentBlockJS();

				// add jquery if active in layout
				if ($objLayout->addJQuery && $objLayout->backendJS)
				{
					array_unshift($GLOBALS['TL_JAVASCRIPT'], 'system/modules/contentblocks/assets/jquery.noConflict.js|static');
					array_unshift($GLOBALS['TL_JAVASCRIPT'], 'assets/jquery/core/' . $GLOBALS['TL_ASSETS']['JQUERY'] . '/jquery.min.js|static');
				}

				
				// add content block template css
				$this->addContentBlockCSS();
				
				// add extra content block css
				if (is_array($GLOBALS['TL_CB_CSS']))
				{
					$GLOBALS['TL_USER_CSS'] = array_merge($GLOBALS['TL_USER_CSS'], $GLOBALS['TL_CB_CSS']);
				}

				
				// combine stylesheets and javascripts
				list($objTemplate->stylesheets, $objTemplate->javascripts) = explode('#', $this->replaceDynamicScriptTags('[[TL_CSS]]#[[TL_HEAD]]'));
				
			}
		}
	}

	public function addContentBlockCSS ($strBuffer='', $objTemplate=null)
	{
		foreach (array('CSS', 'SCSS' , 'LESS') as $strType)
		{
			if ($GLOBALS['TL_CTB_' . $strType] == '')
			{
				continue;
			}

			$strKey = substr(md5($strType . $GLOBALS['TL_CTB_CSS'] . $GLOBALS['TL_CTB_SCSS'] . $GLOBALS['TL_CTB_LESS']), 0, 12);
			$strPath = 'assets/css/' . $strKey . '.' . strtolower($strType);
			
			// Write to a temporary file in the assets folder
			if (!file_exists($strPath))
			{
				$objFile = new \File($strPath, true);
				$objFile->write($GLOBALS['TL_CTB_' . $strType]);
				$objFile->close();
			}
				
			$strPath .= '|static';
			
			// add file path to TL_USER_CSS
			$GLOBALS['TL_USER_CSS'][] = $strPath;
		}

		return $strBuffer;
	}

	public function addContentBlockJS ($strBuffer='', $objTemplate=null)
	{
		if ($GLOBALS['TL_CTB_JS'] == '')
		{
			return $strBuffer;
		}

		$strKey = substr(md5('js' . $GLOBALS['TL_CTB_JS']), 0, 12);
		$strPath = 'assets/js/' . $strKey . '.js';
		
		// Write to a temporary file in the assets folder
		if (!file_exists($strPath))
		{
			$objFile = new \File($strPath, true);
			$objFile->write($GLOBALS['TL_CTB_JS']);
			$objFile->close();
		}
			
		$strPath .= '|static';
		
		// add file path to TL_JAVASCRIPT
		$GLOBALS['TL_JAVASCRIPT'][] = $strPath;

		return $strBuffer;
	}

	
	public function addLayoutJS ($objPage, $objLayout)
	{
		$arrExternalJS = deserialize($objLayout->externalJS);
		
		if (!empty($arrExternalJS) && is_array($arrExternalJS))
		{
			// Consider the sorting order (see #5038)
			if ($objLayout->orderBackendJS != '')
			{
				$tmp = deserialize($objLayout->orderExternalJS);
				
				if (!empty($tmp) && is_array($tmp))
				{
					// Remove all values
					$arrOrder = array_map(function(){}, array_flip($tmp));
					
					// Move the matching elements to their position in $arrOrder
					foreach ($arrExternalJS as $k=>$v)
					{
						if (array_key_exists($v, $arrOrder))
						{
							$arrOrder[$v] = $v;
							unset($arrExternalJS[$k]);
						}
					}
					
					// Append the left-over style sheets at the end
					if (!empty($arrExternalJS))
					{
						$arrOrder = array_merge($arrOrder, array_values($arrExternalJS));
					}
					
					// Remove empty (unreplaced) entries
					$arrExternalJS = array_values(array_filter($arrOrder));
					unset($arrOrder);
				}
			}
			
			// Get the file entries from the database
			$objFiles = \FilesModel::findMultipleByUuids($arrExternalJS);
			
			if ($objFiles !== null)
			{
				while ($objFiles->next())
				{
					if (file_exists(TL_ROOT . '/' . $objFiles->path))
					{
						$GLOBALS['TL_JAVASCRIPT'][] = $objFiles->path . '|static';
					}
				}
			}

			unset($objFiles);
		}
					
		return;
	}

	
	
	// register new content elements (for content element class assignment)
	public function loadAndRegisterBlockElements ()
	{
		// Don´t register twice
		if (is_array($GLOBALS['TL_CTB'])) 
		{
			return;
		}

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

		// add to registry
		$GLOBALS['TL_CTB'] = $arrCTE; // content block elements
		
		array_insert($GLOBALS['TL_CTE'], 0, $arrCTE); // add to content elements array

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
			// HOOK: custom method to discover the layout id
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



	
	/**
	 * register callbacks for news extension bundles with contao core
	 */
	public function setNewsArticleCallbacks ($strTable)
	{
		if ($strTable != 'tl_news' || TL_MODE == 'FE')
		{
			return;
		}
		
		$GLOBALS['TL_DCA']['tl_news']['config']['oncopy_callback'][] = array('tl_news_contentblocks', 'copyRelatedValues');
		$GLOBALS['TL_DCA']['tl_news']['config']['ondelete_callback'][] = array('tl_news_contentblocks', 'deleteRelatedValues');

	}
	

	
}

