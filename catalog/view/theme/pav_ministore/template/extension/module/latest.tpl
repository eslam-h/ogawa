<?php
	$load = $this->registry->get("load");
	$language = $load->language("module/themecontrol");
	$text_sale = $language['text_sale'];
	$quick_view = $language['quick_view'];
?>
<div class="panel panel-default">
<?php   
  $config = $this->registry->get('config'); 
 require( DIR_TEMPLATE.$config->get('theme_default_directory')."/template/common/products_module.tpl" ); ?>  
</div>
