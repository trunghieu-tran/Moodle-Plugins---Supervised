/**
 * This module part applied to a Formal Languages Block
 */

/**
 * Namespace
 */
M.block_formal_langs = { };

/**
 * An ajax page, which will receive all requests
 * @type {string}
 */
M.block_formal_langs.ajax_page = "";
/**
 * A context, where block was created
 * @type {number}
 */
M.block_formal_langs.context = 0;
/**
 * A path to icon with closed eye
 * @type {string}
 */
M.block_formal_langs.hide_icon = "";
/**
 * A path to icon with open eye
 * @type {string}
 */
M.block_formal_langs.show_icon = "";
/**
 * Whether block is displayed in a system context
 * i.e. this is global settings for a site
 * @type {boolean}
 */
M.block_formal_langs.is_global = false;
/**
 * A string for affected courses
 * @type {string}
 */
M.block_formal_langs.affected_courses_label = "";
/**
 * Initializes block
 * @param {YUI} Y yui class
 * @param {string} ajax_page a page for ajax requests
 * @param {number} context context id
 * @param {string} hide_icon an icon for hidden languages
 * @param {string} show_icon an icon for visible languages
 * @param {boolean} is_global whether block is shown in global context
 * @param {string} affected_courses_label a label for affected courses
 */
M.block_formal_langs.init = function(Y, ajax_page, context, hide_icon, show_icon, is_global, affected_courses_label) {
    M.block_formal_langs.ajax_page = ajax_page;
    M.block_formal_langs.context = context;
    M.block_formal_langs.hide_icon = hide_icon;
    M.block_formal_langs.show_icon = show_icon;
    M.block_formal_langs.is_global = is_global;
    M.block_formal_langs.affected_courses_label = affected_courses_label;

    $("document").ready(function() {
        $("a.deletelanguage").click(function() {
            var id = $(this).attr("data-id");
            $("span[data-id=" + id + "]").parent().parent().remove();
            //noinspection JSUnusedLocalSymbols
            $.ajax({
                "url": M.block_formal_langs.ajax_page,
                "type" : "GET",
                "data": {
                    "action": "removeformallanguage",
                    "languageid" : id,
                    "context" : M.block_formal_langs.context
                },
                "dataType": "text",
                "success": function() {
                },
                "error": function(xhr) {
                    //noinspection JSPotentiallyInvalidConstructorUsage
                    var a = new M.core.ajaxException({
                        "error": "Cannot remove formal language",
                        "center": true,
                        "closeButton": true,
                        "draggable": true,
                        "reproductionlink": M.block_formal_langs.ajax_page
                    });
                    a.show();
                }
            });
        });
        $("a.changevisibility").click(function() {
            var id = $(this).attr("data-id");
            var visible = $(this).attr("data-visible");
            var src = M.block_formal_langs.hide_icon;
            if (visible == 1)
            {
                visible = 0;
                $("span[data-id=" + id + "]").addClass("dimmed_text");
            } else {
                visible = 1;
                $("span[data-id=" + id + "]").removeClass("dimmed_text");
                src = M.block_formal_langs.show_icon;
            }
            $(this).find("img").attr("src", src);
            $(this).attr("data-visible", visible);
            var updateinheritance = function (text) {
                $(this).parent().parent().parent().find(".inherited-hint").html(text);
            };
            var updateinheritancethis = updateinheritance.bind(this);
            //noinspection JSUnusedLocalSymbols
            $.ajax({
                "url": M.block_formal_langs.ajax_page,
                "type" : "GET",
                "data": {
                    "action": "flanguagevisibility",
                    "languageid" : id,
                    "visible" : visible,
                    "context" : M.block_formal_langs.context,
                    "global"  : M.block_formal_langs.is_global
                },
                "dataType": "json",
                "success": function(data) {
                    var label = M.block_formal_langs.affected_courses_label + "<br />";
                    if (M.block_formal_langs.is_global) {
                        label =  label + data.map(function(o) {
                            //noinspection JSUnresolvedVariable
                            return o.shortname
                        }).join("<br />");
                        $(".global-affected-courses").html(label);
                    } else {
                        updateinheritancethis(data);
                    }
                },
                "error": function(xhr) {
                    //noinspection JSPotentiallyInvalidConstructorUsage
                    var a = new M.core.ajaxException({
                        "error": "Cannot change visibility",
                        "center": true,
                        "closeButton": true,
                        "draggable": true,
                        "reproductionlink":  M.block_formal_langs.ajax_page
                    });
                    a.show();
                }
            });
        });
    });
};