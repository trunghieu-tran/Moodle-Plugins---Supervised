<?PHP // $Id: version.php,v 1.1.2.3 2009/10/04 19:49:58 oasychev Exp $

$plugin->component = 'qtype_preg';
$plugin->version  = 2012011300;
$plugin->requires = 2011070100;
$plugin->release = 'Preg 2.2';
$plugin->maturity = MATURITY_STABLE;

$plugin->dependencies = array(
    'qtype_shortanswer' => 2011102700,
    'qbehaviour_adaptivehints' => 2011111902,
    'qbehaviour_adaptivehintsnopenalties' => 2011111902
);
?>
