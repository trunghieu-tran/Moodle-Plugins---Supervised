<?php
// This file is part of CorrectWriting question type - https://code.google.com/p/oasychev-moodle-plugins/
//
// CorrectWriting question type is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// CorrectWriting is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with CorrectWriting.  If not, see <http://www.gnu.org/licenses/>.

defined('MOODLE_INTERNAL') || die();



class qtype_correctwriting_enum_catcher {

    protected $enums;

    /**
     * Do all processing and fill enums.
     * @param object $tree syntax tree of correct answer.
     */
    public function __construct($tree) {
        $this->enums = array();
        //find enumeration on 2 levels statement and expression.
        $this->find_stmt($tree);
        $this->find_enum_decl($tree);

        //clear up empty enumerations.
        for ($i=0; $i < count($this->enums); $i++) {
            if (count($this->enums[$i])==0) {
                unset($this->enums[$i]);
            }
        }
    }

    /**
     * Search variable declaration in given node.
     * @param object $node of syntax tree for correct answer.
     * @param boolean $root is current node is root node.
     * @param array $enum enumaration's array.
     */
    protected function find_var_decl($node,$root = false, &$enum) {
        $excluded_keys = array(); // excluded keys of enumeration
        $temp = null; // temporary variable

        if ($enum == null) {
            $enum = array();
        }

        // if current node is type-node return true
        if (!$root && !is_array($node)) {
            if($node->type() == "type")
                return true;
        }

        // get childs of current node
        if($root&&is_array($node))
            $temp = $node;
        else
            $temp = $node->childs();

        // if node has childs
        // else if current node is idetifier append it to enumeration
        if ($temp != null) {

            // update exluded keys array
            foreach ($temp as $key => $value) {
                if (get_class($value) == "block_formal_langs_c_token_operators" && $value->value() == "=") {
                    $enum[] = [$temp[$key-1]->token_index(),$temp[$key+1]->token_index()];
                    $excluded_keys[] = $key-1;
                    $excluded_keys[] = $key;
                    $excluded_keys[] = $key+1;
                } elseif (get_class($value) == "block_formal_langs_c_token_operators" && $value->value() == "*") {
                    $enum[] = [$value->token_index(),$temp[$key+1]->token_index()];
                    $excluded_keys[] = $key;
                    $excluded_keys[] = $key+1;
                }
            }

            // append element to enumeration
            foreach ($temp as $key => $value) {
                if(!in_array($key, $excluded_keys)) {
                    if($root) {
                        $last = array();
                        $this->find_var_decl($value,false,$last,false);
                        $enum[] = $last;
                    } else {
                        $this->find_var_decl($value,false,$enum,false);
                    }
                }
            }
        } elseif (get_class($node) == "block_formal_langs_c_token_identifier") {
            if ($node) {
                $enum[] = [$node->token_index(),$node->token_index()];
            }
        }

        //if current node is root, set enumeration field
        if ($root) {
            $this->enums = $enum;
        }
    }

    /**
     * Search enumeration declaration in given node.
     * @param object $node of syntax tree for correct answer.
     */
    protected function find_enum_decl($node) {
        $enumword = false; // boolean variable show is find enum keyword
        $enumbody = false; // boolean variable show is find enumeration body
        $excluded_keys = array(); // excluded keys of enumeration

        // get childs of current node
        if(is_array($node))
            $temp = $node;
        else
            $temp = $node->childs();
        // if node has childs
        if($temp != null)
            // find enum keyword and enumeration body and update enumeration array.
            foreach ($temp as $key => $value) {
                if (get_class($value) == "block_formal_langs_c_token_keyword" && $value->value() == "enum") {
                    $enumword = true;
                    $excluded_keys[] = $key;
                } else if ($enumword && $value->type() == "enum_body") {
                    $enumbody = true;
                    $excluded_keys[] = $key;
                    $this->analyze_enum($value);
                }
            }
        
        //if enumeration not find, analize node for others enumeration rules 
        if(!($enumbody&&$enumword) && $temp != null) {
            foreach ($temp as $key => $value) {
                if (!in_array($key, $excluded_keys)) {
                    $this->find_enum_decl($value);
                }
            }
        }
    }

    /**
     * Search logic expression in given node.
     * @param object $node of syntax tree for correct answer.
     */
    protected function find_logic_expr($node) {
        // TODO search logic expressions(||,&&,==,!=) in given node and in its childs.
    }

    /**
     * Search bit expression in given node.
     * @param object $node of syntax tree for correct answer.
     */
    protected function find_bit_expr($node) {
        // TODO search binary expressions(|,&) in given node and in its childs.
    }

