$.each({
	successMessage: function(msg){
    $.univ().notify('Success',msg,'success',null,null,null);
  },
  infoMessage: function(msg){
		$.univ().notify('Info',msg,'info',null,null,null);
	},
	errorMessage: function(msg){
		$.univ().notify('Error',msg,'error',null,null,null);
	}
},$.univ._import);

(function ($) {
      $.each(['show', 'hide'], function (i, ev) {
        var el = $.fn[ev];
        $.fn[ev] = function () {
          this.trigger(ev);
          return el.apply(this, arguments);
        };
      });
    })(jQuery);

$.ui.dialog.prototype._allowInteraction = function(e) {
    return !!$(e.target).closest('.ui-dialog, .ui-datepicker, .select2-dropdown, .mce-window, .modal-dialog').length;
};

$.fn.__tabs = $.fn.tabs;
$.fn.tabs = function (a, b, c, d, e, f) {
  var base = window.location.href.replace(/#.*$/, '');
  $('ul>li>a[href^="#"]', this).each(function () {
    var href = $(this).attr('href');
    $(this).attr('href', base + href);
  });
  $(this).__tabs(a, b, c, d, e, f);
};