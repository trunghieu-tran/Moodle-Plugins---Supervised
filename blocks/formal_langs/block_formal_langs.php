<?php
// This file is part of Formal Languages block - https://code.google.com/p/oasychev-moodle-plugins/
//
// Formal Languages block is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Formal Languages block is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Formal Languages block.  If not, see <http://www.gnu.org/licenses/>.

/**
 * A main class of block
 *
 * @package    formal_langs
 * @copyright  2012 Sychev Oleg
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot.'/blocks/formal_langs/language_base.php');
require_once($CFG->dirroot.'/blocks/moodleblock.class.php');
require_once($CFG->dirroot.'/lib/accesslib.php');

class block_formal_langs extends block_base {
    //TODO: Implement this
    public function init() {
        $this->title = get_string('pluginname', 'block_formal_langs');
    }

    function has_config() {
        return true;
    }

    /**
     * Returns an array of languages for given context
     *
     * @param int $contextid id of context, null means whole site
     * @return array where key is language id and value is user interface language name (received throught get_string)
     */
    public static function available_langs($contextid = null) {
        global $CFG;
        $languages = block_formal_langs::all_languages();
        // TODO - create a table with eye icons and set "visible" DB field for the language accordingly instead of using $CFG->xxx.
        $showedlanguages = $CFG->block_formal_langs_showablelangs;
        if (textlib::strlen($showedlanguages) != 0)
        {
            $availablelanguages = array();
            $showedlanguages = explode(',', $showedlanguages);
            foreach($showedlanguages as $langkey)
            {
                // Copy only visible langugages.
                $availablelanguages[$langkey] = $languages[$langkey];
            }
        } else {
            $availablelanguages = $languages;
        }
        return $availablelanguages;
    }

    /**
     * This function returns all languages.
     *
     * It is used in language configuration only and doesn't respect admin setting for available languages. 
     * For interaction with user please use function available_langs().
     * @return array where key is language id and value is user interface language name (received throught get_string)
     */
    public static function all_languages() {
        global $DB;

        //BUG: When installing moodle 2.5 settings of correctwriting will eventually call this function
        // before table created
        $dbman = $DB->get_manager();
        if ($dbman->table_exists('block_formal_langs') == false) {
            return array();
        }

        //Get all visible records
        $records = $DB->get_records('block_formal_langs', array('visible' => '1'));

        //Map, that checks amount of unique names in table. Populate it with values
        $counts = array();
        foreach($records as $record) {
            if ($record->name !== null) {//Predefined language, uiname is actually a language string, so replace it with actual name.
                $record->uiname = get_string('lang_' . $record->name , 'block_formal_langs');
            }
            if (array_key_exists($record->uiname, $counts)) {
                $counts[$record->uiname] = $counts[$record->uiname] + 1;
            } else {
                $counts[$record->uiname] = 1;
            }
        }
        //Populate result array
        $result = array();
        foreach($records as $record) {
            if ($counts[$record->uiname] > 1) {
                $result[$record->id] = $record->uiname . ' ' . $record->version;
            } else {
                $result[$record->id] = $record->uiname;
            }
        }

        return $result;
    }

    /**
     * Constructs and returns a language object for given languaged id
     *
     * @param int $langid id of the language
     * @return block_formal_langs_abstract_language an intialised object of the child of the block_formal_langs_abstract_language class
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

    /**
     * Finds or inserts language definition.
     * All fields must be set
     * @param array $language as tuple <ui_name, description, name, scanrules, parserules, version visible>.
     * @return int id of inserted language
     */
    public static function find_or_insert_language($language) {
        global $DB;
        // Seek for language and insert it if not found, handling some error stuff
        // Also cannot compare strings in some common case.
        $sql = 'SELECT id
                      FROM {block_formal_langs}
                     WHERE ';
        $filternames = array('name', 'version');
        $filtervalues = array($language['name'], $language['version']);
        if ($language['scanrules'] != null || $language['parserules'] != null) {
            $filternames[] = 'scanrules';
            $filternames[] = 'parserules';
            $filtervalues[]  = $language['scanrules'] ;
            $filtervalues[]  = $language['parserules'];
        }
        // Transform columns into sql comparisons
        $sqlfilternames = array();
        foreach($filternames as $name) {
            $sqlfilternames[] = $DB->sql_compare_text($name, 512) . ' = ' . $DB->sql_compare_text('?', 512);
        }
        // Build actual sql request
        $sql .= implode(' AND ', $sqlfilternames);
        $sql .= ';';

        $record = $DB->get_record_sql($sql, $filtervalues);
        if ($record == false) {
            $result = $DB->insert_record('block_formal_langs', $language);
        } else {
            $result = $record->id;
        }
        return $result;
    }


    /**
     * Synchronizes context informations with config
     */
    public static function sync_contexts_with_config($result = null) {
        global $CFG, $DB;
        // Sometimes this is called during install, so we need to check tables for
        // existence, otherwise it would inevitably fail with dml_exception
        $dbman = $DB->get_manager();
        $langsdoesnotexists = $dbman->table_exists('block_formal_langs') == false;
        $permsdoesnotexists = $dbman->table_exists('block_formal_langs_perms') == false;
        if ($langsdoesnotexists || $permsdoesnotexists)
            return;

        $systemcontextid = context_system::instance()->id;
        $showedlanguages = $CFG->block_formal_langs_showablelangs;
        if ($result !== null)
            $showedlanguages = $result;
        $showedarray = array();
        $showall = true;
        //  Fetch languages
        $languagerecords = $DB->get_records('block_formal_langs', array());
        //  Fetch global permissions
        if (textlib::strlen($showedlanguages) != 0)
        {
            $showedarray = explode(',', $showedlanguages);
            $showall = false;
        }
        // Fetch and build hash-table of permissions
        $globalpermrecords = $DB->get_records('block_formal_langs_perms', array('contextid' => $systemcontextid));
        $globalpermissions = array();
        foreach($globalpermrecords as $record) {
            $globalpermissions[$record->languageid] = $record;
        }

        foreach($languagerecords as $record) {
            // Compute visible flags
            if ($showall) {
                $visible = 1;
            } else {
                $visible = in_array($record->id, $showedarray);
                $visible = ($visible) ? 1 : 0;
            }
            // Select action, based on permissions
            $shouldinsert = false;
            $shouldupdate = false;
            $updateid = -1;
            if (is_array($globalpermissions)) {
                if (array_key_exists($record->id, $globalpermissions)) {
                    $permission =  $globalpermissions[$record->id];
                    $shouldupdate = $permission->visible != $visible;
                    $updateid =  $permission->id;
                } else {
                    $shouldinsert = true;
                }
            }  else {
                $shouldinsert = true;
            }
            $dataobject = new stdClass();
            $dataobject->languageid = $record->id;
            $dataobject->contextid = $systemcontextid;
            $dataobject->visible = $visible;
            if ($updateid > -1) {
                $dataobject->id = $updateid;
            }
            if ($shouldinsert) {
                $DB->insert_record('block_formal_langs_perms', $dataobject, false, true);
            }

            if ($shouldupdate) {
                $DB->update_record('block_formal_langs_perms', $dataobject, true);
            }

        }
    }
}
