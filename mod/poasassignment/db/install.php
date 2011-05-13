<?php

/**
 * Post installation procedure
 */
function xmldb_poasassignment_install() {
    global $DB;
    
    // Add message provider
    $provider = new stdClass();
    $provider->name = 'poasassignment_updates';
    $provider->component = 'mod_poasassignment';
    if (!$DB->record_exists('message_providers', array('name' => $provider->name, 
                                                       'component' => $provider->component))) {
        $DB->insert_record('message_providers', $provider);
    }
}
