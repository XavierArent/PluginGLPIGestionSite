<?php
include ("../../../inc/includes.php");

// Check if plugin is activated...
$plugin = new Plugin();
if (!$plugin->isInstalled('gestionsite') || !$plugin->isActivated('gestionsite')) {
   Html::displayNotFoundError();
}

//check for ACLs
if (PluginGestionSiteCity::canView()) {
   //View is granted: display the list.

   //Add page header
   Html::header(
      __('My example plugin', 'gestionsite'),
      $_SERVER['PHP_SELF'],
      'assets',
      'plugingestionsitecity',
      'city'
   );

   Search::show('PluginGestionSiteCity');

   Html::footer();
} else {
   //View is not granted.
   Html::displayRightError();
}