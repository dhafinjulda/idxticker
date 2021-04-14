<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use GuzzleHttp\Client;

class Welcome extends CI_Controller {

	public function __construct()

	{
		parent::__construct();
		// Include library
		$this->load->library('mongo_db');

	}
	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
		$this->load->view('welcome_message');
	}

	public function test_insert(){

		// Create new connection
		$mongo_db = new Mongo_db();
		$mongo_db->connect();

		$mongo_db->insert('pairs', [
			'id' => 'btcid',
			'symbol' => 'BTCIDR',
			'base_currency' =>'idr',
			'traded_currency' =>'btc',
			'traded_currency_unit' =>'BTC',
			'description' =>'BTC/IDR',
			'ticker_id' =>'btc_idr',
			'volume_precision' => 0,
			'price_precision' => 1000,
			'price_round' => 8,
			'pricescale' => 1000,
			'trade_min_base_currency' => 10000,
			'trade_min_traded_currency' => 0.00001063,
			'has_memo' => false,
			'memo_name' => false,
			'has_payment_id' => false,
			'trade_fee_percent' => 0.3,
			'url_logo' => 'https://indodax.com/v2/logo/svg/color/btc.svg',
			'url_logo_png' =>'https://indodax.com/v2/logo/png/color/btc.png',
			'is_maintenance' => 0
		]);

	}

	public function getPairs(){

		$client = new Client();
		$response = $client->request('GET', 'https://indodax.com/api/pairs');

		$data = json_decode($response->getBody()->getContents());
		$mongo_db = new Mongo_db();
		$mongo_db->connect();

		foreach ($data as $d){
			$mongo_db->insert('pairs', (array)$d);
		}

	}

	public function getPairsFromDb(){

		$mongo_db = new Mongo_db();
		$mongo_db->connect();

		echo "<pre>";
		print_r($mongo_db->get('pairs'));

	}

	public function getBtcIdrTicker(){

		$mongo_db = new Mongo_db();
		$mongo_db->connect();

		$client = new Client();
		$response = $client->request('GET', 'https://indodax.com/api/ticker/btcidr');
		$data = json_decode($response->getBody()->getContents());
		$data->ticker->pairs = 'btcidr';
		$mongo_db->insert('ticker', (array)$data->ticker);

		echo "<pre>";
		print_r($mongo_db->get('ticker'));

	}
}
