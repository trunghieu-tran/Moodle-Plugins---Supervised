<?
class gradertest_qtype extends default_questiontype {
    function name() {
        return 'gradertest';
    }
}
question_register_questiontype(new gradertest_qtype());