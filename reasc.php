<?php //$Id: reasc.php,put version put time dvkolesov Exp $
//fa - finite automate

//+++создать кнопку хинт, демонстрирующюю что-нибудь.
//+++разобраться с делением на классы
//+++заменить табы пробелами
//+++расставить пробелы в усовиях и между ифом и скобками
//закоментировать по английски
//написать парсер
//+++6 function need convert to static

/*ПЛАН:
*Все свойства всех классов сделать private, класс тестировщик сделать для всех дружественным,
*класс reasc сделать дружественным для класса узла (node) и класса результата сравнения (compare_result)
*класс reasc снабдить методами для взаимодействия с внешней програмой, все остальные методы сделать private.
*
*+++Убрать $croot, $cconn, $finiteautomate, вместо них передавать в buildfa и другие функции использующие $this->c.*
*   номер ассерта(нуль для основного выражения) по которому строится автомат
*   и использовать сразу $finiteautomates[<полученный номер>] аналогично для $roots и $connection
*
*+++сделать статическими(т.к. они не используют ни свойства, ни динамические методы) следующии методы:
*   is_include_characters, push_unique(переименовать в push_unique), followpos, lastpos, firstpos, nullable
*
*добавить объект класса парсера как свойство класса reasc
*/

/*PUBLIC МЕТОДЫ КЛАССА REASC:
*reasc::input_regex($regex); получает регулярное выражение и строит по нему ДКА, возвращает 0 если автомат был  построен,
*                            если регекс содержал неподдерживаемую операцию возвращает её номер,
*                            если регекс содержал синтаксическую ошибку возвращает -1.
*reasc::result($string); выполняет сравнение регекса, полученого в пердыдущем методе, со строкой, 
*                        если регекс небыл введен возвращает ложь, если сравнение было проведено истину.
*reasc::index()         если сравнение было проведено, возвращает индекс последнего верного символа (от -1 до strlen -1)
*                       если сравнение небыло проведено возвращает false.
*reasc::full()          если сравнение небыло проведено возвращает -1, 0 если соответствие неполное, 1 если полное.
*reasc::next()          возвращает символ допустимый на следующей позиции, может быть 0.
*                       если сравнение небыло проведено возвращает false
*
*если будет время, то сделать функции чтения из файла/записи в файл ДКА
*/

define('LEAF','0');
define('NODE','1');
define('LEAF_CHARCLASS','2');
define('LEAF_EMPTY','3');
define('LEAF_END','4');
define('LEAF_LINK','5');
define('LEAF_METASYMBOLDOT','6');
define('NODE_CONC','7');
define('NODE_ALT','8');
define('NODE_ITER','9');
define('NODE_SUBPATT','10');
define('NODE_CONDSUBPATT','11');
define('NODE_QUESTQUANT','12');
define('NODE_PLUSQUANT','13');
define('NODE_QUANT','14');
define('NODE_ASSERTTF','15');
define('NODE_ASSERTTB','16');
define('NODE_ASSERTFF','17');
define('NODE_ASSERTFB','18');
define('ASSERT','1073741824');
define('DOT','987654321');
define('STREND','123456789');

class node {
    var $type;
    var $subtype;
    var $firop;
    var $secop;
    var $thirdop;
    var $nullable;
    var $number;
    var $firstpos;
    var $lastpos;
    var $direction;
    var $greed;
    var $chars;
    var $leftborder;
    var $rightborder;
    
    function name() {
        return 'node';
    }
}

class finite_automate_state {//finite automate state
    var $asserts;
    var $passages;//хранит номера состояний к которым перейти
    var $marked;//if marked then true else false.
    
    function name() {
        return 'finite_automate_state';
    }
}

class compare_result {
    var $index;
    var $full;
    var $next;
    
    function name() {
        return 'compare_result';
    }
}

class reasc {


    var $connection;//array, $connection[0] for main regex, $connection[<assert number>] for asserts
    var $roots;//array,[0] main root, [<assert number>] assert's root
    var $finiteautomates;
	var $maxnum;
    
