current_selected_component = undefined;
origin = 'page';
xepan_drop_component_html= '';
xepan_editor_element = null;
xepan_component_selector = null;
xepan_component_layout_optioned_added = false;
xepan_save_and_take_snapshot=false;
xepan_global_component_options = {};

jQuery.widget("ui.xepanEditor",{
	options:{
		base_url:undefined,
		file_path:undefined,
		template_file_path:undefined,
		template:undefined,
		save_url:undefined,
		template_editing:undefined,
		tools:{},
		basic_properties: undefined,
		component_selector: '.xepan-component',
		webpage_id: undefined,
		webtemplate_id: undefined
	},

	topbar:{},
	leftbar:{},
	rightbar:{},

	_create: function(){
		var self = this;
		current_selected_component = $('body');
		xepan_editor_element = self.element;
		xepan_component_selector = self.options.component_selector;
		
		// if(self.options.template_editing){
		// 	$('.xepan-page-wrapper').removeClass('xepan-sortable-component');
		// }else{
		// 	$('.xepan-page-wrapper').addClass('xepan-component');
		// 	$('.xepan-page-wrapper').addClass('xepan-sortable-component');
		// 	$('body .xepan-component:not(.xepan-page-wrapper):not(.xepan-page-wrapper .xepan-component)')
		// 	.dblclick(function(ev) {
		// 		console.log($(this));	
		// 		ev.preventDefault();ev.stopPropagation();
		// 		$('<div><div>This component is in common portion of all pages called "Page Template", To Edit this section plese open Pages in SideBar and click "EDIT PAGE TEMPLATE" Or Click "Edit Template Now" below</div>, <img src="vendor/xepan/cms/templates/images/page-template-hint.png"> <div>Enter in page Template Editing ?</div></div>')
		// 		.dialog({
		// 			modal: true,
		// 			width: 400,
		// 			buttons: {
		// 				"Edit Template Now" : function(){
		// 					$(self.element).xepanEditor('editTemplate');
		// 				},
		// 				Cancel: function(){
		// 					$( this ).dialog( "close" );
		// 				}
		// 			}
		// 		});
		// 	});;
		// }

		// console.log(self.options);

		self.setupEnvironment();
		self.setupTools();
		// self.setupToolbar();
		self.setUpShortCuts();
		self.setupEditableText();
		self.cleanup(); // Actually these are JUGAAD, that must be cleared later on
		xepan_global_component_options = {editing_template:self.options.template_editing,component_selector: self.options.component_selector,editor_id:self.options.editor_id};
		$(self.options.component_selector).each(function(index, el) {
			$(el).xepanComponent(xepan_global_component_options);
		});
	},

	setupEnvironment: function(){
		var self = this;

		// throw self html out of body
		$(self.element).insertAfter('body');


		// right bar
		self.rightbar = $('<div id="xepan-cms-toolbar-right-side-panel" class="sidebar sidebar-right bootstrap-iso" style="right: -230px;" data-status="opened"></div>').insertAfter('body');
		// basic and selection tools

		self.generic_tool_wrapper = $('<div class="xepan-cms-group-panel clearfix xepan-cms-tool"></div>').appendTo(self.rightbar);
		self.generic_tool = $('<div class="btn-group btn-group-xs" style="padding-bottom:2px;"></div>').appendTo(self.generic_tool_wrapper);
		
		$('<button class="btn btn-primary">Selection</button>').appendTo(self.generic_tool);
		self.selection_previous_sibling = $('<button id="epan-component-selection-previous-sibling" type="button" title="Previous Sibling" class="btn btn-default"><i class="fa fa-arrow-left"></i></button>').appendTo(self.generic_tool);
		self.selection_next_sibling = $('<button id="epan-component-selection-next-sibling" type="button" title="Next Sibling" class="btn btn-default"><i class="fa fa-arrow-right"></i></button>').appendTo(self.generic_tool);
		self.selection_parent = $('<button id="epan-component-selection-parent" type="button" title="Parent" class="btn btn-default"><i class="fa fa-arrow-up"></i></button>').appendTo(self.generic_tool);
		self.selection_child = $('<button id="epan-component-selection-child" type="button" title="Child/Next" class="btn btn-default"><i class="fa fa-arrow-down"></i></button>').appendTo(self.generic_tool);

		$(self.selection_previous_sibling).click(function(event){
			ctrlShiftLeftSelection(event);
		});

		$(self.selection_next_sibling).click(function(event){
			ctrlShiftRightSelection(event);
		});

		$(self.selection_parent).click(function(event) {
			ctrlShiftUpSelection(event);
		});

		$(self.selection_child).click(function(event){
			tabSelection(event);
		});


		self.move_wrapper = $('<div class="btn-group btn-group-xs" style="padding-bottom:2px;"></div>').appendTo(self.generic_tool_wrapper);
		$('<button class="btn btn-primary">Move</button>').appendTo(self.move_wrapper);
		self.move_left = $('<button id="epan-component-move-left" type="button" title="move left" class="btn btn-default"><i class="fa fa-arrow-left">Left</i></button>').appendTo(self.move_wrapper);
		self.move_right = $('<button id="epan-component-move-right" type="button" title="move right" class="btn btn-default">Right<i class="fa fa-arrow-right"></i></button>').appendTo(self.move_wrapper);
		$(self.move_left).click(function(event){
			componentMoveLeft(event);
		});

		$(self.move_right).click(function(event){
			componentMoveRight(event);
		});

		// duplicate
		self.duplicate_wrapper = $('<div class="epan-component-duplicate-wrapper btn btn-group btn-group-xs"></div>').appendTo(self.generic_tool_wrapper);
		self.duplicate_btn = $('<button id="epan-component-duplicate-child" class="btn btn-primary"><span class="fa fa-copy">&nbsp;</span>Duplicate</button>').appendTo(self.duplicate_wrapper);
		$(self.duplicate_btn).click(function(event){
			duplicateComponent(event);
		});

		//- Extra Buttons this button action in toolbar.php file

		self.extra_html_wrapper = $('<div class="btn btn-group btn-group-xs" style="padding:0px;"></div>').appendTo(self.generic_tool_wrapper);
 		// Create Layout Button
 		extra_html = '<div class="btn btn-primary btn-xs" id="xepan-tool-createlayoutbtn" title="Create Layout"><i class="fa fa-plus"></i>&nbsp;Layout'+
 						'<div class="xepan-create-layout-dialoge" style="display:none;">'+
 							'<p> All form fields are required.</p>'+
							'<div class="xepan-create-layout-form">'+
								'<fieldset>'+
									'<label for="name" style="width:100%;">Name</label>'+
									'<input type="text" name="name" id="xepan-layout-name" style="width:100%;"/>'+
									'<label for="name" style="width:100%;"> Html</label>'+
									'<textarea type="textarea" name="layout_html" id="xepan-layout-html" style="width:100%;height:150px;" />'+
									'<label for="Image Base 64 Code" style="width:100%;"> Image Base 64 </label>'+
									'<textarea type="textarea" name="layout_img_base" id="xepan-layout-image-64" style="width:100%;height:150px;" />'+
								'</fieldset>'+
							'</div>'+
						'</div>'+
					'</div>';

		// Override template
		extra_html += '<div class="btn btn-danger btn-xs" id="override-xepan-tool-template" title="Override HTML Templates"><i class="fa fa-code"></i>Html</div>';
		// Custom CSS
		extra_html += '<div class="btn btn-success btn-xs" id="xepan-tool-mystyle" title="Edit Custom CSS"><i class="fa fa-pencil"></i>CSS</div>';
		// inspector
		extra_html += '<div class="btn btn-warning btn-xs" id="xepan-tool-inspector" title="inspector"><i class="fa fa-arrows"></i></div>';
		$(extra_html).appendTo(self.extra_html_wrapper);

		// right bar content
		$('<div class="xepan-cms-tool xepan-cms-tool-option-panel" style="margin-bottom:0px;">OPTIONS</div>').appendTo(self.rightbar);
		
		self.rightbar_toggle_btn = $('<div class="toggler"><span class="fa fa-chevron-left fa-2x" style="display: block;">&nbsp;</span> <span class="fa fa-chevron-right fa-2x" style="display: none;">&nbsp;</span></div>').appendTo(self.rightbar);
		$(self.rightbar_toggle_btn).click(function(){
			$('#xepan-cms-toolbar-right-side-panel').toggleClass('toggleSideBar');
		});

		$('#override-xepan-tool-template').click(function(event) {
			if(typeof current_selected_component == 'undefined' || current_selected_component.is($('body'))) {
				$.univ().dialogOK('Please select any component','No component is selected, please select any to override template');
				return;
			}
			$.univ().frameURL('Override Tool Template',{'0':'/?page=xepan_cms_overridetemplate','options':JSON.stringify($(current_selected_component).attr()),'xepan-tool-to-clone':$(".xepan-tools-options div[for-xepan-component]:visible").attr('for-xepan-component')});
		});

		// disable all clicks
		$('body').find('a, .btn').click(function(ev){ ev.preventDefault();});
		$('body').find('i.xepan-cms-icon').removeAttr('onclick');


		// left bar
		self.leftbar = $('<div id="xepan-cms-toolbar-left-side-panel" class="sidebar sidebar-left bootstrap-iso" style="left: -230px;" data-status="opened"></div>').insertAfter('body');
		// right bar content
		self.leftbar_toggle_btn = $('<div class="toggler"><span class="fa fa-chevron-right fa-2x" style="display: block;">&nbsp;</span> <span class="fa fa-chevron-left fa-2x" style="display: none;">&nbsp;</span></div>').appendTo(self.leftbar);
		$(self.leftbar_toggle_btn).click(function(){
			$('#xepan-cms-toolbar-left-side-panel').toggleClass('toggleSideBar');
		});
		
		// // top bar
		// self.topbar = $('<div id="xepan-cms-toolbar-top-side-panel" class="container sidebar sidebar-top toggleSideBar" style="top:-50px;" data-status="opened"></div>').insertAfter('body');
		// // top bar content
		// $('<h2>Top Bar</h2>').appendTo(self.topbar);
		// self.topbar_toggle_btn = $('<div class="toggler"><span class="glyphicon glyphicon-chevron-down" style="display: block;">&nbsp;</span> <span class="glyphicon glyphicon-chevron-up" style="display: none;">&nbsp;</span></div>').appendTo(self.topbar);
		// $(self.topbar_toggle_btn).click(function(){
		// 	$('#xepan-cms-toolbar-top-side-panel').toggleClass('toggleSideBar');
		// });
		
		self.editor_helper_wrapper = $('<div class="xepan-cms-editor-helper-wrraper">').appendTo(self.leftbar);
		// page and template management
		self.setUpPagesAndTemplates();
		// save and snapshot btn
		// var save_tool_bar = $('<div class="btn-toolbar-no-need" role="toolbar">').appendTo(self.editor_helper_wrapper);
		var save_btn_group = $('<div class="btn-group btn-group-justified" role="group">').appendTo(self.editor_helper_wrapper);
		// var snapshot_btn = $('<button id="save-as-snapshot" title="Save as Snapshot" type="button" class="btn btn-default btn-sm" ><span class="fa fa-camera-retro" aria-hidden="true"> Snapshot</span></button>').appendTo(save_btn_group);
		var change_theme 			= $('<div class="btn-group btn-group-xs" role="group"><button id="xepan-change-template-theme" title="Change Theme" class="btn btn-warning">Theme<span class="fa fa-web"></span></button></div>').appendTo(save_btn_group);
		var save_btn 				= $('<div class="btn-group btn-group-xs" role="group"><button id="xepan-savepage-btn" title="Save Page" type="button" class="btn btn-success"><span class="fa fa-floppy-o"></span> Save</button></div>').appendTo(save_btn_group);
		var save_btn_with_snapshot 	= $('<div class="btn-group btn-group-xs" role="group"><button id="xepan-savepage-btn-with-snapshot" title="Save & Snapshot" type="button" class="btn btn-success"><span class="fa fa-floppy-o"></span>/<span class="fa fa-camera"></span></button></div>').appendTo(save_btn_group);
		var logout_btn 				= $('<div class="btn-group btn-group-xs" role="group"><button id="xepan-logout-btn" title="Logout" type="button" class="btn btn-danger"><span class="fa fa-power-off"></span></button></div>').appendTo(save_btn_group);

		$(change_theme).click(function(event) {
			$.univ().frameURL('Change Template','index.php?page=xepan_cms_theme&cut_page=1');
		});

		$(save_btn).click(function(){
			xepan_save_and_take_snapshot = false;
			$(self.element).xepanEditor('savePage');
		});

		$(save_btn_with_snapshot).click(function(){
			var snapshot_name = prompt("Please enter name for snapshot, [Only Page Content snapshot will be saved, NOT PAGE TEMPLATE]", Date());
			if(snapshot_name == null ) {
				alert('Canceled, not saving page');
				return;
			}

			xepan_save_and_take_snapshot = snapshot_name;
			$(self.element).xepanEditor('savePage');
			xepan_save_and_take_snapshot = false;
		});

		$(logout_btn).click(function(event) {
			window.location.href='?page=logout';
		});

		// show border and easy drop
		var easy_wrapper = $('<div class="xepan-cms-easy-drop-wrapper xepan-cms-tool btn-group btn-group-justified" style="margin:0px;padding:0px;">').appendTo(self.editor_helper_wrapper);
		var easy_drop = $('<label for="epan-component-extra-padding" style="font-weight:normal;" class="btn btn-primary btn-xs"><input id="epan-component-extra-padding" aria-label="Checkbox for following text input" type="checkbox"> Easy Drop</label>').appendTo(easy_wrapper);
		var show_border = $('<label for="epan-component-border" style="font-weight:normal;" class="btn btn-primary  btn-xs"><input id="epan-component-border" aria-label="Checkbox for following text input" type="checkbox"> Show Border</label>').appendTo(easy_wrapper);
		// var hide_temp_page_wrapper = $('<div class="xepan-cms-hide-temp-page-wrapper xepan-cms-tool btn-group btn-group-justified" style="margin:0px;padding:0px;">').appendTo(self.editor_helper_wrapper);
		// var hide_template = $('<label for="epan-hide-template-content" style="font-weight:normal;" class="btn btn-primary btn-xs"><input id="epan-hide-template-content" aria-label="Checkbox for following text input" type="checkbox"> Hide Page Template</label>').appendTo(hide_temp_page_wrapper);
		// var hide_page = $('<label for="epan-hide-page-content" style="font-weight:normal;" class="btn btn-primary btn-xs"><input id="epan-hide-page-content" aria-label="Checkbox for following text input" type="checkbox"> Hide Page</label>').appendTo(hide_temp_page_wrapper);

		/*Component Editing outline show border*/
		$("#epan-component-border").click(function(event) {
		    if($('#epan-component-border:checked').size() > 0){
		        $('.xepan-component').addClass('component-outline');
		    }else{
		        $('.xepan-component').removeClass('component-outline');
		    }
		});

		/*Drag & Drop Component to Another  Extra Padding top & Bottom*/
		$('#epan-component-extra-padding').click(function(event) {
		    if($('#epan-component-extra-padding:checked').size() > 0){
		        $('.xepan-sortable-component').addClass('xepan-sortable-extra-padding');
		    }else{
		        $('.xepan-sortable-component').removeClass('xepan-sortable-extra-padding');
		    }
		});

		/* Hide page template */
		// $('#epan-hide-template-content').change(function(event) {
		// 	if($('#epan-hide-template-content:checked').size() > 0 ){
		// 		if($('#epan-hide-page-content:checked').size() > 0 ) $('#epan-hide-page-content').click();
		// 		$('.xepan-page-wrapper').wrap('<div class="xepan-page-wrapper-temp_spot"></div>')
		// 		$('.xepan-page-wrapper').appendTo('.xepan-v-body');
		// 		$('.xepan-v-body').children().filter(":not(.xepan-page-wrapper)").hide();
		// 	}else{
		// 		$('.xepan-v-body').children().filter(":not(.xepan-page-wrapper)").show();
		// 		$('.xepan-page-wrapper').appendTo('.xepan-page-wrapper-temp_spot');
		// 		$('.xepan-page-wrapper').unwrap();
		// 	}
		// });

		// $('#epan-hide-page-content').change(function(event) {
		// 	if($('#epan-hide-page-content:checked').size() > 0 ){
		// 		if($('#epan-hide-template-content:checked').size() > 0 ) $('#epan-hide-template-content:checked').click();

		// 		$('.xepan-page-wrapper').wrap('<div class="xepan-hide-page-content-wrapper"></div>')
		// 		$('<div class="xepan-hide-page-content-wrapper-heading"> <h1 align="center" >PAGE CONTENT AREA</h1><h3 align="center" > It helps you focus on Page Template only.. </h3> </div>').appendTo('.xepan-hide-page-content-wrapper');
		// 		$('.xepan-page-wrapper').hide();
		// 	}else{
		// 		$('.xepan-hide-page-content-wrapper .xepan-hide-page-content-wrapper-heading').remove();
		// 		$('.xepan-page-wrapper').unwrap().show();
		// 	}
		// });

		// settings up tool buttons
		// var responsive_tool_bar = $('<div class="btn-toolbar" role="toolbar">').appendTo(self.editor_helper_wrapper);
		var responsive_btn_group =	$('<div class="btn-group btn-group-justified">').appendTo(self.editor_helper_wrapper);
		var $screen_reset_btn = $('<div class="btn-group btn-group-xs" role="group"><button id="epan-editor-preview-screen-reset" title="Reset to original Preview" type="button" class="btn btn-default"><span class="fa fa-undo" aria-hidden="true"></span></button></div>').appendTo(responsive_btn_group);
		var $screen_lg_btn = $('<div class="btn-group btn-group-xs" role="group"><button id="epan-editor-preview-screen-lg" title="Desktop Preview" type="button" class="btn btn-default"><span class="fa fa-desktop" aria-hidden="true"></span></button></div>').appendTo(responsive_btn_group);
		var $screen_md_btn = $('<div class="btn-group btn-group-xs" role="group"><button id="epan-editor-preview-screen-md" title="Laptop Preview" type="button" class="btn btn-default" ><span class="fa fa-laptop" aria-hidden="true"></span></button></div>').appendTo(responsive_btn_group);
		var $screen_sm_btn = $('<div class="btn-group btn-group-xs" role="group"><button id="epan-editor-preview-screen-sm" title="Tablet Preview" type="button" class="btn btn-default" ><span class="fa fa-tablet" aria-hidden="true"></span></button></div>').appendTo(responsive_btn_group);
		var $screen_xs_btn = $('<div class="btn-group btn-group-xs" role="group"><button id="epan-editor-preview-screen-xm" title="Mobile Preview" type="button" class="btn btn-default" ><span class="fa fa-mobile" aria-hidden="true"></span></button></div>').appendTo(responsive_btn_group);
		// var $screen_custom_btn = $('<button id="epan-editor-preview-screen-xm" title="Custom Size Preview" type="button" class="btn btn-default" ><span class="fa fa-plus" aria-hidden="true"></span></button>').appendTo(responsive_btn_group);

		$screen_reset_btn.click(function(event) {
			$('body').removeClass('xepan-cms-responsive-wrapper xepan-responsive-xs xepan-responsive-sm xepan-responsive-md xepan-responsive-lg');
		});

		$screen_xs_btn.click(function(event) {
			$('body').removeClass('xepan-responsive-sm xepan-responsive-md xepan-responsive-lg');
			$('body').addClass('xepan-cms-responsive-wrapper xepan-responsive-xs');
		});

		$screen_sm_btn.click(function(event) {
			$('body').removeClass('xepan-responsive-xs xepan-responsive-md xepan-responsive-lg');
			$('body').addClass('xepan-cms-responsive-wrapper xepan-responsive-sm');
		});

		$screen_md_btn.click(function(event) {
			$('body').removeClass('xepan-responsive-xs xepan-responsive-sm xepan-responsive-lg');
			$('body').addClass('xepan-cms-responsive-wrapper xepan-responsive-md');
		});

		$screen_lg_btn.click(function(event) {
			$('body').removeClass('xepan-responsive-xs xepan-responsive-sm xepan-responsive-md');
			$('body').addClass('xepan-cms-responsive-wrapper xepan-responsive-lg');
		});


	},

	setupTools: function(){
		var self = this;
		
		var left_bar_tool_wrapper = $('.xepan-cms-editor-helper-wrraper');
		$('<div class="xepan-cms-tool">Tools</div>').appendTo(left_bar_tool_wrapper);
		var apps_dropdown = $('<select class="xepan-layout-selector"></select>').appendTo(left_bar_tool_wrapper);
		var option = '<option value="0">Select</option>';
		var category_dropdown = $('<select class="xepan-layout-selector-category"></select>').appendTo(left_bar_tool_wrapper);
		$(category_dropdown).hide();

		// add update layout button
		var update_theme_layout_button = $('<button class="btn btn-warning btn-xs btn-block" id="xepan-editor-update-theme-layouts">Update Theme Layout</button>').appendTo(left_bar_tool_wrapper);

		var tools_options = $('<div class="xepan-tools-options">').appendTo(self.rightbar);

		var layout_category = [];

		$.each(self.options.tools,function(app_name,tools){

			option += '<option value="'+app_name+'">'+app_name+'</option>';
			var app_tool_wrapper = $('<div class="xepan-cms-toolbar-tool '+app_name+'">').appendTo(self.leftbar);
			var tools_html = "";
			$.each(tools,function(tool_name_with_namespace,tool_data){

				// category
				if(tool_data.category != undefined && $.inArray(tool_data.category, layout_category) < 0){
					layout_category.push(tool_data.category);
				}

				var t_name = tool_name_with_namespace;
				if(t_name.length >0 )
					t_name = t_name.replace(/\\/g, "");

				$('<div class="xepan-cms-tool '+tool_data.category+'" data-toolname="'+t_name+'"><img src="'+tool_data.icon_img+'"/ onerror=this.src="./vendor/xepan/cms/templates/images/default_icon.png"><p>'+tool_data.name+'</p></div>')
					.appendTo(app_tool_wrapper)
					.disableSelection()
					.draggable({
						inertia:true,
						appendTo:'body',
						connectToSortable:'.xepan-sortable-component',
						helper:'clone',
						start: function(event,ui){
							origin='toolbox';
							xepan_drop_component_html= tool_data.drop_html;
							self.leftbar.hide();
							$('.xepan-sortable-component').addClass('xepan-sortable-highlight-droppable');
						},

						stop: function(event,ui){
							self.leftbar.show();
							$('.xepan-sortable-component').removeClass('xepan-sortable-highlight-droppable');
						},
						revert: 'invalid',
						tolerance: 'pointer'
					})
					;
					
					if(tool_data.tool =='xepan/cms/Tool_Layout' && xepan_component_layout_optioned_added==true ){
					}else{
						$(tool_data.option_html).appendTo(tools_options);
						if(tool_data.tool =='xepan/cms/Tool_Layout') xepan_component_layout_optioned_added = true;
					}
			});
			$(app_tool_wrapper).hide();
		});

		$(option).appendTo(apps_dropdown);

		var category_option = '<option value="0">All Category</option>';
		$.each(layout_category, function(index, cat_name) {
			category_option += '<option value="'+cat_name+'">'+cat_name+'</option>';
		});
		$(category_option).appendTo(category_dropdown);

		$(apps_dropdown).change(function(){
			selected_app = $(this).val();
			$('.xepan-cms-toolbar-tool').hide();
			$('.xepan-cms-toolbar-tool.'+selected_app).show();
			
			if(selected_app == "Layouts"){

				$(category_dropdown).show();
			}else{
				$(category_dropdown).hide();
			}
		});

		$(category_dropdown).change(function(event) {
			/* Act on the event */
			selected_cat = $(this).val();
			$('.xepan-cms-toolbar-tool.Layouts .xepan-cms-tool').hide();
			$('.xepan-cms-toolbar-tool.Layouts').show();
			if(selected_cat == 0){
				$('.xepan-cms-toolbar-tool.Layouts .xepan-cms-tool').show();
			}else{
				$('.xepan-cms-toolbar-tool.Layouts .'+selected_cat).show();
			}

		});

		// Show default custom layouts
		$(apps_dropdown).val('Layouts');
		$(category_dropdown).show();
		$(category_dropdown).val('customlayout');
		$(category_dropdown).trigger('change');

		$(self.options.basic_properties).appendTo(tools_options);

		$(update_theme_layout_button).click(function(event){
			$.ajax({
				url: 'index.php?page=xepan_cms_editor_updatelayout&cut_page=1',
			})
			.always(function(message) {
				eval(message)
			});
		});
	},

	setUpPagesAndTemplates: function(){
		var self = this;

		var page_btn_wrapper = $('<div class="btn-group btn-group-justified xepan-cms-template-page-management"></div>').appendTo(self.editor_helper_wrapper);
		// var $template_edit_btn = $('<div class="btn-group btn-group-xs"> <button class="btn btn-primary" title="Edit Current Page Template"> <i class="fa fa-edit"> Template</i></button></div>').appendTo(page_btn_wrapper);
		// var $page_btn = $('<input disabled="" title="Current Page:'+self.options.current_page+'" class="form-control" aria-describedby="basic-addon3" value="'+self.options.current_page+' "/><span title="Page and Template Management" class="input-group-addon"><i class="fa fa-cog"></i></span>').appendTo(page_btn_wrapper);

		// var $page_btn = $('<div class="btn-group btn-group-xs"><button title="Page and Template Management" class="btn btn-primary">Page&nbsp;<i class="fa fa-cog"></i></button></div>').appendTo(page_btn_wrapper);
		$page_dropdown = $('<div class="btn-group btn-group-xs" role="group"><button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Pages<span class="caret"></span></button><div class="dropdown-menu" style="width:250px;"> <button class="btn btn-default xepan-current-page-template-edit" title="edit current page template">Edit Page Template</button><button class="btn btn-default xepan-page-management" title="Manage Page and Template">New/Manage Pages</button>  <ul class="dropdown-menu page-list" style="display:block;max-height:400px;overflow-y:auto;overflow-x:hidden;width:250px;"><li><a href="#"><i class="fa fa-circle-o-notch"></i></a></li></ul></div></div>').appendTo(page_btn_wrapper);

		$('.xepan-current-page-template-edit').click(function(event) {
			$(self.element).xepanEditor('editTemplate');
		});

		$('.xepan-page-management').click(function(event) {
			$.univ().frameURL('Pages & Templates','index.php?page=xepan_cms_cmspagemanager&cut_page=1');
		});

		$page_dropdown.click(function(event) {
			$('.dropdown-toggle').dropdown();
			
			$.ajax({
				url: 'index.php?page=xepan_cms_getwebpage&cut_page=1',
				success: function ( page_list ){
					if($(page_list).length > 0)
						$page_dropdown.find('ul.page-list').html(page_list);
					else
						$page_dropdown.find('ul.page-list').html("<li>No Pages Found</li>");
				}
			});
		});

	},

	setupEditableText: function(){
		var self = this;
		var selector = ".xepan-editable-text";
		// if(self.options.template_editing){
		// 	selector= '.xepan-v-body .xepan-editable-text';
		// }else{
		// 	selector= '.xepan-page-wrapper .xepan-editable-text';
		// }

		$(selector).attr('contenteditable','true');
		$(selector+ ':not(.xepan-no-richtext)').each(function(index, el) {
			$.univ().xepan_richtext_admin($(this)[0],xepan_cms_tinymce_options,true);
		});
	},

	setupToolbar: function(){
		var self = this;

		$(this.element).draggable({
			handle:'.xepan-toolbar-drag-handler',
			containment : 'window'
		});

		$('.xepan-tools-options').draggable({
			handle:'.xepan-tools-options-drag-handler',
			containment : 'window'
		});
		
		/*=====----Setup TopToolBar Editor-----===========*/
		/*Save page Content*/
		$('#xepan-savepage-btn').click(function(){
			$('.xepan-toolbar').xepanEditor('savePage');
		});
		$('#toolbar-toggle-btn').click(function(){
			$('.xepan-toolbar-group-component').toggle();
		});

		/*Component Editing outline*/
		$('#epan-component-border').click(function(event) {
		    if($('#epan-component-border:checked').size() > 0){
		        $(self.options.component_selector).addClass('component-outline');
		    }else{
		        $(self.options.component_selector).removeClass('component-outline');
		    }
		});
		/*Preview Mode*/
		$('#epan-editor-preview i').click(function(event){
		    $('#epan-editor-left-panel').visibilityToggle();
		});
		/*Access to Admin panel*/
		 $('#dashboard-btn').click(function(event) {
	        // TODO check if content is changed
	        window.location.replace('admin/');
	    });

		/*Drag & Drop Component to Another  Extra Padding top & Bottom*/
		$('#epan-component-extra-padding').click(function(event) {
		    if($('#epan-component-extra-padding:checked').size() > 0){
		        $(self.options.component_selector + ' .xepan-sortable-component').addClass('xepan-sortable-extra-padding');
		    }else{
		        $(self.options.component_selector + ' .xepan-sortable-component').removeClass('xepan-sortable-extra-padding');
		    }
		});
		
		
		/*Website Desktop,Laptop,Tablet,Mobile Device Preview*/

		$('#epan-editor-preview-mobile').click(function(event){
		    $("<div>").append($("<iframe width='100%' height='100%' />")
		        .attr("src", "index.php?preview=1"))
		        .dialog({
		            width: 320,
		            height: 480,
		            modal: true
		    });
		    $('iframe').on("load", function() {
			    $('iframe').contents().find('body').css('margin-top','0');    
				$('iframe').contents().find('.xepan-toolbar').css('display','none');
			});    
		});

		$('#epan-editor-preview-tablet').click(function(event){
		    $("<div>").append($("<iframe width='100%' height='100%' />")
		        .attr("src", "index.php?preview=1"))
		        .dialog({
		            width: 768,
		            height: 480,
		            modal: true
		    });
		    $('iframe').on("load", function() {
			    $('iframe').contents().find('body').css('margin-top','0');    
				$('iframe').contents().find('.xepan-toolbar').css('display','none');
			});    
		});

		$('#epan-editor-preview-laptop').click(function(event){
		    $("<div>").append($("<iframe width='100%' height='100%' style='margin-top:10px' />")
		        .attr("src", "index.php?preview=1"))
		        .dialog({
		            width: 992,
		            height: 500,
		            modal: true
		    });
		    $('iframe').on("load", function() {
			    $('iframe').contents().find('body').css('margin-top','0');    
				$('iframe').contents().find('.xepan-toolbar').css('display','none');
			});    
		});

		$('#epan-editor-preview-desktop').click(function(event){
		    $("<div>").append($("<iframe width='100%' height='100%' />")
		        .attr("src", "index.php?preview=1"))
		        .dialog({
		            width: 1024,
		            height: 550,
		            modal: true
		    });
		    $('iframe').on("load", function() {
			    $('iframe').parent().find('.ui-dialog-titlebar').css('margin-top','55px');    
			    $('iframe').contents().find('body').css('margin-top','0');    
				$('iframe').contents().find('.xepan-toolbar').css('display','none');
			});    
		});

		$('#save-as-snapshot').click(function(event){

		});

		$('#template-btn').click(function(event){
			$('.xepan-toolbar').xepanEditor('editTemplate');
		});

	},

	editTemplate : function(){
		// alert(this.options.template);
		// alert(this.options.template_file);
		$.univ().location('index.php?page='+this.options.template+'&xepan-template-edit='+this.options.template);
	},

	savePage: function(){
		// console.log(this.options.save_url);
		// console.log(this.options.file_path);

		var self= this;

		// show all contents from D&D helper - page and template first
		if($('#epan-hide-template-content:checked').size() > 0 ) $('#epan-hide-template-content:checked').click();
		if($('#epan-hide-page-content:checked').size() > 0 ) $('#epan-hide-page-content:checked').click();
		
		// $('body').trigger('beforeSave');
		try{
	    	$('body').triggerHandler('beforeSave');
		}catch(err){
			console.log(err);
		}

	    $('body').univ().infoMessage('Wait.. saving your page !!!');

	    $('.xepan-selected-component').removeClass('xepan-selected-component');
	    $(xepan_component_selector).each(function(index, comp) {
	    	try{
	    		$(comp).xepanComponent('deselect');
	    	}catch(e){
	    		console.log(comp);
	    	}
	 	});

	   	// responsive classes
	    $('body').removeClass('xepan-cms-responsive-wrapper xepan-responsive-xs xepan-responsive-sm xepan-responsive-md xepan-responsive-lg');

	    var overlay = jQuery('<div id="xepan-cms-page-save-overlay"> </div>');
	    overlay.insertAfter(document.body).css('z-index','10000');

	    var template_file_path = self.options.template_file;
	    var page_file_path = self.options.file_path;
	    
		
		// if(self.options.template_editing){
	    var html_body = $('body').clone();
	    if($(html_body).find('.xepan-v-body').length > 0 )
	    	$(html_body).children().filter(":not(.xepan-v-body)").remove();
		// }		
		
		// remove unwanted code as per given attributes
		$(html_body).find('[xepan-selector-to-remove-before-save]').each(function(index, el) {
			$(this).find($(this).attr('xepan-selector-to-remove-before-save')).remove();
		});

		// run jquery code
		$(html_body).find('[xepan-cmp-creator-code-run-before-save]').each(function(index, el) {
			eval($(this).attr('xepan-cmp-creator-code-run-before-save'));
		});

	    $(html_body).find('.xepan-serverside-component').html("");
	    $(html_body).find('.xepan-editable-text').attr('contenteditable','false');
	    $(html_body).find('.mce-tinymce').remove();
	    $(html_body).find('.mce-tooltip').remove();

	    $(html_body).find('.xepan-component-drag-handler').remove();
	    $(html_body).find('.xepan-component-remove').remove();
	    $(html_body).find('.xepan-component').removeClass('xepan-component-hover-selector');
	    $(html_body).find('.xepan-component').removeClass('component-outline');
	    $(html_body).find('.xepan-component').removeClass('xepan-selected-component');
	    $(html_body).find('.xepan-component').removeClass('xepan-sortable-extra-padding');

	    page_html = encodeURIComponent($.trim($(html_body).find('.xepan-page-wrapper').first().html()));
	    // console.log('page_html');
	    // console.log($.trim($(html_body).find('.xepan-page-wrapper').first().html()));
	    page_crc = crc32(page_html);


	    $(html_body).find('.xepan-page-wrapper').html('{$Content}');

	    html_body = encodeURIComponent($.trim($(html_body).html()));
	    html_crc = crc32(html_body);
	    
	    // html_body = ($.trim($(html_body).html()));

	    // if (edit_template == true) {
	    //     html_body = encodeURIComponent($.trim($('#epan-content-wrapper').html()));
	    //     html_crc = crc32(html_body);
	    // }

	    $("body").css("cursor", "default");

	    $.ajax({
	        url: this.options.save_url,
	        type: 'POST',
	        dataType: 'html',
	        data: {
	            
	            body_html: html_body,
	            html_crc32: html_crc,
	            html_length: html_body.length,
	            webtemplate_id: self.options.webtemplate_id,
	            body_attributes: encodeURIComponent($('body').attr('style')),
	            
	            page_html: page_html,
	            page_crc32: page_crc,
	            page_length: page_html.length,
	            webpage_id: self.options.webpage_id,

	            take_snapshot: ((xepan_save_and_take_snapshot==false) ? 'N' : xepan_save_and_take_snapshot),
	            file_path: self.options.file_path,
	            template_file_path: self.options.template_file_path,
	            is_template: self.options.template_editing,
	            page_name: self.options.current_page
	        },
	    })
	    .done(function(message) {
            eval(message);	        
	        $('body').triggerHandler('saveSuccess');
	    })
	    .fail(function(err) {
	        $('body').trigger('saveFail');
	    })
	    .always(function() {
	        $('body').triggerHandler('afterSave');
	    });
	},

	hideOptions:function(){
		$('.xepan-tool-options').hide();
	},

	setUpShortCuts: function(){
		var self = this;
		shortcut.add("Ctrl+s", function(event) {
	        $(self.element).xepanEditor('savePage');
	        event.stopPropagation();
	    });

	    shortcut.add("Ctrl+backspace", function(event) {
	    	if (typeof current_selected_component == 'undefined') return;
	        if(!$(current_selected_component).hasClass('xepan-no-delete') && !$(current_selected_component).hasClass('xepan-no-remove'))
	        	$(current_selected_component).xepanComponent('remove');
	        event.stopPropagation();
	    });

		shortcut.add("Ctrl+Shift+Up", function(event) {
        	ctrlShiftUpSelection(event);
	    });

	    shortcut.add("Ctrl+Shift+Left", function(event) {

	    	ctrlShiftLeftSelection(event);
	    });

	    shortcut.add("Ctrl+Shift+Right", function(event) {
	    	ctrlShiftRightSelection();
	    });

		shortcut.add("Tab", function(event) {
	        tabSelection(event);
	    }, {
	        disable_in_input: true
	    });

	    shortcut.add("Shift+Tab", function(event) {
	    	shiftTabSelection(event);
	    });

	    shortcut.add("Esc", function(event) {
	    	$('.xepan-selected-component').removeClass('xepan-selected-component');
				$('.xepan-selected-component').removeClass('xepan-selected-component');
				$(xepan_component_selector).each(function(index, el) {
	        		try{
						$(el).xepanComponent('deselect');	
					}catch(e){
						console.log('This looks like wrong xepanComponent in wrong position, class is not making it component');
						console.log($(this));
						// throw e;
					}
				});

	        $('#xepan-cms-toolbar-right-side-panel').removeClass('toggleSideBar');
	        $('#xepan-cms-toolbar-left-side-panel').removeClass('toggleSideBar');
	        event.preventDefault();event.stopPropagation();
	    });

	    shortcut.add("F2", function(event) {
        $('.xepan-toolbar-group-component ').toggle('slideup');
	    });

	    shortcut.add("F4", function(event) {
	        $('.xepan-tools-options').toggle('slideup');
	    });


	},

	cleanup: function(){
		$('.xepan-editable-text .xepan-component').removeClass('xepan-component');
		$('.xepan-serverside-component .xepan-component').removeClass('xepan-component');
	}

	
});

