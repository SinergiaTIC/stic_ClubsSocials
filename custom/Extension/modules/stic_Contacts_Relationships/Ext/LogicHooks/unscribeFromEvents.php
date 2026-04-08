<?php
//prevents directly accessing this file from a web browser
if (!defined('sugarEntry') || !sugarEntry) {
    die('Not A Valid Entry Point');
}
$hook_array['after_save'][] = array(100, 'after_save', 'custom/Extension/modules/stic_Contacts_Relationships/Ext/LogicHooksCode/unscribeFromEvents.php', 'stic_Contacts_RelationshipsUnscribeFromEvents', 'unscribeRegistrations');



