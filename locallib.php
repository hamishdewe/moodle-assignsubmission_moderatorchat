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
 * This file contains the definition for the library class for online comment submission plugin
 *
 * @package assignsubmission_moderatorchat
 */

 defined('MOODLE_INTERNAL') || die();

 require_once($CFG->dirroot . '/comment/lib.php');
 require_once($CFG->dirroot . '/mod/assign/submissionplugin.php');

/**
 * Library class for comment submission plugin extending submission plugin base class
 *
 * @package assignsubmission_moderatorchat
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_submission_moderatorchat extends assign_submission_plugin {


    /**
     * Get the name of the online comment submission plugin
     * @return string
     */
    public function get_name() {
      $cmid = optional_param('id', null, PARAM_INT);
      return $cmid && has_capability('mod/assignsubmission:post', context_module::instance($cmid))
              ? get_string('pluginname', 'assignsubmission_moderatorchat')
              : false;
    }

    public function view(stdClass $submissionorgrade) {
      $cmid = optional_param('id', null, PARAM_INT);
      return $cmid && has_capability('mod/assignsubmission:post', context_module::instance($cmid))
              ? parent::view($submissionorgrade)
              : null;
      $context = context_module::instance(optional_param('id', null, PARAM_INT));
    }

    /**
     * Display AJAX based comment in the submission status table
     *
     * @param stdClass $submission
     * @param bool $showviewlink - If the comments are long this is
     *                             set to true so they can be shown in a separate page
     * @return string
     */
    public function view_summary(stdClass $submission, & $showviewlink) {
        global $PAGE;
        $cmid = optional_param('id', null, PARAM_INT);
        if($cmid && !has_capability('mod/assignsubmission:post', context_module::instance($cmid))) {
          return;
        }

        // Never show a link to view full submission.
        $showviewlink = false;
        // Need to used this init() otherwise it does not have the javascript includes.
        comment::init();

        $options = new stdClass();
        $options->area    = 'submission_moderatorchat';
        $options->course    = $this->assignment->get_course();
        $options->context = $this->assignment->get_context();
        $options->itemid  = $submission->id;
        $options->component = 'assignsubmission_moderatorchat';
        $options->showcount = true;
        $options->displaycancel = true;

        $comment = new comment($options);

        $o = $this->assignment->get_renderer()->container($comment->output(true), 'commentscontainer');
        if ($PAGE->bodyid === 'page-mod-assign-gradingpanel') {
          return "<div><h3>" . get_string('pluginname', 'assignsubmission_moderatorchat') . "</h3>" . $o . "</div>";
        } else {
          return $o;
        }

    }

    /**
     * Always return true because the submission comments are not part of the submission form.
     *
     * @param stdClass $submission
     * @return bool
     */
    public function is_empty(stdClass $submission) {
        return true;
    }

    /**
     * Return true if this plugin can upgrade an old Moodle 2.2 assignment of this type
     * and version.
     *
     * @param string $type old assignment subtype
     * @param int $version old assignment version
     * @return bool True if upgrade is possible
     */
    public function can_upgrade($type, $version) {

        if ($type == 'upload' && $version >= 2011112900) {
            return true;
        }
        return false;
    }


    /**
     * Upgrade the settings from the old assignment to the new plugin based one
     *
     * @param context $oldcontext - the context for the old assignment
     * @param stdClass $oldassignment - the data for the old assignment
     * @param string $log - can be appended to by the upgrade
     * @return bool was it a success? (false will trigger a rollback)
     */
    public function upgrade_settings(context $oldcontext, stdClass $oldassignment, & $log) {
        if ($oldassignment->assignmenttype == 'upload') {
            // Disable if allow notes was not enabled.
            if (!$oldassignment->var2) {
                $this->disable();
            }
        }
        return true;
    }

    /**
     * Upgrade the submission from the old assignment to the new one
     *
     * @param context $oldcontext The context for the old assignment
     * @param stdClass $oldassignment The data record for the old assignment
     * @param stdClass $oldsubmission The data record for the old submission
     * @param stdClass $submission The new submission record
     * @param string $log Record upgrade messages in the log
     * @return bool true or false - false will trigger a rollback
     */
    public function upgrade(context $oldcontext,
                            stdClass $oldassignment,
                            stdClass $oldsubmission,
                            stdClass $submission,
                            & $log) {

        if ($oldsubmission->data1 != '') {

            // Need to used this init() otherwise it does not have the javascript includes.
            comment::init();

            $options = new stdClass();
            $options->area = 'submission_moderatorchat_upgrade';
            $options->course = $this->assignment->get_course();
            $options->context = $this->assignment->get_context();
            $options->itemid = $submission->id;
            $options->component = 'assignsubmission_moderatorchat';
            $options->showcount = true;
            $options->displaycancel = true;

            $comment = new comment($options);
            $comment->add($oldsubmission->data1);
            $comment->set_view_permission(true);

            return $comment->output(true);
        }

        return true;
    }

    /**
     * The submission comments plugin has no submission component so should not be counted
     * when determining whether to show the edit submission link.
     * @return boolean
     */
    public function allow_submissions() {
        return false;
    }

    /**
     * Automatically enable or disable this plugin based on "$CFG->commentsenabled"
     *
     * @return bool
     */
    public function is_enabled() {
        global $CFG;

        return (!empty($CFG->usecomments));
    }

    /**
     * Automatically hide the setting for the submission plugin.
     *
     * @return bool
     */
    public function is_configurable() {
        return false;
    }

    /**
     * Return the plugin configs for external functions.
     *
     * @return array the list of settings
     * @since Moodle 3.2
     */
    public function get_config_for_external() {
        return (array) $this->get_config();
    }

    public static function observe_marker_updated($event) {
      global $DB, $CFG;

      $submission = $DB->get_record('assign_submission', ['id'=>$event->objectid]);
      $student = $DB->get_record('user', ['id'=>$submission->userid]);
      $comments = $DB->get_records(
        'comments',
        ['contextid'=>$event->contextid, 'component'=>'assignsubmission_moderatorchat', 'commentarea'=>'submission_moderatorchat'], 'timecreated desc');
      // var_dump($comments);
      // echo "<br>";
      $messagetext = '';
      foreach($comments as $comment) {
        $user = $DB->get_record('user', ['id'=>$comment->userid]);
        $a = new stdClass();
        $a->username = fullname($user);
        $a->timecreated = userdate($comment->timecreated, get_string('strftimedate', 'langconfig'));
        $a->content = $comment->content;
        $messagetext .= get_string('commentinemail', 'assignsubmission_moderatorchat', $a);
      }
      if (!empty($messagetext)) {
        $user = $DB->get_record('user', array('id'=>$event->other['markerid']));
        $from = core_user::get_noreply_user();
        $subject = get_string('commentemailsubject', 'assignsubmission_moderatorchat');
        $a = new stdClass();
        $a->url = "{$CFG->wwwroot}/mod/assign/view.php?id={$event->contextinstanceid}&rownum=0&action=grader&userid={$submission->userid}";
        $a->fullname = fullname($student);
        $messagetext = get_string('commentemaillink', 'assignsubmission_moderatorchat', $a) . "<br>{$messagetext}";
        email_to_user($user, $from, $subject, html_to_text($messagetext), $messagetext);
      }
    }

    public static function observe_comment_created(\assignsubmission_moderatorchat\event\comment_created $event) {
      global $DB, $CFG;

      $submission = $DB->get_record('assign_submission', ['id'=>$event->other['itemid']]);
      $flags = $DB->get_record('assign_user_flags', ['assignment'=>$submission->assignment, 'userid'=>$submission->userid]);

      // Return early if no marker to send mail to.
      if (!$flags->allocatedmarker) {
        return;
      }
      // Return early if the commenter is the marker
      if ($flags->allocatedmarker == $event->userid) {
        return;
      }

      $comment = $DB->get_record('comments', ['id'=>$event->objectid]);
      $commenter = $DB->get_record('user', ['id'=>$event->userid]);
      $marker = $DB->get_record('user', ['id'=>$flags->allocatedmarker]);
      $student = $DB->get_record('user', ['id'=>$submission->userid]);

      $a = new stdClass();
      $a->commenter = fullname($commenter);
      $a->student = fullname($student);
      $a->timecreated = userdate($comment->timecreated, get_string('strftimedate', 'langconfig'));
      $a->content = $comment->content;
      $from = core_user::get_noreply_user();
      $subject = get_string('singlecommentsubject', 'assignsubmission_moderatorchat', $a);
      $a->url = "{$CFG->wwwroot}/mod/assign/view.php?id={$event->contextinstanceid}&rownum=0&action=grader&userid={$submission->userid}";
      $a->fullname = fullname($student);
      $messagetext = get_string('singlecommentbody', 'assignsubmission_moderatorchat', $a);
      email_to_user($marker, $from, $subject, html_to_text($messagetext), $messagetext);
    }
}
