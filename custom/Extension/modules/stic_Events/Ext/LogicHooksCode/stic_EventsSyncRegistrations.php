<?php
if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

class stic_EventsSyncRegistrations {
    protected static $processed_contacts = array();

    public function syncOnRelationshipAdd($bean, $event, $arguments) {
        $log = $GLOBALS['log'];

        if ($arguments['relationship'] === 'stic_events_stic_events_1') {
            $parent_id = $bean->id;
            $child_id  = $arguments['related_id'];
            $child = BeanFactory::getBean('stic_Events', $child_id);
            if (
                empty($child->stic_cs_inherit_reg_c)
            ) {
                return;
            }

            if (!empty($child->stic_cs_inherit_reg_c)) {
                $GLOBALS['stic_initial_sync'][$child_id] = $parent_id;
            }
        }
    }




    public function syncRegistrations($bean, $event, $arguments) {

        
        if (!empty($GLOBALS['stic_sync_in_progress'])) {
            $GLOBALS['log']->fatal('AFTER_SAVE BLOQUEJAT');
            return;
        }
        $log = $GLOBALS['log'];
        if (!empty($GLOBALS['stic_initial_sync'][$bean->id])) {
            $log->debug("INITIAL SYNC VALUE {$GLOBALS['stic_initial_sync'][$bean->id]} -> {$bean->id}");
            $parent_id = $GLOBALS['stic_initial_sync'][$bean->id];
    
            unset($GLOBALS['stic_initial_sync'][$bean->id]);
        
            $this->syncRegistrationsLogic($parent_id, $bean->id, true);
    
            return;
        }

        // Obtener el ID del padre asegurando que sea string (o extrayéndolo si es un objeto Link2)
        if ($bean->load_relationship('stic_events_stic_events_1')) {

            $parents = $bean->stic_events_stic_events_1->get();
        
            $GLOBALS['log']->fatal(print_r($parents, true));
        }
        $parent_id = $bean->stic_events_stic_events_1stic_events_ida;
        if (is_object($parent_id)) {
            $parent_id = isset($bean->fetched_row['stic_events_stic_events_1stic_events_ida']) 
                ? $bean->fetched_row['stic_events_stic_events_1stic_events_ida'] 
                : null;
        }

        if (!empty($parent_id) && is_string($parent_id) && !empty($bean->stic_cs_inherit_reg_c) && $bean->status === 'active') {
            $log->debug("STIC_SYNC: El evento actual es HIJO. Sincronizando con Padre: {$parent_id}");
            $this->syncRegistrationsLogic($parent_id, $bean->id);
        }

        // Replicar a hijos existentes si estamos editando
        if ($bean->status === 'active') {
            $log->debug("STIC_SYNC: Replicando desde Evento PADRE hacia sus hijos...");
            $this->replicateToChildren($bean);
        }
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

    private function syncRegistrationsLogic($parent_id, $child_id, $force = false) {



        $GLOBALS['stic_sync_in_progress'] = true;

        try {
            $log = $GLOBALS['log'];
        
            $parent = BeanFactory::getBean('stic_Events', $parent_id);
            $child = BeanFactory::getBean('stic_Events', $child_id);
            if (!$parent || !$child) {
                $GLOBALS['stic_sync_in_progress'] = false;
                return;
            }

            if (!$force && (empty($child->stic_cs_inherit_reg_c) || $child->status !== 'active')) {
                $log->debug("STIC_SYNC: Sincronización OMITIDA para hijo {$child_id}. Inherit flag: " . (!empty($child->stic_cs_inherit_reg_c) ? 'SI' : 'NO') . " | Status: {$child->status}.");
                $GLOBALS['stic_sync_in_progress'] = false;
                return;
            } else {
                $GLOBALS['log']->fatal("Inherit=".$child->stic_cs_inherit_reg_c);
            }

            // Sincronizar proyecto si el hijo no tiene ninguno indicado.
            $project_id_field = 'stic_events_projectproject_ida';
            $project_name_field = 'stic_events_project_name';
            $log->debug("STIC_SYNC: Sincronizando Proyecto '{$parent->$project_name_field}' al evento hijo {$child->id}");

            if (empty($child->$project_id_field) && !empty($parent->$project_id_field)) {
                if ($child->load_relationship('stic_events_project')) {
                    $child->stic_events_project->add($parent->$project_id_field);
                }
            }

            // Sincronizar inscripciones entre evento principal e hijos.

            $rel_name = 'stic_registrations_stic_events';
            $parent->load_relationship($rel_name);
            $parent_regs = $parent->$rel_name->getBeans();

            $parent->load_relationship($rel_name);
            $child->load_relationship($rel_name);
        
            $parent_regs = $parent->$rel_name->getBeans();
            $child_regs = $child->$rel_name->getBeans();
        
            foreach ($parent_regs as $p_reg) {

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
                    $allowed_statuses = array('confirmed', 'participates');
                    if (!in_array($p_reg->status, $allowed_statuses)) {
                        continue;
                    }
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
        } finally {
        $GLOBALS['stic_sync_in_progress'] = false;
        }
    }
}