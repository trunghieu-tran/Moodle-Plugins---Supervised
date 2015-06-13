<?php
define('NO_OFFSET', -2147483647);
define('DEBUG', 0);

class LemonStateNode
{
    public $key;
    public $data;
    public $from = 0;
    public $next = 0;
}


/**
 * The state of the yy_action table under construction is an instance of
 * the following structure
 */
class LemonActtab
{
    public $nAction = 0;                 /* Number of used slots in aAction[] */
    public $aAction =                  /* The yy_action[] table under construction */
        array(array(
            'lookahead' => -1,             /* Value of the lookahead token */
            'action' => -1                /* Action to take on the given lookahead */
        ));
    public $aLookahead =               /* A single new transaction set */
        array(array(
            'lookahead' => 0,             /* Value of the lookahead token */
            'action' => 0                /* Action to take on the given lookahead */
        ));
    public $mnLookahead = 0;             /* Minimum aLookahead[].lookahead */
    public $mnAction = 0;                /* Action associated with mnLookahead */
    public $mxLookahead = 0;             /* Maximum aLookahead[].lookahead */
    public $nLookahead = 0;              /* Used slots in aLookahead[] */

    /**
     * Add a new action to the current transaction set
     * @param int
     * @param int
     */
    function acttab_action($lookahead, $action)
    {
        if ($this->nLookahead === 0) {
            $this->aLookahead = array();
            $this->mxLookahead = $lookahead;
            $this->mnLookahead = $lookahead;
            $this->mnAction = $action;
        } else {
            if ($this->mxLookahead < $lookahead) {
                $this->mxLookahead = $lookahead;
            }
            if ($this->mnLookahead > $lookahead) {
                $this->mnLookahead = $lookahead;
                $this->mnAction = $action;
            }
        }
        $this->aLookahead[$this->nLookahead] = array(
            'lookahead' => $lookahead,
            'action' => $action);
        $this->nLookahead++;
    }

    /**
     * Add the transaction set built up with prior calls to acttab_action()
     * into the current action table.  Then reset the transaction set back
     * to an empty set in preparation for a new round of acttab_action() calls.
     *
     * Return the offset into the action table of the new transaction.
     */
    function acttab_insert()
    {
        if ($this->nLookahead <= 0) {
            throw new Exception('nLookahead is not set up?');
        }

        /* Scan the existing action table looking for an offset where we can
        ** insert the current transaction set.  Fall out of the loop when that
        ** offset is found.  In the worst case, we fall out of the loop when
        ** i reaches $this->nAction, which means we append the new transaction set.
        **
        ** i is the index in $this->aAction[] where $this->mnLookahead is inserted.
        */
        for ($i = 0; $i < $this->nAction + $this->mnLookahead; $i++) {
            if (!isset($this->aAction[$i])) {
                $this->aAction[$i] = array(
                    'lookahead' => -1,
                    'action' => -1,
                );
            }
            if ($this->aAction[$i]['lookahead'] < 0) {
                for ($j = 0; $j < $this->nLookahead; $j++) {
                    if (!isset($this->aLookahead[$j])) {
                        $this->aLookahead[$j] = array(
                            'lookahead' => 0,
                            'action' => 0,
                        );
                    }
                    $k = $this->aLookahead[$j]['lookahead'] -
                        $this->mnLookahead + $i;
                    if ($k < 0) {
                        break;
                    }
                    if (!isset($this->aAction[$k])) {
                        $this->aAction[$k] = array(
                            'lookahead' => -1,
                            'action' => -1,
                        );
                    }
                    if ($this->aAction[$k]['lookahead'] >= 0) {
                        break;
                    }
                }
                if ($j < $this->nLookahead ) {
                    continue;
                }
                for ($j = 0; $j < $this->nAction; $j++) {
                    if (!isset($this->aAction[$j])) {
                        $this->aAction[$j] = array(
                            'lookahead' => -1,
                            'action' => -1,
                        );
                    }
                    if ($this->aAction[$j]['lookahead'] == $j +
                          $this->mnLookahead - $i) {
                        break;
                    }
                }
                if ($j == $this->nAction) {
                    break;  /* Fits in empty slots */
                }
            } elseif ($this->aAction[$i]['lookahead'] == $this->mnLookahead) {
                if ($this->aAction[$i]['action'] != $this->mnAction) {
                    continue;
                }
                for ($j = 0; $j < $this->nLookahead; $j++) {
                    $k = $this->aLookahead[$j]['lookahead'] -
                        $this->mnLookahead + $i;
                    if ($k < 0 || $k >= $this->nAction) {
                        break;
                    }
                    if (!isset($this->aAction[$k])) {
                        $this->aAction[$k] = array(
                            'lookahead' => -1,
                            'action' => -1,
                        );
                    }
                    if ($this->aLookahead[$j]['lookahead'] !=
                          $this->aAction[$k]['lookahead']) {
                        break;
                    }
                    if ($this->aLookahead[$j]['action'] !=
                          $this->aAction[$k]['action']) {
                        break;
                    }
                }
                if ($j < $this->nLookahead) {
                    continue;
                }
                $n = 0;
                for ($j = 0; $j < $this->nAction; $j++) {
                    if (!isset($this->aAction[$j])) {
                        $this->aAction[$j] = array(
                            'lookahead' => -1,
                            'action' => -1,
                        );
                    }
                    if ($this->aAction[$j]['lookahead'] < 0) {
                        continue;
                    }
                    if ($this->aAction[$j]['lookahead'] == $j +
                          $this->mnLookahead - $i) {
                        $n++;
                    }
                }
                if ($n == $this->nLookahead) {
                    break;  /* Same as a prior transaction set */
                }
            }
        }
        /* Insert transaction set at index i. */
        for ($j = 0; $j < $this->nLookahead; $j++) {
            if (!isset($this->aLookahead[$j])) {
                $this->aLookahead[$j] = array(
                    'lookahead' => 0,
                    'action' => 0,
                );
            }
            $k = $this->aLookahead[$j]['lookahead'] - $this->mnLookahead + $i;
            $this->aAction[$k] = $this->aLookahead[$j];
            if ($k >= $this->nAction) {
                $this->nAction = $k + 1;
            }
        }
        $this->nLookahead = 0;
        $this->aLookahead = array();

        /* Return the offset that is added to the lookahead in order to get the
        ** index into yy_action of the action */
        return $i - $this->mnLookahead;
    }
}

/* Symbols (terminals and nonterminals) of the grammar are stored
** in the following: */
class LemonSymbol
{
    const TERMINAL = 1;
    const NONTERMINAL = 2;
    const MULTITERMINAL = 3;

    const LEFT = 1;
    const RIGHT = 2;
    const NONE = 3;
    const UNK = 4;
    public $name;          /* Name of the symbol */
    public $index;         /* Index number for this symbol */
  /* enum {
    TERMINAL,
    NONTERMINAL,
    MULTITERMINAL
  } */
    public $type;          /* Symbols are all either TERMINALS or NTs */
    /**
     * @var LemonRule
     */
    public $rule; /* Linked list of rules of this (if an NT) */
    /**
     * @var LemonSymbol
     */
    public $fallback;      /* fallback token in case this token doesn't parse */
    public $prec;          /* Precedence if defined (-1 otherwise) */
  /* enum e_assoc {
    LEFT,
    RIGHT,
    NONE,
    UNK
  } */
    public $assoc;         /* Associativity if predecence is defined */
    public $firstset;      /* First-set for all rules of this symbol */
    /**
     * @var boolean
     */
    public $lambda;        /* True if NT and can generate an empty string */
    public $destructor = 0;    /* Code which executes whenever this symbol is
                           ** popped from the stack during error processing */
    public $destructorln;  /* Line number of destructor code */
    public $datatype;      /* The data type of information held by this
                           ** object. Only used if type==NONTERMINAL */
    public $dtnum;         /* The data type number.  In the parser, the value
                           ** stack is a union.  The .yy%d element of this
                           ** union is the correct data type for this object */
    /* The following fields are used by MULTITERMINALs only */
    public $nsubsym;           /* Number of constituent symbols in the MULTI */
    /**
     * @var array an array of {@link LemonSymbol} objects
     */
    public $subsym = array();  /* Array of constituent symbols */
    private static $symbol_table = array();
    /**
     * Return a pointer to the (terminal or nonterminal) symbol "x".
     * Create a new symbol if this is the first time "x" has been seen.
     * (this is a singleton)
     * @param string
     * @return LemonSymbol
     */
    public static function Symbol_new($x)
    {
        if (isset(self::$symbol_table[$x])) {
            return self::$symbol_table[$x];
        }
        $sp = new LemonSymbol;
        $sp->name = $x;
        $sp->type = preg_match('/[A-Z]/', $x[0]) ? self::TERMINAL : self::NONTERMINAL;
        $sp->rule = 0;
        $sp->fallback = 0;
        $sp->prec = -1;
        $sp->assoc = self::UNK;
        $sp->firstset = array();
        $sp->lambda = false;
        $sp->destructor = 0;
        $sp->datatype = 0;
        self::$symbol_table[$sp->name] = $sp;
        return $sp;
    }

    /**
     * Return the number of unique symbols
     * @return int
     */
    public static function Symbol_count()
    {
        return count(self::$symbol_table);
    }

    public static function Symbol_arrayof()
    {
        return array_values(self::$symbol_table);
    }

    public static function Symbol_find($x)
    {
        if (isset(self::$symbol_table[$x])) {
            return self::$symbol_table[$x];
        }
        return 0;
    }

    /**
     * Sort function helper for symbols
     *
     * Symbols that begin with upper case letters (terminals or tokens)
     * must sort before symbols that begin with lower case letters
     * (non-terminals).  Other than that, the order does not matter.
     *
     * We find experimentally that leaving the symbols in their original
     * order (the order they appeared in the grammar file) gives the
     * smallest parser tables in SQLite.
     * @param LemonSymbol
     * @param LemonSymbol
     */
    public static function sortSymbols($a, $b)
    {
        $i1 = $a->index + 10000000*(ord($a->name[0]) > ord('Z'));
        $i2 = $b->index + 10000000*(ord($b->name[0]) > ord('Z'));
        return $i1 - $i2;
    }

    /**
     * Return true if two symbols are the same.
     */
    public static function same_symbol(LemonSymbol $a, LemonSymbol $b)
    {
        if ($a === $b) return 1;
        if ($a->type != self::MULTITERMINAL) return 0;
        if ($b->type != self::MULTITERMINAL) return 0;
        if ($a->nsubsym != $b->nsubsym) return 0;
        for ($i = 0; $i < $a->nsubsym; $i++) {
            if ($a->subsym[$i] != $b->subsym[$i]) return 0;
        }
        return 1;
    }
}

/* Each production rule in the grammar is stored in the following
** structure.  */
class LemonRule {
    /**
     * @var array an array of {@link LemonSymbol} objects
     */
    public $lhs;      /* Left-hand side of the rule */
    public $lhsalias = array();          /* Alias for the LHS (NULL if none) */
    public $ruleline;            /* Line number for the rule */
    public $nrhs;                /* Number of RHS symbols */
    /**
     * @var array an array of {@link LemonSymbol} objects
     */
    public $rhs;     /* The RHS symbols */
    public $rhsalias = array();         /* An alias for each RHS symbol (NULL if none) */
    public $line;                /* Line number at which code begins */
    public $code;              /* The code executed when this rule is reduced */
    /**
     * @var LemonSymbol
     */
    public $precsym;  /* Precedence symbol for this rule */
    public $index;               /* An index number for this rule */
    public $canReduce;       /* True if this rule is ever reduced */
    /**
     * @var LemonRule
     */
    public $nextlhs;    /* Next rule with the same LHS */
    /**
     * @var LemonRule
     */
    public $next;       /* Next rule in the global list */
}

/* A configuration is a production rule of the grammar together with
** a mark (dot) showing how much of that rule has been processed so far.
** Configurations also contain a follow-set which is a list of terminal
** symbols which are allowed to immediately follow the end of the rule.
** Every configuration is recorded as an instance of the following: */
class LemonConfig {
    const COMPLETE = 1;
    const INCOMPLETE = 2;
    /**
     * @var LemonRule
     */
    public $rp;         /* The rule upon which the configuration is based */
    public $dot;                 /* The parse point */
    public $fws;               /* Follow-set for this configuration only */
    /**
     * @var LemonPlink
     */
    public $fplp;      /* Follow-set forward propagation links */
    /**
     * @var LemonPlink
     */
    public $bplp;      /* Follow-set backwards propagation links */
    /**
     * @var LemonState
     */
    public $stp;       /* Pointer to state which contains this */
  /* enum {
    COMPLETE,              /* The status is used during followset and
    INCOMPLETE             /*    shift computations
  } */
    public $status;
    /**
     * Index of next LemonConfig object
     * @var int
     */
    public $next;     /* Next configuration in the state */
    /**
     * Index of the next basis configuration LemonConfig object
     * @var int
     */
    public $bp;       /* The next basis configuration */

    /**
     * @var LemonConfig
     */
    static public $current;      /* Top of list of configs */
    /**
     * @var LemonConfig
     */
    static public $currentend;      /* Last on list of configs */

    /**
     * @var LemonConfig
     */
    static public $basis;      /* Top of list of basis configs */
    /**
     * @var LemonConfig
     */
    static public $basisend;      /* Last on list of basis configs */

    static public $x4a = array();

    /**
     * Return a pointer to a new configuration
     * @return LemonConfig
     */
    private static function newconfig()
    {
        return new LemonConfig;
    }

    static function Configshow(LemonConfig $cfp)
    {
        $fp = fopen('php://output', 'w');
        while ($cfp) {
            if ($cfp->dot == $cfp->rp->nrhs) {
                $buf = sprintf('(%d)', $cfp->rp->index);
                fprintf($fp, '    %5s ', $buf);
            } else {
                fwrite($fp,'          ');
            }
            $cfp->ConfigPrint($fp);
            fwrite($fp, "\n");
            if (0) {
                //SetPrint(fp,cfp->fws,$this);
                //PlinkPrint(fp,cfp->fplp,"To  ");
                //PlinkPrint(fp,cfp->bplp,"From");
            }
            $cfp = $cfp->next;
        }
        fwrite($fp, "\n");
        fclose($fp);
    }

    /**
     * Initialized the configuration list builder
     */
    static function Configlist_init()
    {
        self::$current = 0;
        self::$currentend = &self::$current;
        self::$basis = 0;
        self::$basisend = &self::$basis;
        self::$x4a = array();
    }

    /**
     * Remove all data from the table.  Pass each data to the function "f"
     * as it is removed.  ("f" may be null to avoid this step.)
     */
    static function Configtable_reset($f)
    {
        self::$current = 0;
        self::$currentend = &self::$current;
        self::$basis = 0;
        self::$basisend = &self::$basis;
        self::Configtable_clear(0);
    }

    /**
     * Remove all data from the table.  Pass each data to the function "f"
     * as it is removed.  ("f" may be null to avoid this step.)
     */
    static function Configtable_clear($f)
    {
        if (!count(self::$x4a)) {
            return;
        }
        if ($f) {
            for ($i = 0; $i < count(self::$x4a); $i++) {
                call_user_func($f, self::$x4a[$i]->data);
            }
        }
        self::$x4a = array();
    }

    /**
     * Initialized the configuration list builder
     */
    static function Configlist_reset()
    {
        self::Configtable_clear(0);
    }

    /**
     * Add another configuration to the configuration list
     * @param LemonRule the rule
     * @param int Index into the RHS of the rule where the dot goes
     * @return LemonConfig
     */
    static function Configlist_add($rp, $dot)
    {
        $model = new LemonConfig;
        $model->rp = $rp;
        $model->dot = $dot;
        $cfp = self::Configtable_find($model);
        if ($cfp === 0) {
            $cfp = self::newconfig();
            $cfp->rp = $rp;
            $cfp->dot = $dot;
            $cfp->fws = array();
            $cfp->stp = 0;
            $cfp->fplp = $cfp->bplp = 0;
            $cfp->next = 0;
            $cfp->bp = 0;
            self::$currentend = $cfp;
            self::$currentend = &$cfp->next;
            self::Configtable_insert($cfp);
        }
        return $cfp;
    }

    /**
     * Add a basis configuration to the configuration list
     * @param LemonRule
     * @param int
     * @return LemonConfig
     */
    static function Configlist_addbasis($rp, $dot)
    {
        $model = new LemonConfig;
        $model->rp = $rp;
        $model->dot = $dot;
        $cfp = self::Configtable_find($model);
        if ($cfp === 0) {
            $cfp = self::newconfig();
            $cfp->rp = $rp;
            $cfp->dot = $dot;
            $cfp->fws = array();
            $cfp->stp = 0;
            $cfp->fplp = $cfp->bplp = 0;
            $cfp->next = 0;
            $cfp->bp = 0;
            self::$currentend = $cfp;
            self::$currentend = &$cfp->next;
            self::$basisend = $cfp;
            self::$basisend = &$cfp->bp;
            self::Configtable_insert($cfp);
        }
        return $cfp;
    }

