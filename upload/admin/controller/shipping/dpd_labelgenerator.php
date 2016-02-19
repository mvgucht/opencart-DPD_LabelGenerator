<?php
class ControllerShippingDpdLabelGenerator extends Controller {
	private $error = array();

	// Configuration screen
	public function index() {
		$this->language->load('shipping/dpd_labelgenerator');
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		/*
		if (!$this->user->hasPermission('modify', 'shipping/dpd_carrier')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}		
		*/

		$this->load->model('setting/setting');
		
		// Save data
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
		
			$this->request->post['dpd_labelgenerator_status'] = false;
			
			$this->model_setting_setting->editSetting('dpd_labelgenerator', $this->request->post);		

			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('extension/shipping', 'token=' . $this->session->data['token'], 'SSL'));
		}

		// Output
		$this->data['heading_title'] = $this->language->get(heading_title');

		$this->data['entry_status'] = $this->language->get('Status:');
		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		
		$this->data['breadcrumbs'] = array();

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => false
		);

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_shipping'),
			'href'      => $this->url->link('extension/shipping', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('DPD Label Generator'),
			'href'      => $this->url->link('shipping/dpd_labelgenerator', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);
		
		$this->data['action'] = $this->url->link('shipping/dpd_labelgenerator', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['cancel'] = $this->url->link('extension/shipping', 'token=' . $this->session->data['token'], 'SSL');

		// Labels
		$this->data['shipper_name1'] = $this->language->get('Name 1');
		$this->data['shipper_street'] = $this->language->get('Street');
		$this->data['shipper_houseNo'] = $this->language->get('H#');
		$this->data['shipper_PC'] = $this->language->get('Postal code');
		$this->data['shipper_city'] = $this->language->get('City');
		$this->data['shipper_country'] = $this->language->get('Country');
		
		
		$this->load->model('setting/extension');
    $installed_modules = $this->model_setting_extension->getInstalled('shipping');
		
		$this->data['dpd_carrier_installed'] = false;
    if(in_array('dpd_carrier', $installed_modules)) {
			$this->data['dpd_carrier_installed'] = true;
		} else {
			$this->data['delis_id'] = $this->language->get('DelisID:');
			$this->data['delis_password'] = $this->language->get('Password:');
			
			$this->data['delis_server'] = $this->language->get('Server:');
			$this->data['delis_server_live'] = $this->language->get('Live');
			$this->data['delis_server_stage'] = $this->language->get('Stage');
			
			if (isset($this->request->post['dpd_labelgenerator_delis_id'])) {
			$this->data['dpd_labelgenerator_delis_id'] = $this->request->post['dpd_labelgenerator_delis_id'];
			} else {
				$this->data['dpd_labelgenerator_delis_id'] = $this->config->get('dpd_labelgenerator_delis_id');
			}	
			
			if (isset($this->request->post['dpd_labelgenerator_delis_password'])) {
				$this->data['dpd_labelgenerator_delis_password'] = $this->request->post['dpd_labelgenerator_delis_password'];
			} else {
				$this->data['dpd_labelgenerator_delis_password'] = $this->config->get('dpd_labelgenerator_delis_password');
			}	
			
			if (isset($this->request->post['dpd_labelgenerator_delis_server'])) {
				$this->data['dpd_labelgenerator_delis_server'] = $this->request->post['dpd_labelgenerator_delis_server'];
			} else {
				$this->data['dpd_labelgenerator_delis_server'] = $this->config->get('dpd_labelgenerator_delis_server');
			}
		
			if (isset($this->error['delis_id'])) {
				$this->data['delis_id_error'] = $this->error['delis_id'];
			} else {
				$this->data['delis_id_error'] = '';
			}
			
			if (isset($this->error['delis_password'])) {
				$this->data['delis_password_error'] = $this->error['delis_password'];
			} else {
				$this->data['delis_password_error'] = '';
			}
			
			if (isset($this->error['delis_server'])) {
				$this->data['delis_server_error'] = $this->error['delis_server'];
			} else {
				$this->data['delis_server_error'] = '';
			}
		}
		
		// Data
		if (isset($this->request->post['dpd_labelgenerator_shipper_name1'])) {
			$this->data['dpd_labelgenerator_shipper_name1'] = $this->request->post['dpd_labelgenerator_shipper_name1'];
		} else {
			$this->data['dpd_labelgenerator_shipper_name1'] = $this->config->get('dpd_labelgenerator_shipper_name1');
		}	
		
		if (isset($this->request->post['dpd_labelgenerator_shipper_street'])) {
			$this->data['dpd_labelgenerator_shipper_street'] = $this->request->post['dpd_labelgenerator_shipper_street'];
		} else {
			$this->data['dpd_labelgenerator_shipper_street'] = $this->config->get('dpd_labelgenerator_shipper_street');
		}	
		
		if (isset($this->request->post['dpd_labelgenerator_shipper_houseNo'])) {
			$this->data['dpd_labelgenerator_shipper_houseNo'] = $this->request->post['dpd_labelgenerator_shipper_houseNo'];
		} else {
			$this->data['dpd_labelgenerator_shipper_houseNo'] = $this->config->get('dpd_labelgenerator_shipper_houseNo');
		}	
				
		if (isset($this->request->post['dpd_labelgenerator_shipper_country'])) {
			$this->data['dpd_labelgenerator_shipper_country'] = $this->request->post['dpd_labelgenerator_shipper_country'];
		} else {
			$this->data['dpd_labelgenerator_shipper_country'] = $this->config->get('dpd_labelgenerator_shipper_country');
		}
		
		if (isset($this->request->post['dpd_labelgenerator_shipper_PC'])) {
			$this->data['dpd_labelgenerator_shipper_PC'] = $this->request->post['dpd_labelgenerator_shipper_PC'];
		} else {
			$this->data['dpd_labelgenerator_shipper_PC'] = $this->config->get('dpd_labelgenerator_shipper_PC');
		}	
		
		if (isset($this->request->post['dpd_labelgenerator_shipper_city'])) {
			$this->data['dpd_labelgenerator_shipper_city'] = $this->request->post['dpd_labelgenerator_shipper_city'];
		} else {
			$this->data['dpd_labelgenerator_shipper_city'] = $this->config->get('dpd_labelgenerator_shipper_city');
		}	
		
		// Errors
		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		if (isset($this->error['shipper_name1'])) {
			$this->data['shipper_name1_error'] = $this->error['shipper_name1'];
		} else {
			$this->data['shipper_name1_error'] = '';
		}
		
		if (isset($this->error['shipper_street'])) {
			$this->data['shipper_street_error'] = $this->error['shipper_street_error'];
		} else {
			$this->data['shipper_street_error'] = '';
		}
		
		if (isset($this->error['shipper_houseNo'])) {
			$this->data['shipper_houseNo_error'] = $this->error['shipper_houseNo_error'];
		} else {
			$this->data['shipper_houseNo_error'] = '';
		}
		
		if (isset($this->error['shipper_country'])) {
			$this->data['shipper_country_error'] = $this->error['shipper_country_error'];
		} else {
			$this->data['shipper_country_error'] = '';
		}
		
		if (isset($this->error['shipper_PC'])) {
			$this->data['shipper_PC_error'] = $this->error['shipper_PC_error'];
		} else {
			$this->data['shipper_PC_error'] = '';
		}
		
		if (isset($this->error['shipper_city'])) {
			$this->data['shipper_city_error'] = $this->error['shipper_city_error'];
		} else {
			$this->data['shipper_city_error'] = '';
		}

		$this->load->model('design/layout');

		$this->data['layouts'] = $this->model_design_layout->getLayouts();

		$this->template = 'shipping/dpd_labelgenerator.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}
	
	// Configuration validation
	protected function validate() {
		if (!$this->user->hasPermission('modify', 'shipping/dpd_labelgenerator')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		// TODO: ad validation :)

		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
	
	public function label() {
		$orders = array();
		
		if (isset($this->request->post['selected'])) {
			$orders = $this->request->post['selected'];
		} elseif (isset($this->request->get['order_id'])) {
			$orders[] = $this->request->get['order_id'];
		}
				
		$this->load->library('DPD/dpdlogin');
		$this->load->library('DPD/dpdshipment');
		
		// TODO: add check if dpd_carrier is installed!!
		$this->delisID = $this->config->get('dpd_carrier_delis_id');
		$this->delisPw = $this->config->get('dpd_carrier_delis_password');
		
		$this->url = $this->config->get('dpd_carrier_delis_server') == 1 ? 'https://public-ws.dpd.com/services/' : 'https://public-ws-stage.dpd.com/services/';
		
		$this->timeLogging = $this->config->get('dpd_carrier_time_logging') == 1;
		
		$login;
		if(!($login = unserialize($this->cache->get('dpd_carrier_login')))
			|| !($login->url == $this->url)
			|| !($login->delisId == $this->delisId))
		{
			try
			{
				$login = new DpdLogin($this->delisID, $this->delisPw, $this->url, $this->timeLogging);
			}
			catch (Exception $e)
			{
				$this->log->write('Something went wrong logging in to the DPD Web Services (' . $e->getMessage() . ')');
				die;
			}
		}
		
		$this->load->model('sale/order');
		$this->load->library('PDFMerger/PDFMerger');
		
		$pdf_output = new PDFMerger;
		$temp_files = array();
		
		foreach ($orders as $order_id) {
			$order_info = $this->model_sale_order->getOrder($order_id);
			
			$address_prefix = $order_info['shipping_code'] == 'dpd_carrier.pickup' ? 'payment_' : 'shipping_';
			
			$shipment = new DpdShipment($login);
			$shipment->request = array(
				'order' => array(
					'generalShipmentData' => array(
						'mpsCustomerReferenceNumber1' => $order_info['order_id']
						,'mpsCustomerReferenceNumber2' => $order_info['invoice_prefix'] . $order_info['invoice_no']
						,'sendingDepot' => $login->depot
						,'product' => 'CL'
						,'sender' => array(
							'name1' => $this->config->get('dpd_labelgenerator_shipper_name1')
							,'street' => $this->config->get('dpd_labelgenerator_shipper_street')
							,'houseNo' => $this->config->get('dpd_labelgenerator_shipper_houseNo')
							,'country' => $this->config->get('dpd_labelgenerator_shipper_country')
							,'zipCode' => $this->config->get('dpd_labelgenerator_shipper_PC')
							,'city' => $this->config->get('dpd_labelgenerator_shipper_city')
						)
						,'recipient' => array(
							'name1' => $order_info['shipping_firstname'] . ' ' . $order_info['shipping_lastname']
							,'name2' => $order_info['shipping_address_2']
							,'street' => $order_info[$address_prefix . 'address_1']
							,'country' => $order_info[$address_prefix . 'iso_code_2']
							,'zipCode' => $order_info[$address_prefix . 'postcode']
							,'city' => $order_info[$address_prefix . 'city']
							,'phone' => $order_info['telephone']
						)
					)
					,'parcels' => array(
						'weight' => '350'
					)
					,'productAndServiceData' => array(
						'orderType' => 'consignment'
					)
				)
			);
			
			
			if($order_info['shipping_code'] == 'dpd_carrier.pickup') {
				$shipment->request['order']['generalShipmentData']['recipient']['name1'] = $order_info['shipping_lastname'];
				$shipment->request['order']['generalShipmentData']['recipient']['name2'] = '';
				$shipment->request['order']['productAndServiceData']['parcelShopDelivery'] = array(
					'parcelShopId' => $order_info['shipping_address_2']
					,'parcelShopNotification' => array(
						'channel' => 1
						,'value' => $order_info['email']
						,'language' => $order_info['language_code']
					)
				);
			}
			
			if($order_info['shipping_code'] == 'dpd_carrier.home_with_predict') {
				$shipment->request['order']['productAndServiceData']['predict'] = array(
					'channel' => 1
					,'value' => $order_info['email']
					,'language' => $order_info['language_code']
				);
			}
			
			try
			{			
				$shipment->send();
			}
			catch (Exception $e)
			{
				$this->log->write('Something went wrong generating a dpd label (' . $e->getMessage() . ')');
			}

			if(isset($shipment->result->orderResult->parcellabelsPDF)) {
				$label_number = $shipment->result->orderResult->shipmentResponses->parcelInformation->parcelLabelNumber;
				
				$data['order_status_id'] = 3;
				$data['notify'] = true;
				$data['comment'] = 'DPD Label Generated (' . $label_number . ') Visit https://tracking.dpd.de/parcelstatus?locale=en_En&query=' . $label_number . ' to follow your parcel.';
				
				$this->model_sale_order->addOrderHistory($order_id, $data);
				
				$tmpfname = tempnam("/tmp", 'DPDLabel');
				$temp_files[] = $tmpfname;
				
				$handle = fopen($tmpfname, "w");
				fwrite($handle, $shipment->result->orderResult->parcellabelsPDF);
				fclose($handle);
				
				$pdf_output->addPDF($tmpfname, 'all');
			}
		}
		if(count($temp_files) > 0) {
			$pdf_output->merge('browser', 'DPD_' . date("dmYHis") . '.pdf');
			ob_end_flush();
			die;
		}
		
		foreach($temp_files as $file) {
			unlink($file);
		}
	}
}
?>
