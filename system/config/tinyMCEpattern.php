<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @license LGPL-3.0+
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