    /* Compute the closure of the configuration list */
    static function Configlist_closure(LemonData $lemp)
    {
        for ($cfp = self::$current; $cfp; $cfp = $cfp->next) {
            $rp = $cfp->rp;
            $dot = $cfp->dot;
            if ($dot >= $rp->nrhs) {
                continue;
            }
            $sp = $rp->rhs[$dot];
            if ($sp->type == LemonSymbol::NONTERMINAL) {
                if ($sp->rule === 0 && $sp !== $lemp->errsym) {
                    Lemon::ErrorMsg($lemp->filename, $rp->line,
                        "Nonterminal \"%s\" has no rules.", $sp->name);
                    $lemp->errorcnt++;
                }
                for ($newrp = $sp->rule; $newrp; $newrp = $newrp->nextlhs) {
                    $newcfp = self::Configlist_add($newrp, 0);
                    for ($i = $dot + 1; $i < $rp->nrhs; $i++) {
                        $xsp = $rp->rhs[$i];
                        if ($xsp->type == LemonSymbol::TERMINAL) {
                            $newcfp->fws[$xsp->index] = 1;
                            break;
                        } elseif ($xsp->type == LemonSymbol::MULTITERMINAL) {
                            for ($k = 0; $k < $xsp->nsubsym; $k++) {
                                $newcfp->fws[$xsp->subsym[k]->index] = 1;
                            }
                            break;
                        } else {
                            $a = array_diff_key($xsp->firstset, $newcfp->fws);
                            $newcfp->fws += $a;
                            if ($xsp->lambda === false) {
                                break;
                            }
                        }
                    }
                    if ($i == $rp->nrhs) {
                        LemonPlink::Plink_add($cfp->fplp, $newcfp);
                    }
                }
            }
        }
    }

    /**
     * Sort the configuration list
     */
    static function Configlist_sort()
    {
        $a = 0;
        //self::Configshow(self::$current);
        self::$current = Lemon::msort(self::$current,'next', array('LemonConfig', 'Configcmp'));
        //self::Configshow(self::$current);
        self::$currentend = &$a;
        self::$currentend = 0;
    }

    /**
     * Sort the configuration list
     */
    static function Configlist_sortbasis()
    {
        $a = 0;
        self::$basis = Lemon::msort(self::$current,'bp', array('LemonConfig', 'Configcmp'));
        self::$basisend = &$a;
        self::$basisend = 0;
    }

    /** Return a pointer to the head of the configuration list and
     * reset the list
     * @return LemonConfig
     */
    static function Configlist_return()
    {
        $old = self::$current;
        self::$current = 0;
        self::$currentend = &self::$current;
        return $old;
    }

    /** Return a pointer to the head of the basis list and
     * reset the list
     * @return LemonConfig
     */
    static function Configlist_basis()
    {
        $old = self::$basis;
        self::$basis = 0;
        self::$basisend = &self::$basis;
        return $old;
    }

    /**
     * Free all elements of the given configuration list
     * @param LemonConfig
     */
    static function Configlist_eat($cfp)
    {
        for(; $cfp; $cfp = $nextcfp){
            $nextcfp = $cfp->next;
            if ($cfp->fplp !=0) {
                throw new Exception('fplp of configuration non-zero?');
            }
            if ($cfp->bplp !=0) {
                throw new Exception('bplp of configuration non-zero?');
            }
            if ($cfp->fws) {
                $cfp->fws = array();
            }
        }
    }

    static function Configcmp($a, $b)
    {
        $x = $a->rp->index - $b->rp->index;
        if (!$x) {
            $x = $a->dot - $b->dot;
        }
        return $x;
    }

    function ConfigPrint($fp)
    {
        $rp = $this->rp;
        fprintf($fp, "%s ::=", $rp->lhs->name);
        for ($i = 0; $i <= $rp->nrhs; $i++) {
            if ($i === $this->dot) {
                fwrite($fp,' *');
            }
            if ($i === $rp->nrhs) {
                break;
            }
            $sp = $rp->rhs[$i];
            fprintf($fp,' %s', $sp->name);
            if ($sp->type == LemonSymbol::MULTITERMINAL) {
                for ($j = 1; $j < $sp->nsubsym; $j++) {
                    fprintf($fp, '|%s', $sp->subsym[$j]->name);
                }
            }
        }
    }

    /**
     * Hash a configuration
     */
    private static function confighash(LemonConfig $a)
    {
        $h = 0;
        $h = $h * 571 + $a->rp->index * 37 + $a->dot;
        return $h;
    }

    /**
     * Insert a new record into the array.  Return TRUE if successful.
     * Prior data with the same key is NOT overwritten
     */
    static function Configtable_insert(LemonConfig $data)
    {
//typedef struct s_x4node {
//  struct config *data;                  /* The data */
//  struct s_x4node *next;   /* Next entry with the same hash */
//  struct s_x4node **from;  /* Previous link */
//} x4node;
//
//        x4node *np;
//        int h;
//        int ph;

        $h = self::confighash($data);
        if (isset(self::$x4a[$h])) {
            $np = self::$x4a[$h];
        } else {
            $np = 0;
        }
        while ($np) {
            if (self::Configcmp($np->data, $data) == 0) {
                /* An existing entry with the same key is found. */
                /* Fail because overwrite is not allows. */
                return 0;
            }
            $np = $np->next;
        }
        /* Insert the new data */
        $np = array('data' => $data, 'next' => 0, 'from' => 0);
        $np = new LemonStateNode;
        $np->data = $data;
        // as you might notice, "from" always points to itself.
        // this bug is in the original lemon parser, but from is never actually accessed
        // so it don't much matter now, do it?
        if (isset(self::$x4a[$h])) {
            self::$x4a[$h]->from = $np->next;
            $np->next = self::$x4a[$h];
        }
        $np->from = $np;
        self::$x4a[$h] = $np;
        return 1;
    }

    /**
     * Return a pointer to data assigned to the given key.  Return NULL
     * if no such key.
     * @return LemonConfig|0
     */
    static function Configtable_find(LemonConfig $key)
    {
        $h = self::confighash($key);
        if (!isset(self::$x4a[$h])) {
            return 0;
        }
        $np = self::$x4a[$h];
        while ($np) {
            if (self::Configcmp($np->data, $key) == 0) {
                break;
            }
            $np = $np->next;
        }
        return $np ? $np->data : 0;
    }
}

/* Every shift or reduce operation is stored as one of the following */
class LemonAction {
    const SHIFT = 1, ACCEPT = 2, REDUCE = 3, ERROR = 4, CONFLICT = 5, SH_RESOLVED = 6,
          RD_RESOLVED = 7, NOT_USED = 8;
    /**
     * @var LemonSymbol
     */
    public $sp;       /* The look-ahead symbol */
  /* enum e_action {
    SHIFT,
    ACCEPT,
    REDUCE,
    ERROR,
    CONFLICT,                /* Was a reduce, but part of a conflict
    SH_RESOLVED,             /* Was a shift.  Precedence resolved conflict
    RD_RESOLVED,             /* Was reduce.  Precedence resolved conflict
    NOT_USED                 /* Deleted by compression
  } */
    public $type;
  /* union {
    struct state *stp;     /* The new state, if a shift
    struct rule *rp;       /* The rule, if a reduce
  } */
    public $x = array('stp' => null, 'rp' => null);
    /**
     * @var LemonAction
     */
    public $next;     /* Next action for this state */
    /**
     * @var LemonAction
     */
    public $collide;  /* Next action with the same hash */

    /* Compare two actions */
    static function actioncmp(LemonAction $ap1, LemonAction $ap2)
    {
        $rc = $ap1->sp->index - $ap2->sp->index;
        if ($rc === 0) {
            $rc = $ap1->type - $ap2->type;
        }
        if ($rc === 0) {
            if ($ap1->type != LemonAction::REDUCE &&
                  $ap1->type != LemonAction::RD_RESOLVED &&
                  $ap1->type != LemonAction::CONFLICT) {
                throw new Exception('action has not been processed: ' .
                    $ap1->sp->name);
            }
            if ($ap2->type != LemonAction::REDUCE &&
                  $ap2->type != LemonAction::RD_RESOLVED &&
                  $ap2->type != LemonAction::CONFLICT) {
                throw new Exception('action has not been processed: ' .
                    $ap2->sp->name);
            }
            $rc = $ap1->x->index - $ap2->x->index;
        }
        return $rc;
    }

    /**
     * create linked list of LemonActions
     *
     * @param LemonAction|null
     * @param int one of the constants from LemonAction
     * @param LemonSymbol
     * @param LemonSymbol|LemonRule
     */
    static function Action_add(&$app, $type, LemonSymbol $sp, $arg)
    {
        $new = new LemonAction;
        $new->next = $app;
        $app = $new;
        $new->type = $type;
        $new->sp = $sp;
        $new->x = $arg;
    }

    /* Sort parser actions */
    static function Action_sort(LemonAction $ap)
    {
        $ap = Lemon::msort($ap, 'next', array('LemonAction', 'actioncmp'));
        return $ap;
    }

    /**
     * Print an action to the given file descriptor.  Return FALSE if
     * nothing was actually printed.
     */
    function PrintAction($fp, $indent)
    {
        $result = 1;
        switch ($this->type)
        {
            case self::SHIFT:
                fprintf($fp, "%${indent}s shift  %d", $this->sp->name, $this->x->statenum);
                break;
            case self::REDUCE:
                fprintf($fp, "%${indent}s reduce %d", $this->sp->name, $this->x->index);
                break;
            case self::ACCEPT:
                fprintf($fp, "%${indent}s accept", $this->sp->name);
                break;
            case self::ERROR:
                fprintf($fp, "%${indent}s error", $this->sp->name);
                break;
            case self::CONFLICT:
                fprintf($fp, "%${indent}s reduce %-3d ** Parsing conflict **", $this->sp->name, $this->x->index);
                break;
            case self::SH_RESOLVED:
            case self::RD_RESOLVED:
            case self::NOT_USED:
                $result = 0;
                break;
        }
        return $result;
    }
}

/* A followset propagation link indicates that the contents of one
** configuration followset should be propagated to another whenever
** the first changes. */
class LemonPlink {
    /**
     * @var LemonConfig
     */
    public $cfp;      /* The configuration to which linked */
    /**
     * @var LemonPlink
     */
    public $next = 0;      /* The next propagate link */

    /**
     * Add a plink to a plink list
     * @param LemonPlink|null
     * @param LemonConfig
     */
    static function Plink_add(&$plpp, LemonConfig $cfp)
    {
        $new = new LemonPlink;
        $new->next = $plpp;
        $plpp = $new;
        $new->cfp = $cfp;
    }

    /* Transfer every plink on the list "from" to the list "to" */
    static function Plink_copy(LemonPlink &$to, LemonPlink $from)
    {
        while ($from) {
            $nextpl = $from->next;
            $from->next = $to;
            $to = $from;
            $from = $nextpl;
        }
    }

    /**
     * Delete every plink on the list
     * @param LemonPlink|0
     */
    static function Plink_delete($plp)
    {
        while ($plp) {
            $nextpl = $plp->next;
            $plp->next = 0;
            $plp = $nextpl;
        }
    }
}

/* Each state of the generated parser's finite state machine
** is encoded as an instance of the following structure. */
class LemonState {
    /**
     * @var LemonConfig
     */
    public $bp;       /* The basis configurations for this state */
    /**
     * @var LemonConfig
     */
    public $cfp;      /* All configurations in this set */
    public $statenum;            /* Sequencial number for this state */
    /**
     * @var LemonAction
     */
    public $ap;       /* Array of actions for this state */
    public $nTknAct, $nNtAct;     /* Number of actions on terminals and nonterminals */
    public $iTknOfst, $iNtOfst;   /* yy_action[] offset for terminals and nonterms */
    public $iDflt;               /* Default action */
    public static $x3a = array();
    public static $states = array();

    /**
     * Compare two states for sorting purposes.  The smaller state is the
     * one with the most non-terminal actions.  If they have the same number
     * of non-terminal actions, then the smaller is the one with the most
     * token actions.
     */
    static function stateResortCompare($a, $b)
    {
        $n = $b->nNtAct - $a->nNtAct;
        if ($n === 0) {
            $n = $b->nTknAct - $a->nTknAct;
        }
        return $n;
    }

    static function statecmp($a, $b)
    {
        for ($rc = 0; $rc == 0 && $a && $b;  $a = $a->bp, $b = $b->bp) {
            $rc = $a->rp->index - $b->rp->index;
            if ($rc === 0) {
                $rc = $a->dot - $b->dot;
            }
        }
        if ($rc == 0) {
            if ($a) {
                $rc = 1;
            }
            if ($b) {
                $rc = -1;
            }
        }
        return $rc;
    }

    /* Hash a state */
    private static function statehash(LemonConfig $a)
    {
        $h = 0;
        while ($a) {
            $h = $h * 571 + $a->rp->index * 37 + $a->dot;
            $a = $a->bp;
        }
        return (int) $h;
    }

    /**
     * Return a pointer to data assigned to the given key.  Return NULL
     * if no such key.
     * @param LemonConfig
     * @return null|LemonState
     */
    static function State_find(LemonConfig $key)
    {
        if (!count(self::$x3a)) {
            return 0;
        }
        $h = self::statehash($key);
        if (!isset(self::$x3a[$h])) {
            return 0;
        }
        $np = self::$x3a[$h];
        while ($np) {
            if (self::statecmp($np->key, $key) == 0) {
                break;
            }
            $np = $np->next;
        }
        return $np ? $np->data : 0;
    }

    /**
     * Insert a new record into the array.  Return TRUE if successful.
     * Prior data with the same key is NOT overwritten
     *
     * @param LemonState $state
     * @param LemonConfig $key
     * @return unknown
     */
    static function State_insert(LemonState $state, LemonConfig $key)
    {
        $h = self::statehash($key);
        if (isset(self::$x3a[$h])) {
            $np = self::$x3a[$h];
        } else {
            $np = 0;
        }
        while ($np) {
            if (self::statecmp($np->key, $key) == 0) {
                /* An existing entry with the same key is found. */
                /* Fail because overwrite is not allows. */
                return 0;
            }
            $np = $np->next;
        }
        /* Insert the new data */
        $np = new LemonStateNode;
        $np->key = $key;
        $np->data = $state;
        self::$states[] = $np;
        // the original lemon code sets the from link always to itself
        // setting up a faulty double-linked list
        // however, the from links are never used, so I suspect a copy/paste
        // error from a standard algorithm that was never caught
        if (isset(self::$x3a[$h])) {
            self::$x3a[$h]->from = $np; // lemon has $np->next here
        } else {
            self::$x3a[$h] = 0; // dummy to avoid notice
        }
        $np->next = self::$x3a[$h];
        self::$x3a[$h] = $np;
        $np->from = self::$x3a[$h];
        return 1;
    }

    static function State_arrayof()
    {
        return self::$states;
    }
}

/* The state vector for the entire parser generator is recorded as
** follows.  (LEMON uses no global variables and makes little use of
** static variables.  Fields in the following structure can be thought
** of as begin global variables in the program.) */
class LemonData {
    /**
     * @var array array of {@link LemonState} objects
     */
    public $sorted;   /* Table of states sorted by state number */
    /**
     * @var LemonRule
     */
    public $rule;       /* List of all rules */
    public $nstate;              /* Number of states */
    public $nrule;               /* Number of rules */
    public $nsymbol;             /* Number of terminal and nonterminal symbols */
    public $nterminal;           /* Number of terminal symbols */
    /**
     * @var array array of {@link LemonSymbol} objects
     */
    public $symbols = array(); /* Sorted array of pointers to symbols */
    public $errorcnt;            /* Number of errors */
    /**
     * @var LemonSymbol
     */
    public $errsym;   /* The error symbol */
    public $name;              /* Name of the generated parser */
    public $arg;               /* Declaration of the 3th argument to parser */
    public $tokentype;         /* Type of terminal symbols in the parser stack */
    public $vartype;           /* The default type of non-terminal symbols */
    public $start;             /* Name of the start symbol for the grammar */
    public $stacksize;         /* Size of the parser stack */
    public $include_code;           /* Code to put at the start of the parser file */
    public $includeln;          /* Line number for start of include code */
    public $include_classcode;   /* Code to put in the parser class */
    public $include_classln;     /* Line number for start of include code */
    public $declare_classcode;   /* any extends/implements code */
    public $declare_classln;     /* Line number for start of class declaration code */
    public $error;             /* Code to execute when an error is seen */
    public $errorln;            /* Line number for start of error code */
    public $overflow;          /* Code to execute on a stack overflow */
    public $overflowln;         /* Line number for start of overflow code */
    public $failure;           /* Code to execute on parser failure */
    public $failureln;          /* Line number for start of failure code */
    public $accept;            /* Code to execute when the parser excepts */
    public $acceptln;           /* Line number for the start of accept code */
    public $extracode;         /* Code appended to the generated file */
    public $extracodeln;        /* Line number for the start of the extra code */
    public $tokendest;         /* Code to execute to destroy token data */
    public $tokendestln;        /* Line number for token destroyer code */
    public $vardest;           /* Code for the default non-terminal destructor */
    public $vardestln;          /* Line number for default non-term destructor code*/
    public $filename;          /* Name of the input file */
    public $filenosuffix;   /* Name of the input file without its extension */
    public $outname;           /* Name of the current output file */
    public $tokenprefix;       /* A prefix added to token names in the .h file */
    public $nconflict;           /* Number of parsing conflicts */
    public $tablesize;           /* Size of the parse tables */
    public $basisflag;           /* Prpublic $only basis configurations */
    public $has_fallback;        /* True if any %fallback is seen in the grammer */
    public $argv0;             /* Name of the program */

