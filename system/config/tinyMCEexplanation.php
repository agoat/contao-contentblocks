<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2015 Leo Feyer
 *
 * @license LGPL-3.0+
 */


/**
 * This is the tinyMCE (rich text editor) configuration file. Please visit
 * http://tinymce.moxiecode.com for more information.
 */
if ($GLOBALS['TL_CONFIG']['useRTE']):

?>
<script>window.tinymce || document.write('<script src="<?php echo TL_ASSETS_URL; ?>assets/tinymce4/tinymce.gzip.js">\x3C/script>')</script>
<script>
window.tinymce && tinymce.init({
  skin: "contao",
  selector: "#<?php echo $selector; ?>",
  language: "<?php echo Backend::getTinyMceLanguage(); ?>",
  element_format: "html",
  document_base_url: "<?php echo Environment::get('base'); ?>",
  entities: "160,nbsp,60,lt,62,gt,173,shy",
  setup: function(editor) {
    editor.getElement().removeAttribute('required');
  },
  init_instance_callback: function(editor) {
    editor.on('focus', function(){ Backend.getScrollOffset(); });
  },
 
  plugins: "code link",
  browser_spellcheck: true,
  tabfocus_elements: ":prev,:next",
  
  style_formats: [
	{title: 'Blue text', inline: 'span', styles: {color: '#4b85ba'}},
	{title: 'Red text', inline: 'span', styles: {color: '#c33'}},
	{title: 'Green text', inline: 'span', styles: {color: '#77ac45'}},
	{title: 'Yellow text', inline: 'span', styles: {color: '#d68c23'}},

  ],
  
  fontsize_formats: "0.7em 1em 1.2em 1.5em 2em 3em",

  menubar: false,
  toolbar: "styleselect | fontsizeselect | bold italic | alignleft aligncenter | outdent indent | link unlink | undo redo | removeformat | code"
});
</script>
<?php endif; ?>
