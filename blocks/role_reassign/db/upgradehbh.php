<?php
function xmldb_block_role_reassign_upgrade($oldversion = 0) {
    global $DB;
    $dbman = $DB->get_manager();
    
    $result = true;
    if ($oldversion < 2007101541) {
        
        // Define table role_reassign_groups to be created
        $table = new xmldb_table('role_reassign_groups');

        // Adding fields to table role_reassign_groups
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('ruleid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('groupid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);

        // Adding keys to table role_reassign_groups
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for role_reassign_groups
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // role_reassign savepoint reached
        upgrade_block_savepoint(true, 2007101541, 'role_reassign');
        
    }
}
