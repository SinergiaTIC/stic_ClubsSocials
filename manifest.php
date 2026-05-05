<?php
$manifest = [
    'acceptable_sugar_versions' => ['regex_matches' => array('(.*?)\\.(.*?)\\.(.*?)')],
    'author' => 'LSA',
    'readme' => '',
    'description' => 'Añade campos, informes y valores a desplegables para la funcionalidad de Club Social',
    'is_uninstallable' => true,
    'published_date' => '2026-05-05 16:06:22',
    'name' => 'STIC Clubs sociales',
    'type' => 'module',
    'version' => '0.0.11',
    'key' => 'stic',    
    'remove_tables' => 'prompt',
];

$installdefs = [
    'id' => 'stic_clubs_sociales_2024',
    'copy' => [
        [
            'from' => '<basepath>/custom/',
            'to' => 'custom/',
        ],
    ],
    'scripts' => [
        'post_install' => '<basepath>/scripts/post_install.php',
    ],
];

$upgrade_manifest = [
    'upgrade_from_version' => ['0\.0\..*'],
    'upgrade_from_id' => 'stic_clubs_sociales_2024',
];