<?php
$manifest = [
    'acceptable_sugar_versions' => ['regex_matches' => array('(.*?)\\.(.*?)\\.(.*?)')],
    'author' => 'LSA',
    'readme' => '',
    'license' => 'LICENSE.txt',
    'description' => 'Añade campos, informes y valores a desplegables para la funcionalidad de Club Social',
    'is_uninstallable' => true,
    'published_date' => '2026-10-28 12:30:10',
    'name' => 'STIC Clubs sociales',
    'type' => 'module',
    'version' => '1793190610',
    'key' => 'stic',    
    'remove_tables' => 'prompt',
];

$installdefs = [
    'id' => 'stic_clubs_sociales_2024',
    // Copia explícita de las carpetas del paquete. No se copia todo custom/
    // para no sobrescribir custom/include/language, donde Studio guarda los
    // desplegables personalizados de cada instalación.
    'copy' => [
        [
            'from' => '<basepath>/custom/Extension/',
            'to' => 'custom/Extension/',
        ],
        [
            'from' => '<basepath>/custom/metadata/',
            'to' => 'custom/metadata/',
        ],
        [
            'from' => '<basepath>/custom/modules/',
            'to' => 'custom/modules/',
        ],
    ],
    'scripts' => [
        'post_install' => '<basepath>/scripts/post_install.php',
    ],
    // Relación de auto-referencia Evento principal -> Subeventos. Al declararla en el
    // manifiesto, ModuleInstaller::install_relationships() crea la tabla de unión
    // stic_events_stic_events_1_c si no existe (por ejemplo en instalaciones nuevas).
    'relationships' => [
        [
            'meta_data' => '<basepath>/custom/metadata/stic_events_stic_events_1MetaData.php',
        ],
    ],
];

$upgrade_manifest = [
    'upgrade_from_version' => ['0\.0\..*'],
    'upgrade_from_id' => 'stic_clubs_sociales_2024',
];