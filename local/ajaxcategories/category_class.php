<?php
// This file is part of ajaxcategories plugin - https://code.google.com/p/oasychev-moodle-plugins/
//
// Ajaxcategories plugin is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

defined('MOODLE_INTERNAL') || die();

// Number of categories to display on page.

require_once($CFG->libdir . '/listlib.php');
require_once($CFG->dirroot . '/question/category_form.php');
require_once($CFG->dirroot . '/question/move_form.php');
require_once($CFG->dirroot . '/question/category_class.php');
/**
 * Class representing a list of ajax question categories
 *
 */
class ajax_question_category_list extends moodle_list {
    public $table = "question_categories";
    public $listitemclassname = 'ajax_question_category_list_item';
    /**
     * @var reference to list displayed below this one.
     */
    public $nextlist = null;
    /**
     * @var reference to list displayed above this one.
     */
    public $lastlist = null;
    /**
     * @var context og this list.
     */
    public $context = null;
    public $sortby = 'parent, sortorder, name';

    public function __construct($type='ul', $attributes='', $editable = false, $pageurl=null, $page = 0,
                                $pageparamname = 'page', $itemsperpage = 20, $context = null) {
        parent::__construct('ul', $attributes, $editable, $pageurl, $page, 'cpage', $itemsperpage);
        $this->context = $context;
    }

    public function get_records() {
        $this->records = get_categories_for_contexts($this->context->id, $this->sortby);
    }

    /**
     * Replace category item in choosen place.
     *
     * @var $movingid - id of question_category_item replacing category.
     * @var $environment - array with keys: 'before' - before item id
     *                                      'after' - after item id
     *                                      'level' - 'normal' or 'inner'
     *                                      'dest' - destination list
     */
    public function change_category_list($movingid, $environment) {
        global $DB;
        // Get item by id.
        $item = $this->find_item($movingid);
        // Change context.
        if ($environment['dest']->context->id != $this->context->id) {
            // Get all children items of moving item.
            $children = $item->get_all_children();
            $children[] = $movingid;
            // Change context for all replacing categories.
            foreach ($children as $child) {
                $oldcat = $DB->get_record('question_categories', array('id' => $child), '*', MUST_EXIST);
                $oldcat->contextid = $environment['dest']->context->id;
                $DB->update_record('question_categories', $oldcat);
            }
        }

        // Define the place of replacing.
        if ($environment['after'] != -1) {
            // Replacing at the same level after some item.
            $afteritem = $environment['dest']->find_item($environment['after']);
            if ($environment['level'] != "inner") {
                // At the same level.
                if (isset($afteritem->parentlist->parentitem)) {
                    $newparent = $afteritem->parentlist->parentitem->id;
                } else {
                    $newparent = 0;
                }

                $DB->set_field($environment['dest']->table, "parent", $newparent, array("id" => $item->id));
                $newpeers = $environment['dest']->get_items_peers($afteritem->id);
                // Place of item after which should be added moving category.
                $oldkey = array_search($afteritem->id, $newpeers);
                // Place of moving category.
                $key = array_search($item->id, $newpeers);
                // If moving category was at the same list reoder categories in list.
                if ($key) {
                    // Replace moving category up.
                    if ($oldkey < $key) {
                        $neworder = array_merge(array_slice($newpeers, 0, $oldkey + 1), array($item->id));
                        $left = array_slice($newpeers, $oldkey + 1);
                        $keyleft = array_search($item->id, $left);
                        unset($left[$keyleft]);
                        $left = array_values($left);
                        $neworder = array_merge($neworder, $left);
                    } else {
                        // Replace moving category down.
                        $neworder = array_merge(array_slice($newpeers, 0, $key), array_slice($newpeers, $key + 1, $oldkey),
                                                array($item->id), array_slice($newpeers, $oldkey + 1));
                    }
                } else {
                    $neworder = array_merge(array_slice($newpeers, 0, $oldkey + 1), array($item->id),
                                            array_slice($newpeers, $oldkey + 1));
                }
                // Set new order of categories.
                $environment['dest']->reorder_peers($neworder);
            } else {
                // Create new nested list. Replace moving category to it.
                $newlist = new ajax_question_category_list($this->type, $this->attributes, $this->editable, $this->pageurl,
                                                           $this->page, $this->pageparamname, $this->itemsperpage, $this->context);
                $newlist->parentitem = $afteritem;
                $newparent = $afteritem->id;
                $DB->set_field($environment['dest']->table, "parent", $newparent, array("id" => $item->id));
            }
        } else {
            // Replacing at the same level before some item.
            $beforeitem = $environment['dest']->find_item($environment['before']);
            if (isset($beforeitem->parentlist->parentitem)) {
                $newparent = $beforeitem->parentlist->parentitem->id;
            } else {
                $newparent = 0;
            }
            $DB->set_field($environment['dest']->table, "parent", $newparent, array("id" => $item->id));
            $newpeers = $environment['dest']->get_items_peers($beforeitem->id);
            // Place of item after which should be added moving category.
            $oldkey = array_search($beforeitem->id, $newpeers);
            // Place of moving category.
            $key = array_search($item->id, $newpeers);
            // If moving category was at the same list reoder categories in list.
            if ($key) {
                $neworder = array_merge(array_slice($newpeers, 0, $oldkey), array($item->id),
                                        array_slice($newpeers, $oldkey, $key), array_slice($newpeers, $key + 1));
            } else {
                $neworder = array_merge(array_slice($newpeers, 0, $oldkey), array($item->id), array_slice($newpeers, $oldkey));
            }
            $environment['dest']->reorder_peers($neworder);
        }
    }


