jQuery.widget("ui.xepanTool",{
	
	_create: function(){
		self = this;
		self.setupTool();
	},

	setupTool: function(){
		self = this;
		$(this.element).disableSelection().draggable({
			inertia:true,
			appendTo: 'body',
			connectToSortable: '.xepan-sortable-component',
			// helper: function(event, ui){
			// 	return $($(this).xepanTool('getHTML'));
			// },
			helper: "clone",
			start: function(event, ui){
				origin='toolbox';
				xepan_drop_component_html = $($(this).xepanTool('getHTML'));
			},
			revert: 'invalid',
		  	tolerance: 'pointer'
		});
	},

	getHTML(){
		return this.options.drop_html;
	}
	
});