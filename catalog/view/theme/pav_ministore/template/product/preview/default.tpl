<?php if ($thumb || $images) { ?>
<div class="col-lg-5 col-md-5 col-sm-5 col-xs-12 image-container">
    <?php if ($thumb) { ?>
    <div class="image thumbnail">

        <?php if( isset($date_available) && $date_available == date('Y-m-d')) {   ?>
            <span class="product-label product-label-new">
            <span><?php echo 'New'; ?></span>
        </span>
        <?php } ?>
        <?php if( $special )  { ?>
            <span class="product-label exist">
                <span class="product-label-special"><?php echo 'Sale'; ?></span>
            </span>
        <?php } ?>

        <a id="mainImgA" href="<?php echo $popup; ?>" title="<?php echo $heading_title; ?>" class="info_colorbox">
            <img  src="<?php echo $thumb; ?>" title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" id="image"  data-zoom-image="<?php echo $popup; ?>" class="product-image-zoom img-responsive"/>
        </a>

    </div>
    <?php } ?>
</div>
<?php } ?>
<div class="thumbnails quickview-thumbnail">
<div class="col-lg-1 col-md-1 col-sm-1 col-xs-12 hidden-xs">
    <?php if ($images) { ?>
    <div class="image-additional slide carousel vertical" id="image-additional">
        <div id="image-additional-carousel" class="carousel-inner">
            <?php
            if( $productConfig['product_zoomgallery'] == 'slider' && $thumb ) {
                $eimages = array( 0=> array( 'popup'=>$popup,'thumb'=> $thumb )  );
                $images = array_merge( $eimages, $images );
            }
            $icols = 4; $i= 0;
            foreach ($images as  $image) { ?>
                <?php if( (++$i)%$icols == 1 ) { ?>
                <div class="item clearfix">
                <?php } ?>
                    <a href="<?php echo $image['popup']; ?>" title="<?php echo $heading_title; ?>" class="colorbox" data-zoom-image="<?php echo $image['popup']; ?>" data-image="<?php echo $image['popup']; ?>">
                        <img src="<?php echo $image['thumb']; ?>" style="max-width:<?php echo $config->get('theme_default_image_additional_width');?>px"  title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" data-zoom-image="<?php echo $image['popup']; ?>" class="product-image-zoom img-responsive" />
                    </a>
                <?php if( $i%$icols == 0 || $i==count($images) ) { ?>
                </div>
              <?php } ?>
            <?php } ?>
        </div>

        <!-- Controls -->
        <?php
        if(count($images)>4){
        ?>
        <div class="carousel-controls-v1">
            <a class="carousel-control carousel-xs left" href="#image-additional" data-slide="next">
                <i class="fa fa-angle-left"></i>
            </a>
            <a class="carousel-control carousel-xs right" href="#image-additional" data-slide="prev">
                <i class="fa fa-angle-right"></i>
            </a>
        </div>
        <?php } ?>
    </div>
    <script type="text/javascript">
        $('#image-additional .item:first').addClass('active');
        $('#image-additional').carousel({interval:false})
    </script>
    <?php } ?>
</div>
</div>

<script type="text/javascript" src=" catalog/view/javascript/jquery/elevatezoom/elevatezoom-min.js"></script>
<script type="text/javascript"><!--
    $('#image-additional-carousel a').on('click', function() {

        var src = $(this).attr('href');

        $('#mainImgA').attr('href', src);
        $('#image').attr('src', src);
        $('#image').attr('data-zoom-image', src);

        if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|BB|PlayBook|IEMobile|Windows Phone|Kindle|Silk|Opera Mini/i.test(navigator.userAgent)) {
            // Take the user to a different screen here.
        }
        else {
            $("#image").data('zoom-image', src).elevateZoom({
                responsive: true,
                scrollZoom: true,
                containLensZoom: true
            });
        }
        return false;
    });


    if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|BB|PlayBook|IEMobile|Windows Phone|Kindle|Silk|Opera Mini/i.test(navigator.userAgent)) {
        // Take the user to a different screen here.
    }
    else {
        $("#image").elevateZoom({scrollZoom : true});
    }

    //--></script>