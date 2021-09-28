$.each({
	runIntro: function(object){

		$('[data-intro]:hidden').each(function(index,obj){
			var $t = $(this);
		    $t
		        .attr({
		            'data-intro-hidden' : $t.attr('data-intro'),
		        })
		        .removeAttr('data-intro')
		    ;
		});

		$('[data-intro-hidden]:visible').each(function(index,obj){
			var $t = $(this);
		    $t
		        .attr({
		            'data-intro' : $t.attr('data-intro-hidden'),
		        })
		        .removeAttr('data-intro-hidden')
		    ;
		});

		if(typeof object === undefined){
			introJs().start();
		}
		else{
			introJs(object).start();
		}
	}
},$.univ._import);