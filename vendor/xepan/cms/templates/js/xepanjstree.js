placeTemplateContentRegion_selected = undefined;

$.each({

	placeTemplateContentRegion: function(obj,data,verify_selctor_url,save_url){
		$(obj).on("changed.jstree", function (e, data) {
				placeTemplateContentRegion_selected = data.instance.get_selected(true);
			    // console.log(data.instance.get_selected(true)); // newly selected
			    // console.log(data.changed.selected); // newly selected
			    // console.log(data.changed.deselected); // newly deselected
			    }).jstree({
						'core': {
							'check_callback': true,
							'data': data,
							'dataType':'json'
						},
						"contextmenu":{         
						    "items": function($node) {
						        var tree = $(obj).jstree(true);
						        return {
						            "Rename": {
						                "separator_before": false,
						                "separator_after": false,
						                "label": "Rename",
						                "action": function (obj) { 
						                    tree.edit($node);
						                }
						            }
						        };
						    }
						},
						'plugins': ['wholerow','changed','contextmenu']
					});
		

		$replace_button = $('<button id="replace_button" class="btn btn-success" style="display:none;">Replace Page Template</button>').insertAfter(obj);
		$replace_button.click(function(event) {
			var tag_array=[];
			$.each(placeTemplateContentRegion_selected, function(index, tag) {
				tag_array.push(tag.text);
			});
			$.univ().frameURL('Updating Page',save_url+"&page_content_border="+encodeURIComponent(JSON.stringify(tag_array)));
		});

		$verify_selectors_btn = $('<button id="verify_selectors_btn" class="btn btn-success">Verify start and end selectors on other pages</button>').insertAfter(obj);
		$verify_selectors_btn.click(function(event) {
			$('#replace_button').hide();
			var tag_array=[];
			$.each(placeTemplateContentRegion_selected, function(index, tag) {
				tag_array.push(tag.text);
			});
			$.univ().frameURL(verify_selctor_url+"&page_content_border="+encodeURIComponent(JSON.stringify(tag_array)));
		});
	}

	},$.univ._import);