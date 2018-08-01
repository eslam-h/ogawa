<?php
class ModelToolcartbindercombo1b extends Model {
	public function addcartbindercombo1b($data) {
		asort($data['primaryproducts']);
		asort($data['secondarycategories']);
		$primarypids = implode(",", $data['primaryproducts']);
		$secondarycids = implode(",", $data['secondarycategories']);
		if(!isset($data['cids'])){$data['cids']=array();}
		$this->db->query("INSERT INTO  " . DB_PREFIX . "cartbindercombo1b_setting SET type = '" . (int)$data['type'] . "',discount = '" . (float)$data['discount'] . "',anyorall = '" . (int)$data['anyorall'] . "',primaryquant = '" . (float)$data['primaryquant'] . "',secondaryquant = '" . (float)$data['secondaryquant'] . "', primarypids = '".$this->db->escape($primarypids)."',secondarycids = '".$this->db->escape($secondarycids)./*"',name = '".$this->db->escape($data['name']).*/"', sales_offer_id = '".(int)$data['sales_offer_id']."', cids = '".json_encode($data['cids'])."', showoffer = '".(int)$data['showoffer']."', displaylocation = '".(int)$data['displaylocation']."', status = '".(int)$data['status']."'");
         foreach ($data['name'] as $language_id => $value) {
            $sql = "INSERT INTO ".DB_PREFIX."cartbindercombo1b_setting_description SET title= '".$value.
                "', language_id = '".$language_id."', offer_id = (SELECT LAST_INSERT_ID());";
            
            $this->db->query($sql);
        }
	}

	public function editcartbindercombo1b($id, $data) {
		asort($data['primaryproducts']);
		asort($data['secondarycategories']);
		$primarypids = implode(",", $data['primaryproducts']);
		$secondarycids = implode(",", $data['secondarycategories']);
		if(!isset($data['cids'])){$data['cids']=array();}
		$this->db->query("UPDATE " . DB_PREFIX . "cartbindercombo1b_setting SET type = '" . (int)$data['type'] . "',discount = '" . (float)$data['discount'] . "',anyorall = '" . (int)$data['anyorall'] . "',primaryquant = '" . (float)$data['primaryquant'] . "',secondaryquant = '" . (float)$data['secondaryquant'] . "', primarypids = '".$this->db->escape($primarypids)."',secondarycids = '".$this->db->escape($secondarycids)./*"',name = '".$this->db->escape($data['name']).*/"', sales_offer_id = '".(int)$data['sales_offer_id']."', cids = '".json_encode($data['cids'])."', showoffer = '".(int)$data['showoffer']."', displaylocation = '".(int)$data['displaylocation']."', status = '".(int)$data['status']."' WHERE id = '" . (int)$id . "'");
        
        foreach ($data['name'] as $language_id => $value) {
            $sql = "UPDATE ".DB_PREFIX."cartbindercombo1b_setting_description SET title= '".$value.
                "' WHERE language_id = ".$language_id." AND offer_id = ".$id.";";
            
            $this->db->query($sql);
        }
	}
	
	public function delete($id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "cartbindercombo1b_setting WHERE id = '" . (int)$id . "'"); 
        $this->db->query("DELETE FROM " . DB_PREFIX . "cartbindercombo1b_setting_description WHERE offer_id = '" . (int)$id . "'");
	}

	public function getcartbindercombo1b($id) {
//		$query = $this->db->query("SELECT  * FROM " . DB_PREFIX . "cartbindercombo1b_setting WHERE id = '" . (int)$id . "'");

         $query = "SELECT  c.*, des.title as name FROM ".DB_PREFIX."cartbindercombo1b_setting c
        LEFT JOIN ".DB_PREFIX."cartbindercombo1b_setting_description des on des.offer_id = c.id
        WHERE c.id = ".$id." AND des.language_id = ".(int)$this->config->get('config_language_id').";";

		return $this->db->query($query)->row;
	}
    
    public function getcartbindercombo1bDescription($id)
    {
         $query = "SELECT language_id ,title as name FROM ".DB_PREFIX."cartbindercombo1b_setting_description
         WHERE offer_id = ".$id ;
         $results = $this->db->query($query)->rows;
        
         $values = array();
         foreach($results as $result)
         {
             $values[$result['language_id']] = $result['name'];
         }
        
        return $values;
    }

        
	public function getTotalForOffer($offer_id) {
		$query = $this->db->query("SELECT  SUM(total) as total FROM " . DB_PREFIX . "cartbindercombo1b WHERE offer_id = '" . (int)$offer_id . "'");
		if ($query->num_rows) {
			return $this->currency->format($query->row['total'], $this->config->get('config_currency'));	
		} else {
			return 0;
		}
	}

	public function getTotalOfferApplied($offer_id) {
		$query = $this->db->query("SELECT  DISTINCT(order_id) FROM " . DB_PREFIX . "cartbindercombo1b WHERE offer_id = '" . (int)$offer_id . "'");
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

	public function getCNames($cids) {
		$categories = explode(",", $cids);
		$name = $out = "";
		foreach ($categories as $key => $value) {
			$categoryname =  $this->getCategory($value);
			$name .= $out.$categoryname;
			$out = "<br>";
		}
		return $name;
		
	}

	public function getCategory($category_id) {
		if($category_id) {
			$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "category c  LEFT JOIN " . DB_PREFIX . "category_description cd2 ON (c.category_id = cd2.category_id) WHERE c.category_id = '" . (int)$category_id . "' AND cd2.language_id = '" . (int)$this->config->get('config_language_id') . "'");
			return $query->row['name'];
		} else {
			return "";
		}
	}

	
	public function getcartbindercombo1bs($data) {
		$sql = "SELECT c.*, des.title as name FROM " . DB_PREFIX . "cartbindercombo1b_setting c LEFT JOIN " . DB_PREFIX . "cartbindercombo1b_setting_description des on des.offer_id = c.id WHERE des.language_id = ".(int)$this->config->get('config_language_id');

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
	
	public function getTotalcartbindercombo1b($data) {
		
		$sql = "SELECT c.*, des.title as name FROM " . DB_PREFIX . "cartbindercombo1b_setting c LEFT JOIN " . DB_PREFIX . "cartbindercombo1b_setting_description des on des.offer_id = c.id WHERE des.language_id = ".(int)$this->config->get('config_language_id');

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