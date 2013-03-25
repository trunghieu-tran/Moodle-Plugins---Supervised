<?
require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/config.php');
require_once(dirname(dirname(dirname(__FILE__))) . '/model.php');
require_once('kd_xmlrpc.php');
require_once('xmlrpc_functions.php');

// if page is opened via xmlrpc, process data
$xmlrpc_request = XMLRPC_parse($GLOBALS['HTTP_RAW_POST_DATA']);
if ($xmlrpc_request) {
    $methodName = XMLRPC_getMethodName($xmlrpc_request);
    $params = XMLRPC_getParams($xmlrpc_request);
    if (function_exists($methodName)) {
        call_user_func_array($methodName, $params);
    }
    else {
        XMLRPC_response(XMLRPC_prepare("404 Not found"));
    }
}
else {
    XMLRPC_response(XMLRPC_prepare("404 Not found"));
}
