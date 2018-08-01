<?php

	$config = $sconfig;

  $themeConfig = (array)$config->get('themecontrol');
  $productConfig = array(
      'product_enablezoom'         => 1,
      'product_zoommode'           => 'basic',
      'product_zoomeasing'         => 1,
      'product_zoomlensshape'      => "round",
      'product_zoomlenssize'       => "150",
      'product_zoomgallery'        => 0,
      'enable_product_customtab'   => 0,
      'product_customtab_name'     => '',
      'product_customtab_content'  => '',
      'product_related_column'     => 0,
    );
    $listingConfig = array(
      'category_pzoom'                    => 1,
      'quickview'                                 => 0,
      'show_swap_image'                         => 0,
      'catalog_mode'                => 1,
      'layout_pinfo' => 'default'
    );
    $listingConfig          = array_merge($listingConfig, $themeConfig );
    $categoryPzoom            = $listingConfig['category_pzoom'];
    $quickview                = $listingConfig['quickview'];
    $swapimg                  = ($listingConfig['show_swap_image'])?'swap':'';
    $productConfig                = array_merge( $productConfig, $themeConfig );
    $languageID               = $config->get('config_language_id');

    $layout_pinfo = $listingConfig['layout_pinfo'];
    $layout_detail = $listingConfig['product_detail'];

?>
<?php echo $header; ?>
<div class="breadcrumb">
  <div class="container">
    <h1><?php echo $heading_title; ?></h1>
    <ul>
      <?php foreach ($breadcrumbs as $breadcrumb) { ?>
      <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
      <?php } ?>
    </ul>
  </div>
