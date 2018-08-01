<?php

class ControllerInformationStores extends Controller {
    private $error = array();

    public function index() {



        $this->load->language('information/stores');

        $this->document->setTitle($this->language->get('heading_title'));

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('information/stores')
        );

        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_tele'] = $this->language->get('text_tele');
        $data['text_open'] = $this->language->get('text_open');

        $this->load->model('localisation/stores');

        $data['locations'] = $this->model_localisation_stores->getLocations();

        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('information/stores_new', $data));
    }



}