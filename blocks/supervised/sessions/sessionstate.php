<?php
/**
 * Class StateSession
 *
 * Describes possible session states
 */
class StateSession
{
    const Planned   = 1;
    const Active    = 2;
    const Finished  = 3;

    /**
     * Converts session state to string
     *
     * @param $val  integer session state
     * @return string string representation of the session state
     */
    public static function getStateName($val){
        //todo use strings from lang file
        switch($val){
            case 1:
                return get_string('plannedstate', 'block_supervised');
                break;
            case 2:
                return get_string('activestate', 'block_supervised');
                break;
            case 3:
                return get_string('finishedstate', 'block_supervised');
                break;
            default:
                return get_string('unknownstate', 'block_supervised');
        }
    }
}