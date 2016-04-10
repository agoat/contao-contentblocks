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


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Classes
	'Contao\\ContentBlocks'			=> 'system/modules/contentblocks/classes/ContentBlocks.php',
	'Contao\\Pattern'				=> 'system/modules/contentblocks/classes/Pattern.php',

	// Elements
	'Contao\\ContentBlockElement'	=> 'system/modules/contentblocks/elements/ContentBlockElement.php',

	// Models
	'Contao\\ContentBlocksModel'	=> 'system/modules/contentblocks/models/ContentBlocksModel.php',
	'Contao\\ContentPatternModel'	=> 'system/modules/contentblocks/models/ContentPatternModel.php',
	'Contao\\ContentValueModel'		=> 'system/modules/contentblocks/models/ContentValueModel.php',

	// Pattern
	'Contao\\PatternTextField'		=> 'system/modules/contentblocks/pattern/PatternTextField.php',
	'Contao\\PatternTextArea'		=> 'system/modules/contentblocks/pattern/PatternTextArea.php',
	'Contao\\PatternSelectField'	=> 'system/modules/contentblocks/pattern/PatternSelectField.php',
	'Contao\\PatternCheckBox'		=> 'system/modules/contentblocks/pattern/PatternCheckBox.php',
	'Contao\\PatternListWizard'		=> 'system/modules/contentblocks/pattern/PatternListWizard.php',
	'Contao\\PatternTableWizard'	=> 'system/modules/contentblocks/pattern/PatternTableWizard.php',
	'Contao\\PatternFileTree'		=> 'system/modules/contentblocks/pattern/PatternFileTree.php',

	'Contao\\PatternSection'		=> 'system/modules/contentblocks/pattern/PatternSection.php',
	'Contao\\PatternExplanation'	=> 'system/modules/contentblocks/pattern/PatternExplanation.php',
	
	'Contao\\PatternVisibility'		=> 'system/modules/contentblocks/pattern/PatternVisibility.php',
	'Contao\\PatternProtection'		=> 'system/modules/contentblocks/pattern/PatternProtection.php',

	'Contao\\PatternForm'			=> 'system/modules/contentblocks/pattern/PatternForm.php',
	'Contao\\PatternComment'		=> 'system/modules/contentblocks/pattern/PatternComment.php',
	'Contao\\PatternModule'			=> 'system/modules/contentblocks/pattern/PatternModule.php',

	// Widgets
	'Contao\\FileTree'				=> 'system/modules/contentblocks/widgets/FileTree.php', // overwrite FileTree widget
	'Contao\\Explanation'			=> 'system/modules/contentblocks/widgets/Explanation.php', // new explanation widget (text for backend)
	'Contao\\VisualSelectMenu'		=> 'system/modules/contentblocks/widgets/VisualSelectMenu.php', // new select menu with images
	
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'cb_standard' => 'system/modules/contentblocks/templates',
	'cb_simple' => 'system/modules/contentblocks/templates',
	'cb_debug' => 'system/modules/contentblocks/templates',
	'tinymce_standard' => 'system/modules/contentblocks/templates',
	'tinymce_simple' => 'system/modules/contentblocks/templates',
));

