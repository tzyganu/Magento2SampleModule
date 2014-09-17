/**
 * Sample_News extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category       Sample
 * @package        Sample_News
 * @copyright      Copyright (c) 2014
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/*jshint jquery:true browser:true*/
/*global Ajax:true alert:true*/
define([
    "jquery",
    "jquery/ui",
    "mage/backend/form",
    "prototype"
], function($){
    "use strict";

    $.widget("mage.sectionForm", $.mage.form, {
        options: {
            sectionIdSelector : 'input[name="section[id]"]',
            sectionPathSelector : 'input[name="section[path]"]'
        },

        /**
         * Form creation
         * @protected
         */
        _create: function() {
            this._super();
            $('body').on('sectionMove.tree', $.proxy(this.refreshPath, this));
        },

        /**
         * Sending ajax to server to refresh field 'section[path]'
         * @protected
         */
        refreshPath: function() {
            if (!this.element.find(this.options.sectionIdSelector).prop('value')) {
                return false;
            }
            // @TODO delete this prototype functional
            new Ajax.Request(
                this.options.refreshUrl,
                {
                    method:     'POST',
                    evalScripts: true,
                    onSuccess: this._refreshPathSuccess.bind(this)
                }
            );
        },

        /**
         * Refresh field 'section[path]' on ajax success
         * @param {Object} The XMLHttpRequest object returned by ajax
         * @protected
         */
        _refreshPathSuccess: function(transport) {
            if (transport.responseText.isJSON()) {
                var response = transport.responseText.evalJSON();
                if (response.error) {
                    alert(response.message);
                } else {
                    if (this.element.find(this.options.sectionIdSelector).prop('value') === response.id) {
                        this.element.find(this.options.sectionPathSelector)
                            .prop('value', response.path);
                    }
                }
            }
        }
    });

});