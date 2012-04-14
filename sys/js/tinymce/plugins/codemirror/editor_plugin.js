(function () {
	tinymce.create('tinymce.plugins.CodeMirror', {

		init: function (ed, url) {
			var t = this;
			t.editor = ed;
			this.CMURL = url;
			//load editarea script
			tinymce.DOM.add(tinymce.DOM.select('head'), 'script', { src: url + '/js/codemirror.js' });
			tinymce.DOM.add(tinymce.DOM.select('head'), 'script', { src: url + '/js/htmlmixed.js' });
			tinymce.DOM.add(tinymce.DOM.select('head'), 'script', { src: url + '/js/xml.js' });
			tinymce.DOM.add(tinymce.DOM.select('head'), 'script', { src: url + '/js/css.js' });
			tinymce.DOM.add(tinymce.DOM.select('head'), 'script', { src: url + '/js/javascript.js' });

			//load CSS
			tinymce.DOM.loadCSS(url + '/css/codemirror.css');
			tinymce.DOM.loadCSS(url + '/css/xml.css');
			tinymce.DOM.loadCSS(url + '/css/javascript.css');
			tinymce.DOM.loadCSS(url + '/css/css.css');

			// Register commands
			ed.addCommand('mceCodeMirror', t._editArea, t);

			// Register buttons
			ed.addButton('codemirror', {
				title: 'Advanced source editor', cmd: 'mceCodeMirror',
				image: url + '/img/html.png'
			});

			ed.onNodeChange.add(t._nodeChange, t);
		},

		// source - codemirror.net/
		getInfo: function () {
			return {
				longname: 'CodeMirror 2 integration for TinyMCE',
				author: 'Clint Cameron',
				authorurl: 'http://www.sencia.ca',
				version: '0.6'
			};
		},

		_nodeChange: function (ed, cm, n) {
			var ed = this.editor;
			//not used for the moment
		},

		_editArea: function () {
			var ed = this.editor, formObj, os, i, elementId;
			this._showEditArea();
		},

		_showEditArea: function () {
			var n, t = this, ed = t.editor, s = t.settings, r, mf, me, td;
			baseurl = this.CMURL;
			areaId = ed.getElement().id;
			//mw = ed.getContainer().firstChild.style.width;
			//mh = ed.getContainer().firstChild.style.height;
			mw = document.getElementById('html_tbl').style.width;
			mh = document.getElementById('html_ifr').style.height;
			ed.hide();

			t.CMEditor = CodeMirror.fromTextArea(document.getElementById(areaId), {
				mode: 'text/html',
				tabMode: 'indent'
			});

			fc_click = function () {
				ed.show();
				ed.setContent(t.CMEditor.getValue());
				CMEditorWrapper.parentNode.removeChild(CMEditorWrapper.toolBarDiv);
				CMEditorWrapper.parentNode.removeChild(CMEditorWrapper);
				t.CMEditor = null;
			};

			CMEditorWrapper = t.CMEditor.getWrapperElement();

			CMEditorWrapper.id = 'frame_' + areaId;
			CMEditorWrapper.style.width = mw;
			CMEditorWrapper.style.height = mh;

			var i = 0;
			for (i = 0; i <= t.CMEditor.lineCount(); i++) {
				t.CMEditor.indentLine(i);
			}

			CMEditorWrapper.toolBarDiv = document.createElement("div");
			CMEditorWrapper.toolBarDiv.id = 'div_' + areaId
			CMEditorWrapper.toolBarDiv.style.width = mw;
			CMEditorWrapper.toolBarDiv.className = 'CodeMirrorToolBar';
			CMEditorWrapper.parentNode.insertBefore(CMEditorWrapper.toolBarDiv, CMEditorWrapper);

			btn = tinymce.DOM.add(CMEditorWrapper.toolBarDiv, 'input', { type: 'button', value: 'Editor', 'class': 'CMBtn', id: 'Btn_' + areaId });
			btn.onclick = fc_click;

		}

	});

	// Register plugin
	tinymce.PluginManager.add('codemirror', tinymce.plugins.CodeMirror);
})();