    /**
     * Returns html string.
     *
     * @param integer $indent depth of indentation.
     */
    public function to_html($indent=0, $extraargs=array()) {
        $attributes = array(
            'id' => 'ajaxlistitem',
        );

        $placeholder = array(
            'id' => 'placeholder',
        );

        if (count($this->items)) {
            $tabs = str_repeat("\t", $indent);
            $first = true;
            $itemiter = 1;
            $lastitem = '';
            $html = '';
            // Get html string for each list.
            foreach ($this->items as $item) {
                // Create item attributes.
                $itemattributes = array(
                    'id' => 'ajaxitem',
                    'data-id' => $item->id,
                );
                // Add first placeholder for first item.
                if ($first) {
                    $html .= html_writer::start_tag('div', $placeholder);
                    $html .= html_writer::end_tag('div');
                }
                // Write item html.
                $html .= html_writer::start_tag('li', $attributes);
                $html .= html_writer::start_div('ajaxitem', $itemattributes);
                $last = (count($this->items) == $itemiter);
                if ($this->editable) {
                    $item->set_icon_html($first, $last, $lastitem);
                }
                if ($itemhtml = $item->to_html($indent + 1, $extraargs)) {
                    $html .= $itemhtml;
                }

                $html .= html_writer::end_tag('li');
                $html .= html_writer::start_tag('div', $placeholder);
                $html .= html_writer::end_tag('div');
                $first = false;
                $lastitem = $item;
                $itemiter++;
            }
        } else {
            $html = '';
        }
        if ($html) {// If there are list items to display then wrap them in ul / ol tag.
            $tabs = str_repeat("\t", $indent);
            $html = $tabs.'<'.$this->type.((!empty($this->attributes)) ? (' '.$this->attributes) : '').">\n".$html;
            $html .= $tabs."</".$this->type.">\n";
        } else {
            $html = '';
        }
        return $html;
    }
}


/**
 * An item in a list of question categories.
 *
 */
class ajax_question_category_list_item extends question_category_list_item {
    /**
     * Returns array of all children items of this.
     *
     */
    public function get_all_children() {
        $children = array();
        if (isset($this->children)) {
            if (isset($this->children->items)) {
                foreach ($this->children->items as $item) {
                    $children[] = $item->id;
                    $children = array_merge($children, $item->get_all_children());
                }
            }
        }
        return $children;
    }

    public function set_icon_html($first, $last, $lastitem) {
        global $CFG;
        $category = $this->item;
        $url = new moodle_url('/question/category.php', ($this->parentlist->pageurl->params() + array('edit' => $category->id)));
        $this->icons['edit'] = $this->image_icon(get_string('editthiscategory', 'question'), $url, 'edit');

    }

