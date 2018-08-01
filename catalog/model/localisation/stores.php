<?php
class ModelLocalisationStores extends Model {


    public function getLocations() {
        if($this->config->get('config_language_id') == 1) {
            $query = $this->db->query("SELECT * FROM stores where language_id = '" . (int)$this->config->get('config_language_id') . "' order by type DESC");
        }
        else {
            $query = $this->db->query("SELECT * FROM stores where language_id = '" . (int)$this->config->get('config_language_id') . "' order by type ASC");

        }

        $locations = array();

        $location_type = '';

        $locations_to_type = array();

        foreach ($query->rows as $location) {

            $new_location_type = $location['type'];

            if($location_type != $new_location_type) {
                $locations[$location_type] = $locations_to_type;
                $locations_to_type = array();
                $location_type = $new_location_type;
                $locations_to_type[] = $location;
                continue;
            }
            $locations_to_type[] = $location;
        }

        $locations[$location_type] = $locations_to_type;

        array_shift($locations);

        return $locations;
    }
}