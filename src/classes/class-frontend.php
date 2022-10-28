<?php


/**
 * This class contains all methods used to create shortcode
 *
 * @link  https://iamankitpanchal.com/
 * @since 1.0.0
 *
 * @package    Loop_Events
 * @subpackage Loop_Events/src/classes
 */


/**
 * This class contains all methods used to create shortcode.
 *
 * @since      1.0.0
 * @package    Loop_Events
 * @subpackage Loop_Events/src/classes
 * @author     Ankit Panchal <ankitpanchalweb7@gmail.com>
 */

class Loop_Events_Shortcode {

	/**
	 * Variable for Helper class.
	 *
	 * @var Loop_Events_Helper
	 */
	public $helper;

	/**
	 * Constructor method
	 */
	public function __construct() {
		add_shortcode( 'lps_upcoming_events', array( $this, 'display_events' ) );

		$this->helper = new Loop_Events_Helper();
	}


	/**
	 * Method to display upcoming events for shortcode.
	 *
	 * @return false|string
	 */
	public function display_events() {

		ob_start();
		$content = __( 'View Upcoming Events:', 'loop-events' ) . ' <a href="' . plugin_dir_url( LOOP_EVENTS_ROOT ) . 'upcoming-events.json" target="_blank">' . __( 'View', 'loop-events' ) . '</a>';
		$content .= '<table class="pure-table pure-table-bordered">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>#ID</th>
                                <th>Title</th>
                                <th>Organiser</th>
                                <th>Timestamp</th>
                                <th>Email</th>
                                <th>Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            {events_table}
                        </tbody>
                    </table>';

		$events = $this->helper->get_events( strtotime( gmdate( 'Y-m-d H:i:s' ) ) );

		$table_body = '';
		if ( ! empty( $events ) ) {
			$index = 1;
			foreach ( $events as $event ) {
				$table_body .= '<tr>
                                <td>' . $index . '</td>
                                <td>' . get_post_meta( $event->ID, '_lpv_event_id', true ) . '</td>
                                <td>' . $event->post_title . '</td>
                                <td>' . get_post_meta( $event->ID, '_lpv_organiser', true ) . '</td>
                                <td>' . $this->helper->get_human_readable_time( get_post_meta( $event->ID, '_lpv_timestamp', true ) ) . '</td>
                                <td>' . get_post_meta( $event->ID, '_lpv_email', true ) . '</td>
                                <td>' . get_post_meta( $event->ID, '_lpv_address', true ) . '</td>
                            </tr>';
				$index++;
			}
		}
		echo str_replace( '{events_table}', $table_body, $content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		$output = ob_get_contents();

		ob_end_clean();

		return $output;

	}

}


