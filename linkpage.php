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
 * File : linkpage.php
 * Link cohort and displays congratulation message.
 */

require_once("../../config.php");
require_once("$CFG->dirroot/enrol/cohort/locallib.php");
require_once("$CFG->dirroot/group/lib.php");

$courseid = required_param('courseid', PARAM_INT);
$cohortid = required_param('cohortid', PARAM_INT);

global $PAGE, $DB;

require_login();
$context = context_course::instance($courseid);

$PAGE->set_context($context);
$PAGE->navbar->add(get_string('linkcohort', 'local_cohortlinker'));

$cohort = $DB->get_record('cohort', array('id' => $cohortid));

if (has_capability('enrol/cohort:config', $context)) {

    if ($cohort->visible || is_siteadmin()) {

        $course = $DB->get_record('course', array('id' => $courseid));

        $datagroup = new stdClass();
        $datagroup->name = $cohort->name;
        $datagroup->idnumber = $cohort->idnumber;
        $datagroup->courseid = $course->id;

        $groupid = groups_create_group($datagroup);

        $studentroleid = $DB->get_record('role', array('shortname' => 'student'))->id;

        $cohortplugin = enrol_get_plugin('cohort');
        $cohortplugin->add_instance($course, array('customint1' => $cohortid,
            'roleid' => $studentroleid, 'customint2' => $groupid));

        $trace = new null_progress_trace();
        enrol_cohort_sync($trace, $course->id);
        $trace->finished();
    }
}

echo $OUTPUT->header();
echo get_string('success', 'local_cohortlinker');
echo $OUTPUT->footer();