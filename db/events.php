<?php

$observers = array(

    array(
        'eventname'   => '\mod_assign\event\marker_updated',
        'callback'    => 'assign_submission_moderatorchat::observe_marker_updated',
        'internal'    => true
    ),
);
