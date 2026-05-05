<?php
if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

function pre_install() {
    global $db;

    // Lista de campos conflictivos y sus tablas
    $campos_a_verificar = [
        'contacts_cstm' => 'stic_cs_h_majors_c',
        'contacts_cstm' => 'stic_cs_h_majors_c',
        'contacts_cstm' => 'stic_cs_h_majors_c',
        'contacts_cstm' => 'stic_cs_h_majors_c',
    ];

    foreach ($campos_a_verificar as $tabla => $columna) {
        $query = "SHOW COLUMNS FROM $tabla LIKE '$columna'";
        $result = $db->query($query);
        $row = $db->fetchByAssoc($result);

        if ($row) {
            // Si la columna ya existe, SinergiaCRM fallará al intentar el ALTER TABLE.
            // Una técnica es renombrarla temporalmente o, mejor aún, 
            // asegurarnos de que el manifest no intente crearla de nuevo 
            // si el sistema ya la reconoce.
            $GLOBALS['log']->info("La columna $columna ya existe en $tabla. Procediendo con cautela.");
        }
    }
}