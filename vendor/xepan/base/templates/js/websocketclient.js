$.each({
	runWebSocketClient: function(host, uu_id){

		try {
		      socket = new WebSocket(host);
		      socket.onopen = function () {
		          console.log('connection is opened');
		          socket.send(JSON.stringify({'cmd':'register','uu_id':uu_id}));
		          return;
		      };
		      socket.onmessage = function (msg) {
		        // console.log(msg);
		        if(msg.data.length == 0){
		        	// console.log('msg.data.length == 0, returning');
		        	return;
		        }
		        var $data = JSON.parse(msg.data);
		        // console.log('Parsed data');
		        // console.log($data);
		        // console.log($data.message.length);
				if($data.message.length > 0){
						
						var title= "Notification";
						var type= "notice";
						var desktop = true;
						var sticky = true;
						var icon = undefined;

						if (("title" in $data) !=false) title = $data.title;
						if (("type" in $data) !=false) type = $data.type;
						if (("desktop" in $data) ==false) 
							desktop = undefined;	
						else
							desktop = $data.desktop;	

						if (("sticky" in $data) ==false) skicky = undefined;						
						if (("icon" in $data) !=false) icon = undefined;						

						$.univ().notify(title, $data.message, type, desktop, undefined, sticky, icon);
				  }
				  if (("js" in $data) !=false){
				  	eval($data.js);
				  }
				return;
		      };
		      socket.onclose = function (e) {
		          console.log('connection is closed '+e.reason);
		          setTimeout(function() {
		          		// console.log('raload auto '+host);
		          		// console.log('raload '+uu_id);
				      	$.univ().runWebSocketClient(host,uu_id);
				    }, 5000);
		          return;
		      };
		      socket.onerror = function(err){
		      	console.log('connection on error');
		      	console.log(err);
		      	return;
		      };
		  } catch (e) {
		      console.log(e);
		  }
	}
}, $.univ._import);