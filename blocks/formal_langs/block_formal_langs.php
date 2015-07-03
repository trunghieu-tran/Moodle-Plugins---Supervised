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


class block_formal_langs extends block_list {
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
     * @param context $context context, whose languages will be extracted, null means whole site
     * @return array where key is language id and value is user interface language name (received throught get_string)
     */
    public static function available_langs($context = null) {
        if ($context == null) {
            $currentcontexts = array( context_system::instance()->id );
        } else {
            $currentcontexts = $context->get_parent_context_ids(false);
            $currentcontexts[] = $context->id;
        }

        $table = block_formal_langs::build_visibility_for_all_languages($currentcontexts);


        $languages = block_formal_langs::all_languages();
        foreach($table as $record) {
            if ($record->visible == 0 && array_key_exists($record->id, $languages)) {
                unset($languages[$record->id]);
            }
        }

        return $languages;
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
     * Finds or insers language definition.
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
     * Finds or inserts language definition.
     * All fields must be set
     * @param array $language as tuple <ui_name, description, name, scanrules, parserules, version visible>.
     * @return int id of inserted language
     */
    public static function find_or_insert_language($language) {
        global $DB, $CFG;
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
            $setting = block_formal_langs::get_visible_language_setting();
            $showedlanguages = $CFG->block_formal_langs_showablelangs;
            $showedarray = explode(',', $showedlanguages);
            $showedarray[] = $result;
            $realshowedarray = array();
            foreach($showedarray as $id) {
                $realshowedarray[$id] = 1;
            }
            $setting->write_setting($realshowedarray);
            block_formal_langs::sync_contexts_with_config();
        } else {
            $result = $record->id;
        }
        return $result;
    }

