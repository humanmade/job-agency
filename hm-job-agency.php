<?php

/**
 * Plugin Name: Job Agency
 * 
 */

class Job_Agency {

	/**
	 * Create the jobs table
	 */
	public static function create_table() {

		global $wpdb;

		$wpdb->query( "CREATE TABLE `" . $this->get_table_name() . "` (
			`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`type` varchar(300) NOT NULL DEFAULT '',
			`created_date` datetime NOT NULL,
			`completed_date` datetime DEFAULT NULL,
			`status` varchar(300) DEFAULT NULL,
			`payload` longtext,
			PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT") ;
	}

	/**
	 * Add a new job to the job queue
	 * 
	 * @param $type string
	 * @param $payload mixed
	 */
	public static function add_job( $type, $payload ) {

		$wpdb->insert(
			$this->get_table_name(),
			array( 'type' => $type, 'created_date' => date( 'Y-m-d H:i:s' ), json_encode( $payload ) ),
		);
	}

	/**
	 * Get a new job which has not been started
	 */
	public static function get_new_job() {

		$job = $wpdb->get_row(
			"SELECT * FROM " . $this->get_table_name() . " WHERE status = '' LIMIT 1"
		);

		if ( ! $job )
			return null;

		return new Job_Agency_Job( $job );
	}

	/**
	 * Get the table name for the job queue
	 */
	private static function get_table_name() {

		global $wpdb;
		return $wpdb->prefix . '_jobs';
	}
}

class Job_Agency_Job {

	public function __construct( $job_data ) {

		$this->id = $job_data->id;
		$this->type = $data->type;
		$this->created_date = strtotime( $job_data->created_date );
		$this->payload = json_decode( $job_data->payload );
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
			'status' => $status,
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