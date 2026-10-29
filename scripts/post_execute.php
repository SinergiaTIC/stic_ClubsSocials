<?php
if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

/*
 * ModuleInstaller::install() ejecuta al final rebuild_all() y repairAndClearAll().
 *
 * Dos comportamientos del instalador hacen que una instalación normal recorra
 * toda la instancia:
 *
 *  1. Si el paquete no declara "beans" en el manifiesto, $installed_modules queda
 *     vacío. repairAndClearAll() interpreta entonces que debe reparar TODOS los
 *     módulos (repairDatabase()) y upgrade_custom_relationships() recorre también
 *     todos los módulos de la instancia. En servidores grandes esto supone decenas
 *     de segundos o minutos de comparaciones y ALTER TABLE, y agota el timeout.
 *
 *  2. rebuild_all() reconstruye las extensiones de todos los módulos. Este paquete
 *     solo modifica Contacts, stic_Events, stic_Sessions y
 *     stic_Contacts_Relationships, así que no hace falta recorrer el resto.
 *
 * Este fichero se incluye dentro de ModuleInstaller::post_execute(), por lo que
 * $this es la instancia del instalador. Limitamos ambas fases. Los ficheros de
 * aplicación (custom/Extension/application/...) se reconstruyen siempre,
 * independientemente de estas listas.
 *
 * Los campos de base de datos y la limpieza de cachés de los cuatro módulos se
 * completan de forma dirigida en scripts/post_install.php.
 */
if (isset($this) && $this instanceof ModuleInstaller) {
    // repairAndClearAll() y upgrade_custom_relationships() reciben la lista de
    // módulos "instalados". Si está vacía, ambas recorren TODA la instancia. Para
    // evitarlo se usa un módulo real y muy pequeño: la reparación que necesita el
    // paquete se hace de forma dirigida en scripts/post_install.php.
    //
    // No se usa stic_Events aquí porque upgrade_custom_relationships() recorre su
    // árbol completo de relaciones y en instancias grandes sigue siendo costoso.
    // La relación del paquete es many-to-many y no necesita esa reparación legacy
    // (que solo corrige relaciones one-to-many auto-referenciadas).
    $this->installed_modules = array('Schedulers');

    // Módulos cuyas extensiones (vardefs, idiomas, layouts...) deben reconstruirse.
    $this->modules = array(
        'Contacts',
        'stic_Events',
        'stic_Sessions',
        'stic_Contacts_Relationships',
    );
}
