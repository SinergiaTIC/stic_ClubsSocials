<?php
if(!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

// Heredamos de la EditView estándar del sistema
require_once('include/MVC/View/views/view.detail.php');

class stic_EventsViewDetail extends ViewDetail 
{
    public function __construct()
    {
        parent::__construct();
    }

    public function display() 
    {
        parent::display();

        echo "<script type='text/javascript' src='custom/modules/stic_Events/sticCSUtils.js'></script>";
        }
}