    function name() {
        return 'reasc';
    }
    function append_end($index) {
        $root = $this->roots[$index];
        $this->roots[$index] = new node;
        $this->roots[$index]->type = NODE;
        $this->roots[$index]->subtype = NODE_CONC;
        $this->roots[$index]->firop = $root;
        $this->roots[$index]->secop = new node;
        $this->roots[$index]->secop->type = LEAF;
        $this->roots[$index]->secop->subtype = LEAF_END;
        $this->roots[$index]->secop->direction = true;
    }
    /**
    *Function numerate leafs, nodes use for find leafs. Start on root and move to leafs.
    *Put pair of number=>character to $this->connection[$index][].
    *@param $node current node (or leaf) for numerating.
    */
    function numeration($node, $index) {
        if ($node->type==NODE && $node->subtype == NODE_ASSERTTF) {//assert node need number
            $node->number = ++$this->maxnum + ASSERT;
        } else if ($node->type == NODE) {//not need number for not assert node, numerate operands
            $this->numeration($node->firop, $index);
            if ($node->subtype == NODE_CONC || $node->subtype == NODE_ALT) {//concatenation and alternative have second operand, numerate it.
                $this->numeration($node->secop, $index);
            }
        }
        if ($node->type==LEAF) {//leaf need number
            switch($node->subtype) {//number depend from subtype (charclass, metasymbol dot or end symbol)
                case LEAF_CHARCLASS://normal number for charclass
                    $node->number = ++$this->maxnum;
                    $this->connection[$index][$this->maxnum] = $node->chars;
                    break;
                case LEAF_END://STREND number for end leaf
                    $node->number = STREND;
                    break;
                case LEAF_METASYMBOLDOT://normal + DOT for dot leaf
                    $node->number = ++$this->maxnum + DOT;
                    $this->connection[$index][$this->maxnum + DOT] = $node->chars;
                    break;
            }
        }
    }
    /**
    *Function determine: subtree with root in this node can give empty word or not.
    *@param node - node fo analyze
    *@return true if can give empty word, else false
    */
    static function nullable($node) {//to static
        $result = false;
        if ($node->type == NODE) {
            switch($node->subtype) {
                case NODE_ALT://alternative can give empty word if one operand can.
                    $result = (reasc::nullable($node->firop) || reasc::nullable($node->secop));
                    break;
                case NODE_CONC://concatenation can give empty word if both operands can.
                    $result = (reasc::nullable($node->firop) && reasc::nullable($node->secop));
                    reasc::nullable($node->secop);
                    break;
                case NODE_ITER://iteration and question quantificator can give empty word without dependence from operand.
                case NODE_QUESTQUANT:
                    $result = true;
                    reasc::nullable($node->firop);
                    break;
                case NODE_ASSERTTF://assert can give empty word.
                    $result = true;
                    break;//operand of assert not need for main finite automate. It form other finite automate.
            }
        }
        $node->nullable = $result;//save result in node
        return $result;
    }
    /**
    *функция определяет какие символы могут стоять на 1-м месте в слове порождаемом поддеревом с вершиной в данном узле
    *@param $node root of subtree giving word
    *@return numbers of characters (array)
    */
    static function firstpos($node) {//to static
        if ($node->type == NODE) {
            switch($node->subtype) {
                case NODE_ALT:
                    $result = array_merge(reasc::firstpos($node->firop), reasc::firstpos($node->secop));
                    break;
                case NODE_CONC:
                    $result = reasc::firstpos($node->firop);
                    if ($node->firop->nullable) {
                        $result = array_merge($result, reasc::firstpos($node->secop));
                    } else {
                        reasc::firstpos($node->secop);
                    }
                    break;
                case NODE_QUESTQUANT:
                case NODE_ITER:
                    $result = reasc::firstpos($node->firop);
                    break;
                case NODE_ASSERTTF:
                    $result = array($node->number);
                    break;
            }
        } else {
            if ($node->direction) {
                $result = array($node->number);
            } else {
                $result = array(-$node->number);
            }
        }
        $node->firstpos = $result;
        return $result;
    }
    /**
    *функция определяет символы которые могут стоять на последнем месте в слове порождаемом
    *поддеревом с вершиной в данном узле
    @param $node - root of subtree
    @return numbers of characters (array)
    */
    static function lastpos($node) {//to static
        if ($node->type == NODE) {
            switch($node->subtype) {
                case NODE_ALT:
                    $result = array_merge(reasc::lastpos($node->firop), reasc::lastpos($node->secop));
                    break;
                case NODE_CONC:
                    $result = reasc::lastpos($node->secop);
                    if ($node->secop->nullable) {
                        $result = array_merge(reasc::lastpos($node->firop), $result);
                    } else {
                        reasc::lastpos($node->firop);
                    }
                    break;
                case NODE_ITER:
                case NODE_QUESTQUANT:
                    $result = reasc::lastpos($node->firop);
                    break;
                case NODE_ASSERTTF:
                    $result = array($node->number);
                    break;
            }
        } else {
            if ($node->direction) {
                $result = array($node->number);
            } else {
                $result = array(-$node->number);
            }
        }
        $node->lastpos = $result;
        return $result;
    }
    static function followpos($node, &$fpmap) {//to static
        if ($node->type == NODE) {
            switch($node->subtype) {
                case NODE_CONC:
                    reasc::followpos($node->firop, $fpmap);
                    reasc::followpos($node->secop, $fpmap);
                    foreach ($node->firop->lastpos as $key) {
                        reasc::push_unique($fpmap[$key], $node->secop->firstpos);
                    }
                    break;
                case NODE_ITER:
                    reasc::followpos($node->firop, $fpmap);
                    foreach ($node->firop->lastpos as $key) {
                        reasc::push_unique($fpmap[$key], $node->firop->firstpos);
                    }
                    break;
                case NODE_ALT:
                    reasc::followpos($node->secop, $fpmap);
                case NODE_QUESTQUANT:
                    reasc::followpos($node->firop, $fpmap);
                    break;
            }
        }
    }
    function buildfa($index) {//Начальное состояние ДКА сохраняется в поле finiteautomates[0][0]
        $old = $this->connection;                     //oстальные состояния в прочих эл-тах этого массива,finiteautomates[!=0] - asserts' fa
        $this->maxnum = 0;
        $this->finiteautomates[$index][0] = new finite_automate_state;
        $this->numeration($this->roots[$index], $index);
        if($old == $this->connection) { }
        reasc::nullable($this->roots[$index]);
        reasc::firstpos($this->roots[$index]);
        reasc::lastpos($this->roots[$index]);
        reasc::followpos($this->roots[$index], $map);
        $this->find_asserts($this->roots[$index]);
        foreach ($this->roots[$index]->firstpos as $value) {
            $this->finiteautomates[$index][0]->passages[$value] = -2;//BUG!!! эта строка зависает!
        }
        $this->finiteautomates[$index][0]->marked = false;
        while ($this->not_marked_state($this->finiteautomates[$index]) !== false) {
            $currentstateindex = $this->not_marked_state($this->finiteautomates[$index]);
            $this->finiteautomates[$index][$currentstateindex]->marked = true;
            foreach ($this->finiteautomates[$index][$currentstateindex]->passages as $num => $passage) {
                $newstate = new finite_automate_state;
                $fpU = $this->followposU($num, $map, $this->finiteautomates[$index][$currentstateindex]->passages, $index);
                foreach ($fpU as $follow) {
                    if ($follow<ASSERT) {
                        $newstate->passages[$follow] = -2;
                    } else {
                        $this->finiteautomates[$index][$currentstateindex]->asserts[] = $follow;
                    }
                }
                if ($num!=STREND) {
                    if ($this->state($newstate->passages, $index) === false && count($newstate->passages) != 0) {
                        array_push($this->finiteautomates[$index], $newstate);
                        end($this->finiteautomates[$index]);
                        $this->finiteautomates[$index][$currentstateindex]->passages[$num] = key($this->finiteautomates[$index]);
                    } else {
                        $this->finiteautomates[$index][$currentstateindex]->passages[$num] = $this->state($newstate->passages, $index);
                    }
                } else {
                    $this->finiteautomates[$index][$currentstateindex]->passages[$num] = -1;
                }
            }
        }
    }
    function compare($string, $assertnumber) {//if main regex then assertnumber is 0
        $index = 0;//char index in string, comparing begin of first char in string
        $end = false;//current state is end state, not yet
        $full = true;//if string match with asserts
        $next = 0;// character can put on next position, 0 for full matching with regex string
        $maxindex = strlen($string);//string cannot match with regex after end, if mismatch with assert - index of last matching with assert character
        $currentstate = 0;//finite automate begin work at start state, zero index in array
        do {
        /*check current character while: 1)checked substring match with regex
                                         2)current character isn't end of string
                                         3)finite automate not be in end state
        */
            $maybeend = false;
            $found = false;//current character no accepted to fa yet
            reset($this->finiteautomates[$assertnumber][$currentstate]->passages);
            ///////////////////////////////////////////////////////////////////////////////////////////////////////////
            //finding positive character class with this character
            reset($this->finiteautomates[$assertnumber][$currentstate]->passages);
            $key = false;
            while (!$found && current($this->finiteautomates[$assertnumber][$currentstate]->passages) !== false) { //while not found and all passages not checked yet
                //if character class number is positive (it's mean what character class is positive) and
                //current character is contain in character class
                $key = key($this->finiteautomates[$assertnumber][$currentstate]->passages);
                $found = ($key > 0 && strpos($this->connection[$assertnumber][$key], $string[$index]) !== false);
                if (!$found) {
                    next($this->finiteautomates[$assertnumber][$currentstate]->passages);
                }
            }
            reset($this->finiteautomates[$assertnumber][$currentstate]->passages);
            while (!$found && current($this->finiteautomates[$assertnumber][$currentstate]->passages) !== false) { //while not found and all passages not checked yet
                //finding metasymbol dot's passages, it accept any character.
                $key = key($this->finiteautomates[$assertnumber][$currentstate]->passages);
                $found = ($key > DOT && $index < strlen($string));
                if (!$found) {
                    next($this->finiteautomates[$assertnumber][$currentstate]->passages);
                }
            }
            $foundkey = $key;
            //finding negative character class without this character
            reset($this->finiteautomates[$assertnumber][$currentstate]->passages);
            while (!$found && current($this->finiteautomates[$assertnumber][$currentstate]->passages) !== false) { //while not found and all passages not checked yet
                $key = key($this->finiteautomates[$assertnumber][$currentstate]->passages);
                $found = ($key < 0 && strpos($this->connection[$assertnumber][abs($key)], $string[$index]) === false);
                if ($found) {
                    $foundkey = $key;
                } else {
                    next($this->finiteautomates[$assertnumber][$currentstate]->passages);
                }
            }
            if (array_key_exists(STREND, $this->finiteautomates[$assertnumber][$currentstate]->passages)) {
            //if current character is end of string and fa can go to end state.
                if ($index == strlen($string)) { //must be end   
                    $found = true;
                    $foundkey = STREND;
                } elseif(count($this->finiteautomates[$assertnumber][$currentstate]->passages) == 1) {//must be end
                    $foundkey = STREND;
                }
                $maybeend = true;//may be end.
            }
            $index++;
            if (count($this->finiteautomates[$assertnumber][$currentstate]->asserts)) { // if there are asserts in this state
                foreach ($this->finiteautomates[$assertnumber][$currentstate]->asserts as $assert) {
                    $tmpres = $this->compare(substr($string, $index), $assert);//result of compare substring starting at next character with current assert
                    if ($tmpres->next !== 0) {
                    /* if string not match with assert then assert give borders
                       match string with regex can't be after mismatch with assert
                       p.s. string can match if it not end when assert end
                    */
                        $full = false;
                        if ($maxindex > $tmpres->index + $index) {
                            $next = $tmpres->next;
                            $maxindex = $tmpres->index + $index;
                        }
                    }
                }
            }
            //form results of check this character
            if ($found) { //if finite automate did accept this character
                $correct = true;
                if ($foundkey != STREND) {// if finite automate go to not end state
                    $currentstate = $this->finiteautomates[$assertnumber][$currentstate]->passages[$key];
                    $end = false;
                } else { 
                    $end = true;
                }
            } else {
                $correct = false;
            }
        } while($correct && !$end && $index <= strlen($string));//index - 1, becase index was incrimented
        //form result comparing string with regex
        $result = new compare_result;$len = strlen($string);
        if ($index - 2 < $maxindex) {//if asserts not give border to lenght of matching substring
            $result->index = $index - 2;
        } else {
            $result->index = $maxindex;
        }
       if (strlen($string) == $result->index + 1 && $end && $full && $correct) {//if all string match with regex.
            $result->full = true;
        } else {
            $result->full = false;
        }
        if ($result->full || $maybeend || $end) {//if string must be end on end of matching substring.
            $result->next = 0;
        //determine next character, which will be correct and increment lenght of matching substring.
        } elseif ($full && $index-2 < $maxindex) {//if assert not border next character
            reset($this->finiteautomates[$assertnumber][$currentstate]->passages);
            $key = key($this->finiteautomates[$assertnumber][$currentstate]->passages);
            if ($key > 0) {//if positive character class
                $result->next = $this->connection[$assertnumber][$key][0];
                if($key > DOT && next($this->finiteautomates[$assertnumber][$currentstate]->passages) !== false) {
                    $key = key($this->finiteautomates[$assertnumber][$currentstate]->passages);
                    $result->next = $this->connection[$assertnumber][$key][0];
                }
            } else {
                for($c = ' '; strpos($this->connection[$assertnumber][abs($key)], $c) !== false; $c++);
                $result->next = $c;
            }
        } else {
            $result->next = $next;
        }
        return $result;
    }
    
    
    
