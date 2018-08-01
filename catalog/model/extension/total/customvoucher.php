<?php
class ModelExtensionTotalCustomVoucher extends Model {
    public function getCustomvoucher() {
        $customvoucher_query = $this->db->query("SELECT * FROM voucher ORDER BY total DESC");

        foreach ($customvoucher_query->rows as $voucher)
        {
            if ($voucher['total'] < $this->cart->getSubTotal()) {
                return array(
                    'customvoucher_id'      => $voucher['voucher_id'],
                    'discount'              => $voucher['value'],
                    'total'                 => $voucher['total'],
                );
            }
        }

        unset($this->session->data['voucher_used']);

    }

    public function getTotal($total) {
        
        $this->load->language('extension/total/customvoucher');

        $customvoucher_info = $this->getCustomvoucher();

        if ($customvoucher_info && isset($this->session->data['voucher_used'])) {

            $sub_total = $this->cart->getSubTotal();

            $customvoucher_info['discount'] = min($customvoucher_info['discount'], $sub_total);

            $discount_total = $customvoucher_info['discount'];

            // If discount greater than total
            if ($discount_total > $total) {
                $discount_total = $total;
            }

            if ($discount_total > 0) {
                $total['totals'][] = array(
                    'code'       => 'customvoucher',
                    'title'      => $this->language->get('text_customvoucher'),
                    'value'      => -$discount_total,
                    'sort_order' => $this->config->get('customvoucher_sort_order')
                );

                $total['total'] -= $discount_total;
            }
        }
    }
}