    /* Find a precedence symbol of every rule in the grammar.
     *
     * Those rules which have a precedence symbol coded in the input
     * grammar using the "[symbol]" construct will already have the
     * rp->precsym field filled.  Other rules take as their precedence
     * symbol the first RHS symbol with a defined precedence.  If there
     * are not RHS symbols with a defined precedence, the precedence
     * symbol field is left blank.
     */
    function FindRulePrecedences()
    {
        for ($rp = $this->rule; $rp; $rp = $rp->next) {
            if ($rp->precsym === 0) {
                for ($i = 0; $i < $rp->nrhs && $rp->precsym === 0; $i++) {
                    $sp = $rp->rhs[$i];
                    if ($sp->type == LemonSymbol::MULTITERMINAL) {
                        for ($j = 0; $j < $sp->nsubsym; $j++) {
                            if ($sp->subsym[$j]->prec >= 0) {
                                $rp->precsym = $sp->subsym[$j];
                                break;
                            }
                        }
                    } elseif ($sp->prec >= 0) {
                        $rp->precsym = $rp->rhs[$i];
                    }
                }
            }
        }
    }

    /* Find all nonterminals which will generate the empty string.
     * Then go back and compute the first sets of every nonterminal.
     * The first set is the set of all terminal symbols which can begin
     * a string generated by that nonterminal.
     */
    function FindFirstSets()
    {
        for ($i = 0; $i < $this->nsymbol; $i++) {
            $this->symbols[$i]->lambda = false;
        }
        for($i = $this->nterminal; $i < $this->nsymbol; $i++) {
            $this->symbols[$i]->firstset = array();
        }

        /* First compute all lambdas */
        do{
            $progress = 0;
            for ($rp = $this->rule; $rp; $rp = $rp->next) {
                if ($rp->lhs->lambda) {
                    continue;
                }
                for ($i = 0; $i < $rp->nrhs; $i++) {
                    $sp = $rp->rhs[$i];
                    if ($sp->type != LemonSymbol::TERMINAL || $sp->lambda === false) {
                        break;
                    }
                }
                if ($i === $rp->nrhs) {
                    $rp->lhs->lambda = true;
                    $progress = 1;
                }
            }
        } while ($progress);

        /* Now compute all first sets */
        do {
            $progress = 0;
            for ($rp = $this->rule; $rp; $rp = $rp->next) {
                $s1 = $rp->lhs;
                for ($i = 0; $i < $rp->nrhs; $i++) {
                    $s2 = $rp->rhs[$i];
                    if ($s2->type == LemonSymbol::TERMINAL) {
                        //progress += SetAdd(s1->firstset,s2->index);
                        $progress += isset($s1->firstset[$s2->index]) ? 0 : 1;
                        $s1->firstset[$s2->index] = 1;
                        break;
                    } elseif ($s2->type == LemonSymbol::MULTITERMINAL) {
                        for ($j = 0; $j < $s2->nsubsym; $j++) {
                            //progress += SetAdd(s1->firstset,s2->subsym[j]->index);
                            $progress += isset($s1->firstset[$s2->subsym[$j]->index]) ? 0 : 1;
                            $s1->firstset[$s2->subsym[$j]->index] = 1;
                        }
                        break;
                    } elseif ($s1 === $s2) {
                        if ($s1->lambda === false) {
                            break;
                        }
                    } else {
                        //progress += SetUnion(s1->firstset,s2->firstset);
                        $test = array_diff_key($s2->firstset, $s1->firstset);
                        if (count($test)) {
                            $progress++;
                            $s1->firstset += $test;
                        }
                        if ($s2->lambda === false) {
                            break;
                        }
                    }
                }
            }
        } while ($progress);
    }

    /** Compute all LR(0) states for the grammar.  Links
     * are added to between some states so that the LR(1) follow sets
     * can be computed later.
     */
    function FindStates()
    {
        LemonConfig::Configlist_init();

        /* Find the start symbol */
        if ($this->start) {
            $sp = LemonSymbol::Symbol_find($this->start);
            if ($sp == 0) {
                Lemon::ErrorMsg($this->filename, 0,
                    "The specified start symbol \"%s\" is not " .
                    "in a nonterminal of the grammar.  \"%s\" will be used as the start " .
                    "symbol instead.", $this->start, $this->rule->lhs->name);
                $this->errorcnt++;
                $sp = $this->rule->lhs;
            }
        } else {
            $sp = $this->rule->lhs;
        }

        /* Make sure the start symbol doesn't occur on the right-hand side of
        ** any rule.  Report an error if it does.  (YACC would generate a new
        ** start symbol in this case.) */
        for ($rp = $this->rule; $rp; $rp = $rp->next) {
            for ($i = 0; $i < $rp->nrhs; $i++) {
                if ($rp->rhs[$i]->type == LemonSymbol::MULTITERMINAL) {
                    foreach ($rp->rhs[$i]->subsym as $subsp) {
                        if ($subsp === $sp) {
                            Lemon::ErrorMsg($this->filename, 0,
                                "The start symbol \"%s\" occurs on the " .
                                "right-hand side of a rule. This will result in a parser which " .
                                "does not work properly.", $sp->name);
                            $this->errorcnt++;
                        }
                    }
                } elseif ($rp->rhs[$i] === $sp) {
                    Lemon::ErrorMsg($this->filename, 0,
                        "The start symbol \"%s\" occurs on the " .
                        "right-hand side of a rule. This will result in a parser which " .
                        "does not work properly.", $sp->name);
                    $this->errorcnt++;
                }
            }
        }

        /* The basis configuration set for the first state
        ** is all rules which have the start symbol as their
        ** left-hand side */
        for ($rp = $sp->rule; $rp; $rp = $rp->nextlhs) {
            $newcfp = LemonConfig::Configlist_addbasis($rp, 0);
            $newcfp->fws[0] = 1;
        }

        /* Compute the first state.  All other states will be
        ** computed automatically during the computation of the first one.
        ** The returned pointer to the first state is not used. */
        $newstp = array();
        $newstp = $this->getstate();
        if (is_array($newstp)) {
            $this->buildshifts($newstp[0]); /* Recursively compute successor states */
        }
    }

    /**
     * @return LemonState
     */
    private function getstate()
    {
        /* Extract the sorted basis of the new state.  The basis was constructed
        ** by prior calls to "Configlist_addbasis()". */
        LemonConfig::Configlist_sortbasis();
        $bp = LemonConfig::Configlist_basis();

        /* Get a state with the same basis */
        $stp = LemonState::State_find($bp);
        if ($stp) {
            /* A state with the same basis already exists!  Copy all the follow-set
            ** propagation links from the state under construction into the
            ** preexisting state, then return a pointer to the preexisting state */
            for($x = $bp, $y = $stp->bp; $x && $y; $x = $x->bp, $y = $y->bp) {
                LemonPlink::Plink_copy($y->bplp, $x->bplp);
                LemonPlink::Plink_delete($x->fplp);
                $x->fplp = $x->bplp = 0;
            }
            $cfp = LemonConfig::Configlist_return();
            LemonConfig::Configlist_eat($cfp);
        } else {
            /* This really is a new state.  Construct all the details */
            LemonConfig::Configlist_closure($this);    /* Compute the configuration closure */
            LemonConfig::Configlist_sort();           /* Sort the configuration closure */
            $cfp = LemonConfig::Configlist_return();   /* Get a pointer to the config list */
            $stp = new LemonState;           /* A new state structure */
            $stp->bp = $bp;                /* Remember the configuration basis */
            $stp->cfp = $cfp;              /* Remember the configuration closure */
            $stp->statenum = $this->nstate++; /* Every state gets a sequence number */
            $stp->ap = 0;                 /* No actions, yet. */
            LemonState::State_insert($stp, $stp->bp);   /* Add to the state table */
            // this can't work, recursion is too deep, move it into FindStates()
            //$this->buildshifts($stp);       /* Recursively compute successor states */
            return array($stp);
        }
        return $stp;
    }

    /**
     * Construct all successor states to the given state.  A "successor"
     * state is any state which can be reached by a shift action.
     * @param LemonData
     * @param LemonState The state from which successors are computed
     */
    private function buildshifts(LemonState $stp)
    {
//    struct config *cfp;  /* For looping thru the config closure of "stp" */
//    struct config *bcfp; /* For the inner loop on config closure of "stp" */
//    struct config *new;  /* */
//    struct symbol *sp;   /* Symbol following the dot in configuration "cfp" */
//    struct symbol *bsp;  /* Symbol following the dot in configuration "bcfp" */
//    struct state *newstp; /* A pointer to a successor state */

        /* Each configuration becomes complete after it contibutes to a successor
        ** state.  Initially, all configurations are incomplete */
        $cfp = $stp->cfp;
        for ($cfp = $stp->cfp; $cfp; $cfp = $cfp->next) {
            $cfp->status = LemonConfig::INCOMPLETE;
        }

        /* Loop through all configurations of the state "stp" */
        for ($cfp = $stp->cfp; $cfp; $cfp = $cfp->next) {
            if ($cfp->status == LemonConfig::COMPLETE) {
                continue;    /* Already used by inner loop */
            }
            if ($cfp->dot >= $cfp->rp->nrhs) {
                continue;  /* Can't shift this config */
            }
            LemonConfig::Configlist_reset();                      /* Reset the new config set */
            $sp = $cfp->rp->rhs[$cfp->dot];             /* Symbol after the dot */

            /* For every configuration in the state "stp" which has the symbol "sp"
            ** following its dot, add the same configuration to the basis set under
            ** construction but with the dot shifted one symbol to the right. */
            $bcfp = $cfp;
            for ($bcfp = $cfp; $bcfp; $bcfp = $bcfp->next) {
                if ($bcfp->status == LemonConfig::COMPLETE) {
                    continue;    /* Already used */
                }
                if ($bcfp->dot >= $bcfp->rp->nrhs) {
                    continue; /* Can't shift this one */
                }
                $bsp = $bcfp->rp->rhs[$bcfp->dot];           /* Get symbol after dot */
                if (!LemonSymbol::same_symbol($bsp, $sp)) {
                    continue;      /* Must be same as for "cfp" */
                }
                $bcfp->status = LemonConfig::COMPLETE;             /* Mark this config as used */
                $new = LemonConfig::Configlist_addbasis($bcfp->rp, $bcfp->dot + 1);
                LemonPlink::Plink_add($new->bplp, $bcfp);
            }

            /* Get a pointer to the state described by the basis configuration set
            ** constructed in the preceding loop */
            $newstp = $this->getstate();
            if (is_array($newstp)) {
                $this->buildshifts($newstp[0]); /* Recursively compute successor states */
                $newstp = $newstp[0];
            }

            /* The state "newstp" is reached from the state "stp" by a shift action
            ** on the symbol "sp" */
            if ($sp->type == LemonSymbol::MULTITERMINAL) {
                for($i = 0; $i < $sp->nsubsym; $i++) {
                    LemonAction::Action_add($stp->ap, LemonAction::SHIFT, $sp->subsym[$i],
                                            $newstp);
                }
            } else {
                LemonAction::Action_add($stp->ap, LemonAction::SHIFT, $sp, $newstp);
            }
        }
    }

    /**
     * Construct the propagation links
     */
    function FindLinks()
    {
        /* Housekeeping detail:
        ** Add to every propagate link a pointer back to the state to
        ** which the link is attached. */
        foreach ($this->sorted as $info) {
            $info->key->stp = $info->data;
        }

        /* Convert all backlinks into forward links.  Only the forward
        ** links are used in the follow-set computation. */
        for ($i = 0; $i < $this->nstate; $i++) {
            $stp = $this->sorted[$i];
            for ($cfp = $stp->data->cfp; $cfp; $cfp = $cfp->next) {
                for ($plp = $cfp->bplp; $plp; $plp = $plp->next) {
                    $other = $plp->cfp;
                    LemonPlink::Plink_add($other->fplp, $cfp);
                }
            }
        }
    }

    /**
     * Compute the reduce actions, and resolve conflicts.
     */
    function FindActions()
    {
        /* Add all of the reduce actions
        ** A reduce action is added for each element of the followset of
        ** a configuration which has its dot at the extreme right.
        */
        for ($i = 0; $i < $this->nstate; $i++) {   /* Loop over all states */
            $stp = $this->sorted[$i]->data;
            for ($cfp = $stp->cfp; $cfp; $cfp = $cfp->next) {
                /* Loop over all configurations */
                if ($cfp->rp->nrhs == $cfp->dot) {        /* Is dot at extreme right? */
                    for ($j = 0; $j < $this->nterminal; $j++) {
                        if (isset($cfp->fws[$j])) {
                            /* Add a reduce action to the state "stp" which will reduce by the
                            ** rule "cfp->rp" if the lookahead symbol is "$this->symbols[j]" */
                            LemonAction::Action_add($stp->ap, LemonAction::REDUCE,
                                                    $this->symbols[$j], $cfp->rp);
                        }
                    }
                }
            }
        }

        /* Add the accepting token */
        if ($this->start instanceof LemonSymbol) {
            $sp = LemonSymbol::Symbol_find($this->start);
            if ($sp === 0) {
                $sp = $this->rule->lhs;
            }
        } else {
            $sp = $this->rule->lhs;
        }
        /* Add to the first state (which is always the starting state of the
        ** finite state machine) an action to ACCEPT if the lookahead is the
        ** start nonterminal.  */
        LemonAction::Action_add($this->sorted[0]->data->ap, LemonAction::ACCEPT, $sp, 0);

        /* Resolve conflicts */
        for ($i = 0; $i < $this->nstate; $i++) {
    //    struct action *ap, *nap;
    //    struct state *stp;
            $stp = $this->sorted[$i]->data;
            if (!$stp->ap) {
                throw new Exception('state has no actions associated');
            }
            $stp->ap = LemonAction::Action_sort($stp->ap);
            for ($ap = $stp->ap; $ap !== 0 && $ap->next !== 0; $ap = $ap->next) {
                for ($nap = $ap->next; $nap !== 0 && $nap->sp === $ap->sp ; $nap = $nap->next) {
                    /* The two actions "ap" and "nap" have the same lookahead.
                    ** Figure out which one should be used */
                    $this->nconflict += $this->resolve_conflict($ap, $nap, $this->errsym);
                }
            }
        }

        /* Report an error for each rule that can never be reduced. */
        for ($rp = $this->rule; $rp; $rp = $rp->next) {
            $rp->canReduce = false;
        }
        for ($i = 0; $i < $this->nstate; $i++) {
            for ($ap = $this->sorted[$i]->data->ap; $ap !== 0; $ap = $ap->next) {
                if ($ap->type == LemonAction::REDUCE) {
                    $ap->x->canReduce = true;
                }
            }
        }
        for ($rp = $this->rule; $rp !== 0; $rp = $rp->next) {
            if ($rp->canReduce) {
                continue;
            }
            Lemon::ErrorMsg($this->filename, $rp->ruleline, "This rule can not be reduced.\n");
            $this->errorcnt++;
        }
    }

