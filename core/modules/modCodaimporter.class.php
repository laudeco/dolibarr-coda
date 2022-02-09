<?php
/* Copyright (C) 2004-2018  Laurent Destailleur     <eldy@users.sourceforge.net>
 * Copyright (C) 2018-2019  Nicolas ZABOURI         <info@inovea-conseil.com>
 * Copyright (C) 2019       Frédéric France         <frederic.france@netlogic.fr>
 * Copyright (C) 2020 Laudeco
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * 	\defgroup   codaimporter     Module Codaimporter
 *  \brief      Codaimporter module descriptor.
 *
 *  \file       htdocs/codaimporter/core/modules/modCodaimporter.class.php
 *  \ingroup    codaimporter
 *  \brief      Description and activation file for module Codaimporter
 */
include_once DOL_DOCUMENT_ROOT.'/core/modules/DolibarrModules.class.php';

final class modCodaimporter extends DolibarrModules
{
    /**
     * Constructor. Define names, constants, directories, boxes, permissions
     *
     * @param DoliDB $db Database handler
     */
    public function __construct($db)
    {
        global $langs, $conf;
        $this->db = $db;

        $this->numero = 511110;
        $this->rights_class = 'codaimporter';
        $this->family = "Belgium";
        $this->module_position = '90';
        $this->name = preg_replace('/^mod/i', '', get_class($this));
        $this->description = $langs->trans("ModuleCodaimporterDesc");
        // Used only if file README.md and README-LL.md not found.
        $this->descriptionlong = $langs->trans("ModuleCodaimporterDescLong");
        $this->editor_name = 'Laudeco';
        $this->version = 'development';

        $this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
        $this->picto = 'generic';
        
        $this->module_parts = array(
            'triggers' => 0,
            'login' => 0,
            'substitutions' => 0,
            'menus' => 0,
            'tpl' => 0,
            'barcode' => 0,
            'models' => 0,
            'theme' => 0,
            'css' => [],
            'js' => [],
            'hooks' => [],
            'moduleforexternal' => 0,
        );
        
        $this->dirs = array("/codaimporter/codas");
        // Config pages. Put here list of php page, stored into codaimporter/admin directory, to use to setup module.
        $this->config_page_url = array("setup.php@codaimporter");
        $this->hidden = false;
        $this->depends = [];
        $this->requiredby = [];
        $this->conflictwith = [];
        $this->langfiles = array("codaimporter@codaimporter");
        $this->phpmin = array(7, 0); // Minimum version of PHP required by module
        $this->need_dolibarr_version = array(11, -3); // Minimum version of Dolibarr required by module
        $this->warnings_activation = [];
        $this->warnings_activation_ext = [];

        $this->const = [];

        if (!isset($conf->codaimporter) || !isset($conf->codaimporter->enabled)) {
            $conf->codaimporter = new stdClass();
            $conf->codaimporter->enabled = 0;
        }

        $this->tabs = [];
        $this->dictionaries = [];
        $this->boxes = [];

        $this->cronjobs = [];
        $this->rights = [];

        $r = 0;
        $this->rights[$r][0] = $this->numero + $r; // Permission id (must not be already used)
        $this->rights[$r][1] = 'Import a coda'; // Permission label
        $this->rights[$r][4] = 'coda'; // In php code, permission will be checked by test if ($user->rights->codaimporter->level1->level2)
        $this->rights[$r][5] = 'import'; // In php code, permission will be checked by test if ($user->rights->codaimporter->level1->level2)

        // Main menu entries to add
        $this->menu = [];
        $r = 0;
        // Add here entries to declare new menus
        /* BEGIN MODULEBUILDER TOPMENU */
        $this->menu[$r++] = array(
            'fk_menu'=>'', // '' if this is a top menu. For left menu, use 'fk_mainmenu=xxx' or 'fk_mainmenu=xxx,fk_leftmenu=yyy' where xxx is mainmenucode and yyy is a leftmenucode
            'type'=>'top', // This is a Top menu entry
            'titre'=>'CODA',
            'mainmenu'=>'codaimporter',
            'leftmenu'=>'',
            'url'=>'/codaimporter/index.php',
            'langs'=>'codaimporter@codaimporter', // Lang file to use (without .lang) by module. File must be in langs/code_CODE/ directory.
            'position'=>1000 + $r,
            'enabled'=>'$user->rights->codaimporter->coda->import',
            'perms'=>'$user->rights->codaimporter->coda->import',
            'target'=>'',
            'user'=>0,
        );
    }

    /**
     *  Function called when module is enabled.
     *  The init function add constants, boxes, permissions and menus (defined in constructor) into Dolibarr database.
     *  It also creates data directories
     *
     *  @param      string  $options    Options when enabling module ('', 'noboxes')
     *  @return     int             	1 if OK, 0 if KO
     */
    public function init($options = '')
    {
        $result = $this->_load_tables('/codaimporter/sql/');
        if ($result < 0) return -1; // Do not activate module if error 'not allowed' returned when loading module SQL queries (the _load_table run sql with run_sql with the error allowed parameter set to 'default')

        $this->remove($options);

        $sql = [];
        return $this->_init($sql, $options);
    }

    /**
     *  Function called when module is disabled.
     *  Remove from database constants, boxes and permissions from Dolibarr database.
     *  Data directories are not deleted
     *
     *  @param      string	$options    Options when enabling module ('', 'noboxes')
     *  @return     int                 1 if OK, 0 if KO
     */
    public function remove($options = '')
    {
        $sql = [];
        return $this->_remove($sql, $options);
    }
}
