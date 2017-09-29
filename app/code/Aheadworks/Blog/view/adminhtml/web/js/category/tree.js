define([
    "jquery",
    "jquery/ui",
    "jquery/jstree/jquery.jstree"
], function($){
    'use strict';

    $.widget("awblog.categoryTree", {
        options: {
            treeContainerId: 'category-edit-tree',
            treeInitData: [],
            addRootBtnId: 'add_root_category_button',
            addSubBtnId: 'add_subcategory_button'
        },
        _create: function() {
            this.element.jstree({
                plugins: ["themes", "json_data", "ui", "crrm"],
                json_data: {data: this.options.treeInitData},
                ui: {select_limit: 1}
            });
            this.treeContainer = $('#' + this.options.treeContainerId);
            this._bind();
        },
        destroy: function() {
            this.element.jstree('destroy');
            this._unbind();
        },
        _bind: function() {
            this.element.on("select_node.jstree", $.proxy(this._selectNode, this));
            $(document).on('awblog.catEdit.ajaxUpdate', $.proxy(this._ajaxUpdate, this));
            $('#' + this.options.addRootBtnId).on('click', $.proxy(this._addRoot, this))
            $('#' + this.options.addSubBtnId).on('click', $.proxy(this._addSub, this))
        },
        _unbind: function() {
            this.element.off('select_node.jstree');
            $(document).off('awblog.catEdit.ajaxUpdate');
            $('#' + this.options.addRootBtnId).off('click');
            $('#' + this.options.addSubBtnId).off('click');
        },
        _selectNode: function(event, data) {
            this._doAjax(data.rslt.obj.attr('url'), {});
        },
        _addRoot: function(event) {
            this._doAjax(this.options.addRootUrl, {});
        },
        _addSub: function(event) {
            this._doAjax(this.options.addSubUrl, {});
        },
        _doAjax: function(url, params) {
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
        },
        _ajaxUpdate: function(event, data) {
            if (data.tree) {
                this.treeContainer.parent().html(data.tree).trigger('contentUpdated');
                this.destroy();
            }
        }
    });

    return $.awblog.categoryTree;
});