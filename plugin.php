<?php

/**
 * Plugin Name: Job Agency
 * 
 */

require_once 'class-job-agency.php';
require_once 'class-job-agency-job.php';
require_once 'class-job-agency-worker.php';
require_once 'class-job-agency-cli-command.php';


add_action( 'init', function() {

	//Job_Agency::add_job( 'upload-backup', array( 'site_id' => 1 ) );

	$job = Job_Agency::get_new_job();
	$job->complete();

	var_dump($job);
	exit;
});