<?php
/**
 * A main class of block
 *
 * @package    formal_langs
 * @copyright  2012 Sychev Oleg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot.'/blocks/formal_langs/language_base.php');
require_once($CFG->dirroot.'/blocks/formal_langs/simple_english_language.php');
require_once($CFG->dirroot.'/blocks/moodleblock.class.php');

class block_formal_langs extends block_base {
    //TODO: Implement this
    public function init() {
        $this->title = get_string('pluginname', 'block_formal_langs');
    }

    /**
     * Returns an array of languages for given context
     *
     * @param contextid id of context, null means whole site
     * @return array where key is language id and value is user interface language name (received throught get_string)
     */
    public static function available_langs($contextid = null) {
        //TODO: Replace it with actual code
        return array( 2 => 'Simple english' );
    }

    /**
     * Constructs and returns a language object for given languaged id
     *
     * @param langid id of the language
     * @return an intialised object of the child of the block_formal_langs_abstract_language class
     */
    public static function lang_object($langid) {
        //TODO: Replace it with actual code
        return new block_formal_langs_simple_english_language();
    }
    
    /**
     * Insert new record to a description table
     * @param string $sourcetable source table field
     * @param object $dataobject  object to be inserted
     */
    public static function insert_description($sourcetable, $dataobject) {
        global $DB;
        $dataobject->tablename = $sourcetable; 
        return $DB->insert_record('block_formal_langs_descrs',$dataobject);
    }
    
    /**
     * Update record in a description table
     * @param string $sourcetable source table field
     * @param object $dataobject object to be inserted
     */
    public static function update_description($sourcetable, $dataobject) {
        global $DB;
        $dataobject->tablename = $sourcetable; 
        return $DB->update_record('block_formal_langs_descrs',$dataobject);
    }
    
    /**
     * Deletes records, using particular WHERE clause 
     * @param string $sourcetable source table field
     * @param $where  WHERE clause
     */
    public static function delete_descriptions_select($sourcetable,  $where) {
        global $DB;
        $DB->delete_records_select('block_formal_langs_descrs', $where . " AND tablename = '$sourcetable' ");
    }
    /**
     * Returns a descriptions from table, using particular WHERE clause 
     * @param string $sourcetable source table field
     * @param $where  WHERE clause
     */
    public static function get_descriptions_select($sourcetable, $where) {
        global $DB;
        return $DB->get_records_select('block_formal_langs_descrs', $where . " AND tablename = '$sourcetable'");
    }
}
