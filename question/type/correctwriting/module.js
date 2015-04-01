/**
 * This module part applied to a form to work with descriptions.
 */

/**
 * Namespace
 */
M.question_type_correctwriting = { };


M.question_type_correctwriting.Form = function() {
    /**
     * List of descriptions to be hidden
     * @type {Array}
     */
    this.hidden_descriptions  = [];
    /**
     * Count of answers in form for input
     * @type {int}
     */
    this.answer_count = 0;
    /**
     * URL for working with lexer
     * @type {string}
     */
    this.lexerurl = 0;
    /**
     * Last time, when event of keyup is fired. Used to determine, whether to perform ajax invocation
     * @type {object}
     */
    this.lasttimefired = {};
    //noinspection JSUnusedGlobalSymbols
    /**
     * Forces descriptions to be hidden
     * @param {YUI} Y
     * @param {array} descriptions list of descriptions
     */
    this.hide_descriptions = function(Y, descriptions) {
        this.hidden_descriptions = descriptions;
        $(document).ready(function() {
            var i, id, el;
            var form = M.question_type_correctwriting.form;
            for( i = 0; i < form.hidden_descriptions.length; i++) {
                id = form.hidden_descriptions[i];
                el = $("#fitem_id_lexemedescriptions_" + id);
                el.css("display", "none");
                el.prev().css("display", "none");
            }
        });
    };
    //noinspection JSUnusedGlobalSymbols
    /**
     * Forces descriptions to be hidden
     * @param {YUI} Y
     * @param {int} i answer index, which description should be hidden
     */
    this.hide_description_field = function(Y, i) {
        var selector = "#fitem_id_lexemedescriptions_" + i;
        var element = $(selector);
        element.prev().css("display", "none");
        element.css("display", "none");
    };
    //noinspection JSUnusedGlobalSymbols
    /**
     * Forces inits text input on page
     * @param {YUI} Y
     * @param {int} answercount
     * @param {string} lexerurl
     */
    this.init_text_input = function(Y, answercount, lexerurl)  {
        this.answer_count = answercount;
        this.lexerurl = lexerurl;

        var event_handler = function() {
            var ctime = new Date().getTime();
            var matches = $(this).attr("name").match("(answer|fraction)\\[([0-9]+)\\]");
            var number = matches[2];
            var hintgradeborder = parseFloat($("input[name=hintgradeborder]").val());
            var text  = $("textarea[name='answer[" + number +"]']").val();
            var descriptions = $("#fitem_id_lexemedescriptions_" + number);
            var fraction = parseFloat($("select[name='fraction[" + number +"]']").val());
            var shouldrequestdescriptions = false;
            if (!isNaN(hintgradeborder)) {
                if (text.length == 0 || fraction < hintgradeborder) {
                    descriptions.css("display", "none");
                    descriptions.prev().css("display", "none");
                } else {
                    descriptions.css("display", "block");
                    descriptions.prev().css("display", "block");
                    shouldrequestdescriptions = true;
                }
            }
            if (ctime - M.question_type_correctwriting.form.lasttimefired[number] > 50)
            {
                M.question_type_correctwriting.form.lasttimefired[number] = ctime;
                if (shouldrequestdescriptions) {
                    M.question_type_correctwriting.form.run_request(text, number)
                }
            }
        };

        var hintgradeborderchanged = function() {
            var gradeborder = parseFloat($(this).val());
            var fraction, descriptions, text;
            if (!isNaN(gradeborder)) {
                for(var i = 0; i < M.question_type_correctwriting.form.answer_count; i++)
                {
                    text  = $("textarea[name=\'answer[" + i +"]\']").val();
                    fraction  = parseFloat($("select[name=\'fraction[" + i +"]\']").val());
                    descriptions = $("#fitem_id_lexemedescriptions_" + i);
                    if (text.length == 0 || fraction < gradeborder)  {
                        descriptions.css("display", "none");
                        descriptions.prev().css("display", "none");
                    } else {
                        descriptions.css("display", "block");
                        descriptions.prev().css("display", "block");
                        M.question_type_correctwriting.form.run_request(text, i);
                    }
                }
            }
        };
        var readyhandler =  function() {
            /** @var this M.question_type_correctwriting.Form  */
            for(var i = 0; i < this.answer_count; i++) {
                M.question_type_correctwriting.form.lasttimefired[i] = new Date().getTime();
                $("textarea[name=\'answer[" + i +"]\']").keyup(event_handler);
                $("select[name=\'fraction[" + i +"]\']").change(event_handler);
                $("input[name=hintgradeborder]").focusout(hintgradeborderchanged);
            }
        }.bind(this);
        $(document).ready(readyhandler);
    };
    /**
     * Runs a tokenization request for text
     * @param {string} text a text for request
     * @param {int} number a number of index of answer, which request is came from
     */
    this.run_request = function(text, number) {
        var labeltextarea = $("label[for=id_lexemedescriptions_" + number + "] textarea");
        var editabletextarea = $("#id_lexemedescriptions_" + number);
        var currentlanguage = $("#id_langid").val();
        var answerfield = $("textarea[name=\'answer[" + number+ "]\']");
        var mistakespanselector = "*[id=\'id_error_answer[" + number + "]\']";
        $.ajax({
            "url": this.lexerurl,
            "type": "POST",
            "data": {
                "scannedtext" : text,
                "lang" : currentlanguage
            },
            "dataType": "json",
            "success": function(data) {
                if (typeof(data) ==  "object" && data != null) {
                    if (!("tokens" in data)) {
                        //noinspection JSPotentiallyInvalidConstructorUsage
                        new M.core.ajaxException(data);
                        return;
                    }
                    var cols  = 0;
                    //noinspection JSUnresolvedVariable
                    for(var i = 0; i < data.tokens.length; i++) {
                        //noinspection JSUnresolvedVariable
                        cols = Math.max(cols, data.tokens[i].length);
                    }
                    // Reset mistakes array accordingly
                    //noinspection JSUnresolvedFunction
                    qf_errorHandler(answerfield[0], "");
                    if (data.errors.length != 0) {
                        // fake label for errors, we need to set text as html,
                        // but qf_errorHandler does not allow us to do so
                        // we doing it via jQuery. This is so going to be
                        // messed up on any kind of form update.
                        // But sadly, there is no other way...
                        //noinspection JSUnresolvedFunction
                        qf_errorHandler(answerfield[0], "fake label");
                        $(mistakespanselector).html(data.errors);
                    }
                    labeltextarea.removeAttr("style");
                    labeltextarea.css("display", "inline");
                    //noinspection JSUnresolvedVariable
                    labeltextarea.attr("rows", data.tokens.length);
                    labeltextarea.attr("cols", cols);
                    //noinspection JSUnresolvedVariable
                    labeltextarea.val(data.tokens.join("\n"));
                    //noinspection JSUnresolvedVariable
                    editabletextarea.attr("rows", data.tokens.length);
                }
            }
        });
    }
};

M.question_type_correctwriting.form = new  M.question_type_correctwriting.Form();



