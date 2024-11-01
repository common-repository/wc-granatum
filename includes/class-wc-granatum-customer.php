<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class WC_Granatum_Customer
{
	/**
	 * @var object
	 */
	protected $api;

	/**
	 * @var object
	 */
	protected $helpful;

	/**
	 * Class construct
	 */
	public function __construct() {
		$this->api = new WC_Granatum_API;
		$this->helpful = new WC_Granatum_Helpful;

		// Executions
		add_action( 'woocommerce_created_customer', array( &$this, 'new_customer' ), 150, 1 );
	}

	/**
	 * Add new customer
	 *
	 * @param  string $customer_id
	 *
	 * @return void
	 */
	public function new_customer($customer_id) {
		$data['persontype']   = sanitize_text_field( $_POST['billing_persontype'] );
		$data['first_name']   = sanitize_text_field( $_POST['billing_first_name'] );
		$data['last_name']    = sanitize_text_field( $_POST['billing_last_name'] );
		$data['company']      = sanitize_text_field( $_POST['billing_company'] );
		$data['cpf']          = sanitize_text_field( $_POST['billing_cpf'] );
		$data['cnpj']         = sanitize_text_field( $_POST['billing_cnpj'] );
		$data['state']        = sanitize_text_field( $_POST['billing_state'] );
		$data['city']         = sanitize_text_field( $_POST['billing_city'] );
		$data['phone']        = sanitize_text_field( $_POST['billing_phone'] );
		$data['email']        = sanitize_email( $_POST['billing_email'] );
		$data['address_1']    = sanitize_text_field( $_POST['billing_address_1'] );
		$data['address_2']    = sanitize_text_field( $_POST['billing_address_2'] );
		$data['number']       = sanitize_text_field( $_POST['billing_number'] );
		$data['neighborhood'] = sanitize_text_field( $_POST['billing_neighborhood'] );
		$data['postcode']     = sanitize_text_field( $_POST['billing_postcode'] );


		// Cleaning and preparing data for customer_data
		if ($data['persontype'] == '1') {
			$data['full_name'] = $data['first_name'] . ' ' . $data['last_name'];
			$data['document']  = $data['cpf'];
		}else{
			$data['full_name'] = $data['company'];
			$data['document']  = $data['cnpj'];
		}

		$data['fantasy_name'] = $data['first_name'] . ' ' . $data['last_name'];
		$data['state']        = $this->helpful->get_id_state( $data['state'] );
		$data['city']         = $this->helpful->get_id_city( $data['state'], $data['city'] );
		$data['observation']  = '';

		if ( is_null( $data['city'] ) ) {
			$data['observation'] = sprintf( __( 'The city %s was not found during registration.' , 'wc-granatum' ), $data['city'] );
		}

		// Fill customer_data
		$data_customer = array(
			'nome'                 => $data['full_name'],
			'nome_fantasia'        => $data['fantasy_name'],
			'documento'            => $data['document'],
			'inscricao_estadual'   => '',
			'telefone'             => $data['phone'],
			'email'                => $data['email'],
			'endereco'             => $data['address_1'],
			'endereco_numero'      => $data['number'],
			'endereco_complemento' => $data['address_2'],
			'bairro'               => $data['neighborhood'],
			'cep'                  => $data['postcode'],
			'cidade_id'            => $data['city'],
			'estado_id'            => $data['state'],
			'observacao'           => $data['observation'],
			'fornecedor'           => FALSE
		);

		// Make a request
		$customer = $this->api->request('POST', 'clientes', NULL, $data_customer);

		// Update customer meta data
		if ($customer != FALSE) {
			update_user_meta( $customer_id, 'granatum_id', $customer['id'] );
		}
	}

}
new WC_Granatum_Customer();
