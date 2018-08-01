<?php

class ControllerCatalogCustomVoucher extends Controller
{
    private $error = array();

    public function index() {
        $this->load->language('catalog/customvoucher');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/customvoucher');

        $this->getList();
    }

    public function add() {
        $this->load->language('catalog/customvoucher');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/customvoucher');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_catalog_customvoucher->addVoucher($this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('catalog/customvoucher', 'token=' . $this->session->data['token'] . $url, true));
        }

        $this->getForm();
    }

    public function edit() {
        $this->load->language('catalog/customvoucher');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/customvoucher');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_catalog_customvoucher->editVoucher($this->request->get['voucher_id'], $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('catalog/customvoucher', 'token=' . $this->session->data['token'] . $url, true));
        }

        $this->getForm();
    }

    public function delete() {
        $this->load->language('catalog/customvoucher');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/customvoucher');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $voucher_id) {
                $this->model_catalog_customvoucher->deleteVoucher($voucher_id);
            }

            $this->session->data['success'] = $this->language->get('text_success');

            $url = '';

            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }

            $this->response->redirect($this->url->link('catalog/customvoucher', 'token=' . $this->session->data['token'] . $url, true));
        }

        $this->getList();
    }

    protected function getList() {
        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'v.total';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('catalog/customvoucher', 'token=' . $this->session->data['token'] . $url, true)
        );

        $data['add'] = $this->url->link('catalog/customvoucher/add', 'token=' . $this->session->data['token'] . $url, true);
        $data['delete'] = $this->url->link('catalog/customvoucher/delete', 'token=' . $this->session->data['token'] . $url, true);

        $data['vouchers'] = array();

        $filter_data = array(
            'sort'  => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $voucher_total = $this->model_catalog_customvoucher->getTotalVouchers();

        $results = $this->model_catalog_customvoucher->getVouchers($filter_data);

        foreach ($results as $result) {
            $data['vouchers'][] = array(
                'voucher_id' => $result['voucher_id'],
                'total'        => $result['total'],
                'value'        => $result['value'],
                'edit'        => $this->url->link('catalog/customvoucher/edit', 'token=' . $this->session->data['token'] . '&voucher_id=' . $result['voucher_id'] . $url, true)
            );
        }

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_list'] = $this->language->get('text_list');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_confirm'] = $this->language->get('text_confirm');

        $data['column_total'] = $this->language->get('column_total');
        $data['column_value'] = $this->language->get('column_value');
        $data['column_action'] = $this->language->get('column_action');

        $data['button_add'] = $this->language->get('button_add');
        $data['button_edit'] = $this->language->get('button_edit');
        $data['button_delete'] = $this->language->get('button_delete');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];

            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        if (isset($this->request->post['selected'])) {
            $data['selected'] = (array)$this->request->post['selected'];
        } else {
            $data['selected'] = array();
        }

        $url = '';

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['sort_total'] = $this->url->link('catalog/customvoucher', 'token=' . $this->session->data['token'] . '&sort=v.total' . $url, true);
        $data['sort_value'] = $this->url->link('catalog/customvoucher', 'token=' . $this->session->data['token'] . '&sort=v.value' . $url, true);

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->total = $voucher_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('catalog/customvoucher', 'token=' . $this->session->data['token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($voucher_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($voucher_total - $this->config->get('config_limit_admin'))) ? $voucher_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $voucher_total, ceil($voucher_total / $this->config->get('config_limit_admin')));

        $data['sort'] = $sort;
        $data['order'] = $order;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('catalog/voucher_list', $data));
    }

    protected function getForm() {
        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_form'] = !isset($this->request->get['voucher_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
        $data['text_loading'] = $this->language->get('text_loading');

        $data['entry_total'] = $this->language->get('entry_total');
        $data['entry_value'] = $this->language->get('entry_value');

        $data['help_total'] = $this->language->get('help_total');
        $data['help_value'] = $this->language->get('help_value');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['button_upload'] = $this->language->get('button_upload');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['total'])) {
            $data['error_total'] = $this->error['total'];
        } else {
            $data['error_total'] = array();
        }

        if (isset($this->error['value'])) {
            $data['error_value'] = $this->error['value'];
        } else {
            $data['error_value'] = array();
        }

        $url = '';

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('catalog/customvoucher', 'token=' . $this->session->data['token'] . $url, true)
        );

        if (!isset($this->request->get['voucher_id'])) {
            $data['action'] = $this->url->link('catalog/customvoucher/add', 'token=' . $this->session->data['token'] . $url, true);
        } else {
            $data['action'] = $this->url->link('catalog/customvoucher/edit', 'token=' . $this->session->data['token'] . '&voucher_id=' . $this->request->get['voucher_id'] . $url, true);
        }

        $data['cancel'] = $this->url->link('catalog/customvoucher', 'token=' . $this->session->data['token'] . $url, true);

        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();

        if (isset($this->request->get['voucher_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
            $download_info = $this->model_catalog_customvoucher->getVoucher($this->request->get['voucher_id']);
        }

        $data['token'] = $this->session->data['token'];

        if (isset($this->request->get['voucher_id'])) {
            $data['voucher_id'] = $this->request->get['voucher_id'];
        } else {
            $data['voucher_id'] = 0;
        }

        if (isset($this->request->post['total'])) {
            $data['total'] = $this->request->post['total'];
        } elseif (!empty($download_info)) {
            $data['total'] = $download_info['total'];
        } else {
            $data['total'] = '';
        }

        if (isset($this->request->post['value'])) {
            $data['value'] = $this->request->post['value'];
        } elseif (!empty($download_info)) {
            $data['value'] = $download_info['value'];
        } else {
            $data['value'] = '';
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('catalog/voucher_form', $data));
    }

    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'catalog/customvoucher')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if ((utf8_strlen($this->request->post['total']) < 1) || (utf8_strlen($this->request->post['total']) > 128)) {
            $this->error['total'] = $this->language->get('error_total');
        }

        if ((utf8_strlen($this->request->post['value']) < 1) || (utf8_strlen($this->request->post['value']) > 128)) {
            $this->error['value'] = $this->language->get('error_value');
        }

        return !$this->error;
    }

    protected function validateDelete() {
        if (!$this->user->hasPermission('modify', 'catalog/customvoucher')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }
}