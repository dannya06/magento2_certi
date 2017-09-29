define([
    'Magento_Ui/js/form/form',
    './adapter'
], function (Form, adapter) {
    'use strict';

    return Form.extend({
        initAdapter: function () {
            adapter.on({
                'reset': this.reset.bind(this),
                'save': this.save.bind(this, true, 'save'),
                'schedule': this.save.bind(this, true, 'schedule'),
                'publish': this.save.bind(this, true, 'publish'),
                'saveAsDraft': this.save.bind(this, false, 'save_as_draft'),
                'saveAndContinue': this.save.bind(this, false, 'save_and_continue')
            });
            return this;
        },
        save: function (redirect, action) {
            this.source.set('data.action', action);
            this._super(redirect);
        }
    });
});
