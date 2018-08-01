<?php
class ModelCatalogGallery extends Model {
    public function getGalleyCategory($data = array()) {
        if ($data) {
            $sql = "SELECT * FROM gallery_category gc ";

            $sort_data = array(
                'gc.title',
                'gc.sort_order'
            );

            if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
                $sql .= " ORDER BY " . $data['sort'];
            } else {
                $sql .= " ORDER BY gc.title";
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
        } else {

            $gallery_category_data = $this->cache->get('gallery_category_data.' . (int)$this->config->get('config_language_id'));

            if (!$gallery_category_data) {
                $query = $this->db->query("SELECT * FROM gallery_category gc ORDER BY gc.title");

                $gallery_category_data = $query->rows;

                $this->cache->set('gallery_category_data.' . (int)$this->config->get('config_language_id'), $gallery_category_data);
            }

            return $gallery_category_data;
        }
    }

    public function getTotalGalleryCategorys() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM gallery_category");

        return $query->row['total'];
    }

    public function getGalleryImages($gallery_category_id) {
        $query = $this->db->query("SELECT * FROM gallery_picture WHERE gallery_category_id = '" . (int)$gallery_category_id . "' ORDER BY sort_order ASC");

        return $query->rows;
    }

    public function getGalleryVideos($gallery_category_id) {
        $query = $this->db->query("SELECT * FROM gallery_video WHERE gallery_category_id = '" . (int)$gallery_category_id . "' ORDER BY sort_order ASC");

        return $query->rows;
    }

    public function editGallery($gallery_category_id, $data) {


        $this->db->query("DELETE FROM gallery_picture WHERE gallery_category_id = '" . (int)$gallery_category_id . "'");

        if (isset($data['gallery_image'])) {
            foreach ($data['gallery_image'] as $gallery_image) {
                $this->db->query("INSERT INTO gallery_picture SET gallery_category_id = '" . (int)$gallery_category_id . "', path = '" . $this->db->escape($gallery_image['image']) . "', sort_order = '" . (int)$gallery_image['sort_order'] . "'");
            }
        }

        $this->db->query("DELETE FROM gallery_video WHERE gallery_category_id = '" . (int)$gallery_category_id . "'");

        if (isset($data['gallery_video'])) {
            foreach ($data['gallery_video'] as $gallery_video) {
                $this->db->query("INSERT INTO gallery_video SET gallery_category_id = '" . (int)$gallery_category_id . "', link = '" . $this->db->escape($gallery_video['video']) . "', sort_order = '" . (int)$gallery_video['sort_order'] . "'");
            }
        }
    }

    public function getGalleryCategorys($data = array()) {
        if ($data) {
            $sql = "SELECT * FROM gallery_category gc LEFT JOIN gallery_category_description gcd ON (gc.gallery_category_id = gcd.gallery_category_id) WHERE gcd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

            $sort_data = array(
                'gcd.title',
                'gc.sort_order'
            );

            if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
                $sql .= " ORDER BY " . $data['sort'];
            } else {
                $sql .= " ORDER BY gcd.title";
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
        } else {
            $gallery_category_data = $this->cache->get('gallery_category.' . $this->config->get('config_language_id'));

            if (!$gallery_category_data) {
                $query = $this->db->query("SELECT * FROM gallery_category gc LEFT JOIN gallery_category_description gcd ON (gc.gallery_category_id = gcd.gallery_category_id) WHERE gcd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY gcd.title");

                $gallery_category_data = $query->rows;

                $this->cache->set('gallery_category.' . $this->config->get('config_language_id'), $gallery_category_data);
            }

            return $gallery_category_data;
        }
    }

    public function addCategory($data) {
        $this->db->query("INSERT INTO gallery_category SET sort_order = '" . (int)$this->request->post['sort_order'] . "', status = '" . (int)$data['status'] . "'");

        $gallery_category_id = $this->db->getLastId();

        foreach ($data['gallery_category_description'] as $language_id => $value) {
            $this->db->query("INSERT INTO gallery_category_description SET gallery_category_id = '" . (int)$gallery_category_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "'");
        }

        $this->cache->delete('gallery_category');
    }

    public function editCategory($gallery_category_id, $data) {
        $this->db->query("UPDATE gallery_category SET sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "' WHERE gallery_category_id = '" . (int)$gallery_category_id . "'");

        $this->db->query("DELETE FROM gallery_category_description WHERE gallery_category_id = '" . (int)$gallery_category_id . "'");

        foreach ($data['gallery_category_description'] as $language_id => $value) {
            $this->db->query("INSERT INTO gallery_category_description SET gallery_category_id = '" . (int)$gallery_category_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "'");
        }

        $this->cache->delete('gallery_category');
    }

    public function deleteCategory($gallery_category_id) {
        $this->db->query("DELETE FROM gallery_category WHERE gallery_category_id = '" . (int)$gallery_category_id . "'");
        $this->db->query("DELETE FROM gallery_category_description WHERE gallery_category_id = '" . (int)$gallery_category_id . "'");

        $this->cache->delete('gallery_category');
    }

    public function getCategory($gallery_category_id) {
        $query = $this->db->query("SELECT DISTINCT * FROM gallery_category WHERE gallery_category_id = '" . (int)$gallery_category_id . "'");

        return $query->row;
    }

    public function getGalleryCategoryDescriptions($gallery_category_id) {
        $gallery_category_description_data = array();

        $query = $this->db->query("SELECT * FROM gallery_category_description WHERE gallery_category_id = '" . (int)$gallery_category_id . "'");

        foreach ($query->rows as $result) {
            $gallery_category_description_data[$result['language_id']] = array(
                'title'       => $result['title']
            );
        }

        return $gallery_category_description_data;
    }

    public function geTotaltGallerysByGalleryCategoryId($gallery_category_id) {

        $total = 0;

        $query = $this->db->query("SELECT COUNT(*) AS total FROM gallery_picture WHERE gallery_category_id = '" . (int)$gallery_category_id . "'");

        $total += $query->row['total'];

        $query = $this->db->query("SELECT COUNT(*) AS total FROM gallery_video WHERE gallery_category_id = '" . (int)$gallery_category_id . "'");

        $total += $query->row['total'];

        return $total;
    }
}