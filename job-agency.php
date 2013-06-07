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