    /** Resolve a conflict between the two given actions.  If the
     * conflict can't be resolve, return non-zero.
     *
     * NO LONGER TRUE:
     *   To resolve a conflict, first look to see if either action
     *   is on an error rule.  In that case, take the action which
     *   is not associated with the error rule.  If neither or both
     *   actions are associated with an error rule, then try to
     *   use precedence to resolve the conflict.
     *
     * If either action is a SHIFT, then it must be apx.  This
     * function won't work if apx->type==REDUCE and apy->type==SHIFT.
     * @param LemonAction
     * @param LemonAction
     * @param LemonSymbol|null The error symbol (if defined.  NULL otherwise)
     */
    function resolve_conflict($apx, $apy, $errsym)
    {
        $errcnt = 0;
        if ($apx->sp !== $apy->sp) {
            throw new Exception('no conflict but resolve_conflict called');
        }
        if ($apx->type == LemonAction::SHIFT && $apy->type == LemonAction::REDUCE) {
            $spx = $apx->sp;
            $spy = $apy->x->precsym;
            if ($spy === 0 || $spx->prec < 0 || $spy->prec < 0) {
                /* Not enough precedence information. */
                $apy->type = LemonAction::CONFLICT;
                $errcnt++;
            } elseif ($spx->prec > $spy->prec) {    /* Lower precedence wins */
                $apy->type = LemonAction::RD_RESOLVED;
            } elseif ($spx->prec < $spy->prec) {
                $apx->type = LemonAction::SH_RESOLVED;
            } elseif ($spx->prec === $spy->prec && $spx->assoc == LemonSymbol::RIGHT) {
                /* Use operator */
                $apy->type = LemonAction::RD_RESOLVED;                       /* associativity */
            } elseif ($spx->prec === $spy->prec && $spx->assoc == LemonSymbol::LEFT) {
                /* to break tie */
                $apx->type = LemonAction::SH_RESOLVED;
            } else {
                if ($spx->prec !== $spy->prec || $spx->assoc !== LemonSymbol::NONE) {
                    throw new Exception('$spx->prec !== $spy->prec || $spx->assoc !== LemonSymbol::NONE');
                }
                $apy->type = LemonAction::CONFLICT;
                $errcnt++;
            }
        } elseif ($apx->type == LemonAction::REDUCE && $apy->type == LemonAction::REDUCE) {
            $spx = $apx->x->precsym;
            $spy = $apy->x->precsym;
            if ($spx === 0 || $spy === 0 || $spx->prec < 0 ||
                  $spy->prec < 0 || $spx->prec === $spy->prec) {
                $apy->type = LemonAction::CONFLICT;
                $errcnt++;
            } elseif ($spx->prec > $spy->prec) {
                $apy->type = LemonAction::RD_RESOLVED;
            } elseif ($spx->prec < $spy->prec) {
                $apx->type = LemonAction::RD_RESOLVED;
            }
        } else {
            if ($apx->type!== LemonAction::SH_RESOLVED &&
                $apx->type!== LemonAction::RD_RESOLVED &&
                $apx->type!== LemonAction::CONFLICT &&
                $apy->type!== LemonAction::SH_RESOLVED &&
                $apy->type!== LemonAction::RD_RESOLVED &&
                $apy->type!== LemonAction::CONFLICT) {
                throw new Exception('$apx->type!== LemonAction::SH_RESOLVED &&
                $apx->type!== LemonAction::RD_RESOLVED &&
                $apx->type!== LemonAction::CONFLICT &&
                $apy->type!== LemonAction::SH_RESOLVED &&
                $apy->type!== LemonAction::RD_RESOLVED &&
                $apy->type!== LemonAction::CONFLICT');
            }
            /* The REDUCE/SHIFT case cannot happen because SHIFTs come before
            ** REDUCEs on the list.  If we reach this point it must be because
            ** the parser conflict had already been resolved. */
        }
        return $errcnt;
    }

    /**
     * Reduce the size of the action tables, if possible, by making use
     * of defaults.
     *
     * In this version, we take the most frequent REDUCE action and make
     * it the default.
     */
    function CompressTables()
    {
        for ($i = 0; $i < $this->nstate; $i++) {
            $stp = $this->sorted[$i]->data;
            $nbest = 0;
            $rbest = 0;

            for ($ap = $stp->ap; $ap; $ap = $ap->next) {
                if ($ap->type != LemonAction::REDUCE) {
                    continue;
                }
                $rp = $ap->x;
                if ($rp === $rbest) {
                    continue;
                }
                $n = 1;
                for ($ap2 = $ap->next; $ap2; $ap2 = $ap2->next) {
                    if ($ap2->type != LemonAction::REDUCE) {
                        continue;
                    }
                    $rp2 = $ap2->x;
                    if ($rp2 === $rbest) {
                        continue;
                    }
                    if ($rp2 === $rp) {
                        $n++;
                    }
                }
                if ($n > $nbest) {
                    $nbest = $n;
                    $rbest = $rp;
                }
            }

            /* Do not make a default if the number of rules to default
            ** is not at least 1 */
            if ($nbest < 1) {
                continue;
            }


            /* Combine matching REDUCE actions into a single default */
            for ($ap = $stp->ap; $ap; $ap = $ap->next) {
                if ($ap->type == LemonAction::REDUCE && $ap->x === $rbest) {
                    break;
                }
            }
            if ($ap === 0) {
                throw new Exception('$ap is not an object');
            }
            $ap->sp = LemonSymbol::Symbol_new("{default}");
            for ($ap = $ap->next; $ap; $ap = $ap->next) {
                if ($ap->type == LemonAction::REDUCE && $ap->x === $rbest) {
                    $ap->type = LemonAction::NOT_USED;
                }
            }
            $stp->ap = LemonAction::Action_sort($stp->ap);
        }
    }

    /**
     * Renumber and resort states so that states with fewer choices
     * occur at the end.  Except, keep state 0 as the first state.
     */
    function ResortStates()
    {
        for ($i = 0; $i < $this->nstate; $i++) {
            $stp = $this->sorted[$i]->data;
            $stp->nTknAct = $stp->nNtAct = 0;
            $stp->iDflt = $this->nstate + $this->nrule;
            $stp->iTknOfst = NO_OFFSET;
            $stp->iNtOfst = NO_OFFSET;
            for ($ap = $stp->ap; $ap; $ap = $ap->next) {
                if ($this->compute_action($ap) >= 0) {
                    if ($ap->sp->index < $this->nterminal) {
                        $stp->nTknAct++;
                    } elseif ($ap->sp->index < $this->nsymbol) {
                        $stp->nNtAct++;
                    } else {
                        $stp->iDflt = $this->compute_action($ap);
                    }
                }
            }
            $this->sorted[$i] = $stp;
        }
        $save = $this->sorted[0];
        unset($this->sorted[0]);
        usort($this->sorted, array('LemonState', 'stateResortCompare'));
        array_unshift($this->sorted, $save);
        for($i = 0; $i < $this->nstate; $i++) {
            $this->sorted[$i]->statenum = $i;
        }
    }

    /**
     * Given an action, compute the integer value for that action
     * which is to be put in the action table of the generated machine.
     * Return negative if no action should be generated.
     * @param LemonAction
     */
    function compute_action($ap)
    {
        switch ($ap->type) {
            case LemonAction::SHIFT:
                $act = $ap->x->statenum;
                break;
            case LemonAction::REDUCE:
                $act = $ap->x->index + $this->nstate;
                break;
            case LemonAction::ERROR:
                $act = $this->nstate + $this->nrule;
                break;
            case LemonAction::ACCEPT:
                $act = $this->nstate + $this->nrule + 1;
                break;
            default:
                $act = -1;
                break;
        }
        return $act;
    }

    /**
     * Generate the "y.output" log file
     */
    function ReportOutput()
    {
        $fp = fopen($this->filenosuffix . ".out", "wb");
        if (!$fp) {
            return;
        }
        for ($i = 0; $i < $this->nstate; $i++) {
            $stp = $this->sorted[$i];
            fprintf($fp, "State %d:\n", $stp->statenum);
            if ($this->basisflag) {
                $cfp = $stp->bp;
            } else {
                $cfp = $stp->cfp;
            }
            while ($cfp) {
                if ($cfp->dot == $cfp->rp->nrhs) {
                    $buf = sprintf('(%d)', $cfp->rp->index);
                    fprintf($fp, '    %5s ', $buf);
                } else {
                    fwrite($fp,'          ');
                }
                $cfp->ConfigPrint($fp);
                fwrite($fp, "\n");
                if (0) {
                    //SetPrint(fp,cfp->fws,$this);
                    //PlinkPrint(fp,cfp->fplp,"To  ");
                    //PlinkPrint(fp,cfp->bplp,"From");
                }
                if ($this->basisflag) {
                    $cfp = $cfp->bp;
                } else {
                    $cfp = $cfp->next;
                }
            }
            fwrite($fp, "\n");
            for ($ap = $stp->ap; $ap; $ap = $ap->next) {
                if ($ap->PrintAction($fp, 30)) {
                    fprintf($fp,"\n");
                }
            }
            fwrite($fp,"\n");
        }
        fclose($fp);
    }

    /* The next function finds the template file and opens it, returning
    ** a pointer to the opened file. */
    private function tplt_open()
    {
        $templatename = dirname(__FILE__) . "/Lempar.php";
        $buf = $this->filenosuffix . '.lt';
        if (file_exists($buf) && is_readable($buf)) {
            $tpltname = $buf;
        } elseif (file_exists($templatename) && is_readable($templatename)) {
            $tpltname = $templatename;
        } elseif ($fp = @fopen($templatename, 'rb', true)) {
            return $fp;
        }
        if (!isset($tpltname)) {
            echo "Can't find the parser driver template file \"%s\".\n",
                $templatename;
            $this->errorcnt++;
            return 0;
        }
        $in = @fopen($tpltname,"rb");
        if (!$in) {
            printf("Can't open the template file \"%s\".\n", $tpltname);
            $this->errorcnt++;
            return 0;
        }
        return $in;
    }

#define LINESIZE 1000
    /**#@+
     * The next cluster of routines are for reading the template file
     * and writing the results to the generated parser
     */
    /**
     * The first function transfers data from "in" to "out" until
     * a line is seen which begins with "%%".  The line number is
     * tracked.
     *
     * if name!=0, then any word that begin with "Parse" is changed to
     * begin with *name instead.
     */
    private function tplt_xfer($name, $in, $out, &$lineno)
    {
        while (($line = fgets($in, 1024)) && ($line[0] != '%' || $line[1] != '%')) {
            $lineno++;
            $iStart = 0;
            if ($name) {
                for ($i = 0; $i < strlen($line); $i++) {
                    if ($line[$i] == 'P' && substr($line, $i, 5) == "Parse"
                          && ($i === 0 || preg_match('/[^a-zA-Z]/', $line[$i - 1]))) {
                        if ($i > $iStart) {
                            fwrite($out, substr($line, $iStart, $i - $iStart));
                        }
                        fwrite($out, $name);
                        $i += 4;
                        $iStart = $i + 1;
                    }
                }
            }
            fwrite($out, substr($line, $iStart));
        }
    }

    /**
     * Print a #line directive line to the output file.
     */
    private function tplt_linedir($out, $lineno, $filename)
    {
        fwrite($out, '#line ' . $lineno . ' "' . $filename . "\"\n");
    }

    /**
     * Print a string to the file and keep the linenumber up to date
     */
    private function tplt_print($out, $str, $strln, &$lineno)
    {
        if ($str == '') {
            return;
        }
        $this->tplt_linedir($out, $strln, $this->filename);
        $lineno++;
        fwrite($out, $str);
        $lineno += count(explode("\n", $str)) - 1;
        $this->tplt_linedir($out, $lineno + 2, $this->outname);
        $lineno += 2;
    }
    /**#@-*/

    /**
     * Compute all followsets.
     *
     * A followset is the set of all symbols which can come immediately
     * after a configuration.
     */
    function FindFollowSets()
    {
        for ($i = 0; $i < $this->nstate; $i++) {
            for ($cfp = $this->sorted[$i]->data->cfp; $cfp; $cfp = $cfp->next) {
                $cfp->status = LemonConfig::INCOMPLETE;
            }
        }

        do {
            $progress = 0;
            for ($i = 0; $i < $this->nstate; $i++) {
                for ($cfp = $this->sorted[$i]->data->cfp; $cfp; $cfp = $cfp->next) {
                    if ($cfp->status == LemonConfig::COMPLETE) {
                        continue;
                    }
                    for ($plp = $cfp->fplp; $plp; $plp = $plp->next) {
                        $a = array_diff_key($cfp->fws, $plp->cfp->fws);
                        if (count($a)) {
                            $plp->cfp->fws += $a;
                            $plp->cfp->status = LemonConfig::INCOMPLETE;
                            $progress = 1;
                        }
                    }
                    $cfp->status = LemonConfig::COMPLETE;
                }
            }
        } while ($progress);
    }

    /**
     * Generate C source code for the parser
     * @param int Output in makeheaders format if true
     */
    function ReportTable($mhflag)
    {
//        FILE *out, *in;
//        char line[LINESIZE];
//        int  lineno;
//        struct state *stp;
//        struct action *ap;
//        struct rule *rp;
//        struct acttab *pActtab;
//        int i, j, n;
//        char *name;
//        int mnTknOfst, mxTknOfst;
//        int mnNtOfst, mxNtOfst;
//        struct axset *ax;

        $in = $this->tplt_open();
        if (!$in) {
            return;
        }
        $out = fopen($this->filenosuffix . ".php", "wb");
        if (!$out) {
            fclose($in);
            return;
        }
        $this->outname = $this->filenosuffix . ".php";
        $lineno = 1;
        $this->tplt_xfer($this->name, $in, $out, $lineno);

        /* Generate the include code, if any */
        $this->tplt_print($out, $this->include_code, $this->includeln, $lineno);
        $this->tplt_xfer($this->name, $in, $out, $lineno);

        /* Generate the class declaration code */
        $this->tplt_print($out, $this->declare_classcode, $this->declare_classln,
            $lineno);
        $this->tplt_xfer($this->name, $in, $out, $lineno);

        /* Generate the internal parser class include code, if any */
        $this->tplt_print($out, $this->include_classcode, $this->include_classln,
            $lineno);
        $this->tplt_xfer($this->name, $in, $out, $lineno);

        /* Generate #defines for all tokens */
        //if ($mhflag) {
            //fprintf($out, "#if INTERFACE\n");
            $lineno++;
            if ($this->tokenprefix) {
                $prefix = $this->tokenprefix;
            } else {
                $prefix = '';
            }
            for ($i = 1; $i < $this->nterminal; $i++) {
                fprintf($out, "    const %s%-30s = %2d;\n", $prefix, $this->symbols[$i]->name, $i);
                $lineno++;
            }
            //fwrite($out, "#endif\n");
            $lineno++;
        //}
        fwrite($out, "    const YY_NO_ACTION = " .
            ($this->nstate + $this->nrule + 2) . ";\n");
        $lineno++;
        fwrite($out, "    const YY_ACCEPT_ACTION = " .
            ($this->nstate + $this->nrule + 1) . ";\n");
        $lineno++;
        fwrite($out, "    const YY_ERROR_ACTION = " .
            ($this->nstate + $this->nrule) . ";\n");
        $lineno++;
        $this->tplt_xfer($this->name, $in, $out, $lineno);

        /* Generate the action table and its associates:
        **
        **  yy_action[]        A single table containing all actions.
        **  yy_lookahead[]     A table containing the lookahead for each entry in
        **                     yy_action.  Used to detect hash collisions.
        **  yy_shift_ofst[]    For each state, the offset into yy_action for
        **                     shifting terminals.
        **  yy_reduce_ofst[]   For each state, the offset into yy_action for
        **                     shifting non-terminals after a reduce.
        **  yy_default[]       Default action for each state.
        */

        /* Compute the actions on all states and count them up */

        $ax = array();
        for ($i = 0; $i < $this->nstate; $i++) {
            $stp = $this->sorted[$i];
            $ax[$i * 2] = array();
            $ax[$i * 2]['stp'] = $stp;
            $ax[$i * 2]['isTkn'] = 1;
            $ax[$i * 2]['nAction'] = $stp->nTknAct;
            $ax[$i * 2 + 1] = array();
            $ax[$i * 2 + 1]['stp'] = $stp;
            $ax[$i * 2 + 1]['isTkn'] = 0;
            $ax[$i * 2 + 1]['nAction'] = $stp->nNtAct;
        }
        $mxTknOfst = $mnTknOfst = 0;
        $mxNtOfst = $mnNtOfst = 0;

        /* Compute the action table.  In order to try to keep the size of the
        ** action table to a minimum, the heuristic of placing the largest action
        ** sets first is used.
        */

        usort($ax, array('LemonData', 'axset_compare'));
        $pActtab = new LemonActtab;
        for ($i = 0; $i < $this->nstate * 2 && $ax[$i]['nAction'] > 0; $i++) {
            $stp = $ax[$i]['stp'];
            if ($ax[$i]['isTkn']) {
                for ($ap = $stp->ap; $ap; $ap = $ap->next) {
                    if ($ap->sp->index >= $this->nterminal) {
                        continue;
                    }
                    $action = $this->compute_action($ap);
                    if ($action < 0) {
                        continue;
                    }
                    $pActtab->acttab_action($ap->sp->index, $action);
                }
                $stp->iTknOfst = $pActtab->acttab_insert();
                if ($stp->iTknOfst < $mnTknOfst) {
                    $mnTknOfst = $stp->iTknOfst;
                }
                if ($stp->iTknOfst > $mxTknOfst) {
                    $mxTknOfst = $stp->iTknOfst;
                }
            } else {
                for ($ap = $stp->ap; $ap; $ap = $ap->next) {
                    if ($ap->sp->index < $this->nterminal) {
                        continue;
                    }
                    if ($ap->sp->index == $this->nsymbol) {
                        continue;
                    }
                    $action = $this->compute_action($ap);
                    if ($action < 0) {
                        continue;
                    }
                    $pActtab->acttab_action($ap->sp->index, $action);
                }
                $stp->iNtOfst = $pActtab->acttab_insert();
                if ($stp->iNtOfst < $mnNtOfst) {
                    $mnNtOfst = $stp->iNtOfst;
                }
                if ($stp->iNtOfst > $mxNtOfst) {
                    $mxNtOfst = $stp->iNtOfst;
                }
            }
        }
        /* Output the yy_action table */

        fprintf($out, "    const YY_SZ_ACTTAB = %d;\n", $pActtab->nAction);
        $lineno++;
        fwrite($out, "static public \$yy_action = array(\n");
        $lineno++;
        $n = $pActtab->nAction;
        for($i = $j = 0; $i < $n; $i++) {
            $action = $pActtab->aAction[$i]['action'];
            if ($action < 0) {
                $action = $this->nsymbol + $this->nrule + 2;
            }
            // change next line
            if ($j === 0) {
                fprintf($out, " /* %5d */ ", $i);
            }
            fprintf($out, " %4d,", $action);
            if ($j == 9 || $i == $n - 1) {
                fwrite($out, "\n");
                $lineno++;
                $j = 0;
            } else {
                $j++;
            }
        }
        fwrite($out, "    );\n"); $lineno++;

        /* Output the yy_lookahead table */

        fwrite($out, "    static public \$yy_lookahead = array(\n");
        $lineno++;
        for ($i = $j = 0; $i < $n; $i++) {
            $la = $pActtab->aAction[$i]['lookahead'];
            if ($la < 0) {
                $la = $this->nsymbol;
            }
            // change next line
            if ($j === 0) {
                fprintf($out, " /* %5d */ ", $i);
            }
            fprintf($out, " %4d,", $la);
            if ($j == 9 || $i == $n - 1) {
                fwrite($out, "\n");
                $lineno++;
                $j = 0;
            } else {
                $j++;
            }
        }
        fwrite($out, ");\n");
        $lineno++;

        /* Output the yy_shift_ofst[] table */
        fprintf($out, "    const YY_SHIFT_USE_DFLT = %d;\n", $mnTknOfst - 1);
        $lineno++;
        $n = $this->nstate;
        while ($n > 0 && $this->sorted[$n - 1]->iTknOfst == NO_OFFSET) {
            $n--;
        }
        fprintf($out, "    const YY_SHIFT_MAX = %d;\n", $n - 1);
        $lineno++;
        fwrite($out, "    static public \$yy_shift_ofst = array(\n");
        $lineno++;
        for ($i = $j = 0; $i < $n; $i++) {
            $stp = $this->sorted[$i];
            $ofst = $stp->iTknOfst;
            if ($ofst === NO_OFFSET) {
                $ofst = $mnTknOfst - 1;
            }
            // change next line
            if ($j === 0) {
                fprintf($out, " /* %5d */ ", $i);
            }
            fprintf($out, " %4d,", $ofst);
            if ($j == 9 || $i == $n - 1) {
                fwrite($out, "\n");
                $lineno++;
                $j = 0;
            } else {
                $j++;
            }
        }
        fwrite($out, ");\n");
        $lineno++;


        /* Output the yy_reduce_ofst[] table */

        fprintf($out, "    const YY_REDUCE_USE_DFLT = %d;\n", $mnNtOfst - 1);
        $lineno++;
        $n = $this->nstate;
        while ($n > 0 && $this->sorted[$n - 1]->iNtOfst == NO_OFFSET) {
            $n--;
        }
        fprintf($out, "    const YY_REDUCE_MAX = %d;\n", $n - 1);
        $lineno++;
        fwrite($out, "    static public \$yy_reduce_ofst = array(\n");
        $lineno++;
        for ($i = $j = 0; $i < $n; $i++) {
            $stp = $this->sorted[$i];
            $ofst = $stp->iNtOfst;
            if ($ofst == NO_OFFSET) {
                $ofst = $mnNtOfst - 1;
            }
            // change next line
            if ($j == 0) {
                fprintf($out, " /* %5d */ ", $i);
            }
            fprintf($out, " %4d,", $ofst);
            if ($j == 9 || $i == $n - 1) {
                fwrite($out, "\n");
                $lineno++;
                $j = 0;
            } else {
                $j++;
            }
        }
        fwrite($out, ");\n");
        $lineno++;

        /* Output the expected tokens table */

        fwrite($out, "    static public \$yyExpectedTokens = array(\n");
        $lineno++;
        for ($i = 0; $i < $this->nstate; $i++) {
            $stp = $this->sorted[$i];
            fwrite($out, "        /* $i */ array(");
            for ($ap = $stp->ap; $ap; $ap = $ap->next) {
                if ($ap->sp->index < $this->nterminal) {
                    if ($ap->type == LemonAction::SHIFT ||
                          $ap->type == LemonAction::REDUCE) {
                        fwrite($out, $ap->sp->index . ', ');
                    }
                }
            }
            fwrite($out, "),\n");
            $lineno++;
        }
        fwrite($out, ");\n");
        $lineno++;

        /* Output the default action table */

        fwrite($out, "    static public \$yy_default = array(\n");
        $lineno++;
        $n = $this->nstate;
        for ($i = $j = 0; $i < $n; $i++) {
            $stp = $this->sorted[$i];
            // change next line
            if ($j == 0) {
                fprintf($out, " /* %5d */ ", $i);
            }
            fprintf($out, " %4d,", $stp->iDflt);
            if ($j == 9 || $i == $n - 1) {
                fprintf($out, "\n"); $lineno++;
                $j = 0;
            } else {
                $j++;
            }
        }
        fwrite($out, ");\n");
        $lineno++;
        $this->tplt_xfer($this->name, $in, $out, $lineno);

        /* Generate the defines */
        fprintf($out, "    const YYNOCODE = %d;\n", $this->nsymbol + 1);
        $lineno++;
        if ($this->stacksize) {
            if($this->stacksize <= 0) {
                Lemon::ErrorMsg($this->filename, 0,
                    "Illegal stack size: [%s].  The stack size should be an integer constant.",
                    $this->stacksize);
                $this->errorcnt++;
                $this->stacksize = "100";
            }
            fprintf($out, "    const YYSTACKDEPTH = %s;\n", $this->stacksize);
            $lineno++;
        } else {
            fwrite($out,"    const YYSTACKDEPTH = 100;\n");
            $lineno++;
        }
        $name = $this->name ? $this->name : "Parse";
        if (isset($this->arg) && strlen($this->arg)) {
            $this->arg = str_replace('$', '', $this->arg); // remove $ from $var
            fprintf($out, "    const %sARG_DECL = '%s';\n", $name, $this->arg);
            $lineno++;
        } else {
            fprintf($out, "    const %sARG_DECL = false;\n", $name);
            $lineno++;
        }
        fprintf($out, "    const YYNSTATE = %d;\n", $this->nstate);
        $lineno++;
        fprintf($out, "    const YYNRULE = %d;\n", $this->nrule);
        $lineno++;
        fprintf($out, "    const YYERRORSYMBOL = %d;\n", $this->errsym->index);
        $lineno++;
        fprintf($out, "    const YYERRSYMDT = 'yy%d';\n", $this->errsym->dtnum);
        $lineno++;
        if ($this->has_fallback) {
            fwrite($out, "    const YYFALLBACK = 1;\n");
        } else {
            fwrite($out, "    const YYFALLBACK = 0;\n");
        }
        $lineno++;
        $this->tplt_xfer($this->name, $in, $out, $lineno);

        /* Generate the table of fallback tokens.
        */

        if ($this->has_fallback) {
            for ($i = 0; $i < $this->nterminal; $i++) {
                $p = $this->symbols[$i];
                if ($p->fallback === 0) {
                    // change next line
                    fprintf($out, "    0,  /* %10s => nothing */\n", $p->name);
                } else {
                    // change next line
                    fprintf($out, "  %3d,  /* %10s => %s */\n",
                        $p->fallback->index, $p->name, $p->fallback->name);
                }
                $lineno++;
            }
        }
        $this->tplt_xfer($this->name, $in, $out, $lineno);


        /* Generate a table containing the symbolic name of every symbol
            ($yyTokenName)
        */

        for ($i = 0; $i < $this->nsymbol; $i++) {
            fprintf($out,"  %-15s", "'" . $this->symbols[$i]->name . "',");
            if (($i & 3) == 3) {
                fwrite($out,"\n");
                $lineno++;
            }
        }
        if (($i & 3) != 0) {
            fwrite($out, "\n");
            $lineno++;
        }
        $this->tplt_xfer($this->name, $in, $out, $lineno);

        /* Generate a table containing a text string that describes every
        ** rule in the rule set of the grammer.  This information is used
        ** when tracing REDUCE actions.
        */

        for ($i = 0, $rp = $this->rule; $rp; $rp = $rp->next, $i++) {
            if ($rp->index !== $i) {
                throw new Exception('rp->index != i and should be');
            }
            // change next line
            fprintf($out, " /* %3d */ \"%s ::=", $i, $rp->lhs->name);
            for ($j = 0; $j < $rp->nrhs; $j++) {
                $sp = $rp->rhs[$j];
                fwrite($out,' ' . $sp->name);
                if ($sp->type == lemonSymbol::MULTITERMINAL) {
                    for($k = 1; $k < $sp->nsubsym; $k++) {
                        fwrite($out, '|' . $sp->subsym[$k]->name);
                    }
                }
            }
            fwrite($out, "\",\n");
            $lineno++;
        }
        $this->tplt_xfer($this->name, $in, $out, $lineno);

        /* Generate code which executes every time a symbol is popped from
        ** the stack while processing errors or while destroying the parser.
        ** (In other words, generate the %destructor actions)
        */

        if ($this->tokendest) {
            for ($i = 0; $i < $this->nsymbol; $i++) {
                $sp = $this->symbols[$i];
                if ($sp === 0 || $sp->type != LemonSymbol::TERMINAL) {
                    continue;
                }
                fprintf($out, "    case %d:\n", $sp->index);
                $lineno++;
            }
            for ($i = 0; $i < $this->nsymbol &&
                         $this->symbols[$i]->type != LemonSymbol::TERMINAL; $i++);
            if ($i < $this->nsymbol) {
                $this->emit_destructor_code($out, $this->symbols[$i], $lineno);
                fprintf($out, "      break;\n");
                $lineno++;
            }
        }
        if ($this->vardest) {
            $dflt_sp = 0;
            for ($i = 0; $i < $this->nsymbol; $i++) {
                $sp = $this->symbols[$i];
                if ($sp === 0 || $sp->type == LemonSymbol::TERMINAL ||
                      $sp->index <= 0 || $sp->destructor != 0) {
                    continue;
                }
                fprintf($out, "    case %d:\n", $sp->index);
                $lineno++;
                $dflt_sp = $sp;
            }
            if ($dflt_sp != 0) {
                $this->emit_destructor_code($out, $dflt_sp, $lineno);
                fwrite($out, "      break;\n");
                $lineno++;
            }
        }
        for ($i = 0; $i < $this->nsymbol; $i++) {
            $sp = $this->symbols[$i];
            if ($sp === 0 || $sp->type == LemonSymbol::TERMINAL ||
                  $sp->destructor === 0) {
                continue;
            }
            fprintf($out, "    case %d:\n", $sp->index);
            $lineno++;

            /* Combine duplicate destructors into a single case */

            for ($j = $i + 1; $j < $this->nsymbol; $j++) {
                $sp2 = $this->symbols[$j];
                if ($sp2 && $sp2->type != LemonSymbol::TERMINAL && $sp2->destructor
                      && $sp2->dtnum == $sp->dtnum
                      && $sp->destructor == $sp2->destructor) {
                    fprintf($out, "    case %d:\n", $sp2->index);
                    $lineno++;
                    $sp2->destructor = 0;
                }
            }

            $this->emit_destructor_code($out, $this->symbols[$i], $lineno);
            fprintf($out, "      break;\n");
            $lineno++;
        }
        $this->tplt_xfer($this->name, $in, $out, $lineno);

        /* Generate code which executes whenever the parser stack overflows */

        $this->tplt_print($out, $this->overflow, $this->overflowln, $lineno);
        $this->tplt_xfer($this->name, $in, $out, $lineno);

        /* Generate the table of rule information
        **
        ** Note: This code depends on the fact that rules are number
        ** sequentually beginning with 0.
        */

        for ($rp = $this->rule; $rp; $rp = $rp->next) {
            fprintf($out, "  array( 'lhs' => %d, 'rhs' => %d ),\n",
                $rp->lhs->index, $rp->nrhs);
            $lineno++;
        }
        $this->tplt_xfer($this->name, $in, $out, $lineno);


        /* Generate code which executes during each REDUCE action */

        for ($rp = $this->rule; $rp; $rp = $rp->next) {
            if ($rp->code) {
                $this->translate_code($rp);
            }
        }

        /* Generate the method map for each REDUCE action */

        for ($rp = $this->rule; $rp; $rp = $rp->next) {
            if ($rp->code === 0) {
                continue;
            }
            fwrite($out, '        ' . $rp->index . ' => ' . $rp->index . ",\n");
            $lineno++;
            for ($rp2 = $rp->next; $rp2; $rp2 = $rp2->next) {
                if ($rp2->code === $rp->code) {
                    fwrite($out, '        ' . $rp2->index . ' => ' .
                        $rp->index . ",\n");
                    $lineno++;
                    $rp2->code = 0;
                }
            }
        }
        $this->tplt_xfer($this->name, $in, $out, $lineno);

        for ($rp = $this->rule; $rp; $rp = $rp->next) {
            if ($rp->code === 0) {
                continue;
            }
            $this->emit_code($out, $rp, $lineno);
        }
        $this->tplt_xfer($this->name, $in, $out, $lineno);


        /* Generate code which executes if a parse fails */

        $this->tplt_print($out, $this->failure, $this->failureln, $lineno);
        $this->tplt_xfer($this->name, $in, $out, $lineno);

        /* Generate code which executes when a syntax error occurs */

        $this->tplt_print($out, $this->error, $this->errorln, $lineno);
        $this->tplt_xfer($this->name, $in, $out, $lineno);

        /* Generate code which executes when the parser accepts its input */

        $this->tplt_print($out, $this->accept, $this->acceptln, $lineno);
        $this->tplt_xfer($this->name, $in, $out, $lineno);

        /* Append any addition code the user desires */

        $this->tplt_print($out, $this->extracode, $this->extracodeln, $lineno);

        fclose($in);
        fclose($out);
    }

    /**
     * Generate code which executes when the rule "rp" is reduced.  Write
     * the code to "out".  Make sure lineno stays up-to-date.
     */
    function emit_code($out, LemonRule $rp, &$lineno)
    {
        $linecnt = 0;

        /* Generate code to do the reduce action */
        if ($rp->code) {
            $this->tplt_linedir($out, $rp->line, $this->filename);
            fwrite($out, "    function yy_r$rp->index(){" . $rp->code);
            $linecnt += count(explode("\n", $rp->code)) - 1;
            $lineno += 3 + $linecnt;
            fwrite($out, "    }\n");
            $this->tplt_linedir($out, $lineno, $this->outname);
        } /* End if( rp->code ) */
    }

    /**
     * Append text to a dynamically allocated string.  If zText is 0 then
     * reset the string to be empty again.  Always return the complete text
     * of the string (which is overwritten with each call).
     *
     * n bytes of zText are stored.  If n==0 then all of zText is stored.
     *
     * If n==-1, then the previous character is overwritten.
     * @param string
     * @param int
     */
    function append_str($zText, $n)
    {
        static $z = '';
        $zInt = '';

        if ($zText === '') {
            $ret = $z;
            $z = '';
            return $ret;
        }
        if ($n <= 0) {
            if ($n < 0) {
                if (!strlen($z)) {
                    throw new Exception('z is zero-length');
                }
                $z = substr($z, 0, strlen($z) - 1);
                if (!$z) {
                    $z = '';
                }
            }
            $n = strlen($zText);
        }
        $i = 0;
        $z .= substr($zText, 0, $n);
        return $z;
    }

    /**
     * zCode is a string that is the action associated with a rule.  Expand
     * the symbols in this string so that the refer to elements of the parser
     * stack.
     */
    function translate_code(LemonRule $rp)
    {
        $lhsused = 0;    /* True if the LHS element has been used */
        $used = array();   /* True for each RHS element which is used */

        for($i = 0; $i < $rp->nrhs; $i++) {
            $used[$i] = 0;
        }

        $this->append_str('', 0);
        for ($i = 0; $i < strlen($rp->code); $i++) {
            $cp = $rp->code[$i];
            if (preg_match('/[A-Za-z]/', $cp) &&
                 ($i === 0 || (!preg_match('/[A-Za-z0-9_]/', $rp->code[$i - 1])))) {
                //*xp = 0;
                // previous line is in essence a temporary substr, so
                // we will simulate it
                $test = substr($rp->code, $i);
                preg_match('/[A-Za-z0-9_]+/', $test, $matches);
                $tempcp = $matches[0];
                $j = strlen($tempcp) + $i;
                if ($rp->lhsalias && $tempcp == $rp->lhsalias) {
                    $this->append_str("\$this->_retvalue", 0);
                    $cp = $rp->code[$j];
                    $i = $j;
                    $lhsused = 1;
                } else {
                    for ($ii = 0; $ii < $rp->nrhs; $ii++) {
                        if ($rp->rhsalias[$ii] && $tempcp == $rp->rhsalias[$ii]) {
                            if ($ii !== 0 && $rp->code[$ii - 1] == '@') {
                                /* If the argument is of the form @X then substitute
                                ** the token number of X, not the value of X */
                                $this->append_str("\$this->yystack[\$this->yyidx + " .
                                    ($ii - $rp->nrhs + 1) . "]->major", -1);
                            } else {
                                $sp = $rp->rhs[$ii];
                                if ($sp->type == LemonSymbol::MULTITERMINAL) {
                                    $dtnum = $sp->subsym[0]->dtnum;
                                } else {
                                    $dtnum = $sp->dtnum;
                                }
                                $this->append_str("\$this->yystack[\$this->yyidx + " .
                                    ($ii - $rp->nrhs + 1) . "]->minor", 0);
                            }
                            $cp = $rp->code[$j];
                            $i = $j;
                            $used[$ii] = 1;
                            break;
                        }
                    }
                }
            }
            $this->append_str($cp, 1);
        } /* End loop */

        /* Check to make sure the LHS has been used */
        if ($rp->lhsalias && !$lhsused) {
            Lemon::ErrorMsg($this->filename, $rp->ruleline,
                "Label \"%s\" for \"%s(%s)\" is never used.",
                $rp->lhsalias, $rp->lhs->name, $rp->lhsalias);
                $this->errorcnt++;
        }

        /* Generate destructor code for RHS symbols which are not used in the
        ** reduce code */
        for($i = 0; $i < $rp->nrhs; $i++) {
            if ($rp->rhsalias[$i] && !isset($used[$i])) {
                Lemon::ErrorMsg($this->filename, $rp->ruleline,
                    "Label %s for \"%s(%s)\" is never used.",
                    $rp->rhsalias[$i], $rp->rhs[$i]->name, $rp->rhsalias[$i]);
                $this->errorcnt++;
            } elseif ($rp->rhsalias[$i] == 0) {
                if ($rp->rhs[$i]->type == LemonSymbol::TERMINAL) {
                    $hasdestructor = $this->tokendest != 0;
                }else{
                    $hasdestructor = $this->vardest !== 0 || $rp->rhs[$i]->destructor !== 0;
                }
                if ($hasdestructor) {
                    $this->append_str("  \$this->yy_destructor(" .
                        ($rp->rhs[$i]->index) . ", \$this->yystack[\$this->yyidx + " .
                        ($i - $rp->nrhs + 1) . "]->minor);\n", 0);
                } else {
                    /* No destructor defined for this term */
                }
            }
        }
        $cp = $this->append_str('', 0);
        $rp->code = $cp;
    }

    /**
     * The following routine emits code for the destructor for the
     * symbol sp
     */
    function emit_destructor_code($out, LemonSymbol $sp, &$lineno)
//    FILE *out;
//    struct symbol *sp;
//    struct lemon *lemp;
//    int *lineno;
    {
        $cp = 0;

        $linecnt = 0;
        if ($sp->type == LemonSymbol::TERMINAL) {
            $cp = $this->tokendest;
            if ($cp === 0) {
                return;
            }
            $this->tplt_linedir($out, $this->tokendestln, $this->filename);
            fwrite($out, "{");
        } elseif ($sp->destructor) {
            $cp = $sp->destructor;
            $this->tplt_linedir($out, $sp->destructorln, $this->filename);
            fwrite($out, "{");
        } elseif ($this->vardest) {
            $cp = $this->vardest;
            if ($cp === 0) {
                return;
            }
            $this->tplt_linedir($out, $this->vardestln, $this->filename);
            fwrite($out, "{");
        } else {
            throw new Exception('emit_destructor'); /* Cannot happen */
        }
        for ($i = 0; $i < strlen($cp); $i++) {
            if ($cp[$i]=='$' && $cp[$i + 1]=='$' ) {
                fprintf($out, "(yypminor->yy%d)", $sp->dtnum);
                $i++;
                continue;
            }
            if ($cp[$i] == "\n") {
                $linecnt++;
            }
            fwrite($out, $cp[$i]);
        }
        $lineno += 3 + $linecnt;
        fwrite($out, "}\n");
        $this->tplt_linedir($out, $lineno, $this->outname);
    }

    /**
     * Compare to axset structures for sorting purposes
     */
    static function axset_compare($a, $b)
    {
        return $b['nAction'] - $a['nAction'];
    }
}

class Lemon
{
    const MAXRHS = 1000;
    const OPT_FLAG = 1, OPT_INT = 2, OPT_DBL = 3, OPT_STR = 4,
          OPT_FFLAG = 5, OPT_FINT = 6, OPT_FDBL = 7, OPT_FSTR = 8;
    public $azDefine = array();
    private static $options = array(
        'b' => array(
            'type' => self::OPT_FLAG,
            'arg' => 'basisflag',
            'message' => 'Print only the basis in report.'
        ),
        'c' => array(
            'type' => self::OPT_FLAG,
            'arg' => 'compress',
            'message' => 'Don\'t compress the action table.'
        ),
        'D' => array(
            'type' => self::OPT_FSTR,
            'arg' => 'handle_D_option',
            'message' => 'Define an %ifdef macro.'
        ),
        'g' => array(
            'type' => self::OPT_FLAG,
            'arg' => 'rpflag',
            'message' => 'Print grammar without actions.'
        ),
        'm' => array(
            'type' => self::OPT_FLAG,
            'arg' => 'mhflag',
            'message' => 'Output a makeheaders compatible file'
        ),
        'q' => array(
            'type' => self::OPT_FLAG,
            'arg' => 'quiet',
            'message' => '(Quiet) Don\'t print the report file.'
        ),
        's' => array(
            'type' => self::OPT_FLAG,
            'arg' => 'statistics',
            'message' => 'Print parser stats to standard output.'
        ),
        'x' => array(
            'type' => self::OPT_FLAG,
            'arg' => 'version',
            'message' => 'Print the version number.'
        )
    );

