var editorConfig;
var editorUserConfig;
var editorTheme;
var editorMode;

jQuery(document).ready(function () {

	// setup codemirror instances and functions

	if(typeof editorTheme === 'undefined'){
		editorTheme = 'default';
	}

	var editorMode = 'php';

	// cmfold = function(cm){cm.foldCode(cm.getCursor(),{"widget":"...","minFoldSize":2});};

	editorConfig = {
		mode                      : editorMode,
		theme                     : editorTheme,
		lineNumbers               : true,
		indentWithTabs            : true,
		indentUnit                : 4,
		enterMode                 : "keep",
		tabMode                   : "shift",
		fixedGutter               : true,
		styleActiveLine           : true,
		matchBrackets             : true, // highlight matching brackets when cusrsor is next to one
        autoCloseBrackets         : true, // auto close brackets when typing
		autoCloseTags             : true, // auto close tags when typing
		// showTrailingSpace         : true, // adds the CSS class cm-trailingspace to stretches of whitespace at the end of lines.
		highlightSelectionMatches : true, // {showToken : /\w/}, for word boundaries
		// viewportMargin            : Infinity, // for autosizing
		// lineWrapping           : true,
		// matchTags              : true, // adds class CodeMirror-matchingtag to tags contents
		foldGutter                : true,
		gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"],
		saveFunction              : function(cm) { customSave(cm); },
		extraKeys: {
			// "Ctrl-Q" : function(cm) { foldFunc(cm, cm.getCursor().line); },
			// "Ctrl-Q" : function(cm) { cmfold(cm) },
			"F11"    : function(cm) { setFullScreen(cm, !isFullScreen(cm)); },
			"Esc"    : function(cm) { if (isFullScreen(cm)) setFullScreen(cm, false); },
			"Ctrl-S" : function(cm) { customSave(cm); },
            "Ctrl-Space" : "autocomplete"
		}
	};

	CodeMirror.commands.autocomplete = function(cm) {
		CodeMirror.showHint(cm); // auto
		// CodeMirror.showHint(cm, CodeMirror.hint.anyword);
	};

	// do not know what this does, looks like old ctrl+q fold debouncer
	// function keyEvent(cm, e) {
	//	if (e.keyCode == 81 && e.ctrlKey) {
	//		if (e.type == "keydown") {
	//			e.stop();
	//			setTimeout(function() {foldFunc(cm, cm.getCursor().line);}, 50);
	//		}
	//		return true;
	//	}
	// }

	if(typeof editor_defTheme != 'undefined' && editor_defTheme != 'default'){
		var parts = editor_defTheme.split(' ');
		loadjscssfile("template/js/codemirror/theme/"+parts[0]+".css", "css");
	}

	/**
	 * editorFromTextarea replaces a textarea with a codemirror editor
	 * @param dom or string of a textarea
	 * @param editorConfig config obj
	 * @param editorUserConfig config to merge
	 */
	$.fn.editorFromTextarea = function(textarea){

		if (typeof editorConfig === "undefined" || editorConfig === null) editorConfig = {};
		if (typeof editorUserConfig === "undefined" || editorUserConfig === null) editorUserConfig = {};

		var editor = CodeMirror.fromTextArea(textarea, jQuery.extend({}, editorConfig, editorUserConfig));

		// add reference to this editor to the textarea
		$(textarea).data('editor', editor);

		editor.on('change', function(cm){
			cm.hasChange = true;
		});

		// var foldFunc = CodeMirror.newFoldFunction(CodeMirror.braceRangeFinder,'...');
		// editor.on("gutterClick", cmfold);

		// add resisable capability to codemirror
		$(editor.getWrapperElement()).resizable({
			// helper: "outline", // less intensive resizing
			autoHide : true, // hide the resize grips when unfocused
			minHeight: 25,
			start: function(e,ui) {
				ui.originalElement.css('min-height','25px'); // clamp min height				
			},
			resize: function(e,ui) {
				editor.setSize(null, $(this).height());
			},
			stop: function(e,ui) {
				// Debugger.log(ui.originalElement);
				ui.originalElement.css('min-height','25px'); // clamp min height
				ui.originalElement.css('max-height','none');
				editor.refresh();
			}
		});

		// replace jqueryui resize handle with custom
		$(editor.getWrapperElement()).find($('.ui-resizable-se')).removeClass('ui-icon');
		// $(editor.getWrapperElement()).find($('.ui-resizable-se')).addClass('handle fa fa-th-large');
		$(editor.getWrapperElement()).find($('.ui-resizable-se')).addClass('handle');
		$(editor.getWrapperElement()).find($('.ui-resizable-se')).html('◢'); // U+25E2	e2 97 a2 BLACK LOWER RIGHT TRIANGLE

		fullscreen_button(editor);

		return editor;
	};

	// apply codemirror to class of .code_edit
	$(".code_edit").each(function(i,textarea) {	jQuery().editorFromTextarea(textarea); });

	function editorScrollVisible(cm){
		var wrap = cm.getWrapperElement();
		var scroller =  $(wrap).find('.CodeMirror-vscrollbar').css('display');
		return scroller == "block";
	}

	function customSave(cm){
		Debugger.log('saving');
		$("#submit_line input.submit").trigger('submit');
	}

    function winHeight() {
      return window.innerHeight || (document.documentElement || document.body).clientHeight;
    }

    function isFullScreen(cm) {
      return /\bCodeMirror-fullscreen\b/.test(cm.getWrapperElement().className);
    }

    function toggleFullscreen(cm){
		setFullScreen(cm, !isFullScreen(cm));
    }

    function setFullScreen(cm, full) {
      var wrap = cm.getWrapperElement();
      if (full) {
        wrap.className += " CodeMirror-fullscreen";
        $(wrap).data('normalheight',$(wrap).css('height')); // store original height
        wrap.style.height = winHeight() + "px";
        document.documentElement.style.overflow = "hidden";
      } else {
        wrap.className = wrap.className.replace(" CodeMirror-fullscreen", "");
        wrap.style.height = $(wrap).data('normalheight'); // restore original height
        document.documentElement.style.overflow = "";
      }
      cm.refresh();
    }

    // adjust for window resizing awhen in fullscreen
	CodeMirror.on(window, "resize", function(e) {
        var showing = document.body.getElementsByClassName("CodeMirror-fullscreen")[0];
        if (!showing) return;
        showing.CodeMirror.getWrapperElement().style.height = winHeight() + "px";
    });

	function setThemeSelected(theme){
		$("#cm_themeselect").val(theme);
	}

	function fullscreen_button(cm){
		var cmwrapper = $(cm.getWrapperElement());
		var scrolled = editorScrollVisible(cm);

		var button = cmwrapper.find(".overlay_but_fullscrn a");
		// Debugger.log(button);

		// if no button create it and add to editor
		if(button.length === 0){
			buttonhtml = $('<div class="overlay_but_fullscrn"></div>');
			button = $('<a href="javascript:void(0)"><i class="fa fa-arrows-alt"></i></a>').appendTo(buttonhtml);
			buttoncont = buttonhtml.appendTo(cmwrapper);
			button.on('click', cm,function(e){
				toggleFullscreen(e.data);
			});

			// events to watch for to adjust positioning accordingly
			cm.on('change', fullscreen_button);
			cm.on('update', fullscreen_button);
		}

		// adjust fullscreen button visibility and position
		button.toggleClass("scrolled",scrolled); // scrollbars
		button.toggleClass("hidden",cmwrapper.height() <= 25); // too small
	}


	setThemeSelected(editorTheme);
	cm_theme_update(editorTheme);
});
