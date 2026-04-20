<?php
$manifest = [
    'acceptable_sugar_versions' => ['regex_matches' => array('(.*?)\\.(.*?)\\.(.*?)')],
    'author' => 'LSA',
    'description' => 'Añade campos, informes y valores a desplegables para la funcionalidad de Club Social',
    'is_uninstallable' => true,
    'name' => 'STIC Clubs sociales',
    'type' => 'module',
    'version' => '0.0.3',
    
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
        'post_install' => '<basepath>/.scripts/post_install.php',
    ],
];