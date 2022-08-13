<?php

/**
 * -------------------------------------------------------------------------
 * Gestion Site plugin for GLPI
 * Copyright (C) 2022 by the Gestion Site Development Team.
 * -------------------------------------------------------------------------
 *
 * MIT License
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * --------------------------------------------------------------------------
 */

/**
 * Plugin install process
 *
 * @return boolean
 */
function plugin_gestionsite_install()
{
    global $DB;

    //instanciate migration with version
    $migration = new Migration(100);
 
    //Create table only if it does not exists yet! -- Villes
    if (!$DB->tableExists('glpi_plugin_gestionsite_cities')) {
       //table creation query
       $query = "CREATE TABLE `glpi_plugin_gestionsite_cities` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                    `name` VARCHAR(255) NOT NULL COLLATE 'utf8mb4_general_ci',
                    `postal_code` INT(11) NOT NULL,
                    `insee_code` INT(11) NULL DEFAULT NULL,
                    `logo` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
                    `created_at` TIMESTAMP NULL DEFAULT NULL,
                    `updated_at` TIMESTAMP NULL DEFAULT NULL,
                    PRIMARY KEY (`id`) USING BTREE
                )
                COLLATE='utf8mb4_general_ci'
                ENGINE=InnoDB";
       $DB->queryOrDie($query, $DB->error());
    }

    //Create table only if it does not exists yet! -- Sites
    if (!$DB->tableExists('glpi_plugin_gestionsite_sites')) {
        //table creation query
        $query = "CREATE TABLE `glpi_plugin_gestionsite_sites` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                    `name` VARCHAR(50) NOT NULL COLLATE 'utf8mb4_general_ci',
                    `address` VARCHAR(255) NOT NULL COLLATE 'utf8mb4_general_ci',
                    `phone` INT(11) NULL DEFAULT NULL,
                    `photo` VARCHAR(255) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
                    `city_id` INT(11) NOT NULL,
                    PRIMARY KEY (`id`) USING BTREE,
                    CONSTRAINT `FK_CITY_SITES` FOREIGN KEY (`city_id`) REFERENCES `glpi_plugin_gestionsite_cities` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT
                )
                COLLATE='utf8mb4_general_ci'
                ENGINE=InnoDB";

        $DB->queryOrDie($query, $DB->error());
     }

    //Create table only if it does not exists yet! -- Bâtiments
    if (!$DB->tableExists('glpi_plugin_gestionsite_buildings')) {
        //table creation query
        $query = "CREATE TABLE `glpi_plugin_gestionsite_buildings` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(50) NOT NULL COLLATE 'utf8mb4_general_ci',
            `photo` INT(11) NULL DEFAULT NULL,
            `site_id` INT(11) NOT NULL,
            PRIMARY KEY (`id`) USING BTREE,
            CONSTRAINT `FK_SITE_BUILDINGS` FOREIGN KEY (`site_id`) REFERENCES `glpi_plugin_gestionsite_sites` (`id`) ON UPDATE CASCADE ON DELETE RESTRICT
        )
        COLLATE='utf8mb4_general_ci'
        ENGINE=InnoDB";

        $DB->queryOrDie($query, $DB->error());
     }
 
    //execute the whole migration
    $migration->executeMigration();
 
    return true;
}

/**
 * Plugin uninstall process
 *
 * @return boolean
 */
function plugin_gestionsite_uninstall()
{
    global $DB;

   $tables = [
      'buildings',
      'sites',
      'cities'  
   ];

   foreach ($tables as $table) {
      $tablename = 'glpi_plugin_gestionsite_' . $table;
      //Create table only if it does not exists yet!
      if ($DB->tableExists($tablename)) {
         $DB->queryOrDie(
            "DROP TABLE `$tablename`",
            $DB->error()
         );
      }
   }


    return true;
}
