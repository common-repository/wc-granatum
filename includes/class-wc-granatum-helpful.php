<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class WC_Granatum_Helpful
{
	/**
	 * @var object
	 */
	protected $api;

	/**
	 * Class construct
	 */
	public function __construct() {
		$this->api = new WC_Granatum_API;
	}

	/**
	 * Get id of the state informed
	 *
	 * @param  string $str_state
	 *
	 * @return string
	 */
	public function get_id_state( $str_state ){
		$states = array( '1' => 'AC', '2' => 'AL', '3' => 'AM', '4' => 'AP', '5' => 'BA', '6' => 'CE', '7' => 'DF', '8' => 'ES', '9' => 'GO', '10' => 'MA', '11' => 'MG', '12' => 'MS', '13' => 'MT', '14' => 'PA', '15' => 'PB', '16' => 'PE', '17' => 'PI', '18' => 'PR', '19' => 'RJ', '20' => 'RN', '21' => 'RO', '22' => 'RR', '23' => 'RS', '24' => 'SC', '25' => 'SE', '26' => 'SP', '27' => 'TO');

		$id_state = array_search($str_state, $states);

		return $id_state;
	}

	/**
	 * Get id of the city informed.
	 * See a better solution than this. In php > 5.5 exists the function
	 * array_column which can be used in combination with the function array_search
	 *
	 * @param  string $id_state
	 * @param  string $str_city
	 *
	 * @return string|null
	 */
	public function get_id_city( $id_state, $str_city ){
		// Hitting query string
		$query_string = array(
			'estado_id' => $id_state
		);

		// Make a request
		$cities_response = $this->api->request('GET', 'cidades', $query_string, NULL);

		// Check the API response
		if ( !is_array($cities_response)) {
			return $cities_response;
		}

		// Find city code
		foreach ($cities_response as $city){
			if( strtolower( $city["nome"] ) == strtolower( $str_city ) ){
				return $city["id"];
				exit();
			}
		}
	}

	/**
	 * Retrieve a list of bank accounts
	 *
	 * @return array
	 */
	public function get_bank_accounts(){
		// Make a request
		$accounts_response = $this->api->request('GET', 'contas', NULL, NULL);

		// Check the API response
		if ( !is_array($accounts_response)) {
			return $accounts_response;
		}

		// Generate return value
		$accounts_return = array();
		foreach ($accounts_response as $account){
			$accounts_return[$account['id']] = $account['descricao'];
		}

		return $accounts_return;
	}

	/**
	 * Retrieve a list of category
	 *
	 * @return array
	 */
	public function get_categorys(){
		// Make a request
		$categorys_response = $this->api->request('GET', 'categorias', NULL, NULL);

		// Check the API response
		if ( !is_array($categorys_response)) {
			return $categorys_response;
		}

		// Generate return value
		$array_categorys = $this->build_categorys_tree($categorys_response);

		return $array_categorys;
	}

	/**
	 * Builds a tree of categories
	 *
	 * @param  array $arr
	 * @param  array $output
	 * @param  int $index
	 *
	 * @return array
	 */
	function build_categorys_tree(array $arr, &$output = array(), $index = 0){
		foreach($arr as $item)
		{
			$output[$item['id']] = str_repeat('&mdash; ', $index) . $item['descricao'];
			if(isset($item['categorias_filhas']))
			{
				$this->build_categorys_tree($item['categorias_filhas'], $output, $index + 1);
			}
		}
		return $output;
	}

	/**
	 * Retrieve a list of profit Center
	 *
	 * @return array
	 */
	public function get_profit_centers(){
		// Make a request
		$profit_centers_response = $this->api->request('GET', 'centros_custo_lucro', NULL, NULL);

		// Check the API response
		if ( !is_array($profit_centers_response)) {
			return $profit_centers_response;
		}


		// Generate return value
		$profit_centers_return = array();
		foreach ($profit_centers_response as $profit_centers){
			$profit_centers_return[$profit_centers['id']] = $profit_centers['descricao'];
		}

		return $profit_centers_return;
	}

	/**
	 * Retrieve a list of payment methods
	 *
	 * @return array
	 */
	public function get_payment_methods(){
		// Make a request
		$payment_methods_response = $this->api->request('GET', 'formas_pagamento', NULL, NULL);

		// Check the API response
		if ( !is_array($payment_methods_response)) {
			return $payment_methods_response;
		}

		// Generate return value
		$payment_methods_return = array();
		foreach ($payment_methods_response as $payment_methods){
			$payment_methods_return[$payment_methods['id']] = $payment_methods['descricao'];
		}

		return $payment_methods_return;
	}
}
new WC_Granatum_Helpful();
