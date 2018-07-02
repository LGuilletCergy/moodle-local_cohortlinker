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
 * File : sendlinkmessage.php
 * Send message with link
 */

namespace local_cohortlinker\task;

defined('MOODLE_INTERNAL') || die();

class sendlinkmessage extends \core\task\scheduled_task {

    public function get_name() {

        return get_string('sendlinkmessage', 'local_cohortlinker');
    }

    public function execute() {

        global $DB;

        $sql = "SELECT * FROM {cohort} WHERE id NOT IN "
                . "(SELECT cohortid FROM {local_cohortlinker} WHERE 1) AND idnumber NOT LIKE ''";
        $listcohortsnottreated = $DB->get_records_sql($sql);

        foreach ($listcohortsnottreated as $cohortnottreated) {

            $listlinkablecourses = $DB->get_records('course',
                    array('idnumber' => $cohortnottreated->idnumber));

            foreach ($listlinkablecourses as $linkablecourse) {

                // Vérifier que la cohorte n'est pas lié au cours

                if (!$DB->record_exists('enrol',
                        array('enrol' => 'cohort', 'customint1' => $cohortnottreated->id))) {

                    $contextcourse = \context_course::instance($linkablecourse->id);

                    $listteachers = $DB->get_records('role_assignments',
                            array('roleid' => 3, 'contextid' => $contextcourse->id));

                    foreach ($listteachers as $teacher) {

                        send_link_message($teacher->userid, $linkablecourse, $cohortnottreated);
                    }
                }
            }

            $cohorttreated = new \stdClass();
            $cohorttreated->cohortid = $cohortnottreated->id;

            $DB->insert_record('local_cohortlinker', $cohorttreated);
        }
    }
}

function send_link_message($teacherid, $course, $cohort) {

    global $CFG, $DB;

    require_once($CFG->dirroot.'/local/cohortlinker/notification.php');

    $contact = \core_user::get_support_user();

    $url = new \moodle_url('/local/cohortlinker/linkpage.php',
            array('courseid' => $course->id, 'cohortid' => $cohort->id));

    $data = new \stdClass();
    $data->cohortname = $cohort->name;
    $data->coursename = $course->fullname;
    $data->linkurl = $url;


    $subject = get_string('subjectlinkmessage', 'local_cohortlinker');
    $content = get_string('contentlinkmessage', 'local_cohortlinker', $data);

    $teacher = $DB->get_record('user', array('id' => $teacherid));

    $message = new \local_cohortlinker_notification($teacher, $contact, $subject, $content,
            $url);

    message_send($message);
}

