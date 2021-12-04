/*globals $, svgCanvas, svgEditor*/
/*jslint regexp:true*/
// TODO: Might add support for "exportImage" custom
//   handler as in "ext-server_opensave.js" (and in savefile.php)

svgEditor.addExtension("php_savefile", {
	callback: function() {
		'use strict';
		function getFileNameFromTitle () {
			var name;
			name='url';
			var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
		    if (results==null){
		       return null;
		    }
		    else{
		       return results[1] || 0;
		    }
			var title = svgCanvas.getDocumentTitle();
			return $.trim(title);
		}
		var save_svg_action = '../../../../../../index.php?page=xepan_cms_savesvg';
		svgEditor.setCustomHandlers({
			save: function(win, data) {
				var svg = '<?xml version="1.0" encoding="UTF-8"?>\n' + data,
					filename = decodeURI(getFileNameFromTitle());
							
				$.post(save_svg_action, {output_svg: svg, filename: filename})
					.done(function( ret){
						if(ret == 'saved')
							alert('saved');
						else
							alert('could not save');
					});
			}
		});
	}
});
