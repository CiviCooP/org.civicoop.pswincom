<?php
// This file declares a managed database record of type "Job".
// The record will be automatically inserted, updated, or deleted from the
// database as appropriate. For more details, see "hook_civicrm_managed" at:
// http://wiki.civicrm.org/confluence/display/CRMDOC42/Hook+Reference
return array (
  0 =>
    array (
      'name' => 'Cron:Pswincom.ProcessInbound',
      'entity' => 'Job',
      'params' =>
        array (
          'version' => 3,
          'name' => 'Call Pswincom.ProcessInbound API',
          'description' => 'Call Pswincom.ProcessInbound API',
          'run_frequency' => 'Always',
          'api_entity' => 'Pswincom',
          'api_action' => 'ProcessInbound',
          'parameters' => 'limit=100',
        ),
    ),
);