<?php
class ModelCatalogGallery extends Model {

    public function getPictures() {
        $gallery_categorys = $this->db->query("SELECT * FROM gallery_category gc, gallery_category_description gcd "
                                ."where gc.gallery_category_id = gcd.gallery_category_id and status = '1' and gcd.language_id = '". (int)$this->config->get('config_language_id') ."' "
                                ."ORDER BY sort_order");

        $pictures = array();

        foreach ($gallery_categorys->rows as $gallery_category) {
            $gallery_category_pictures = array();

            $pics = $this->db->query("SELECT * FROM gallery_picture WHERE gallery_category_id =" . $gallery_category['gallery_category_id'] . " ORDER BY sort_order");

            foreach ($pics->rows as $pic) {
                if ($this->request->server['HTTPS']) {
                    $gallery_category_pictures[] = $this->config->get('config_ssl') . 'image/' . $pic['path'];
                } else {
                    $gallery_category_pictures[] = $this->config->get('config_url') . 'image/' . $pic['path'];
                }
            }

            $pictures[$gallery_category['title']] = $gallery_category_pictures;
        }

        return $pictures;
    }


    public function getVideos() {
        $gallery_categorys = $this->db->query("SELECT * FROM gallery_category gc, gallery_category_description gcd "
            ."where gc.gallery_category_id = gcd.gallery_category_id and status = '1' and gcd.language_id = '". (int)$this->config->get('config_language_id') ."' "
            ."ORDER BY sort_order");

        $vedios = array();

        foreach ($gallery_categorys->rows as $gallery_category) {
            $gallery_category_videos = array();

            $veds = $this->db->query("SELECT * FROM gallery_video WHERE gallery_category_id =" . $gallery_category['gallery_category_id'] . " ORDER BY sort_order");
            foreach ($veds->rows as $ved) {

                $gallery_category_videos[] = $ved['link'];
            }

            $vedios[$gallery_category['title']] = $gallery_category_videos;
        }

        return $vedios;
    }
}