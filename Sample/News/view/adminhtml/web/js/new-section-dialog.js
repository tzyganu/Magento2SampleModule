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
/*jshint browser:true jquery:true*/
/*global FORM_KEY*/
define([
    "jquery",
    "jquery/ui",
    "jquery/template",
    "mage/translate",
    "mage/backend/tree-suggest",
    "mage/backend/validation"
], function($){
    'use strict';
    var clearParentSection = function () {
        $('#new_section_parent').find('option').each(function(){
            $('#new_section_parent-suggest').treeSuggest('removeOption', null, this);
        });
    };

    $.widget('samlpe_news.newSectionDialog', {
        _create: function () {
            var widget = this;
            $('#new_section_parent').before($('<input>', {
                id: 'new_section_parent-suggest',
                placeholder: $.mage.__('start typing to search section')
            }));

            $('#new_section_parent-suggest').treeSuggest(this.options.suggestOptions)
                .on('suggestbeforeselect', function (event) {
                    clearParentSection();
                    $(event.target).treeSuggest('close');
                    $('#new_section_name').focus();
                });

            $.validator.addMethod('validate-parent-section', function() {
                return $('#new_section_parent').val() || $('#new_section_parent-suggest').val() === '';
            }, $.mage.__('Choose existing category.'));
            var newSectionForm = $('#new_section_form');
            newSectionForm.mage('validation', {
                errorPlacement: function (error, element) {
                    error.insertAfter(element.is('#new_section_parent') ?
                        $('#new_section_parent-suggest').closest('.mage-suggest') :
                        element);
                }
            }).on('highlight.validate', function (e) {
                    var options = $(this).validation('option');
                    if ($(e.target).is('#new_section_parent')) {
                        options.highlight($('#new_section_parent-suggest').get(0),
                            options.errorClass, options.validClass || '');
                    }
                });

            this.element.dialog({
                title: $.mage.__('Create Section'),
                autoOpen: false,
                minWidth: 560,
                dialogClass: 'mage-new-section-dialog form-inline',
                modal: true,
                multiselect: true,
                resizable: false,
                open: function() {
                    // fix for suggest field - overlapping dialog z-index
                    $('#new_section_parent-suggest').css('z-index', $.ui.dialog.maxZ + 1);
                    var enteredName = $('#section_ids-suggest').val();
                    $('#new_section_name').val(enteredName);
                    if (enteredName === '') {
                        $('#new_section_name').focus();
                    }
                    $('#new_section_messages').html('');
                },
                close: function() {
                    $('#new_section_name, #new_section_parent-suggest').val('');
                    var validationOptions = newSectionForm.validation('option');
                    validationOptions.unhighlight($('#new_section_parent-suggest').get(0),
                        validationOptions.errorClass, validationOptions.validClass || '');
                    newSectionForm.validation('clearError');
                    $('#section_ids-suggest').focus();
                },
                buttons: [{
                    text: $.mage.__('Create Section'),
                    'class': 'action-create primary',
                    'data-action': 'save',
                    click: function(event) {
                        if (!newSectionForm.valid()) {
                            return;
                        }

                        var thisButton = $(event.target).closest('[data-action=save]');
                        thisButton.prop('disabled', true);
                        $.ajax({
                            type: 'POST',
                            url: widget.options.saveSectionUrl,
                            data: {
                                section: {
                                    name: $('#new_section_name').val(),
                                    status: 1
                                    //TODO: add default values here
                                },
                                parent: $('#new_section_parent').val(),
                                //use_config: ['available_sort_by', 'default_sort_by'],
                                form_key: FORM_KEY,
                                return_session_messages_only: 1
                            },
                            dataType: 'json',
                            context: $('body')
                        })
                            .success(
                            function (data) {
                                if (!data.error) {
                                    $('#section_ids-suggest').trigger('selectItem', {
                                        id: data.section.entity_id,
                                        label: data.section.name
                                    });
                                    $('#new_section_name, #new_section_parent-suggest').val('');
                                    $('#section_ids-suggest').val('');
                                    clearParentSection();
                                    widget.element.dialog('close');
                                } else {
                                    $('#new_section_messages').html(data.messages);
                                }
                            }
                        )
                            .complete(
                            function () {
                                thisButton.prop('disabled', false);
                            }
                        );
                    }
                }]
            });
        }
    });
});