var NS8CSPLib = {

    showLoader: function () {
        require([
            'jquery'
        ], function ($, loader) {
            $('body').loader('show');
        });
    },

    hideLoader: function () {
        require([
            'jquery'
        ], function ($) {
            $('body').loader('hide');
        });
    },

    approveOrder: function (params, ajaxUrl, callback) {
        require([
            'jquery',
            'Magento_Ui/js/modal/prompt'
        ], function ($, prompt) {
            prompt({
                title: 'Approve order?',
                content: 'Note (optional):',
                actions: {
                    confirm: function (value) {

                        params.note = value;

                        $.ajax({
                            showLoader: true,
                            url: ajaxUrl,
                            data: params,
                            type: "POST",
                            dataType: 'json'
                        }).done(function (data) {

                            if (data.code != 200) {
                                NS8CSPLib.notice('Issue', data.message || 'Unable to approve order.');
                            } else {
                                if (callback) {
                                    callback(null, data);
                                }
                            }
                        }).fail(function (jqXHR, textStatus, errorThrown) {
                            NS8CSPLib.notice('Error', 'Unable to approve order. ' + textStatus);

                            if (callback) {
                                callback(new Error('textStatus'));
                            }
                        });
                    }
                }
            });
        });
    },

    holdOrder: function (params, ajaxUrl, callback) {
        require([
            'jquery',
            'Magento_Ui/js/modal/prompt'
        ], function ($, prompt) {

            prompt({
                title: 'Hold order?',
                content: 'Note (optional):',
                actions: {
                    confirm: function (value) {
                        params.note = value;
                        $.ajax({
                            showLoader: true,
                            url: ajaxUrl,
                            data: params,
                            type: "POST",
                            dataType: 'json'
                        }).done(function (data) {
                            if (data.code != 200) {
                                NS8CSPLib.notice('Issue', data.message || 'Unable to hold order.');
                            } else {
                                if (callback) {
                                    callback(null, data);
                                }
                            }
                        }).fail(function (jqXHR, textStatus, errorThrown) {
                            NS8CSPLib.notice('Error', 'Unable to hold order. ' + textStatus);

                            if (callback) {
                                callback(new Error('textStatus'));
                            }
                        });
                    }
                }
            });
        });
    },

    cancelOrder: function (params, ajaxUrl, callback) {

        require([
            'jquery',
            'Magento_Ui/js/modal/prompt'
        ], function ($, prompt) {
            prompt({
                title: 'Cancel order?',
                content: 'Note (optional):',
                actions: {
                    confirm: function (value) {
                        params.note = value;

                        $.ajax({
                            showLoader: true,
                            url: ajaxUrl,
                            data: params,
                            type: "POST",
                            dataType: 'json'
                        }).done(function (data) {

                            if (data.code != 200) {
                                NS8CSPLib.notice('Issue', data.message || 'Unable to cancel order.');
                            } else {
                                if (callback) {
                                    callback(null, data);
                                }
                            }
                        }).fail(function (jqXHR, textStatus, errorThrown) {
                            NS8CSPLib.notice('Error', 'Unable to cancel order. ' + textStatus);

                            if (callback) {
                                callback(new Error('textStatus'));
                            }
                        });
                    }
                }
            });
        });
    },

    validateOrder: function (params, ajaxUrl, callback) {
        require([
            'jquery',
            'Magento_Ui/js/modal/confirm'
        ], function ($, confirm) {
            confirm({
                title: 'Send validation request to this customer?',
                actions: {
                    confirm: function (value) {

                        $.ajax({
                            showLoader: true,
                            url: ajaxUrl,
                            data: params,
                            type: "POST",
                            dataType: 'json'
                        }).done(function (data) {

                            if (data.code != 200) {
                                NS8CSPLib.notice('Issue', data.message || 'Unable to validate order.');
                            } else {
                                if (callback) {
                                    callback(null, data);
                                }
                            }
                        }).fail(function (jqXHR, textStatus, errorThrown) {
                            NS8CSPLib.notice('Error', 'Unable to validate order. ' + textStatus);

                            if (callback) {
                                callback(new Error('textStatus'));
                            }
                        });
                    }
                }
            });
        });
    },

    notice: function (title, message) {
        require([
            'Magento_Ui/js/modal/alert'
        ], function (alert) {

            alert({
                title: title,
                content: message,
                actions: {
                    always: function () {

                    }
                }
            });
        });
    }
};
