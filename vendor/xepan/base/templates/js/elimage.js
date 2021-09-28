$.each({
	// Useful info about mouse clicking bug in jQuery UI:
	// http://stackoverflow.com/questions/6300683/jquery-ui-autocomplete-value-after-mouse-click-is-old-value
	// http://stackoverflow.com/questions/7315556/jquery-ui-autocomplete-select-event-not-working-with-mouse-click
	// http://jqueryui.com/demos/autocomplete/#events (check focus and select events)

	myelimage: function(other_field,$replace_value){
		var q=this.jquery;
		var fm = $('<div/>').dialogelfinder({
												url : 'index.php?page=xepan_base_elconnector&cut_page=true',
												lang : 'en',
												width : 840,
												destroyOnClose : true,
												// autoOpen:true,
												getFileCallback : function(files, fm) {
													$(other_field).val(files.url.replace($replace_value, ''));

												},
												onlyMimes: ['image'],
												commandsOptions : {
													getfile : {
														oncomplete : 'close',
														folders : false
													}
												}
											}).dialogelfinder('instance');

	}

},$.univ._import);

/*		$(this.jquery).click(function(event) {
			alert('HI');
			// var fm = $('<div/>').dialogelfinder({
			// 										url : 'elfinder/php/connector.php',
			// 										lang : 'en',
			// 										width : 840,
			// 										destroyOnClose : true,
			// 										getFileCallback : function(files, fm) {
			// 											$(other_field).val(files.url);
			// 										},
			// 										commandsOptions : {
			// 											getfile : {
			// 												oncomplete : 'close',
			// 												folders : true
			// 											}
			// 										}
			// 									}).dialogelfinder('instance');
		});
	}
*/