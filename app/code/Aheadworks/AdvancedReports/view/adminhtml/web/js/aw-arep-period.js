/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

/**
 * Initialization widget to daterange
 *
 * @method _create()
 * @method _setCustomRange()
 * @method _applyPeriod()
 * @method _cancelPeriod()
 * @method _dateToString(d)
 * @method _switchDateRange()
 * @method _override(object, methodName, callback)
 * @method _after(extraBehavior)
 */
define([
    'jquery',
    'mage/translate',
    'timeframe'
], function($, $t) {
    "use strict";

    $.widget('mage.awArepPeriod', {
        options: {
            earliest: '',
            latest: '',
            weekOffset: 0,
            ranges: '',
            compareAvailable: true,
            compareEnabled: false,
            compareTypeDefault: '',
            periodDateFromSelector: '#awarep-period_date_from',
            periodDateToSelector: '#awarep-period_date_to',
            customDateRangeSelector: '#awarep-custom_date_range',
            compareCheckboxSelector: '#awarep-compare_checkbox',
            compareTypeSelector: '#awarep-compare_dropdown_date_range',
            comparePeriodFromSelector: '#awarep-compare_date_from',
            comparePeriodToSelector: '#awarep-compare_date_to',
            compareCustomRangeSelector: '#awarep-compare_custom_date_range',
            applyPeriodButtonSelector: '#awarep-apply-period',
            cancelPeriodButtonSelector: '#awarep-cancel-period',
            calendarsHeaderSelector: '#awarep-calendars_header',
            calendarsHeaderCompareSelector: '#awarep-calendars_header_compare',
            calendarsContainerSelector: '#awarep-calendars-container'
        },

        /**
         * Initialize widget
         * @private
         */
        _create: function () {
            var self = this;

            this.datePicker = new Timeframe('awarep-calendars', {
                startField: 'awarep-period_date_from',
                endField: 'awarep-period_date_to',
                compareStartField: 'awarep-compare_date_from',
                compareEndField: 'awarep-compare_date_to',
                resetButton: 'reset',
                header: 'awarep-calendars_header',
                form: 'awarep-calendars-container',
                earliest: this.options.earliest,
                latest: this.options.latest,
                weekOffset: this.options.weekOffset,
                calendarSelectionCallback: this.calendarRangeSelect.bind(this)
            });
            this.datePicker.parseField('start', true);
            this.datePicker.parseField('end', true);
            this.datePicker.parseField('compareStart', true);
            this.datePicker.parseField('compareEnd', true);
            if (!this.options.compareEnabled) {
                this.datePicker.clearCompareRange();
            }
            this.datePicker.selectstart = true;
            this.datePicker.populate().refreshRange();

            this.dontTouchCustom = false;

            this._override(this.datePicker, 'handleDateClick', this._after(function(element, couldClear) {
                $(self.options.periodDateFromSelector).trigger('input');
            }));

            $(this.options.periodDateFromSelector).on('input', this._setCustomRange.bind(this));
            $(this.options.periodDateToSelector).on('input', this._setCustomRange.bind(this));
            $(this.options.customDateRangeSelector).on('change', this._switchDateRange.bind(this));
            $(this.options.compareCheckboxSelector).on('click', this._compareCheckboxClick.bind(this));
            $(this.options.compareTypeSelector).on('change', this._switchCompareDateRange.bind(this));
            $(this.options.comparePeriodFromSelector).on('click input', this._selectCustomCompareType.bind(this));
            $(this.options.comparePeriodToSelector).on('click input', this._selectCustomCompareType.bind(this));
            $(this.options.applyPeriodButtonSelector).on('click', this._applyPeriod.bind(this));
            $(this.options.cancelPeriodButtonSelector).on('click', this._cancelPeriod.bind(this));

            this._switchCompare();
            this._showCompareHeader();
        },

        /**
         * Set custom range
         * @private
         */
        _setCustomRange: function() {
            $(this.options.customDateRangeSelector).val('custom');
        },

        /**
         * Aplly period click
         * @private
         */
        _applyPeriod:  function() {
            var type = $(this.options.customDateRangeSelector).val(),
                dateFrom = new Date($(this.options.periodDateFromSelector).val()),
                dateTo = new Date($(this.options.periodDateToSelector).val()),
                compareType = $(this.options.compareTypeSelector).val(),
                compareFrom = new Date($(this.options.comparePeriodFromSelector).val()),
                compareTo = new Date($(this.options.comparePeriodToSelector).val()),
                params = document.location.search.replace('?', '').toQueryParams();

            Object.extend(params, {
                period_type: type,
                period_from: this._dateToString(dateFrom),
                period_to: this._dateToString(dateTo),
            });

            if (this.options.compareAvailable && this.options.compareEnabled) {
                Object.extend(params, {
                    compare_type: compareType,
                    compare_from: this._dateToString(compareFrom),
                    compare_to: this._dateToString(compareTo)
                });
            } else {
                delete params.compare_type;
                delete params.compare_from;
                delete params.compare_to;
            }

            document.location.search = '?' + $.param(params);
        },

        /**
         * Cancel button click
         * @private
         */
        _cancelPeriod:  function() {
            $(this.options.calendarsHeaderSelector).removeClass('opened');
            $(this.options.calendarsContainerSelector).removeClass('is_displayed');
            this._showCompareHeader;
        },

        /**
         * Convert date to string
         * @param d
         * @returns {string}
         * @private
         */
        _dateToString: function(d) {
            return d.getFullYear().toString()
                + '-' + ('0' + (d.getMonth() + 1)).slice(-2)
                + '-' + ('0' + d.getDate()).slice(-2);
        },

        /**
         * Switch date range
         * @private
         */
        _switchDateRange: function() {
            var dateFrom = $(this.options.periodDateFromSelector),
                dateTo = $(this.options.periodDateToSelector),
                periodType = $(this.options.customDateRangeSelector).val();

            if (!Object.isUndefined(this.options.ranges[periodType])) {
                dateFrom.val(this.options.ranges[periodType].from);
                dateTo.val(this.options.ranges[periodType].to);
            }
            this.datePicker.range.set('start', new Date.parseToObject(dateFrom.val()));
            this.datePicker.range.set('end', new Date.parseToObject(dateTo.val()));
            this.datePicker.parseField('start', false);
            this.datePicker.parseField('end', false);
            $(this.options.calendarsHeaderSelector).html(dateFrom.val() + ' - ' + dateTo.val());
            this._switchCompareDateRange();
        },

        /**
         * Compare checkbox click handler
         * @private
         */
        _compareCheckboxClick: function() {
            if (this.options.compareAvailable) {
                this.options.compareEnabled = !this.options.compareEnabled;
                if (!this.options.compareEnabled) {
                    this._setDefaultCompareType();
                    this.datePicker.clearCompareRange();
                } else {
                    this._switchCompareDateRange();
                }
                this._switchCompare();
                this.datePicker.refreshRange();
            }
        },

        /**
         * Switch compare range
         * @private
         */
        _switchCompare: function() {
            if (this.options.compareAvailable) {
                $(this.options.compareCheckboxSelector).attr("disabled", false);
                if (this.options.compareEnabled) {
                    $(this.options.compareCheckboxSelector).prop('checked', true);
                    $(this.options.compareTypeSelector).attr("disabled", false);
                    $(this.options.compareCustomRangeSelector).show();
                } else {
                    $(this.options.compareCheckboxSelector).prop("checked", false);
                    $(this.options.compareTypeSelector).attr("disabled", true);
                    $(this.options.compareCustomRangeSelector).hide();
                    $(this.options.calendarsHeaderCompareSelector).html('');
                }
            }
        },

        /**
         * Switch compare date range
         * @private
         */
        _switchCompareDateRange: function() {
            if (!this.options.compareAvailable || !this.options.compareEnabled) {
                return;
            }
            var comparePeriodType = $(this.options.compareTypeSelector).val();

            var dateFrom = $(this.options.periodDateFromSelector),
                dateTo = $(this.options.periodDateToSelector);

            var dateFromDate =  new Date.parseToObject(dateFrom.val()),
                dateToDate = new Date.parseToObject(dateTo.val());

            switch (comparePeriodType) {
                case 'custom':
                    if (!this.dontTouchCustom) {
                        $(this.options.comparePeriodFromSelector).addClass('selected');
                        this.datePicker.selectCompare = true;
                        this._enableComparisonPeriodFields();
                    }
                    this.dontTouchCustom = false;
                    break;
                case 'previous_period':
                    var delta = (dateToDate.getTime() - dateFromDate.getTime()) + 86400000;
                    dateFromDate.setTime(dateFromDate.getTime() - delta);
                    dateToDate.setTime(dateToDate.getTime() - delta);
                    this._setCompareDates(dateFromDate, dateToDate);
                    break;
                case 'previous_year':
                    dateFromDate.setFullYear(dateFromDate.getFullYear() - 1);
                    dateToDate.setFullYear(dateToDate.getFullYear() - 1);
                    this._setCompareDates(dateFromDate, dateToDate);
                    break;
            }
        },

        /**
         * Show compare data in period header
         * @private
         */
        _showCompareHeader: function() {
            if (this.options.compareAvailable && this.options.compareEnabled) {
                var compareFrom = $(this.options.comparePeriodFromSelector),
                    compareTo = $(this.options.comparePeriodToSelector);

                $(this.options.calendarsHeaderCompareSelector).html($t('Compare to') + ': '
                    + compareFrom.val() + ' - ' + compareTo.val());
            }
        },

        /**
         * Remove compare data from period header
         * @private
         */
        _hideCompareHeader: function() {
            $(this.options.calendarsHeaderCompareSelector).html();
        },

        /**
         * Set default compare type
         * @private
         */
        _setDefaultCompareType: function() {
            $(this.options.compareTypeSelector).val(this.options.compareTypeDefault);
        },

        /**
         * "Disable" comparison period inputs
         * @private
         */
        _disableComparisonPeriodFields: function() {
            $(this.options.compareCustomRangeSelector).addClass('grayed');
            $(this.options.comparePeriodFromSelector).removeClass('selected');
            $(this.options.comparePeriodToSelector).removeClass('selected');
        },

        /**
         * "Enable" comparison period inputs
         * @private
         */
        _enableComparisonPeriodFields: function() {
            $(this.options.compareCustomRangeSelector).removeClass('grayed');
        },

        /**
         * Select custom compare type
         * @param event
         * @private
         */
        _selectCustomCompareType: function(event) {
            $(this.options.compareTypeSelector).val('custom');
            this._removeAllPeriodFieldsSelection();

            $(event.currentTarget).addClass('selected');
            this.datePicker.selectCompare = true;
            if (event.currentTarget.name = 'compare_from') {
                this.datePicker.selectstart = true;
                this.datePicker.parseField('compareStart', true);
            } else {
                this.datePicker.selectstart = false;
                this.datePicker.parseField('compareEnd', true);
            }
            this._enableComparisonPeriodFields();
        },

        /**
         * Check and fix specified date
         *
         * @param {Date} date
         * @returns {Date}
         * @private
         */
        _getValidDate: function(date) {
            var earliestDate = new Date.parseToObject(this.options.earliest);
            var latestDate = new Date.parseToObject(this.options.latest);
            var validDate = new Date();

            validDate.setTime(date.getTime());

            if (date.getTime() < earliestDate.getTime()) {
                validDate.setTime(earliestDate.getTime());

            }
            if (date.getTime() > latestDate.getTime()) {
                validDate.setTime(latestDate.getTime());
            }
            return validDate;
        },

        /**
         * Set compare dates in the calendar
         *
         * @param {Date} from
         * @param {Date} to
         * @retunrs {Void}
         * @private
         */
        _setCompareDates: function(from, to) {
            var compareFrom = $(this.options.comparePeriodFromSelector),
                compareTo = $(this.options.comparePeriodToSelector);

            var customCompare = false;
            var validDateFrom = this._getValidDate(from);
            if (validDateFrom.getTime() != from.getTime()) {
                customCompare = true;
            }
            var validDateTo = this._getValidDate(to);
            if (validDateTo.getTime() != to.getTime()) {
                customCompare = true;
            }

            compareFrom.val(validDateFrom.strftime(this.datePicker.format));
            compareTo.val(validDateTo.strftime(this.datePicker.format));

            this.datePicker.range.set('compareStart', validDateFrom);
            this.datePicker.range.set('compareEnd', validDateTo);
            this.datePicker.parseField('compareStart', false);
            this.datePicker.parseField('compareEnd', false);

            if (customCompare) {
                $(this.options.compareTypeSelector).val('custom');
                this._enableComparisonPeriodFields();
            } else {
                this._disableComparisonPeriodFields();
            }
        },

        /**
         * Remove selection of all period fields
         * @private
         */
        _removeAllPeriodFieldsSelection: function() {
            $(this.options.periodDateFromSelector).removeClass('selected');
            $(this.options.periodDateToSelector).removeClass('selected');
            $(this.options.comparePeriodFromSelector).removeClass('selected');
            $(this.options.comparePeriodToSelector).removeClass('selected');
        },

        /**
         * Callback for calendar range select
         */
        calendarRangeSelect: function() {
            this.dontTouchCustom = true;
            this._switchCompareDateRange();
        },

        /**
         * For override original method
         * @param object
         * @param methodName
         * @param callback
         * @private
         */
        _override: function(object, methodName, callback) {
            object[methodName] = callback(object[methodName])
        },

        /**
         * Call custom method after original
         * @param extraBehavior
         * @returns {Function}
         * @private
         */
        _after: function(extraBehavior) {
            return function(original) {
                return function() {
                    var returnValue = original.apply(this, arguments)
                    extraBehavior.apply(this, arguments)
                    return returnValue
                }
            }
        }
    });

    return $.mage.awArepPeriod;
});
