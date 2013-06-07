<?php

class Job_Agency {

	/**
	 * Create the jobs table
	 */
	public static function create_table() {

		global $wpdb;

		$wpdb->query( "CREATE TABLE IF NOT EXISTS`" . self::get_table_name() . "` (
			`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`type` varchar(300) NOT NULL DEFAULT '',
			`created_date` datetime NOT NULL,
			`completed_date` datetime DEFAULT NULL,
			`status` varchar(300) DEFAULT '',
			`payload` longtext,
			`result` longtext,
			PRIMARY KEY (`id`)
			) ENGINE=InnoDB") ;
	}

	/**
	 * Add a new job to the job queue
	 * 
	 * @param $type string
	 * @param $payload mixed
	 * @return int
	 */
	public static function queue_job( $type, $payload = null ) {

		global $wpdb;

		$wpdb->insert(
			self::get_table_name(),
			array( 
				'type' => $type,
				'status' => 'queued',
				'created_date' => date( 'Y-m-d H:i:s' ),
				'payload' => serialize( $payload )
				)
		);
		return $wpdb->insert_id;
	}

	/**
	 * Get the amount of not-started jobs
	 * 
	 * @return int
	 */
	public static function get_jobs_queued_count() {

		global $wpdb;

		return (int) $wpdb->get_var( "SELECT count(id) FROM " . self::get_table_name() . " WHERE `status` = 'queued'" );
	}

	/**
	 * Get a job based on its ID
	 * 
	 * @param int 
	 * @return object|null
	 */
	public static function get_job( $job_id ) {
		global $wpdb;

		$job = $wpdb->get_row( $wpdb->prepare(
			"SELECT * FROM " . self::get_table_name() . " WHERE `id` = %d",
			$job_id
			)
		);

		if ( ! $job )
			return null;

		return new Job_Agency_Job( $job );
	}

	/**
	 * Get a new job which has not been started
	 */
	public static function get_new_job() {

		global $wpdb;

		$job = $wpdb->get_row(
			"SELECT * FROM " . self::get_table_name() . " WHERE `status` = 'queued' LIMIT 1"
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

	/**
	 * Stop all the current workers on jobs, this won't interrupt a worker which it is doing a job
	 * but will cause that worker to stop after the job is completed
	 */
	public static function fire_workers() {
		$fire_date = time();

		// allow external sources to hook in here, as they may not have memcached object cache
		if ( apply_filters( 'job_agency_update_last_fire_date', null, time() ) )
			return;

		global $wp_object_cache;

		if ( ! method_exists( $wp_object_cache, 'get_mc' ) )
			return false;

		$wp_object_cache->get_mc( 'default' )->set( 'job_agency_last_fire_date', time() );
	}

	public static function get_last_fire_date() {

		if ( $time = apply_filters( 'job_agency_get_last_fire_date', null, time() ) !== null)
			return $time;

		global $wp_object_cache;

		if ( ! method_exists( $wp_object_cache, 'get_mc' ) )
			return false;

		return $wp_object_cache->get_mc( 'default' )->get( 'job_agency_last_fire_date' );
	}
}