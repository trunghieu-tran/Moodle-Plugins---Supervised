<?php
// This file is part of Correct Writing question type - https://code.google.com/p/oasychev-moodle-plugins/
//
// Correct Writing question type is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Correct Writing is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Serve question type files
 *
 * @package    qtype_correctwriting
 * @copyright  2013 Oleg Sychev, Volgograd State Technical University
 * @author     Oleg Sychev <oasychev@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Checks file access for Correct Writing questions.
 */
function qtype_correctwriting_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    global $DB, $CFG;
    require_once($CFG->libdir . '/questionlib.php');
    question_pluginfile($course, $context, 'qtype_correctwriting', $filearea, $args, $forcedownload, $options);
}

/**
 * An interface for id-preserving serialization
 */
interface qtype_correctwriting_can_preserving_serialize {
    /**
     * A callback which is called when we need to store preserved data for serializing
     * @param int $key        key for saving
     * @param stdClass $value value to save
     * @param stdClass $storage  temporary storage, where can be stored some important between loops data
     *                           One key is required - usedids, which can be used to handling some unused
     * @param array    $oldvalues  Old values of serialized data
     */
    public function save_stored_data($key, $value, &$storage, $oldvalues);

    /**
     * A callback, which will be called to handle unused values
     * @param array $ids array of id data
     * @param stdClass $storage storage data
     */
    public function handle_unused_records($ids, &$storage);
}

/**
 * An interface, which preserve id, when storing old data
 */
class qtype_correctwriting_preserving_serializator {
    /**
     * @var array $oldvalues of int - old ids of values
     */
    protected $oldvalues;
    /**
     * @var array $newvalues of int new ids of values
     */
    protected $newvalues;
    /**
     * @var qtype_correctwriting_can_preserving_serialize serializable object
     */
    protected $serializable;
    /**
     * @var $stdClass $storage data
     */
    protected $storage;
    /**
     * Constructs new preserving serializator
     * @param array $oldvalues array of ids or records. From records an id fields should be traken
     * @param array $newvalues  of stdClass new values of array
     * @param qtype_correctwriting_can_preserving_serialize $s serializable object
     * @param stdClass|null $storage Storage - temporary object, which can be filled when something
     */
    public function __construct($oldvalues, $newvalues, $s, $storage = null) {
        $oldvalueskeys = array_keys($oldvalues);
        $this->oldvalues = $oldvalues;
        if (count($oldvalueskeys)) {
            if (is_object($oldvalues[$oldvalueskeys[0]])) {
                $this->oldvalues = array();
                foreach ($oldvalues as $key => $object) {
                    $this->oldvalues[$key] = $object->id;
                }
            }
        }
        $this->newvalues = $newvalues;
        $this->serializable = $s;
        if ($storage != null) {
            $this->storage = $storage;
        } else {
            $this->storage = new stdClass();
        }
        $this->storage->usedids = array();
    }

    /**
     * Saves all associated data
     */
    public function save() {
        // Serializable can optionally implement starting and finishing working with data
        // So we handle that.
        if (method_exists($this->serializable, 'start_preserving_save')) {
            $this->serializable->start_preserving_save($this->oldvalues, $this->newvalues, $this->storage);
        }
        if (count($this->newvalues)) {
            foreach ($this->newvalues as $key => $value) {
                $this->serializable->save_stored_data($key, $value, $this->storage, $this->oldvalues);
            }
            if (count($this->oldvalues)) {
                $oldanswerunused = $this->oldvalues;
                if (count($this->storage->usedids)) {
                    $oldanswerunused = array_diff($this->oldvalues, $this->storage->usedids);
                }
                if (count($oldanswerunused)) {
                    $this->serializable->handle_unused_records($oldanswerunused, $storage);
                }
            }
        }
        if (method_exists($this->serializable, 'finish_preserving_save')) {
            $this->serializable->finish_preserving_save($this->oldvalues, $this->newvalues, $this->storage);
        }
    }
}