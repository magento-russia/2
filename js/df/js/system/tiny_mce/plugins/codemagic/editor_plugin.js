(function () {
    tinymce.PluginManager.requireLangPack('codemagic');
	tinymce.create('tinymce.plugins.CodeMagic', {

		init: function (ed, url) {
			
            // Register commands
			ed.addCommand('mceCodeMagic', function() {
                ed.windowManager.open({
                    file : url + '/codemagic.htm',
                    width : 1100,
                    height : 600,
                    inline : 1,
                    maximizable: true
                }, {
                    plugin_url : url
                });
            });

			// Register buttons
			ed.addButton('codemagic', {
				title: 'codemagic.editor_button', 
                cmd: 'mceCodeMagic', 
                image: url + '/img/code.png'
			});

			ed.onNodeChange.add(function(ed, cm, n, co) {
                cm.setDisabled('link', co && n.nodeName != 'A');
                cm.setActive('link', n.nodeName == 'A' && !n.name);
            });
		},

		getInfo: function () {
			return {
				longname: 'Редактор HTML'
				,author: 'Дмитрий Федюк',
				authorurl: 'http://magento-forum.ru/',
				version: '1.0.0'
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('codemagic', tinymce.plugins.CodeMagic);
})();