    private $basisflag = 0;
    private $compress = 0;
    private $rpflag = 0;
    private $mhflag = 0;
    private $quiet = 0;
    private $statistics = 0;
    private $version = 0;
    private $size;
    /**
     * Process a flag command line argument.
     * @param int
     * @param array
     * @return int
     */
    function handleflags($i, $argv)
    {
        if (!isset($argv[1]) || !isset(self::$options[$argv[$i][1]])) {
            throw new Exception('Command line syntax error: undefined option "' .  $argv[$i] . '"');
        }
        $v = self::$options[$argv[$i][1]] == '-';
        if (self::$options[$argv[$i][1]]['type'] == self::OPT_FLAG) {
            $this->{self::$options[$argv[$i][1]]['arg']} = (int) $v;
        } elseif (self::$options[$argv[$i][1]]['type'] == self::OPT_FFLAG) {
            $this->{self::$options[$argv[$i][1]]['arg']}($v);
        } elseif (self::$options[$argv[$i][1]]['type'] == self::OPT_FSTR) {
            $this->{self::$options[$argv[$i][1]]['arg']}(substr($v, 2));
        } else {
            throw new Exception('Command line syntax error: missing argument on switch: "' . $argv[$i] . '"');
        }
        return 0;
    }

    /**
     * Process a command line switch which has an argument.
     * @param int
     * @param array
     * @param array
     * @return int
     */
    function handleswitch($i, $argv)
    {
        $lv = 0;
        $dv = 0.0;
        $sv = $end = $cp = '';
        $j; // int
        $errcnt = 0;
        $cp = strstr($argv[$i],'=');
        if (!$cp) {
            throw new Exception('INTERNAL ERROR: handleswitch passed bad argument, no "=" in arg');
        }
        $argv[$i] = substr($argv[$i], 0, strlen($argv[$i]) - strlen($cp));
        if (!isset(self::$options[$argv[$i]])) {
            throw new Exception('Command line syntax error: undefined option "' .  $argv[$i] .
                $cp . '"');
        }
        $cp = substr($cp, 1);
        switch (self::$options[$argv[$i]]['type']) {
            case self::OPT_FLAG:
            case self::OPT_FFLAG:
                throw new Exception('Command line syntax error: option requires an argument "' .
                    $argv[$i] . '=' . $cp . '"');
            case self::OPT_DBL:
            case self::OPT_FDBL:
                $dv = (double) $cp;
                break;
            case self::OPT_INT:
            case self::OPT_FINT:
                $lv = (int) $cp;
                break;
            case self::OPT_STR:
            case self::OPT_FSTR:
                $sv = $cp;
                break;
        }
        switch(self::$options[$argv[$i]]['type']) {
            case self::OPT_FLAG:
            case self::OPT_FFLAG:
                break;
            case self::OPT_DBL:
                $this->${self::$options[$argv[$i]]['arg']} = $dv;
                break;
            case self::OPT_FDBL:
                $this->${self::$options[$argv[$i]]['arg']}($dv);
                break;
            case self::OPT_INT:
                $this->${self::$options[$argv[$i]]['arg']} = $lv;
                break;
            case self::OPT_FINT:
                $this->${self::$options[$argv[$i]]['arg']}($lv);
                break;
            case self::OPT_STR:
                $this->${self::$options[$argv[$i]]['arg']} = $sv;
                break;
            case self::OPT_FSTR:
                $this->${self::$options[$argv[$i]]['arg']}($sv);
                break;
        }
        return 0;
    }

