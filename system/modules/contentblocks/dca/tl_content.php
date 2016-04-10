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



// table callbacks
$GLOBALS['TL_DCA']['tl_content']['config']['onload_callback'][] = array('tl_content_change', 'buildPaletteAndFields');
$GLOBALS['TL_DCA']['tl_content']['config']['onsubmit_callback'][] = array('tl_content_change', 'savePatternFields');

$GLOBALS['TL_DCA']['tl_content']['config']['ondelete_callback'][] = array('tl_content_change', 'deleteRelatedValues');
$GLOBALS['TL_DCA']['tl_content']['config']['oncopy_callback'][] = array('tl_content_change', 'copyRelatedValues');

$GLOBALS['TL_DCA']['tl_content']['config']['onversion_callback'][] = array('tl_content_change', 'newRelatedValuesVersion');
$GLOBALS['TL_DCA']['tl_content']['config']['onrestore_callback'][] = array('tl_content_change', 'restoreRelatedValues');

$GLOBALS['TL_DCA']['tl_content']['list']['sorting']['child_record_callback'] = array('tl_content_change', 'addCteType');

// remove some filter options
$GLOBALS['TL_DCA']['tl_content']['fields']['guests']['filter'] = false;


// new type selection widget
$GLOBALS['TL_DCA']['tl_content']['fields']['type']['inputType'] = 'visualselect';


// new options callback to get new content block elements
$GLOBALS['TL_DCA']['tl_content']['fields']['type']['options_callback'] = array('tl_content_change', 'getContentElements');

// set new default element
$GLOBALS['TL_DCA']['tl_content']['fields']['type']['load_callback'] = array(array('tl_content_change', 'setDefaultType'));
$GLOBALS['TL_DCA']['tl_content']['fields']['type']['default'] = false;




class tl_content_change extends tl_content
{
	
	
	/**
	 * @var array returned field values
	 *
	 * array[duplicatId][patternId][columnName]
	 */
	protected $arrLoadedValues = array();

	/**
	 * @var array returned field values
	 */
	protected $arrModifiedValues = array();

	
	
	/**
	 * Add the type of content element
	 *
	 * @param array $arrRow
	 *
	 * @return string
	 */
	public function addCteType($arrRow)
	{	
		// get block element
		$objBlock = \ContentBlocksModel::findOneByAlias($arrRow['type']);
		
		$this->import('tl_content');
		$return = $this->{'tl_content'}->{'addCteType'}($arrRow);
		
		return ($objBlock->invisible) ? substr_replace($return, ' <span style="color: #b3b3b3;">(invisible content block)</div>', strpos($return, '</div>')) : $return;
				
	}
	
	
	
	
	/**
	 * generate content element list for type selection
	 */
	public function getContentElements ($dc)
	{

		// don´t try to add content block elements if nothing exists
		if (\Config::get('overwriteCTE') && isset($GLOBALS['TL_CTB']))
		{
			$arrCTE = $GLOBALS['TL_CTB'][$this->getThemeId($dc->activeRecord->ptable, $dc->activeRecord->pid)];
		}
		else
		{
			$arrCTE = $GLOBALS['TL_CTE'];

			unset($arrCTE['ctb']);
			array_insert($arrCTE, 0, $GLOBALS['TL_CTB'][$this->getThemeId($dc->activeRecord->ptable, $dc->activeRecord->pid)]);
		}
		
		// legacy support
		if ($dc->value != '' && !in_array($dc->value, array_keys(array_reduce($arrCTE, 'array_merge', array()))))
		{
			return array($dc->value);
		}
	
		foreach ($arrCTE as $k=>$v)
		{
			foreach (array_keys($v) as $kk)
			{
				$groups[$k][] = $kk;
			}
		}	
	
		return $groups;		
	}


