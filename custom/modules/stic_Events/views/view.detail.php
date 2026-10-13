<?php
if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

// 1. Requerimos la vista base de SuiteCRM y la librería auxiliar de SinergiaCRM
require_once 'include/MVC/View/views/view.detail.php';
require_once 'SticInclude/Views.php';

class stic_EventsViewDetail extends ViewDetail 
{
    public function __construct()
    {
        parent::__construct();
    }

    public function preDisplay()
    {
        parent::preDisplay();

        // Mantenemos la preparación previa original de SinergiaCRM
        SticViews::preDisplay($this);
    }

    public function display() 
    {
        parent::display();

        // 2. Ejecutamos la lógica nativa del módulo SinergiaCRM
        SticViews::display($this);
        echo getVersionedScript("modules/stic_Events/Utils.js");

        // 3. Añadimos tu script de personalización
        echo "<script type='text/javascript' src='custom/modules/stic_Events/sticCSUtils.js'></script>";
    }
}