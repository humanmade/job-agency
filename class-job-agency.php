<?php

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

		global $wpdb;

		$wpdb->insert(
			self::get_table_name(),
			array( 'type' => $type, 'created_date' => date( 'Y-m-d H:i:s' ), 'payload' => serialize( $payload ) )
		);
	}

	/**
	 * Get a new job which has not been started
	 */
	public static function get_new_job() {

		global $wpdb;

		$job = $wpdb->get_row(
			"SELECT * FROM " . self::get_table_name() . " WHERE `status` IS NULL LIMIT 1"
		);

		if ( ! $job )
			return null;

		return new Job_Agency_Job( $job );
	}

	/**
	 * Get the table name for the job queue
	 */
	public static function get_table_name() {

		global $wpdb;
		return $wpdb->prefix . 'jobs';
	}
}