function ctrlShiftRightSelection(event){
	if (typeof current_selected_component == 'undefined') return;
    next_sibling = $(current_selected_component).next('.xepan-component');
    if (next_sibling.length === 0) {
        $('body').univ().errorMessage('No Next Sibling element found');
        return;
    }
    $(current_selected_component).xepanComponent('deselect');
    $(next_sibling).xepanComponent('select');
    event.stopPropagation();
}

function ctrlShiftLeftSelection(event){
	if (typeof current_selected_component == 'undefined') return;
    prev_sibling = $(current_selected_component).prev('.xepan-component');
    if (prev_sibling.length === 0) {
        $('body').univ().errorMessage('No Next Sibling element found');
        return;
    }
    $(current_selected_component).xepanComponent('deselect');
    $(prev_sibling).xepanComponent('select');
    event.stopPropagation();
}

function ctrlShiftUpSelection(event){
	if (typeof current_selected_component == 'undefined') return;
    parent_component = $(current_selected_component).parent('.xepan-component');
    if (parent_component.length === 0) {
        $('body').univ().errorMessage('On Top Component');
        return;
    }
    $(current_selected_component).xepanComponent('deselect');
    $(parent_component).xepanComponent('select');
}

function tabSelection(event){
	if (typeof current_selected_component == 'undefined') {
        next_component = $('.xepan-page-wrapper').children('.xepan-component:first-child');
    } else {
        var $x = $('.xepan-component:not(.xepan-page-wrapper)');
        next_component = $x.eq($x.index($(current_selected_component)) + 1);
    }

    if($(next_component).attr('id') === undefined){
        next_component = $('.xepan-page-wrapper').children('.xepan-component:first-child');
        if($(next_component).attr('id') === undefined){
            event.preventDefault();event.stopPropagation();
            $.univ.errorMessage('No Component On Screen');
            return;
        }
    }

	$('.xepan-selected-component').removeClass('xepan-selected-component');
    $(xepan_component_selector).each(function(index, el) {
		try{
			$(el).xepanComponent('deselect');	
		}catch(e){
			console.log('This looks like wrong xepanComponent in wrong position, class is not making it component');
			console.log($(this));
			console.trace();
			// throw e;
		}
	});
    $(next_component).xepanComponent('select');
    event.preventDefault();event.stopPropagation();
}


