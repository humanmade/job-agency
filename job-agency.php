<?php

/**
 * Plugin Name: Job Agency
 * 
 */

require_once 'class-job-agency.php';
require_once 'class-job-agency-job.php';
require_once 'class-job-agency-worker.php';

if ( defined( 'WP_CLI' ) && WP_CLI )
	require_once 'class-job-agency-cli-command.php';

add_action( 'admin_init', array( 'Job_Agency', 'create_table' ) );

/**
 * Queue a job for the Job Agency to complete
 *
 * @param string $type
 * @param mixed $payload
 * @return int $job_id
 */
function job_agency_queue_job( $type, $payload ) {
	return Job_Agency::queue_job( $type, $payload );
}

/**
 * Get the job status for a given job
 * 
 * @param int
 * @return string
 */
function job_agency_get_job_status( $job_id ) {
	$job = Job_Agency::get_job( $job_id );
	if ( ! $job )
		return '';

	return $job->get_status();	
}

/**
 * Get the result of a given job
 */
function job_agency_get_job_result( $job_id ) {
	$job = Job_Agency::get_job( $job_id );
	if ( ! $job )
		return '';

	return $job->get_result();
}
