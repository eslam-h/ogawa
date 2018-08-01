<?php
class ModelToolcartbindercombo1a extends Model {
	public function addcartbindercombo1a($data) {
		asort($data['primaryproducts']);
		$primarypids = implode(",", $data['primaryproducts']);
		if(!isset($data['cids'])){$data['cids']=array();}
		$this->db->query("INSERT INTO  " . DB_PREFIX . "cartbindercombo1a_setting SET type = '" . (int)$data['type'] . "',discount = '" . (float)$data['discount'] . "',primaryquant = '" . (float)$data['primaryquant'] . "',secondaryquant = '" . (float)$data['secondaryquant'] . "', primarypids = '".$this->db->escape($primarypids)./*"',name = '".$this->db->escape($data['name']).*/"', sales_offer_id = '".(int)$data['sales_offer_id']."', cids = '".json_encode($data['cids'])."', showoffer = '".(int)$data['showoffer']."', displaylocation = '".(int)$data['displaylocation']."', bundle = '".(int)$data['bundle']."', status = '".(int)$data['status']."'");
        
        foreach ($data['name'] as $language_id => $value) {
            $sql = "INSERT INTO ".DB_PREFIX."cartbindercombo1a_setting_description SET title= '".$value.
                "', language_id = '".$language_id."', offer_id = (SELECT LAST_INSERT_ID());";
            
            $this->db->query($sql);
        }
	}

	public function editcartbindercombo1a($id, $data) {
		asort($data['primaryproducts']);
		$primarypids = implode(",", $data['primaryproducts']);
		if(!isset($data['cids'])){$data['cids']=array();}
		$this->db->query("UPDATE " . DB_PREFIX . "cartbindercombo1a_setting SET type = '" . (int)$data['type'] . "',discount = '" . (float)$data['discount'] . "',primaryquant = '" . (float)$data['primaryquant'] . "',secondaryquant = '" . (float)$data['secondaryquant'] . "', primarypids = '".$this->db->escape($primarypids)./*"',name = '".$this->db->escape($data['name']).*/"', sales_offer_id = '".(int)$data['sales_offer_id']."', cids = '".json_encode($data['cids'])."', showoffer = '".(int)$data['showoffer']."', displaylocation = '".(int)$data['displaylocation']."', bundle = '".(int)$data['bundle']."', status = '".(int)$data['status']."' WHERE id = '" . (int)$id . "'");
        
        foreach ($data['name'] as $language_id => $value) {
            $sql = "UPDATE ".DB_PREFIX."cartbindercombo1a_setting_description SET title= '".$value.
                "' WHERE language_id = ".$language_id." AND offer_id = ".$id.";";
            
            $this->db->query($sql);
        }
	}
	
	public function delete($id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "cartbindercombo1a_setting WHERE id = '" . (int)$id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "cartbindercombo1a_setting_description WHERE offer_id = '" . (int)$id . "'");
	}

	public function getcartbindercombo1a($id) {
//		$query = $this->db->query("SELECT  * FROM " . DB_PREFIX . "cartbindercombo1a_setting WHERE id = '" . (int)$id . "'");

        $query = "SELECT  c.*, des.title as name FROM ".DB_PREFIX."cartbindercombo1a_setting c
        LEFT JOIN ".DB_PREFIX."cartbindercombo1a_setting_description des on des.offer_id = c.id
        WHERE c.id = ".$id." AND des.language_id = ".(int)$this->config->get('config_language_id').";";
        
        return $this->db->query($query)->row;
	}

	public function getTotalForOffer($offer_id) {
		$query = $this->db->query("SELECT  SUM(total) as total FROM " . DB_PREFIX . "cartbindercombo1a WHERE offer_id = '" . (int)$offer_id . "'");
		if ($query->num_rows) {
			return $this->currency->format($query->row['total'], $this->config->get('config_currency'));	
		} else {
			return 0;
		}
	}
    public function getcartbindercombo1aDescription($id)
    {
         $query = "SELECT language_id ,title as name FROM ".DB_PREFIX."cartbindercombo1a_setting_description
         WHERE offer_id = ".$id ;
         $results = $this->db->query($query)->rows;
        
         $values = array();
         foreach($results as $result)
         {
             $values[$result['language_id']] = $result['name'];
         }
        
        return $values;
    }
    
	public function getTotalOfferApplied($offer_id) {
		$query = $this->db->query("SELECT  DISTINCT(order_id) FROM " . DB_PREFIX . "cartbindercombo1a WHERE offer_id = '" . (int)$offer_id . "'");
		return $query->num_rows;
	}
	
	public function getNames($pids) {
		$products = explode(",", $pids);
		$name = $out = "";
		foreach ($products as $key => $value) {
			$productname =  $this->getProduct($value);
			$name .= $out.$productname;
			$out = "<br>";
		}
		return $name;
		
	}

	public function getProduct($product_id) {
		$query = $this->db->query("SELECT pd.name FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
		return $query->row['name'];
	}

	

	public function getcartbindercombo1as($data) {
		$sql = "SELECT c.*, des.title as name FROM " . DB_PREFIX . "cartbindercombo1a_setting c LEFT JOIN " . DB_PREFIX . "cartbindercombo1a_setting_description des on des.offer_id = c.id WHERE des.language_id = ".(int)$this->config->get('config_language_id');

		if (!empty($data['filter_name'])) {
			$sql .= " AND des.title LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND c.status = '" . (int)$data['filter_status'] . "'";
		}	


		$sort_data = array(
			'c.name',
			'c.id',
			'c.status'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . 'des.title';//$data['sort'];
		} else {
			$sql .= " ORDER BY c.id";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}
		
		$query = $this->db->query($sql);
	
		return $query->rows;
	}
	
	public function getTotalcartbindercombo1a($data) {
		
		$sql = "SELECT c.*, des.title as name FROM " . DB_PREFIX . "cartbindercombo1a_setting c LEFT JOIN " . DB_PREFIX . "cartbindercombo1a_setting_description des on des.offer_id = c.id WHERE des.language_id = ".(int)$this->config->get('config_language_id');

		if (!empty($data['filter_name'])) {
			$sql .= " AND des.title LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND c.status = '" . (int)$data['filter_status'] . "'";
		}	
		
		$query = $this->db->query($sql);
		
		return $query->num_rows;
	}
}
?>