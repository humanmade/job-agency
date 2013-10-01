<?php

class Job_Agency_Worker {

	public $date_started_work = null;

	public function start_work() {

		if ( ! defined( 'JOB_AGENCY_JOB' ) )
			define( 'JOB_AGENCY_JOB', true );
		
		$this->date_started_work = time();

		while( $job = Job_Agency::get_new_job() ) {

			$job->start();
			$job->call_handler();
			$job->complete();

			if ( $this->is_fired() )
				return;
		}
	}

	private function is_fired() {
		if ( $fire_date = Job_Agency::get_last_fire_date() )
			return $fire_date > $this->date_started_work;

		return false;
	}
}