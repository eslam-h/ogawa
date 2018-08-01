<div class="product-block clearfix">
    <?php if ($first_product['thumb']) {    ?>      
      <div class="image">
        <?php if( $first_product['special'] ) {   ?>
          <span class="product-label bts"><span class="product-label-special"><?php echo $objlang->get('text_sale'); ?></span></span>
        <?php } ?>

        <div class="product-img img">
          <a class="img" title="<?php echo $first_product['name']; ?>" href="<?php echo $first_product['href']; ?>">
            <img class="img-responsive" src="<?php echo $first_product['thumb']; ?>" title="<?php echo $first_product['name']; ?>" alt="<?php echo $first_product['name']; ?>" />
          </a>
         <div class="box-v1"></div>         
        </div>
        <div class="bottom">
          <?php if( !isset($listingConfig['catalog_mode']) || !$listingConfig['catalog_mode'] ) { ?>
            <div class="cart">            
               <button data-loading-text="Loading..." class="btn" type="button" onclick="cart.addcart('<?php echo $first_product['product_id']; ?>');">
                 <i class="zmdi zmdi-shopping-cart-plus"></i><span><?php echo $button_cart; ?></span>
              </button>
            </div>
          <?php } ?>
          <div class="action">           

            <div class="compare">     
              <button class="btn btn-sm radius-x btn-outline-light " type="button" data-toggle="tooltip" data-placement="top" title="<?php echo $objlang->get("button_compare"); ?>" onclick="compare.addcompare('<?php echo $first_product['product_id']; ?>');"><i class="zmdi zmdi-tune zmdi-hc-fw"></i></button> 
            </div>  
            <div class="wishlist">
              <button class="btn btn-sm radius-x btn-outline-light " type="button" data-toggle="tooltip" data-placement="top" title="<?php echo $objlang->get("button_wishlist"); ?>" onclick="wishlist.addwishlist('<?php echo $first_product['product_id']; ?>');"><i class="zmdi zmdi-favorite-outline zmdi-hc-fw"></i></button> 
            </div> 
            <?php if ( isset($quickview) && $quickview ) { ?>
             <div class="quickview hidden-sm hidden-xs">
              <a class="iframe-link btn quick-view btn btn-sm radius-x btn-outline-light" data-toggle="tooltip" data-placement="top" href="<?php echo $ourl->link('themecontrol/product','product_id='.$first_product['product_id']);?>"  title="<?php echo $objlang->get('quick_view'); ?>" ><i class="zmdi zmdi-eye zmdi-hc-fw"></i></a>
            </div>
            <?php } ?> 
            <?php if( isset($categoryPzoom) && $categoryPzoom ) { $zimage = str_replace( "cache/","", preg_replace("#-\d+x\d+#", "",  $first_product['thumb'] ));  ?>
            <div class="zoom hidden-xs hidden-sm">
                <?php if( isset($categoryPzoom) && $categoryPzoom ) { $zimage = str_replace( "cache/","", preg_replace("#-\d+x\d+#", "",  $first_product['thumb'] ));  ?>
                  <a data-toggle="tooltip" data-placement="top" href="<?php echo $zimage;?>" class="product-zoom info-view colorbox cboxElement btn btn-sm radius-x btn-outline-light" title="<?php echo $first_product['name']; ?>"><i class="zmdi zmdi-zoom-in zmdi-hc-fw"></i></a>
                <?php } ?>
            </div>  
            <?php } ?> 
          </div>

        </div>   
      </div>
    <?php } ?>

  <div class="product-meta">
        <h6 class="name"><a href="<?php echo $first_product['href']; ?>"><?php echo $first_product['name']; ?></a></h6>
        <div class="rating-price">
          <?php if ($first_product['rating']) { ?>
          <div class="rating">
            <?php for ($is = 1; $is <= 5; $is++) { ?>
            <?php if ($first_product['rating'] < $is) { ?>
            <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-1x"></i></span>
            <?php } else { ?>
            <span class="fa fa-stack"><i class="fa fa-star fa-stack-1x"></i>
            </span>
            <?php } ?>
            <?php } ?>
            </div>
          <?php } ?>   
           <?php if ($first_product['price']) { ?>
          <div class="price">
            <?php if (!$first_product['special']) {  ?>
              <span class="price-original"><?php echo $first_product['price']; ?></span>
              <?php if( preg_match( '#(\d+).?(\d+)#',  $first_product['price'], $p ) ) { ?> 
              <?php } ?>
            <?php } else { ?>
              <span class="price-new"><?php echo $first_product['special']; ?></span>
              <span class="price-old"><?php echo $first_product['price']; ?></span> 
              <?php if( preg_match( '#(\d+).?(\d+)#',  $first_product['special'], $p ) ) { ?> 
              <?php } ?>

            <?php } ?>
          </div>
          <?php } ?>
        </div>                

        <?php if( isset($first_product['description']) ){ ?> 
        <p class="description"><?php echo utf8_substr( strip_tags($first_product['description']),0,200);?>...</p>
        <?php } ?>                     
  </div>  
</div>





