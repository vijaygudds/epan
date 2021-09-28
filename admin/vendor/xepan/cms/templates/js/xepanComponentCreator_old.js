current_selected_dom = 0;
current_selected_dom_of_code_change = 0;
current_selected_dom_original_html = "";
current_selected_dom_component_type = undefined;
repitative_selected_dom = 0;
current_selected_tag_dom = 0;
tags_associate_list = [];
selection_previous_dom=[];

jQuery.widget("ui.xepanComponentCreator",{
	options:{
		base_url:undefined,
		file_path:undefined,
		template_file:undefined,
		template:undefined,
		template_editing:undefined,
		save_url:undefined,
		template_editing:undefined,
		tools:{},
		basic_properties: undefined,
		component_selector: '.xepan-component',
	},

	_create: function(){
		var self = this;

		self.createDomInspector();
		// this.manageDomSelected();

	},

	createDomInspector: function(){
		var self = this;
		// to this.element // hide UI if any outer most
		// on click attach moucemove/enter/out event
		// on click set current_selected_dom variable
		// and detach UI

		filter_selector = ".xepan-page-wrapper *";
		if(self.options.template_editing)
			filter_selector = 'body *';

		var myDomOutline = DomOutline({
			'onClick': function(element){
				current_selected_dom = element;

				var xepan_component_of_dom = $(current_selected_dom).closest('[xepan-component]').attr('xepan-component');
				// check if component is server side
				if(self.isComponentServerSide(xepan_component_of_dom)){
					var r = confirm("this is part of server side component, we are selecting it");
					if (r == true) {
						current_selected_dom = $(current_selected_dom).closest('[xepan-component]');
					}
					if(r == false)
						return;
				}

				// check here text and server side component
				if($(current_selected_dom).closest('.xepan-editable-text').length){
					var r = confirm("this is part of editable text, selecting parent component");
					if (r == true) {
						current_selected_dom = $(current_selected_dom).closest('.xepan-editable-text');
					}
					if(r == false)
						return;
				}

				// check if selected dom is img for existing image component

				if($(current_selected_dom).closest('[xepan-component="xepan/cms/Tool_Image"]').length){
					var r = confirm("This is image of existing Image Tool, selecting 'Image' component");
					if (r == true) {
						current_selected_dom = $(current_selected_dom).closest('[xepan-component="xepan/cms/Tool_Image"]');
					}
					if(r == false)
						return;
				}

				self.manageDomSelected();
			
			},
			filter:filter_selector
		});

		$('#xepan-tool-inspector').click(function(){
			myDomOutline.start();
			return false;
		});

		// myDomOutline.stop();
	},

	manageDomSelected: function () {
		var self = this;
		// create Base UI // component type only infact
		// filter types like if rows and bootstrap col-md/sd etc is there let column Type be there or remove

		current_selected_dom_original_html = $(current_selected_dom).prop('outerHTML');
		
		$('.sidebar').removeClass('toggleSideBar');
		if($('#xepan-component-creator-form').length){
			$('#xepan-component-creator-form').remove();
			$('.modal-backdrop').remove();
		}

		// display form in modal layout
		var form_layout = '<div id="xepan-component-creator-form" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="xepan-component-creator">'+
  						'<div class="modal-dialog modal-lg" role="document">'+
    						'<div class="modal-content">'+
      							'<div class="modal-header">'+
        							'<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+
        							'<h4 class="modal-title" id="gridSystemModalLabel">Epan Component Creator</h4>'+
      							'</div>'+
      							'<div class="modal-body">'+
      							'</div>'+
      							'<div class="modal-footer">'+
        							'<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>'+
        							'<button type="button" class="btn btn-primary" id="xepan-component-creator-form-save">Save changes</button>'+
      							'</div>'+
    						'</div>'+
  						'</div>'+ 
					'</div>';

		$form = $(form_layout).appendTo('body');
		$form.modal('toggle');

		var form_body = $form.find('.modal-body');
		var form_footer = $form.find('.modal-footer');
		
		current_selected_dom_component_type = $(current_selected_dom).attr('xepan-component')?$(current_selected_dom).attr('xepan-component'):'Generic';

		// html code 
		current_selected_dom_html = '<textarea class="form-control" style="width:100%;" rows="4" disabled></textarea>';
		html_textarea = $(current_selected_dom_html).appendTo($(form_body));
		$(html_textarea).val($(current_selected_dom).prop('outerHTML'));

		// selection
		selection_group = $('<div class="btn-group btn-group-xs"></div>').appendTo($(form_body));

		$('<button class="btn btn-primary" id="xepan-creator-reselection">Selection</button>').appendTo($(selection_group));
		var selection_parent = $('<button id="xepan-creator-current-dom-select-parent" type="button" title="Parent" class="btn btn-default"><i class="fa fa-arrow-up"></i></button>').appendTo($(selection_group));
		var selection_previous = $('<button id="xepan-creator-current-dom-select-previous" type="button" title="Previous" class="btn btn-default" style="display:none"><i class="fa fa-arrow-down"></i></button>').appendTo($(selection_group));
		// var selection_child = $('<button id="xepan-creator-current-dom-select-child" type="button" title="Child/Next" class="btn btn-default"><i class="fa fa-arrow-down"></i></button>').appendTo($(selection_group));
		// var selection_previous_sibling = $('<button id="xepan-creator-current-dom-select-previous-sibling" type="button" title="Previous Sibling" class="btn btn-default"><i class="fa fa-arrow-left"></i></button>').appendTo($(selection_group));
		// var selection_next_sibling = $('<button id="xepan-creator-current-dom-select-next-sibling" type="button" title="Next Sibling" class="btn btn-default"><i class="fa fa-arrow-right"></i></button>').appendTo($(selection_group));

		$(selection_group).click(function(event) {
			$('#xepan-component-creator-form').remove();
			$('.modal-backdrop').remove();
			$('#xepan-tool-inspector').trigger('click');
		});

		$(selection_parent).click(function(event){
			selection_previous_dom.push($(current_selected_dom));
			current_selected_dom = $(current_selected_dom).parent()[0];
			self.manageDomSelected();
		});

		$(selection_previous).click(function(event){
			current_selected_dom = selection_previous_dom.pop()[0];
			$(selection_parent).show();
			self.manageDomSelected();
		});

		if($(current_selected_dom).parent('.xepan-page-wrapper').length){
			$(selection_parent).hide();
		}

		if(selection_previous_dom.length > 0 ) 
			$(selection_previous).show();
		else
			$(selection_previous).hide();

		// $(self.selection_next_sibling).click(function(event){
		// 	ctrlShiftRightSelection(event);
		// });

		// $(self.selection_parent).click(function(event) {
		// 	ctrlShiftUpSelection(event);
		// });

		// $(self.selection_child).click(function(event){
		// 	tabSelection(event);
		// });

		var type_select_layout = '<select id="xepan-component-creator-component-type-selector"><option value="Generic"> Generic Tool</option>';
		$.each(self.options.tools, function(appliction, app_tools) {
			 /* iterate through array or object */
			 if(appliction == "Layouts") return; //actually continue

			 $.each(app_tools, function(tool_name, tool_info) {
			 	tool_name = tool_name.replace(/\\/g, "/");
			 	type_select_layout += '<option value="'+tool_name+'">'+tool_name+'</option>';
			 });
		});
		type_select_layout += '</select>';


		$type_select =  $(type_select_layout).appendTo($(form_body));
		
		// add move section
		self.addMoveToTemplate();

		// append component wrapper
		$('<div id="xepan-component-creator-type-wrapper"></div>').appendTo($(form_body));

		self.handleComponentTypeChange(current_selected_dom_component_type);
		$type_select.change(function(event) {
			current_selected_dom_component_type = $(this).val();
			self.handleComponentTypeChange($(this).val());
		});
		$type_select.val(current_selected_dom_component_type);

		
		// save button called
		$('#xepan-component-creator-form-save').click(function(event) {
			// on server side component create related UI
			if(self.isComponentServerSide($('#xepan-component-creator-component-type-selector').val())){
				self.saveServerSideComponent();
			}else{
				self.saveClientSideComponent();
			}

		});
	},

	saveClientSideComponent: function(){

		switch(current_selected_dom_component_type){
			case 'xepan/cms/Tool_Image':
				$(current_selected_dom).wrap('<span class="xepan-component" xepan-component="xepan/cms/Tool_Image" xepan-component-name="Image"></span>');
				$.univ().infoMessage('saved and reload page');
				// $('#xepan-component-creator-form').modal('close');
				$('#xepan-component-creator-form').remove();
				$('.modal-backdrop').remove();
				return;
			case 'xepan/cms/Tool_Icon':
			// i.xepan-component.xepan-cms-icon.text-center.fa.fa-leaf(id="{$_name}" xepan-component='xepan/cms/Tool_Icon' icon-link-target="none" icon-class="fa-leaf" icon-size="fa-1x" icon-link="#" icon-align="text-center" style="width:100%;" xepan-component-name="Icon")
				$(current_selected_dom)
					.addClass('xepan-component')
					.attr({
						'xepan-component'	: 'xepan/cms/Tool_Icon',
						'icon-link-target'	: 'none',
						'icon-size'			: "fa-1x",
						'icon-link'			: "#",
						'icon-align'		: "text-center",
						'icon-class'		:"fa-leaf",
						'xepan-component-name': "Icon"
					});
				var classNames = $(current_selected_dom).attr("class").toString().split(' ');
		        $.each(classNames, function (i, className) {
		            if(className.match(/fa-/i)){
		            	$(current_selected_dom).attr('icon-class', className);
		            }
		        });
				$('#xepan-component-creator-form').remove();
				$('.modal-backdrop').remove();
				return;
		}

		// xepan component 
		if($('#xepan-cmp-creator-xepan-component:checked').size() > 0)
			$(current_selected_dom).addClass('xepan-component');
		else
			$(current_selected_dom).removeClass('xepan-component');

		// xepan sortable component 
		if($('#xepan-cmp-creator-xepan-sortable-component:checked').size() > 0)
			$(current_selected_dom).addClass('xepan-sortable-component');
		else
			$(current_selected_dom).removeClass('xepan-sortable-component');

		// xepan editable text
		if($('#xepan-cmp-creator-xepan-editable-text:checked').size() > 0){
			$(current_selected_dom).addClass('xepan-editable-text');
		}else
			$(current_selected_dom).removeClass('xepan-editable-text');
		
		// no richtext
		if($('#xepan-cmp-creator-xepan-no-richtext:checked').size() > 0)
			$(current_selected_dom).addClass('xepan-no-richtext');
		else
			$(current_selected_dom).removeClass('xepan-no-richtext');

		// no move
		if($('#xepan-cmp-creator-xepan-no-move:checked').size() > 0)
			$(current_selected_dom).addClass('xepan-no-move');
		else
			$(current_selected_dom).removeClass('xepan-no-move');

		// no delete
		if($('#xepan-cmp-creator-xepan-no-delete:checked').size() > 0)
			$(current_selected_dom).addClass('xepan-no-delete');
		else
			$(current_selected_dom).removeClass('xepan-no-delete');

		// component name
		if($('#xepan-cmp-creator-component-name').val().length){
			$(current_selected_dom).attr('xepan-component-name',$('#xepan-cmp-creator-component-name').val());
		}else{
			$(current_selected_dom).attr('xepan-component-name','Generic');
		}

		// dynamic options lister
		if($('#xepan-cmp-creator-dynamic-list-selector').val().length){
			$(current_selected_dom).attr('xepan-component-dynamic-option-list',$('#xepan-cmp-creator-dynamic-list-selector').val());
		}else
			$(current_selected_dom).removeAttr('xepan-component-dynamic-option-list');

		// remove selector
		if($('#xepan-cmp-creator-selector-to-remove-before-save').val().length){
			$(current_selected_dom).attr('xepan-selector-to-remove-before-save',$('#xepan-cmp-creator-selector-to-remove-before-save').val());
		}else
			$(current_selected_dom).removeAttr('xepan-selector-to-remove-before-save');

		// run jquery code
		if($('#xepan-cmp-creator-code-run-before-save').val().length){
			$(current_selected_dom).attr('xepan-cmp-creator-code-run-before-save',$('#xepan-cmp-creator-code-run-before-save').val());
		}else
			$(current_selected_dom).removeAttr('xepan-cmp-creator-code-run-before-save');

		// dynamic option list
		var find_str = "xepan-dynamic-option-";

		if(current_selected_dom.attributes != undefined){
			$.each(current_selected_dom.attributes, function( index, attr ) {
				console.log("attribute: ",attr);
				if(attr == undefined) return ; //actually continnue

				if(attr.name.indexOf(find_str)===0){
					$(current_selected_dom).removeAttr(attr.name);
				}
			});

			$.each($('#xepan-creator-existing-dynamic-list .xepan-creator-existing-dynamic-list-added'), function(index, row_obj) {
				var name = 'xepan-dynamic-option-'+(index + 1);
				$(current_selected_dom).attr(name,$(row_obj).attr('data-dynamic-option'));
			});
		}

		$.univ().infoMessage('saved and reload page');
		// $('#xepan-component-creator-form').modal('close');
		$('#xepan-component-creator-form').remove();
		$('.modal-backdrop').remove();

	},
	saveServerSideComponent: function(){
		// code-editor form modal removed

		if($.inArray('{rows}',self.tags) && !$(repitative_selected_dom).length){
			$.univ().errorMessage('first select repitative section');
			return;
		}
		
		$('#xepan-component-creator-code-form').remove();
		$('.modal-backdrop').remove();

		$('*').removeClass('xepan-component-creator-extra-margin');

		if($(repitative_selected_dom).length){
		
			var repetative_orig_html = $(repitative_selected_dom).prop('outerHTML');

			$(repitative_selected_dom).siblings().remove();
			// console.log('siblings: ',$(repitative_selected_dom).siblings());

			row_html = "{rows}{row}"+repetative_orig_html+"{\/}{\/}";

			// not found message
			var no_message = $('#xepan-cmp-creator-not-found-message').val();
			row_html += '{not_found}<div role="alert" class="full-width alert alert-warning"><strong class="glyphicon glyphicon-warning-sign">&nbsp;Warning!<span>&nbsp;{not_found_message}'+no_message+'{\/}</span></strong></div>{\/}';

			// paginator tags
			if($('#xepan-cmp-creator-add-paginator:checked').size() > 0)
				row_html += "{paginator_wrapper}{$Paginator}{\/}";

			$(repitative_selected_dom).prop('outerHTML', row_html);
			$('#xepan-creator-repitative-html').val($(repitative_selected_dom).prop('outerHTML'));
			// $(repitative_selected_dom).html(repetative_orig_html);
		}

		$(current_selected_dom).removeClass('xepan-component-hover-selector');
		$(current_selected_dom).find('.xepan-component-hoverbar').remove();

		if($(current_selected_dom).attr('xepan-component')){
			var template_html = $(current_selected_dom).html();
		}else{
			var template_html = $(current_selected_dom).prop('outerHTML');
		}
		
		template_html = $(template_html);
		$(template_html).attr('id','{$_name}');
		$(template_html).addClass('{$class}');
		$(template_html).attr('style', $(template_html).attr('style')+" {$style}");

		template_html = $(template_html).prop('outerHTML');

		// open new modal popup
		var code_editor_form = '<div id="xepan-component-creator-code-form" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="xepan-component-creator">'+
  						'<div class="modal-dialog" role="document">'+
    						'<div class="modal-content">'+
      							'<div class="modal-header">'+
        							'<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+
        							'<h4 class="modal-title" id="gridSystemModalLabel">Epan Component Creator Code Editor</h4>'+
      							'</div>'+
      							'<div class="modal-body">'+
      							'</div>'+
      							'<div class="modal-footer">'+
        							'<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>'+
        							'<button type="button" class="btn btn-primary" id="xepan-component-creator-code-editor-form-save">Save</button>'+
      							'</div>'+
    						'</div>'+
  						'</div>'+ 
					'</div>';

		$code_form = $(code_editor_form).appendTo('body');
		$code_form.modal('show');

		var code_field = $('<textarea id="xepan-component-creator-layout-code" style="width:100%;height:400px"></textarea>').appendTo($code_form.find('.modal-body'));
		$(code_field).val(template_html);
		$(code_field).ace({'width':'100%'});

		$('#xepan-component-creator-code-editor-form-save').click(function(event) {
			template_html = $('#xepan-component-creator-layout-code').val();
			$.ajax({
				url :'index.php?page=xepan_cms_overridetemplate&cut_page=1',
				type: 'POST',
				data: {
					'xepan-tool-to-clone':current_selected_dom_component_type,
					'template_html': template_html
				},
				async:false,
				success: function(json){
					// console.log(json);
					var result = $.parseJSON(json);
					if(result.status != "success"){
						$.univ().errorMessage('Not Saved');
						return;
					}

					if($(current_selected_dom).attr('xepan-component') == undefined){
						current_selected_dom.outerHTML = '<div class="xepan-component xepan-serverside-component" xepan-component="'+current_selected_dom_component_type+'">PLEASE SAVE AND RELOAD</div>';
					}
										
					// $(current_selected_dom).html(current_selected_dom_original_html);
					// current_selected_dom = 0;
					current_selected_dom_original_html = "";
					current_selected_dom_component_type = undefined;
					repitative_selected_dom = 0;
					current_selected_tag_dom = 0;
					tags_associate_list = [];
					$.univ().successMessage('Saved');

					$('#xepan-component-creator-form').remove();
					$('#xepan-component-creator-code-form').remove();
					$('.modal-backdrop').remove();
				}
			});			
		});
		return;
		// $.univ().frameURL('Override Tool Template');

		

	},

	addToTagList: function(tag_name,dom_obj,implement_as){

		if(tag_name.indexOf('$') > 0){
			tag_name_with_dollar = tag_name;
			tag_name_without_dollar = tag_name.replace('$',"");
		}else{
			tag_name_with_dollar = tag_name.replace('{','{$');
			tag_name_without_dollar = tag_name;
		}

		switch(implement_as){
			case 'href':
				$(dom_obj).attr('href',tag_name_with_dollar);
			break;

			case 'src':
				$(dom_obj).attr('src',tag_name_with_dollar);
			break;
				
			case 'text':
				$(dom_obj).text(tag_name_with_dollar);
			break;

			case 'wrapper':
				var orig_html = $(dom_obj).prop('outerHTML');
				var wrapped_html = tag_name_without_dollar+orig_html+"{/}";
			 	$(dom_obj).prop('outerHTML', wrapped_html);
			break;

			case 'class':
				$(dom_obj).addClass(tag_name_with_dollar);
			break;

			case 'style':
				$(dom_obj).addStyle(tag_name_with_dollar);
			break;
		}
		// console.log("dom obj:",$(dom_obj));
		// console.log("dom obj html:",$(dom_obj).prop('outerHTML'));
	},

	handleComponentTypeChange: function(tool_name){
		var self = this;
		$('#xepan-component-creator-type-wrapper').html("");

		// on server side component create related UI
		if(self.isComponentServerSide(tool_name)){
			self.createServerSideComponentUI();
		}else{
			self.createClientSideComponentUI();
		}
	},

	isExistingComponent: function(){
		return $(current_selected_dom).hasClass('xepan-component');
	},

	isComponentServerSide: function(tool_name){
		var self = this;

		var is_serverside = false;
		$.each(self.options.tools, function(appliction, app_tools) {
			 /* iterate through array or object */
			 if(appliction == "Layouts") return; //actually continue

			 $.each(app_tools, function(app_tool_name, tool_info) {
			 	app_tool_name = app_tool_name.replace(/\\/g, "/");

			 	if(app_tool_name == tool_name){
			 		is_serverside = tool_info.is_serverside;
			 	}
			 });
		});

		// return this.isExistingComponent() && $(current_selected_dom).hasClass('xepan-serverside-component');
		// send ajax this component value or lookup un in options;
		// console.log('is server side '+is_serverside);
		return is_serverside;
	},

	createServerSideComponentUI: function (){
		var self = this;

		// get Original File Code
		// get Overrided File Code
		// set in Tabs or any poper UI
		// create rest of UI 
		// populate tags related UI
		// if(this.isServerSideComponent()){
			// reload values or create required run time components
		// }

		$creator_wrapper = $('#xepan-component-creator-type-wrapper');
		$('<div class="alert alert-danger"> Server Side</div>').appendTo($creator_wrapper);

		var row = $('<div class="row">').appendTo($creator_wrapper);
		var col1 = $('<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12"><h4>Original HTML</h4></div>').appendTo($(row));
		var col2 = $('<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12"><h4>Override HTML</h4></div>').appendTo($(row));
		var col3 = $('<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12"></div>').appendTo($(row));
		var original_template_textarea = $('<textarea id="xepan-tool-original-template">').appendTo($(col1));
		var override_template_textarea = $('<textarea id="xepan-tool-override-template">').appendTo($(col2));
		var original_tags = $('<textarea id="xepan-tool-original-template-tags">').appendTo($(col1));
		self.original_template_tags = [];

		$.ajax({
			url :'index.php?page=xepan_cms_overridetemplate&cut_page=1',
			data: {
				'xepan-tool-to-clone':current_selected_dom_component_type,
				required: 'htmlcode'
			},
			async:false,
			success: function(json){
				return_data = $.parseJSON(json);
				$(original_template_textarea).val(return_data.original_content);
				var tags = return_data.tags[0];
				$(original_tags).val(tags);
				self.original_template_tags = tags;
			}
		});

		$(override_template_textarea).val($(current_selected_dom).html());

		// if original template file contains {Rows} 
		/*
		{
			make a dom_selector (title- Repetitive block){
				on select/click{
					set variable current_selected_dom_repititve_dom = Selected one
				}
				make parent selector or code shower etc ...
			} 
		 }
		
		make dom_selector (title - set tag)
		on select dom {
			tag_dom_array[next] = selected one
			create short form for this dom
			[selector] [tags list dropdown] [as/href/src/text/wrapper] [remove btn]
		}

		create no recrod found message
		and paginator spot

		====== ON SAVE ====
		 */
		if($.inArray('{rows}',self.tags)){
			$('<h4>Repetative Selector</h4>').appendTo($(col3));
			repetative_btn_group = $('<div class="btn-group btn-group-xs"></div>').appendTo($(col3));
			$('<button class="btn btn-primary">Selection</button>').appendTo($(repetative_btn_group));
			var repetative_dom_selector = $('<button id="xepan-creator-repitative-dom-selector" type="button" title="Repetitive Dom Selector" class="btn btn-warning"><i class="fa fa-arrows"></i></button>').appendTo($(repetative_btn_group));
			var repetative_selection_parent = $('<button id="xepan-creator-repitative-select-parent" type="button" title="Parent" class="btn btn-default"><i class="fa fa-arrow-up"></i></button>').appendTo($(repetative_btn_group));
			var repetative_html = $('<textarea id="xepan-creator-repitative-html">').appendTo($(col3));
			
			// initialize dom object
			var repitativeDomOutline = DomOutline({
				'onClick': function(element){
					repitative_selected_dom = element;

					// extra padding
					if($('#xepan-cmp-creator-add-extra-padding:checked').size() > 0)
						$(repitative_selected_dom).addClass('xepan-component-creator-extra-margin');
					else
						$(repitative_selected_dom).removeClass('xepan-component-creator-extra-margin');

					$('#xepan-component-creator-form').modal('show');
					$('#xepan-creator-repitative-html').val($(repitative_selected_dom).prop('outerHTML'));
				}
			});
			// repetative dom selector
			$(repetative_dom_selector).click(function(){
				$('#xepan-component-creator-form').modal('hide');
				repitativeDomOutline.start();
				return false;
			});

			// parent selection
			$(repetative_selection_parent).click(function(event) {
				repitative_selected_dom = $(repitative_selected_dom).parent();
				$('#xepan-creator-repitative-html').val($(repitative_selected_dom).prop('outerHTML'));
			});

			// no record found message
			$('<label for="xepan-cmp-creator-not-found-message">No Record Found Message</label><input id="xepan-cmp-creator-not-found-message" value="Not Matching Record Found" />').appendTo($(col3));

			// add paginator section here if {rows}{row} has then pagination is must
			$('<input type="checkbox" id="xepan-cmp-creator-add-paginator" checked /><label for="xepan-cmp-creator-add-paginator"> Add Paginator</label>').appendTo($(col3));
			
			// add extra padding for selection
			var extra_padding = $('<input type="checkbox" id="xepan-cmp-creator-add-extra-padding" checked /><label for="xepan-cmp-creator-add-extra-padding"> Add Extra Padding For Selection</label>').appendTo($(col3));
			$('#xepan-cmp-creator-add-extra-padding').change(function(event) {
				if(!$(repitative_selected_dom).length){
					$.univ().errorMessage('first select repatative ');
					return;
				}
				if(this.checked) {
					$(repitative_selected_dom).addClass('xepan-component-creator-extra-margin');
				}else
					$(repitative_selected_dom).removeClass('xepan-component-creator-extra-margin');

			});
		}

		var tag_implementor_wrapper = $('<div class="btn-group btn-group-xs"></div>').appendTo($creator_wrapper);
		$('<button class="btn btn-primary">Selection</button>').appendTo($(tag_implementor_wrapper));
		var tag_dom_selector = $('<button id="xepan-creator-tag-dom-selector" type="button" title="Repetitive Dom Selector" class="btn btn-warning"><i class="fa fa-arrows"></i></button>').appendTo($(tag_implementor_wrapper));

		// tag select
		var tag_select = '<div class="btn-group" role="group"><select id="xepan-component-serverside-creator-tags" ><option value="">Select Tags</option>';
		$.each(self.original_template_tags, function(index, tag_name) {
			tag_select += '<option value="'+tag_name+'">'+tag_name+'</option>';
		});
		tag_select += '</select></div>';
		$(tag_select).appendTo($(tag_implementor_wrapper));

		// as 
		var as_select = '<div class="btn-group" role="group"><select id="xepan-component-serverside-creator-apply-as">'+
							'<option value="">apply as</option>'+
							'<option value="href">href</option>'+
							'<option value="src">src</option>'+
							'<option value="text">text</option>'+
							'<option value="wrapper">wrapper</option>'+
							'<option value="class">class</option>'+
							'<option value="style">style</option>'+
						'</select></div>';
		$(as_select).appendTo($(tag_implementor_wrapper));


		tag_associate_btn = $('<button id="xepan-component-creator-tag-dom-association-btn" class="btn btn-primary">Add</button>').appendTo($(tag_implementor_wrapper));
		
		tag_textarea = $('<textarea id="xepan-creator-tag-html" >').appendTo($(tag_implementor_wrapper));
		// tags_added wrapper
		$('<div id="xepan-creator-implement-tag-wrapper"></div>').appendTo($creator_wrapper);

		// initialize dom object
		var tagDomOutline = DomOutline({
			'onClick': function(element){

				if($.contains(repitative_selected_dom,element)){
					current_selected_tag_dom = element;
					$('#xepan-creator-tag-html').val($(current_selected_tag_dom).prop('outerHTML'));
				}
				else
					alert('Please select child of repetative dom/Element ');

				$('#xepan-component-creator-form').modal('show');
			}
		});

		$(tag_dom_selector).click(function(event) {
			$('#xepan-component-creator-form').modal('hide');
			tagDomOutline.start();
			return false;
		});

		// if has tag dom then select show the crud
		// if($(current_selected_tag_dom).length){

		// }

		$(tag_associate_btn).click(function(event){
			var selected_tag = $('#xepan-component-serverside-creator-tags').val();
			var implement_as = $('#xepan-component-serverside-creator-apply-as').val();

			if(!$(current_selected_tag_dom).length){
				$.univ().errorMessage('first select the dom element');
				return;
			}

			if(!selected_tag.length){
				$.univ().errorMessage('tags must not be empty');
				return;
			}
			
			if(!implement_as.length){
				$.univ().errorMessage('apply as must not be empty');
				return;
			}


			// console.log(current_selected_tag_dom);
			var temp = [];
				temp.tag = selected_tag;
				temp.dom = $(current_selected_tag_dom);
				temp.implement_as = implement_as;

			tags_associate_list.push(temp);

			self.addToTagList(selected_tag,$(current_selected_tag_dom),implement_as);
			
			$('#xepan-component-serverside-creator-tags').val("");
			$('#xepan-component-serverside-creator-apply-as').val("");
			$('#xepan-creator-tag-html').val("");
			self.showAppliedTags();
		});

		self.addDomCodeUI(col3);

	},

	addMoveToTemplate: function(){
		var self = this;

		// manage move to header of footer
		if(self.options.template_editing){
			return;	
		}

		if($(current_selected_dom).parent('.xepan-page-wrapper').length == 0){
			return;
		}

		var wrapper = $('#xepan-component-creator-form .modal-body');
		// edit dom code to change html
		var btn_wrapper = $('<div class="btn-group btn-group-xs"></div>').appendTo($(wrapper));
		var move_to_header = $('<button id="xepan-creator-move-to-header-btn" type="button" title="update html to dom" class="btn btn-primary"> Move To Header</button>').appendTo($(btn_wrapper));
		var move_to_footer = $('<button id="xepan-creator-move-to-footer-btn" type="button" title="remove" class="btn btn-danger"> Move To Footer</button>').appendTo($(btn_wrapper));

		$(move_to_header).click(function(event) {
			self.moveToCall('header');
		});

		$(move_to_footer).click(function(event) {
			self.moveToCall('footer');
		});

	},

	moveToCall: function(move_to) {
		var self = this;

		$(current_selected_dom).removeClass('xepan-component-hover-selector');
		$(current_selected_dom).find('.xepan-component-hoverbar').remove();

		var move_html = $(current_selected_dom).prop('outerHTML');
		$.ajax({
			url :'index.php?page=xepan_cms_componentcreator&cut_page=1',
			type: 'POST',
			data: {
				'template':self.options.template,
				'move_html': move_html,
				'move_to': move_to
			},
			async:false,
			success: function(result){
				eval(result);
				
				$('#xepan-component-creator-form').remove();
				$('.modal-backdrop').remove();
				$(current_selected_dom).remove();
				$.univ().errorMessage('Please reload page to see effect');
			}
		});
	},

	addDomCodeUI: function(parent){
		var self = this;

		if(parent == undefined)
			var wrapper = $('#xepan-component-creator-type-wrapper');
		else
			var wrapper = $(parent);

		// edit dom code to change html
		var dom_code_change_wrapper = $('<div class="btn-group btn-group-xs"></div>').appendTo($(wrapper));
		$('<button class="btn btn-primary">Change HTML Of DOM</button>').appendTo($(dom_code_change_wrapper));
		var dom_code_change_selector = $('<button id="xepan-creator-dom-code-change-html-selector" type="button" title="Dom Selector for html update" class="btn btn-warning"><i class="fa fa-arrows"></i></button>').appendTo($(dom_code_change_wrapper));
		var dom_code_change_selector_parent = $('<button id="xepan-creator-dom-code-change-html-selector-parent" type="button" title="select parent" class="btn btn-default"><i class="fa fa-arrow-up"></i></button>').appendTo($(dom_code_change_wrapper));
		var dom_code_change_save_btn = $('<button id="xepan-creator-dom-code-change-html-save-btn" type="button" title="update html to dom" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>').appendTo($(dom_code_change_wrapper));
		var dom_code_change_remove_btn = $('<button id="xepan-creator-dom-code-change-html-remove-btn" type="button" title="remove" class="btn btn-danger"><i class="fa fa-remove"></i> Remove</button>').appendTo($(dom_code_change_wrapper));
		var dom_html = $('<textarea id="xepan-creator-dom-code-updated-html">').appendTo($(dom_code_change_wrapper));

		// initialize dom object
		var codeDomChangeOutline = DomOutline({
			'onClick': function(element){
				current_selected_dom_of_code_change = element;
				
				$(dom_code_change_remove_btn).show();
				$(dom_code_change_save_btn).show();

				$('#xepan-component-creator-form').modal('show');
				$('#xepan-creator-dom-code-updated-html').val($(current_selected_dom_of_code_change).prop('outerHTML'));
			}
		});

		$(dom_code_change_selector_parent).click(function(event) {
			if(!$(current_selected_dom_of_code_change).length){
				$.univ().errorMessage('first select the dom/element');
				return;
			}

			$(dom_code_change_remove_btn).show();
			$(dom_code_change_save_btn).show();

			current_selected_dom_of_code_change = $(current_selected_dom_of_code_change).parent()[0];
			$('#xepan-creator-dom-code-updated-html').val($(current_selected_dom_of_code_change).prop('outerHTML'));
		});

		$('#xepan-creator-dom-code-change-html-selector').click(function(event) {
			$('#xepan-component-creator-form').modal('hide');
			codeDomChangeOutline.start();
			return false;
		});

		$('#xepan-creator-dom-code-change-html-save-btn').click(function(event) {
			if(!$(current_selected_dom_of_code_change).length){
				$.univ().errorMessage('first select the dom/element');
				return;
			}

			current_selected_dom_of_code_change = $(current_selected_dom_of_code_change).prop('outerHTML', $('#xepan-creator-dom-code-updated-html').val());
			$(this).hide();
			$(dom_code_change_remove_btn).hide();
			$.univ().successMessage('Seleced Element/Dom Html Updated');
		});

		$(dom_code_change_remove_btn).click(function(event) {
			if(!$(current_selected_dom_of_code_change).length){
				$.univ().errorMessage('first select the dom/element');
				return;
			}

			// $(current_selected_dom_of_code_change).prop('outerHTML', "");
			$(current_selected_dom_of_code_change).remove();
			$('#xepan-creator-dom-code-updated-html').val("");

			$.univ().successMessage('Selected Element Removed');
		});

	},

	showAppliedTags: function(){
		var self = this;

		$('#xepan-creator-implement-tag-wrapper').html("");
		$.each(tags_associate_list, function(index, data) {
			var applied_btn = $('<div class="btn btn-success btn-sm" type="button">'+data.tag+'('+data.implement_as+')</div>').appendTo($('#xepan-creator-implement-tag-wrapper'));
			var delete_btn = $('<span class="xepan-creator-delete-applied-tag label label-danger" data-id='+index+'>x</span>').appendTo($(applied_btn));
			$(delete_btn).click(function(event){

			 	delete tags_associate_list[$(this).attr('data-id')];
			 	self.showAppliedTags();
			});
		});
	},

	createClientSideComponentUI: function(){
		// create UI 
		var self = this;

		repitative_selected_dom = 0;
		current_selected_tag_dom = 0;
		tags_associate_list = [];
		
		$creator_wrapper =  $('#xepan-component-creator-type-wrapper');
		// $('<div class="alert alert-success"> Client Side </div>').appendTo($creator_wrapper);

		// xepan component
		if($(current_selected_dom).hasClass('xepan-component')){
			$('<input type="checkbox" id="xepan-cmp-creator-xepan-component" checked /><label for="xepan-cmp-creator-xepan-component"> Create Component</label>').appendTo($creator_wrapper);
		}else{
			$('<input type="checkbox" id="xepan-cmp-creator-xepan-component" /><label for="xepan-cmp-creator-xepan-component"> Create Component</label>').appendTo($creator_wrapper);
		}

		// sortable component
		if($(current_selected_dom).hasClass('xepan-sortable-component'))
			$('<input checked type="checkbox" id="xepan-cmp-creator-xepan-sortable-component" /><label for="xepan-cmp-creator-xepan-sortable-component"> Make Sortable/Droppable</label>').appendTo($creator_wrapper);
		else
			$('<input type="checkbox" id="xepan-cmp-creator-xepan-sortable-component" /><label for="xepan-cmp-creator-xepan-sortable-component"> Make Sortable/Droppable</label>').appendTo($creator_wrapper);
		
		// editable text
		if($(current_selected_dom).hasClass('xepan-editable-text'))
			$('<input checked type="checkbox" id="xepan-cmp-creator-xepan-editable-text" /><label for="xepan-cmp-creator-xepan-editable-text"> Create Editable Text</label>').appendTo($creator_wrapper);	
		else
			$('<input type="checkbox" id="xepan-cmp-creator-xepan-editable-text" /><label for="xepan-cmp-creator-xepan-editable-text"> Create Editable Text</label>').appendTo($creator_wrapper);	
			
		// editable text
		if($(current_selected_dom).hasClass('xepan-no-richtext'))
			$('<input checked type="checkbox" id="xepan-cmp-creator-xepan-no-richtext" /><label for="xepan-cmp-creator-xepan-no-richtext"> No Rich Text</label>').appendTo($creator_wrapper);
		else
			$('<input type="checkbox" id="xepan-cmp-creator-xepan-no-richtext" /><label for="xepan-cmp-creator-xepan-no-richtext"> No Rich Text</label>').appendTo($creator_wrapper);
		
		// no move
		if($(current_selected_dom).hasClass('xepan-no-move'))
			$('<input checked type="checkbox" id="xepan-cmp-creator-xepan-no-move" /><label for="xepan-cmp-creator-xepan-no-move">Disabled Moving</label>').appendTo($creator_wrapper);
		else
			$('<input type="checkbox" id="xepan-cmp-creator-xepan-no-move" /><label for="xepan-cmp-creator-xepan-no-move">Disabled Moving</label>').appendTo($creator_wrapper);

		// no delete 
		if($(current_selected_dom).hasClass('xepan-no-delete'))
			$('<input checked type="checkbox" id="xepan-cmp-creator-xepan-no-delete" /><label for="xepan-cmp-creator-xepan-no-delete">Disabled Delete</label>').appendTo($creator_wrapper);
		else
			$('<input type="checkbox" id="xepan-cmp-creator-xepan-no-delete" /><label for="xepan-cmp-creator-xepan-no-delete">Disabled Delete</label>').appendTo($creator_wrapper);

		// component name
		$('<div><label for="xepan-cmp-creator-component-name">Component Name</label><input id="xepan-cmp-creator-component-name" /></div>').appendTo($creator_wrapper);
		$('#xepan-cmp-creator-component-name').val($(current_selected_dom).attr('xepan-component-name'));

		$('#xepan-cmp-creator-xepan-editable-text').change(function(event) {
			if($('#xepan-cmp-creator-xepan-editable-text:checked').size() > 0 ){
				if($(current_selected_dom).children('.xepan-component').length > 0){
					$.univ().errorMessage('this element contains existing component, can not convert to editable text');
					$('#xepan-cmp-creator-xepan-editable-text').prop('checked', false);
				}
			}
		});

		// dynamic lister selector input box
		$('<div><label for="xepan-cmp-creator-dynamic-list-selector">Dynamic Option List Selector</label><input id="xepan-cmp-creator-dynamic-list-selector" /></div>').appendTo($creator_wrapper);
		$('#xepan-cmp-creator-dynamic-list-selector').val($(current_selected_dom).attr('xepan-component-dynamic-option-list'));

		// selector before remove
		$('<div><label for="xepan-cmp-creator-selector-to-remove-before-save">Selector To Remove Before Page Save</label><textarea id="xepan-cmp-creator-selector-to-remove-before-save" ></textarea></div>').appendTo($creator_wrapper);
		$('#xepan-cmp-creator-selector-to-remove-before-save').val($(current_selected_dom).attr('xepan-selector-to-remove-before-save'));

		// eval jquery code
		$('<div><label for="xepan-cmp-creator-code-run-before-save">Jquery Code To Run Before Page Save</label><textarea id="xepan-cmp-creator-code-run-before-save"></textarea></div>').appendTo($creator_wrapper);
		$('#xepan-cmp-creator-code-run-before-save').val($(current_selected_dom).attr('xepan-cmp-creator-code-run-before-save'));

		self.addDomCodeUI();

		$('<hr/>').appendTo($creator_wrapper);
		var add_dynamic_html = 
								'<h3>Dynamic Options</h3>'+
								'<div class="row xepan-cmp-creator-dynamic-option">'+
									'<div class="col-md-4">'+
										'<div class="form-group">'+
											'<label for="xepan-creator-dynamic-selector" class="control-label">Selector:</label>'+
											'<input class="form-control" id="xepan-creator-dynamic-selector">'+
										'</div>'+
									'</div>'+
									'<div class="col-md-2">'+
										'<div class="form-group">'+
											'<label for="xepan-creator-dynamic-title" class="control-label">Title:</label>'+
											'<input class="form-control" id="xepan-creator-dynamic-title">'+
										'</div>'+
									'</div>'+
									'<div class="col-md-2">'+
										'<div class="form-group">'+
											'<label for="xepan-creator-dynamic-attribute" class="control-label">Attribute:</label>'+
											'<select class="form-control" id="xepan-creator-dynamic-attribute">'+
												'<option value="">select</option>'+
												'<option value="text">text</option>'+
												'<option value="href">href</option>'+
												'<option value="css">css</option>'+
												'<option value="src">src</option>'+
												'<option value="label">label</option>'+
												'<option value="attr">attribute</option>'+
											'</select>'+
										'</div>'+
									'</div>'+
									'<div class="col-md-2">'+
										'<div class="form-group">'+
											'<label for="xepan-creator-dynamic-additional" class="control-label">Additional:</label>'+
											'<input class="form-control" id="xepan-creator-dynamic-additional">'+
										'</div>'+
									'</div>'+
									'<div class="col-md-2">'+
										'<label class="control-label"> </label>'+
										'<button class="btn btn-primary btn-block" id="xepan-creator-dynamic-add-btn">Add</button>'+
									'</div>'+
								'</div>';

		$(add_dynamic_html).appendTo($creator_wrapper);
		
		$existing_list = $('<div id="xepan-creator-existing-dynamic-list"></div>').appendTo($creator_wrapper);

		var find_str = "xepan-dynamic-option-";
		$(current_selected_dom).each(function(index) {
			var elem = this;
			$.each(this.attributes, function( index, attr ) {
				if(attr.name.indexOf(find_str)===0){
					self.addDynamicOptionToList(attr.value);
				}
			});
		});

		$('#xepan-creator-dynamic-add-btn').click(function(){

			var selector =  $.trim($('#xepan-creator-dynamic-selector').val());
			var title =  $.trim($('#xepan-creator-dynamic-title').val());
			var attribute =  $.trim($('#xepan-creator-dynamic-attribute').val());
			var additional =  $.trim($('#xepan-creator-dynamic-additional').val());
			
			if(!selector.length){
				var form_group = $('#xepan-creator-dynamic-selector').closest('.form-group');
				$(form_group).addClass('has-error');
				$('<p class="xepan-creator-form-error-text text-danger">must not be empty</p>').appendTo($(form_group));
				return;
			}

			if(!title.length){
				var form_group = $('#xepan-creator-dynamic-title').closest('.form-group');
				$(form_group).addClass('has-error');
				$('<p class="xepan-creator-form-error-text text-danger">must not be empty</p>').appendTo($(form_group));
				return;
			}

			if(!attribute.length){
				var form_group = $('#xepan-creator-dynamic-attribute').closest('.form-group');
				$(form_group).addClass('has-error');
				$('<p class="xepan-creator-form-error-text text-danger">must not be empty</p>').appendTo($(form_group));
				return;
			}

			var str = selector+'|'+title+'|'+attribute+'|'+additional;

			$('#xepan-creator-dynamic-selector').val("");
			$('#xepan-creator-dynamic-title').val("");
			$('#xepan-creator-dynamic-attribute').val("");
			$('#xepan-creator-dynamic-additional').val("");

			self.addDynamicOptionToList(str.trim('|'));
		});

		// if(this.isExistingComponent()){
			// reload values or create required run time components
		// }

		// error wrapper removed
		$('.form-group input').keyup(function(event) {
			$(this).closest('.form-group').removeClass('has-error');
			$(this).closest('.form-group').find('p.xepan-creator-form-error-text').remove();
		});

		$('.form-group select').change(function(event) {
			$(this).closest('.form-group').removeClass('has-error');
			$(this).closest('.form-group').find('p.xepan-creator-form-error-text').remove();
		});
	},

	addDynamicOptionToList: function(dynamic_option){

		option_array = dynamic_option.split('|');
		var selector = option_array[0];
		var title = option_array[1];
		var attribute = option_array[2];
		var additional = option_array[3];
		if( additional == undefined){
			additional = "";
			dynamic_option = dynamic_option.trim('|');
		}

		var html = '<div class="row xepan-creator-existing-dynamic-list-added" data-dynamic-option="'+dynamic_option+'">'+
									'<div class="col-md-4">'+
										'<div class="form-group">'+
											// '<label for="xepan-creator-dynamic-selector" class="control-label">Selector:</label>'+
											'<input disabled class="form-control" id="xepan-creator-dynamic-selector" value="'+selector+'">'+
										'</div>'+
									'</div>'+
									'<div class="col-md-2">'+
										'<div class="form-group">'+
											// '<label for="xepan-creator-dynamic-title" class="control-label">Title:</label>'+
											'<input disabled class="form-control" id="xepan-creator-dynamic-title" value="'+title+'">'+
										'</div>'+
									'</div>'+
									'<div class="col-md-2">'+
										'<div class="form-group">'+
											// '<label for="xepan-creator-dynamic-attribute" class="control-label">Attribute:</label>'+
											'<input disabled class="form-control" id="xepan-creator-dynamic-attribute" value="'+attribute+'">'+
										'</div>'+
									'</div>'+
									'<div class="col-md-2">'+
										'<div class="form-group">'+
											// '<label for="xepan-creator-dynamic-additional" class="control-label">Additional:</label>'+
											'<input disabled class="form-control" id="xepan-creator-dynamic-additional" value="'+additional+'">'+
										'</div>'+
									'</div>'+
									'<div class="col-md-2 dynamic-option-remove-wrapper">'+
									'</div>'+
								'</div>';
		record_row =  $(html).appendTo($('#xepan-creator-existing-dynamic-list'));

		$('<button class="btn btn-danger btn-block" id="xepan-creator-dynamic-option-remove-btn">Remove</button>')
			.appendTo($(record_row).find('.dynamic-option-remove-wrapper'))
			.click(function(event) {
				$(this).closest('.row').remove();
			});
	}

});