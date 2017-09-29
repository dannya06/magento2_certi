define([
    "jquery",
    "mage/backend/form",
    "jquery/ui"
], function($){
    "use strict";

    $.widget("awblog.categoryForm", $.mage.form, {
        options: {
            categoryContainerId: 'category-edit-container'
        },
        _create: function() {
            this.categoryEditContainer = $('#' + this.options.categoryContainerId);
            this._bind();
        },
        destroy: function() {
            this._unbind();
        },
        _bind: function()
        {
            $(document).on('awblog.catEdit.ajaxUpdate', $.proxy(this._ajaxUpdate, this));
        },
        _unbind: function() {
            $(document).off('awblog.catEdit.ajaxUpdate', $.proxy(this._ajaxUpdate, this));
        },
        _ajaxUpdate: function(event, data) {
            if (data.form) {
                this.categoryEditContainer.html(data.form).trigger('contentUpdated');
                this.destroy();
            }
        }
    });

    return $.awblog.categoryForm;
});