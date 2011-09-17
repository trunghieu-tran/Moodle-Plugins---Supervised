<?php

class preg_error {

    //Human-understandable error message
    public $errormsg;
    //
    public $index_first;
    //
    public $index_last;
    
    protected function highlight_regex($regex, $indfirst, $indlast) {
        return substr($regex, 0, $indfirst) . '<b>' . substr($regex, $indfirst, $indlast-$indfirst+1) . '</b>' . substr($regex, $indlast + 1);
    }

}

class preg_parsing_error extends preg_error {

    public function __construct($regex, $parsernode) {
        $this->index_first = $parsernode->firstindxs[0];
        $this->index_last = $parsernode->lastindxs[0];
        $this->errormsg = $this->highlight_regex($regex, $this->index_first, $this->index_last) . '<br/>' . $parsernode->error_string();
    }

}

class preg_accepting_error extends preg_error {

    public function __construct($regex, $matcher, $nodename, $indexes) {
        $a = new stdClass;
        $a->nodename = $nodename;
        $a->indfirst = $indexes['start'];
        $a->indlast = $indexes['end'];
        $a->engine = get_string($matcher->name(), 'qtype_preg');
        $this->index_first = $a->indfirst;
        $this->index_last = $a->indlast;
        $this->errormsg = $this->highlight_regex($regex, $this->index_first, $this->index_last) . '<br/>' . get_string('unsupported','qtype_preg',$a);
    }

}

class preg_modifier_error extends preg_error {

    public function __construct($matcher, $modifier) {
        $a = new stdClass;
        $a->modifier = $modifier;
        $a->classname = $matcher->name();
        $this->errormsg = get_string('unsupportedmodifier','qtype_preg',$a);
    }

}