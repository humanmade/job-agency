<?php

class Job_Agency_Job {

	public function __construct( $job_data ) {

		$this->id = $job_data->id;
		$this->type = $job_data->type;
		$this->created_date = strtotime( $job_data->created_date );
		$this->payload = unserialize( $job_data->payload );
		$this->status = $job_data->status;
	}

	/**
	 * Mark the job as started, so now one else will try and start it
	 */
	public function start() {
		$this->update_status( 'started' );
	}

	/**
	 * Update the status of the job to something
	 * 
	 * @param $status $string
	 */
	public function update_status( $status ) {

		global $wpdb;
		$this->status = $status;
		$wpdb->update( Job_Agency::get_table_name(), array( 'status' => $status ), array( 'id' => $this->id ) );
	}

	/**
	 * Set the job as completed, good work!
	 */
	public function complete() {

		global $wpdb;
		$this->status = 'completed';
		$wpdb->update( Job_Agency::get_table_name(), array( 'status' => 'completed', 'completed_date' => date( 'Y-m-d H:i:s' ) ), array( 'id' => $this->id ) );
	}

	/**
	 * Call the callable that will be responsible for handling this job
	 */
	public function call_handler() {

		if ( is_callable( $this->type ) )
			call_user_func( $this->type, $this->payload, $this );

		else
			do_action( 'job_agency_do_job_' . $this->type, $this->payload, $this );
	}
}