<?php
/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @license LGPL-3.0+
 */
 
 

 
/**
 * Table tl_content_element
 */
$GLOBALS['TL_DCA']['tl_content_value'] = array
(
	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'enableVersioning'            => true,
		'ptable'                      => 'tl_content',

		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary',
				'cid' => 'index',
				'pid' => 'index',
			)
		)
	),

	// Fields
	'fields' => array
	(
		'id' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL auto_increment"
		),
		'cid' => array	// tl_content.id
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'pid' => array	// tl_content_pattern.id
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'rid' => array	// replica id
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),
		'tstamp' => array
		(
			'sql'                     => "int(10) unsigned NOT NULL default '0'"
		),

		// value columns 
		'text' => array
		(
			'sql'                     => "mediumtext NULL"
		),
		'textField' => array
		(
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'multiField' => array
		(
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'inputUnit' => array
		(
			'sql'                     => "varchar(255) NOT NULL default ''"
		),
		'selectField' => array
		(
			'sql'                     => "varchar(128) NOT NULL default ''"
		),
		'checkBox' => array
		(
			'sql'                     => "char(1) NOT NULL default ''"
		),
		'listItems' => array
		(
			'sql'                     => "blob NULL"
		),
		'tableItems' => array
		(
			'sql'                     => "mediumblob NULL"
		),
		'singleSRC' => array
		(
			'sql'                     => "binary(16) NULL"
		),
		'multiSRC' => array
		(
			'sql'                     => "blob NULL"
		),
		'orderSRC' => array
		(
			'sql'                     => "blob NULL"
		),
		'sortBy' => array
		(
			'sql'                     => "varchar(32) NOT NULL default ''"
		),		
		'size' => array
		(
			'sql'                     => "varchar(64) NOT NULL default ''"
		),


	)
);



