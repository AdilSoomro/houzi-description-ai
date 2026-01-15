<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://https://booleanbites.com
 * @since      1.0.0
 *
 * @package    Houzi_Description_Ai
 * @subpackage Houzi_Description_Ai/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Houzi_Description_Ai
 * @subpackage Houzi_Description_Ai/admin
 * @author     BooleanBites Ltd <houzi@booleanbites.com>
 */
class Houzi_Description_Ai_Admin
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Houzi_Description_Ai_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Houzi_Description_Ai_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/houzi-description-ai-admin.css', array(), $this->version, 'all');

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts($hook)
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Houzi_Description_Ai_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Houzi_Description_Ai_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		$deps = array('jquery');
		if (in_array($hook, array('post.php', 'post-new.php'))) {
			$deps = array('jquery', 'wp-data', 'wp-editor', 'wp-edit-post');
		}

		wp_enqueue_script($this->plugin_name . '-admin', plugin_dir_url(__FILE__) . 'js/houzi-description-ai-admin.js', $deps, $this->version, true);

		$post_id = 0;
		if (in_array($hook, array('post.php', 'post-new.php'))) {
			$post_id = get_the_ID();
		}

		wp_localize_script($this->plugin_name . '-admin', 'houzi_ai_obj', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('houzi_ai_nonce'),
			'post_id' => $post_id,
		));

	}

	/**
	 * Add property meta box.
	 */
	public function add_property_meta_box()
	{
		$post_types = array('property', 'houzez_agent', 'houzez_agency');
		foreach ($post_types as $post_type) {
			add_meta_box(
				'houzi_ai_description_meta_box',
				__('Houzi AI Description', 'houzi-description-ai'),
				array($this, 'render_property_meta_box'),
				$post_type,
				'side',
				'default'
			);
		}
	}

	/**
	 * Render property meta box.
	 */
	public function render_property_meta_box($post)
	{
		?>
		<div class="houzi-ai-metabox-content">
			<button type="button" id="houzi-generate-single-btn" class="button button-primary button-large"
				style="width: 100%;">
				<?php _e('Generate AI Description', 'houzi-description-ai'); ?>
			</button>
			<div id="houzi-ai-single-status" style="margin-top: 10px; font-size: 13px;"></div>
		</div>
		<?php
	}

	/**
	 * Add admin menu.
	 */
	public function add_admin_menu()
	{
		add_menu_page(
			__('Houzi AI', 'houzi-description-ai'),
			__('Houzi AI', 'houzi-description-ai'),
			'manage_options',
			$this->plugin_name,
			array($this, 'display_plugin_setup_page'),
			'dashicons-superhero',
			60
		);
	}

	/**
	 * Register settings.
	 */
	public function register_settings()
	{
		register_setting($this->plugin_name, 'houzi_ai_api_key');
		register_setting($this->plugin_name, 'houzi_ai_gemini_model', array(
			'default' => 'gemini-flash-latest',
		));
	}

	/**
	 * Display the setup page.
	 */
	public function display_plugin_setup_page()
	{
		include_once plugin_dir_path(__FILE__) . 'partials/houzi-description-ai-admin-display.php';
	}

	/**
	 * AJAX handler to get total properties.
	 */
	public function houzi_get_total_posts()
	{
		check_ajax_referer('houzi_ai_nonce', 'nonce');

		$scope = isset($_POST['scope']) ? sanitize_text_field($_POST['scope']) : 'all';
		$post_type = isset($_POST['post_type']) ? sanitize_text_field($_POST['post_type']) : 'property';

		$args = array(
			'post_type' => $post_type,
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'fields' => 'ids',
		);

		if ($scope === 'pending') {
			$args['meta_query'] = array(
				array(
					'key' => 'ai_content',
					'compare' => 'NOT EXISTS',
				),
			);
		}

		$query = new WP_Query($args);
		$ids = $query->posts;

		wp_send_json_success(array(
			'total' => count($ids),
			'ids' => $ids,
		));
	}

	/**
	 * AJAX handler to generate description for a single property.
	 */
	public function houzi_generate_description()
	{
		check_ajax_referer('houzi_ai_nonce', 'nonce');

		$post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
		if (!$post_id) {
			wp_send_json_error('Invalid post ID');
		}

		$api_key = get_option('houzi_ai_api_key');
		$model = get_option('houzi_ai_gemini_model', 'gemini-flash-latest');

		if (!$api_key) {
			wp_send_json_error('API Key is missing.');
		}

		$post = get_post($post_id);
		if (!$post) {
			wp_send_json_error('Post not found.');
		}

		$post_type = $post->post_type;
		$title = get_the_title($post_id);
		$prompt_data = "";

		if ($post_type === 'property') {
			$property_types = wp_get_post_terms($post_id, 'property_type', array('fields' => 'names'));
			$property_status = wp_get_post_terms($post_id, 'property_status', array('fields' => 'names'));
			$features = wp_get_post_terms($post_id, 'property_feature', array('fields' => 'names'));

			$address = get_post_meta($post_id, 'fave_property_map_address', true);
			$bedrooms = get_post_meta($post_id, 'fave_property_bedrooms', true);
			$bathrooms = get_post_meta($post_id, 'fave_property_bathrooms', true);
			$garage = get_post_meta($post_id, 'fave_property_garage', true);
			$price = get_post_meta($post_id, 'fave_property_price', true);

			$prompt_data .= "Title: " . $title . "\n";
			if (!empty($property_types))
				$prompt_data .= "Property Type: " . implode(', ', $property_types) . "\n";
			if (!empty($property_status))
				$prompt_data .= "Status: " . implode(', ', $property_status) . "\n";
			if (!empty($features))
				$prompt_data .= "Key Features: " . implode(', ', $features) . "\n";
			if ($address)
				$prompt_data .= "Address: " . $address . "\n";
			if ($bedrooms)
				$prompt_data .= "Bedrooms: " . $bedrooms . "\n";
			if ($bathrooms)
				$prompt_data .= "Bathrooms: " . $bathrooms . "\n";
			if ($garage)
				$prompt_data .= "Garage: " . $garage . "\n";
			if ($price && floatval($price) > 0) {
				$currency = function_exists('houzez_get_currency_symbol') ? houzez_get_currency_symbol() : '$';
				$prompt_data .= "Price: " . $currency . $price . "\n";
			}

			$role_instruction = "Act as a premium real estate copywriter. Your task is to transform the following raw property data into a compelling, high-conversion property description.";
			$structure_instruction = "- A captivating opening statement that highlights the property's unique appeal.
			- A detailed breakdown of key features and interior highlights.
			- Mention location advantages and lifestyle benefits.
			- A professional closing that encourages interest.";
		} elseif ($post_type === 'houzez_agent') {
			$company = get_post_meta($post_id, 'fave_agent_company', true);
			$address = get_post_meta($post_id, 'fave_agent_address', true);
			$service_area = get_post_meta($post_id, 'fave_agent_service_area', true);
			$specialties = get_post_meta($post_id, 'fave_agent_specialties', true);
			$position = get_post_meta($post_id, 'fave_agent_position', true);
			$extra_des = get_post_meta($post_id, 'fave_agent_des', true);

			$prompt_data .= "Name: " . $title . "\n";
			if ($company)
				$prompt_data .= "Company: " . $company . "\n";
			if ($address)
				$prompt_data .= "Address: " . $address . "\n";
			if ($service_area)
				$prompt_data .= "Service Area: " . $service_area . "\n";
			if ($specialties)
				$prompt_data .= "Specialties: " . $specialties . "\n";
			if ($position)
				$prompt_data .= "Position: " . $position . "\n";
			if ($extra_des)
				$prompt_data .= "Additional Info: " . $extra_des . "\n";

			$role_instruction = "Act as a professional real estate brand specialist. Your task is to create a compelling, trustworthy, and expert agent bio based on the provided data.";
			$structure_instruction = "- A strong professional opening introducing the agent and their expertise.
			- Highlight key specialties, service areas, and their unique approach to client service.
			- A narrative that builds trust and demonstrates market authority.
			- A call to action that encourages potential clients to reach out.";
		} elseif ($post_type === 'houzez_agency') {
			$address = get_post_meta($post_id, 'fave_agency_address', true);
			if (!$address) {
				$address = get_post_meta($post_id, 'fave_agency_map_address', true);
			}

			$prompt_data .= "Agency Name: " . $title . "\n";
			if ($address)
				$prompt_data .= "Address: " . $address . "\n";

			$role_instruction = "Act as a premium real estate branding expert. Your task is to craft a sophisticated and authoritative agency description that showcases market leadership and exceptional service.";
			$structure_instruction = "- An impactful introduction that defines the agency's mission and presence in the market.
			- Focus on their values, expertise in the local area, and commitment to excellence.
			- Highlight the range of services or the standard of care clients can expect.
			- A professional closing that strengthens the brand's identity.";
		} else {
			wp_send_json_error('Unsupported post type.');
		}

		$prompt = "$role_instruction

		Guidelines:
		1. Tone: Sophisticated, inviting, and professional.
		2. Structure: 
		$structure_instruction
		3. Content: Focus strictly on the description text. Do not include salutations, introductory remarks, or conversational filler.
		4. Language: Use descriptive, evocative language to help the reader connect with the subject.

		Data:
		$prompt_data
		";

		$response = wp_remote_post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$api_key}", array(
			'headers' => array(
				'Content-Type' => 'application/json',
			),
			'body' => wp_json_encode(array(
				'contents' => array(
					array(
						'parts' => array(
							array('text' => $prompt),
						),
					),
				),
			)),
			'timeout' => 60,
		));

		if (is_wp_error($response)) {
			wp_send_json_error($response->get_error_message());
		}

		$body = json_decode(wp_remote_retrieve_body($response), true);
		if (isset($body['candidates'][0]['content']['parts'][0]['text'])) {
			$description = $body['candidates'][0]['content']['parts'][0]['text'];

			// Update the post
			wp_update_post(array(
				'ID' => $post_id,
				'post_content' => $description,
			));

			update_post_meta($post_id, 'ai_content', true);

			wp_send_json_success(array(
				'message' => 'Description updated for ' . $title,
				'description' => $description
			));
		} else {
			$error_msg = isset($body['error']['message']) ? $body['error']['message'] : 'Failed to generate description.';
			wp_send_json_error($error_msg);
		}
	}

}
