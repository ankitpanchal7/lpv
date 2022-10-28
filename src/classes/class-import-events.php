<?php

/**
 * Fired during plugin activation
 *
 * @link  https://iamankitpanchal.com/
 * @since 1.0.0
 *
 * @package    Loop_Events
 * @subpackage Loop_Events/src/classes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Loop_Events
 * @subpackage Loop_Events/src/classes
 * @author     Ankit Panchal <ankitpanchalweb7@gmail.com>
 */
class Loop_Events_Import {

	/**
	 * Variable to store API URL.
	 *
	 * @var string
	 */
	public $api_url = LOOP_EVENTS_API_URL;

	/**
	 * Variable for helper class.
	 *
	 * @var Loop_Events_Helper
	 */
	public $helper;

	/**
	 * Constructor method.
	 */
	public function __construct() {
		$this->helper = new Loop_Events_Helper();
	}

	/**
	 * This method is used to import events from json (remote) object.
	 *
	 * @since 1.0.0
	 */
	public function import_events() {

		global $wpdb;

		if ( empty( $this->api_url ) ) {
			WP_CLI::error( 'Something went wrong! Please check your API URL.' );
		}

		$args = array(
			'headers' => array( 'Content-type' => 'application/json' ),
		);

		$response      = wp_remote_get( $this->api_url, $args );
		$response_code = wp_remote_retrieve_response_code( $response );

		if ( 200 !== absint( $response_code ) ) {
			WP_CLI::error( 'Data not received, Something went wrong on API server.' );
		}

		$events = json_decode( wp_remote_retrieve_body( $response ) );

		$total_events = (int) count( $events );

		$progress = \WP_CLI\Utils\make_progress_bar( 'Importing events', $total_events );

		$updated_events  = 0;
		$imported_events = 0;
		$export_events   = array();

		if ( ! empty( $events ) ) {
			foreach ( $events as $event ) {

				$new_event = array(
					'post_title'   => $event->title,
					'post_content' => $event->about,
					'post_status'  => 'publish',
					'post_date'    => gmdate( 'Y-m-d H:i:s' ),
					'post_type'    => 'loop-events',
				);

				$event_id = $wpdb->get_var(
					$wpdb->prepare( "SELECT `post_id` FROM $wpdb->postmeta WHERE meta_key = %s AND meta_value = %d LIMIT 1", '_lpv_event_id', $event->id )
				);

				if ( $event_id ) {
					$new_event['ID'] = $event_id;
					wp_update_post( $new_event );
					$updated_events++;
				} else {
					$event_id = wp_insert_post( $new_event );
					$imported_events++;
				}

				if ( is_array( $event->tags ) ) {
					wp_set_post_terms( $event_id, $event->tags, 'loop-tags' );
				}

				update_post_meta( $event_id, '_lpv_event_id', $event->id );
				update_post_meta( $event_id, '_lpv_organiser', $event->organizer );
				if ( ! empty( $event->timestamp ) ) {
					$event_date = new DateTime( $event->timestamp );
					update_post_meta( $event_id, '_lpv_timestamp', $event_date->getTimestamp() );
				}
				update_post_meta( $event_id, '_lpv_email', $event->email );
				update_post_meta( $event_id, '_lpv_address', $event->address );
				update_post_meta( $event_id, '_lpv_latitude', $event->latitude );
				update_post_meta( $event_id, '_lpv_longitude', $event->longitude );

				$today = strtotime( gmdate( 'Y-m-d H:i:s' ) );
				if ( $event_date->getTimestamp() >= $today ) {
					array_push( $export_events, $event );
				}
				$progress->tick();
			}
		}
		$progress->finish();

		WP_CLI::line( $updated_events . ' - Events updated successfully.' );
		WP_CLI::line( $imported_events . ' - Events added successfully.' );
		WP_CLI::line( $total_events . ' - Events synced successfully.' );

		$to      = 'logging@agentur-loop.com';
		$subject = 'Events synced successfully.';
		$headers = 'From: ' . $to . "\r\n" .
		'Reply-To: ' . $to . "\r\n";

		$helper = new Loop_Events_Helper();
		$body   = $helper->get_email_template();

		$body = str_replace( '{updated_events}', $updated_events, $body );
		$body = str_replace( '{imported_events}', $imported_events, $body );

		//Here put your Validation and send mail
		$sent = wp_mail( $to, $subject, $body, $headers );
		if ( $sent ) {
			WP_CLI::line( 'Email notification sent successfully.' );
		} else {
			WP_CLI::line( 'Something went wrong! Email notification not sent successfully.' );
		}

		// Sort the array
		usort( $export_events, array( $this, 'date_compare' ) );

		if ( is_array( $export_events ) ) {
			$events_json = wp_json_encode( $export_events );

			WP_Filesystem();
			global $wp_filesystem;
			$wp_filesystem->put_contents( LOOP_EVENTS_FILE_ROOT . '/upcoming-events.json', $events_json );
		}
	}

	/**
	 * Date compare logic.
	 *
	 * @param $el_one
	 * @param $el_two
	 *
	 * @return false|int
	 */
	public function date_compare( $el_one, $el_two ) {
		$dt_one = strtotime( $el_one->timestamp );
		$dt_two = strtotime( $el_two->timestamp );
		return $dt_one - $dt_two;
	}

}


