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
define([
    "jquery",
    "prototype"
], function(jQuery){

var sectionSubmit = function (url, useAjax) {
    var activeTab = $('active_tab_id');
    if (activeTab) {
        if (activeTab.tabsJsObject && activeTab.tabsJsObject.tabs('activeAnchor')) {
            activeTab.value = activeTab.tabsJsObject.tabs('activeAnchor').prop('id');
        }
    }

    var params = {};
    var fields = $('section_edit_form').getElementsBySelector('input', 'select');
    for(var i=0;i<fields.length;i++){
        if (!fields[i].name) {
            continue;
        }
        params[fields[i].name] = fields[i].getValue();
    }

    // Get info about what we're submitting - to properly update tree nodes
    var sectionId = params['section[id]'] ? params['section[id]'] : 0;
    var isCreating = sectionId == 0; // Separate variable is needed because '0' in javascript converts to TRUE
    var path = params['section[path]'].split('/');
    var parentId = path.pop();
    if (parentId == sectionId) { // Maybe path includes section id itself
        parentId = path.pop();
    }

    // Make operations with section tree
    if (isCreating) {
        /* Some specific tasks for creating section */
        if (!tree.currentNodeId) {
            // First submit of form - select some node to be current
            tree.currentNodeId = parentId;
        }
        tree.addNodeTo = parentId;
    } else {
        /* Some specific tasks for editing section */
        // Maybe change section enabled/disabled style
        if (tree) {
            var currentNode = tree.getNodeById(sectionId);

            if (currentNode) {
                if (parseInt(params['section[status]'])) {
                    var oldClass = 'no-active-category';
                    var newClass = 'active-category';
                } else {
                    var oldClass = 'active-category';
                    var newClass = 'no-active-category';
                }

                Element.removeClassName(currentNode.ui.wrap.firstChild, oldClass);
                Element.addClassName(currentNode.ui.wrap.firstChild, newClass);
            }
        }
    }

    // Submit form
    jQuery('#section_edit_form').trigger('submit');
};

window.sectionSubmit = sectionSubmit;

});