<?php
// created: 2026-04-09 10:42:22
$dictionary["stic_events_stic_events_1"] = array (
  'true_relationship_type' => 'one-to-many',
  'from_studio' => true,
  'relationships' => 
  array (
    'stic_events_stic_events_1' => 
    array (
      'lhs_module' => 'stic_Events',
      'lhs_table' => 'stic_events',
      'lhs_key' => 'id',
      'rhs_module' => 'stic_Events',
      'rhs_table' => 'stic_events',
      'rhs_key' => 'id',
      'relationship_type' => 'many-to-many',
      'join_table' => 'stic_events_stic_events_1_c',
      'join_key_lhs' => 'stic_events_stic_events_1stic_events_ida',
      'join_key_rhs' => 'stic_events_stic_events_1stic_events_idb',
    ),
  ),
  'table' => 'stic_events_stic_events_1_c',
  'fields' => 
  array (
    0 => 
    array (
      'name' => 'id',
      'type' => 'varchar',
      'len' => 36,
    ),
    1 => 
    array (
      'name' => 'date_modified',
      'type' => 'datetime',
    ),
    2 => 
    array (
      'name' => 'deleted',
      'type' => 'bool',
      'len' => '1',
      'default' => '0',
      'required' => true,
    ),
    3 => 
    array (
      'name' => 'stic_events_stic_events_1stic_events_ida',
      'type' => 'varchar',
      'len' => 36,
    ),
    4 => 
    array (
      'name' => 'stic_events_stic_events_1stic_events_idb',
      'type' => 'varchar',
      'len' => 36,
    ),
  ),
  'indices' => 
  array (
    0 => 
    array (
      'name' => 'stic_events_stic_events_1spk',
      'type' => 'primary',
      'fields' => 
      array (
        0 => 'id',
      ),
    ),
    1 => 
    array (
      'name' => 'stic_events_stic_events_1_ida1',
      'type' => 'index',
      'fields' => 
      array (
        0 => 'stic_events_stic_events_1stic_events_ida',
      ),
    ),
    2 => 
    array (
      'name' => 'stic_events_stic_events_1_alt',
      'type' => 'alternate_key',
      'fields' => 
      array (
        0 => 'stic_events_stic_events_1stic_events_idb',
      ),
    ),
  ),
);