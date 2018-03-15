define(['jquery', 'jquery/ui', 'domReady'], function ($) {
    "use strict";
    var xhr = null;
    var searchAutoComplete =
        {
            ajaxSearch: function (e) {
                var q = $("#search").val();

                var config = {
                    baseURL: window.baseURL,
                    loaderAjax: window.loaderAjax
                };

                /* if there is a previous ajax request, then we abort it and then set xhr to null */
                if( xhr != null ) {
                    xhr.abort();
                    xhr = null;
                }

                xhr = $.ajax({
                    url: config.baseURL + 'searchautocomplete',
                    dataType: 'json',
                    type: 'post',
                    data: { q : q },
                    success: function(data) {
                        $('.searchautocomplete').show();
                        $('.searchautocomplete').find('.prod-container').html(data.results);
                        $( ".wpx-footer" ).text(config.resultFooter);
                        $('.wpx-search-autocomplete ul li').click(function(){
                            $('#search').val($(this).find('.qs-option-name').text());
                            $('#search_mini_form').submit();
                        });
                    },
                    complete: function(){
                        //Finished processing, hide the Progress!
                        $(".search .control").removeClass("loader-ajax").css('background-image', 'none');;

                    }
                });
            }
        };

    return searchAutoComplete;
});