    /**
     * @param array arguments
     * @param array valid options
     * @return int
     */
    function OptInit($a)
    {
        $errcnt = 0;
        $argv = $a;
        try {
            if (is_array($argv) && count($argv) && self::$options) {
                for($i = 1; $i < count($argv); $i++) {
                    if ($argv[$i][0] == '+' || $argv[$i][0] == '-') {
                        $errcnt += $this->handleflags($i, $argv);
                    } elseif (strstr($argv[$i],'=')) {
                        $errcnt += $this->handleswitch(i, $argv);
                    }
                }
            }
        } catch (Exception $e) {
            OptPrint();
            echo $e;
            exit(1);
        }
        return 0;
    }

    /**
     * Return the index of the N-th non-switch argument.  Return -1
     * if N is out of range.
     * @param int
     * @return int
     */
    private function argindex($n, $a)
    {
        $dashdash = 0;
        if (!is_array($a) || !count($a)) {
            return -1;
        }
        for ($i=1; $i < count($a); $i++) {
            if ($dashdash || !($a[$i][0] == '-' || $a[$i][0] == '+' ||
                  strchr($a[$i], '='))) {
                if ($n == 0) {
                    return $i;
                }
                $n--;
            }
            if ($_SERVER['argv'][$i] == '--') {
                $dashdash = 1;
            }
        }
        return -1;
    }

    /**
     * Return the value of the non-option argument as indexed by $i
     *
     * @param int
     * @param array the value of $argv
     * @return 0|string
     */
    private function OptArg($i, $a)
    {
        if (-1 == ($ind = $this->argindex($i, $a))) {
            return 0;
        }
        return $a[$ind];
    }

    /**
     * @return int number of arguments
     */
    function OptNArgs($a)
    {
        $cnt = $dashdash = 0;
        if (is_array($a) && count($a)) {
            for($i = 1; $i < count($a); $i++) {
                if ($dashdash || !($a[$i][0] == '-' || $a[$i][0] == '+' ||
                      strchr($a[$i], '='))) {
                    $cnt++;
                }
                if ($a[$i] == "--") {
                    $dashdash = 1;
                }
            }
        }
        return $cnt;
    }

    /**
     * Print out command-line options
     */
    function OptPrint()
    {
        $max = 0;
        foreach (self::$options as $label => $info) {
            $len = strlen($label) + 1;
            switch ($info['type']) {
                case self::OPT_FLAG:
                case self::OPT_FFLAG:
                    break;
                case self::OPT_INT:
                case self::OPT_FINT:
                    $len += 9;       /* length of "<integer>" */
                    break;
                case self::OPT_DBL:
                case self::OPT_FDBL:
                    $len += 6;       /* length of "<real>" */
                    break;
                case self::OPT_STR:
                case self::OPT_FSTR:
                    $len += 8;       /* length of "<string>" */
                    break;
            }
            if ($len > $max) {
                $max = $len;
            }
        }
        foreach (self::$options as $label => $info) {
            switch ($info['type']) {
                case self::OPT_FLAG:
                case self::OPT_FFLAG:
                    printf("  -%-*s  %s\n", $max, $label, $info['message']);
                    break;
                case self::OPT_INT:
                case self::OPT_FINT:
                    printf("  %s=<integer>%*s  %s\n", $label, $max - strlen($label) - 9,
                        $info['message']);
                    break;
                case self::OPT_DBL:
                case self::OPT_FDBL:
                    printf("  %s=<real>%*s  %s\n", $label, $max - strlen($label) - 6,
                        $info['message']);
                    break;
                case self::OPT_STR:
                case self::OPT_FSTR:
                    printf("  %s=<string>%*s  %s\n", $label, $max - strlen($label) - 8,
                        $info['message']);
                    break;
            }
        }
    }

    /**
    * This routine is called with the argument to each -D command-line option.
    * Add the macro defined to the azDefine array.
    * @param string
    */
    private function handle_D_option($z)
    {
        if ($a = strstr($z, '=')) {
            $z = substr($a, 1); // strip first =
        }
        $this->azDefine[] = $z;
    }

    /**************** From the file "main.c" ************************************/
/*
** Main program file for the LEMON parser generator.
*/


    /* The main program.  Parse the command line and do it... */
    function main()
    {
        $lem = new LemonData;

        $this->OptInit($_SERVER['argv']);
        if ($this->version) {
            echo "Lemon version 1.0/PHP port version 1.0\n";
            exit(0);
        }
        if ($this->OptNArgs($_SERVER['argv']) != 1) {
            echo "Exactly one filename argument is required.\n";
            exit(1);
        }
        $lem->errorcnt = 0;

        /* Initialize the machine */
        $lem->argv0 = $_SERVER['argv'][0];
        $lem->filename = $this->OptArg(0, $_SERVER['argv']);
        $a = pathinfo($lem->filename);
        if (isset($a['extension'])) {
            $ext = '.' . $a['extension'];
            $lem->filenosuffix = substr($lem->filename, 0, strlen($lem->filename) - strlen($ext));
        } else {
            $lem->filenosuffix = $lem->filename;
        }
        $lem->basisflag = $this->basisflag;
        $lem->has_fallback = 0;
        $lem->nconflict = 0;
        $lem->name = $lem->include_code = $lem->include_classcode = $lem->arg =
            $lem->tokentype = $lem->start = 0;
        $lem->vartype = 0;
        $lem->stacksize = 0;
        $lem->error = $lem->overflow = $lem->failure = $lem->accept = $lem->tokendest =
          $lem->tokenprefix = $lem->outname = $lem->extracode = 0;
        $lem->vardest = 0;
        $lem->tablesize = 0;
        LemonSymbol::Symbol_new("$");
        $lem->errsym = LemonSymbol::Symbol_new("error");

        /* Parse the input file */
        $parser = new LemonParser($this);
        $parser->Parse($lem);
        if ($lem->errorcnt) {
            exit($lem->errorcnt);
        }
        if ($lem->rule === 0) {
            printf("Empty grammar.\n");
            exit(1);
        }

        /* Count and index the symbols of the grammar */
        $lem->nsymbol = LemonSymbol::Symbol_count();
        LemonSymbol::Symbol_new("{default}");
        $lem->symbols = LemonSymbol::Symbol_arrayof();
        for ($i = 0; $i <= $lem->nsymbol; $i++) {
            $lem->symbols[$i]->index = $i;
        }
        usort($lem->symbols, array('LemonSymbol', 'sortSymbols'));
        for ($i = 0; $i <= $lem->nsymbol; $i++) {
            $lem->symbols[$i]->index = $i;
        }
        // find the first lower-case symbol
        for($i = 1; ord($lem->symbols[$i]->name[0]) < ord ('Z'); $i++);
        $lem->nterminal = $i;

        /* Generate a reprint of the grammar, if requested on the command line */
        if ($this->rpflag) {
            $this->Reprint();
        } else {
            /* Initialize the size for all follow and first sets */
            $this->SetSize($lem->nterminal);

            /* Find the precedence for every production rule (that has one) */
            $lem->FindRulePrecedences();

            /* Compute the lambda-nonterminals and the first-sets for every
            ** nonterminal */
            $lem->FindFirstSets();

            /* Compute all LR(0) states.  Also record follow-set propagation
            ** links so that the follow-set can be computed later */
            $lem->nstate = 0;
            $lem->FindStates();
            $lem->sorted = LemonState::State_arrayof();

            /* Tie up loose ends on the propagation links */
            $lem->FindLinks();

            /* Compute the follow set of every reducible configuration */
            $lem->FindFollowSets();

            /* Compute the action tables */
            $lem->FindActions();

            /* Compress the action tables */
            if ($this->compress===0) {
                $lem->CompressTables();
            }

            /* Reorder and renumber the states so that states with fewer choices
            ** occur at the end. */
            $lem->ResortStates();

            /* Generate a report of the parser generated.  (the "y.output" file) */
            if (!$this->quiet) {
                $lem->ReportOutput();
            }

            /* Generate the source code for the parser */
            $lem->ReportTable($this->mhflag);

    /* Produce a header file for use by the scanner.  (This step is
    ** omitted if the "-m" option is used because makeheaders will
    ** generate the file for us.) */
//            if (!$this->mhflag) {
//                $this->ReportHeader();
//            }
        }
        if ($this->statistics) {
            printf("Parser statistics: %d terminals, %d nonterminals, %d rules\n",
                $lem->nterminal, $lem->nsymbol - $lem->nterminal, $lem->nrule);
            printf("                   %d states, %d parser table entries, %d conflicts\n",
                $lem->nstate, $lem->tablesize, $lem->nconflict);
        }
        if ($lem->nconflict) {
            printf("%d parsing conflicts.\n", $lem->nconflict);
        }
        exit($lem->errorcnt + $lem->nconflict);
        return ($lem->errorcnt + $lem->nconflict);
    }

    function SetSize($n)
    {
        $this->size = $n + 1;
    }

    /**
     * Merge in a merge sort for a linked list
     * Inputs:
     *  - a:       A sorted, null-terminated linked list.  (May be null).
     *  - b:       A sorted, null-terminated linked list.  (May be null).
     *  - cmp:     A pointer to the comparison function.
     *  - offset:  Offset in the structure to the "next" field.
     *
     * Return Value:
     *   A pointer to the head of a sorted list containing the elements
     *   of both a and b.
     *
     * Side effects:
     *   The "next" pointers for elements in the lists a and b are
     *   changed.
     */
    static function merge($a, $b, $cmp, $offset)
    {
        if($a === 0) {
            $head = $b;
        } elseif ($b === 0) {
            $head = $a;
        } else {
            if (call_user_func($cmp, $a, $b) < 0) {
                $ptr = $a;
                $a = $a->$offset;
            } else {
                $ptr = $b;
                $b = $b->$offset;
            }
            $head = $ptr;
            while ($a && $b) {
                if (call_user_func($cmp, $a, $b) < 0) {
                    $ptr->$offset = $a;
                    $ptr = $a;
                    $a = $a->$offset;
                } else {
                    $ptr->$offset = $b;
                    $ptr = $b;
                    $b = $b->$offset;
                }
            }
            if ($a !== 0) {
                $ptr->$offset = $a;
            } else {
                $ptr->$offset = $b;
            }
        }
        return $head;
    }

    /*
    ** Inputs:
    **   list:      Pointer to a singly-linked list of structures.
    **   next:      Pointer to pointer to the second element of the list.
    **   cmp:       A comparison function.
    **
    ** Return Value:
    **   A pointer to the head of a sorted list containing the elements
    **   orginally in list.
    **
    ** Side effects:
    **   The "next" pointers for elements in list are changed.
    */
    #define LISTSIZE 30
    static function msort($list, $next, $cmp)
    {
        if ($list === 0) {
            return $list;
        }
        if ($list->$next === 0) {
            return $list;
        }
        $set = array_fill(0, 30, 0);
        while ($list) {
            $ep = $list;
            $list = $list->$next;
            $ep->$next = 0;
            for ($i = 0; $i < 29 && $set[$i] !== 0; $i++) {
                $ep = self::merge($ep, $set[$i], $cmp, $next);
                $set[$i] = 0;
            }
            $set[$i] = $ep;
        }
        $ep = 0;
        for ($i = 0; $i < 30; $i++) {
            if ($set[$i] !== 0) {
                $ep = self::merge($ep, $set[$i], $cmp, $next);
            }
        }
        return $ep;
    }

