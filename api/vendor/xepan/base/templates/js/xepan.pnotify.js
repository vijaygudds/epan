$.each({
	notify: function(xTitle, xText, xType, isDesktop, callback, sticky, xIcon){
		if(isDesktop != undefined && isDesktop != null && isDesktop != false) {
			var nn = new PNotify(
			{ 
				title: xTitle?xTitle:"Notification",
				text: (isDesktop ===true) ? xText : isDesktop,
				type: xType==null?"notice":xType,
				hide: true,
				desktop: {
						desktop: true
					}
			});
		}

		if(sticky != undefined && sticky != null) {
			// But also show sticky notification on web page, in case user missed desktop notification
			var nn = new PNotify(
			{ 
				title: xTitle?xTitle:"Notification",
				text: xText,
				type: xType==null?"notice":xType,
				hide: false,
				icon: xIcon,
				desktop: {
						desktop: false
					},
				history: {
        			menu: true
    			}
			});
		}else{
			var nn = new PNotify(
			{ 
				title: xTitle,
				text: xText,
				icon: xIcon,
				type: xType==null?"success":xType,
				history: {
        			menu: false
    			}
    		}
			);
		}
	}
}, $.univ._import);