<?php
class ControllerCommonHeader extends Controller {
    public function index() {

        // Pavo 2.2 fix
        require_once( DIR_SYSTEM . 'pavothemes/loader.php' );

        $this->load->language('extension/module/themecontrol');
        $data['objlang'] = $this->language;
        $data['objurl'] = $this->url;

        $config = $this->registry->get('config');
        $data['sconfig'] = $config;

        $helper = ThemeControlHelper::getInstance( $this->registry, $config->get('theme_default_directory') );
        $helper->triggerUserParams( array('header_layout','productlayout') );
        $data['helper'] = $helper;

        $themeConfig = (array)$config->get('themecontrol');

        $headerlayout = $helper->getConfig('header_layout','headerv1');
        $data['headerlayout'] = $headerlayout;
        // Pavo 2.2 end fixheader

        // Analytics
        $this->load->model('extension/extension');

        $data['analytics'] = array();

        $analytics = $this->model_extension_extension->getExtensions('analytics');

        foreach ($analytics as $analytic) {
            if ($this->config->get($analytic['code'] . '_status')) {
                $data['analytics'][] = $this->load->controller('analytics/' . $analytic['code'], $this->config->get($analytic['code'] . '_status'));
            }
        }

        if ($this->request->server['HTTPS']) {
            $server = $this->config->get('config_ssl');
        } else {
            $server = $this->config->get('config_url');
        }

        if (is_file(DIR_IMAGE . $this->config->get('config_icon'))) {
            $this->document->addLink($server . 'image/' . $this->config->get('config_icon'), 'icon');
        }

        $data['title'] = $this->document->getTitle();

        $data['base'] = $server;
$this->document->addStyle('catalog/view/theme/default/stylesheet/slsoffr.css');
        $data['description'] = $this->document->getDescription();
        $data['keywords'] = $this->document->getKeywords();
        $data['links'] = $this->document->getLinks();
        $data['styles'] = $this->document->getStyles();
        $data['scripts'] = $this->document->getScripts();
        $data['lang'] = $this->language->get('code');
        $data['direction'] = $this->language->get('direction');

        $data['name'] = $this->config->get('config_name');

        if ($data['lang'] == 'ar')
        {
            $logo_path = $this->config->get('config_logo');
            $parts = explode('.', $logo_path);
            $full_parts = array_merge(array_slice($parts, 0, -1), ["-ar", "."], array_slice($parts, -1));
            $logo_path = implode("",$full_parts);
        }
        else
            $logo_path = $this->config->get('config_logo');


        if (is_file(DIR_IMAGE . $logo_path)) {
            $data['logo'] = $server . 'image/' . $logo_path;


        } else {
            $data['logo'] = '';
        }

        $this->load->language('common/header');

        $data['text_home'] = $this->language->get('text_home');

        // Wishlist
        if ($this->customer->isLogged()) {
            $this->load->model('account/wishlist');

            $data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), $this->model_account_wishlist->getTotalWishlist());

            $this->load->model('account/customer');
            $user_data = $this->model_account_customer->getCustomer((int)$this->customer->getId());
            $data['text_account'] = $user_data['firstname']." ".$user_data['lastname'];
            $data['text_first_name'] = $user_data['firstname'];
        } else {
            $data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0));
            $data['text_account'] = $this->language->get('text_account');
            $data['text_first_name'] = '';
        }

        $data['text_shopping_cart'] = $this->language->get('text_shopping_cart');
        $data['text_logged'] = sprintf($this->language->get('text_logged'), $this->url->link('account/account', '', true), $this->customer->getFirstName(), $this->url->link('account/logout', '', true));