    /**
     * Gets showable language setting
     * @return block_formal_langs_admin_setting_visible_languages
     */
    public static function get_visible_language_setting() {
        $cfgname = 'block_formal_langs_showablelangs';
        $label =  get_string('visiblelangslabel', 'block_formal_langs');
        $description = get_string('visiblelangsdescription', 'block_formal_langs');
        $default = array('1' => '1');
        $setting  = new block_formal_langs_admin_setting_visible_languages($cfgname, $label, $description, $default, null);
        $setting->load_choices();
        return $setting;
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
        if (core_text::strlen($showedlanguages) != 0)
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

    /**
     * Builds visibility table for array of contexts
     * @param  array $contexttree of int list of parent contexts
     * @return array of stdClass <int id, string name, string ui_name, string version, bool visible>
     */
    public static function build_visibility_for_all_languages($contexttree) {
        global $DB;
        $languages = $DB->get_records('block_formal_langs', array('visible' => '1'));
        if (count($contexttree) == 0) {
            $contexttree = array( context_system::instance()->id );
        }
        // Fetch associated context data.
        list($insql, $params) = $DB->get_in_or_equal($contexttree);
        $sql = 'SELECT *
                FROM {block_formal_langs_perms}
                WHERE contextid ' . $insql;
        $contextspermissions = $DB->get_records_sql($sql, $params);
        $sql = 'SELECT id, depth
                FROM {context}
                WHERE id ' . $insql;
        $contexts = $DB->get_records_sql($sql, $params);
        $globalcontextid = context_system::instance()->id;
        foreach($languages as $language) {
            $language->contextid = $globalcontextid;
            // Fetch permissions for language.
            $permissionsforlanguage = array();
            foreach($contextspermissions as $permission) {
                if ($permission->languageid == $language->id) {
                    $permissionsforlanguage[$permission->contextid] = $permission->visible;
                }
            }
            // Compute permission for context with max depth
            $maxdepth = -1;
            $maxdepthid = -1;
            if (count($permissionsforlanguage)) {
                foreach($permissionsforlanguage as $contextid => $contextvalue) {
                    if (array_key_exists($contextid, $contexts)) {
                        $depth = $contexts[$contextid]->depth;
                        if ($depth > $maxdepth) {
                            $language->contextid = $contextid;
                            $maxdepth = $depth;
                            $maxdepthid = $contextid;
                        }
                    }
                }
            }
            if ($maxdepthid > 0) {
                $language->visible = $permissionsforlanguage[$maxdepthid];
            }
        }
        return $languages;
    }

    /**
     * Updates language visibility for language list.
     * @param int $languageid id of language
     * @param int $visibility visibility of data
     * @param int $contextid id of context for items
     * @return string either course - new setting for this language  for context will be taken from course
     *                    or site   - new setting for this language wll be taken  from site
     */
    public static function update_language_visibility($languageid, $visibility, $contextid) {
        global $DB;
        $contexttree = context::instance_by_id($contextid)->get_parent_context_ids(true);
        list($insql, $params) = $DB->get_in_or_equal($contexttree);
        array_unshift($params, $languageid);
        $sql = 'SELECT perms.*, ctx.depth
                FROM {block_formal_langs_perms} perms,
                {context} ctx WHERE perms.languageid = ? AND perms.contextid '
               . $insql
               . ' AND  ctx.id = perms.contextid ORDER BY depth DESC LIMIT 2';
        $contextpermissions = $DB->get_records_sql($sql, $params);
        $visibility = ($visibility > 0) ? 1 : 0;
        $existentobject = new stdClass();
        $existentobject->languageid = $languageid;
        $existentobject->contextid = $contextid;
        $existentobject->visible = $visibility;
        $result = 'course';
        if (count($contextpermissions) == 0) {
            $DB->insert_record('block_formal_langs_perms', $existentobject);
        } else {
            $currentpermission =  array_shift($contextpermissions);
            /* If settings exists for current context,
               we could either update or delete setting.
               Otherwise we could either insert new setting or do nothing.
             */
            if ($currentpermission->contextid == $contextid) {
                $parentvisibilitymatches = false;
                if (count($contextpermissions) > 0) {
                    $parentpermission = array_shift($contextpermissions);
                    $parentvisibilitymatches = $parentpermission->visible == $visibility;
                }
                // If current permission matches visibility - we won't change anything.
                // Otherwise, we could delete data.
                if ($parentvisibilitymatches  && $currentpermission->visible != $visibility) {
                    $DB->delete_records('block_formal_langs_perms', array('id' => $currentpermission->id));
                    $result = 'site';
                } else {
                    if ($currentpermission->visible != $visibility) {
                        $existentobject->id = $currentpermission->id;
                        $DB->update_record('block_formal_langs_perms', $existentobject);
                    }
                }
            } else {
                // First permission will be parent.
                $parentpermission = $currentpermission;
                if ($parentpermission->visible != $visibility) {
                    $DB->insert_record('block_formal_langs_perms', $existentobject);
                }
            }
        }
        return $result;
    }


    public function get_content() {
        global $_REQUEST, $PAGE, $CFG, $OUTPUT, $DB, $USER;

        $PAGE->requires->jquery();

        $this->content         = new stdClass;
        $this->content->items  = array();
        $this->content->icons  = array();



        $context  = $this->page->context;

        $caneditall = has_capability('block/formal_langs:editalllanguages', $context);
        $caneditown = has_capability('block/formal_langs:editownlanguages', $context);
        $contexts = $context->get_parent_context_ids(true);
        array_unshift($contexts, $context->id);


        // If cannot view language list don't do anything
        if (!has_capability('block/formal_langs:viewlanguagelist', $context)) {
            return null;
        }

        $action  = optional_param('action', '', PARAM_RAW);
        if ($action == 'removeformallanguage') {
            $langid = required_param('languageid', PARAM_INT);
            $DB->delete_records('block_formal_langs', array('id' => $langid));
            $DB->delete_records('block_formal_langs_perms', array('languageid' => $langid));
        }
        if ($action == 'flanguagevisibility') {
            $langid  = required_param('languageid', PARAM_INT);
            $visible = required_param('visible', PARAM_INT);
            block_formal_langs::update_language_visibility($langid, $visible, $context->id);
        }

        $permissions = block_formal_langs::build_visibility_for_all_languages($contexts);

        if ($this->page->user_is_editing() && has_capability('block/formal_langs:addlanguage', $context)) {
            $link = $CFG->wwwroot . '/blocks/formal_langs/edit.php?new=1&context=' . $context->id;
            $icon =  html_writer::empty_tag('img', array('src' => $OUTPUT->pix_url('t/add')));
            $icona = html_writer::tag('a', $icon, array('href' => $link, 'title' => get_string('addnewlanguage', 'block_formal_langs')));
            $this->content->icons[] = $icona;
            $this->content->items[] =  html_writer::tag('a', get_string('addnewlanguage', 'block_formal_langs'), array('href' => $link));;
        }
        $isglobal = $context->id == context_system::instance()->id;
        foreach($permissions as $permission) {
            if ($permission->visible) {
                $visible = 0;
                $icon = 'i/hide';
            } else {
                $visible = 1;
                $icon = 'i/show';
            }

            // $link  = $this->page->url->out(false, array('languageid' => $permission->id, 'action' => 'flanguagevisibility', 'visible' => $visible));
            $link = 'javascript: void(0)';
            $viconattr =  array('src' => $OUTPUT->pix_url($icon), 'width' => '16', 'height' => '16');
            $viconhref =  array(
                'href' => $link,
                'class' => 'padright changevisibility',
                'title' => get_string('changevisibility', 'block_formal_langs'),
                'data-id' => $permission->id,
                'data-visible' => $permission->visible
            );
            if (has_capability('block/formal_langs:changelanguagevisibility', $context)) {
                $visibleicon = html_writer::tag('a', html_writer::empty_tag('img', $viconattr), $viconhref);
            } else {
                $visibleicon = '';
            }

            $editlinks = '';
            $caneditlang = $this->page->user_is_editing() && core_text::strlen($permission->scanrules) != 0;
            $caneditlang =  $caneditlang && ($caneditall || ($caneditown && $permission->author == $USER->id));
            if ($caneditlang) {
                $editlink = $CFG->wwwroot . '/blocks/formal_langs/edit.php?id=' . $permission->id . '&context=' . $context->id;
                $editiconattr =  array('src' => $OUTPUT->pix_url('t/edit'));
                $editiconhref =  array('href' => $editlink, 'class' => 'padright', 'title' => get_string('editlanguage', 'block_formal_langs', $permission->uiname));
                $editlinks .= html_writer::tag('a', html_writer::empty_tag('img', $editiconattr), $editiconhref);

                // $link  = $this->page->url->out(false, array('languageid' => $permission->id, 'action' => 'removeformallanguage'));
                $link = 'javascript: void(0)';
                $viconattr =  array('src' => $OUTPUT->pix_url('t/delete'));
                $viconhref =  array(
                    'href' => $link,
                    'class' => 'padright deletelanguage',
                    'title' => get_string('deletelanguage', 'block_formal_langs', $permission->uiname),
                    'data-id' => $permission->id
                );
                $editlinks .= html_writer::tag('a', html_writer::empty_tag('img', $viconattr), $viconhref);
            }
            $class = '';
            if (!($permission->visible)) {
                $class = 'dimmed_text';
            }
            $text =  html_writer::tag('span',  $visibleicon . $editlinks, array('class' => $class, 'data-id' => $permission->id));
            $this->content->icons[] = $text;
            $text = $permission->uiname . ' (' . $permission->version . ')';
            // Add inheritance hint
            if ($isglobal == false) {
                if ($permission->contextid != $context->id) {
                    $inheritedtext = get_string('inherited_site', 'block_formal_langs');
                } else {
                    $inheritedtext = get_string('inherited_course', 'block_formal_langs');
                }
                $inheritedtext = '(' . $inheritedtext . ')';
                $text =  html_writer::tag('span', $inheritedtext, array('class' => 'inherited-hint')) . ' ' . $text;
            }
            $text =  html_writer::tag('span',  $text, array('class' => $class, 'data-id' => $permission->id));
            $this->content->items[]  = $text;
        }

        return null;
    }

    public function formatted_contents($output) {
        global $OUTPUT;
        $result = parent::formatted_contents($output);
        // Somehow CSS was not included, so it was included here manually
        $this->page->requires->css('/blocks/formal_langs/styles.css');

        // Include related JS
        $context  = $this->page->context;
        $isglobal = $context->id == context_system::instance()->id;
        $icon = new pix_icon('t/show', get_string('hide'));
        $hidesrc = $OUTPUT->pix_url($icon->pix, $icon->component)->out(true);
        $icon = new pix_icon('t/hide', get_string('hide'));
        $showsrc = $OUTPUT->pix_url($icon->pix, $icon->component)->out(true);
        $ajaxhandlerurl = new moodle_url('/blocks/formal_langs/ajaxhandler.php');
        $localpage = $ajaxhandlerurl->out();
        $params = array(
            $localpage,
            $context->id,
            $hidesrc,
            $showsrc,
            $isglobal,
            get_string('affectedcourses', 'block_formal_langs')
        );
        $jsmodule = array(
            'name' => 'block_formal_langs',
            'fullpath' => '/blocks/formal_langs/module.js'
        );
        $this->page->requires->js_init_call('M.block_formal_langs.init', $params, null, $jsmodule);

        // Add a div, if block is shown in global context
        $globalaffectdiv = '';
        if ($isglobal) {
            $globalaffectdiv = html_writer::tag('div', '', array('class' => 'global-affected-courses'));
        }
        return $result . $globalaffectdiv;
    }

    public function applicable_formats() {
        return array(
            'course-view' => true
        );
    }
}
