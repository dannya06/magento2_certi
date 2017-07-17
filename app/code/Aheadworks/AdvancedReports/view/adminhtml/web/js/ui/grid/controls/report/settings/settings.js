/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

define([
    'underscore',
    'mageUtils',
    'uiCollection'
], function (_, utils, Collection) {
    'use strict';

    return Collection.extend({
        defaults: {
            applied: {},
            settings: {},
            statefull: {
                applied: true
            },
            listens: {
                applied: 'initSettings',
                elems: 'initAppliedByDefault'
            },
            exports: {
                applied: '${ $.provider }:params.report_settings'
            }
        },

        /**
         * Sets settings data to the applied state
         *
         * @returns {Settings} Chainable
         */
        apply: function () {
            this.validateForm();

            if (!this.get('params.invalid')) {
                this.set('applied', utils.copy(this.prepareSettingsData()));
            }

            return this;
        },

        /**
         * Initialize settings variable to the applied state
         *
         * @returns {Settings} Chainable
         */
        initSettings: function () {
            if (this.applied == undefined) {
                this.applied = {};
            }
            this.set('settings', utils.copy(this.applied));

            this.elems().forEach(function (elem, index) {
                if (this.applied[elem.index] == undefined && elem.service && elem.service.template) {
                    elem.restoreToDefault();
                    elem.isUseDefault(true);
                }
                if (this.applied[elem.index] != undefined && elem.service && elem.service.template) {
                    elem.isUseDefault(false);
                }
            }, this);
            return this;
        },

        /**
         * Called when another element was added to filters collection
         *
         * @returns {Settings} Chainable
         */
        initElement: function (elem) {
            this._super();

            elem.on('isUseDefault', function(state) {
                if (state) {
                    this.restoreToDefault();
                }
            }.bind(elem));

            if (this.applied != undefined && this.applied[elem.index] != undefined && elem.service && elem.service.template) {
                elem.isUseDefault(false);
            }

            return this;
        },

        /**
         * Initialize applied variable default values (from settings)
         *
         * @returns {Settings} Chainable
         */
        initAppliedByDefault: function () {
            if (!_.keys(this.applied).length && this.initChildCount == this.elems().length) {
                this.set('applied', utils.copy(this.prepareSettingsData()));
            }

            return this;
        },

        /**
         * Validates each element and returns true, if all elements are valid
         */
        validateForm: function () {
            this.set('params.invalid', false);
            this.trigger('data.validate');
        },

        /**
         * Prepare data
         *
         * @param {Array} data
         */
        prepareSettingsData: function() {
            var data = {};

            this.elems().forEach(function (elem, index) {
                if (elem.service && elem.service.template && elem.isUseDefault()) {
                } else {
                    data[elem.index] = elem.value();
                }
            });

            return data;
        }
    });
});
