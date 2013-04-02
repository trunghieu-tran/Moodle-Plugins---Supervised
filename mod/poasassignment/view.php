<?php
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once('poasassignment_tabbed_page.php');
$pagemanager = new poasassignment_tabbed_page();
$pagemanager->view(); // Display page