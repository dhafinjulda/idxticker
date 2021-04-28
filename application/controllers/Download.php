<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

defined('BASEPATH') or exit('No direct script access allowed');

class Download extends CI_Controller
{

	public function __construct()

	{
		parent::__construct();
		// Include library
		$this->load->library('mongo_db');

	}

	/**
	 * @throws \PhpOffice\PhpSpreadsheet\Exception
	 */
	public function excel(){

		$id = $this->uri->segment(3);

		if (!$id){
			$this->load->view('download');
		} else {
			switch ($id){
				case 1:
					$pairs = 'btcidr';
					break;
				case 2:
					$pairs = 'ethidr';
					break;
				case 3:
					$pairs = 'xrpidr';
					break;
				case 4:
					$pairs = 'bnbidr';
					break;
				case 5:
					$pairs = 'dogeidr';
					break;
				case 6:
					$pairs = 'ltcidr';
					break;
				default:
					echo "index not match";
					exit();
			}

			$mongo_db = new Mongo_db();
			$mongo_db->connect();
			$rows = [
				'high',
				'low',
				'vol_btc',
				'vol_idr',
				'last',
				'buy',
				'sell',
				'server_time'
			];

			$data = $this->mongo_db->where('pairs', $pairs)->get('ticker');
			$sheetIndex = 0;

			if(sizeof($data) > 0){
				$spreadsheet = new Spreadsheet();
				$myWorkSheet = new Worksheet($spreadsheet, strtoupper($pairs));
				$spreadsheet->addSheet($myWorkSheet, $sheetIndex);
				$column = "A";
				$rowNumber = 2;

				foreach ($rows as $row){
					$myWorkSheet->setCellValue($column.'1', $row);
					$column++;
				}

				foreach ($data as $d){
					$column1 = 'A';
					foreach ($rows as $row){
						if($row == 'server_time'){
							$date = date('Y-m-d H:i:s', $d[$row]);
							$myWorkSheet->setCellValue($column1.$rowNumber, $date);
						} else {
							$myWorkSheet->setCellValue($column1.$rowNumber, $d[$row]);
							$myWorkSheet->getStyle($column1.$rowNumber)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
							$myWorkSheet->getColumnDimension($column1)->setAutoSize(true);
						}
						$column1 ++;
					}
					$rowNumber++;
				}

				$writer = new Xlsx($spreadsheet);
				header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				header('Content-Disposition: attachment; filename="'.strtoupper($pairs).'.xlsx"');
				$writer->save('php://output');
			}
		}
	}


}
