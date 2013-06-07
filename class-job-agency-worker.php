<?php

class Job_Agency_Worker {

	public $date_started_work = null;

	public function start_work() {

		$this->date_started_work = time();

		while( $job = Job_Agency::get_new_job() ) {

			$job->start();
			$job->call_handler();
			$job->completed();

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