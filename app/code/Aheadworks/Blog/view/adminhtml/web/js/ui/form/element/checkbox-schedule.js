define([
    'ko',
    'jquery',
    'Magento_Ui/js/form/element/abstract'
], function (ko, $, Abstract) {
    'use strict';

    return Abstract.extend({
        defaults: {
            virtualStatus: '',
            saveButton: '[data-ui-id=save-button]',
            scheduleButton: '[data-ui-id=schedule-button]',
            publishButton: '[data-ui-id=publish-button]',
            saveAndContinueButton: '[data-ui-id=save-and-continue-button]'
        },
        initialize: function () {
            this._super();
            this.allSaveButtons = [
                this.saveButton,
                this.scheduleButton,
                this.publishButton,
                this.saveAndContinueButton
            ].join(',');
            return this;
        },
        onUpdate: function () {
            this._super();
            var isScheduledPost = (this.virtualStatus == 'scheduled');
            $(this.allSaveButtons).hide();
            if (this.value()) {
                if (isScheduledPost) {
                    $(this.saveButton).show();
                    $(this.saveAndContinueButton).show();
                } else {
                    $(this.scheduleButton).show();
                }
            } else {
                $(this.publishButton).show();
            }
        }
    });
});
