<?php
if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

class stic_EventsSyncRegistrations {
    protected static $processed_contacts = array();

    public function syncRegistrations($bean, $event, $arguments) {
        if (!empty($GLOBALS['stic_sync_in_progress'])) return;

        $log = $GLOBALS['log'];
        $log->debug("STIC_SYNC: Iniciando para Evento: " . $bean->name);
        if (!empty($bean->stic_cs_inherit_reg_c) && !empty($bean->stic_events_stic_events_1stic_events_ida) && $bean->status === 'active') {
            $this->syncRegistrationsLogic($bean->stic_events_stic_events_1stic_events_ida, $bean->id);
        } else {
            $log->debug("STIC_SYNC: Sincronización omitida. Status actual: " . $bean->status);
        }
        $this->replicateToChildren($bean);
    }

    private function replicateToChildren($parent_bean) {
        $rel_link_name = 'stic_events_stic_events_1'; 
        if ($parent_bean->load_relationship($rel_link_name)) {
            $children_ids = $parent_bean->$rel_link_name->get();
            foreach ($children_ids as $child_id) {
                $child = BeanFactory::getBean('stic_Events', $child_id);
                if ($child && !empty($child->stic_cs_inherit_reg_c) && $child->status === 'active') {
                    $this->syncRegistrationsLogic($parent_bean->id, $child_id);
                }
            }
        }
    }

    private function syncRegistrationsLogic($parent_id, $child_id) {
        $GLOBALS['stic_sync_in_progress'] = true;
        $log = $GLOBALS['log'];
    
        $parent = BeanFactory::getBean('stic_Events', $parent_id);
        $child = BeanFactory::getBean('stic_Events', $child_id);
        
        if (!$parent || !$child) {
            $GLOBALS['stic_sync_in_progress'] = false;
            return;
        }
    
        $rel_name = 'stic_registrations_stic_events';
        $parent->load_relationship($rel_name);
        $child->load_relationship($rel_name);
    
        $parent_regs = $parent->$rel_name->getBeans();
        $child_regs = $child->$rel_name->getBeans();
    
        foreach ($parent_regs as $p_reg) {


            $allowed_statuses = array('confirmed', 'participates');
            if (!in_array($p_reg->status, $allowed_statuses)) {
                continue;
            }

            $contact_id = $p_reg->stic_registrations_contactscontacts_ida; 
            
            if (empty($contact_id)) {
                continue;
            }

            $process_key = $child_id . "_" . $contact_id;
            if (isset(self::$processed_contacts[$process_key])) continue;
            self::$processed_contacts[$process_key] = true;

            $matched_child_reg = null;
            foreach ($child_regs as $c_reg) {
                if ($c_reg->stic_registrations_contactscontacts_ida == $contact_id) {
                    $matched_child_reg = $c_reg;
                    break;
                }
            }
            if ($matched_child_reg) {
                if ($matched_child_reg->status != $p_reg->status) {
                    $log->debug("STIC_SYNC: Actualizando estado a {$p_reg->status} para contacto {$contact_id} en evento hijo");
                    $matched_child_reg->status = $p_reg->status;
                    $matched_child_reg->save();
                }
            } else {
                $new_reg = BeanFactory::newBean('stic_Registrations');

                $new_reg->deleted = 0; 
                $new_reg->name = $p_reg->stic_registrations_contacts_name . " - " . $child->name ;
                $new_reg->status = $p_reg->status;
                $new_reg->registration_date = $p_reg->registration_date;
                $new_reg->assigned_user_id = $p_reg->assigned_user_id;
                $new_reg->processed = true; 
                $new_id = $new_reg->save();

                if ($new_id) {
                    if ($new_reg->load_relationship('stic_registrations_contacts')) {
                        $new_reg->stic_registrations_contacts->add($contact_id);
                    }
                    if ($new_reg->load_relationship('stic_registrations_stic_events')) {
                        $new_reg->stic_registrations_stic_events->add($child_id);
                    }
                }
            }
        }
        $GLOBALS['stic_sync_in_progress'] = false;
    }
}