function shiftTabSelection(event){
    if (typeof current_selected_component == 'undefined') {
        next_component = $('.xepan-page-wrapper').children('.xepan-component:first-child');
    } else {
        var $x = $('.xepan-component:not(.xepan-page-wrapper)');
        next_component = $x.eq($x.index($(current_selected_component)) - 1);
    }

    if($(next_component).attr('id') === undefined){
        next_component = $('.xepan-page-wrapper').children('.xepan-component:first-child');
        if($(next_component).attr('id') === undefined){
            event.stopPropagation();
            $.univ.errorMessage('No Component On Screen');
            return;
        }
    }

    $(xepan_component_selector).each(function(index, el) {
		try{
			$(el).xepanComponent('deselect');	
		}catch(e){
			console.log('This looks like wrong xepanComponent in wrong position, class is not making it component');
			console.log($(this));
			console.trace();
			// throw e;
		}
	});
    $(next_component).xepanComponent('select');
    event.stopPropagation();
}

function componentMoveLeft(event){
	if (typeof current_selected_component == 'undefined') return;
	if($(current_selected_component).hasClass('xepan-no-move')){
		$.univ().errorMessage('This Component is restricted to be moved');
		return;
	}
    previous_sibling = $(current_selected_component).prev('.xepan-component');
    if (previous_sibling.length === 0) {
        $('body').univ().errorMessage('No Previous Sibling element found');
        return;
    }

    $(current_selected_component).insertBefore(previous_sibling);
    event.stopPropagation();
}

