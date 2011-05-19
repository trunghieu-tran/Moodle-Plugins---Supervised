<?php

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once('poasassignment_tabbed_page.php');
$poasassignmenttabbedpageinstance = new poasassignment_tabbed_page();
$poasassignmenttabbedpageinstance->view(); // Display page
