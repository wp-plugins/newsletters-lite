/**
 * Newsletters TinyMCE Plugin
 * @author Tribulant Software
 */

(function() {
	tinymce.PluginManager.requireLangPack("Newsletters");

	tinymce.create('tinymce.plugins.Newsletters', {
		init: function(ed, url) {
			this.url = url;
		
			ed.addCommand('mceNewsletters', function() {			
				ed.windowManager.open({
					file: ajaxurl + '?action=newsletters_tinymce_dialog',
					width : 440,
					height : 400,
					inline : 1
				}, {
					plugin_url : url
				});
			});

			ed.addButton('Newsletters', {
				title : 'Newsletters.desc',
				cmd : 'mceNewsletters',
				//image : url + '/newsletters.png'
			});	
		},		
		createControl : function(n, cm) {
			return null;
		},
		getInfo : function() {
			return {
				longname : 'Newsletters TinyMCE Plugin',
				author : 'Tribulant Software',
				authorurl : 'http://tribulant.com',
				infourl : 'http://tribulant.com',
				version : "1.0"
			};
		}
	});

	tinymce.PluginManager.add('Newsletters', tinymce.plugins.Newsletters);
})();