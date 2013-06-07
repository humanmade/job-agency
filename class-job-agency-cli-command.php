<?php

class Job_Agency_CLI_Command extends WP_CLI_Command {

	/**
	 * Find work for this process, will make this process now block
	 * 
	 * @subcommand find-work
	 */
	public function find_work() {

		$worker = Job_Agency_Worker();

		$worker->start_work();
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
}

WP_CLI::add_command( 'job-agency', 'Job_Agency_CLI_Command' );