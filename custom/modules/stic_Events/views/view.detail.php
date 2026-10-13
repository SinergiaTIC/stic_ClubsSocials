<?php
if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

// 1. Requerimos las librerías base necesarias
require_once 'include/MVC/View/views/view.detail.php';
if (file_exists('SticInclude/Views.php')) {
    require_once 'SticInclude/Views.php';
}

// 2. Si la vista original no se había cargado, la requerimos solo para asegurar dependencias
if (file_exists('modules/stic_Events/views/view.detail.php') && !class_exists('stic_EventsViewDetail')) {
    require_once 'modules/stic_Events/views/view.detail.php';
}

// 3. Declaramos la clase que extenderá la vista estándar
class CustomSticEventsViewDetail extends ViewDetail
{
    public function preDisplay()
    {
        parent::preDisplay();
        if (class_exists('SticViews')) {
            SticViews::preDisplay($this);
        }
    }

    public function display()
    {
        parent::display();

        // Ejecutamos la inyección nativa de SinergiaCRM
        if (class_exists('SticViews')) {
            SticViews::display($this);
        }
        if (file_exists('modules/stic_Events/Utils.js')) {
            echo getVersionedScript("modules/stic_Events/Utils.js");
        }

        // Carga tu script personalizado del paquete ZIP
        echo "<script type='text/javascript' src='custom/modules/stic_Events/sticCSUtils.js'></script>";
    }
}

// 4. Se asigna la clase hija a la vista si aún no está instanciada
if (!class_exists('stic_EventsViewDetail')) {
    class_alias('CustomSticEventsViewDetail', 'stic_EventsViewDetail');
}