    /* Find a good place to break "msg" so that its length is at least "min"
    ** but no more than "max".  Make the point as close to max as possible.
    */
    static function findbreak($msg, $min, $max)
    {
        if ($min >= strlen($msg)) {
            return strlen($msg);
        }
        for ($i = $spot = $min; $i <= $max && $i < strlen($msg); $i++) {
            $c = $msg[$i];
            if ($c == '-' && $i < $max - 1) {
                $spot = $i + 1;
            }
            if ($c == ' ') {
                $spot = $i;
            }
        }
        return $spot;
    }

    static function ErrorMsg($filename, $lineno, $format)
    {
        /* Prepare a prefix to be prepended to every output line */
        if ($lineno > 0) {
            $prefix = sprintf("%20s:%d: ", $filename, $lineno);
        } else {
            $prefix = sprintf("%20s: ", $filename);
        }
        $prefixsize = strlen($prefix);
        $availablewidth = 79 - $prefixsize;

        /* Generate the error message */
        $ap = func_get_args();
        array_shift($ap); // $filename
        array_shift($ap); // $lineno
        array_shift($ap); // $format
        $errmsg = vsprintf($format, $ap);
        $linewidth = strlen($errmsg);
        /* Remove trailing "\n"s from the error message. */
        while ($linewidth > 0 && in_array($errmsg[$linewidth-1], array("\n", "\r"), true)) {
            --$linewidth;
            $errmsg = substr($errmsg, 0, strlen($errmsg) - 1);
        }

        /* Print the error message */
        $base = 0;
        $errmsg = str_replace(array("\r", "\n", "\t"), array(' ', ' ', ' '), $errmsg);
        while (strlen($errmsg)) {
            $end = $restart = self::findbreak($errmsg, 0, $availablewidth);
            if (strlen($errmsg) <= 79 && $end < strlen($errmsg) && $end <= 79) {
                $end = $restart = strlen($errmsg);
            }
            while (isset($errmsg[$restart]) && $errmsg[$restart] == ' ') {
                $restart++;
            }
            printf("%s%.${end}s\n", $prefix, $errmsg);
            $errmsg = substr($errmsg, $restart);
        }
    }

    /**
     * Duplicate the input file without comments and without actions
     * on rules
     */
    function Reprint()
    {
        printf("// Reprint of input file \"%s\".\n// Symbols:\n", $this->filename);
        $maxlen = 10;
        for ($i = 0; $i < $this->nsymbol; $i++) {
            $sp = $this->symbols[$i];
            $len = strlen($sp->name);
            if ($len > $maxlen ) {
                $maxlen = $len;
            }
        }
        $ncolumns = 76 / ($maxlen + 5);
        if ($ncolumns < 1) {
            $ncolumns = 1;
        }
        $skip = ($this->nsymbol + $ncolumns - 1) / $ncolumns;
        for ($i = 0; $i < $skip; $i++) {
            print "//";
            for ($j = $i; $j < $this->nsymbol; $j += $skip) {
                $sp = $this->symbols[$j];
                //assert( sp->index==j );
                printf(" %3d %-${maxlen}.${maxlen}s", $j, $sp->name);
            }
            print "\n";
        }
        for ($rp = $this->rule; $rp; $rp = $rp->next) {
            printf("%s", $rp->lhs->name);
/*          if ($rp->lhsalias) {
                printf("(%s)", $rp->lhsalias);
            }*/
            print " ::=";
            for ($i = 0; $i < $rp->nrhs; $i++) {
                $sp = $rp->rhs[$i];
                printf(" %s", $sp->name);
                if ($sp->type == LemonSymbol::MULTITERMINAL) {
                    for ($j = 1; $j < $sp->nsubsym; $j++) {
                        printf("|%s", $sp->subsym[$j]->name);
                    }
                }
/*              if ($rp->rhsalias[$i]) {
                    printf("(%s)", $rp->rhsalias[$i]);
                }*/
            }
            print ".";
            if ($rp->precsym) {
                printf(" [%s]", $rp->precsym->name);
            }
/*          if ($rp->code) {
                print "\n    " . $rp->code);
            }*/
            print "\n";
        }
    }
}

class LemonParser
{
    const INITIALIZE = 1;
    const WAITING_FOR_DECL_OR_RULE = 2;
    const WAITING_FOR_DECL_KEYWORD = 3;
    const WAITING_FOR_DECL_ARG = 4;
    const WAITING_FOR_PRECEDENCE_SYMBOL = 5;
    const WAITING_FOR_ARROW = 6;
    const IN_RHS = 7;
    const LHS_ALIAS_1 = 8;
    const LHS_ALIAS_2 = 9;
    const LHS_ALIAS_3 = 10;
    const RHS_ALIAS_1 = 11;
    const RHS_ALIAS_2 = 12;
    const PRECEDENCE_MARK_1 = 13;
    const PRECEDENCE_MARK_2 = 14;
    const RESYNC_AFTER_RULE_ERROR = 15;
    const RESYNC_AFTER_DECL_ERROR = 16;
    const WAITING_FOR_DESTRUCTOR_SYMBOL = 17;
    const WAITING_FOR_DATATYPE_SYMBOL = 18;
    const WAITING_FOR_FALLBACK_ID = 19;

    public $filename;       /* Name of the input file */
    public $tokenlineno;      /* Linenumber at which current token starts */
    public $errorcnt;         /* Number of errors so far */
    public $tokenstart;     /* Text of current token */
    /**
     * @var LemonData
     */
    public $gp;     /* Global state vector */
  /* enum e_state {
    INITIALIZE,
    WAITING_FOR_DECL_OR_RULE,
    WAITING_FOR_DECL_KEYWORD,
    WAITING_FOR_DECL_ARG,
    WAITING_FOR_PRECEDENCE_SYMBOL,
    WAITING_FOR_ARROW,
    IN_RHS,
    LHS_ALIAS_1,
    LHS_ALIAS_2,
    LHS_ALIAS_3,
    RHS_ALIAS_1,
    RHS_ALIAS_2,
    PRECEDENCE_MARK_1,
    PRECEDENCE_MARK_2,
    RESYNC_AFTER_RULE_ERROR,
    RESYNC_AFTER_DECL_ERROR,
    WAITING_FOR_DESTRUCTOR_SYMBOL,
    WAITING_FOR_DATATYPE_SYMBOL,
    WAITING_FOR_FALLBACK_ID
  } */
    public $state;                   /* The state of the parser */
    /**
     * @var LemonSymbol
     */
    public $fallback;   /* The fallback token */
    /**
     * @var LemonSymbol
     */
    public $lhs;        /* Left-hand side of current rule */
    public $lhsalias;            /* Alias for the LHS */
    public $nrhs;                  /* Number of right-hand side symbols seen */
    /**
     * @var array array of {@link LemonSymbol} objects
     */
    public $rhs = array();  /* RHS symbols */
    public $alias = array();       /* Aliases for each RHS symbol name (or NULL) */
    /**
     * @var LemonRule
     */
    public $prevrule;     /* Previous rule parsed */
    public $declkeyword;         /* Keyword of a declaration */
    /**
     * @var array array of strings
     */
    public $declargslot = array();        /* Where the declaration argument should be put */
    public $decllnslot;           /* Where the declaration linenumber is put */
    /*enum e_assoc*/
    public $declassoc;    /* Assign this association to decl arguments */
    public $preccounter;           /* Assign this precedence to decl arguments */
    /**
     * @var LemonRule
     */
    public $firstrule;    /* Pointer to first rule in the grammar */
    /**
     * @var LemonRule
     */
    public $lastrule;     /* Pointer to the most recently parsed rule */

    /**
     * @var Lemon
     */
    private $lemon;

    function __construct($lem)
    {
        $this->lemon = $lem;
    }
    /**
     * Run the proprocessor over the input file text.  The Lemon variable
     * $azDefine contains the names of all defined
     * macros.  This routine looks for "%ifdef" and "%ifndef" and "%endif" and
     * comments them out.  Text in between is also commented out as appropriate.
     */
    private function preprocess_input(&$z)
    {
        $lineno = $exclude = 0;
        for ($i=0; $i < strlen($z); $i++) {
            if ($z[$i] == "\n") {
                $lineno++;
            }
            if ($z[$i] != '%' || ($i > 0 && $z[$i-1] != "\n")) {
                continue;
            }
            if (substr($z, $i, 6) === "%endif" && trim($z[$i+6]) === '') {
                if ($exclude) {
                    $exclude--;
                    if ($exclude === 0) {
                        for ($j = $start; $j < $i; $j++) {
                            if ($z[$j] != "\n") $z[$j] = ' ';
                        }
                    }
                }
                for ($j = $i; $j < strlen($z) && $z[$j] != "\n"; $j++) {
                    $z[$j] = ' ';
                }
            } elseif (substr($z, $i, 6) === "%ifdef" && trim($z[$i+6]) === '' ||
                      substr($z, $i, 7) === "%ifndef" && trim($z[$i+7]) === '') {
                if ($exclude) {
                    $exclude++;
                } else {
                    $j = $i;
                    $n = strtok(substr($z, $j), " \t");
                    $exclude = 1;
                    if (isset($this->lemon->azDefine[$n])) {
                        $exclude = 0;
                    }
                    if ($z[$i + 3]=='n') {
                        // this is a rather obtuse way of checking whether this is %ifndef
                        $exclude = !$exclude;
                    }
                    if ($exclude) {
                        $start = $i;
                        $start_lineno = $lineno;
                    }
                }
                //for ($j = $i; $j < strlen($z) && $z[$j] != "\n"; $j++) $z[$j] = ' ';
                $j = strpos(substr($z, $i), "\n");
                if ($j === false) {
                    $z = substr($z, 0, $i); // remove instead of adding ' '
                } else {
                    $z = substr($z, 0, $i) . substr($z, $i + $j); // remove instead of adding ' '
                }
            }
        }
        if ($exclude) {
            throw new Exception("unterminated %ifdef starting on line $start_lineno\n");
        }
    }

    /**
     * In spite of its name, this function is really a scanner.
     *
     * It reads in the entire input file (all at once) then tokenizes it.
     * Each token is passed to the function "parseonetoken" which builds all
     * the appropriate data structures in the global state vector "gp".
     * @param LemonData
     */
    function Parse($gp)
    {
        $startline = 0;

        $this->gp = $gp;
        $this->filename = $gp->filename;
        $this->errorcnt = 0;
        $this->state = self::INITIALIZE;

        /* Begin by reading the input file */
        $filebuf = file_get_contents($this->filename);
        if (!$filebuf) {
            Lemon::ErrorMsg($this->filename, 0, "Can't open this file for reading.");
            $gp->errorcnt++;
            return;
        }
        if (filesize($this->filename) != strlen($filebuf)) {
            ErrorMsg($this->filename, 0, "Can't read in all %d bytes of this file.",
                filesize($this->filename));
            $gp->errorcnt++;
            return;
        }

        /* Make an initial pass through the file to handle %ifdef and %ifndef */
        $this->preprocess_input($filebuf);

        /* Now scan the text of the input file */
        $lineno = 1;
        for ($cp = 0, $c = $filebuf[0]; $cp < strlen($filebuf); $cp++) {
            $c = $filebuf[$cp];
            if ($c == "\n") $lineno++;              /* Keep track of the line number */
            if (trim($c) === '') {
                continue;
            }  /* Skip all white space */
            if ($filebuf[$cp] == '/' && ($cp + 1 < strlen($filebuf)) && $filebuf[$cp + 1] == '/') {
                /* Skip C++ style comments */
                $cp += 2;
                $z = strpos(substr($filebuf, $cp), "\n");
                if ($z === false) {
                    $cp = strlen($filebuf);
                    break;
                }
                $lineno++;
                $cp += $z;
                continue;
            }
            if ($filebuf[$cp] == '/' && ($cp + 1 < strlen($filebuf)) && $filebuf[$cp + 1] == '*') {
                /* Skip C style comments */
                $cp += 2;
                $z = strpos(substr($filebuf, $cp), '*/');
                if ($z !== false) {
                    $lineno += count(explode("\n", substr($filebuf, $cp, $z))) - 1;
                }
                $cp += $z + 1;
                continue;
            }
            $this->tokenstart = $cp;                /* Mark the beginning of the token */
            $this->tokenlineno = $lineno;           /* Linenumber on which token begins */
            if ($filebuf[$cp] == '"') {                     /* String literals */
                $cp++;
                $oldcp = $cp;
                $test = strpos(substr($filebuf, $cp), '"');
                if ($test === false) {
                    Lemon::ErrorMsg($this->filename, $startline,
                    "String starting on this line is not terminated before the end of the file.");
                    $this->errorcnt++;
                    $nextcp = $cp = strlen($filebuf);
                } else {
                    $cp += $test;
                    $nextcp = $cp + 1;
                }
                $lineno += count(explode("\n", substr($filebuf, $oldcp, $cp - $oldcp))) - 1;
            } elseif ($filebuf[$cp] == '{') {               /* A block of C code */
                $cp++;
                for ($level = 1; $cp < strlen($filebuf) && ($level > 1 || $filebuf[$cp] != '}'); $cp++) {
                    if ($filebuf[$cp] == "\n") {
                        $lineno++;
                    } elseif ($filebuf[$cp] == '{') {
                        $level++;
                    } elseif ($filebuf[$cp] == '}') {
                        $level--;
                    } elseif ($filebuf[$cp] == '/' && $filebuf[$cp + 1] == '*') {
                        /* Skip comments */
                        $cp += 2;
                        $z = strpos(substr($filebuf, $cp), '*/');
                        if ($z !== false) {
                            $lineno += count(explode("\n", substr($filebuf, $cp, $z))) - 1;
                        }
                        $cp += $z + 2;
                    } elseif ($filebuf[$cp] == '/' && $filebuf[$cp + 1] == '/') {
                        /* Skip C++ style comments too */
                        $cp += 2;
                        $z = strpos(substr($filebuf, $cp), "\n");
                        if ($z === false) {
                            $cp = strlen($filebuf);
                            break;
                        } else {
                            $lineno++;
                        }
                        $cp += $z;
                    } elseif ($filebuf[$cp] == "'" || $filebuf[$cp] == '"') {
                        /* String a character literals */
                        $startchar = $filebuf[$cp];
                        $prevc = 0;
                        for ($cp++; $cp < strlen($filebuf) && ($filebuf[$cp] != $startchar || $prevc === '\\'); $cp++) {
                            if ($filebuf[$cp] == "\n") {
                                $lineno++;
                            }
                            if ($prevc === '\\') {
                                $prevc = 0;
                            } else {
                                $prevc = $filebuf[$cp];
                            }
                        }
                    }
                }
                if ($cp >= strlen($filebuf)) {
                    Lemon::ErrorMsg($this->filename, $this->tokenlineno,
                        "PHP code starting on this line is not terminated before the end of the file.");
                    $this->errorcnt++;
                    $nextcp = $cp;
                } else {
                    $nextcp = $cp + 1;
                }
            } elseif (preg_match('/[a-zA-Z0-9]/', $filebuf[$cp])) {
                /* Identifiers */
                preg_match('/[a-zA-Z0-9_]+/', substr($filebuf, $cp), $preg_results);
                $cp += strlen($preg_results[0]);
                $nextcp = $cp;
            } elseif ($filebuf[$cp] == ':' && $filebuf[$cp + 1] == ':' &&
                      $filebuf[$cp + 2] == '=') {
                /* The operator "::=" */
                $cp += 3;
                $nextcp = $cp;
            } elseif (($filebuf[$cp] == '/' || $filebuf[$cp] == '|') &&
                      preg_match('/[a-zA-Z]/', $filebuf[$cp + 1])) {
                $cp += 2;
                preg_match('/[a-zA-Z0-9_]+/', substr($filebuf, $cp), $preg_results);
                $cp += strlen($preg_results[0]);
                $nextcp = $cp;
            } else {
                /* All other (one character) operators */
                $cp ++;
                $nextcp = $cp;
            }
            $this->parseonetoken(substr($filebuf, $this->tokenstart,
                $cp - $this->tokenstart)); /* Parse the token */
            $cp = $nextcp - 1;
        }
        $gp->rule = $this->firstrule;
        $gp->errorcnt = $this->errorcnt;
    }

