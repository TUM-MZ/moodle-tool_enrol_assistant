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
 * The enrolment set-up page
 *
 * @package    tool
 * @subpackage enrol_assistant
 * @author     Dimitri Vorona <vorona@in.tum.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

$PAGE->set_url('/admin/tool/enrol_assistant/view.php');
$PAGE->set_pagelayout('admin');

require_login();
require_capability('moodle/site:config', context_system::instance());

global $DB, $CFG, $PAGE, $OUTPUT;

$context = context_system::instance();
$PAGE->set_context($context);
    $PAGE->set_heading(get_string('heading', 'tool_enrol_assistant'));
$PAGE->set_title(get_string('heading', 'tool_enrol_assistant'));

echo $OUTPUT->header();

if (array_key_exists('submit3', $_POST)) {
    echo "<p class='message'>Selection cleared</p>";
    unset($_SESSION['werte']);
    unset($_SESSION['kurse']);
}


// insert
if (empty($_REQUEST['liste01'])) {
} else {
    $userlist = $_REQUEST['liste01'];
    for ($i = 0; $i < count($userlist); $i++) {
        $_SESSION['werte'][] = $userlist[$i];
    }
}

//rausschmeisen
if (array_key_exists('liste02', $_REQUEST)) {
    $userlist2 = $_REQUEST['liste02'];
    for ($i = 0; $i < count($userlist2); $i++) {
        $suchid = $userlist2[$i];
        $temp = array_flip($_SESSION['werte']);
        unset($temp[$suchid]);
        $_SESSION['werte'] = array_flip($temp);
    }
}


//insert
if (array_key_exists('liste03', $_REQUEST)) {
    $kurslist = $_REQUEST['liste03'];
    if (!array_key_exists('kurse', $_SESSION)) {
        $_SESSION['kurse'] = array();
    }
    for ($ii = 0; $ii < count($kurslist); $ii++) {
        array_push($_SESSION['kurse'], $kurslist[$ii]);
    }
}

// remove
if (array_key_exists('liste04', $_REQUEST)) {
    $kurslist2 = $_REQUEST['liste04'];
    for ($ii = 0; $ii < count($kurslist2); $ii++) {
        $kursid = $kurslist2[$ii];
        $temp2 = array_flip($_SESSION['kurse']);
        unset($temp2[$kursid]);
        $_SESSION['kurse'] = array_flip($temp2);
    }
}

?>

<div id="header_div" align="center"><?php get_string('heading', 'tool_enrol_assistant'); ?></div>
<br/>
<table width="960" border="0" cellspacing="0" cellpadding="0" class="table01">
    <tr>
        <td colspan="3">
            Benutzer:<br/><br/>
        </td>
    </tr>
    <tr>
        <form name="s001" action="view.php" method="post">
            <td colspan="3">
                <input name="such_nutzer" type="text" style="width:350px;" class="liste01"/>
                <input type="submit" name="submit_s1" value="suche" class="button01"/>
                <br/><br/>
            </td>
        </form>
    </tr>
    <form name="f001" action="view.php" method="post">
        <tr>
            <td width="450" align="center">
                <select name="liste01[]" size="18" multiple style="width:450px;" class="liste01">
                    <?php
                    if (array_key_exists('such_nutzer', $_POST)) {
                        $such_nutzer = '%'. strtolower($_POST['such_nutzer']) . '%';

                        $sql = "SELECT * FROM {user} " .
                            "WHERE deleted != 1 " .
                            "  AND ( lower(idnumber) LIKE ? OR lower(firstname) LIKE ? OR lower(lastname) LIKE ? OR  lower(email) LIKE ? ) " .
                            "ORDER BY lastname ASC";
                        $result = $DB->get_records_sql($sql, array($such_nutzer, $such_nutzer, $such_nutzer, $such_nutzer));
                        foreach ($result as $row) {
                            $id = $row->id;
                            $nachname = $row->lastname;
                            $nachname = utf8_decode($nachname);
                            $vorname = $row->firstname;
                            $vorname = utf8_decode($vorname);
                            $emailadresse = $row->email;
                            $username = $row->username;

                            $checkstatus = 0;
                            foreach ($_SESSION['werte'] as $checkid) {
                                if ($id == $checkid) {
                                    $checkstatus = 1;
                                } else {
                                }
                            }

                            if ($checkstatus == 1) {
                            } else {
                                echo "<option value=\"$id\" title=\"$username\">$nachname $vorname ($emailadresse)</option>";
                            }
                        }
                    }
                ?>
                </select>
            </td>
            <td width="60" align="center">
                <input type="submit" name="ein" value=" &lt;=&gt; " class="button01"/>
            </td>
            <td width="450" align="center">
                <select name="liste02[]" size="18" multiple style="width:450px;" class="liste02">
                    <?php
                    foreach ($_SESSION['werte'] as $userid) {
                        $sql = "SELECT * FROM {user} WHERE id = ?";
                        $result = $DB->get_record_sql($sql, array($userid));
                        $nachname = $result->lastname;
                        $vorname = $result->firstname;
                        $emailadresse = $result->email;
                        echo "<option value=\"$userid\">$nachname $vorname ($emailadresse)</option>";
                    }
                    ?>
                </select>
            </td>
        </tr>
    </form>