//        $data['text_account'] = $this->language->get('text_account');
        $data['text_register'] = $this->language->get('text_register');
        $data['text_login'] = $this->language->get('text_login');
        $data['text_order'] = $this->language->get('text_order');
        $data['text_transaction'] = $this->language->get('text_transaction');
        $data['text_download'] = $this->language->get('text_download');
        $data['text_logout'] = $this->language->get('text_logout');
        $data['text_checkout'] = $this->language->get('text_checkout');
        $data['text_category'] = $this->language->get('text_category');
        $data['text_all'] = $this->language->get('text_all');

        $data['home'] = $this->url->link('common/home');
        $data['wishlist'] = $this->url->link('account/wishlist', '', true);
        $data['logged'] = $this->customer->isLogged();
        $data['account'] = $this->url->link('account/account', '', true);
        $data['register'] = $this->url->link('account/register', '', true);
        $data['login'] = $this->url->link('account/login', '', true);
        $data['order'] = $this->url->link('account/order', '', true);
        $data['transaction'] = $this->url->link('account/transaction', '', true);
        $data['download'] = $this->url->link('account/download', '', true);
        $data['logout'] = $this->url->link('account/logout', '', true);
        $data['shopping_cart'] = $this->url->link('checkout/cart');
        $data['checkout'] = $this->url->link('checkout/checkout', '', true);
        $data['contact'] = $this->url->link('information/contact');
        $data['telephone'] = $this->config->get('config_telephone');

        // Menu
        $this->load->model('catalog/category');

        $this->load->model('catalog/product');

        $data['categories'] = array();

        $categories = $this->model_catalog_category->getCategories(0);

        foreach ($categories as $category) {
            if ($category['top']) {
                // Level 2
                $children_data = array();

                $children = $this->model_catalog_category->getCategories($category['category_id']);

                foreach ($children as $child) {
                    $filter_data = array(
                        'filter_category_id'  => $child['category_id'],
                        'filter_sub_category' => true
                    );

                    $children_data[] = array(
                        'name'  => $child['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
                        'href'  => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id'])
                    );
                }

                // Level 1
                $data['categories'][] = array(
                    'name'     => $category['name'],
                    'children' => $children_data,
                    'column'   => $category['column'] ? $category['column'] : 1,
                    'href'     => $this->url->link('product/category', 'path=' . $category['category_id'])
                );
            }
        }

        $data['language'] = $this->load->controller('common/language');
        $data['currency'] = $this->load->controller('common/currency');
        $data['search'] = $this->load->controller('common/search');
        $data['cart'] = $this->load->controller('common/cart');

        // For page specific css

        $this->load->language('offers/salescombopge');
        $data['text_salescombopge_heading'] = $this->language->get('text_salescombopge_heading');
        $data['salescombopge_info'] = array();
        $this->load->model('offers/salescombopge');
        $salescombopge_info = $this->model_offers_salescombopge->getPages();
        foreach ($salescombopge_info as $key => $value) {
          $data['salescombopge_info'][] = array(
            'name'=> $value['title'],
            'href' => $this->url->link('offers/salescombopge', 'page_id=' .  $value['salescombopge_id']),
            'id' => "",
            'children_level2' => array()
          );
        }
        if(!empty($data['salescombopge_info'])) {
          $data['categories'][] = array(
            'name'     => $data['text_salescombopge_heading'],
            'children' => $data['salescombopge_info'],
            'column'   => 1,
            'href'     => $this->url->link("offers/alloffers")
          );
        }

        
        if (isset($this->request->get['route'])) {
            if (isset($this->request->get['product_id'])) {
                $class = '-' . $this->request->get['product_id'];
            } elseif (isset($this->request->get['path'])) {
                $class = '-' . $this->request->get['path'];
            } elseif (isset($this->request->get['manufacturer_id'])) {
                $class = '-' . $this->request->get['manufacturer_id'];
            } elseif (isset($this->request->get['information_id'])) {
                $class = '-' . $this->request->get['information_id'];
            } else {
                $class = '';
            }

            $data['class'] = str_replace('/', '-', $this->request->get['route']) . $class;
        } else {
            $data['class'] = 'common-home';
        }

		$headerlayout = str_replace('-','',$headerlayout);

        if (file_exists(DIR_TEMPLATE . $this->config->get('theme_default_directory') . '/template/common/'.$headerlayout.'.tpl')) {
            $header = $headerlayout;
        } else {
            $header = "header";
        }

        return $this->load->view('common/'.$header, $data);
    }
}