function componentMoveRight(event){
	if (typeof current_selected_component == 'undefined') return;
	if($(current_selected_component).hasClass('xepan-no-move')){
		$.univ().errorMessage('This Component is restricted to be moved');
		return;
	}
    next_sibling = $(current_selected_component).next('.xepan-component');
    if (next_sibling.length === 0) {
        $('body').univ().errorMessage('No Next Sibling element found');
        return;
    }

    $(current_selected_component).insertAfter(next_sibling);
    event.stopPropagation();
}

function duplicateComponent(event){
	if (typeof current_selected_component == 'undefined') return;
	html_clone = $(current_selected_component).clone();

	// console.log(html_clone);
	$(html_clone).removeAttr('id');
	$(html_clone).find('.xepan-component').each(function(){
		$(this).removeAttr('id');
	});

	duplicate_component = $(html_clone).insertAfter(current_selected_component);

	old_options = $.extend(true, {}, (current_selected_component).xepanComponent('getOptions'));
	$(current_selected_component).xepanComponent('deselect');
    $(duplicate_component).xepanComponent(old_options);
    $(duplicate_component).xepanComponent('select');
    $(duplicate_component).find('.xepan-component').xepanComponent(old_options);

    event.stopPropagation();
}

function Utf8Encode(string) {
    string = string.replace(/\r\n/g, "\n");
    var utftext = "";

    for (var n = 0; n < string.length; n++) {
        var c = string.charCodeAt(n);
        if (c < 128) {
            utftext += String.fromCharCode(c);
        } else if ((c > 127) && (c < 2048)) {
            utftext += String.fromCharCode((c >> 6) | 192);
            utftext += String.fromCharCode((c & 63) | 128);
        } else {
            utftext += String.fromCharCode((c >> 12) | 224);
            utftext += String.fromCharCode(((c >> 6) & 63) | 128);
            utftext += String.fromCharCode((c & 63) | 128);
        }
    }
    return utftext;
};

