<?php

class Job_Agency_Job {

	private $id = null;
	private $type = '';
	private $status = '';
	private $payload = '';
	private $created_date = null;
	private $completed_date = null;
	private $result = '';

	public function __construct( $job_data ) {

		$this->id = $job_data->id;
		$this->type = $job_data->type;
		$this->status = $job_data->status;
		$this->created_date = strtotime( $job_data->created_date );
		$this->payload = maybe_unserialize( $job_data->payload );

		if ( $job_data->completed_date )
			$this->completed_date = strtotime( $job_data->completed_date );

		if ( isset( $job_data->result ) )
			$this->result = unserialize( $job_data->result );
	}

	/**
	 * Get the ID for the job
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Get the type for the job
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * Mark the job as started, so now one else will try and start it
	 */
	public function start() {
		$this->update_status( 'started' );
	}

	/**
	 * Get the status of the job
	 */
	public function get_status() {
		return $this->status;
	}

	/**
	 * Get the result of a job
	 */
	public function get_result() {
		return $this->result;
	}

	/**
	 * Update the status of the job to something
	 * 
	 * @param $status $string
	 */
	public function update_status( $status ) {

		$this->status = $status;
		$this->save();
		do_action( 'job_agency_job_updated_status', $status, $this );
	}

	/**
	 * Set the job as completed, good work!
	 */
	public function complete() {

		if ( is_wp_error( $this->result ) )
			$this->status = 'errored';
		else
			$this->status = 'completed';

		$this->completed_date = time();
		$this->save();
		do_action( 'job_agency_job_updated_status', $this->status, $this );
	}

	/**
	 * Save the current job state to the database
	 */
	public function save() {
		global $wpdb;

		return (bool)$wpdb->update( Job_Agency::get_table_name(),
			array(
				'type'              => $this->type,
				'status'            => $this->status,
				'created_date'      => date( 'Y-m-d H:i:s', $this->created_date ),
				'completed_date'    => $this->completed_date ? date( 'Y-m-d H:i:s', $this->completed_date ) : null,
				'payload'           => serialize( $this->payload ),
				'result'            => $this->result === null ? '' : serialize( $this->result ),
			), array( 'id' => $this->id ) );
	}

	/**
	 * Call the callable that will be responsible for handling this job
	 */
	public function call_handler() {

		if ( is_callable( $this->type ) )
			$this->result = call_user_func( $this->type, $this->payload );
		else
			$this->result = apply_filters( 'job_agency_do_job_' . $this->type, $this->payload, $this );
	}
}