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
 * Strings for component 'submission_moderatorchat', language 'en'
 *
 * @package   assignsubmission_moderatorchat
 */

$string['blindmarkingname'] = 'Participant {$a}';
$string['blindmarkingviewfullname'] = 'Participant {$a->participantnumber} ({$a->participantfullname})';
$string['privacy:metadata:commentpurpose'] = 'Comments between the moderator and marker about a submission.';
$string['default'] = 'Enabled by default';
$string['default_help'] = 'If set, this submission method will be enabled by default for all new assignments.';
$string['enabled'] = 'Moderator chat';
$string['enabled_help'] = 'If enabled, students can leave comments on their own submission. For example, this can be used for students to specify which is the master file when submitting inter-linked files.';
$string['pluginname'] = 'Moderator chat';
$string['commentemailsubject'] = 'Submission assigned with moderation comments';
$string['commentemaillink'] = 'You are now the allocated marker for <a href="{$a->url}">{$a->fullname}\'s submission</a>.<br>The moderator chat history is included below.';
$string['commentinemail'] = '<blockquote>{$a->content}<footer><small>&mdash; {$a->username} on {$a->timecreated}</small></footer></blockquote>';
$string['singlecommentsubject'] = 'Comment on {$a->student}\'s submission';
$string['singlecommentbody'] = '{$a->commenter} has commented on <a href="{$a->url}">{$a->student}\'s submission</a><br><blockquote>{$a->content}<footer><small>&mdash; {$a->timecreated}</small></footer></blockquote>';
