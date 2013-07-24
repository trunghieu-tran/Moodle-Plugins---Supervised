/**
 * Script for preg text+button widget
 *
 * @copyright  2012 Oleg Sychev, Volgograd State Technical University
 * @author Pahomov Dmitry, Terechov Grigory, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

M.poasquestion_text_and_button = (function() {

    var self = {

    /** @var input, from witch we read data */
    currentlinput : null,

    /** @var data, readed from input */
    data : null,

    /** @var reference to yui dialog object */
    dialog : null,

    /** @var reference to node with user html code of dialog */
    dialoghtmlnode : null,

    dialogtitle : null,

    /**
     * @var this function will be called only once, after dialog creation.
     * Reference to this object will be passed as the first parameter of the
     * function.
     * To set this property use method M.poasquestion_text_and_button.setup();
     */
    onfirstpresscallback : null,

    /** @var this function will be called after each click.
     * Reference to this object will be passed as the first parameter of the
     * function.
     * To set this property use method M.poasquestion_text_and_button.setup();
     */
    oneachpresscallback : null,

    /** @var data for module-extender */
    extendeddata : null,

    /** @var width of dialog */
    dialogwidth: 1000,

    /** Just creates object */
    init : function(Y, dialogwidth, dialogtitle) {
        self.dialogwidth = dialogwidth;
        self.dialogtitle = dialogtitle;
    },

    fix_textarea_rows : function(e) {
        var jqtarget = $(e.target);
        jqtarget.attr('rows',jqtarget.val().split('\n').length);
    },

    /**
     * Sets handler for button with id = button_id and input with id input_id
     * @param {Object} Y NOT USED! It need because moodle passes this object as first param anyway...
     * @param {string} button_id id of button for witch you want to set handler
     * @param {string} input_id id of input from witch you want to read data
     * @param {int} pagewidth width of modal window
     */
    set_handler : function (Y, button_id, input_id, pagewidth) {
        //this.Y = this.Y || Y;
        if(button_id==null || input_id==null) {
            return;
        }
        if(button_id.indexOf('#') != 0) {
            button_id = '#' + button_id;
        }
        if(input_id.indexOf('#') != 0) {
            input_id = '#' + input_id;
        }
        var testregexbtn = $(button_id);
        var testregexlineedit = $(input_id);
        var eventdata = {
            pagewidth: pagewidth,
            targetinput: testregexlineedit
        };
        $(testregexbtn).click(eventdata, self.btn_pressed);
        $(testregexlineedit).keyup(self.fix_textarea_rows).trigger('keyup');
    },

    /**
     * Handler of jquery event: pressing on the button
     * @param {targetinput} e.data.input from which data should be readen (should be passed as jquery event data)
     */
    btn_pressed : function(e) {

        e.preventDefault();
        var is_first_press = self.dialog === null;

        self.currentlinput = e.data.targetinput;// a reference to input from which we got a regex (this reference is passed as 'this' when we install this handler)
        self.data = self.currentlinput.val();
        if (is_first_press) {
            // if the 'Test regex' button is first pressed, we should generate a dialog window
            self.setup_dialog();
        }

        if(is_first_press && typeof(self.onfirstpresscallback) === "function") {
            self.onfirstpresscallback();
        }

        if(!is_first_press && typeof(self.oneachpresscallback) === "function") {
            self.oneachpresscallback();
        }
        self.dialog.dialog('open');
    },

    /**
     * Сreates new dialog object
     * @param {int} pagewidth width of dialog
     */
    setup_dialog : function(pagewidth) {
        self.dialog = $('<div id="preg_authoring_tools_dialog"><p>Loading...</p></div>');
        self.dialog.dialog({
            modal: true,
            width: self.dialogwidth,
            title: self.dialogtitle,
            buttons: [
                {text: "Save", click: self.close_and_set_new_data},
                {text: "Cancel", click: function() {$(this).dialog("close")}}
            ]
        });
    },

    /**
     * Sets up this module.
     * @param {Object} options Object that contains declaration of
     * onfirstpresscallback (function that calls at first dialog open)
     * and oneachpresscallback (function that calls at second and others dialog
     * open). Also may add extendeddata object to this module.
     * Example:
     *    var options = {
     *       onfirstpresscallback : function() {
     *           alert(1);
     *       },
     *
     *       oneachpresscallback : function() {
     *           alert(1);
     *       }
     *   };
     */
    setup : function (options) {
        self.onfirstpresscallback = options.onfirstpresscallback;
        self.oneachpresscallback = options.oneachpresscallback;
        self.extendeddata = options.extendeddata;
    },

    /**
     * Forces dialog close and sets data from _data param or this.data property
     * into  current input.
     * @param {String} _data data to set into current input
     */
    close_and_set_new_data : function(_data) {
        if (typeof(_data) === "string") {
            self.data = _data;
            self.currentlinput.val(_data);
        } else {
            self.currentlinput.val(self.data);
        }
		$('input[name=\'regextests[' + $(self.currentlinput).attr('id').split("id_answer_")[1] + ']\']').val($('#id_regex_match_text').val());
        self.dialog.dialog('close');
    }
};


return self;

})();