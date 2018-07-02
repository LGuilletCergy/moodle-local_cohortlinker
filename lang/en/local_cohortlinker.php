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
 * Initially developped for :
 * Université de Cergy-Pontoise
 * 33, boulevard du Port
 * 95011 Cergy-Pontoise cedex
 * FRANCE
 *
 * Send notification to teachers when they can link a new cohort.
 *
 * @package   local_cohortlinker
 * @copyright 2018 Laurent Guillet <laurent.guillet@u-cergy.fr>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * File : en/local_cohortlinker.php
 * English language file
 */

$string['sendlinkmessage'] = 'Send message to teacher to link new cohorts.';
$string['subjectlinkmessage'] = 'New cohort available';
$string['contentlinkmessage'] = 'Hello,<br>'
        . 'A new cohort {$a->cohortname} is available for your course {$a->coursename}.<br>'
        . 'You can link it to your course by following this url : {$a->linkurl}.<br>';
$string['linkcohort'] = 'Link a cohort';
$string['pluginname'] = 'Cohort linker';
$string['success'] = 'You have successfully linked the cohort.';
$string['privacy:metadata'] = 'The plugin only store a list of cohorts.';