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
 * The in-between page to confirm the enrolled users
 *
 * @package    tool
 * @subpackage enrol_assistant
 * @author     Dimitri Vorona <vorona@in.tum.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../../config.php');
require_once($CFG->libdir . '/adminlib.php');
global $DB, $CFG, $PAGE, $OUTPUT;

$PAGE->set_url('/admin/tool/enrol_assistant/enrol.php');
$PAGE->set_pagelayout('admin');


require_login();
require_capability('moodle/site:config', context_system::instance());

$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('heading', 'tool_enrol_assistant'));
//$PAGE->set_heading(get_string('heading', 'tool_enrol_assistant'));
$PAGE->set_heading('test');
echo $OUTPUT->header();

//Dozent, Student, Tutor... 3,4,5,6,8
$userrole = $_POST["userrole"];

//UserID von Moodle mdl_user.id
$userinfo = $_SESSION['werte'];

//KursID von Moodle mdl_course.id
$usercourses = $_SESSION['kurse'];
?>

<div id="header_div" align="center"><?php get_string('heading', 'tool_enrol_assistant'); ?></div>

<?php
$plugin = enrol_get_plugin('manual');

foreach ($userinfo as $userid) {

    if (!($userdata = $DB->get_record('user', array('id' => $userid)))) {
        echo "<h2>User $userid not found</h2>";
        continue;
    }
    $show_user_vn = $userdata->firstname;
    $show_user_nn = $userdata->lastname;
    $show_user_em = $userdata->email;
    $show_user_mk = $userdata->idnumber;


    $context = context_system::instance();
    $allroles = role_fix_names(get_all_roles($context), $context);
    $role_name = $allroles[$userrole]->localname;

    echo "<strong>$show_user_nn $show_user_vn (id: $userid), $show_user_em ($show_user_mk)</strong><br /><br />";

    echo "<ul class='course_list'>";
    foreach ($usercourses as $courseid) {

        if (!($coursedata = $DB->get_record('course', array('id' => $courseid)))) {
            echo "<li>Course <strong>$courseid</strong> not found</li>";
            continue;
        }

        $instance = $DB->get_record('enrol',
            array('courseid' => $courseid, 'enrol' => 'manual'));

        $plugin->enrol_user($instance, $userid, $userrole);
        echo "Added to \"$coursedata->fullname\" (id: $courseid) as $role_name</u>!<br />";
    }
    echo "</ul>";
}
?>
<a href="./view.php" target="_self">Zurück</a>

<?php echo $OUTPUT->footer(); ?>

