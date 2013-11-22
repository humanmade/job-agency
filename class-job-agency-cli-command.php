<?php

class Job_Agency_CLI_Command extends WP_CLI_Command {

	/**
	 * Find work for this process, will make this process now block
	 * 
	 * @subcommand find-work [--max_jobs=<max-jobs>]
	 */
	public function find_work( $args, $assoc_args ) {

		WP_CLI::line( sprintf( "[%s] Worker %d started looking for work.", date( 'Y-m-d H:i:s' ), getmypid() ) );

		$worker = new Job_Agency_Worker();

		$worker->start_work( $assoc_args );

		WP_CLI::line( sprintf( "[%s] Worker %d completed its work.", date( 'Y-m-d H:i:s' ), getmypid() ) );
	}

	/**
	 * Check how many jobs there are to be done
	 * 
	 * @subcommand check-employment-needs
	 */
	public function check_employment_needs() {

		WP_CLI::line( Job_Agency::get_jobs_queued_count() );
	}

	/**
	 * Fire all currently working workers
	 * 
	 * @subcommand fire-workers
	 */
	public function fire_workers() {

		Job_Agency::fire_workers();
		WP_CLI::success( 'Workers have been fired, they will exit once they have finished their current jobs.' );
	}

	/**
	 * Cancel all jobs
	 * 
	 * @subcommand cancel-jobs
	 */
	public function cancel_jobs() {

		$jobs = Job_Agency::cancel_jobs();
		WP_CLI::success( sprintf( "All %d open jobs were canceled.", $jobs ) );
	}
}

WP_CLI::add_command( 'job-agency', 'Job_Agency_CLI_Command' );