function crc32(str) {
    str = Utf8Encode(str);
    var table = "00000000 77073096 EE0E612C 990951BA 076DC419 706AF48F E963A535 9E6495A3 0EDB8832 79DCB8A4 E0D5E91E 97D2D988 09B64C2B 7EB17CBD E7B82D07 90BF1D91 1DB71064 6AB020F2 F3B97148 84BE41DE 1ADAD47D 6DDDE4EB F4D4B551 83D385C7 136C9856 646BA8C0 FD62F97A 8A65C9EC 14015C4F 63066CD9 FA0F3D63 8D080DF5 3B6E20C8 4C69105E D56041E4 A2677172 3C03E4D1 4B04D447 D20D85FD A50AB56B 35B5A8FA 42B2986C DBBBC9D6 ACBCF940 32D86CE3 45DF5C75 DCD60DCF ABD13D59 26D930AC 51DE003A C8D75180 BFD06116 21B4F4B5 56B3C423 CFBA9599 B8BDA50F 2802B89E 5F058808 C60CD9B2 B10BE924 2F6F7C87 58684C11 C1611DAB B6662D3D 76DC4190 01DB7106 98D220BC EFD5102A 71B18589 06B6B51F 9FBFE4A5 E8B8D433 7807C9A2 0F00F934 9609A88E E10E9818 7F6A0DBB 086D3D2D 91646C97 E6635C01 6B6B51F4 1C6C6162 856530D8 F262004E 6C0695ED 1B01A57B 8208F4C1 F50FC457 65B0D9C6 12B7E950 8BBEB8EA FCB9887C 62DD1DDF 15DA2D49 8CD37CF3 FBD44C65 4DB26158 3AB551CE A3BC0074 D4BB30E2 4ADFA541 3DD895D7 A4D1C46D D3D6F4FB 4369E96A 346ED9FC AD678846 DA60B8D0 44042D73 33031DE5 AA0A4C5F DD0D7CC9 5005713C 270241AA BE0B1010 C90C2086 5768B525 206F85B3 B966D409 CE61E49F 5EDEF90E 29D9C998 B0D09822 C7D7A8B4 59B33D17 2EB40D81 B7BD5C3B C0BA6CAD EDB88320 9ABFB3B6 03B6E20C 74B1D29A EAD54739 9DD277AF 04DB2615 73DC1683 E3630B12 94643B84 0D6D6A3E 7A6A5AA8 E40ECF0B 9309FF9D 0A00AE27 7D079EB1 F00F9344 8708A3D2 1E01F268 6906C2FE F762575D 806567CB 196C3671 6E6B06E7 FED41B76 89D32BE0 10DA7A5A 67DD4ACC F9B9DF6F 8EBEEFF9 17B7BE43 60B08ED5 D6D6A3E8 A1D1937E 38D8C2C4 4FDFF252 D1BB67F1 A6BC5767 3FB506DD 48B2364B D80D2BDA AF0A1B4C 36034AF6 41047A60 DF60EFC3 A867DF55 316E8EEF 4669BE79 CB61B38C BC66831A 256FD2A0 5268E236 CC0C7795 BB0B4703 220216B9 5505262F C5BA3BBE B2BD0B28 2BB45A92 5CB36A04 C2D7FFA7 B5D0CF31 2CD99E8B 5BDEAE1D 9B64C2B0 EC63F226 756AA39C 026D930A 9C0906A9 EB0E363F 72076785 05005713 95BF4A82 E2B87A14 7BB12BAE 0CB61B38 92D28E9B E5D5BE0D 7CDCEFB7 0BDBDF21 86D3D2D4 F1D4E242 68DDB3F8 1FDA836E 81BE16CD F6B9265B 6FB077E1 18B74777 88085AE6 FF0F6A70 66063BCA 11010B5C 8F659EFF F862AE69 616BFFD3 166CCF45 A00AE278 D70DD2EE 4E048354 3903B3C2 A7672661 D06016F7 4969474D 3E6E77DB AED16A4A D9D65ADC 40DF0B66 37D83BF0 A9BCAE53 DEBB9EC5 47B2CF7F 30B5FFE9 BDBDF21C CABAC28A 53B39330 24B4A3A6 BAD03605 CDD70693 54DE5729 23D967BF B3667A2E C4614AB8 5D681B02 2A6F2B94 B40BBE37 C30C8EA1 5A05DF1B 2D02EF8D";
    var crc = 0;
    var x = 0;
    var y = 0;

    crc = crc ^ (-1);
    for (var i = 0, iTop = str.length; i < iTop; i++) {
        y = (crc ^ str.charCodeAt(i)) & 0xFF;
        x = "0x" + table.substr(y * 9, 8);
        crc = (crc >>> 8) ^ x;
    }

    return (crc ^ (-1)) >>> 0;
};

(function(old) {
  $.fn.attr = function() {
    if(arguments.length === 0) {
      if(this.length === 0) {
        return null;
      }

      var obj = {};
      $.each(this[0].attributes, function() {
        if(this.specified) {
          obj[this.name] = this.value;
        }
      });
      return obj;
    }

    return old.apply(this, arguments);
  };
})($.fn.attr);

// $('iframe').load(function(){
// 	$(this).find('body').css('margin-top','0');
// });