    static function push_unique(&$arr1, $arr2) {// to static
        foreach ($arr2 as $value) {
            if (!in_array($value, $arr1)) {
                $arr1[] = $value;
            }
        }
    }
    function find_asserts($node) {
        if ($node->type == NODE) {
            switch($node->subtype) {
                case NODE_ASSERTTF:
                    $this->roots[$node->number] = $node->firop;
                case NODE_ALT:
                case NODE_CONC:
                    $this->find_asserts($node->secop);
                case NODE_ITER:
                case NODE_QUESTQUANT:
                    $this->find_asserts($node->firop);
                    break;
            }
        }
    }
    function not_marked_state($built) {//передавать номер автомата, вместо массива с автоматом,  оставить динамической
        $notmarkedstate = false;
        $size = count($built);
        for ($i = 0; $i < $size && $notmarkedstate === false; $i++) {
            if (!$built[$i]->marked) {
                $notmarkedstate = $i;
            }
        }
        return $notmarkedstate;
    }
    static function is_include_characters($string1, $string2) {// to static
        $result = true;
        $size = strlen($string2);
        for ($i = 0; $i < $size && $result; $i++) {
            if (strpos($string1, $string2[$i]) === false) {
                $result = false;
            }
        }
        return $result;
    }
    function followposU($number, $fpmap, $passages, $index) {
        $str1 = $this->connection[$index][$number];//for this charclass will found equivalent numbers
        $equnum = array();
        foreach ($this->connection[$index] as $num => $cc) {//forming vector of equivalent numbers
            $str2 = $cc;
            if (reasc::is_include_characters($str1, $str2) && array_key_exists($num, $passages)) {//if charclass 1 and 2 equivalenta and number exist in passages
                array_push($equnum, $num);
            }
        }
        $followU = array();
        foreach ($equnum as $num) {//forming map of following numbers
            reasc::push_unique($followU, $fpmap[$num]);
        }
        return $followU;
    }
    function state($state, $index) {
        $passcount = count($state);
        $result = false;
        $fas = count($this->finiteautomates[$index]);
        for ($i=0; $i < $fas && $result === false; $i++) {
            $flag = true;
            if ($passcount != count($this->finiteautomates[$index][$i]->passages)) {
                $flag = false;
            }
            reset($state);
            reset($this->finiteautomates[$index][$i]->passages);
            for ($j=0; $flag && $j < $passcount; $j++) {
                if (key($state) != key($this->finiteautomates[$index][$i]->passages)) {
                    $flag = false;
                }
                next($state);
                next($this->finiteautomates[$index][$i]->passages);
            }
            if ($flag) {
                $result =$i;
            }
        }
        return $result;
    }
    static function copy_subtree($node) {
        $result = new node;
        $result->type = $node->type;
        $result->subtype = $node->subtype;
        $result->greed = $node->greed;
        $result->direction = $node->direction;
        $result->chars = $node->chars;
        if ($node->type == NODE) {
            $result->firop = reasc::copy_subtree($node->firop);
            if ($node->subtype == NODE_ALT || $node->subtype == NODE_CONC) {
                $result->secop = reasc::copy_subtree($node->secop);
            }
        }
        return $result;
    }
    static function convert_tree($node) {
    }
}
?>