$.each({dialogPrepare:function(i){var t=$('<div class="dialog dialog_autosize" title="Untitled"><div style="min-height: 300px"></div>').appendTo("body");if(i.noAutoSizeHack&&t.removeClass("dialog_autosize"),t.dialog(i),!$(".ui-dialog-titlebar").hasClass("help_icon-added")){var e=$(".ui-dialog-titlebar").prepend("<span class='fa fa-question-circle dialog-help'></span>");$(".ui-dialog-titlebar").addClass("help_icon-added")}return i.customClass&&t.parent().addClass(i.customClass),$.data(t.get(0),"opener",this.jquery),$.data(t.get(0),"options",i),$(window).resize(function(){t.dialog("option","position",{my:"center",at:"center",of:window})}),t},getDialogData:function(i){var t=this.jquery.closest(".dialog").get(0);if(!t)return null;var e=$.data(t,i);return e?e:null},getFrameOpener:function(){var i=this.getDialogData("opener");return i?$(this.getDialogData("opener")):null},dialogBox:function(i){i.ok_label||(i.ok_label="Ok"),i.ok_class||(i.ok_class="atk-effect-primary");var t=[];return t.push({text:i.ok_label,"class":i.ok_class,click:function(){var i=$(this).find("form");i.length?i.eq(0).submit():$(this).dialog("close")}}),t.push({text:"Cancel",click:function(){$(this).dialog("close")}}),this.dialogPrepare($.extend({bgiframe:!0,modal:!0,width:1e3,position:{my:"top",at:"top+100",of:window},autoOpen:!1,beforeClose:function(){return $(this).is(".atk4_loader")&&!$(this).atk4_loader("remove")?!1:void 0},buttons:t,open:function(i){$("body").css({overflow:"hidden"}).children(".atk-layout").addClass("visible-dialog"),$(i.target).css({"max-height":$(window).height()-180})},close:function(){$("body").css({overflow:"auto"}).children(".atk-layout").removeClass("visible-dialog"),$(this).dialog("destroy"),$(this).remove()}},i))},dialogURL:function(i,t,e,o){"undefined"==typeof t&&(t=i,i="Untitled Dialog");var a=this.dialogBox($.extend(e,{title:i,autoOpen:!0}));return a.closest(".ui-dialog").hide().fadeIn("slow"),a.atk4_load(t,o),a.dialog("open")},frameURL:function(i,t,e,o){return e=$.extend({buttons:{}},e),this.dialogURL(i,t,e,o)},dialogOK:function(i,t,e,o){var a=this.dialogBox($.extend({title:i,width:450,close:e,open:function(){$(this).parents(".ui-dialog-buttonpane button:eq(0)").focus()},buttons:{Ok:function(){$(this).dialog("close")}}},o));a.html(t),a.dialog("open")},dialogConfirm:function(i,t,e,o){var a=this.dialogBox($.extend({title:i,width:450,height:200},o));a.html("<form></form>"+t),a.find("form").submit(function(i){i.preventDefault(),e&&e(),a.dialog("close")}),a.dialog("open")},dialogError:function(i,t,e){this.dialogConfirm("Error",'<span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>'+i,null,$.extend({buttons:{Ok:function(){$(this).dialog("close"),e&&e()}}},t))},dialogAttention:function(i,t,e){this.dialogConfirm("Attention!",'<span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>'+i,null,$.extend({buttons:{Ok:function(){$(this).dialog("close"),e&&e()}}},t))},message:function(i,t){t.find("span").text(i),t.find(".do-close").click(function(i){i.preventDefault(),t.remove()});var e=$("body");return e.length?(t.prependTo(e),t):(alert(i),!1)},successMessage:function(i,t){var e=$('<div class="atk-layout-row" style="position: fixed; z-index: 1000">    <div class="atk-swatch-green atk-cells atk-padding-small">      <div class="atk-cell atk-jackscrew"><i class="icon-info"></i>&nbsp;<span>Agile Toolkit failed to automatically renew certificate.</span></div>      <div class="atk-cell"><a href="javascript: void()" class="do-close"><i class="icon-cancel"></i></a></div>    </div>  </div>');this.message(i,e),setTimeout(function(){e.remove()},t?t:8e3)},errorMessage:function(i,t){var e=$('<div class="atk-layout-row" style="position: fixed; z-index: 1000">    <div class="atk-swatch-red atk-cells atk-padding-small">      <div class="atk-cell atk-jackscrew"><i class="icon-attention"></i>&nbsp;<span>Agile Toolkit failed to automatically renew certificate.</span></div>      <div class="atk-cell"><a href="javascript: void()" class="do-close"><i class="icon-cancel"></i></a></div>    </div>  </div>');this.message(i,e),t&&setTimeout(function(){e.remove()},t)},closeDialog:function(){var i=this.getFrameOpener();i&&(this.jquery.closest(".dialog").dialog("close"),this.jquery=i)},loadingInProgress:function(){this.successMessage("Loading is in progress. Please wait")}},$.univ._import);var oldcr=$.ui.dialog.prototype._create;$.ui.dialog.prototype._create=function(){var i=this;$("<div/>").insertBefore(this.element).on("remove",function(){i.element.remove()}),oldcr.apply(this,arguments)},$.widget("ui.dialog",$.ui.dialog,{_allowInteraction:function(i){return this._super(i)?!0:i.target.ownerDocument!=this.document[0]?!0:$(i.target).closest(".cke_dialog, .mce-window, .moxman-window").length?!0:$(i.target).closest(".cke").length?!0:void 0}});