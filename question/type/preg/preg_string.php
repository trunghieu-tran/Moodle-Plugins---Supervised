<?php

/**
 * Defines string class.
 *
 * @copyright &copy; 2012  Valeriy Streltsov
 * @author Valeriy Streltsov, Volgograd State Technical University
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package questions
 */

require_once($CFG->dirroot . '/question/type/preg/preg_unicode.php');

class qtype_preg_string implements ArrayAccess {
    /** @var string the utf-8 string itself */
    private $fstring;
    /**@var int length of the string, calculated when string is modified */
    private $flength;

    public function __construct($str = '') {
        $this->set_string($str);
    }

    public function __toString() {
        return $this->fstring;
    }

    public function set_string($str) {
        $this->fstring = $str;
        $this->flength = qtype_preg_unicode::strlen($str);
    }

    public function string() {
        return $this->fstring;
    }

    public function length() {
        return $this->flength;
    }

    /**
     * Concatenates a string.
     * @param mixed a string to concatenate (can be either an object of qtype_preg_string or a simple string).
     */
    public function concatenate($str) {
        if (is_a($str, 'qtype_preg_string')) {
            $this->fstring .= $str->fstring;
            $this->flength += $str->flength;
        } else {
            $this->fstring .= $str;
            $this->flength += qtype_preg_unicode::strlen($str);
        }
    }

    public function offsetExists($offset) {
        return ($offset >= 0 && $offset < $this->flength);
    }

    public function offsetGet($offset) {
        if ($this->offsetExists($offset)) {
            return qtype_preg_unicode::substr($this->fstring, $offset, 1);
        } else {
            return null;
        }
    }

    public function offsetSet($offset, $value) {
        if ($this->offsetExists($offset)) {
            // Modify a character.
            $part1 = qtype_preg_unicode::substr($this->fstring, 0, $offset);
            $part2 = qtype_preg_unicode::substr($this->fstring, $offset + 1, $this->flength - $offset - 1);
            $this->fstring = $part1 . $value . $part2;
        } else if ($offset === $this->flength) {
            // Concatenate a character.
            $this->concatenate($value);
        }
    }

    public function offsetUnset($offset) {
        // Do nothing.
    }

}
