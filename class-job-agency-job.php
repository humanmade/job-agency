<?php

class Job_Agency_Job {

	public function __construct( $job_data ) {

		$this->id = $job_data->id;
		$this->type = $data->type;
		$this->created_date = strtotime( $job_data->created_date );
		$this->payload = unserialize( $job_data->payload );
	}

	/**
	 * Mark the job as started, so now one else will try and start it
	 */
	public function start_job() {
		$this->update_status( 'started' );
	}

	/**
	 * Update the status of the job to something
	 * 
	 * @param $status $string
	 */
	public function update_status( $status ) {

		global $wpdb;

		$wpdb->update( array( 
			'id' => $this->id,
			'status' => $status
		));
	}

	/**
	 * Set the job as completed, good work!
	 */
	public function complete_job() {

		global $wpdb;

		$wpdb->update( array( 
			'id' => $this->id,
			'status' => 'completed',
			'completed_date' => date( 'Y-m-d H:i:s' )
		));
	}

	/**
	 * Call the callable that will be responsible for handling this job
	 */
	public function call_handler() {

		if ( is_callable( $this->type ) )
			call_user_func( $this->type, $this->payload );

		else
			do_action( 'job_agency_do_job_' . $this->type, $this->payload );
	}
}