$.each({
  setup_shortcuts: function(shortcuts,popup){
        shortcut.add("Alt+q", function(event) {
          $(popup).find('.modal-body').html('');
          $input = $('<input>');
          $resultdiv= $('<div>');
          $(popup).find('.modal-body').append($input);
          $(popup).find('.modal-body').append($resultdiv);
          $(popup).modal();

          $modal_content = $(popup).find('.modal-content');

          $(popup).on('shown.bs.modal', function () {
              $input.focus();
              $resultdiv.html('');
          });

          var fuse = new Fuse(shortcuts, {
                                                  shouldSort: true,
                                                  tokenize: true,
                                                  matchAllTokens: false,
                                                  findAllMatches: true,
                                                  threshold: 0.6,
                                                  location: 0,
                                                  distance: 500,
                                                  maxPatternLength: 32,
                                                  minMatchCharLength: 1,
                                                  keys: [
                                                    "title",
                                                    "keywords",
                                                    "description"
                                                  ]
                                                }
                                      ); // "list" is the item array
          $input.autocomplete({
            appendTo: $modal_content,
            source: function(request, response) {
                    var new_arr = $.map(fuse.search(request.term), function(item) {
                      return {label : item.title , value : item.normal_access , title: item.title, description: item.description, normal_access: item.normal_access, url: item.url, mode: item.mode, keywords: item.keywords};
                    });                    
                    response(new_arr);
              },
              select: function(event, ui) {
                  var obj = ui.item;
                  if(obj.mode =='frame'){
                    $.univ().frameURL(obj.title, obj.url);
                  }else if(obj.mode =='fullframe'){
                    $.univ().frameURL(obj.title, obj.url,{'width':$(window).width(), 'height': $(window).height(), 'left':'0px','top':'0px', 'dialogClass': 'fullframe'});
                  }else{
                    document.location=obj.url;
                  }
                  $(popup).modal('hide');
                  return false;
              }
            }).data("ui-autocomplete")._renderItem  = function(ul, item) {
                $resultblock = $('<li><h4>'+item.title+'</h4><p>'+item.description+'</p><span class="label label-primary">'+item.normal_access+'</span><hr/></li>');
                return $resultblock
                    .data("ui-autocomplete-item", item)
                    .appendTo(ul);
            }
  });
  }
},$.univ._import);
