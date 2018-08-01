<?php

class ModelCatalogCustomVoucher extends Model
{
    public function addVoucher($data) {
        $this->db->query("INSERT INTO voucher SET total = '" . $this->db->escape($data['total']) . "', value = '" . $this->db->escape($data['value']) . "'");

        $voucher_id = $this->db->getLastId();

        return $voucher_id;
    }

    public function editVoucher($voucher_id, $data) {
        $this->db->query("UPDATE voucher SET total = '" . $this->db->escape($data['total']) . "', value = '" . $this->db->escape($data['value']) . "' WHERE voucher_id = '" . (int)$voucher_id . "'");
    }

    public function deleteVoucher($voucher_id) {
        $this->db->query("DELETE FROM voucher WHERE voucher_id = '" . (int)$voucher_id . "'");
    }

    public function getVoucher($voucher_id) {
        $query = $this->db->query("SELECT DISTINCT * FROM voucher v WHERE v.voucher_id = '" . (int)$voucher_id . "'");

        return $query->row;
    }

    public function getVouchers($data = array()) {
        $sql = "SELECT * FROM voucher v ";


        $sort_data = array(
            'v.total',
            'v.value'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY v.total";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getTotalVouchers() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM voucher");

        return $query->row['total'];
    }
}