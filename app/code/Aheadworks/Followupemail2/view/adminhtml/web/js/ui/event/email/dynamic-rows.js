/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

define([
    'jquery',
    'underscore',
    'mageUtils',
    'mage/translate',
    'Magento_Ui/js/dynamic-rows/dynamic-rows',
    'Magento_Ui/js/modal/confirm',
    'Magento_Ui/js/modal/alert',
    'Magento_Ui/js/lib/spinner',
], function ($, _, utils, $t, DynamicRows, confirm, alert, loader) {
    'use strict';

    return DynamicRows.extend({
        defaults: {
            manageFormName: '${ $.ns }.${ $.ns }',
            parentRowsName: '${ $.ns }.${ $.ns }.data.events',
            emailFormName: '${ $.ns }.${ $.ns }.email_edit_modal.aw_followupemail2_email_form',
            emailFormModalName: '${ $.ns }.${ $.ns }.email_edit_modal',
            deleteProperty: false,
            modules: {
                parentRows: '${ $.parentRowsName }',
                emailForm: '${ $.emailFormName }',
                emailFormModal: '${ $.emailFormModalName }'
            }
        },

        /**
         * Init header elements
         */
        initHeader: function () {
            var data;

            if (!this.labels().length) {
                _.each(this.childTemplate.children, function (cell) {
                    data = this.createHeaderTemplate(cell.config);

                    cell.config.labelVisible = false;
                    _.extend(data, {
                        label: cell.config.label,
                        name: cell.name,
                        additionalClasses: cell.config.columnsHeaderClasses
                    });

                    this.labels.push(data);
                }, this);
            }
        },

        /**
         * If has record data
         *
         * @returns {boolean}
         */
        hasData: function () {
            if (this.recordData().length > 0) {
                return true;
            }
            return false;
        },

        /**
         * Get readme text
         *
         * @returns {string}
         */
        getReadmeText: function () {
            var readmeText = $t('Click "{ButtonName}" to add new email.');
            var buttonName = '<strong>' + $t('Add Email') + '</strong>';
            readmeText = readmeText.replace('{ButtonName}', buttonName);

            return readmeText + '<br />' + this.parentRows().getUserGuideText();
        },

        /**
         * Get email name
         *
         * @param {number} emailId
         * @returns {string}
         */
        getEmailName: function (emailId) {
            var emailName = '';
            var emailFound = false;
            $.each(this.source.data.events, function (eventKey, event) {
                $.each(event.emails, function (emailKey, email) {
                    if (email.id == emailId) {
                        emailName = email.name;
                        emailFound = true;
                        return false;
                    }
                });
                if (emailFound) {
                    return false;
                }
            });
            return emailName;
        },

        /**
         * Check if a/b testing mode is enabled
         *
         * @param {number} emailId
         */
        isTestingEnabled: function (emailId) {
            var abTestingMode = false;
            var emailFound = false;
            $.each(this.source.data.events, function (eventKey, event) {
                $.each(event.emails, function (emailKey, email) {
                    if (email.id == emailId) {
                        abTestingMode = (email.ab_testing_mode == 1);
                        emailFound = true;
                        return false;
                    }
                });
                if (emailFound) {
                    return false;
                }
            });
            return abTestingMode;
        },

        /**
         * Open email edit form
         *
         * @param {number} eventId
         */
        editEmailForm: function (emailId) {
            this.emailForm().destroyInserted();
            this.emailFormModal().set('options.title', this.editEmailTitle);
            var data = {
                id: emailId,
            };
            this.emailForm().updateData(data);
            this.emailFormModal().openModal();
        },

        /**
         * Open duplicate email form
         *
         * @param {number} emailId
         * @returns {*}
         */
        duplicateEmailForm: function (emailId) {
            this.emailForm().destroyInserted();
            this.emailFormModal().set('options.title', this.newEmailTitle);
            var data = {
                id: emailId,
                duplicate: true
            };
            this.emailForm().updateData(data);
            this.emailFormModal().openModal();
        },

        /**
         * Disable email
         *
         * @param {number} emailId
         * @param {Array} params
         */
        changeStatusEmail: function (emailId, params) {
            if (params.href) {
                this.showLoader();
                makeAjaxRequest(
                    params.href,
                    {id:emailId},
                    this.onSuccessChangeStatus.bind(this),
                    this.onError.bind(this)
                );
            }
        },

        /**
         * Delete email
         *
         * @param {number} emailId
         * @param {Array} params
         */
        deleteEmail: function (emailId, params) {
            if (params.href) {
                this.showLoader();
                makeAjaxRequest(
                    params.href,
                    {id:emailId},
                    this.onSuccessDelete.bind(this, emailId),
                    this.onError.bind(this)
                );
            }
        },

        /**
         * Apply action
         *
         * @param {Object} action
         * @param {Object} index
         * @param {Object} recordId
         * @returns {*}
         */
        applyAction: function (action, recordId) {
            var emailId = this.getEmailId(recordId);
            var callback = this.getCallback(action, emailId);

            if (action.confirm) {
                this.confirm(action, callback)
            } else {
                callback()
            }
            return this;
        },

        /**
         * Get callback
         *
         * @param {Object} action
         * @param {number} emailId
         * @param {number} recordId
         * @returns {Function}
         */
        getCallback: function (action, emailId) {
            var callback = action.callback,
                params = [],
                self = this;

            if (utils.isObject(callback)) {
                if (callback.params) {
                    params = callback.params;
                }
                callback = callback.target;

                return function () {
                    self[callback](emailId, params);
                };
            }
            return function () {

            };
        },

        /**
         * Get email id by record id
         *
         * @param {number} recordId
         * @returns {number}
         */
        getEmailId: function (recordId) {
            var records = this.recordData();
            var emailId = 0;
            records.forEach(function (record) {
                if (record.record_id == recordId) {
                    emailId = record.id;
                }
            });
            return emailId;
        },

        /**
         * Display confirm message before applying callback
         *
         * @param {Object} action
         * @param {Function} callback
         */
        confirm: function (action, callback) {
            var confirmParams = action.confirm;
            confirm({
                title: confirmParams.title,
                content: confirmParams.message,
                actions: {
                    confirm: callback
                }
            });
        },

        /**
         * Show error popup
         *
         * @param {string} errorMessage
         */
        showError: function (errorMessage) {
            alert({
                content: $t(errorMessage),
            });
        },

        /**
         * Ajax request error handler
         *
         * @param errorMessage
         */
        onError: function (errorMessage) {
            this.showError(errorMessage);
            this.hideLoader();
            this.showSpinner(false);
        },

        /**
         * Success change status handler
         *
         * @param {Object} email
         */
        onSuccessChangeStatus: function (email) {
            var eventFound = false;
            var eventIndex = 0;
            this.source.data.events.forEach(function (event, index) {
                if (event.id == email.event_id) {
                    eventFound = true;
                    eventIndex = index;
                }
            });
            if (eventFound) {
                var records = this.recordData();
                var emailIndex = 0;
                var emailRecordId = 0;
                var emailFound = false;
                records.forEach(function (oldEmail, index) {
                    if (oldEmail.id == email.id) {
                        emailFound = true;
                        emailIndex = index;
                        emailRecordId = oldEmail.record_id
                    }
                });
                if (emailFound) {
                    email.record_id = emailRecordId;
                    this.source.data.events[eventIndex].emails[emailIndex] = email;
                    this.reload();
                }
            }
            this.hideLoader();
            this.showSpinner(false);
        },

        /**
         * Success delete handler
         *
         * @param {number} emailId
         * @param {number} eventsCount
         * @param {number} emailsCount
         * @param {Object} campaignStats
         */
        onSuccessDelete: function (emailId, eventsCount, emailsCount, campaignStats) {
            var eventIndex = 0;
            var emailFound = false;
            var emailIndex = 0;
            var emailRecordId = 0;
            this.source.data.events.forEach(function (event, evIndex) {
                event.emails.forEach(function (email, emIndex) {
                    if (email.id == emailId) {
                        emailFound = true;
                        emailIndex = emIndex;
                        eventIndex = evIndex;
                        emailRecordId = email.record_id;
                    }
                });
            });
            if (emailFound) {
                this.processingDeleteRecord(emailIndex, emailRecordId);
                if (eventsCount != null  && emailsCount != null) {
                    this.parentRows().updateCampaignShortStatisticsData(eventsCount, emailsCount, campaignStats);
                }
                this.reload();
            }
            this.hideLoader();
            this.showSpinner(false);
        },

        /**
         * Shows loader.
         */
        showLoader: function () {
            loader.get(this.manageFormName).show();
        },

        /**
         * Hides loader.
         */
        hideLoader: function () {
            loader.get(this.manageFormName).hide();
        },

        /**
         * Get actions
         * @param record
         * @returns {Array}
         */
        getActions: function (record) {
            var records = this.recordData();
            var recordId = record.recordId;
            var email = null;
            var emailFound = false;
            var actions = [];
            records.forEach(function (item) {
                if (item.record_id == recordId) {
                    email = item;
                    emailFound = true;
                }
            });

            if (emailFound) {
                this.actions.forEach(function (action) {
                    if (action.condition) {
                        if (action.condition.status == email.status) {
                            actions.push(action);
                        }
                    } else {
                        actions.push(action);
                    }
                });
            }

            return actions;
        },

        /**
         * Set classes
         *
         * @param {Object} data
         * @param {Object}|null record
         * @returns {Object} Classes
         */
        setClasses: function (data, record) {
            var classes = this._super(data);

            _.extend(classes, {
                'inactive': this.isInactive(),
                'email-disabled': this.isEmailDisabled(record)
            });

            return classes;
        },

        /**
         * Set classes for footer
         *
         * @returns {string}
         */
        setClassesForFooter: function () {
            var classes = '';
            if (this.isInactive()) {
                classes = 'inactive';
            } else {
                classes = '';
            }
            return classes;
        },

        /**
         * Is inactive
         *
         * @param record
         * @return {Boolean}
         */
        isInactive: function () {
            var records = this.recordData();
            var recordFound = false;
            $.each(records, function (key, value) {
                if (value.event_id) {
                    recordFound = value;
                    return false;
                }
            });
            if (recordFound) {
                var eventFound = false;
                $.each(this.source.data.events, function (key, value) {
                    if (recordFound.event_id == value.id) {
                        eventFound = value;
                        return false;
                    }
                });
                if (eventFound) {
                    return eventFound.status == 0;
                }
            };
            return false;
        },

        /**
         * Check if current email in the grid is inactive
         *
         * @param {Object} record
         * @returns {boolean}
         */
        isEmailDisabled: function (record) {
            var flag = false;
            if (record) {
                var recordData = this.getCurrentRecordData(record.recordId);
                if (recordData) {
                    if (recordData.is_email_disabled) {
                        flag =  true;
                    }
                }
            }

            return flag;
        },

        /**
         * Get current record data
         *
         * @param {int} recordId
         * @returns {Object}|false
         */
        getCurrentRecordData: function (recordId) {
            var records = this.recordData();
            var recordData = false;
            if (records) {
                $.each(records, function (key, value) {
                    if (recordId == value.record_id) {
                        recordData = value;
                    }
                });
            }
            return recordData;
        },

        /**
         * Reset event statistics
         */
        resetStatistics: function () {
            var parentRecordId = this.dataScope.split('.').pop(),
                event = this.source.data.events[parentRecordId];

            confirm({
                title: $.mage.__('Are you sure you want to reset the event statistics?'),
                content: $.mage.__('All the event emails statistics will be set to 0. This action cannot be reversed.'),
                actions: {
                    confirm: function() {
                        this.showLoader();
                        $.ajax({
                            url: this.reset_statistics_url,
                            type: "POST",
                            dataType: 'json',
                            data: {id:event.id},
                            success: function(response) {
                                if (response.ajaxExpired) {
                                    window.location.href = response.ajaxRedirect;
                                }

                                if (!response.error) {
                                    window.location.href = response.redirect_url;
                                    return true;
                                }
                                this.hideLoader();
                                self.onError(response.message);
                                return false;
                            }
                        });
                    }.bind(this)
                },
                buttons: [{
                    text: $.mage.__('Cancel'),
                    class: 'action-secondary action-dismiss',
                    click: function (event) {
                        this.closeModal(event);
                    }
                }, {
                    text: $.mage.__('Reset Statistics'),
                    class: 'action-primary action-accept',
                    click: function (event) {
                        this.closeModal(event, true);
                    }
                }]
            });
        },

        /**
         *Get totals value
         *
         * @param {string} index
         * @returns {string}
         */
        getTotals: function (index) {
            var parentRecordId = this.dataScope.split('.').pop(),
                event = this.source.data.events[parentRecordId];

            return event.totals[index];
        },

        /**
         * Format percent
         *
         * @param value
         * @returns {string}
         */
        formatPercent: function (value) {
            return String(Number(value * 1).toFixed(2)) + '%'
        }
    })

    /**
     * Make ajax request
     *
     * @param {String} url
     * @param {Array} params
     * @param {Function} successCallback
     * @param {Function} errorCallback
     */
    function makeAjaxRequest (url, params, successCallback, errorCallback) {
        params = utils.serialize(params);
        params['form_key'] = window.FORM_KEY;
        $.ajax({
            url: url,
            data: params,
            dataType: 'json',

            /**
             * Success callback.
             * @param {Object} response
             * @returns {Boolean}
             */
            success: function (response) {
                if (response.ajaxExpired) {
                    window.location.href = response.ajaxRedirect;
                }

                if (!response.error) {
                    var eventsCount = null;
                    var emailsCount = null;
                    var campaignStats = null;
                    if ("events_count" in response && "emails_count" in response) {
                        eventsCount = response.events_count;
                        emailsCount = response.emails_count;
                    }
                    if ("campaign_stats" in response) {
                        campaignStats = response.campaign_stats;
                    }

                    if (!response.email) {
                        successCallback(eventsCount, emailsCount, campaignStats);
                    } else {
                        successCallback(response.email, eventsCount, emailsCount, campaignStats);
                    }
                    return true;
                }
                errorCallback(response.message);
                return false;
            },
        });
    }
});