    /**
     * Returns html
     *
     * @param integer $indent
     * @param array $extraargs any extra data that is needed to print the list item
     *                            may be used by sub class.
     * @return string html
     */
    public function to_html($indent = 0, $extraargs = array()) {
        if (!$this->display) {
            return '';
        }
        $tabs = str_repeat("\t", $indent);

        if (isset($this->children)) {
            $childrenhtml = $this->children->to_html($indent+1, $extraargs);
        } else {
            $childrenhtml = '';
        }
        return $this->item_html($extraargs).'&nbsp;'.(join($this->icons, '')) . html_writer::end_div() . (($childrenhtml !='')?("\n".$childrenhtml):'');
    }

    public function item_html($extraargs = array()) {
        global $CFG, $OUTPUT;
        $str = $extraargs['str'];
        $category = $this->item;
        $editqestions = get_string('editquestions', 'question');

        // Each section adds html to be displayed as part of this list item.
        $questionbankurl = new moodle_url('/question/edit.php', $this->parentlist->pageurl->params());
        $questionbankurl->param('cat', $category->id . ',' . $category->contextid);
        $catediturl = new moodle_url($this->parentlist->pageurl, array('edit' => $this->id));
        $item = '';
        // Add drag-handle icon.
        if ($this->parentlist !== null && ($this->parentlist->parentitem !== null || count($this->parentlist->items) > 1)) {
            $item .= html_writer::div($OUTPUT->pix_icon('i/move_2d', 'You can drag and drop this category'), 'drag-handle');
        }
        $item .= html_writer::tag('b', html_writer::link($catediturl,
                format_string(' ' . $category->name, true, array('context' => $this->parentlist->context)),
                array('title' => $str->edit))) . ' ';
        $item .= html_writer::link($questionbankurl, '(' . $category->questioncount . ')',
                array('title' => $editqestions)) . ' ';
        $item .= format_text($category->info, $category->infoformat,
                array('context' => $this->parentlist->context, 'noclean' => true));

        // Don't allow delete if this is the last category in this context.
        if (count($this->parentlist->records) != 1) {
            $deleteurl = new moodle_url($this->parentlist->pageurl, array('delete' => $this->id, 'sesskey' => sesskey()));
            $item .= html_writer::link($deleteurl,
                    html_writer::empty_tag('img', array('src' => $OUTPUT->pix_url('t/delete'),
                            'class' => 'iconsmall', 'alt' => $str->delete)),
                    array('title' => $str->delete));
        }
        return $item;
    }
}


/**
 * Class representing q question category
 */
class ajax_question_category_object extends question_category_object {

    /**
     * Initializes this classes general category-related variables
     */
    public function initialize($page, $contexts, $currentcat, $defaultcategory, $todelete, $addcontexts) {
        $lastlist = null;
        foreach ($contexts as $context) {
            $this->editlists[$context->id] = new ajax_question_category_list('ul', 'id="ajaxcategorylist" data-id = "' .
                                                                             $context->id . '"', true, $this->pageurl, $page,
                                                                             'cpage', QUESTION_PAGE_LENGTH, $context);
            $this->editlists[$context->id]->lastlist =& $lastlist;
            if ($lastlist !== null) {
                $lastlist->nextlist =& $this->editlists[$context->id];
            }
            $lastlist =& $this->editlists[$context->id];
        }

        $count = 1;
        $paged = false;
        foreach ($this->editlists as $key => $list) {
            list($paged, $count) = $this->editlists[$key]->list_from_records($paged, $count);
        }
        $this->catform = new question_category_edit_form($this->pageurl, compact('contexts', 'currentcat'));
        if (!$currentcat) {
            $this->catform->set_data(array('parent' => $defaultcategory));
        }
    }

    /**
     * Returns list with category.
     *
     * @param integer $categoryid id of category which should be in searched list.
     */
    public function find_list($categoryid) {
        foreach ($this->editlists as $key => $list) {
            if ($list->find_item($categoryid, true) !== null) {
                return $list;
            }
        }
        return null;
    }
}
