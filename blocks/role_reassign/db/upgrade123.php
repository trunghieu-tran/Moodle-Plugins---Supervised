<?php
function xmldb_block_role_reassign_upgrade($oldversion = 0) {
    global $DB;
    $dbman = $DB->get_manager();
    
    $result = true;
    if ($oldversion < 2007101526) {

        
        // Define table role_reassign_rules to be created
        $table = new xmldb_table('role_reassign_rules');

        // Adding fields to table role_reassign_rules
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('destroleid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('restorable', XMLDB_TYPE_BINARY, 'small', null, XMLDB_NOTNULL, null, null);
        $table->add_field('eventname', XMLDB_TYPE_CHAR, '166', null, XMLDB_NOTNULL, null, null);
        $table->add_field('restoreeventname', XMLDB_TYPE_CHAR, '166', null, XMLDB_NOTNULL, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table role_reassign_rules
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for role_reassign_rules
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
        
         // Define table role_reassign_source_roles to be created
        $table = new xmldb_table('role_reassign_source_roles');

        // Adding fields to table role_reassign_source_roles
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('ruleid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('roleid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);

        // Adding keys to table role_reassign_source_roles
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for role_reassign_source_roles
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
        
         // Define table role_reassign_instances to be created
        $table = new xmldb_table('role_reassign_instances');

        // Adding fields to table role_reassign_instances
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('ruleid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('instanceid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);

        // Adding keys to table role_reassign_instances
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for role_reassign_instances
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }
        
        // Define table role_reassign_user to be created
        $table = new xmldb_table('role_reassign_user');

        // Adding fields to table role_reassign_user
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('instanceid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('attemptid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('roleid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('eventname', XMLDB_TYPE_CHAR, '166', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table role_reassign_user
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for role_reassign_user
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // role_reassign savepoint reached
        upgrade_block_savepoint(true, 2007101526, 'role_reassign');
    }
}
