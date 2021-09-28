$.each({
	xEpanColorPicker: function(options){
		$(this.jquery).colorpicker({
			parts:'full',
	        alpha:true,
	        showOn:'both',
	        buttonColorize:true,
	        showNoneButton:true,
	        position: {
				my: 'center',
				at: 'center',
				of: window
			},
			modal: true,
			// 'colorFormat': 'RGBA',
			buttonImage:'vendor/xepan/cms/templates/css/colorpicker/images/ui-colorpicker.png',
			revert:true,
			open: function(){
				// console.log($(current_selected_component).css('background-color'));
			},
			cancel:function(){
				// console.log('Color panel cancled');
			}
			// ok: function(event,color){
			// 	console.log('ok '+color.formatted);
			// },
			// cancel: function(event,color){
			// 	console.log('cancel '+color.formatted);
			// }

		});
		// $(this.jquery).pickAColor();
	}
},$.univ._import);