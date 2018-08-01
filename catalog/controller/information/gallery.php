<?php
class ControllerInformationGallery extends Controller {
    private $error = array();

    public function index() {


        $data['rtl'] = 'false';

        if((int)$this->config->get('config_language_id') == 2) {
            $data['rtl'] = 'true';
        }

        $this->load->language('information/gallery');

        $this->document->setTitle($this->language->get('heading_title'));

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('information/gallery')
        );

        $data['heading_title'] = $this->language->get('heading_title');

        $this->load->model('catalog/gallery');

        $data['pictures'] = $this->model_catalog_gallery->getPictures();
        $data['videos'] = $this->model_catalog_gallery->getVideos();

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $this->response->setOutput($this->load->view('information/gallery', $data));
    }

}
