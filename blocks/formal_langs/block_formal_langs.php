<?php
/**
 * A main class of block
 *
 * @package    formal_langs
 * @copyright  2012 Sychev Oleg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot.'/blocks/formal_langs/language_base.php');
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
        
        //Get all visible records
        $records = $DB->get_records('block_formal_langs', array('visible' => '1'));
        
        //Map, that checks amount of unique names in table. Populate it with values
        $counts = array();
        foreach($records as $record) {
            if ($record->name !== null) {//Predefined language, ui_name is actually a language string, so replace it with actual name.
                $record->ui_name = get_string('lang_' . $record->name , 'block_formal_langs');
            }
            if (array_key_exists($record->ui_name, $counts)) {
                $counts[$record->ui_name] = $counts[$record->ui_name] + 1;
            } else {
                $counts[$record->ui_name] = 1;
            }
        }
        //Populate result array
        $result = array();
        foreach($records as $record) {
            if ($counts[$record->ui_name] > 1) {
                $result[$record->id] = $record->ui_name . ' ' . $record->version;
            } else {
                $result[$record->id] = $record->ui_name;
            }
        }
        
        return $result;
    }

    /**
     * Constructs and returns a language object for given languaged id
     *
     * @param langid id of the language
     * @return an intialised object of the child of the block_formal_langs_abstract_language class
     */
    public static function lang_object($langid) {
        global $DB, $CFG;
        $record = $DB->get_record('block_formal_langs', array('id' => $langid));
        $result = null;
        $arrayrecord = (array)$record;
        if ($arrayrecord['name'] == null) {
            $result = new block_formal_langs_userdefined_language($langid, $record->version, $record);
        } else {
            require_once($CFG->dirroot.'/blocks/formal_langs/language_' . $record->name . '.php');
            $langname = 'block_formal_langs_language_' . $record->name;
            $result = new $langname($langid, $record);
        }
        return $result;
    }
}
