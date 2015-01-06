<?php

defined('MOODLE_INTERNAL') || die();

// Number of categories to display on page.
define('QUESTION_PAGE_LENGTH', 25);

require_once($CFG->libdir . '/listlib.php');
require_once($CFG->dirroot . '/question/category_form.php');
require_once($CFG->dirroot . '/question/move_form.php');

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
                var_dump($newpeers);
                // Place of item after which should be added moving category.
                $oldkey = array_search($afteritem->id, $newpeers);
                // Place of moving category.
                $key = array_search($item->id, $newpeers);
                // If moving category was at the same list reoder categories in list.
                if ($key) {
                    // Replace moving category up.
                    if ($oldkey < $key) {
                        if ($key !== count($newpeers) - 1) {
                            $neworder = array_merge(array_slice($newpeers, 0, $oldkey + 1), array($item->id),
                                                   array_slice($newpeers, $oldkey + 1, $key - 1), array_slice($newpeers, $key + 1));
                        } else {
                            $neworder = array_merge(array_slice($newpeers, 0, $oldkey + 1), array($item->id),
                                                    array_slice($newpeers, $oldkey + 1, $key - 2));
                        }
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
                $newlist = new ajax_question_category_list($this->type, $this->attributes, $this->editable, $this->pageurl, $this->page,
                                                           $this->pageparamname, $this->itemsperpage, $this->context);
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
                $neworder = array_merge(array_slice($newpeers, 0, $oldkey), array($item->id), array_slice($newpeers, $oldkey, $key), array_slice($newpeers, $key + 1));
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
                $html .= html_writer::end_div();
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
        if ($html) {// if there are list items to display then wrap them in ul / ol tag.
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
class ajax_question_category_list_item extends list_item {
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

        // don't allow delete if this is the last category in this context.
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
class ajax_question_category_object {

    /**
     * @var array common language strings.
     */
    public $str;

    /**
     * @var array nested lists to display categories.
     */
    public $editlists = array();
    public $newtable;
    public $tab;
    public $tabsize = 3;

    /**
     * @var moodle_url Object representing url for this page
     */
    public $pageurl;

    /**
     * @var question_category_edit_form Object representing form for adding / editing categories.
     */
    public $catform;

    /**
     * Constructor
     *
     * Gets necessary strings and sets relevant path information
     */
    public function __construct($page, $pageurl, $contexts, $currentcat, $defaultcategory, $todelete, $addcontexts) {
        global $CFG, $COURSE, $OUTPUT;

        $this->tab = str_repeat('&nbsp;', $this->tabsize);

        $this->str = new stdClass();
        $this->str->course         = get_string('course');
        $this->str->category       = get_string('category', 'question');
        $this->str->categoryinfo   = get_string('categoryinfo', 'question');
        $this->str->questions      = get_string('questions', 'question');
        $this->str->add            = get_string('add');
        $this->str->delete         = get_string('delete');
        $this->str->moveup         = get_string('moveup');
        $this->str->movedown       = get_string('movedown');
        $this->str->edit           = get_string('editthiscategory', 'question');
        $this->str->hide           = get_string('hide');
        $this->str->order          = get_string('order');
        $this->str->parent         = get_string('parent', 'question');
        $this->str->add            = get_string('add');
        $this->str->action         = get_string('action');
        $this->str->top            = get_string('top');
        $this->str->addcategory    = get_string('addcategory', 'question');
        $this->str->editcategory   = get_string('editcategory', 'question');
        $this->str->cancel         = get_string('cancel');
        $this->str->editcategories = get_string('editcategories', 'question');
        $this->str->page           = get_string('page');

        $this->pageurl = $pageurl;

        $this->initialize($page, $contexts, $currentcat, $defaultcategory, $todelete, $addcontexts);
    }

    /**
     * Initializes this classes general category-related variables
     */
    public function initialize($page, $contexts, $currentcat, $defaultcategory, $todelete, $addcontexts) {
        $lastlist = null;
        foreach ($contexts as $context) {
            $this->editlists[$context->id] = new ajax_question_category_list('ul', 'id="ajaxcategorylist" data-id = "' . $context->id . '"',
                                                                             true, $this->pageurl, $page, 'cpage', QUESTION_PAGE_LENGTH, $context);
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

    /**
     * Displays the user interface
     *
     */
    public function display_user_interface() {

        // Interface for editing existing categories
        $this->output_edit_lists();

        echo '<br />';
        // Interface for adding a new category:
        $this->output_new_table();
        echo '<br />';

    }

    /**
     * Outputs a table to allow entry of a new category
     */
    public function output_new_table() {
        $this->catform->display();
    }

    /**
     * Outputs a list to allow editing/rearranging of existing categories
     *
     * $this->initialize() must have already been called
     *
     */
    public function output_edit_lists() {
        global $OUTPUT;

        echo $OUTPUT->heading_with_help(get_string('editcategories', 'question'), 'editcategories', 'question');

        foreach ($this->editlists as $context => $list) {
            $listhtml = $list->to_html(0, array('str' => $this->str));
            if ($listhtml) {
                echo $OUTPUT->box_start('boxwidthwide boxaligncenter generalbox questioncategories contextlevel' . $list->context->contextlevel);
                $fullcontext = context::instance_by_id($context);
                echo $OUTPUT->heading(get_string('questioncatsfor', 'question', $fullcontext->get_context_name()), 3);
                echo $listhtml;
                echo $OUTPUT->box_end();
            }
        }
        echo $list->display_page_numbers();
    }

    /**
     * gets all the courseids for the given categories
     *
     * @param array categories contains category objects in  a tree representation
     * @return array courseids flat array in form categoryid=>courseid
     */
    public function get_course_ids($categories) {
        $courseids = array();
        foreach ($categories as $key => $cat) {
            $courseids[$key] = $cat->course;
            if (!empty($cat->children)) {
                $courseids = array_merge($courseids, $this->get_course_ids($cat->children));
            }
        }
        return $courseids;
    }

    public function edit_single_category($categoryid) {
        // Interface for adding a new category
        global $COURSE, $DB;
        // Interface for editing existing categories
        if ($category = $DB->get_record("question_categories", array("id" => $categoryid))) {

            $category->parent = "$category->parent,$category->contextid";
            $category->submitbutton = get_string('savechanges');
            $category->categoryheader = $this->str->edit;
            $this->catform->set_data($category);
            $this->catform->display();
        } else {
            print_error('invalidcategory', '', '', $categoryid);
        }
    }

    /**
     * Sets the viable parents
     *
     *  Viable parents are any except for the category itself, or any of it's descendants
     *  The parentstrings parameter is passed by reference and changed by this function.
     *
     * @param    array parentstrings a list of parentstrings
     * @param   object category
     */
    public function set_viable_parents(&$parentstrings, $category) {

        unset($parentstrings[$category->id]);
        if (isset($category->children)) {
            foreach ($category->children as $child) {
                $this->set_viable_parents($parentstrings, $child);
            }
        }
    }

    /**
     * Gets question categories
     *
     * @param    int parent - if given, restrict records to those with this parent id.
     * @param    string sort - [[sortfield [,sortfield]] {ASC|DESC}]
     * @return   array categories
     */
    public function get_question_categories($parent=null, $sort="sortorder ASC") {
        global $COURSE, $DB;
        if (is_null($parent)) {
            $categories = $DB->get_records('question_categories', array('course' => $COURSE->id), $sort);
        } else {
            $select = "parent = ? AND course = ?";
            $categories = $DB->get_records_select('question_categories', $select, array($parent, $COURSE->id), $sort);
        }
        return $categories;
    }

    /**
     * Deletes an existing question category
     *
     * @param int deletecat id of category to delete
     */
    public function delete_category($categoryid) {
        global $CFG, $DB;
        question_can_delete_cat($categoryid);
        if (!$category = $DB->get_record("question_categories", array("id" => $categoryid))) {  // security
            print_error('unknowcategory');
        }
        // Send the children categories to live with their grandparent
        $DB->set_field("question_categories", "parent", $category->parent, array("parent" => $category->id));

        // Finally delete the category itself
        $DB->delete_records("question_categories", array("id" => $category->id));
    }

    public function move_questions_and_delete_category($oldcat, $newcat) {
        question_can_delete_cat($oldcat);
        $this->move_questions($oldcat, $newcat);
        $this->delete_category($oldcat);
    }

    public function display_move_form($questionsincategory, $category) {
        global $OUTPUT;
        $vars = new stdClass();
        $vars->name = $category->name;
        $vars->count = $questionsincategory;
        echo $OUTPUT->box(get_string('categorymove', 'question', $vars), 'generalbox boxaligncenter');
        $this->moveform->display();
    }

    public function move_questions($oldcat, $newcat) {
        global $DB;
        $questionids = $DB->get_records_select_menu('question',
                'category = ? AND (parent = 0 OR parent = id)', array($oldcat), '', 'id,1');
        question_move_questions_to_category(array_keys($questionids), $newcat);
    }

    /**
     * Creates a new category with given params
     */
    public function add_category($newparent, $newcategory, $newinfo, $return = false, $newinfoformat = FORMAT_HTML) {
        global $DB;
        if (empty($newcategory)) {
            print_error('categorynamecantbeblank', 'question');
        }
        list($parentid, $contextid) = explode(',', $newparent);
        // moodle_form makes sure select element output is legal no need for further cleaning
        require_capability('moodle/question:managecategory', context::instance_by_id($contextid));

        if ($parentid) {
            if (!($DB->get_field('question_categories', 'contextid', array('id' => $parentid)) == $contextid)) {
                print_error('cannotinsertquestioncatecontext', 'question', '', array('cat' => $newcategory, 'ctx' => $contextid));
            }
        }

        $cat = new stdClass();
        $cat->parent = $parentid;
        $cat->contextid = $contextid;
        $cat->name = $newcategory;
        $cat->info = $newinfo;
        $cat->infoformat = $newinfoformat;
        $cat->sortorder = 999;
        $cat->stamp = make_unique_id_code();
        $categoryid = $DB->insert_record("question_categories", $cat);

        // Log the creation of this category.
        $params = array(
            'objectid' => $categoryid,
            'contextid' => $contextid
        );
        $event = \core\event\question_category_created::create($params);
        $event->trigger();

        if ($return) {
            return $categoryid;
        } else {
            redirect($this->pageurl);// always redirect after successful action
        }
    }

    /**
     * Updates an existing category with given params
     */
    public function update_category($updateid, $newparent, $newname, $newinfo, $newinfoformat = FORMAT_HTML) {
        global $CFG, $DB;
        if (empty($newname)) {
            print_error('categorynamecantbeblank', 'question');
        }

        // Get the record we are updating.
        $oldcat = $DB->get_record('question_categories', array('id' => $updateid));
        $lastcategoryinthiscontext = question_is_only_toplevel_category_in_context($updateid);

        if (!empty($newparent) && !$lastcategoryinthiscontext) {
            list($parentid, $tocontextid) = explode(',', $newparent);
        } else {
            $parentid = $oldcat->parent;
            $tocontextid = $oldcat->contextid;
        }

        // Check permissions.
        $fromcontext = context::instance_by_id($oldcat->contextid);
        require_capability('moodle/question:managecategory', $fromcontext);

        // If moving to another context, check permissions some more.
        if ($oldcat->contextid != $tocontextid) {
            $tocontext = context::instance_by_id($tocontextid);
            require_capability('moodle/question:managecategory', $tocontext);
        }

        // Update the category record.
        $cat = new stdClass();
        $cat->id = $updateid;
        $cat->name = $newname;
        $cat->info = $newinfo;
        $cat->infoformat = $newinfoformat;
        $cat->parent = $parentid;
        $cat->contextid = $tocontextid;
        $DB->update_record('question_categories', $cat);

        // If the category name has changed, rename any random questions in that category.
        if ($oldcat->name != $cat->name) {
            $where = "qtype = 'random' AND category = ? AND " . $DB->sql_compare_text('questiontext') . " = ?";

            $randomqtype = question_bank::get_qtype('random');
            $randomqname = $randomqtype->question_name($cat, false);
            $DB->set_field_select('question', 'name', $randomqname, $where, array($cat->id, '0'));

            $randomqname = $randomqtype->question_name($cat, true);
            $DB->set_field_select('question', 'name', $randomqname, $where, array($cat->id, '1'));
        }

        if ($oldcat->contextid != $tocontextid) {
            // Moving to a new context. Must move files belonging to questions.
            question_move_category_to_context($cat->id, $oldcat->contextid, $tocontextid);
        }

        // Cat param depends on the context id, so update it.
        $this->pageurl->param('cat', $updateid . ',' . $tocontextid);
        redirect($this->pageurl);
    }
}
