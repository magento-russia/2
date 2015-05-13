/**
*
*  AJAX IFRAME METHOD (AIM)
*  http://www.webtoolkit.info/
*
**/

AIM = {

	frame : function(c) {

		var n = 'f' + Math.floor(Math.random() * 99999);
		var d = document.createElement('DIV');
		d.innerHTML = '<iframe style="display:none" src="about:blank" id="'+n+'" name="'+n+'" onload="AIM.loaded(\''+n+'\')"></iframe>';
		document.body.appendChild(d);

		var i = document.getElementById(n);
		if (c && typeof(c.onComplete) == 'function') {
			i.onComplete = c.onComplete;
		}

		return n;
	},

	form : function(f, name) {
		f.setAttribute('target', name);
	},

	submit : function(f, c) {
		AIM.form(f, AIM.frame(c));
		if (c && typeof(c.onStart) == 'function') {
			return c.onStart();
		} else {
			return true;
		}
	},

	loaded : function(id) {
		var i = document.getElementById(id);
		if (i.contentDocument) {
			var d = i.contentDocument;
		} else if (i.contentWindow) {
			var d = i.contentWindow.document;
		} else {
			var d = window.frames[id].document;
		}
		if (d.location.href == "about:blank") {
			return;
		}

		if (typeof(i.onComplete) == 'function') {
			i.onComplete(d.body.innerHTML);
		}
	}

}

tinyMCEPopup.requireLangPack();

var SimpleuploadDialog = {
	init : function() {
		this.img    = document.getElementById('simpleupload_loading');
		this.form   = document.getElementById('simpleupload_form');
		this.url    = tinyMCEPopup.getParam('simpleupload_connector_path');
		this.input  = document.getElementById('simpleupload_file');
		this.errors = document.getElementById('simpleupload_errors');
		this.path   = tinyMCEPopup.getParam('simpleupload_images_path');

		this.use_resize = tinyMCEPopup.getParam('advimagescale_maintain_aspect_ratio');
		this.max_width  = tinyMCEPopup.getParam('advimagescale_max_width');

		if(this.form && this.url)
		{
			this.form.action = this.url;
		}
	},

	startCallback: function()
	{
		SimpleuploadDialog.img.style.display = 'block';
	},

	completeCallback: function(req)
	{
		var ed = tinyMCEPopup.editor, dom = ed.dom;

		SimpleuploadDialog.img.style.display = 'none';
		eval('var json=' + req);
		if(json.success == 0 && json.errors)
		{
			SimpleuploadDialog.errors.innerHTML = ed.getLang(json.errors);
			if(json.error_not_translate)
			{
				SimpleuploadDialog.errors.innerHTML += ' - ' + json.error_not_translate;
			}
		}
		else if(json.success == 1 && json.filename)
		{
			var width  = json.width;
			var height = json.height;
			if(SimpleuploadDialog.use_resize)
			{
				  if(width > SimpleuploadDialog.max_width)
				  {	                  var width_in  = SimpleuploadDialog.max_width;

	                  var ratio     = (width_in / width);
	                  var height_in = parseInt(height*ratio);
                  }
                  else
                  {                     var width_in  = width;
                     var height_in = height;
                  }
			}
			var img    = SimpleuploadDialog.path + '/' + json.filename + (width && height ? '?w=' + ( width_in ? width_in : width ) + '&h=' +(height_in ? height_in : height) : ''); /* for advimagescale plugin */
            var html   = '<img src="' + img + '" ' + (width_in ? 'width="'+width_in+'"' : '') +  (height_in ? ' height="' +  height_in + '"' : '') + ' >';

			ed.execCommand('mceInsertContent', false, html);
			tinyMCEPopup.close();
			//return tinyMCE.dom.Event.cancel(e);
		}
	}
};
tinyMCEPopup.onInit.add(SimpleuploadDialog.init, SimpleuploadDialog);
