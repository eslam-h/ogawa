<?php
class ControllerExtensionTotalcustomvoucher extends Controller {
    public function index() {
        if ($this->config->get('customvoucher_status')) {
            $this->load->language('extension/total/customvoucher');

            $data['heading_title'] = $this->language->get('heading_title');

            $data['text_loading'] = $this->language->get('text_loading');

            $data['entry_customvoucher'] = $this->language->get('entry_description');

            $data['button_customvoucher'] = $this->language->get('button_customvoucher');

            return $this->load->view('extension/total/customvoucher', $data);
        }
    }

    public function customvoucher() {
        $this->load->language('extension/total/customvoucher');

        $json = array();

        if(!$this->customer->isLogged())
        {
            $json['error'] = $this->language->get('error_not_logged');
        }

        $this->load->model('extension/total/customvoucher');

        $voucher_array = $this->model_extension_total_customvoucher->getCustomvoucher();

        if(isset($voucher_array))
        {
            $this->session->data['voucher_used'] = 'true';

            $this->session->data['success'] = $this->language->get('text_success');


            $json['redirect'] = $this->url->link('checkout/cart');
        }
        else
        {
            $json['error'] = $this->language->get('error_not_enough_total');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}
