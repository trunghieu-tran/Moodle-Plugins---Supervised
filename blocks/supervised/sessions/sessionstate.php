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
                return "Planned";
                break;
            case 2:
                return "Active";
                break;
            case 3:
                return "Finished";
                break;
            default:
                return "unknown";
        }
    }
}