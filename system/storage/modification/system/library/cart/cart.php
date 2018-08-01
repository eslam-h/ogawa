<?php
namespace Cart;
class Cart {
	private $data = array();

	public function __construct($registry) {
		$this->config = $registry->get('config');
		$this->customer = $registry->get('customer');
		$this->session = $registry->get('session');
		$this->db = $registry->get('db');
		$this->tax = $registry->get('tax');
		$this->weight = $registry->get('weight');
if (!isset($this->session->data['slscmdprdts']) || !is_array($this->session->data['slscmdprdts'])) {$this->session->data['slscmdprdts'] = array();}
     if (!isset($this->session->data['cartbindercombooffers']) || !is_array($this->session->data['cartbindercombooffers'])) { $this->session->data['cartbindercombooffers'] = array(); }
      

		// Remove all the expired carts with no customer ID
		$this->db->query("DELETE FROM " . DB_PREFIX . "cart WHERE (api_id > '0' OR customer_id = '0') AND date_added < DATE_SUB(NOW(), INTERVAL 1 HOUR)");

		if ($this->customer->getId()) {
			// We want to change the session ID on all the old items in the customers cart
			$this->db->query("UPDATE " . DB_PREFIX . "cart SET session_id = '" . $this->db->escape($this->session->getId()) . "' WHERE api_id = '0' AND customer_id = '" . (int)$this->customer->getId() . "'");

			// Once the customer is logged in we want to update the customers cart
			$cart_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cart WHERE api_id = '0' AND customer_id = '0' AND session_id = '" . $this->db->escape($this->session->getId()) . "'");

			foreach ($cart_query->rows as $cart) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "cart WHERE cart_id = '" . (int)$cart['cart_id'] . "'");

				// The advantage of using $this->add is that it will check if the products already exist and increaser the quantity if necessary.
				$this->add($cart['product_id'], $cart['quantity'], json_decode($cart['option']), $cart['recurring_id']);
			}
		}
	}

	public function getProducts() {
		$product_data = array();

		$cart_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cart WHERE api_id = '" . (isset($this->session->data['api_id']) ? (int)$this->session->data['api_id'] : 0) . "' AND customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "'");

		foreach ($cart_query->rows as $cart) {
			$stock = true;

			$product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_store p2s LEFT JOIN " . DB_PREFIX . "product p ON (p2s.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND p2s.product_id = '" . (int)$cart['product_id'] . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.date_available <= NOW() AND p.status = '1'");

			if ($product_query->num_rows && ($cart['quantity'] > 0)) {
				$option_price = 0;
				$option_points = 0;
				$option_weight = 0;

				$option_data = array();

				foreach (json_decode($cart['option']) as $product_option_id => $value) {
					$option_query = $this->db->query("SELECT po.product_option_id, po.option_id, od.name, o.type FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_option_id = '" . (int)$product_option_id . "' AND po.product_id = '" . (int)$cart['product_id'] . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "'");

					if ($option_query->num_rows) {
						if ($option_query->row['type'] == 'select' || $option_query->row['type'] == 'radio') {
							$option_value_query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$value . "' AND pov.product_option_id = '" . (int)$product_option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

							if ($option_value_query->num_rows) {
								if ($option_value_query->row['price_prefix'] == '+') {
									$option_price += $option_value_query->row['price'];
								} elseif ($option_value_query->row['price_prefix'] == '-') {
									$option_price -= $option_value_query->row['price'];
								}

								if ($option_value_query->row['points_prefix'] == '+') {
									$option_points += $option_value_query->row['points'];
								} elseif ($option_value_query->row['points_prefix'] == '-') {
									$option_points -= $option_value_query->row['points'];
								}

								if ($option_value_query->row['weight_prefix'] == '+') {
									$option_weight += $option_value_query->row['weight'];
								} elseif ($option_value_query->row['weight_prefix'] == '-') {
									$option_weight -= $option_value_query->row['weight'];
								}

								if ($option_value_query->row['subtract'] && (!$option_value_query->row['quantity'] || ($option_value_query->row['quantity'] < $cart['quantity']))) {
									$stock = false;
								}

								$option_data[] = array(
									'product_option_id'       => $product_option_id,
									'product_option_value_id' => $value,
									'option_id'               => $option_query->row['option_id'],
									'option_value_id'         => $option_value_query->row['option_value_id'],
									'name'                    => $option_query->row['name'],
									'value'                   => $option_value_query->row['name'],
									'type'                    => $option_query->row['type'],
									'quantity'                => $option_value_query->row['quantity'],
									'subtract'                => $option_value_query->row['subtract'],
									'price'                   => $option_value_query->row['price'],
									'price_prefix'            => $option_value_query->row['price_prefix'],
									'points'                  => $option_value_query->row['points'],
									'points_prefix'           => $option_value_query->row['points_prefix'],
									'weight'                  => $option_value_query->row['weight'],
									'weight_prefix'           => $option_value_query->row['weight_prefix']
								);
							}
						} elseif ($option_query->row['type'] == 'checkbox' && is_array($value)) {
							foreach ($value as $product_option_value_id) {
								$option_value_query = $this->db->query("SELECT pov.option_value_id, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix, ovd.name FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (pov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$product_option_value_id . "' AND pov.product_option_id = '" . (int)$product_option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

								if ($option_value_query->num_rows) {
									if ($option_value_query->row['price_prefix'] == '+') {
										$option_price += $option_value_query->row['price'];
									} elseif ($option_value_query->row['price_prefix'] == '-') {
										$option_price -= $option_value_query->row['price'];
									}

									if ($option_value_query->row['points_prefix'] == '+') {
										$option_points += $option_value_query->row['points'];
									} elseif ($option_value_query->row['points_prefix'] == '-') {
										$option_points -= $option_value_query->row['points'];
									}

									if ($option_value_query->row['weight_prefix'] == '+') {
										$option_weight += $option_value_query->row['weight'];
									} elseif ($option_value_query->row['weight_prefix'] == '-') {
										$option_weight -= $option_value_query->row['weight'];
									}

									if ($option_value_query->row['subtract'] && (!$option_value_query->row['quantity'] || ($option_value_query->row['quantity'] < $cart['quantity']))) {
										$stock = false;
									}

									$option_data[] = array(
										'product_option_id'       => $product_option_id,
										'product_option_value_id' => $product_option_value_id,
										'option_id'               => $option_query->row['option_id'],
										'option_value_id'         => $option_value_query->row['option_value_id'],
										'name'                    => $option_query->row['name'],
										'value'                   => $option_value_query->row['name'],
										'type'                    => $option_query->row['type'],
										'quantity'                => $option_value_query->row['quantity'],
										'subtract'                => $option_value_query->row['subtract'],
										'price'                   => $option_value_query->row['price'],
										'price_prefix'            => $option_value_query->row['price_prefix'],
										'points'                  => $option_value_query->row['points'],
										'points_prefix'           => $option_value_query->row['points_prefix'],
										'weight'                  => $option_value_query->row['weight'],
										'weight_prefix'           => $option_value_query->row['weight_prefix']
									);
								}
							}
						} elseif ($option_query->row['type'] == 'text' || $option_query->row['type'] == 'textarea' || $option_query->row['type'] == 'file' || $option_query->row['type'] == 'date' || $option_query->row['type'] == 'datetime' || $option_query->row['type'] == 'time') {
							$option_data[] = array(
								'product_option_id'       => $product_option_id,
								'product_option_value_id' => '',
								'option_id'               => $option_query->row['option_id'],
								'option_value_id'         => '',
								'name'                    => $option_query->row['name'],
								'value'                   => $value,
								'type'                    => $option_query->row['type'],
								'quantity'                => '',
								'subtract'                => '',
								'price'                   => '',
								'price_prefix'            => '',
								'points'                  => '',
								'points_prefix'           => '',
								'weight'                  => '',
								'weight_prefix'           => ''
							);
						}
					}
				}

				$price = $product_query->row['price'];

				// Product Discounts
				$discount_quantity = 0;

				foreach ($cart_query->rows as $cart_2) {
					if ($cart_2['product_id'] == $cart['product_id']) {
						$discount_quantity += $cart_2['quantity'];
					}
				}

				$product_discount_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$cart['product_id'] . "' AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND quantity <= '" . (int)$discount_quantity . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY quantity DESC, priority ASC, price ASC LIMIT 1");

				if ($product_discount_query->num_rows) {
					$price = $product_discount_query->row['price'];
				}

				// Product Specials
				$product_special_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$cart['product_id'] . "' AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY priority ASC, price ASC LIMIT 1");

				if ($product_special_query->num_rows) {
					$price = $product_special_query->row['price'];
				}

				// Reward Points
				$product_reward_query = $this->db->query("SELECT points FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$cart['product_id'] . "' AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "'");

				if ($product_reward_query->num_rows) {
					$reward = $product_reward_query->row['points'];
				} else {
					$reward = 0;
				}

				// Downloads

      $version = str_replace(".","",VERSION);if($version > 2100) {$key = $cart['cart_id'];$netquantity2 = $salecombinationquantity = $cart['quantity'];} else {$netquantity2 = $salecombinationquantity = $quantity;}
      $salecombination2price = 0;
      if(isset($this->session->data['cartbindercombooffers'][$key])) {
        //$this->log->write(print_r($this->session->data['cartbindercombooffers'],true));
        $ogquantity = $netquantity2;$netquantity2 = $netquantity2 - intval($this->session->data['cartbindercombooffers'][$key]['quantity']);$salecombinationquantity = intval($this->session->data['cartbindercombooffers'][$key]['quantity']);
        $netnewprice = $price + $option_price;
        if($this->session->data['cartbindercombooffers'][$key]['type']){$salecombination2price = ($netnewprice - $this->session->data['cartbindercombooffers'][$key]['discount'])/$netnewprice;} else {
        $salecombination2price = (100 - $this->session->data['cartbindercombooffers'][$key]['discount'])/100;}
        //$this->log->write("salecombinationprice: ".$salecombination2price);$this->log->write("salecombinationquantity: ".$salecombinationquantity);
        $salecombinationquantity =  $netquantity2 + ($salecombinationquantity * $salecombination2price);
        $discountdone = ($netnewprice * $ogquantity) - ($netnewprice * $salecombinationquantity);
        $this->session->data['cartbindercombooffers'][$key]['discountdone'] = $discountdone;
        //$this->log->write("price".$price."---ogquantity".$ogquantity);
        //$this->log->write(print_r($this->session->data['cartbindercombooffers'],true));
      }
      
				$download_data = array();

				$download_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_download p2d LEFT JOIN " . DB_PREFIX . "download d ON (p2d.download_id = d.download_id) LEFT JOIN " . DB_PREFIX . "download_description dd ON (d.download_id = dd.download_id) WHERE p2d.product_id = '" . (int)$cart['product_id'] . "' AND dd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

				foreach ($download_query->rows as $download) {
					$download_data[] = array(
						'download_id' => $download['download_id'],
						'name'        => $download['name'],
						'filename'    => $download['filename'],
						'mask'        => $download['mask']
					);
				}

				// Stock
				if (!$product_query->row['quantity'] || ($product_query->row['quantity'] < $cart['quantity'])) {
					$stock = false;
				}

				$recurring_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "recurring r LEFT JOIN " . DB_PREFIX . "product_recurring pr ON (r.recurring_id = pr.recurring_id) LEFT JOIN " . DB_PREFIX . "recurring_description rd ON (r.recurring_id = rd.recurring_id) WHERE r.recurring_id = '" . (int)$cart['recurring_id'] . "' AND pr.product_id = '" . (int)$cart['product_id'] . "' AND rd.language_id = " . (int)$this->config->get('config_language_id') . " AND r.status = 1 AND pr.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "'");

				if ($recurring_query->num_rows) {
					$recurring = array(
						'recurring_id'    => $cart['recurring_id'],
						'name'            => $recurring_query->row['name'],
						'frequency'       => $recurring_query->row['frequency'],
						'price'           => $recurring_query->row['price'],
						'cycle'           => $recurring_query->row['cycle'],
						'duration'        => $recurring_query->row['duration'],
						'trial'           => $recurring_query->row['trial_status'],
						'trial_frequency' => $recurring_query->row['trial_frequency'],
						'trial_price'     => $recurring_query->row['trial_price'],
						'trial_cycle'     => $recurring_query->row['trial_cycle'],
						'trial_duration'  => $recurring_query->row['trial_duration']
					);
				} else {
					$recurring = false;
				}

				$product_data[] = array(
'salecombinationquantity' => $salecombinationquantity,
					'cart_id'         => $cart['cart_id'],
					'product_id'      => $product_query->row['product_id'],
					'name'            => $product_query->row['name'],
					'model'           => $product_query->row['model'],
					'shipping'        => $product_query->row['shipping'],
					'image'           => $product_query->row['image'],
					'option'          => $option_data,
					'download'        => $download_data,
					'quantity'        => $cart['quantity'],
					'minimum'         => $product_query->row['minimum'],
					'subtract'        => $product_query->row['subtract'],
					'stock'           => $stock,
					'price'           => ($price + $option_price),
					'total'           => ($price + $option_price) * $cart['quantity'],
					'reward'          => $reward * $cart['quantity'],
					'points'          => ($product_query->row['points'] ? ($product_query->row['points'] + $option_points) * $cart['quantity'] : 0),
					'tax_class_id'    => $product_query->row['tax_class_id'],
					'weight'          => ($product_query->row['weight'] + $option_weight) * $cart['quantity'],
					'weight_class_id' => $product_query->row['weight_class_id'],
					'length'          => $product_query->row['length'],
					'width'           => $product_query->row['width'],
					'height'          => $product_query->row['height'],
					'length_class_id' => $product_query->row['length_class_id'],
					'recurring'       => $recurring
				);
			} else {
				$this->remove($cart['cart_id']);
			}
		}

		return $product_data;
	}

	public function add($product_id, $quantity = 1, $option = array(), $recurring_id = 0) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "cart WHERE api_id = '" . (isset($this->session->data['api_id']) ? (int)$this->session->data['api_id'] : 0) . "' AND customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "' AND product_id = '" . (int)$product_id . "' AND recurring_id = '" . (int)$recurring_id . "' AND `option` = '" . $this->db->escape(json_encode($option)) . "'");

		if (!$query->row['total']) {
			$this->db->query("INSERT " . DB_PREFIX . "cart SET api_id = '" . (isset($this->session->data['api_id']) ? (int)$this->session->data['api_id'] : 0) . "', customer_id = '" . (int)$this->customer->getId() . "', session_id = '" . $this->db->escape($this->session->getId()) . "', product_id = '" . (int)$product_id . "', recurring_id = '" . (int)$recurring_id . "', `option` = '" . $this->db->escape(json_encode($option)) . "', quantity = '" . (int)$quantity . "', date_added = NOW()");
		} else {
			$this->db->query("UPDATE " . DB_PREFIX . "cart SET quantity = (quantity + " . (int)$quantity . ") WHERE api_id = '" . (isset($this->session->data['api_id']) ? (int)$this->session->data['api_id'] : 0) . "' AND customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "' AND product_id = '" . (int)$product_id . "' AND recurring_id = '" . (int)$recurring_id . "' AND `option` = '" . $this->db->escape(json_encode($option)) . "'");
 }if(1){$this->sortcombination();
$this->salesonecombo2();
$this->salesonecombo2a();
$this->salesonecombo1();
$this->salesonecombo1c();
$this->salesonecombo1b();
$this->salesonecombo1a();
		}
	}

	public function update($cart_id, $quantity) {
		$this->db->query("UPDATE " . DB_PREFIX . "cart SET quantity = '" . (int)$quantity . "' WHERE cart_id = '" . (int)$cart_id . "' AND api_id = '" . (isset($this->session->data['api_id']) ? (int)$this->session->data['api_id'] : 0) . "' AND customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "'");
$this->sortcombination();
$this->salesonecombo2();
$this->salesonecombo2a();
$this->salesonecombo1();
$this->salesonecombo1c();
$this->salesonecombo1b();
$this->salesonecombo1a();
	}

	public function remove($cart_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "cart WHERE cart_id = '" . (int)$cart_id . "' AND api_id = '" . (isset($this->session->data['api_id']) ? (int)$this->session->data['api_id'] : 0) . "' AND customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "'");
$this->sortcombination();
$this->salesonecombo2();
$this->salesonecombo2a();
$this->salesonecombo1();
$this->salesonecombo1c();
$this->salesonecombo1b();
$this->salesonecombo1a();
	}

	public function clear() {
		$this->db->query("DELETE FROM " . DB_PREFIX . "cart WHERE api_id = '" . (isset($this->session->data['api_id']) ? (int)$this->session->data['api_id'] : 0) . "' AND customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "'");
	}

	public function getRecurringProducts() {
		$product_data = array();

		foreach ($this->getProducts() as $value) {
			if ($value['recurring']) {
				$product_data[] = $value;
			}
		}

		return $product_data;
	}

public function sortcombination(){
      //$this->log->write("insortcombination");
      unset($this->session->data['cartbindercombooffers']);
      $this->session->data['cartbindercombooffers_pages'] = array();
      $this->session->data['cartbindercombooffers_offerapplied'] = array();
      $this->session->data['slscmdprdts'] = array();$this->session->data['slscmdctgs'] = array();$this->session->data['slscmdctgscids'] = array();$version = str_replace(".","",VERSION);if($version > 2100) {
        $cart_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cart WHERE customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "'");
        foreach ($cart_query->rows as $cart) {
        $product_id =  $cart['product_id'];
$this->getcategories($product_id,$cart['quantity']);
        if (!isset($this->session->data['slscmdprdts'][$product_id])) {$this->session->data['slscmdprdts'][$product_id] = (int)$cart['quantity'];} else {$this->session->data['slscmdprdts'][$product_id] += (int)$cart['quantity'];}} } else {$productsincart = $this->session->data['cart'];
        foreach ($productsincart as $key => $quantity) {$product = unserialize(base64_decode($key));
        $product_id =  $product['product_id'];
        if (!isset($this->session->data['slscmdprdts'][$product_id])) {$this->session->data['slscmdprdts'][$product_id] = (int)$quantity;} else {$this->session->data['slscmdprdts'][$product_id] += (int)$quantity;}} }
        }

        public function getcategories($product_id,$quantity) {
      $queries = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");

      foreach ($queries->rows as $result) {
          $this->session->data['slscmdctgs'][$product_id][] = $result['category_id'];
          if(isset($this->session->data['slscmdctgscids'][$result['category_id']])) {
            $this->session->data['slscmdctgscids'][$result['category_id']] += $quantity;
          } else {
            $this->session->data['slscmdctgscids'][$result['category_id']] = $quantity;
          }
      }
    }
     public function checkcondition($product_id,$secondaryarray,$secondarycarray) {
      if(in_array($product_id,$secondaryarray))  {
        return 1;
      }
      if(isset($this->session->data['slscmdctgs'][$product_id])) {
        foreach($this->session->data['slscmdctgs'][$product_id] as $key => $value) {
          if(in_array($value,$secondarycarray)) {
            return 1;
          }
        }
      }
      return 0;
    }
    public function checkcondition2($product_id,$secondarycarray) {
      if(isset($this->session->data['slscmdctgs'][$product_id])) {
        foreach($this->session->data['slscmdctgs'][$product_id] as $key => $value) {
          if(in_array($value,$secondarycarray)) {
            return 1;
          }
        }
      }
      return 0;
    }
     public function checkCg($cids = array()) {
  $cggrup = json_decode($cids,true);
      if(!empty($cggrup)) {
        $cgid = $this->customer->getGroupId();
        if(!in_array($cgid, $cggrup)) {
           return 1;
        }
      }
      return 0;
    }
    public function xnfrmla($tn,$pq,$sq) {
        $sm = $pq + $sq;
        $answer = floor( $tn / $sm );
        $bq = ($answer * $sm) + $pq;
        $uq = ($answer * $sm) + $sm;
        $min = min($tn,$uq);
        $dyn = $min - $bq;
        if($dyn < 0){$dyn = 0;}
        $fq = $answer * $sq;
        return $fq + $dyn;
    }
      

      public function salesonecombo1a(){if(isset($this->session->data['slscmdprdts']) && !empty($this->session->data['slscmdprdts'])){
        //$this->log->write("wholesaleconditionexists");$this->log->write(print_r($this->session->data['slscmdprdts'],true));
          $query = $this->db->query("SELECT  * FROM " . DB_PREFIX . "cartbindercombo1a_setting WHERE status = '1'");
        foreach($query->rows as $step => $destination) {
            $query = "SELECT language_id ,title as name FROM ".DB_PREFIX."cartbindercombo1a_setting_description WHERE offer_id = ".$destination['id'] ;
            $results = $this->db->query($query)->rows;
        
            $values = array();
            foreach($results as $result)
            { 
                $values[$result['language_id']] = $result['name'];
            }
            $destination['name'] = $values;
$primaryarray = explode(",",$destination['primarypids']);
           //$this->log->write("primaryarray");$this->log->write(print_r($primaryarray,true));
            if($this->checkCg($destination['cids'])) {continue;}
            foreach($primaryarray as $key => $value){if(!array_key_exists($value,$this->session->data['slscmdprdts'])) {
               //$this->log->write("break from primary");
               continue 2;
            }}
            //$this->log->write("offer should come");
            $totalprimaryquantity = array();foreach($primaryarray as $pids) {$totalprimaryquantity[] = $this->session->data['slscmdprdts'][$pids];}$commonseocndaryquantity = min($totalprimaryquantity);
            //$this->log->write("common quantity".$commonseocndaryquantity);
            //$netquantity = floor($commonseocndaryquantity/($destination['primaryquant']+$destination['secondaryquant']));
            $netquantity = $this->xnfrmla($commonseocndaryquantity,$destination['primaryquant'],$destination['secondaryquant']);
            //$netquantity = $commonseocndaryquantity;
            //$this->log->write("net quantity".$netquantity);
           $i = 0;if($netquantity >= 0) {$i = 1;}
            $version = str_replace(".","",VERSION);$spprtsimproopt = array();$temp  = $netquantity;$quantityapply = $temp;
            if($version > 2100) {
             $cart_query = $this->db->query("SELECT c.cart_id,c.quantity,c.product_id FROM " . DB_PREFIX . "cart c LEFT JOIN " . DB_PREFIX . "product p ON (p.product_id = c.product_id) WHERE c.customer_id = '" . (int)$this->customer->getId() . "' AND c.session_id = '" . $this->db->escape($this->session->getId()) . "' ORDER BY p.price ASC");
              foreach ($cart_query->rows as $cart) {
                if(!isset($this->session->data['cartbindercombooffers'][$cart['cart_id']]) && in_array($cart['product_id'], $primaryarray) && ($quantityapply > 0)) {
                 $temp = $quantityapply;$quantityapply = $quantityapply - $cart['quantity'];
                 $i = 0; $this->session->data['cartbindercombooffers_offerapplied'][] = $destination['sales_offer_id'];
                 // $this->log->write("Quantity apply : ".$quantityapply."Temp : ".$temp);
                  $this->session->data['cartbindercombooffers'][$cart['cart_id']] = $destination;if($quantityapply >= 0) {$this->session->data['cartbindercombooffers'][$cart['cart_id']]['quantity'] = $cart['quantity'];} else {$this->session->data['cartbindercombooffers'][$cart['cart_id']]['quantity'] = $temp;} if($quantityapply <= 0) {break;}
                  //$this->log->write("offer on product id ".$product_id);$this->log->write(print_r($this->session->data['cartbindercombooffers'],true));
                }
              }
            } else {
              foreach ($this->session->data['cart'] as $key => $quantity) {
              $product = unserialize(base64_decode($key));$product_id = $product['product_id'];
                if(!isset($this->session->data['cartbindercombooffers'][$key]) && in_array($product_id,$primaryarray) && ($quantityapply > 0)) {
                    $temp = $quantityapply;$quantityapply = $quantityapply - $quantity;
                    $i = 0;
                     $this->session->data['cartbindercombooffers_offerapplied'][] = $destination['sales_offer_id'];
                    //$this->log->write("Quantity apply : ".$quantityapply."Temp : ".$temp);
                    $this->session->data['cartbindercombooffers'][$key] =  $destination;
                    if($quantityapply >= 0) {
                      $this->session->data['cartbindercombooffers'][$key]['quantity'] = $quantity;
                      } else {
                    $this->session->data['cartbindercombooffers'][$key]['quantity'] = $temp;
                    } if($quantityapply <= 0) {break;}
                    //$this->log->write("offer on product id ".$product_id);$this->log->write(print_r($this->session->data['cartbindercombooffers'],true));
                 }}}if($i){$this->session->data['cartbindercombooffers_pages'][] = $destination['sales_offer_id'];}}}}

      public function salesonecombo1b(){if(isset($this->session->data['slscmdprdts']) && !empty($this->session->data['slscmdprdts'])){
        //$this->log->write("wholesaleconditionexists");$this->log->write(print_r($this->session->data['slscmdprdts'],true));
          $query = $this->db->query("SELECT  * FROM " . DB_PREFIX . "cartbindercombo1b_setting WHERE status = '1'");foreach($query->rows as $step => $destination) {
$query = "SELECT language_id ,title as name FROM ".DB_PREFIX."cartbindercombo1b_setting_description WHERE offer_id = ".$destination['id'] ;
            $results = $this->db->query($query)->rows;
        
            $values = array();
            foreach($results as $result)
            { 
                $values[$result['language_id']] = $result['name'];
            }
            $destination['name'] = $values;
$primaryarray = explode(",",$destination['primarypids']);$secondaryarray = explode(",",$destination['secondarycids']);
            //$this->log->write("primaryarray");$this->log->write(print_r($primaryarray,true));$this->log->write("secondaryarray");$this->log->write(print_r($secondaryarray,true));
            $totalprimaryquantity  = array();
            if($this->checkCg($destination['cids'])) {continue;}
            foreach($primaryarray as $key => $value){
            if(!array_key_exists($value,$this->session->data['slscmdprdts']) && !$destination['anyorall']) {
               //$this->log->write("break from primary in 1b");
               continue 2;
              } else if(array_key_exists($value,$this->session->data['slscmdprdts'])) {$totalprimaryquantity[] = $this->session->data['slscmdprdts'][$value];}
            }
            if($destination['anyorall']) {$totalprimaryquantity = array_sum($totalprimaryquantity);} else {$totalprimaryquantity = min($totalprimaryquantity);}$commonseocndaryquantity = $totalprimaryquantity;
            //$this->log->write("common quantity".$commonseocndaryquantity);

            $netquantity = floor(($commonseocndaryquantity * $destination['secondaryquant'])/$destination['primaryquant']);
            //$netquantity = $commonseocndaryquantity;
            //$this->log->write("net quantity".$netquantity);
            $i = 0;if($netquantity > 0) {$i = 1;}
            $version = str_replace(".","",VERSION);$spprtsimproopt = array();$temp  = $netquantity;$quantityapply = $temp;
            if($version > 2100) {$cart_query = $this->db->query("SELECT c.cart_id,c.quantity,c.product_id FROM " . DB_PREFIX . "cart c LEFT JOIN " . DB_PREFIX . "product p ON (p.product_id = c.product_id) WHERE c.customer_id = '" . (int)$this->customer->getId() . "' AND c.session_id = '" . $this->db->escape($this->session->getId()) . "' ORDER BY p.price ASC");
              foreach ($cart_query->rows as $cart) {
                if(!isset($this->session->data['cartbindercombooffers'][$cart['cart_id']])) {
                    $product_id =   $cart['product_id'];
                    $checkcondition = $this->checkcondition2($product_id,$secondaryarray);
                    if($checkcondition && ($quantityapply > 0)) {
                    $i = 0;$this->session->data['cartbindercombooffers_offerapplied'][] = $destination['sales_offer_id'];
                    $temp = $quantityapply;
                    $quantityapply = $quantityapply - $cart['quantity'];
                    //$this->log->write("Quantity apply : ".$quantityapply."Temp : ".$temp);
                    $this->session->data['cartbindercombooffers'][$cart['cart_id']] = $destination;
                    if($quantityapply >= 0) {$this->session->data['cartbindercombooffers'][$cart['cart_id']]['quantity'] = $cart['quantity'];} else {$this->session->data['cartbindercombooffers'][$cart['cart_id']]['quantity'] = $temp;}
                    //$this->log->write("offer on product id ".$product_id);$this->log->write(print_r($this->session->data['cartbindercombooffers'], true));
                    if($quantityapply <= 0) {break;}
                  }
                }
              }
            } else {
              foreach ($this->session->data['cart'] as $key => $quantity) {
              if(!isset($this->session->data['cartbindercombooffers'][$key])) {
               $product = unserialize(base64_decode($key));$product_id = $product['product_id'];$checkcondition = $this->checkcondition2($product_id,$secondaryarray);if($checkcondition && ($quantityapply > 0) ) {
                $temp = $quantityapply;$quantityapply = $quantityapply - $quantity;$i = 0;
                $this->session->data['cartbindercombooffers_offerapplied'][] = $destination['sales_offer_id'];
               // $this->log->write("Quantity apply : ".$quantityapply."Temp : ".$temp);
                $this->session->data['cartbindercombooffers'][$key] =  $destination;if($quantityapply >= 0) {$this->session->data['cartbindercombooffers'][$key]['quantity'] = $quantity;} else {$this->session->data['cartbindercombooffers'][$key]['quantity'] = $temp;}
                //$this->log->write("offer on product id ".$product_id);$this->log->write(print_r($this->session->data['cartbindercombooffers'],true));
                if($quantityapply <= 0) {break;}
              }  
    }}}if($i){$this->session->data['cartbindercombooffers_pages'][] = $destination['sales_offer_id'];}}}}

           

      public function salesonecombo1c(){if(isset($this->session->data['slscmdprdts']) && !empty($this->session->data['slscmdprdts'])){
        //$this->log->write("wholesaleconditionexists");$this->log->write(print_r($this->session->data['slscmdprdts'],true));
          $query = $this->db->query("SELECT  * FROM " . DB_PREFIX . "cartbindercombo1c_setting WHERE status = '1'");foreach($query->rows as $step => $destination) {
$query = "SELECT language_id ,title as name FROM ".DB_PREFIX."cartbindercombo1c_setting_description WHERE offer_id = ".$destination['id'] ;
            $results = $this->db->query($query)->rows;
        
            $values = array();
            foreach($results as $result)
            { 
                $values[$result['language_id']] = $result['name'];
            }
            $destination['name'] = $values;
$primaryarray = $destination['primarypids'];
          if($this->checkCg($destination['cids'])) {continue;}
          if(!array_key_exists($primaryarray,$this->session->data['slscmdprdts'])) {
            continue;
          }

           $cart_query = $this->db->query("SELECT `option`,`cart_id`,`quantity` FROM " . DB_PREFIX . "cart WHERE customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "' AND product_id  = '".$primaryarray."'");
           $commonseocndaryquantity = 0;
           foreach($cart_query->rows as $key => $value) {
              $bit = 0;
              $customerselectedoptions = json_decode($value['option'],true);
              $optionset = json_decode($destination['optionidarray'],true);
               if(is_array($customerselectedoptions) && empty($customerselectedoptions)) {
                continue;
              }
              if(is_array($optionset) && empty($optionset)) {
                continue;
              }
              //$this->log->write(print_r($optionset,true));$this->log->write(print_r($customerselectedoptions,true));
              foreach($optionset as $optionkey => $optionvalue) {
                if($destination['anyorall']) {
                  //check if key exist
                  if(array_key_exists($optionkey,$customerselectedoptions)) {

                    //check if value exist
                    if(is_array($optionvalue)) {
                      foreach($optionvalue as $optionkey1 => $optionvalue1) {
                        if(in_array($optionvalue1,$customerselectedoptions[$optionkey])) {
                          $bit = 1;
                        }
                      }
                    } else {
                      if($customerselectedoptions[$optionkey] == $optionvalue) {
                        $bit = 1;
                      }
                    }
                  }
                } else {
                  $bit = 1;
                  //check if key exist
                  if(!array_key_exists($optionkey,$customerselectedoptions)) {
                    $bit = 0;
                     break;
                  }


                  //check if value exist
                  if(is_array($optionvalue)) {
                    foreach($optionvalue as $optionkey1 => $optionvalue1) {
                      if(!in_array($optionvalue1,$customerselectedoptions[$optionkey])) {
                        $bit = 0;
                        break;
                      }
                    }
                  } else {
                    if($customerselectedoptions[$optionkey] != $optionvalue) {
                      $bit = 0;
                      break;
                    }
                  }
                } 
              }
              //$this->log->write("bit value".$bit);
              if($bit) {
                 $commonseocndaryquantity += $value['quantity'];
                $secondarycartids[] = $value['cart_id'];
              }
            }
            if($commonseocndaryquantity > $destination['primaryquant']) {
              //$netquantity = floor($commonseocndaryquantity/($destination['primaryquant']+$destination['secondaryquant']));
              $netquantity = $this->xnfrmla($commonseocndaryquantity,$destination['primaryquant'],$destination['secondaryquant']);
            } else {
              break;
            }
            if(empty($secondarycartids) || $netquantity == 0) {
              break;
            }
            //$this->log->write("netquantity value".$netquantity);
            $i = 0;if($netquantity > 0) {$i = 1;}
            $version = str_replace(".","",VERSION);$spprtsimproopt = array();$temp  = $netquantity;$quantityapply = $temp;
            if($version > 2100) {
              $cart_query = $this->db->query("SELECT  c.cart_id,c.quantity,c.product_id FROM " . DB_PREFIX . "cart c LEFT JOIN " . DB_PREFIX . "product p ON (p.product_id = c.product_id) WHERE c.customer_id = '" . (int)$this->customer->getId() . "' AND c.session_id = '" . $this->db->escape($this->session->getId()) . "' AND c.product_id  = '".$primaryarray."' ORDER BY p.price ASC");
              foreach ($cart_query->rows as $cart) {
               if(!isset($this->session->data['cartbindercombooffers'][$cart['cart_id']])) {
                $product_id = $cart['product_id'];
                if(in_array($cart['cart_id'],$secondarycartids) && ($quantityapply > 0)) {
                 $temp = $quantityapply;$quantityapply = $quantityapply - $cart['quantity'];
                 $i = 0; $this->session->data['cartbindercombooffers_offerapplied'][] = $destination['sales_offer_id'];
                 // $this->log->write("Quantity apply : ".$quantityapply."Temp : ".$temp);
                  $this->session->data['cartbindercombooffers'][$cart['cart_id']] = $destination;if($quantityapply >= 0) {$this->session->data['cartbindercombooffers'][$cart['cart_id']]['quantity'] = $cart['quantity'];} else {$this->session->data['cartbindercombooffers'][$cart['cart_id']]['quantity'] = $temp;} if($quantityapply <= 0) {break;}
                  //$this->log->write("offer on product id ".$product_id);$this->log->write(print_r($this->session->data['cartbindercombooffers'],true));
                  }
                }
              }
            } else {
              foreach ($this->session->data['cart'] as $key => $quantity) {
                if(!isset($this->session->data['cartbindercombooffers'][$key])) {
                  $product = unserialize(base64_decode($key));$product_id = $product['product_id'];
                  if(in_array($product_id,$secondaryarray) && ($quantityapply > 0)) {
                    $temp = $quantityapply;$quantityapply = $quantityapply - $quantity;
                    $i = 0;
                     $this->session->data['cartbindercombooffers_offerapplied'][] = $destination['sales_offer_id'];
                    //$this->log->write("Quantity apply : ".$quantityapply."Temp : ".$temp);
                    $this->session->data['cartbindercombooffers'][$key] =  $destination;
                    if($quantityapply >= 0) {
                      $this->session->data['cartbindercombooffers'][$key]['quantity'] = $quantity;
                      } else {
                    $this->session->data['cartbindercombooffers'][$key]['quantity'] = $temp;
                    } if($quantityapply <= 0) {break;}
                    //$this->log->write("offer on product id ".$product_id);$this->log->write(print_r($this->session->data['cartbindercombooffers'],true));
                 }}}}if($i){$this->session->data['cartbindercombooffers_pages'][] = $destination['sales_offer_id'];}}}}

      public function salesonecombo1(){if(isset($this->session->data['slscmdprdts']) && !empty($this->session->data['slscmdprdts'])){
        //$this->log->write("wholesaleconditionexists");$this->log->write(print_r($this->session->data['slscmdprdts'],true));
          $query = $this->db->query("SELECT  * FROM " . DB_PREFIX . "cartbindercombo1_setting WHERE status = '1'");foreach($query->rows as $step => $destination) {
$query = "SELECT language_id ,title as name FROM ".DB_PREFIX."cartbindercombo1_setting_description WHERE offer_id = ".$destination['id'] ;
            $results = $this->db->query($query)->rows;
        
            $values = array();
            foreach($results as $result)
            { 
                $values[$result['language_id']] = $result['name'];
            }
            $destination['name'] = $values;
$primaryarray = explode(",",$destination['primarypids']);$secondaryarray = explode(",",$destination['secondarypids']);
           //$this->log->write("primaryarray");$this->log->write(print_r($primaryarray,true));$this->log->write("secondaryarray");$this->log->write(print_r($secondaryarray,true));
            if($this->checkCg($destination['cids'])) {continue;}
            foreach($primaryarray as $key => $value){if(!array_key_exists($value,$this->session->data['slscmdprdts'])) {
               //$this->log->write("break from primary");
               continue 2;
            }}
            //$this->log->write("offer should come");
            $totalprimaryquantity = array();foreach($primaryarray as $pids) {$totalprimaryquantity[] = $this->session->data['slscmdprdts'][$pids];}$commonseocndaryquantity = min($totalprimaryquantity);
            //$this->log->write("common quantity".$commonseocndaryquantity);
            $netquantity = floor(($commonseocndaryquantity * $destination['secondaryquant'])/$destination['primaryquant']);
            //$netquantity = $commonseocndaryquantity;
            //$this->log->write("net quantity".$netquantity);
            $i = 0;if($netquantity > 0) {$i = 1;}
            $version = str_replace(".","",VERSION);$spprtsimproopt = array();$temp  = $netquantity;$quantityapply = $temp;
            if($version > 2100) {
              $cart_query = $this->db->query("SELECT c.cart_id,c.product_id,c.quantity FROM " . DB_PREFIX . "cart c LEFT JOIN " . DB_PREFIX . "product p ON (p.product_id = c.product_id) WHERE c.customer_id = '" . (int)$this->customer->getId() . "' AND c.session_id = '" . $this->db->escape($this->session->getId()) . "' ORDER BY p.price ASC");
              foreach ($cart_query->rows as $cart) {
               if(!isset($this->session->data['cartbindercombooffers'][$cart['cart_id']])) {
                $product_id = $cart['product_id'];
                if(in_array($product_id,$secondaryarray) && ($quantityapply > 0)) {
                 $temp = $quantityapply;$quantityapply = $quantityapply - $cart['quantity'];
                 $i = 0; $this->session->data['cartbindercombooffers_offerapplied'][] = $destination['sales_offer_id'];
                 // $this->log->write("Quantity apply : ".$quantityapply."Temp : ".$temp);
                  $this->session->data['cartbindercombooffers'][$cart['cart_id']] = $destination;if($quantityapply >= 0) {$this->session->data['cartbindercombooffers'][$cart['cart_id']]['quantity'] = $cart['quantity'];} else {$this->session->data['cartbindercombooffers'][$cart['cart_id']]['quantity'] = $temp;} if($quantityapply <= 0) {break;}
                  //$this->log->write("offer on product id ".$product_id);$this->log->write(print_r($this->session->data['cartbindercombooffers'],true));
                  }
                }
              }
            } else {
              foreach ($this->session->data['cart'] as $key => $quantity) {
                if(!isset($this->session->data['cartbindercombooffers'][$key])) {
                  $product = unserialize(base64_decode($key));$product_id = $product['product_id'];
                  if(in_array($product_id,$secondaryarray) && ($quantityapply > 0)) {
                    $temp = $quantityapply;$quantityapply = $quantityapply - $quantity;
                    $i = 0;
                     $this->session->data['cartbindercombooffers_offerapplied'][] = $destination['sales_offer_id'];
                    //$this->log->write("Quantity apply : ".$quantityapply."Temp : ".$temp);
                    $this->session->data['cartbindercombooffers'][$key] =  $destination;
                    if($quantityapply >= 0) {
                      $this->session->data['cartbindercombooffers'][$key]['quantity'] = $quantity;
                      } else {
                    $this->session->data['cartbindercombooffers'][$key]['quantity'] = $temp;
                    } if($quantityapply <= 0) {break;}
                    //$this->log->write("offer on product id ".$product_id);$this->log->write(print_r($this->session->data['cartbindercombooffers'],true));
                 }}}}if($i){$this->session->data['cartbindercombooffers_pages'][] = $destination['sales_offer_id'];}}}}

      public function salesonecombo2a(){
      if(isset($this->session->data['slscmdctgs']) && !empty($this->session->data['slscmdctgs']) && isset($this->session->data['slscmdctgscids']) && !empty($this->session->data['slscmdctgscids'])){
          //$this->log->write("categoryconditionexist"); $this->log->write(print_r($this->session->data['slscmdctgs'],true));$this->log->write(print_r($this->session->data['slscmdprdts'],true));
          $version = str_replace(".","",VERSION);
          $query = $this->db->query("SELECT  * FROM " . DB_PREFIX . "cartbindercombo2a_setting WHERE status = '1' Order by primaryquant Desc");
          foreach($query->rows as $step => $destination) {
            $isOfferApplyed = false;    
$query = "SELECT language_id ,title as name FROM ".DB_PREFIX."cartbindercombo2a_setting_description WHERE offer_id = ".$destination['id'] ;
            $results = $this->db->query($query)->rows;
        
            $values = array();
            foreach($results as $result)
            { 
                $values[$result['language_id']] = $result['name'];
            }
            $destination['name'] = $values;
          $primarycarray = array();$primarycrtarray = array();
          $primaryarray = explode(",",$destination['primarycids']);
          //$this->log->write("primaryarray");$this->log->write(print_r($primaryarray,true));
          //$this->log->write("primaryarray");$this->log->write(print_r($primaryarray,true));
          //$this->log->write("category with quantity");$this->log->write(print_r($this->session->data['slscmdctgscids'],true));
          if($this->checkCg($destination['cids'])) {continue;}
            if(!$destination['anyorall']) {
            foreach($primaryarray as $key => $value){if(!array_key_exists($value,$this->session->data['slscmdctgscids'])) {
               //$this->log->write("break from primary");
               continue 2;
            }}}
            if($version > 2100) {
              $cart_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cart WHERE customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "'");
                foreach ($cart_query->rows as $cart) {
                  if(isset($this->session->data['slscmdctgs'][$cart['product_id']])) {
                    foreach($this->session->data['slscmdctgs'][$cart['product_id']] as $key => $value) {
                      if(in_array($value,$primaryarray)  && isset($this->session->data['slscmdctgscids'][$value])) {
                        $primarycarray[$value] = $this->session->data['slscmdctgscids'][$value];
                        $primarycrtarray[$cart['cart_id']] = $cart['quantity'];
                      }
                    }
                  }
                }
              } else {
                foreach ($this->session->data['cart'] as $key => $quantity) {
                  $product = unserialize(base64_decode($key));
                  $product_id = $product['product_id'];
                  if(isset($this->session->data['slscmdctgs'][$product_id])) {
                    foreach($this->session->data['slscmdctgs'][$product_id] as $key1 => $value) {
                      if(in_array($value,$primaryarray)  && isset($this->session->data['slscmdctgscids'][$value])) {
                       $primarycarray[$value] = $this->session->data['slscmdctgscids'][$value];
                        $primarycrtarray[] = $key;
                      }
                    }
                  }
                }
              }
            if(!count($primarycrtarray)) { continue;}
            if($destination['anyorall']) {
              $totalprimaryquantity = array_sum($primarycrtarray);
            } else {
              $totalprimaryquantity = min($primarycrtarray);
            }
            if($totalprimaryquantity < $destination['primaryquant']) { continue; }
      //      if ($totalprimaryquantity > $destination['primaryquant'] + $destination['secondaryquant'])
      //              continue;

            $commonseocndaryquantity = $totalprimaryquantity;
            //$netquantity2 = floor($commonseocndaryquantity/($destination['primaryquant']+$destination['secondaryquant']));
            $netquantity2 = $this->xnfrmla($commonseocndaryquantity,$destination['primaryquant'],$destination['secondaryquant']);
            $i = 0;if($netquantity2 >= 0) {$i = 1;}
            //$this->log->write("net quantity".$netquantity2);
            $spprtsimproopt = array();$temp  = $netquantity2;$quantityapply = $temp;
            if($version > 2100) {$cart_query = $this->db->query("SELECT c.cart_id,c.quantity,c.product_id FROM " . DB_PREFIX . "cart c LEFT JOIN " . DB_PREFIX . "product p ON (p.product_id = c.product_id) WHERE c.customer_id = '" . (int)$this->customer->getId() . "' AND c.session_id = '" . $this->db->escape($this->session->getId()) . "' ORDER BY p.price ASC");
              foreach ($cart_query->rows as $cart) {
                if(!isset($this->session->data['cartbindercombooffers'][$cart['cart_id']]) && array_key_exists($cart['cart_id'], $primarycrtarray) && ($quantityapply > 0)) {
                    $temp = $quantityapply;$i =0;$this->session->data['cartbindercombooffers_offerapplied'][] = $destination['sales_offer_id'];
                    $quantityapply = $quantityapply - $cart['quantity'];
                    //$this->log->write("Quantity apply : ".$quantityapply."Temp : ".$temp."cart : ".$cart['quantity']);
                    $this->session->data['cartbindercombooffers'][$cart['cart_id']] = $destination;
                    $isOfferApplyed = true;
                    if($quantityapply >= 0) {$this->session->data['cartbindercombooffers'][$cart['cart_id']]['quantity'] = $cart['quantity'];} else {$this->session->data['cartbindercombooffers'][$cart['cart_id']]['quantity'] = $temp;}
                    if($quantityapply < 0) {break;}
                }
              }
            } else {
              $this->session->data['tempcart'] = array();
              $products = $this->getProducts();
              foreach ($products as $product) {
                $this->session->data['tempcart'][$product['key']] = $product['price'];
              }
              asort( $this->session->data['tempcart']);
              foreach ($this->session->data['tempcart'] as $key => $price) {
              $quantity = $this->session->data['cart'][$key];
              if(!isset($this->session->data['cartbindercombooffers'][$key]) && in_array($key, $primarycrtarray) && ($quantityapply > 0)) {
                $temp = $quantityapply;$quantityapply = $quantityapply - $quantity;$i =0;
                $this->session->data['cartbindercombooffers_offerapplied'][] = $destination['sales_offer_id'];
               // $this->log->write("Quantity apply : ".$quantityapply."Temp : ".$temp);
                $this->session->data['cartbindercombooffers'][$key] =  $destination;if($quantityapply >= 0) {$this->session->data['cartbindercombooffers'][$key]['quantity'] = $quantity;} else {$this->session->data['cartbindercombooffers'][$key]['quantity'] = $temp;}
                if($quantityapply < 0) {break;}
                }}}if($i){$this->session->data['cartbindercombooffers_pages'][] = $destination['sales_offer_id'];}
                if ($isOfferApplyed)
                   break;
            }
        }}
    

      public function salesonecombo2(){if(isset($this->session->data['slscmdctgs']) && !empty($this->session->data['slscmdctgs']) && isset($this->session->data['slscmdctgscids']) && !empty($this->session->data['slscmdctgscids'])){
          //$this->log->write("categoryconditionexist");$this->log->write(print_r($this->session->data['slscmdctgs'],true));$this->log->write(print_r($this->session->data['slscmdprdts'],true));
          $version = str_replace(".","",VERSION);
          $query = $this->db->query("SELECT  * FROM " . DB_PREFIX . "cartbindercombo2_setting WHERE status = '1'");foreach($query->rows as $step => $destination) {
$query = "SELECT language_id ,title as name FROM ".DB_PREFIX."cartbindercombo2_setting_description WHERE offer_id = ".$destination['id'] ;
            $results = $this->db->query($query)->rows;
        
            $values = array();
            foreach($results as $result)
            { 
                $values[$result['language_id']] = $result['name'];
            }
            $destination['name'] = $values;
$primarycarray = array();$primaryarray = explode(",",$destination['primarycids']);$secondaryarray = explode(",",$destination['secondarypids']);$secondarycarray = explode(",",$destination['secondarycids']);
           //$this->log->write("primaryarray");$this->log->write(print_r($primaryarray,true));$this->log->write("secondaryarray");$this->log->write(print_r($secondaryarray,true));$this->log->write("secondarycarray");$this->log->write(print_r($secondarycarray,true));
           if($this->checkCg($destination['cids'])) {continue;}
           if(!$destination['anyorall']) {
            foreach($primaryarray as $key => $value){if(!array_key_exists($value,$this->session->data['slscmdctgscids'])) {
               //$this->log->write("break from primary");
               continue 2;
            }}}
            $cart_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cart WHERE customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "'");
            foreach ($cart_query->rows as $cart) {
              if(isset($this->session->data['slscmdctgs'][$cart['product_id']])) {
                foreach($this->session->data['slscmdctgs'][$cart['product_id']] as $key => $value) {
                 if(in_array($value,$primaryarray) && isset($this->session->data['slscmdctgscids'][$value])) {
                    $primarycarray[$value] = $this->session->data['slscmdctgscids'][$value];
                  }
                }
              }
            }
            if(!count($primarycarray)) { continue;}
            if($destination['anyorall']) {
              $totalprimaryquantity = array_sum($primarycarray);
            } else {
              $totalprimaryquantity = min($primarycarray);
            }
            if($totalprimaryquantity < $destination['primaryquant']) { continue; }
            $commonseocndaryquantity = $totalprimaryquantity;
            //$netquantity2 = floor($commonseocndaryquantity/($destination['primaryquant']+$destination['secondaryquant']));$netquantity2 = $this->xnfrmla($commonseocndaryquantity,$destination['primaryquant'],$destination['secondaryquant']);
            $netquantity2 = floor(($commonseocndaryquantity * $destination['secondaryquant'])/$destination['primaryquant']);
            if($netquantity2 <= 0){ continue;}
            $i = 0;if($netquantity2 > 0) {$i = 1;}
           //$this->log->write("net quantity".$netquantity2);
            $spprtsimproopt = array();$temp  = $netquantity2;$quantityapply = $temp;
            if($version > 2100) {$cart_query = $this->db->query("SELECT c.cart_id,c.quantity,c.product_id FROM " . DB_PREFIX . "cart c LEFT JOIN " . DB_PREFIX . "product p ON (p.product_id = c.product_id) WHERE c.customer_id = '" . (int)$this->customer->getId() . "' AND c.session_id = '" . $this->db->escape($this->session->getId()) . "' ORDER BY p.price ASC");
              foreach ($cart_query->rows as $cart) {
                if(!isset($this->session->data['cartbindercombooffers'][$cart['cart_id']])) {
                    $product_id =   $cart['product_id'];
                    $checkcondition = $this->checkcondition($product_id,$secondaryarray,$secondarycarray);
                    if($checkcondition && ($quantityapply > 0)) {
                    $i = 0;$this->session->data['cartbindercombooffers_offerapplied'][] = $destination['sales_offer_id'];
                    $temp = $quantityapply;
                    $quantityapply = $quantityapply - $cart['quantity'];
                    //$this->log->write("Quantity apply : ".$quantityapply."Temp : ".$temp);
                    $this->session->data['cartbindercombooffers'][$cart['cart_id']] = $destination;
                    if($quantityapply >= 0) {$this->session->data['cartbindercombooffers'][$cart['cart_id']]['quantity'] = $cart['quantity'];} else {$this->session->data['cartbindercombooffers'][$cart['cart_id']]['quantity'] = $temp;}
                   //$this->log->write("offer on product id ".$product_id);$this->log->write(print_r($this->session->data['cartbindercombooffers'], true));
                    if($quantityapply <= 0) {break;}
                  }
                }
              }
            } else {
              foreach ($this->session->data['cart'] as $key => $quantity) {
              if(!isset($this->session->data['cartbindercombooffers'][$key])) {
               $product = unserialize(base64_decode($key));$product_id = $product['product_id'];$checkcondition = $this->checkcondition($product_id,$secondaryarray,$secondarycarray);if($checkcondition && ($quantityapply > 0) ) {
                $temp = $quantityapply;$quantityapply = $quantityapply - $quantity;$i = 0;
                $this->session->data['cartbindercombooffers_offerapplied'][] = $destination['sales_offer_id'];
               // $this->log->write("Quantity apply : ".$quantityapply."Temp : ".$temp);
                $this->session->data['cartbindercombooffers'][$key] =  $destination;if($quantityapply >= 0) {$this->session->data['cartbindercombooffers'][$key]['quantity'] = $quantity;} else {$this->session->data['cartbindercombooffers'][$key]['quantity'] = $temp;}
                //$this->log->write("offer on product id ".$product_id);$this->log->write(print_r($this->session->data['cartbindercombooffers'],true));
                if($quantityapply <= 0) {break;}
              }  
    }}}if($i){$this->session->data['cartbindercombooffers_pages'][] = $destination['sales_offer_id'];}}}}
    
	public function getWeight() {
		$weight = 0;

		foreach ($this->getProducts() as $product) {
			if ($product['shipping']) {
				$weight += $this->weight->convert($product['weight'], $product['weight_class_id'], $this->config->get('config_weight_class_id'));
			}
		}

		return $weight;
	}

	public function getSubTotal() {
		$total = 0;

		foreach ($this->getProducts() as $product) {
			$total += $product['total'];
		}

		return $total;
	}

	public function getTaxes() {
		$tax_data = array();

		foreach ($this->getProducts() as $product) {
			if ($product['tax_class_id']) {
$product['quantity'] = $product['salecombinationquantity'];
				$tax_rates = $this->tax->getRates($product['price'], $product['tax_class_id']);

				foreach ($tax_rates as $tax_rate) {
					if (!isset($tax_data[$tax_rate['tax_rate_id']])) {
						$tax_data[$tax_rate['tax_rate_id']] = ($tax_rate['amount'] * $product['quantity']);
					} else {
						$tax_data[$tax_rate['tax_rate_id']] += ($tax_rate['amount'] * $product['quantity']);
					}
				}
			}
		}

		return $tax_data;
	}

	public function getTotal() {
		$total = 0;

		foreach ($this->getProducts() as $product) {
$product['quantity'] = $product['salecombinationquantity'];
			$total += $this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity'];
		}

		return $total;
	}

	public function countProducts() {
		$product_total = 0;

		$products = $this->getProducts();

		foreach ($products as $product) {
			$product_total += $product['quantity'];
		}

		return $product_total;
	}

	public function hasProducts() {
		return count($this->getProducts());
	}

	public function hasRecurringProducts() {
		return count($this->getRecurringProducts());
	}

	public function hasStock() {
		foreach ($this->getProducts() as $product) {
			if (!$product['stock']) {
				return false;
			}
		}

		return true;
	}

	public function hasShipping() {
		foreach ($this->getProducts() as $product) {
			if ($product['shipping']) {
				return true;
			}
		}

		return false;
	}

	public function hasDownload() {
		foreach ($this->getProducts() as $product) {
			if ($product['download']) {
				return true;
			}
		}

		return false;
	}

    public function getCartId() {
        $cart_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cart WHERE api_id = '" . (isset($this->session->data['api_id']) ? (int)$this->session->data['api_id'] : 0) . "' AND customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "'");

        $cart_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cart WHERE api_id = '" . (isset($this->session->data['api_id']) ? (int)$this->session->data['api_id'] : 0) . "' AND customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "'");

        foreach ($cart_query->rows as $cart) {
            return $cart['cart_id'];
        }

    }
}
