jQuery.widget("ui.menudesigner",{
	self: undefined,
	
	options:{
		designing_menu:'',
		available_menus:{},
		saved_menus:{},
		top_menu_caption:''
	},

	_create: function(){
		var self=this;

		console.log(self.options);

		self.createUI();
		self.createAvailableMenus();
		self.createSavedMenus();
	},

	createUI: function(){
		var self = this;

		this.widgetui = $('<div class="row"></div>').appendTo(this.element);
		this.left_panel = $('<div class="col-md-6" style="max-height:500px;overflow-y:auto;"></div>').appendTo(this.widgetui);
		this.right_panel = $('<div class="col-md-6"></div>').appendTo(this.widgetui);

		// this.save_button = $('<button>SAVE</button>').appendTo(this.widgetui);
		var menu_name_field = '<div class="input-group">'+
			'<span class="input-group-btn">'+
				'<button class="btn btn-primary" type="button">'+self.options.designing_menu+'</button>'+
			'</span>'+
			'<input placeholder="Top Menu Caption " class="form-control top-menu-caption" type="line" value="'+self.options.top_menu_caption+'">'+
			'<span class="input-group-btn">'+
				'<button class="btn btn-primary xepanp-menu-save-button" type="button">Save Menu</button>'+
			'</span>'+
		'</div>';

		this.right_panel_menu_name = $(menu_name_field).appendTo(this.right_panel);
		this.right_panel_ui = $('<ul class="saved_menus dd-list" style="min-height:100px;border:1px dashed #2980b9;background:#E9FDFB;"></ul>').appendTo(this.right_panel);
		$('<span>Drop list from left panel in above section</span>').appendTo(this.right_panel);

		$('.xepanp-menu-save-button').click(function(){
			self.saveMenu();
		});
	},

	saveMenu: function(){
		var self = this;

		if($('.saved_menus li').length == 0){
			$.univ().errorMessage('cannot save, first drop menus from left side');
			return;
		} 

		var save_list = {};
		var top_menu_caption = $('.top-menu-caption').val().trim();
		if(top_menu_caption.length == 0) top_menu_caption = self.options.designing_menu;
		save_list[top_menu_caption] = [];

		$('.saved_menus li').each(function(index,menu){

			var t_p = "";
			if( $(menu)[0].hasAttribute('data-url_param') && $(menu).attr('data-url_param').length)
				var t_p = $.parseJSON($(menu).attr('data-url_param'));

			save_list[top_menu_caption].push({
					'name':$(menu).attr('data-name'),
					'url':$(menu).attr('data-url'),
					'url_param':t_p,
					'caption':$(menu).attr('data-caption'),
					'icon':$(menu).attr('data-icon'),
				});

		});

		$.ajax({
			url: "index.php?page=xepan_base_menudesigner_save&cut_page=1",
			type: 'POST',
			data: {
					menulist: JSON.stringify(save_list),
					menuname: self.options.designing_menu,
				},
		})
		.done(function(ret) {
			eval(ret);
		})
		.fail(function(ret) {
			eval(ret);
			$.univ().errorMessage('failed, not saved');
		});
	},

	createAvailableMenus: function(){
		var self=this;
		
		$.each(self.options.available_menus, function(MainMenu, SubMenus) {
			var heading = $('<div class="xepan-app-menu-wrapper"><h4>'+MainMenu+'</h4></div>').appendTo(self.left_panel);
			var ul = $('<ul class="dd-list"></ul>').appendTo(heading);
			$.each(SubMenus, function(index, menu) {
				var list_html = '<li class="dd-item dd-item-list dd-handle available_menu" data-name="'+menu.name+'" data-caption="'+menu.name+'" data-url="'+menu.url+'" data-icon="'+menu.icon+'">'+
								'<span class="menu-name">'+menu.name+'</span>'+
								'<div class="nested-links" style="display:none;">'+
									'<span style="color:gray;">Double click to change caption &nbsp;</span>'+
									'<span style="color:red;cursor:pointer;" class="remove-menu-single-list-btn" href="#"><i class="fa fa-trash"></i></span>'+
								'</div>'+
							'</li>';
			 	var li = $(list_html).appendTo(ul).attr('data-url_param',JSON.stringify(menu.url_param));
			 	// console.log($(li).attr('urlparam'));
			});
		});

		$('.available_menu').draggable({
						inertia:true,
						appendTo:'body',
						connectToSortable:'.saved_menus',
						helper:'clone',
						start: function(event,ui){
						},
						stop: function(event,ui){
						}
					});
	},

	createSavedMenus: function(){
		var self=this;

		$.each(self.options.saved_menus, function(index, SubMenus) {
			$.each(SubMenus, function(index, menu) {
				var list_html = '<li class="dd-item dd-handle available_menu" data-name="'+menu.name+'" data-caption="'+menu.caption+'" data-url="'+menu.url+'" data-icon="'+menu.icon+'">';
					list_html += '<span class="menu-name">';
					if(menu.caption != menu.name) 
						list_html += menu.caption+ ' : ( ' + menu.name + ' )';
					else
						list_html += menu.name;
					list_html += "</span>";

					list_html += '<div class="nested-links" style="display:none;">'+
									'<span style="color:gray;">Double click to change caption &nbsp;</span>'+
									'<span style="color:red;cursor:pointer;" class="remove-menu-single-list-btn" ><i class="fa fa-trash"></i></span>'+
								'</div>';
					list_html +='</li>';
			 	var li = $(list_html).appendTo(self.right_panel_ui).attr('data-url_param',JSON.stringify(menu.url_param));
			});
		});

		$('.saved_menus li').livequery(function(){ 
			$(this).dblclick(function(){
				var caption = prompt("Menu Caption", $(this).attr('data-caption'));
				if (caption != null) {
					$(this).attr('data-caption',caption).find('.menu-name').text(caption+" : ( "+$(this).data('name')+" ) ");
				}
			});

			$(this).hover(function(){
				$(this).find('.nested-links').show();
			},function(){
				$(this).find('.nested-links').hide();
			});


			$(this).find('.remove-menu-single-list-btn').click(function(){
				$(this).closest('li').remove();
			});
		});



		$(self.right_panel_ui).sortable({
									appendTo:'body',
									connectWith:'.saved_menus',
									// handle: '.xepan-component-drag-handler',
									cursor: "move",
									revert: true,
									tolerance: "pointer"
								});

	}
});
