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
        global $DB;
        
        $records = $DB->get_records('block_formal_langs');
        
        $result = array();
        for($i = 0;$i < $records;$i++) {
            //TODO: Check, whether lang exists already and add version
            $result[$record->id] = $record->ui_name;
        }
        
        return array( 2 => 'Simple english' );
    }

    /**
     * Constructs and returns a language object for given languaged id
     *
     * @param langid id of the language
     * @return an intialised object of the child of the block_formal_langs_abstract_language class
     */
    public static function lang_object($langid) {
        global $DB;
        $record = $DB->get_record('block_formal_langs', $langid);
        $arrayrecord = (array)$record;
        if ($arrayrecord['name'] == null) {
            // TODO: Create user-defined language
            return null;
        } else {
           require_once($CFG->dirroot.'/blocks/formal_langs/language_' . $record->name . '.php');
           $langname = 'block_formal_langs_language' . $record->name;
           return new $langname();
        }
        //TODO: Replace it with actual code
        return null;
    }
    /**  Inserts or updates descriptions in a DB with values from processed string
      *  @param object block_formal_langs_processed_string object with stringid, table and descriptions filled
      */
    public static function update_descriptions($processedstring) {
        global $DB;
        $conditions = array(" tableid='{$processedstring->stringid}' ", "tablename = '{$processedstring->table}' ");
        $oldrecords = $DB->get_records_select('block_formal_langs_descrs', implode(' AND ', $conditions));
        $index = 0;
        foreach($processedstring->descriptions as $description) {
            $record = null;
            if ($oldrecords != null) {
                $record = array_shift($oldrecords);
            }
            $mustinsert  = ($oldrecords == null);
            if ($record == null) {
                $record = new stdClass();        
            }
            $record->tablename = $processedstring->table;
            $record->tableid = $processedstring->stringid;
            $record->number = $index;
            $record->description = $description;
            
            if ($mustinsert) {
                $DB->insert_record('block_formal_langs_descrs',$record);
            } else {
                $DB->update_record('block_formal_langs_descrs',$record);
            }
            
            $index = $index + 1;
        }
        
        //If some old descriptions left - delete it
        if ($oldrecords != null) {
            $oldrecordids = array();
            foreach($oldrecords as $oldrecord) {
                $oldrecordids[] = $oldrecord->id;    
            }
            $oldrecordin = implode(',',$oldrecordids);
            $DB->delete_records_select('block_formal_langs_descrs', " id IN ({$oldrecordin}) AND tablename = '{$processedstring->table}' ");
        }
    }
    /**  Returns a descriptions from a DB
      *  @param object block_formal_langs_processed_string  object with stringid and table filled
      */
    public static function get_descriptions($processedstring) {
        global $DB;
        $conditions = array(" tableid='{$processedstring->stringid}' ", "tablename = '{$processedstring->table}' ");
        $records = $DB->get_records_select('block_formal_langs_descrs', implode(' AND ', $conditions));
        foreach($records as $record) {
            $processedstring->descriptions[$record->number] = $record->description; 
        }
    }
    /** Removes a descriptions from a DB
      * @param object block_formal_langs_processed_string  object with stringid and table filled
      */
    public static function delete_descriptions($processedstring) {
        global $DB;
        $conditions = array(" tableid='{$processedstring->stringid}' ", "tablename = '{$processedstring->table}' ");
        return $DB->delete_records_select('block_formal_langs_descrs', implode(' AND ', $conditions));
    }
}
