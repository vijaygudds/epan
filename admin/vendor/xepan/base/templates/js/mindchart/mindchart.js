jQuery.widget("ui.xepan_mindchart",{
	options:{
		data:[
				{id: 1, name: 'Root', parent: 0, level:1}
			],
		maxLevel:null,
		addbutton_false_at_level:null,
		deletebutton_false_at_level:null,
		Labels:[{"add":'Add Category'},{"add":'Add Subcategory'},{"add":'Add Example'}],
		field:undefined,
		allowEdit:true,
		showControls:true
	},

	_create: function(){
		$(this.element).orgChart({
			data: this.options.data,
            showControls: this.options.showControls,
            allowEdit: this.options.allowEdit,
            maxLevel:this.options.maxLevel,
            addbutton_false_at_level:this.options.addbutton_false_at_level,
            Labels:this.options.Labels,
            field:this.options.field
		});
	},
});