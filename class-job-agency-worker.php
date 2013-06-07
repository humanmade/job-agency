<?php

class Job_Agency_Worker {

	public function start_work() {

		while( $job = Job_Agency::get_new_job() ) {
			
			$job->start();
			$job->call_handler();
			$job->completed();
		}
	}
}