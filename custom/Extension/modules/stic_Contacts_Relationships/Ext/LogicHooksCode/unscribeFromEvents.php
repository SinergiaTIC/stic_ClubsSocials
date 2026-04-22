<?php
if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

class stic_Contacts_RelationshipsUnscribeFromEvents {

    public function unscribeRegistrations($bean, $event, $arguments) {
        
        $GLOBALS['log']->debug("Line ".__LINE__.": ".__METHOD__.": [INICIO] Ejecutando unscribeRegistrations para Relación ID: {$bean->id}");

        // 1. Verificar si la relación se desactiva
        $isInactive = (isset($bean->active) && $bean->active == '0');
        $isDeleted = ($bean->deleted == 1);

        if ($isInactive || $isDeleted) {
            
            $personaId = $bean->stic_contacts_relationships_contactscontacts_ida;
            $proyectoId = $bean->stic_contacts_relationships_projectproject_ida;

            if (empty($personaId) || empty($proyectoId)) {
                return;
            }

            $contacto = BeanFactory::getBean('Contacts', $personaId);
            if (empty($contacto->id)) {
                return;
            }
            
            $relName = 'stic_registrations_contacts'; 

            if ($contacto->load_relationship($relName)) {
                $inscripcionesIds = $contacto->$relName->get();
                $GLOBALS['log']->debug("Line ".__LINE__.": ".__METHOD__.": [RELACIÓN] Cargada '$relName'. Encontradas " . count($inscripcionesIds) . " inscripciones vinculadas.");

                foreach ($inscripcionesIds as $inscripcionId) {
                    $inscripcion = BeanFactory::getBean('stic_Registrations', $inscripcionId);
                    
                    if (!$inscripcion || empty($inscripcion->id)) {
                        continue;
                    }

                    if ($inscripcion->status == 'dropped') {
                        continue;
                    }

                    // 3. Verificar Proyecto del Evento
                    $eventoId = $inscripcion->stic_registrations_stic_eventsstic_events_ida; 
                    $evento = BeanFactory::getBean('stic_Events', $eventoId);

                    if (!$evento || empty($evento->id) || $evento->type!='club') {
                        continue;
                    }

                    $GLOBALS['log']->debug("Line ".__LINE__.": ".__METHOD__.": [PROYECTO] Inscripción $inscripcionId -> Evento: {$evento->name} | Proyecto Evento: {$evento->stic_events_projectproject_ida} | Proyecto Relación: $proyectoId");

                    if ($evento->stic_events_projectproject_ida == $proyectoId) {                        
                        $inscripcion->status = 'dropped';
                        $inscripcion->save();
                    }
                }
            }
        }
    }
}