	/**
	 * set default value for new records
	 */
	public function setDefaultType($value, $dc)
	{
		if (!$value)
		{
			$objContent = \ContentModel::findByPk($dc->id);
				
			$objContent->type = (isset($GLOBALS['TL_CTB'])) ? $GLOBALS['TL_CTB_DEFAULT'][$this->getThemeId($dc->activeRecord->ptable,  $dc->activeRecord->pid)] : "text";
			$objContent->save();
				
			$this->redirect(\Environment::get('request'));
		}
		
		return $value;
	}

	
	/**
	 * build palettes and field DCA and load values from tl_content_value
	 */
	public function buildPaletteAndFields ($dc)
	{
		// get content	
		$objContent = \ContentModel::findByPk($dc->id);
		
		if ($objContent === null)
		{
			return;
		}

		// get block element
		$objBlock = \ContentBlocksModel::findOneByAlias($objContent->type);
			
		if ($objBlock === null)
		{
			return;
		}


		// add default palette (for type selection)
		$GLOBALS['TL_DCA']['tl_content']['palettes'][$objBlock->alias] = '{type_legend},type';

					
		// add the pattern to palettes
		$colPattern = \ContentPatternModel::findPublishedByPid($objBlock->id, array('order'=>'sorting ASC'));

		if ($colPattern === null)
		{
			return;
		}


		while($colPattern->next())
		{

			// don´t load values for system pattern (because they have no ?? maybe a replica counter ??)
			if (!in_array($colPattern->current()->type, array('section', 'explanation', 'visibility', 'protection')))
			{
				$colValue = \ContentValueModel::findByCidandPid($objContent->id, $colPattern->current()->id);
			
				if ($colValue !== null)
				{
					foreach ($colValue as $objValue)
					{
						$this->arrLoadedValues[$objValue->rid][$colPattern->current()->id] = $objValue->row();
					}							
				}

			}
		
			// construct dca for pattern
			$strClass = \Pattern::findClass($colPattern->current()->type);
				
			if (!class_exists($strClass))
			{
				static::log('Pattern element class "'.$strClass.'" (pattern element "'.$colPattern->current()->type.'") does not exist', __METHOD__, TL_ERROR);
			}
			else
			{
				$objPatternClass = new $strClass($colPattern->current());
				$objPatternClass->cid = $objContent->id;
				$objPatternClass->replica = 0;
				$objPatternClass->alias = $objBlock->alias;			
				
				$objPatternClass->construct();
			}

				
			// extra work for section with replicas
			if ($colPattern->current()->type == 'section' && $colPattern->current()->replicable)
			{

				$objSectionPattern = $colPattern->current();
				$arrPatternClass = array();
						
				// first: load existing values and classes for every pattern
				while($colPattern->next())
				{
					if ($colPattern->current()->type == 'section')
					{
						$colPattern->prev();
						break;
					}
					elseif (!in_array($colPattern->current()->type, array('visibility', 'protection'))) // ignore visibility and protection pattern replicas
					{								
							
						$colValue = \ContentValueModel::findByCidandPid($objContent->id, $colPattern->current()->id);
					
						if ($colValue !== null)
						{
							foreach ($colValue as $objValue)
							{
								$this->arrLoadedValues[$objValue->rid][$colPattern->current()->id] = $objValue->row();
							}							
						}

						
						$strClass = \Pattern::findClass($colPattern->current()->type);
				
						if (!class_exists($strClass))
						{
							static::log('Pattern element class "'.$strClass.'" (pattern element "'.$colPattern->current()->type.'") does not exist', __METHOD__, TL_ERROR);
						}
						else
						{
							$arrPatternClass[] = new $strClass($colPattern->current());
						}
						
					}
				}
			
				// second: construct dca for every replica and pattern
				for ($replica = 0; $replica < $objSectionPattern->maxReplicas; $replica++)
				{
				
					foreach ($arrPatternClass as $objPatternClass)
					{			
							
						$objPatternClass->replica = $replica;
						$objPatternClass->alias = $objBlock->alias;			
						$objPatternClass->construct();
			
						
					}					
				}		
			}
					
		}
	
	}	

	
	/**
	 * save fields value to tl_content_value table
	 */
	public function savePatternFields (&$dc)
	{
	
		foreach ($this->arrModifiedValues as $rid => $pattern)
		{
			foreach ($pattern as $pid => $fields)
			{
				$bolSave = false;
				$objValue = \ContentValueModel::findByCidandPidandRid($dc->activeRecord->id,$pid,$rid);
				if ($objValue === null)
				{
					// if no dataset exist make a new one
					$objValue = new ContentValueModel();
				}
				
				$objValue->pid = $pid;
				$objValue->cid = $dc->activeRecord->id;
				$objValue->rid = $rid;
				$objValue->tstamp = time();
			
				foreach ($fields as $k=>$v)
				{
					
					if ($objValue->$k != $v)
					{

						$bolSave = true;
						$objValue->$k = $v;
					}
				}
		
		
				if ($bolSave)
				{
					$objValue->save();
					$dc->blnCreateNewVersion = true;
				}
				
			}
		}
		
	}

	
	/**
	 * delete related Values when a content element is deleted
	 */
	public function deleteRelatedValues ($dc, $intUndoId)
	{
		$colValues = \ContentValueModel::findByCid($dc->activeRecord->id);
		
		if ($colValues == null)
		{
			return;
		}

		$this->import('BackendUser', 'User');
			
		// get the undo database row
		$objUndo = $this->Database->prepare("SELECT data FROM tl_undo WHERE id=?")
								  ->execute($intUndoId) ;
			
		$arrData = deserialize($objUndo->fetchAssoc()[data]);
		

		foreach ($colValues as $objValue)
		{
			
			// get value row(s)
			$arrData['tl_content_value'][] = $objValue->row();

			$objValue->delete();
		}
		
		// save to the undo database row
		$this->Database->prepare("UPDATE tl_undo SET data=? WHERE id=?")
					   ->execute(serialize($arrData), $intUndoId);

	}

	
	/**
	 * copy related Values when a content element is copied
	 */
	public function copyRelatedValues ($intId, $dc)
	{
		$colValues = \ContentValueModel::findByCid($dc->id);

		if ($colValues == null)
		{
			return;
		}
		
		foreach ($colValues as $objValue)
		{
			$objNewValue = clone $objValue;
			$objNewValue->cid = $intId;
			$objNewValue->save();
		} 
	}

