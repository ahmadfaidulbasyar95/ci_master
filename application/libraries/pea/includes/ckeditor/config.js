/**
 * @license Copyright (c) 2003-2021, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */
 
CKEDITOR.editorConfig = function( config ) {
	config.toolbar = [{
			name: 'document', items: [
				'Source'
			,	'-'
			// ,	'Save'
			// ,	'NewPage'
			,	'ExportPdf'
			,	'Preview'
			,	'Print'
			,	'-'
			,	'Templates'
			]
		},{
			name: 'clipboard', items: [
				'Cut'
			,	'Copy'
			,	'Paste'
			,	'PasteText'
			,	'PasteFromWord'
			,	'-'
			,	'Undo'
			,	'Redo'
			]
		},{
			name: 'editing', items: [
				'Find'
			,	'Replace'
			,	'-'
			,	'SelectAll'
			,	'-'
			,	'Scayt'
			]
		},{
			name: 'forms', items: [
			// 	'Form'
			// ,	'Checkbox'
			// ,	'Radio'
			// ,	'TextField'
			// ,	'Textarea'
			// ,	'Select'
			// ,	'Button'
			// ,	'ImageButton'
			// ,	'HiddenField'
			]
		},{
			name: 'basicstyles', items: [
				'Bold'
			,	'Italic'
			,	'Underline'
			,	'Strike'
			,	'Subscript'
			,	'Superscript'
			,	'-'
			,	'CopyFormatting'
			,	'RemoveFormat'
			]
		},{
			name: 'paragraph', items: [
				'NumberedList'
			,	'BulletedList'
			,	'-'
			,	'Outdent'
			,	'Indent'
			,	'-'
			,	'Blockquote'
			,	'CreateDiv'
			,	'-'
			,	'JustifyLeft'
			,	'JustifyCenter'
			,	'JustifyRight'
			,	'JustifyBlock'
			,	'-'
			,	'BidiLtr'
			,	'BidiRtl'
			// ,	'Language'
			]
		},{
			name: 'links', items: [
				'Link'
			,	'Unlink'
			,	'Anchor'
			]
		},{
			name: 'insert', items: [
				'Image'
			,	'Flash'
			,	'Table'
			,	'HorizontalRule'
			,	'Smiley'
			,	'SpecialChar'
			,	'PageBreak'
			,	'Iframe'
			,	'pre'
			,	'InsertPre'
			,	'replaceTagName'
			,	'socialfeed'
			,	'powrdropdown'
			]
		},{
			name: 'styles', items: [
				'Styles'
			,	'Format'
			,	'Font'
			,	'FontSize'
			,	'lineheight'
			]
		},{
			name: 'colors', items: [
				'TextColor'
			,	'BGColor'
			]
		},{
			name: 'tools', items: [
				'Maximize'
			,	'ShowBlocks'
			]
		},{
			name: 'about', items: [
				// 'About'
			]
		}
	];
	
	config.extraPlugins = ['replaceTagNameByBsquochoai','codemirror','powrsocialfeed','insertpre','pre','lineheight'];

	config.line_height = '1;1.5;2;2.5;3;3.5;4;4.5;5;5.5;6;6.5;7;7.5;8;8.5;9;9.5;10;10.5';

	config.enterMode      = CKEDITOR.ENTER_BR;
	config.shiftEnterMode = CKEDITOR.ENTER_P;

	config.allowedContent       = true;
	CKEDITOR.dtd.$removeEmpty.i = 0;
};
