<?PHP // $Id: version.php,v 1.1.2.3 2009/10/04 19:49:58 oasychev Exp $

$plugin->component = 'qbehaviour_adaptivehintsnopenalties';
$plugin->version  = 2013011800;
$plugin->requires = 2012062500;
$plugin->release = 'Adaptive with hints (no penalties) behaviour 2.3.1';
$plugin->maturity = MATURITY_STABLE;

$plugin->dependencies = array(
    'qbehaviour_adaptivehints' => 2013011800,
);
?>
