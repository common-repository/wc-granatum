<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class WC_Granatum_Payments
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

		// Pagseguro || woocommerce_api_wc_pagseguro_gateway
		add_action( 'valid_pagseguro_ipn_request', array( &$this, 'ipn_response_pagseguro' ) );
	}

	/**
	 * IPN handler.
	 */
	public function ipn_response_pagseguro($response) {
		if ( isset( $response->reference ) ) {
			$pagseguro_settings = get_option( 'woocommerce_pagseguro_settings' );
			$granatum_settings = get_option( 'wc_granatum_settings' );

			if ( ( isset($granatum_settings['bank_account']) ) && ( isset($granatum_settings['category']) ) ) {

				$order_id = (int) str_replace( $pagseguro_settings['invoice_prefix'], '', $response->reference );
				$order    = new WC_Order( $order_id );

				$user_id     = $order->user_id;
				$granatum_id = get_user_meta($user_id, 'granatum_id', TRUE);

				if ( in_array( $response->status, array(3, 4) ) ) {
					$data_payment = array(
						'descricao'             => (string) $response->reference,
						'conta_id'              => $granatum_settings['bank_account'],
						'categoria_id'          => $granatum_settings['category'],
						'valor'                 => (string) $response->netAmount,
						'data_vencimento'       => (string) $response->escrowEndDate,
						'data_pagamento'        => '',
						'data_competencia'      => (string) $response->date,
						'pessoa_id'             => (string) $granatum_id,
						//'tipo_documento_id'     => '', // VEM DA API
						'total_repeticoes'      => '0',
					);

					if ( isset( $granatum_settings['profit_center'] ) && $granatum_settings['profit_center'] != '0' ) {
						$data_payment['centro_custo_lucro_id'] = $granatum_settings['profit_center'];
					}

					if ( isset( $granatum_settings['payment_method'] ) && $granatum_settings['payment_method'] != '0') {
						$data_payment['forma_pagamento_id'] = $granatum_settings['payment_method'];
					}

					// Make a request
					$payment = $this->api->request('POST', 'lancamentos', NULL, $data_payment);
				}
			}
		}
	}
}
new WC_Granatum_Payments();
