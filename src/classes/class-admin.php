<?php

/**
 * Class for Admin configurations.
 */
class AdminClass {


	/**
	 * Constructor method
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'loop_events_cpt' ), 0 );
		add_action( 'add_meta_boxes_loop-events', array( $this, 'meta_box_for_products' ) );
		add_action( 'save_post_loop-events', array( $this, 'lpv_save_meta_box_data' ) );

		// Add the custom columns to the loop events post type:
		add_filter( 'manage_loop-events_posts_columns', array( $this, 'set_custom_columns' ) );
		// Add the data to the custom columns for the loop events post type:
		add_action( 'manage_loop-events_posts_custom_column', array( $this, 'get_custom_columns' ), 10, 2 );

		add_action( 'cli_init', array( $this, 'loop_events_cli_register_commands' ) );

	}


	/**
	 * Method to register post type.
	 *
	 * @return void
	 */
	public function loop_events_cpt() {

		$labels = array(
			'name'                  => _x( 'Loop Events', 'Post Type General Name', 'loop-events' ),
			'singular_name'         => _x( 'Loop Event', 'Post Type Singular Name', 'loop-events' ),
			'menu_name'             => __( 'Loop Events', 'loop-events' ),
			'name_admin_bar'        => __( 'Loop Event', 'loop-events' ),
			'archives'              => __( 'Item Archives', 'loop-events' ),
			'attributes'            => __( 'Item Attributes', 'loop-events' ),
			'parent_item_colon'     => __( 'Parent Item:', 'loop-events' ),
			'all_items'             => __( 'All Items', 'loop-events' ),
			'add_new_item'          => __( 'Add New Item', 'loop-events' ),
			'add_new'               => __( 'Add New', 'loop-events' ),
			'new_item'              => __( 'New Item', 'loop-events' ),
			'edit_item'             => __( 'Edit Item', 'loop-events' ),
			'update_item'           => __( 'Update Item', 'loop-events' ),
			'view_item'             => __( 'View Item', 'loop-events' ),
			'view_items'            => __( 'View Items', 'loop-events' ),
			'search_items'          => __( 'Search Item', 'loop-events' ),
			'not_found'             => __( 'Not found', 'loop-events' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'loop-events' ),
			'featured_image'        => __( 'Featured Image', 'loop-events' ),
			'set_featured_image'    => __( 'Set featured image', 'loop-events' ),
			'remove_featured_image' => __( 'Remove featured image', 'loop-events' ),
			'use_featured_image'    => __( 'Use as featured image', 'loop-events' ),
			'insert_into_item'      => __( 'Insert into item', 'loop-events' ),
			'uploaded_to_this_item' => __( 'Uploaded to this item', 'loop-events' ),
			'items_list'            => __( 'Items list', 'loop-events' ),
			'items_list_navigation' => __( 'Items list navigation', 'loop-events' ),
			'filter_items_list'     => __( 'Filter items list', 'loop-events' ),
		);
		$args   = array(
			'label'               => __( 'Loop Event', 'loop-events' ),
			'description'         => __( 'Loop events', 'loop-events' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor' ),
			'taxonomies'          => array( 'loop-tags' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-admin-site-alt',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'page',
			'show_in_rest'        => true,
		);
		register_post_type( 'loop-events', $args );

		$labels = array(
			'name'                       => _x( 'Tags', 'Taxonomy General Name', 'loop-events' ),
			'singular_name'              => _x( 'Tag', 'Taxonomy Singular Name', 'loop-events' ),
			'menu_name'                  => __( 'Tags', 'loop-events' ),
			'all_items'                  => __( 'All Items', 'loop-events' ),
			'parent_item'                => __( 'Parent Item', 'loop-events' ),
			'parent_item_colon'          => __( 'Parent Item:', 'loop-events' ),
			'new_item_name'              => __( 'New Item Name', 'loop-events' ),
			'add_new_item'               => __( 'Add New Item', 'loop-events' ),
			'edit_item'                  => __( 'Edit Item', 'loop-events' ),
			'update_item'                => __( 'Update Item', 'loop-events' ),
			'view_item'                  => __( 'View Item', 'loop-events' ),
			'separate_items_with_commas' => __( 'Separate items with commas', 'loop-events' ),
			'add_or_remove_items'        => __( 'Add or remove items', 'loop-events' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'loop-events' ),
			'popular_items'              => __( 'Popular Items', 'loop-events' ),
			'search_items'               => __( 'Search Items', 'loop-events' ),
			'not_found'                  => __( 'Not Found', 'loop-events' ),
			'no_terms'                   => __( 'No items', 'loop-events' ),
			'items_list'                 => __( 'Items list', 'loop-events' ),
			'items_list_navigation'      => __( 'Items list navigation', 'loop-events' ),
		);
		$args   = array(
			'labels'            => $labels,
			'hierarchical'      => false,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => true,
			'show_tagcloud'     => true,
		);
		register_taxonomy( 'loop-tags', array( 'loop-events' ), $args );

	}

	/**
	 * Adding meta boxes for custom fields.
	 *
	 * @param $event
	 *
	 * @return void
	 */
	public function meta_box_for_products( $event ) {
		add_meta_box( 'my_meta_box_custom_id', __( 'Event details', 'loop-events' ), array( $this, 'loop_events_custom_meta_box_html_output' ), 'loop-events', 'normal', 'low' );
	}

	/**
	 * Method to add custom fields html.
	 *
	 * @param $event
	 *
	 * @return void
	 */
	public function loop_events_custom_meta_box_html_output( $event ) {

		// Add a nonce field so we can check for it later.
		wp_nonce_field( 'lpv_save_meta_box_data', 'lpv_meta_box_nonce' );

		?>
		<table style="text-align:right;">
			<tbody>
				<tr>
					<th><label for="lpv_organise_name"> <?php esc_attr_e( 'Organiser:', 'loop-events' ); ?></th>
					<td><input type="text" id="lpv_organiser" name="lpv_organiser" value="<?php echo esc_attr( get_post_meta( $event->ID, '_lpv_organiser', true ) ); ?>" size="50" /></td>
				</tr>
				<tr>
					<th><label for="lpv_timestamp"> <?php esc_attr_e( 'Time:', 'loop-events' ); ?></th>
					<td><input type="text" id="lpv_timestamp" name="lpv_timestamp" value="<?php echo esc_attr( get_post_meta( $event->ID, '_lpv_timestamp', true ) ); ?>" size="50" /></td>
				</tr>
				<tr>
					<th><label for="lpv_email"> <?php esc_attr_e( 'Email:', 'loop-events' ); ?></th>
					<td><input type="email" id="lpv_email" name="lpv_email" value="<?php echo esc_attr( get_post_meta( $event->ID, '_lpv_email', true ) ); ?>" size="50" /></td>
				</tr>
				<tr>
					<th><label for="lpv_address"> <?php esc_attr_e( 'Address:', 'loop-events' ); ?></th>
					<td><textarea id="lpv_address" name="lpv_address" cols="50" rows="6" /><?php echo esc_attr( get_post_meta( $event->ID, '_lpv_address', true ) ); ?></textarea></td>
				</tr>
				<tr>
					<th><label for="lpv_latitude"> <?php esc_attr_e( 'Latitude:', 'loop-events' ); ?></th>
					<td><input type="text" id="lpv_latitude" name="lpv_latitude" value="<?php echo esc_attr( get_post_meta( $event->ID, '_lpv_latitude', true ) ); ?>" size="50" /></td>
				</tr>
				<tr>
					<th><label for="lpv_longitude"> <?php esc_attr_e( 'Latitude:', 'loop-events' ); ?></th>
					<td><input type="text" id="lpv_longitude" name="lpv_longitude" value="<?php echo esc_attr( get_post_meta( $event->ID, '_lpv_longitude', true ) ); ?>" size="50" /></td>
				</tr>

			</tbody>
		</table>
		<?php

	}

	/**
	 * When the event is saved, saves our custom data.
	 *
	 * @param int $event_id The ID of the event being saved.
	 */
	public function lpv_save_meta_box_data( $event_id ) {

		if ( ! isset( $_POST['lpv_meta_box_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['lpv_meta_box_nonce'] ) ), 'lpv_save_meta_box_data' ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check the user's permissions.
		if ( ! empty( sanitize_text_field( wp_unslash( $_POST['post_type'] ) ) ) && 'loop-events' === sanitize_text_field( wp_unslash( $_POST['post_type'] ) ) ) {

			if ( ! current_user_can( 'edit_page', $event_id ) ) {
				return;
			}
		} else {

			if ( ! current_user_can( 'edit_post', $event_id ) ) {
				return;
			}
		}

		$lpv_organiser = ! empty( sanitize_text_field( wp_unslash( $_POST['lpv_organiser'] ) ) ) ? sanitize_text_field( wp_unslash( $_POST['lpv_organiser'] ) ) : '';
		update_post_meta( $event_id, '_lpv_organiser', $lpv_organiser );

		$lpv_timestamp = ! empty( sanitize_text_field( wp_unslash( $_POST['lpv_timestamp'] ) ) ) ? sanitize_text_field( wp_unslash( $_POST['lpv_timestamp'] ) ) : '';
		update_post_meta( $event_id, '_lpv_timestamp', $lpv_timestamp );

		$lpv_email = ! empty( sanitize_text_field( wp_unslash( $_POST['lpv_email'] ) ) ) ? sanitize_text_field( wp_unslash( $_POST['lpv_email'] ) ) : '';
		update_post_meta( $event_id, '_lpv_email', $lpv_email );

		$lpv_address = ! empty( sanitize_text_field( wp_unslash( $_POST['lpv_address'] ) ) ) ? sanitize_text_field( wp_unslash( $_POST['lpv_address'] ) ) : '';
		update_post_meta( $event_id, '_lpv_address', $lpv_address );

		$lpv_latitude = ! empty( sanitize_text_field( wp_unslash( $_POST['lpv_latitude'] ) ) ) ? sanitize_text_field( wp_unslash( $_POST['lpv_latitude'] ) ) : '';
		update_post_meta( $event_id, '_lpv_latitude', $lpv_latitude );

		$lpv_longitude = ! empty( sanitize_text_field( wp_unslash( $_POST['lpv_longitude'] ) ) ) ? sanitize_text_field( wp_unslash( $_POST['lpv_longitude'] ) ) : '';
		update_post_meta( $event_id, '_lpv_longitude', $lpv_longitude );

	}

	/**
	 * Setting custom columns for events custom post type table listing.
	 *
	 * @param $columns
	 *
	 * @return mixed
	 */
	public function set_custom_columns( $columns ) {

		unset( $columns['author'] );
		unset( $columns['date'] );

		$columns['lpv_organiser'] = __( 'Event Organiser', 'loop-events' );
		$columns['lpv_time']      = __( 'Event Time', 'loop-events' );

		return $columns;
	}


	/**
	 * Getting custom columns values for custom post type listing page.
	 *
	 * @param $column
	 * @param $event_id
	 *
	 * @return void
	 */
	public function get_custom_columns( $column, $event_id ) {
		switch ( $column ) {

			case 'lpv_organiser':
				echo esc_html( get_post_meta( $event_id, '_lpv_organiser', true ) );
				break;

			case 'lpv_time':
				$new = new Loop_Events_Helper();
				echo ! empty( get_post_meta( $event_id, '_lpv_timestamp', true ) ) ? esc_html( $new->get_human_readable_time( get_post_meta( $event_id, '_lpv_timestamp', true ) ) ) : '';
				break;

		}
	}

	/**
	 * Add custom WPCLI command.
	 *
	 * @return void
	 */
	public function loop_events_cli_register_commands() {
		WP_CLI::add_command( 'lps', 'Loop_Events_Import' );
	}
}

?>
