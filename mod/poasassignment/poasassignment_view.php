<?php
/**
 * Class, that renders data. Singletone. 
 */
class poasassignment_view {
     protected static $view;

     private function __construct() {

     }
     /**
      * Method is used instead of constructor. If poasassignment_view
      * object exists, returns it, otherwise creates object and returns it.
      * @return object poasassignment_view
      */
     static function &get_instance() {
         if (self::$view==null) {
             self::$view = new self();
         }
         return self::$view;
     }

/**
     * Prepare flexible table of task owners for using. Contains
     * 4 default fields:
     * - full user name
     * - usergroups
     * - attempt status
     * - gradestatus
     * More columns and headers can be added by parameters $extracolumns and $extraheaders.
     * 
     * @access public
     * @param array $extracolumns additional columns
     * @param array $extraheaders additional headers
     * @return object flexible_table
     */
    public function prepare_flexible_table_owners(array $extracolumns = array(), array $extraheaders = array()) {
        global $PAGE, $OUTPUT, $CFG;
        require_once($CFG->libdir . '/tablelib.php');
        $table = new flexible_table('mod-poasassignment-task-owners');
        $table->baseurl = $PAGE->url;
        $columns = array(
                'fullname',
                'usergroups',
                'attemptstatus',
                'gradestatus');
        $columns = array_merge($columns, $extracolumns);
        $headers = array(
                get_string('fullname', 'poasassignment'),
                get_string('usergroups', 'poasassignment'),
                get_string('attemptstatus', 'poasassignment'),
                get_string('gradestatus', 'poasassignment')
        );
        $headers = array_merge($headers, $extraheaders);
        $table->define_columns($columns);
        $table->define_headers($headers);
        $table->collapsible(false);
        $table->initialbars(false);
        $table->set_attribute('class', 'poasassignment-table task-owners');

        $table->setup();
    
        return $table;
    }
    
}