    /**
     * Parse a single token
     * @param string token
     */
    function parseonetoken($token)
    {
        $x = $token;
        $this->a = 0; // for referencing in WAITING_FOR_DECL_KEYWORD
        if (DEBUG) {
            printf("%s:%d: Token=[%s] state=%d\n",
                $this->filename, $this->tokenlineno, $token, $this->state);
        }
        switch ($this->state) {
            case self::INITIALIZE:
                $this->prevrule = 0;
                $this->preccounter = 0;
                $this->firstrule = $this->lastrule = 0;
                $this->gp->nrule = 0;
                /* Fall thru to next case */
            case self::WAITING_FOR_DECL_OR_RULE:
                if ($x[0] == '%') {
                    $this->state = self::WAITING_FOR_DECL_KEYWORD;
                } elseif (preg_match('/[a-z]/', $x[0])) {
                    $this->lhs = LemonSymbol::Symbol_new($x);
                    $this->nrhs = 0;
                    $this->lhsalias = 0;
                    $this->state = self::WAITING_FOR_ARROW;
                } elseif ($x[0] == '{') {
                    if ($this->prevrule === 0) {
                        Lemon::ErrorMsg($this->filename, $this->tokenlineno,
                            "There is no prior rule opon which to attach the code
                             fragment which begins on this line.");
                        $this->errorcnt++;
                    } elseif ($this->prevrule->code != 0) {
                        Lemon::ErrorMsg($this->filename, $this->tokenlineno,
                            "Code fragment beginning on this line is not the first \
                             to follow the previous rule.");
                        $this->errorcnt++;
                    } else {
                        $this->prevrule->line = $this->tokenlineno;
                        $this->prevrule->code = substr($x, 1);
                    }
                } elseif ($x[0] == '[') {
                    $this->state = self::PRECEDENCE_MARK_1;
                } else {
                    Lemon::ErrorMsg($this->filename, $this->tokenlineno,
                    "Token \"%s\" should be either \"%%\" or a nonterminal name.",
                    $x);
                    $this->errorcnt++;
                }
                break;
            case self::PRECEDENCE_MARK_1:
                if (!preg_match('/[A-Z]/', $x[0])) {
                    Lemon::ErrorMsg($this->filename, $this->tokenlineno,
                        "The precedence symbol must be a terminal.");
                    $this->errorcnt++;
                } elseif ($this->prevrule === 0) {
                    Lemon::ErrorMsg($this->filename, $this->tokenlineno,
                        "There is no prior rule to assign precedence \"[%s]\".", $x);
                    $this->errorcnt++;
                } elseif ($this->prevrule->precsym != 0) {
                    Lemon::ErrorMsg($this->filename, $this->tokenlineno,
                        "Precedence mark on this line is not the first to follow the previous rule.");
                    $this->errorcnt++;
                } else {
                    $this->prevrule->precsym = LemonSymbol::Symbol_new($x);
                }
                $this->state = self::PRECEDENCE_MARK_2;
                break;
            case self::PRECEDENCE_MARK_2:
                if ($x[0] != ']') {
                    Lemon::ErrorMsg($this->filename, $this->tokenlineno,
                        "Missing \"]\" on precedence mark.");
                    $this->errorcnt++;
                }
                $this->state = self::WAITING_FOR_DECL_OR_RULE;
                break;
            case self::WAITING_FOR_ARROW:
                if ($x[0] == ':' && $x[1] == ':' && $x[2] == '=') {
                    $this->state = self::IN_RHS;
                } elseif ($x[0] == '(') {
                    $this->state = self::LHS_ALIAS_1;
                } else {
                    Lemon::ErrorMsg($this->filename, $this->tokenlineno,
                        "Expected to see a \":\" following the LHS symbol \"%s\".",
                    $this->lhs->name);
                    $this->errorcnt++;
                    $this->state = self::RESYNC_AFTER_RULE_ERROR;
                }
                break;
            case self::LHS_ALIAS_1:
                if (preg_match('/[A-Za-z]/', $x[0])) {
                    $this->lhsalias = $x;
                    $this->state = self::LHS_ALIAS_2;
                } else {
                    Lemon::ErrorMsg($this->filename, $this->tokenlineno,
                        "\"%s\" is not a valid alias for the LHS \"%s\"\n",
                        $x, $this->lhs->name);
                    $this->errorcnt++;
                    $this->state = self::RESYNC_AFTER_RULE_ERROR;
                }
                break;
            case self::LHS_ALIAS_2:
                if ($x[0] == ')') {
                    $this->state = self::LHS_ALIAS_3;
                } else {
                    Lemon::ErrorMsg($this->filename, $this->tokenlineno,
                        "Missing \")\" following LHS alias name \"%s\".",$this->lhsalias);
                    $this->errorcnt++;
                    $this->state = self::RESYNC_AFTER_RULE_ERROR;
                }
                break;
            case self::LHS_ALIAS_3:
                if ($x == '::=') {
                    $this->state = self::IN_RHS;
                } else {
                    Lemon::ErrorMsg($this->filename, $this->tokenlineno,
                        "Missing \"->\" following: \"%s(%s)\".",
                    $this->lhs->name, $this->lhsalias);
                    $this->errorcnt++;
                    $this->state = self::RESYNC_AFTER_RULE_ERROR;
                }
                break;
            case self::IN_RHS:
                if ($x[0] == '.') {
                    $rp = new LemonRule;
                    $rp->ruleline = $this->tokenlineno;
                    for ($i = 0; $i < $this->nrhs; $i++) {
                        $rp->rhs[$i] = $this->rhs[$i];
                        $rp->rhsalias[$i] = $this->alias[$i];
                    }
                    $rp->lhs = $this->lhs;
                    $rp->lhsalias = $this->lhsalias;
                    $rp->nrhs = $this->nrhs;
                    $rp->code = 0;
                    $rp->precsym = 0;
                    $rp->index = $this->gp->nrule++;
                    $rp->nextlhs = $rp->lhs->rule;
                    $rp->lhs->rule = $rp;
                    $rp->next = 0;
                    if ($this->firstrule === 0) {
                        $this->firstrule = $this->lastrule = $rp;
                    } else {
                        $this->lastrule->next = $rp;
                        $this->lastrule = $rp;
                    }
                    $this->prevrule = $rp;
                    $this->state = self::WAITING_FOR_DECL_OR_RULE;
                } elseif (preg_match('/[a-zA-Z]/', $x[0])) {
                    if ($this->nrhs >= Lemon::MAXRHS) {
                        Lemon::ErrorMsg($this->filename, $this->tokenlineno,
                            "Too many symbols on RHS or rule beginning at \"%s\".",
                            $x);
                        $this->errorcnt++;
                        $this->state = self::RESYNC_AFTER_RULE_ERROR;
                    } else {
                        if (isset($this->rhs[$this->nrhs - 1])) {
                            $msp = $this->rhs[$this->nrhs - 1];
                            if ($msp->type == LemonSymbol::MULTITERMINAL) {
                                $inf = array_reduce($msp->subsym,
                                    array($this, '_printmulti'), '');
                                Lemon::ErrorMsg($this->filename, $this->tokenlineno,
                                    'WARNING: symbol ' . $x . ' will not' .
                                    ' be part of previous multiterminal %s',
                                    substr($inf, 0, strlen($inf) - 1)
                                    );
                            }
                        }
                        $this->rhs[$this->nrhs] = LemonSymbol::Symbol_new($x);
                        $this->alias[$this->nrhs] = 0;
                        $this->nrhs++;
                    }
                } elseif (($x[0] == '|' || $x[0] == '/') && $this->nrhs > 0) {
                    $msp = $this->rhs[$this->nrhs - 1];
                    if ($msp->type != LemonSymbol::MULTITERMINAL) {
                        $origsp = $msp;
                        $msp = new LemonSymbol;
                        $msp->type = LemonSymbol::MULTITERMINAL;
                        $msp->nsubsym = 1;
                        $msp->subsym = array($origsp);
                        $msp->name = $origsp->name;
                        $this->rhs[$this->nrhs - 1] = $msp;
                    }
                    $msp->nsubsym++;
                    $msp->subsym[$msp->nsubsym - 1] = LemonSymbol::Symbol_new(substr($x, 1));
                    if (preg_match('/[a-z]/', $x[1]) ||
                          preg_match('/[a-z]/', $msp->subsym[0]->name[0])) {
                        Lemon::ErrorMsg($this->filename, $this->tokenlineno,
                        "Cannot form a compound containing a non-terminal");
                        $this->errorcnt++;
                    }
                } elseif ($x[0] == '(' && $this->nrhs > 0) {
                    $this->state = self::RHS_ALIAS_1;
                } else {
                    Lemon::ErrorMsg($this->filename, $this->tokenlineno,
                        "Illegal character on RHS of rule: \"%s\".", $x);
                    $this->errorcnt++;
                    $this->state = self::RESYNC_AFTER_RULE_ERROR;
                }
                break;
            case self::RHS_ALIAS_1:
                if (preg_match('/[A-Za-z]/', $x[0])) {
                    $this->alias[$this->nrhs - 1] = $x;
                    $this->state = self::RHS_ALIAS_2;
                } else {
                    Lemon::ErrorMsg($this->filename, $this->tokenlineno,
                        "\"%s\" is not a valid alias for the RHS symbol \"%s\"\n",
                        $x, $this->rhs[$this->nrhs - 1]->name);
                    $this->errorcnt++;
                    $this->state = self::RESYNC_AFTER_RULE_ERROR;
                }
                break;
            case self::RHS_ALIAS_2:
                if ($x[0] == ')') {
                    $this->state = self::IN_RHS;
                } else {
                    Lemon::ErrorMsg($this->filename, $this->tokenlineno,
                        "Missing \")\" following LHS alias name \"%s\".", $this->lhsalias);
                    $this->errorcnt++;
                    $this->state = self::RESYNC_AFTER_RULE_ERROR;
                }
                break;
            case self::WAITING_FOR_DECL_KEYWORD:
                if(preg_match('/[A-Za-z]/', $x[0])) {
                    $this->declkeyword = $x;
                    $this->declargslot = &$this->a;
                    $this->decllnslot = &$this->a;
                    $this->state = self::WAITING_FOR_DECL_ARG;
                    if ('name' == $x) {
                        $this->declargslot = &$this->gp->name;
                    } elseif ('include' == $x) {
                        $this->declargslot = &$this->gp->include_code;
                        $this->decllnslot = &$this->gp->includeln;
                    } elseif ('include_class' == $x) {
                        $this->declargslot = &$this->gp->include_classcode;
                        $this->decllnslot = &$this->gp->include_classln;
                    } elseif ('declare_class' == $x) {
                        $this->declargslot = &$this->gp->declare_classcode;
                        $this->decllnslot = &$this->gp->declare_classln;
                    } elseif ('code' == $x) {
                        $this->declargslot = &$this->gp->extracode;
                        $this->decllnslot = &$this->gp->extracodeln;
                    } elseif ('token_destructor' == $x) {
                        $this->declargslot = &$this->gp->tokendest;
                        $this->decllnslot = &$this->gp->tokendestln;
                    } elseif ('default_destructor' == $x) {
                        $this->declargslot = &$this->gp->vardest;
                        $this->decllnslot = &$this->gp->vardestln;
                    } elseif ('token_prefix' == $x) {
                        $this->declargslot = &$this->gp->tokenprefix;
                    } elseif ('syntax_error' == $x) {
                        $this->declargslot = &$this->gp->error;
                        $this->decllnslot = &$this->gp->errorln;
                    } elseif ('parse_accept' == $x) {
                        $this->declargslot = &$this->gp->accept;
                        $this->decllnslot = &$this->gp->acceptln;
                    } elseif ('parse_failure' == $x) {
                        $this->declargslot = &$this->gp->failure;
                        $this->decllnslot = &$this->gp->failureln;
                    } elseif ('stack_overflow' == $x) {
                        $this->declargslot = &$this->gp->overflow;
                        $this->decllnslot = &$this->gp->overflowln;
                    } else if('extra_argument' == $x) {
                        $this->declargslot = &$this->gp->arg;
                    } elseif ('token_type' == $x) {
                        $this->declargslot = &$this->gp->tokentype;
                    } elseif ('default_type' == $x) {
                        $this->declargslot = &$this->gp->vartype;
                    } elseif ('stack_size' == $x) {
                        $this->declargslot = &$this->gp->stacksize;
                    } elseif ('start_symbol' == $x) {
                        $this->declargslot = &$this->gp->start;
                    } elseif ('left' == $x) {
                        $this->preccounter++;
                        $this->declassoc = LemonSymbol::LEFT;
                        $this->state = self::WAITING_FOR_PRECEDENCE_SYMBOL;
                    } elseif ('right' == $x) {
                        $this->preccounter++;
                        $this->declassoc = LemonSymbol::RIGHT;
                        $this->state = self::WAITING_FOR_PRECEDENCE_SYMBOL;
                    } elseif ('nonassoc' == $x) {
                        $this->preccounter++;
                        $this->declassoc = LemonSymbol::NONE;
                        $this->state = self::WAITING_FOR_PRECEDENCE_SYMBOL;
                    } elseif ('destructor' == $x) {
                        $this->state = self::WAITING_FOR_DESTRUCTOR_SYMBOL;
                    } elseif ('type' == $x) {
                        $this->state = self::WAITING_FOR_DATATYPE_SYMBOL;
                    } elseif ('fallback' == $x) {
                        $this->fallback = 0;
                        $this->state = self::WAITING_FOR_FALLBACK_ID;
                    } else {
                        Lemon::ErrorMsg($this->filename, $this->tokenlineno,
                        "Unknown declaration keyword: \"%%%s\".", $x);
                        $this->errorcnt++;
                        $this->state = self::RESYNC_AFTER_DECL_ERROR;
                    }
                } else {
                    Lemon::ErrorMsg($this->filename, $this->tokenlineno,
                        "Illegal declaration keyword: \"%s\".", $x);
                    $this->errorcnt++;
                    $this->state = self::RESYNC_AFTER_DECL_ERROR;
                }
                break;
            case self::WAITING_FOR_DESTRUCTOR_SYMBOL:
                if (!preg_match('/[A-Za-z]/', $x[0])) {
                    Lemon::ErrorMsg($this->filename, $this->tokenlineno,
                        "Symbol name missing after %destructor keyword");
                    $this->errorcnt++;
                    $this->state = self::RESYNC_AFTER_DECL_ERROR;
                } else {
                    $sp = LemonSymbol::Symbol_new($x);
                    $this->declargslot = &$sp->destructor;
                    $this->decllnslot = &$sp->destructorln;
                    $this->state = self::WAITING_FOR_DECL_ARG;
                }
                break;
            case self::WAITING_FOR_DATATYPE_SYMBOL:
                if (!preg_match('/[A-Za-z]/', $x[0])) {
                    Lemon::ErrorMsg($this->filename, $this->tokenlineno,
                        "Symbol name missing after %destructor keyword");
                    $this->errorcnt++;
                    $this->state = self::RESYNC_AFTER_DECL_ERROR;
                } else {
                    $sp = LemonSymbol::Symbol_new($x);
                    $this->declargslot = &$sp->datatype;
                    $this->state = self::WAITING_FOR_DECL_ARG;
                }
                break;
            case self::WAITING_FOR_PRECEDENCE_SYMBOL:
                if ($x[0] == '.') {
                    $this->state = self::WAITING_FOR_DECL_OR_RULE;
                } elseif (preg_match('/[A-Z]/', $x[0])) {
                    $sp = LemonSymbol::Symbol_new($x);
                    if ($sp->prec >= 0) {
                        Lemon::ErrorMsg($this->filename, $this->tokenlineno,
                            "Symbol \"%s\" has already been given a precedence.", $x);
                        $this->errorcnt++;
                    } else {
                        $sp->prec = $this->preccounter;
                        $sp->assoc = $this->declassoc;
                    }
                } else {
                    Lemon::ErrorMsg($this->filename, $this->tokenlineno,
                        "Can't assign a precedence to \"%s\".", $x);
                    $this->errorcnt++;
                }
                break;
            case self::WAITING_FOR_DECL_ARG:
                if (preg_match('/[A-Za-z0-9{"]/', $x[0])) {
                    if ($this->declargslot != 0) {
                        Lemon::ErrorMsg($this->filename, $this->tokenlineno,
                            "The argument \"%s\" to declaration \"%%%s\" is not the first.",
                            $x[0] == '"' ? substr($x, 1) : $x, $this->declkeyword);
                        $this->errorcnt++;
                        $this->state = self::RESYNC_AFTER_DECL_ERROR;
                    } else {
                        $this->declargslot = ($x[0] == '"' || $x[0] == '{') ? substr($x, 1) : $x;
                        $this->a = 1;
                        if (!$this->decllnslot) {
                            $this->decllnslot = $this->tokenlineno;
                        }
                        $this->state = self::WAITING_FOR_DECL_OR_RULE;
                    }
                } else {
                    Lemon::ErrorMsg($this->filename, $this->tokenlineno,
                        "Illegal argument to %%%s: %s",$this->declkeyword, $x);
                    $this->errorcnt++;
                    $this->state = self::RESYNC_AFTER_DECL_ERROR;
                }
                break;
            case self::WAITING_FOR_FALLBACK_ID:
                if ($x[0] == '.') {
                    $this->state = self::WAITING_FOR_DECL_OR_RULE;
                } elseif (!preg_match('/[A-Z]/', $x[0])) {
                    Lemon::ErrorMsg($this->filename, $this->tokenlineno,
                    "%%fallback argument \"%s\" should be a token", $x);
                    $this->errorcnt++;
                } else {
                    $sp = LemonSymbol::Symbol_new($x);
                    if ($this->fallback === 0) {
                        $this->fallback = $sp;
                    } elseif (is_object($sp->fallback)) {
                        Lemon::ErrorMsg($this->filename, $this->tokenlineno,
                        "More than one fallback assigned to token %s", $x);
                        $this->errorcnt++;
                    } else {
                        $sp->fallback = $this->fallback;
                        $this->gp->has_fallback = 1;
                    }
                }
                break;
            case self::RESYNC_AFTER_RULE_ERROR:
            /*      if ($x[0] == '.') $this->state = self::WAITING_FOR_DECL_OR_RULE;
            **      break; */
            case self::RESYNC_AFTER_DECL_ERROR:
                if ($x[0] == '.') {
                    $this->state = self::WAITING_FOR_DECL_OR_RULE;
                }
                if ($x[0] == '%') {
                    $this->state = self::WAITING_FOR_DECL_KEYWORD;
                }
                break;
        }
    }

    function _printmulti($a, $b)
    {
        if (!$a) {
            $a = '';
        }
        $a .= $b->name . '|';
        return $a;
    }
}