</table>
<br/>
<table width="960" border="0" cellspacing="0" cellpadding="0" class="table01">
    <tr>
        <td colspan="3">
            Kurse:<br/><br/>
        </td>
    </tr>
    <tr>
        <form name="s002" action="view.php" method="post">
            <td colspan="3">
                <input name="such_kurse" type="text" style="width:350px;" class="liste01"/>
                <input type="submit" name="submit_s2" value="suche" class="button01"/>
                <br/><br/>
            </td>
        </form>
    </tr>
    <form name="f002" action="view.php" method="post">
        <tr>
            <td width="450" align="center">
                <select name="liste03[]" size="18" multiple style="width:450px;" class="liste01">
                    <?php
                    if (array_key_exists('such_kurse', $_POST)) {
                        $such_kurse = '%' . strtolower($_POST['such_kurse']) . '%';

                        $sql = "SELECT * FROM {course} 
                         WHERE id != 1 
                           AND (lower(fullname) LIKE ? OR lower(shortname) LIKE ? OR idnumber LIKE ?)
                      ORDER BY fullname ASC";
                        $result = $DB->get_records_sql($sql, array($such_kurse, $such_kurse, $such_kurse));
                        foreach ($result as $row) {
                            $kid = $row->id;
                            $idnumber = $row->idnumber;
                            $fullname = $row->fullname;
                            $shortname = $row->shortname;

                            $checkstatusk = 0;
                            foreach ($_SESSION['kurse'] as $checkkid) {
                                if ($kid == $checkkid) {
                                    $checkstatusk = 1;
                                } else {
                                }
                            }

                            if ($checkstatusk == 1) {
                            } else {
                                echo "<option value=\"$kid\" title=\"$shortname\">[$idnumber] $fullname</option>";
                            }
                        }
                    }
                    ?>
                </select>
            </td>
            <td width="60" align="center">
                <input type="submit" name="kein" value=" &lt;=&gt; " class="button01"/>
            </td>
            <td width="450" align="center">
                <select name="liste04[]" size="18" multiple style="width:450px;" class="liste02">
                    <?php
                    foreach ($_SESSION['kurse'] as $kurseid) {
                        $sql = "SELECT * FROM {course} WHERE id = ?";
                        $result = $DB->get_record_sql($sql, array($kurseid));
                        $idnumber = $result->idnumber;
                        $fullname = $result->fullname;
                        $shortname = $result->shortname;
                        echo "<option value=\"$kurseid\" title=\"$shortname\">[$idnumber] $fullname</option>";
                    }
                    ?>
                </select>
            </td>
        </tr>
    </form>
</table>

<form name="f003" action="enrol.php" method="post">
    <tr>
        <td colspan="3">
            Rollen:<br/><br/>
        </td>
    </tr>
    <tr>
        <td width="450" align="center" rows="6">
            <select name="userrole" size="11" style="width:450px" class="liste01">
                <?php
                $allroles = role_fix_names(get_all_roles($context), $context);

                $result = $DB->get_records('role');
                foreach ($result as $row) {
                    $show_role_id = $row->id;
                    $show_role_name = $allroles[$row->id]->localname;
                    echo "<option value=\"$show_role_id\">$show_role_name</option>";
                }
                ?>
            </select>
        </td>
        <td width="60" align="center">&nbsp;</td>
        <td width="450" align="center">
            <input type="submit" name="submit" value="Enrol all." class="button01"/>
        </td>
    </tr>
</form>

<form action="view.php" method="post" name="f004" id="f003">
    <input type="submit" name="submit3" value="Reset" class="button01"/>
</form>


<?php echo $OUTPUT->footer(); ?>