	/**
	 * save new version with the content element for each pattern value
	 */
	public function newRelatedValuesVersion ($strTable, $intVersionId, $dc)
	{
		
		$colValues = \ContentValueModel::findByCid($dc->id);

		if ($colValues == null)
		{
			return;
		}
		
		foreach ($colValues as $objValue)
		{
			// versioning all values
			$objVersion = new \Versions('tl_content_value', $objValue->id);
			$objVersion->initialize();
			$objVersion->create();
		} 			
		
	}

	/**
	 * save new version with the content element for each pattern value
	 */
	public function restoreRelatedValues ($intPid, $strTable, $dc, $intVersion)
	{

		$colValues = \ContentValueModel::findByCid($intPid);

		foreach ($colValues as $objValue)
		{
			// restoring all values (don´t use the version class because the callback is inside the restore method)
			$objData = $this->Database->prepare("SELECT * FROM tl_version WHERE fromTable=? AND pid=? AND version=?")
									  ->limit(1)
									  ->execute('tl_content_value', $objValue->id, $intVersion);
			
			if ($objData->numRows)
			{
			
				$data = deserialize($objData->data);
			
				if (is_array($data))
				{	
					// Get the currently available fields
					$arrFields = array_flip($this->Database->getFieldnames('tl_content_value'));
					
					// Unset fields that do not exist (see #5219)
					$data = array_intersect_key($data, $arrFields);
					
					$this->loadDataContainer('tl_content_value');
					
					// Reset fields added after storing the version to their default value (see #7755)
					foreach (array_diff_key($arrFields, $data) as $k=>$v)
					{
						$data[$k] = \Widget::getEmptyValueByFieldType($GLOBALS['TL_DCA'][$this->strTable]['fields'][$k]['sql']);
					}
					
					$this->Database->prepare("UPDATE tl_content_value %s WHERE id=?")
				   				   ->set($data)
								   ->execute($objValue->id);
				
					$this->Database->prepare("UPDATE tl_version SET active='' WHERE pid=?")
								   ->execute($objValue->id);
					
					$this->Database->prepare("UPDATE tl_version SET active=1 WHERE pid=? AND version=?")
								   ->execute($objValue->id, $intVersion);
								   

				}
			}
		}
		
	}

	
	
	
	/**
	 * load field value from tl_content_value table
	 */
	public function loadFieldValue ($value, $dc)
	{
		if ($this->arrLoadedValues)
		{
			$id = explode('_', $dc->field);			
			return $this->arrLoadedValues[$id[2]][$id[1]][$id[0]];
		}
		
		return $value;		
	}

	/**
	 * save field value to tl_content_value table
	 */
	public function saveFieldAndClear ($value, $dc)
	{
		$id = explode('_', $dc->field);
		$this->arrModifiedValues[$id[2]][$id[1]][$id[0]] = $value;
		return null;
	}


	
	/**
	 * prepare the virtual orderSRC field (filetree widget)
	 */
	public function prepareOrderSRCValue ($value, $dc)
	{
		// Prepare the order field
		$id = explode('_', $dc->field);
		$orderSRC = deserialize($this->arrLoadedValues['orderSRC'][$id[1]][$id[2]]);
		$GLOBALS['TL_DCA']['tl_content']['fields'][$dc->field]['eval']['orderSRC_'.$id[1].'_'.$id[2]] = (is_array($orderSRC)) ? $orderSRC : array();
		
		return $value;
	}

	/**
	 * save the virtual orderSRC field (filetree widget)
	 */
	public function saveOrderSRCValue ($value, $dc)
	{
		// Prepare the order field
		$id = explode('_', $dc->field);
		$orderSRC = array_map('StringUtil::uuidToBin', explode(',', \Input::post('orderSRC_'.$id[1].'_'.$id[2])));
		$this->arrModifiedValues[$id[2]][$id[1]][$id[0]] = $orderSRC;
		
		return $value;
	}

	
	/**
	 * set default value for new records
	 */
	public function defaultValue($value, $dc)
	{
		if ($value == '')
		{			
			return $GLOBALS['TL_DCA']['tl_content']['fields'][$dc->field]['default'];
		}

		return $value;
	}


	
	
	/**
	 * get theme id for content element
	 */
	public static function getThemeId ($strTable, $intPid)
	{
		if ($strTable == 'tl_article')
		{
			$objArticle = \ArticleModel::findById($intPid);
			$objPage = \PageModel::findWithDetails($objArticle->pid);
			$objLayout = \LayoutModel::findById($objPage->layout);	
			return $objLayout->pid;
		}
		elseif($strTable == 'tl_news')
		{
			$objNews = \NewsModel::findById($intPid);
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
					$strBuffer = $this->{$callback[0]}->{$callback[1]}($strTable, $intPid);
				}
			}
			return $strBuffer;
		}
	
	}


}
