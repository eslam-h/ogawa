
<?php if ($thumb || $images[$selected_option]) { ?>
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


















    <div class="thumbs-preview thumbnails horizontal">
        <?php if ($images[$selected_option]) { $icols = 3; $i= 0; $id2 = 0; ?>
        <div class="image-additional olw-carousel  owl-carousel-play" id="image-additional"   data-ride="owlcarousel">
            <div id="image-additional-carousel" class="owl-carousel" data-show="<?php echo $icols; ?>" data-pagination="false" data-navigation="true">
                <?php
                    foreach ($images[$selected_option] as  $image) { ?>
                <div>
                    <a id="a<?php echo $id2; ?>" href="<?php echo $image['popup']; ?>" title="<?php echo $heading_title; ?>" class="imagezoom" data-zoom-image="<?php echo $image['popup']; ?>" data-image="<?php echo $image['popup']; ?>">
                        <img style="margin-left: 26%;" id="i<?php echo $id2++; ?>" src="<?php echo $image['thumb']; ?>"   title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" data-zoom-image="<?php echo $image['popup']; ?>" class="product-image-zoom img-responsive" />
                    </a>
                </div>
                <?php } ?>
            </div>

            <!-- Controls -->
            <?php
            if(count($images[$selected_option])>$icols){
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

<div class="col-lg-1 col-md-1 col-sm-1 col-xs-12 hidden-xs">
</div>

<?php } ?>


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