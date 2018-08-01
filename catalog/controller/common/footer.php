<?php
class ControllerCommonFooter extends Controller {
    public function index() {

        // Pavo 2.2 fix
        require_once( DIR_SYSTEM . 'pavothemes/loader.php' );

        $this->load->language('extension/module/themecontrol');
        $data['objlang'] = $this->language;

        $config = $this->registry->get('config');
        $data['sconfig'] = $config;

        $helper = ThemeControlHelper::getInstance( $this->registry, $config->get('theme_default_directory') );
        $helper->triggerUserParams( array('header_layout','productlayout') );
        $data['helper'] = $helper;

        $themeConfig = (array)$config->get('themecontrol');
        // Pavo 2.2 end fix

        $this->load->language('common/footer');

        $data['scripts'] = $this->document->getScripts('footer');

        $data['text_information'] = $this->language->get('text_information');
        $data['text_service'] = $this->language->get('text_service');
        $data['text_extra'] = $this->language->get('text_extra');
        $data['text_contact'] = $this->language->get('text_contact');
        $data['text_find'] = $this->language->get('text_find');
        $data['text_return'] = $this->language->get('text_return');
        $data['text_sitemap'] = $this->language->get('text_sitemap');
        $data['text_manufacturer'] = $this->language->get('text_manufacturer');
        $data['text_voucher'] = $this->language->get('text_voucher');
        $data['text_affiliate'] = $this->language->get('text_affiliate');
        $data['text_special'] = $this->language->get('text_special');
        $data['text_account'] = $this->language->get('text_account');
        $data['text_order'] = $this->language->get('text_order');
        $data['text_wishlist'] = $this->language->get('text_wishlist');
        $data['text_newsletter'] = $this->language->get('text_newsletter');
        $data['text_product']    = $this->language->get('text_product');
        $data['text_store']      = $this->language->get('text_store');
        $data['text_Distribution'] = $this->language->get('text_Distribution');
        $data['text_about']     = $this->language->get('text_about');
        $data['menu5']     = $this->language->get('menu5');
        $data['text_gallery']     = $this->language->get('text_gallery');
        $data['text_fame']     = $this->language->get('text_fame');
        $data['text_quick_links'] = $this->language->get('text_quick_links');
        $data['text_promotions'] = $this->language->get('text_promotions');
        $data['text_news'] = $this->language->get('text_news');
        $data['text_blogs'] = $this->language->get('text_blogs');
        $data['powered'] = $this->language->get('text_powered');
        
        $this->load->model('catalog/information');

        $data['informations'] = array();

        foreach ($this->model_catalog_information->getInformations() as $result) {
            if ($result['bottom']) {
                $data['informations'][] = array(
                    'title' => $result['title'],
                    'href'  => $this->url->link('information/information', 'information_id=' . $result['information_id'])
                );
            }
        }

        $data['contact'] = $this->url->link('information/contact');
        $data['find'] = $this->url->link('information/stores');
        $data['return'] = $this->url->link('account/return/add', '', true);
        $data['sitemap'] = $this->url->link('information/sitemap');
        $data['manufacturer'] = $this->url->link('product/manufacturer');
        $data['voucher'] = $this->url->link('account/voucher', '', true);
        $data['affiliate'] = $this->url->link('affiliate/account', '', true);
        $data['special'] = $this->url->link('product/special');
        $data['account'] = $this->url->link('account/account', '', true);
        $data['order'] = $this->url->link('account/order', '', true);
        $data['wishlist'] = $this->url->link('account/wishlist', '', true);
        $data['newsletter'] = $this->url->link('account/newsletter', '', true);
        
        $data['products'] = $this->url->link('product/category', 'path=76', true);
        $data['gallery'] = $this->url->link('information/gallery', '', true);
        $data['about'] = $this->url->link('information/information', 'information_id=4', true);
        
        $data['promotions'] = $this->url->link('offers/alloffers', '', true);
        $data['news'] = $this->url->link('#', '', true);
        $data['blogs'] = $this->url->link('pavblog/blogs', '', true);
        

        // Whos Online
        if ($this->config->get('config_customer_online')) {
            $this->load->model('tool/online');

            if (isset($this->request->server['REMOTE_ADDR'])) {
                $ip = $this->request->server['REMOTE_ADDR'];
            } else {
                $ip = '';
            }

            if (isset($this->request->server['HTTP_HOST']) && isset($this->request->server['REQUEST_URI'])) {
                $url = 'http://' . $this->request->server['HTTP_HOST'] . $this->request->server['REQUEST_URI'];
            } else {
                $url = '';
            }

            if (isset($this->request->server['HTTP_REFERER'])) {
                $referer = $this->request->server['HTTP_REFERER'];
            } else {
                $referer = '';
            }

            $this->model_tool_online->addOnline($ip, $this->customer->getId(), $url, $referer);
        }

        return $this->load->view('common/footer', $data);
    }
}
