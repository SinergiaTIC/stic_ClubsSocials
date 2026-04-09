<?php
// created: 2026-04-09 10:42:22
$dictionary["stic_Events"]["fields"]["stic_events_stic_events_1"] = array (
  'name' => 'stic_events_stic_events_1',
  'type' => 'link',
  'relationship' => 'stic_events_stic_events_1',
  'source' => 'non-db',
  'module' => 'stic_Events',
  'bean_name' => 'stic_Events',
  'vname' => 'LBL_STIC_EVENTS_STIC_EVENTS_1_FROM_STIC_EVENTS_L_TITLE',
  'id_name' => 'stic_events_stic_events_1stic_events_ida',
);
$dictionary["stic_Events"]["fields"]["stic_events_stic_events_1_name"] = array (
  'name' => 'stic_events_stic_events_1_name',
  'type' => 'relate',
  'source' => 'non-db',
  'vname' => 'LBL_STIC_EVENTS_STIC_EVENTS_1_FROM_STIC_EVENTS_L_TITLE',
  'save' => true,
  'id_name' => 'stic_events_stic_events_1stic_events_ida',
  'link' => 'stic_events_stic_events_1',
  'table' => 'stic_events',
  'module' => 'stic_Events',
  'rname' => 'name',
);
$dictionary["stic_Events"]["fields"]["stic_events_stic_events_1stic_events_ida"] = array (
  'name' => 'stic_events_stic_events_1stic_events_ida',
  'type' => 'link',
  'relationship' => 'stic_events_stic_events_1',
  'source' => 'non-db',
  'reportable' => false,
  'side' => 'right',
  'vname' => 'LBL_STIC_EVENTS_STIC_EVENTS_1_FROM_STIC_EVENTS_R_TITLE',
);