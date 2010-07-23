<?php //$Id: reasc.php,put version put time dvkolesov Exp $
//+++создать кнопку хинт, демонстрирующюю что-нибудь.
//+++разобраться с делением на классы
//+++заменить табы пробелами
//+++расставить пробелы в усовиях и между ифом и скобками
//закоментировать по английски
//написать парсер
//6 function need convert to static

/*ПЛАН:
*Все свойства всех классов сделать private, класс тестировщик сделать для всех дружественным,
*класс reasc сделать дружественным для класса узла (node) и класса результата сравнения (compare_result)
*класс reasc снабдить методами для взаимодействия с внешней програмой, все остальные методы сделать private.
*
*+++Убрать $croot, $cconn, $finiteautomate, вместо них передавать в buildfa и другие функции использующие $this->c.*
*   номер ассерта(нуль для основного выражения) по которому строится автомат
*   и использовать сразу $finiteautomates[<полученный номер>] аналогично для $roots и $connection
*
*сделать статическими(т.к. они не используют ни свойства, ни динамические методы) следующии методы:
*is_include_characters, push_unique(переименовать в push_unique), followpos, lastpos, firstpos, nullable
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
    
    function name() {
        return 'node';
    }
}

class fas {//finite automate state
    var $asserts;
    var $passages;//хранит номера состояний к которым перейти
    var $marked;//if marked then true else false.
    
    function name() {
        return 'fas';
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
    
    function name() {
        return 'reasc';
    }
    function append_end($index) {
        $root = $this->roots[$index];
        $this->roots[$index] = &new node;
        $this->roots[$index]->type = NODE;
        $this->roots[$index]->subtype = NODE_CONC;
        $this->roots[$index]->firop = $root;
        $this->roots[$index]->secop = &new node;
        $this->roots[$index]->secop->type = LEAF;
        $this->roots[$index]->secop->subtype = LEAF_END;
        $this->roots[$index]->secop->direction = true;
    }
    /**
    *Function numerate leafs, nodes use for find leafs. Start on root and move to leafs.
    *Put pair of number=>character to $this->connection[$index][].
    *@param $node current node (or leaf) for numerating.
    */
    function numeration($node ,$index, &$maxid) {
        if ($node->type==NODE&&$node->subtype==NODE_ASSERTTF) {//assert node need number
            $node->number = ++$maxid + ASSERT;
        } else if ($node->type==NODE) {//not need number for not assert node, numerate operands
            $this->numeration($node->firop);
            if ($node->subtype==NODE_CONC||$node->subtype==NODE_ALT) {//concatenation and alternative have second operand, numerate it.
                $this->numeration($node->secop);
            }
        }
        if ($node->type==LEAF) {//leaf need number
            switch($node->subtype) {//number depend from subtype (charclass, metasymbol dot or end symbol)
                case LEAF_CHARCLASS://normal number for charclass
                    $node->number = ++$maxid;
                    $this->connection[$index][$maxid] = $node->chars;
                    break;
                case LEAF_END://STREND number for end leaf
                    $node->number = STREND;
                    break;
                case LEAF_METASYMBOLDOT://normal + DOT for dot leaf
                    $node->number = ++$maxid + DOT;
                    $this->connection[$index][$maxid + DOT] = $node->chars;
                    break;
            }
        }
    }
    /**
    *Function determine: subtree with root in this node can give empty word or not.
    *@param node - node fo analyze
    *@return true if can give empty word, else false
    */
    function nullable($node) {//to static
        $result = false;
        if ($node->type == NODE) {
            switch($node->subtype) {
                case NODE_ALT://alternative can give empty word if one operand can.
                    $result = ($this->nullable($node->firop) || $this->nullable($node->secop));
                    break;
                case NODE_CONC://concatenation can give empty word if both operands can.
                    $result = ($this->nullable($node->firop) && $this->nullable($node->secop));
                    $this->nullable($node->secop);
                    break;
                case NODE_ITER://iteration and question quantificator can give empty word without dependence from operand.
                case NODE_QUESTQUANT:
                    $result = true;
                    $this->nullable($node->firop);
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
    function firstpos($node) {//to static
        if ($node->type == NODE) {
            switch($node->subtype) {
                case NODE_ALT:
                    $result = array_merge($this->firstpos($node->firop), $this->firstpos($node->secop));
                    break;
                case NODE_CONC:
                    $result = $this->firstpos($node->firop);
                    if ($node->firop->nullable) {
                        $result = array_merge($result, $this->firstpos($node->secop));
                    } else {
                        $this->firstpos($node->secop);
                    }
                    break;
                case NODE_QUESTQUANT:
                case NODE_ITER:
                    $result = $this->firstpos($node->firop);
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
    function lastpos($node) {//to static
        if ($node->type == NODE) {
            switch($node->subtype) {
                case NODE_ALT:
                    $result = array_merge($this->lastpos($node->firop), $this->lastpos($node->secop));
                    break;
                case NODE_CONC:
                    $result = $this->lastpos($node->secop);
                    if ($node->secop->nullable) {
                        $result = array_merge($this->lastpos($node->firop), $result);
                    } else {
                        $this->lastpos($node->firop);
                    }
                    break;
                case NODE_ITER:
                case NODE_QUESTQUANT:
                    $result = $this->lastpos($node->firop);
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
    function followpos($node, &$fpmap) {//to static
        if ($node->type == NODE) {
            switch($node->subtype) {
                case NODE_CONC:
                    $this->followpos($node->firop, $fpmap);
                    $this->followpos($node->secop, $fpmap);
                    foreach ($node->firop->lastpos as $key) {
                        $this->push_unique($fpmap[$key], $node->secop->firstpos);
                    }
                    break;
                case NODE_ITER:
                    $this->followpos($node->firop, $fpmap);
                    foreach ($node->firop->lastpos as $key) {
                        $this->push_unique($fpmap[$key], $node->firop->firstpos);
                    }
                    break;
                case NODE_ALT:
                    $this->followpos($node->secop, $fpmap);
                case NODE_QUESTQUANT:
                    $this->followpos($node->firop, $fpmap);
                    break;
            }
        }
    }
    function buildfa($index) {//Начальное состояние ДКА сохраняется в поле finiteautomates[0][0]
                        //oстальные состояния в прочих эл-тах этого массива,finiteautomates[!=0] - asserts' fa
        $maxnum = 0;
        $this->finiteautomates[$index][0] = new fas;
        $this->numeration($this->roots[$index], $index, $maxnum);
        $this->nullable($this->roots[$index]);
        $this->firstpos($this->roots[$index]);
        $this->lastpos($this->roots[$index]);
        $this->followpos($this->roots[$index], $map);
        $this->find_asserts($this->roots[$index]);
        foreach ($this->roots[$index]->firstpos as $value) {
            $this->finiteautomates[$index][0]->passages[$value] = -2;
        }
        $this->finiteautomates[$index][0]->marked = false;
        while ($this->not_marked_state($this->finiteautomates[$index]) !== false) {
            $currentstate = $this->not_marked_state($this->finiteautomates[$index]);
            $this->finiteautomates[$index][$currentstate]->marked = true;
            foreach ($this->finiteautomates[$index][$currentstate]->passages as $num => $passage) {
                $newstate = new fas;
                $fpU = $this->followposU($num, $map, $this->finiteautomates[$index][$currentstate]->passages);
                foreach ($fpU as $follow) {
                    if ($follow<ASSERT) {
                        $newstate->passages[$follow] = -2;
                    } else {
                        $this->finiteautomates[$index][$currentstate]->asserts[] = $follow;
                    }
                }
                if ($num!=STREND) {
                    if ($this->state($newstate->passages) === false && count($newstate->passages) != 0) {
                        array_push($this->finiteautomates[$index], $newstate);
                        end($this->finiteautomates[$index]);
                        $this->finiteautomates[$index][$currentstate]->passages[$num] = key($this->finiteautomates[$index]);
                    } else {
                        $this->finiteautomates[$index][$currentstate]->passages[$num] = $this->state($newstate->passages);
                    }
                } else {
                    $this->finiteautomates[$index][$currentstate]->passages[$num] = -1;
                }
            }
        }
    }
    function compare($string, $assertnumber) {//if main regex then assertnumber is 0
        $result = new compare_result;
        return $result;
    }
    function push_unique(&$arr1, $arr2) {// to static
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
                    $this->roots[$node->number] = $node;
                    break;
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
    function is_include_characters($string1, $string2) {// to static
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
        foreach (connection[$index] as $num => $cc) {//forming vector of equivalent numbers
            $str2 = $cc;
            if ($this->is_include_characters($str1, $str2) && array_key_exists($num, $passages)) {//if charclass 1 and 2 equivalenta and number exist in passages
                array_push($equnum, $num);
            }
        }
        $followU = array();
        foreach ($equnum as $num) {//forming map of following numbers
            $this->push_unique($followU, $fpmap[$num]);
        }
        return $followU;
    }
    function state($state) {
        $passcount = count($state);
        $result = false;
        $fasize = count($this->finiteautomates[$index]);
        for ($i=0; $i < $fasize && $result === false; $i++) {
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
}
?>