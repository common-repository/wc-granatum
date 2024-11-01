<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Plugin settings class.
 */
class WC_Granatum_API
{
	/**
	 * @var string
	 */
	protected $access_token;


	/**
	 * Class construct
	 */
	public function __construct() {
		// Set access token
		$option = get_option( 'wc_granatum_settings' );
		$this->access_token = $option['access_token'];
	}


	/**
	 * Makes a request to the Granataum API
	 *
	 * @param  string $method
	 * @param  string $end_point
	 * @param  array $query_string
	 * @param  array $data
	 *
	 * @return array|boolean
	 */
	public function request($method, $end_point, $query_string = NULL, $data = NULL) {
		// Check the access token situation
		if ( empty($this->access_token) ){
			return 'You must enter an access token in the plugin.';
		}

		// Create the url of the request
		$api_url = 'https://api.granatum.com.br/v1/';
		$request_url = $api_url . $end_point . '?access_token=' . $this->access_token;

		if ( $query_string && is_array( $query_string ) ) {
			foreach ($query_string as $key => $value) {
				$param = '&' . $key . '=' . $value;
				$request_url = $request_url . $param;
			}
		}

		// Fill request's arguments
		$remote_request_args = array(
			'method'  => $method,
			'headers' => array(
				'Content-Type' => 'application/x-www-form-urlencoded'
			)
		);

		// Fill request's body
		if ( $data && is_array( $data ) ) {
			$remote_request_args['body'] = $data;
		}

		// Execute request
		$response = wp_remote_request( $request_url, $remote_request_args );

		// Works request's response
		$response_status_code =  wp_remote_retrieve_response_code( $response );
		if ( !in_array( $response_status_code , array('200', '201') ) ) {
			return 'Error retrieving API response';
		}

		// Clean response
		$response = json_decode( wp_remote_retrieve_body( $response ), TRUE );

		return $response;
	}
}
new WC_Granatum_API();
