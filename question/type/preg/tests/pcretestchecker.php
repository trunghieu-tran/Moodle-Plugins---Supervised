<?php

defined('MOODLE_INTERNAL') || die();

class qtype_preg_pcre_test_checker extends PHPUnit_Framework_TestCase {

    // Different sources of test data.
    const TAG_FROM_NFA           = 1;
    const TAG_FROM_DFA           = 2;
    const TAG_FROM_BACKTRACKING  = 4;
    const TAG_FROM_PCRE          = 8;
    const TAG_FROM_ATT           = 16;

    // TODO: tags for different capabilities for matchers.

    protected $passcount;              // Number of passes.
    protected $failcount;              // Number of fails.
    protected $skipcount;              // Number of skipped tests.
    protected $exceptionscount;        // Number of exceptions during testing.
    protected $testdataobjects;        // Objects with test data.

    public function __construct() {
        $this->passcount = 0;
        $this->failcount = 0;
        $this->skipcount = 0;
        $this->exceptionscount = 0;
        $this->testdataobjects = array();

        $testdir = dirname(__FILE__) . '/';
        $pregdir = dirname($testdir) . '/';

        // Find all available test files.
        $dh = opendir($testdir);
        if (!$dh) {
            return;
        }

        // Include files with test data.
        while ($file = readdir($dh)) {
            if (strpos($file, 'cross_tests_') !== 0 || pathinfo($file, PATHINFO_EXTENSION) !== 'php') {
                continue;
            }
            // Test data file found.
            require_once($testdir . $file);
            $classname = 'qtype_preg_' . pathinfo($file, PATHINFO_FILENAME);
            $this->testdataobjects[] = new $classname;
        }
        closedir($dh);
    }

    function execute_pcretester($methodname, $data) {
        $descriptorspec = array(0 => array('pipe', 'r'),  // Stdin is a pipe that the child will read from.
                                1 => array('pipe', 'w'),  // Stdout is a pipe that the child will write to.
                                2 => array('pipe', 'w')); // Stderr is a pipe that the child will write to.

        // Prepare test input
        $input = '';

        $regex = $data['regex'];
        $options = 0;
        $tests = $data['tests'];
        if (array_key_exists('modifiers', $data)) {
            $options = qtype_preg_handling_options::string_to_modifiers($data['modifiers']);
        }

        $input .= core_text::strlen($methodname) . "\n";    // method name length
        $input .= $methodname . "\n";                       // method name itself
        $input .= core_text::strlen($regex) . "\n";         // regex length
        $input .= $regex . "\n";                            // regex itself
        $input .= $options . "\n";                          // regex options
        $input .= count($tests) . "\n";                     // tests count
        foreach ($tests as $test) {
            $str = $test['str'];
            $index = $test['index_first'];
            $length = $test['length'];

            $input .= core_text::strlen($str) . "\n";   // string length
            $input .= $str . "\n";                      // string itself

            $input .= count($index) . "\n";             // number of matched subexpressions
            foreach ($index as $key => $ind) {
                $input .= "$key $ind {$length[$key]}\n";    // subexpr index length
            }
        }

        $executable = dirname(__FILE__) . '/pcretestchecker';
        $process = proc_open($executable, $descriptorspec, $pipes);
        $output = '';
        if (is_resource($process)) {
            fwrite($pipes[0], $input);
            fclose($pipes[0]);

            $output = stream_get_contents($pipes[1]);
            $err = stream_get_contents($pipes[2]);
            fclose($pipes[1]);
            fclose($pipes[2]);

            proc_close($process);
        }
        return $output;
    }

    /**
     * The main function - runs all matchers on test-data sets.
     */
    function test() {
        $output = '';
        foreach ($this->testdataobjects as $testdataobj) {
            $testmethods = get_class_methods($testdataobj);
            $classname = get_class($testdataobj);
            foreach ($testmethods as $methodname) {
                // Filtering class methods by names. A test method name should start with 'data_for_test_'.
                if (strpos($methodname, 'data_for_test_') !== 0) {
                    continue;
                }

                $data = $testdataobj->$methodname();
                $output .= $this->execute_pcretester($methodname, $data);
            }
        }

        echo $output;
    }
}