    /**
     * Get element position in correct answer, by given $node object.
     * @param object $node of syntax tree for correct answer.
     */
    protected function get_element_position($node) {
        $position = array();//array - enumeration's element position 
        
        // calculate position
        // if current node is token? get position
        // else get position of first and last childs of current node
        if (method_exists($node, "token_index")) {
            $position[] = $node->token_index();
        } else {
            $childs = $node->childs();
            $reset = reset($childs);
            $end = end($childs);
            $childs = ($this->get_element_position($reset));
            $position[] = reset($childs);
            $childs = ($this->get_element_position($end));
            $position[] = end($childs);
        }
        return $position;
    }

    /**
     * Append element to enumeration, check math rules.
     * @param object $node of syntax tree for correct answer.
     * @param array $enum of enumeration elements.
     */
    protected function append_to_math_enum($node,&$enum) {
        $start = reset($node->childs());// first child of current node
        $end = end($node->childs());// last child of current node
        
        // if first child is composite check it for enumerations
        // else append identifier to current enumeration
        if(get_class($start) != "block_formal_langs_c_token_identifier") {
            // if first child has same operation append it's childs to current enumeration
            //else append it as one element
            if(method_exists($start->childs()[1], "value")
               && $start->childs()[1]->value() == $node->childs()[1]->value()) {
                $this->append_to_math_enum($start,$enum);
            } else {
                $this->find_math_expr($start,$this->enums);
                $enum[] = $this->get_element_position($start);
            }
        } else {
            $enum[] = [$start->token_index(), $start->token_index()];
        }
        // if last child is composite check it for enumerations
        // else append identifair to current enumeration
        if(get_class($end) != "block_formal_langs_c_token_identifier") {
            // if last child has same operation append it's childs to current enumeration
            //else append it as one element
            if(method_exists($end->childs()[1], "value")
               && $end->childs()[1]->value() == $node->childs()[1]->value()) {
                $this->append_to_math_enum($end,$enum);
            } else {
                $this->find_math_expr($end,$this->enums);
                $enum[] = $this->get_element_position($end);
            }
        } else {
            $enum[] = [$end->token_index(), $end->token_index()];
        }
    }

    /**
     * Search math expression, by given $node object.
     * @param object $node of syntax tree for correct answer.
     * @param array $enum of enumeration elements.
     */
    protected function find_math_expr($node,&$enum) {
        // if current node is math expr work with it
        // else search in it's childs
        if (method_exists($node->childs()[1], "value") && ($node->childs()[1]->value() == "*"
                                                       || $node->childs()[1]->value() == "+")) {
            $enumpart = array(); //array of finded enum elements
            $end = end($node->childs()); // right value of math epxr
            $start = reset($node->childs()); // left value of math epxr
            // Append left value to enumeration
            // if left value is not identifier append it and search elements in it
            // else just append 
            if(get_class($start) != "block_formal_langs_c_token_identifier") {
                if(method_exists($start->childs()[1], "value")
                   && $start->childs()[1]->value() == $node->childs()[1]->value()) {
                    $this->append_to_math_enum($start,$enumpart);
                } else {
                    $this->find_math_expr($start,$this->enums);
                    $enumpart[] = $this->get_element_position($start);
                }
            } else {
                $enumpart[] = [$start->token_index(), $start->token_index()];
            }
            // Append right value to enumeration
            // if right value is not identifier append it and search elements in it
            // else just append 
            if(get_class($end) != "block_formal_langs_c_token_identifier") {
                if(method_exists($end->childs()[1], "value")
                   && $end->childs()[1]->value() == $node->childs()[1]->value()) {
                    $this->append_to_math_enum($end,$enumpart);
                } else {
                    $this->find_math_expr($end,$this->enums);
                    $enumpart[] = $this->get_element_position($end);
                }
            } else {
                $enumpart[] = [$end->token_index(), $end->token_index()];
            }
            $enum[] = $enumpart;
        } else {
            // if current node are expression in brakets find enumeration in it
            $is_find = false;
            foreach ($node->childs() as $value) {
                if(get_class($value) == "block_formal_langs_c_token_bracket" && $value->value() == "(") {
                    $is_find =true;
                }
            }

            if ($is_find) {
                $this->find_math_expr($node->childs()[1],$this->enums);
            }
        }
    }

    /**
     * Search and append init as element of enum.
     * @param object $node of syntax tree for correct answer.
     * @param array $enum of enumeration elements.
     */
    protected function find_long_init($node,&$enum) {
        $childs = $node->childs(); // childs of current node
        // if current node is init node append it
        if (count($childs) == 3 && get_class($childs[0]) == "block_formal_langs_c_token_identifier"
                                        && $childs[1]->value() == "=") {
            $enum[] = [$childs[0]->token_index(),$childs[0]->token_index()];
        }
        // if current node childs may include init nodes search in childs
        if (count($childs) == 3 && $childs[2]->type() == "expr_prec_10") {
            $this->find_long_init($childs[2],$array);
        // if current node is identifier node append it.
        } else if (count($childs) == 3 && get_class($childs[2]) == "block_formal_langs_c_token_identifier") {
            $enum[] = [$childs[2]->token_index(),$childs[2]->token_index()];
        }

    }

