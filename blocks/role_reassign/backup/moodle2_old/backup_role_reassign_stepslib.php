<?php
class backup_role_reassign_block_structure_step extends backup_block_structure_step {

    protected function define_structure() {
 
        // To know if we are including userinfo
        $userinfo = $this->get_setting_value('userinfo');
 
        $block = $DB->get_record('block_instancs', array('id' => $this->task->get_blockid()));
        
        
        // Define each element separated
        $role_reassign = new backup_nested_element('role_reassign', array('id'), null);
        
        $rules = new backup_nested_element('rules');
        
        $rule = new backup_nested_element('rule', array('id'), array(
            'destroleid', 'restorable', 'eventname', 'restoreeventname', 'name'));
            
        $sourceroles = new backup_nested_element('sourceroles');
        
        $sourcerole = new backup_nested_element('sourcerole', array('id'), array(
            'ruleid', 'roleid'));
        
        $groups = new backup_nested_element('groups');
        
        $group = new backup_nested_element('group', array('id'), array(
            'ruleid', 'groupid'));
            
        $instances = new backup_nested_element('instances');
        
        $isntance = new backup_nested_element('instance', array('id'), array(
            'ruleid', 'instanceid'));
        
        // Build the tree
        $role_reassign->add_child($rules);
        $rules->add_child($rule);
        
        $role_reassign->add_child($sourceroles);
        $sourceroles->add_child($sourcerole);
        
        $role_reassign->add_child($groups);
        $groups->add_child($group);
        
        $role_reassign->add_child($instances);
        $instances->add_child($instance);
        
        
        
        // Define sources
        $role_reassign->set_source_array(array((object)array('id' => $this->task->get_blockid())));
 
        // Define id annotations
 
        // Define file annotations (none)
 
        // Return the root element (choice), wrapped into standard activity structure
        return $this->prepare_block_structure($role_reassign);
    }
}