</div>
<div class="container">
  <div class="row"><?php echo $column_left; ?>
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-md-6'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-md-9'; ?>
    <?php } else { ?>
    <?php $class = 'col-md-12'; ?>
    <?php } ?>
    <div id="content" class="<?php echo $class; ?>"><?php echo $content_top; ?>
      <div class="product-info">
        <div class="row">
          <?php if ($column_left || $column_right) { ?>
          <?php $class = 'col-md-6 col-sm-6'; ?>
          <?php } else { ?>
          <?php $class = 'col-md-6'; ?>
          <?php } ?>

          <?php require( ThemeControlHelper::getLayoutPath( 'product/preview/custom.tpl' ) );  ?>
          <?php if ($column_left || $column_right) { ?>
          <?php $class = 'col-md-6 col-sm-6'; ?>
          <?php } else { ?>
          <?php $class = 'col-md-6'; ?>
          <?php } ?>
          <div class="<?php echo $class; ?> col-sm-6 space-padding-lr-7">

            <h1 class="name"><?php echo $heading_title; ?></h1>

            <?php if ($review_status) { ?>
            <div class="rating">
              <p>
                <?php for ($i = 1; $i <= 5; $i++) { ?>
                <?php if ($rating < $i) { ?>
                <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-1x"></i></span>
                <?php } else { ?>
                <span class="fa fa-stack"><i class="fa fa-star fa-stack-1x"></i><i class="fa fa-star-o fa-stack-1x"></i></span>
                <?php } ?>
                <?php } ?>
                <a href="" onclick="$('a[href=\'#tab-review\']').trigger('click'); return false;"><?php echo $reviews; ?></a>
                </p>

            </div>
            <?php } ?>

             <?php if ($price) { ?>
              <div class="price detail">
                  <ul class="list-unstyled">
                      <?php if (!$special) { ?>
                          <li>
                              <span class="price-original"> <?php echo $price; ?> </span>
                          </li>
                      <?php } else { ?>

                          <li> <span class="price-new"> <?php echo $special; ?> </span> <span class="price-old"><?php echo $price; ?></span> </li>
                      <?php } ?>
                  </ul>
              </div>
          <?php } ?>

          <ul class="list-unstyled">
              <?php if ($tax) { ?>
                  <li><?php echo $text_tax; ?> <?php echo $tax; ?></li>
              <?php } ?>

              <?php if ($discounts) { ?>
                  <li>
                  </li>
                  <?php foreach ($discounts as $discount) { ?>
                      <li><?php echo $discount['quantity']; ?><?php echo $text_discount; ?><?php echo $discount['price']; ?></li>
                  <?php } ?>
              <?php } ?>
          </ul>


          <ul class="list-unstyled">
              <?php if ($stock) { ?>
              <li><span class="check-box text-primary"><i class="zmdi zmdi-check zmdi-hc-fw"></i></span><?php echo $text_stock; ?><?php echo $stock; ?></li>
              <?php } ?>
              <?php if(false){ //if ($manufacturer) { ?>
                  <li><span class="check-box text-primary"><i class="zmdi zmdi-check zmdi-hc-fw"></i></span><?php echo $text_manufacturer; ?> <a href="<?php echo $manufacturers; ?>"><?php echo $manufacturer; ?></a></li>
              <?php } ?>
              <li><span class="check-box text-primary"><i class="zmdi zmdi-check zmdi-hc-fw"></i></span><?php echo $text_model; ?> <?php echo $model; ?></li>
              <?php if ($reward) { ?>
                  <li><span class="check-box text-primary"><i class="zmdi zmdi-check zmdi-hc-fw"></i></span><?php echo $text_reward; ?> <?php echo $reward; ?></li>
              <?php } ?>
              <?php if ($points) { ?>
                  <li><span class="check-box text-primary"><i class="zmdi zmdi-check zmdi-hc-fw"></i></span><?php echo $text_points; ?> <?php echo $points; ?></li>
              <?php } ?>
          </ul>


            <div id="product">
              <?php if ($options) { ?>
              <hr>
              <h3><?php //echo $text_option; ?></h3>
              <?php foreach ($options as $option) { ?>
              <?php if ($option['type'] == 'select') { ?>
              <div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?>">
                <label class="control-label" for="input-option<?php echo $option['product_option_id']; ?>"><?php echo $option['name']; ?></label>
                <select name="option[<?php echo $option['product_option_id']; ?>]" id="input-option<?php echo $option['product_option_id']; ?>" class="form-control">
                  <option value=""><?php echo $text_select; ?></option>
                  <?php foreach ($option['product_option_value'] as $option_value) { ?>
                  <option value="<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
                  <?php if ($option_value['price']) { ?>
                  (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
                  <?php } ?>
                  </option>
                  <?php } ?>
                </select>
              </div>
              <?php } ?>
              <?php if ($option['type'] == 'radio') { ?>
              <div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?> form-group-v2">
                <label class="control-label"><?php echo $option['name']; ?></label>
                <div id="input-option<?php echo $option['product_option_id']; ?>">

                    <form id="myForm">
                        <?php $id = 0; $style_check = 0;  foreach ($option['product_option_value'] as $option_value) { ?>
                        <div class="radio"  style="display: inline">
                            <label " >
                              <?php if($id == 0){ ?>
                            <script type="text/javascript">var maxx = <?php echo $option_value['option_value_quantity']; ?></script>
                              <input id="<?php echo $id++; ?>" type="radio" hidden checked about="<?php echo $option_value['english_name']; ?>"  name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option_value['product_option_value_id']; ?>" />
                              <?php } else { ?>
                              <input id="<?php echo $id++; ?>" type="radio"  hidden about="<?php echo $option_value['english_name']; ?>" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option_value['product_option_value_id']; ?>" />
                              <?php } ?>
                              <img <?php if($style_check == 0){ $style_check = 1; ?> style="border-style: solid; border-width: 2px; border-color: #00bdec" <?php } ?> about = "<?php echo $option_value['option_value_quantity']; ?>" id="<?php echo $id++; ?>" src="<?php echo $option_value['image']; ?>" alt="<?php echo $option_value['name'] . ($option_value['price'] ? ' ' . $option_value['price_prefix'] . $option_value['price'] : ''); ?>" class="img-thumbnail styledBorder" />
                            </label>
                        </div>
                        <?php } ?>
                    </form>
                </div>
              </div>
              <?php } ?>
              <?php if ($option['type'] == 'checkbox') { ?>
              <div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?> form-group-v2">
                <label class="control-label"><?php echo $option['name']; ?></label>
                <div id="input-option<?php echo $option['product_option_id']; ?>">
                  <?php foreach ($option['product_option_value'] as $option_value) { ?>
                  <div class="checkbox">
                    <label>
                      <input type="checkbox" name="option[<?php echo $option['product_option_id']; ?>][]" value="<?php echo $option_value['product_option_value_id']; ?>" />
                      <?php echo $option_value['name']; ?>
                      <?php if ($option_value['price']) { ?>
                      (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
                      <?php } ?>
                    </label>
                  </div>
                  <?php } ?>
                </div>
              </div>
              <?php } ?>
              <?php if ($option['type'] == 'image') { ?>
              <div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?> form-group-v2">
                <label class="control-label"><?php echo $option['name']; ?></label>
                <div id="input-option<?php echo $option['product_option_id']; ?>">
                  <?php foreach ($option['product_option_value'] as $option_value) { ?>
                  <div class="radio">
                    <label>
                      <input type="radio" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option_value['product_option_value_id']; ?>" />
                      <img src="<?php echo $option_value['image']; ?>" alt="<?php echo $option_value['name'] . ($option_value['price'] ? ' ' . $option_value['price_prefix'] . $option_value['price'] : ''); ?>" class="img-thumbnail" /> <?php echo $option_value['name']; ?>
                      <?php if ($option_value['price']) { ?>
                      (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
                      <?php } ?>
                    </label>
                  </div>
                  <?php } ?>
                </div>
              </div>
              <?php } ?>
              <?php if ($option['type'] == 'text') { ?>
              <div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?>">
                <label class="control-label" for="input-option<?php echo $option['product_option_id']; ?>"><?php echo $option['name']; ?></label>
                <input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['value']; ?>" placeholder="<?php echo $option['name']; ?>" id="input-option<?php echo $option['product_option_id']; ?>" class="form-control" />
              </div>
              <?php } ?>
              <?php if ($option['type'] == 'textarea') { ?>
              <div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?>">
                <label class="control-label" for="input-option<?php echo $option['product_option_id']; ?>"><?php echo $option['name']; ?></label>
                <textarea name="option[<?php echo $option['product_option_id']; ?>]" rows="5" placeholder="<?php echo $option['name']; ?>" id="input-option<?php echo $option['product_option_id']; ?>" class="form-control"><?php echo $option['value']; ?></textarea>
              </div>
              <?php } ?>
              <?php if ($option['type'] == 'file') { ?>
              <div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?>">
                <label class="control-label"><?php echo $option['name']; ?></label>
                <button type="button" id="button-upload<?php echo $option['product_option_id']; ?>" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-default btn-block"><i class="fa fa-upload"></i> <?php echo $button_upload; ?></button>
                <input type="hidden" name="option[<?php echo $option['product_option_id']; ?>]" value="" id="input-option<?php echo $option['product_option_id']; ?>" />
              </div>
              <?php } ?>
              <?php if ($option['type'] == 'date') { ?>
              <div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?>">
                <label class="control-label" for="input-option<?php echo $option['product_option_id']; ?>"><?php echo $option['name']; ?></label>
                <div class="input-group date">
                  <input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['value']; ?>" data-date-format="YYYY-MM-DD" id="input-option<?php echo $option['product_option_id']; ?>" class="form-control" />
                  <span class="input-group-btn">
                  <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
              <?php } ?>
              <?php if ($option['type'] == 'datetime') { ?>
              <div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?>">
                <label class="control-label" for="input-option<?php echo $option['product_option_id']; ?>"><?php echo $option['name']; ?></label>
                <div class="input-group datetime">
                  <input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['value']; ?>" data-date-format="YYYY-MM-DD HH:mm" id="input-option<?php echo $option['product_option_id']; ?>" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
              <?php } ?>
              <?php if ($option['type'] == 'time') { ?>
              <div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?>">
                <label class="control-label" for="input-option<?php echo $option['product_option_id']; ?>"><?php echo $option['name']; ?></label>
                <div class="input-group time">
                  <input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['value']; ?>" data-date-format="HH:mm" id="input-option<?php echo $option['product_option_id']; ?>" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
              </div>
              <?php } ?>
              <?php } ?>
              <?php } ?>
              <?php if ($recurrings) { ?>
              <hr>
              <h3><?php echo $text_payment_recurring ?></h3>
              <div class="form-group required">
                <select name="recurring_id" class="form-control">
                  <option value=""><?php echo $text_select; ?></option>
                  <?php foreach ($recurrings as $recurring) { ?>
                  <option value="<?php echo $recurring['recurring_id'] ?>"><?php echo $recurring['name'] ?></option>
                  <?php } ?>
                </select>
                <div class="help-block" id="recurring-description"></div>
              </div>
              <?php } ?>


                <div class="product-buttons-wrap">
                    <div class="product-qyt-action space-padding-b-45 clearfix border-bottom">
                        <div class="quantity-title qty"><?php echo $objlang->get("entry_qty"); ?></div>

                        <!--
                                            <?php if($quantity > 0 ){ ?>
                                            <div class="product-qyt-action clearfix">
                                                <div class="quantity-adder  pull-left">
                                                    <span class="add-down add-action ">
                                                                <i class="fa fa-minus"></i>
                                                            </span>

                                                    <div class="quantity-number" style="display: inline;">
                                                        <input style=" background-color: #8ec354;" readonly type="text" name="quantity" value="<?php echo $minimum; ?>" size="2" id="input-quantity" class="form-control text-center" min="1"/>
                                                    </div>

                                                    <span  class="add-up add-action " onclick="plusFuntion()">
                                                                <i class="fa fa-plus"></i>
                                                            </span>
                                                </div>
                                            </div>
                                            <?php } else { ?>
                                            <div class="product-qyt-action clearfix">
                                                <div class="quantity-adder  pull-left">
                                                    <div class="quantity-number pull-left">
                                                        <input disabled type="number" name="quantity" value="<?php echo $minimum; ?>" size="2" id="input-quantity" class="form-control text-center" min="1"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php } ?>




                        -->


                        <div class="quantity-adder pull-left">
                        <span class="add-down add-action">
                            <i class="fa fa-minus"></i>
                        </span>
                            <span class="quantity-number">
                            <input type="text" name="quantity" value="<?php echo $minimum; ?>" size="2" id="input-quantity" class="form-control" />
                        </span>
                            <span class="add-up add-action">
                            <i class="fa fa-plus"></i>
                        </span>
                        </div>





                    </div>

                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>" />
                    <div class="clearfix space-padding-tb-35-25">
                        <div class="pull-left space-right-15">

                            <?php if($quantity <= 0 ) { ?>
                            <a href="<?=$contact_form?>" target="_blank" class="btn btn-primary radius-2x">
                                <span style="font-size: 18px;"><?php echo $button_request_product; ?></span>
                            </a>
                            <?php } else { ?>
                            <button type="button" id="button-cart" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-primary radius-2x shadow">
                                <i class="zmdi zmdi-mall"></i><span><?php echo $button_cart; ?></span>
                            </button>
                            <?php } ?>

                            <!--
                            <button <?php if($quantity <= 0 ) echo "disabled"; ?>  type="button" id="button-cart" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-primary radius-2x shadow">
                            <i class="zmdi zmdi-mall"></i><span><?php echo $button_cart; ?></span>
                            </button>
-->

                                </div>


                        <!--

                      <div class="product-buttons-wrap">
                        <div class="product-qyt-action space-padding-b-45 clearfix border-bottom">
                            <div class="quantity-title qty"><?php echo $objlang->get("entry_qty"); ?></div>

                            <div class="quantity-adder pull-left">
                                <span class="add-down add-action">
                                    <i class="fa fa-minus"></i>
                                </span>
                                <span class="quantity-number">
                                    <input type="text" name="quantity" value="<?php echo $minimum; ?>" size="2" id="input-quantity" class="form-control" />
                                </span>
                                <span class="add-up add-action">
                                    <i class="fa fa-plus"></i>
                                </span>
                            </div>
                        </div>

                        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>" />
                        <div class="clearfix space-padding-tb-35-25">
                        <div class="pull-left space-right-15">
                            <button type="button" id="button-cart" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-primary radius-2x shadow">
                              <i class="zmdi zmdi-mall"></i><span><?php echo $button_cart; ?></span>
                            </button>
                        </div>
                            -->



                <div class="pull-left space-right-10">
                    <a data-toggle="tooltip" class="btn btn-sm btn-inverse-light radius-x" title="<?php echo $button_compare; ?>" onclick="compare.add('<?php echo $product_id; ?>');"><i class="zmdi zmdi-refresh-alt"></i></a>
                </div>
                <div class="pull-left">
                    <a data-toggle="tooltip" class="btn btn-sm btn-inverse-light radius-x" title="<?php echo $button_wishlist; ?>" onclick="wishlist.add('<?php echo $product_id; ?>');"><i class="zmdi zmdi-favorite-outline zmdi-hc-fw"></i></a>
                </div>

                <?php if(isset($download_link)){ ?>
                <div class="pull-left" style="margin-top: 2%;">
                    <a href="<?php echo $download_link; ?>"><?php echo $text_download; ?></a>
                </div>
                <?php } ?>

                    </div>
                    <?php if ($minimum > 1) { ?>
                    <div class="alert alert-info space-top-10"><i class="fa fa-info-circle"></i> <?php echo $text_minimum; ?></div>
                    <?php } ?>
                </div>

                <!-- AddThis Button BEGIN -->
                <!--
                <div class="addthis_toolbox addthis_default_style" data-url="<?php echo $share; ?>"><a class="addthis_button_facebook_like" fb:like:layout="button_count"></a> <a class="addthis_button_tweet"></a> <a class="addthis_button_pinterest_pinit"></a> <a class="addthis_counter addthis_pill_style"></a></div>
                <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-515eeaf54693130e"></script>
                -->
                <!-- AddThis Button END -->
            </div>





          </div>

        </div>
      </div>


      <?php if ($tags) { ?>
      <p><?php echo $text_tags; ?>
        <?php for ($i = 0; $i < count($tags); $i++) { ?>
        <?php if ($i < (count($tags) - 1)) { ?>
        <a href="<?php echo $tags[$i]['href']; ?>"><?php echo $tags[$i]['tag']; ?></a>,
        <?php } else { ?>
        <a href="<?php echo $tags[$i]['href']; ?>"><?php echo $tags[$i]['tag']; ?></a>
        <?php } ?>
        <?php } ?>
      </p>
      <?php } ?>


    </div>
    <?php echo $column_right; ?>

  </div>

</div>

<div style="width: 100%;" >

    <div style="margin: 0 5% 0 5%";>
    <?php

        //require( ThemeControlHelper::getLayoutPath( 'product/info/'.$layout_pinfo.'.tpl' ) );
        require( ThemeControlHelper::getLayoutPath( 'product/info/custom.tpl' ) );
        ?>

        </div>

    <div class="container">
        <?php if ($products) {  $heading_title = $text_related; $customcols = 4; ?>
        <div class="panel panel-center product-related"> <?php require( ThemeControlHelper::getLayoutPath( 'common/products_carousel.tpl' ) );  ?>   </div>
        <?php } ?>
    </div>


    <?php echo $content_bottom; ?>
</div>

<script type="text/javascript"><!--
$('select[name=\'recurring_id\'], input[name="quantity"]').change(function(){
	$.ajax({
		url: 'index.php?route=product/product/getRecurringDescription',
		type: 'post',
		data: $('input[name=\'product_id\'], input[name=\'quantity\'], select[name=\'recurring_id\']'),
		dataType: 'json',
		beforeSend: function() {
			$('#recurring-description').html('');
		},
		success: function(json) {
			$('.alert, .text-danger').remove();

			if (json['success']) {
				$('#recurring-description').html(json['success']);
			}
		}
	});
});
//--></script>
<script type="text/javascript"><!--
$('#button-cart').on('click', function() {
	$.ajax({
		url: 'index.php?route=checkout/cart/add',
		type: 'post',
		data: $('#product input[type=\'text\'], #product input[type=\'hidden\'], #product input[type=\'radio\']:checked, #product input[type=\'checkbox\']:checked, #product select, #product textarea'),
		dataType: 'json',
		beforeSend: function() {
			$('#button-cart').button('loading');
		},
		complete: function() {
			$('#button-cart').button('reset');
		},
		success: function(json) {
			$('.alert, .text-danger').remove();
			$('.form-group').removeClass('has-error');

			if (json['error']) {
				if (json['error']['option']) {
					for (i in json['error']['option']) {
						var element = $('#input-option' + i.replace('_', '-'));

						if (element.parent().hasClass('input-group')) {
							element.parent().after('<div class="text-danger">' + json['error']['option'][i] + '</div>');
						} else {
							element.after('<div class="text-danger">' + json['error']['option'][i] + '</div>');
						}
					}
				}

				if (json['error']['recurring']) {
					$('select[name=\'recurring_id\']').after('<div class="text-danger">' + json['error']['recurring'] + '</div>');
				}

				// Highlight any found errors
				$('.text-danger').parent().addClass('has-error');
			}

			if (json['success']) {
				$('#notification').html('<div class="alert alert-success">' + json['success'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');

//				alert(json['total']);
//				$('#cart-total').html(json['total']);
		        res = json['total'].split("-");
  				$('#text-items').html(res[1]);
  				var out = json['total'].substr(0,json['total'].indexOf(' '));
  				$('#cart-total').html(out);
				$('html, body').animate({ scrollTop: 0 }, 'slow');

				$('#cart > ul').load('index.php?route=common/cart/info ul li');
			}
		}
	});
});
//--></script>
<script type="text/javascript"><!--
$('.date').datetimepicker({
	pickTime: false
});

$('.datetime').datetimepicker({
	pickDate: true,
	pickTime: true
});

$('.time').datetimepicker({
	pickDate: false
});

$('button[id^=\'button-upload\']').on('click', function() {
	var node = this;

	$('#form-upload').remove();

	$('body').prepend('<form enctype="multipart/form-data" id="form-upload" style="display: none;"><input type="file" name="file" /></form>');

	$('#form-upload input[name=\'file\']').trigger('click');

	if (typeof timer != 'undefined') {
    	clearInterval(timer);
	}

	timer = setInterval(function() {
		if ($('#form-upload input[name=\'file\']').val() != '') {
			clearInterval(timer);

			$.ajax({
				url: 'index.php?route=tool/upload',
				type: 'post',
				dataType: 'json',
				data: new FormData($('#form-upload')[0]),
				cache: false,
				contentType: false,
				processData: false,
				beforeSend: function() {
					$(node).button('loading');
				},
				complete: function() {
					$(node).button('reset');
				},
				success: function(json) {
					$('.text-danger').remove();

					if (json['error']) {
						$(node).parent().find('input').after('<div class="text-danger">' + json['error'] + '</div>');
					}

					if (json['success']) {
						alert(json['success']);

						$(node).parent().find('input').attr('value', json['code']);
					}
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		}
	}, 500);
});
//--></script>
<script type="text/javascript"><!--
$('#review').delegate('.pagination a', 'click', function(e) {
    e.preventDefault();

    $('#review').fadeOut('slow');

    $('#review').load(this.href);

    $('#review').fadeIn('slow');
});

$('#review').load('index.php?route=product/product/review&product_id=<?php echo $product_id; ?>');

$('#button-review').on('click', function() {
  $.ajax({
    url: 'index.php?route=product/product/write&product_id=<?php echo $product_id; ?>',
    type: 'post',
    dataType: 'json',
    data: $("#form-review").serialize(),
    beforeSend: function() {
      $('#button-review').button('loading');
    },
    complete: function() {
      $('#button-review').button('reset');
    },
    success: function(json) {
      $('.alert-success, .alert-danger').remove();

      if (json['error']) {
        $('#review').after('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '</div>');
      }

      if (json['success']) {
        $('#review').after('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '</div>');

        $('input[name=\'name\']').val('');
        $('textarea[name=\'text\']').val('');
        $('input[name=\'rating\']:checked').prop('checked', false);
      }
    }
  });
});

$(document).ready(function() {
  $('.thumbnail a').click(
    function(){
      $.magnificPopup.open({
        items: {
          src:  $('img',this).attr('src')
        },
        type: 'image'
      });
      return false;
    }
  );
});
//--></script>



<script type="text/javascript"><!--

    var images = <?php echo json_encode($images); ?>;

    $('#myForm input').on('change', function() {
        $('#colorName').html($('input[type=radio]:checked', '#myForm').attr('about'));
        $('.styledBorder').removeAttr('style');
        var ImgId = $('input[type=radio]:checked', '#myForm').attr('id');
        $('#'+ ++ImgId ).attr('style', "border-style: solid; border-width: 2px; border-color: #00bdec")

        maxx = $('#'+ ImgId ).attr('about');

        var input = document.getElementById("input-quantity");
        input.value=1;

        var option = $('input[type=radio]:checked', '#myForm').attr('about');
        var optionImages = images[option];

        $('#mainImgA').attr('href', optionImages[0]['popup'] );
        $('#image').attr('src', optionImages[0]['popup'] );
        $('#image').attr('data-zoom-image', optionImages[0]['popup'] );


        $("#image").data('zoom-image', optionImages[0]['popup']).elevateZoom({
            responsive: true,
            zoomType: 'lens',
            containLensZoom: true
        });

        var i = 0;
        for (i = 0; i < optionImages.length; i++) {

            $('#a' + i).attr('href', optionImages[i]['popup']);
            $('#a' + i).attr('data-zoom-image', optionImages[i]['popup']);
            $('#a' + i).attr('data-image',  optionImages[i]['popup']);
            $('#a' + i).show();
            
            $('#i' + i).attr('src', optionImages[i]['thumb']);
            $('#i' + i).attr('data-zoom-image',  optionImages[i]['popup']);
            $('#i' + i).show();
        }
        
        while($('#a' + i).length)
        {
            $('#a' + i).hide();
            $('#i' + i).hide();
            i++;
        }
    });

//--></script>

<script type="text/javascript">
    var maxx = <?php echo $quantity; ?>

    function plusFuntion() {


        var input = document.getElementById("input-quantity");
        input.max = maxx;
        //alert(maxx);
        if(maxx>parseInt(input.value)){
            //var rr =parseInt(input.value)+1
            //input.value=rr.toString();

        }
        else if(maxx<parseInt(input.value)){

            input.value=parseInt(input.value)-1;

        }
        else{

            input.value=parseInt(input.value)-1;
        }


    }

</script>

<?php echo $footer; ?>

