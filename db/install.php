<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Post-install code for the submission_moderatorchat module.
 *
 * @package assignsubmission_moderatorchat
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Code run after the module database tables have been created.
 * Moves the comments plugin to the bottom
 * @return bool
 */
function xmldb_assignsubmission_moderatorchat_install() {
    global $CFG;

    require_once($CFG->dirroot . '/mod/assign/adminlib.php');
    // Set the correct initial order for the plugins.
    $pluginmanager = new assign_plugin_manager('assignsubmission');

    $pluginmanager->move_plugin('moderatorchat', 'down');
    $pluginmanager->move_plugin('moderatorchat', 'down');

    return true;
}
