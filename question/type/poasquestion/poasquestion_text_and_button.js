/**
 * Script for preg text+button widget
 *
 * @copyright &copy; 2012  Terechov Grigory, Pahomov Dmitry
 * @author Terechov Grigory, Pahomov Dmitry, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

// requaries: 'node', 'panel', 'node-load', 'get', "io-xdr", "substitute", "json-parse"

M.poasquestion_text_and_button = (function(){

    var self = {

    /** @var intupt, from witch we read data */
    currentlinput : null,

    /** @var data, readed from input */
    data : null,

    /** @var reference to yui dialog object */
    dialog : null,

    /** @var reference to node with user html code of dialog */
    dialoghtmlnode : null,

    /** @var YUI object with requaried extentions (sets in js_init_call) */
    Y : null,

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

    /** Just creates object */
    init : function(Y) {
        this.Y = this.Y || Y;
    },

    /**
     * Sets handler for button with id = button_id and input with id input_id
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
        var testregexbtn = this.Y.one(button_id);
        var testregexlineedit = this.Y.one(input_id);
        testregexbtn.on("click", this.btn_pressed, this, pagewidth, testregexlineedit);
    },

    /**
     * Handler of pressing on the button
     * @param {int} pagewidth width of modal window
     * @param {targetinput} input from which data should be readen
     */
    btn_pressed : function(e, pagewidth, targetinput) {

        e.preventDefault();
        pagewidth = pagewidth || 1000;// width of dialog
        var is_first_press = this.dialog === null;

        this.currentlinput = targetinput;// a reference to input from which we got a regex (this reference is passed as 'this' when we install this handler)
        this.data = this.currentlinput.get('value');
        if (is_first_press) {
            // if the 'Test regex' button is first pressed, we should generate a dialog window
            this.setup_dialog(pagewidth);
        }

        if(is_first_press && typeof(this.onfirstpresscallback) === "function") {
            this.onfirstpresscallback();
        }

        if(!is_first_press && typeof(this.oneachpresscallback) === "function") {
            this.oneachpresscallback();
        }
        this.dialog.show();
    },

    /**
     * Сreates new dialog object
     * @param {int} pagewidth width of dialog
     */
    setup_dialog : function(pagewidth) {
        this.dialog = new this.Y.Panel({
            contentBox: Y.Node.create('<div id="dialog" />'),
            bodyContent: '<div class="message icon-warn">Loading...</div>',
            width: pagewidth,
            zIndex: 120,
            centered: true,
            modal: true, // modal behavior
            render: '.example',
            visible: true, // make visible explicitly with .show()
            buttons: {
                 footer: [
                        {
                            name: 'cancel',
                            label: 'Cancel',
                            action: function(e) {
                                e.preventDefault();
                                this.hide();
                                this.callback = false;
                            }
                        },

                        {
                            name: 'proceed',
                            label: 'OK',
                            action: function(e) {
                                e.preventDefault();
                                self.close_and_set_new_data();
                            }
                        }
                    ]
            }
        });
        this.dialoghtmlnode = this.Y.one('#dialog .message');
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
        this.onfirstpresscallback = options.onfirstpresscallback;
        this.oneachpresscallback = options.oneachpresscallback;
        this.extendeddata = options.extendeddata;
    },

    /**
     * Forces dialog close and sets data from _data param or this.data property
     * into  current input.
     * @param {String} _data data to set into current input
     */
    close_and_set_new_data : function(_data) {
        if (typeof(_data) === "string") {
            this.data = _data;
            this.currentlinput.set('value',_data);
        } else {
            this.currentlinput.set('value',this.data);
        }
        this.dialog.hide();
        this.dialog.callback = false;
    }
};


return self;

})();