    /**
     * Search stmt node, by given $node.
     * @param object $node of syntax tree for correct answer.
     */
    protected function find_stmt($node) {
        $childs = null;// childs of current node.
        $is_find = false;// boolean value show find stmt node or no.
        
        //Get childs of current node and search stmt in it.
        if(is_array($node))
            $childs = $node;
        else
        {
            // if current node stmt? analaze it,
            // else get childs
            if ($node->type() == "stmt") {
                $this->analyze_stmt($node);
                $is_find = true;
            } else {
                $childs = $node->childs();
            }
        }

        // Search stmt node in childs if it is need
        if (!$is_find && $childs != null) {
            foreach ($temp as $value) {
                $this->find_stmt($value);
            }
        }
    }

    /**
     * Search typeword, by given $node object.
     * @param object $node of syntax tree for correct answer.
     */
    protected function find_typeword($node) {
        
        // if current node is typeword node return true
        // else search in childs
        if (!is_array($node)&& $node->type() == "type") {
            return true;
        } else {
            $childs = null;// childs of current node 

            //Get childs of current node
            if(is_array($node))
                $childs = $node;
            else
                $childs = $node->childs();

            // Search typeword in childs if they existed
            if ($childs != null) {
                $result = array();
                
                foreach ($temp as $value) {
                    $result[] = $this->find_typeword($value);
                }
                
                foreach ($result as $value) {
                    if($value)
                        return true;
                }
            }
        }
        return false;
    }

    /**
     * Search expr_10 node, by given $node object.
     * @param object $node of syntax tree for correct answer.
     */
    protected function find_expr_10($node) {
        $childs = null; // childs for current node
        $is_find = false; // boolean value show find expr_10 node or no
        // get node childs
        if(is_array($node))
            $childs = $node;
        else
        {
            // if current node is expr_10 node, search long init in it
            // else get childs
            if ($node->type() == "expr_prec_10") {
                $enum = array();
                $this->find_long_init($node,$enum);
                $this->enums[] = $enum;
                $is_find = true;
            } else {
                $childs = $node->childs();
            }
        }
        // Search expr_10 node in childs of current node
        if (!$is_find && $childs != null) {
            foreach ($childs as $value) {
                $this->find_expr_10($value);
            }
        }
    }
    
    /**
     * Analaze stmt $node, check rules for math, variable declaration and long init enumerations.
     * @param object $node of syntax tree for correct answer.
     */
    protected function analyze_stmt($node) {
        
        // Search variable declaretion enumerations 
        if ($this->find_typeword($node)) {
            $enum = null;
            $this->find_var_decl($node,0,true,$enum);
        }

        // Search long init and math enumerations
        $this->find_expr_10($node);
        $this->find_math_expr($node->childs()[0],$this->enums);

    }

    /**
     * Analaze enumeration declaration $node.
     * @param object $node of syntax tree for correct answer.
     */
    protected function analyze_enum($node) {
        $enumbody = null; // enumeration body 
        // Search enumeration body
        foreach ($node->childs() as $value) {
            if ($value->type() == "enum_value_list")
                $enumbody = $value;
        }
        // If body find append declaration to enumeration list
        if($enumbody!=null) {
            $enum = array();
            $this->parse_enum_value_list($node,$enum);
            $this->enums = $enum;
        }
    }

    /**
     * Parse elements of enumeration elements.
     * @param object $node of syntax tree for correct answer.
     * @param array $enum of enumeration elements.
     */
    protected function parse_enum_value_list($node,&$enum) {
        $excluded_keys = array(); // array of excluded keys 
        $enum_elem = array(); // array to keep enumeration element position
        
        // Search enumeration body
        foreach ($node->childs() as $key => $value) {
            if ($value->type() == "enum_value_list") {
                $this->parse_enum_value_list($value,$enum);
                $excluded_keys[] = $key;
            }
        }
        // Append element to enumeration
        foreach ($node->childs() as $key => $value) {
            if (!in_array($key, $excluded_keys)) {
                // if element end append it to enumeration
                // else append token position to array 
                if ($value->value() == ",") {
                    if (count($enum_elem)!=0) {
                        $enum[] = [reset($enum_elem),end($enum_elem)];
                        $enum_elem = array();
                    }
                } else if (get_class($value) != "block_formal_langs_c_token_bracket") {
                    $enum_elem[] = $value->token_index();
                }
            }
        }
        // append element to enumeration if it exist.
        if (count($enum_elem)!=0) {
            $enum[] = [reset($enum_elem),end($enum_elem)];
            $enum_elem = array();
        }
    }
}