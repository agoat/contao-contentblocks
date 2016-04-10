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


$id = explode('_', $this->field);
$objPattern = \ContentPatternModel::findOneById($id[1]);

if ($objPattern === null)
{
	include(\TemplateLoader::getPath('tinymce_simple','html5'));
}
else
{
	include(\TemplateLoader::getPath($objPattern->rteTemplate,'html5'));
}

?>