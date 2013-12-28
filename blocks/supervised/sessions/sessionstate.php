<?php
class StateSession
{
    const Planned   = 1;
    const Active    = 2;
    const Finished  = 3;
    
    public static function getStateName($val){
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