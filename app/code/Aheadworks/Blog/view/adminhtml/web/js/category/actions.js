define([
    'jquery',
    'loadingPopup'
], function ($) {
    return function (config, element) {
        config = config || {};
        $(element).on('click', function (event) {
            (function (action, url) {
                switch (action) {
                    case 'save' :
                        $('#category_edit_form').trigger('submit');
                        break;
                    case 'delete':
                        if (confirm(config.confirm)){
                            location.href = url;
                            jQuery('body').loadingPopup();
                        }
                        break;
                    default :
                        var params = {};
                        var $categoryContainer = $('#category-edit-container'),
                            messagesContainer = $('.messages');
                        messagesContainer.html('');
                        params = jQuery.extend(params || {}, {
                            form_key: FORM_KEY
                        });
                        $.ajax({
                            url: url + (url.match(new RegExp('\\?')) ? '&isAjax=true' : '?isAjax=true' ),
                            data: params,
                            context: $('body'),
                            showLoader: true
                        }).done(function(data){
                            if (!data.error) {
                                $(document).trigger('awblog.catEdit.ajaxUpdate', [data]);
                            }
                            if (data.messages && data.messages.length > 0) {
                                messagesContainer.html(data.messages);
                            }
                            if (data.toolbar) {
                                $('[data-ui-id="page-actions-toolbar-content-header"]').replaceWith(data.toolbar);
                                $('[data-ui-id="page-actions-toolbar-content-header"]').trigger('contentUpdated');
                            }
                        });
                        break;
                }
            })(config.action, config.url);
        });
    };
});