<link href="catalog\view\javascript\slick\slick.css" rel="stylesheet" type="text/css">
<link href="catalog\view\javascript\slick\slick-theme.css" rel="stylesheet" type="text/css">

<style type="text/css">

    .pp_description {
        display: none !important;
    }

    html, body {
        margin: 0;
        padding: 0;
    }

    * {
        box-sizing: border-box;
    }

    .slider {
        width: 100%;
        margin: 100px auto;
    }

    .slick-slide {
        margin: 0px 20px;
    }

    .slick-slide img {
        width: 100%;
    }

    .slick-prev:before,
    .slick-next:before {
        color: #a09f99;
    }




    .fixed {
        position: fixed;
        top: 0;
        /*height: 70px;*/
        z-index: 1;
    }
    #momenul {
        background: #8ec354;
    }
    #momenul li {
        display: inline-block;
    }

    #momenul li a {
        color: #000000;
        text-transform: uppercase;
    }
</style>

<script src="catalog\view\javascript\slick\slick.js" type="text/javascript" charset="utf-8"></script>

<script>
    $(document).on('ready', function() {
        $(".regular").slick({
            dots:           true,
            infinite:       true,
            slidesToShow:   3,
            slidesToScroll: 1,
            autoplay:       true,
            autoplaySpeed:  5000,
            responsive: [
                {
                    breakpoint: 1200,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1
                    }
                },
                {
                    breakpoint: 1008,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                }

            ],
            rtl:            <?php echo $rtl; ?>
    });
    });

    $(window).resize(function () {
        $('.regular').slick('resize');
    });

    $(window).on('orientationchange', function () {
        $('.regular').slick('resize');
    });

</script>

<?php if(isset($available_offers['notab']) && $available_offers['notab']) { ?>
<div class="offer_header">
    <span class="availableoffer_header"><?php echo $text_available_offers; ?></span>
    <span class="availableoffer_headerafter"></span>
</div>
<?php echo $available_offers['notab']; ?>
<?php } ?>

<div class=" tab-v space-margin-tb-60" id="div-momen">
    <ul class="nav nav-tabs" id="momenul">
        <li class=" active"><a style="color: white" class="nowhere" href="#tab-description" data-toggle="tab"><?php echo $tab_description; ?></a></li>

        

        <?php if ($features['description'] || $features['image'] || $features['image-description']) { ?>
        <li><a style="color: white" class="nowhere" href="#tab-feature" data-toggle="tab"><?php echo $tab_feature; ?></a></li>
        <?php } ?>
        <?php if ($attribute_groups) { ?>
        <li><a style="color: white" class="nowhere" href="#tab-specification" data-toggle="tab"><?php echo $tab_attribute; ?></a></li>
        <?php } ?>
        
        <?php if(isset($available_offers['tab']) && $available_offers['tab']) { ?>
        <li><a style="color: white" class="nowhere" href="#tab-available-offers" data-toggle="tab"><?php echo $tab_available_offers; ?></a></li>
        <?php } ?>
        
        <?php if ($review_status) { ?>
        <li><a style="color: white" class="nowhere" href="#tab-review" data-toggle="tab"><?php echo $tab_review; ?></a></li>
        <?php } ?>
        <?php if( $productConfig['enable_product_customtab'] && isset($productConfig['product_customtab_name'][$languageID]) ) { ?>
        <li><a style="color: white" class="nowhere" href="#tab-customtab" data-toggle="tab"><?php echo $productConfig['product_customtab_name'][$languageID]; ?><span class="triangle-bottomright"></span></a></li>
        <?php } ?>
    </ul>


    <div class=" border-bottom">
        <div class="active" id="tab-description">
            <h1 style=" font-size: 24px; color: #5fb937" ><?php echo $tab_description; ?></h1>
            <br>
            <?php echo $description; ?>
        </div>


        <?php if ($features['description'] || $features['image'] || $features['image-description']) { ?>
        <div   id="tab-feature">
            <h1 style=" font-size: 24px; color: #5fb937" ><?php echo $tab_feature; ?></h1>
            <br>
            <?php if($features['description']) { ?>
            <div class="row border-bottom ">

                <?php foreach($features['description'] as $feature){ ?>
                <div style="word-wrap: break-word;" class="col-lg-4 col-md-6 col-sm-12">
                    <h3 style="font-size: 14px;" ><?php echo $feature['title']; ?></h3>
                    <?php echo $feature['description']; ?>
                </div>
                <?php } ?>
                <br><br>
            </div>
            <?php } ?>

            <?php if($features['image-description']) { ?>
            <div class="row ">


                <?php $counter=0; foreach($features['image-description'] as $feature){ ?>

                <?php if($counter++%3 == 0) { ?>
                </div>
                <div class="row ">
                <?php } ?>

                <div style="text-align: center; word-wrap: break-word;" class="col-lg-4 col-md-6 col-sm-12">
                    <img src="<?php echo $feature['image'] ;?>"/>
                    <br>
                    <h3 style="font-size: 14px;" ><?php echo $feature['title']; ?></h3>
                    <?php echo $feature['description']; ?>
                </div>
                <?php } ?>
                    <?php if($counter++%3 == 0) { ?>
                </div>
            <div class="row ">
                <?php } ?>
            <?php } ?>

            <?php if($features['image']) { ?>
            <div class="row border-bottom">
                <section class="regular slider">
                    <?php foreach($features['image'] as $feature){ ?>
                    <div >
                        <img src="<?php echo $feature['image'] ;?>"/>
                    </div>
                    <?php } ?>
                </section>
                <br><br>
            </div>
            <?php } ?>
        </div>
        <?php } ?>



        <?php if ($attribute_groups) { ?>
        <div  id="tab-specification">
            <h1 style=" font-size: 24px; color: #5fb937" ><?php echo $tab_attribute; ?></h1>
            <br>
            <table class="table table-bordered">
                <?php foreach ($attribute_groups as $attribute_group) { ?>
                <thead>
                <tr>
                    <td colspan="2"><strong><?php echo $attribute_group['name']; ?></strong></td>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($attribute_group['attribute'] as $attribute) { ?>
                <tr>
                    <td><strong><?php echo $attribute['name']; ?></strong></td>
                    <td><?php echo $attribute['text']; ?></td>
                </tr>
                <?php } ?>
                </tbody>
                <?php } ?>
            </table>
        </div>
        <?php } ?>
            
            <?php if(isset($available_offers['tab']) && $available_offers['tab']) { ?>
        <h1 style=" font-size: 24px; color: #5fb937" ><?php echo $tab_available_offers; ?></h1>
            <br>
        <div class="tab-pane" id="tab-available-offers"><?php echo $available_offers['tab']; ?></div>
        <?php } ?>
            
        <?php if ($review_status) { ?>

        <div  id="tab-review">
            <h1 style=" font-size: 24px; color: #5fb937" ><?php echo $tab_review; ?></h1>
            <br>
            <form class="form-horizontal" id="form-review">
                <div id="review"></div>
                <h2><?php echo $text_write; ?></h2>
                <?php if ($review_guest) { ?>
                <div class="form-group required">
                    <div class="col-sm-12">
                        <label class="control-label" for="input-name"><?php echo $entry_name; ?></label>
                        <input type="text" name="name" value="<?php echo $customer_name; ?>" id="input-name" class="form-control" />
                    </div>
                </div>
                <div class="form-group required">
                    <div class="col-sm-12">
                        <label class="control-label" for="input-review"><?php echo $entry_review; ?></label>
                        <textarea name="text" rows="5" id="input-review" class="form-control"></textarea>
                        <div style="display: none;" class="help-block"><?php echo $text_note; ?></div>
                    </div>
                </div>
                <div class="form-group required">
                    <div class="col-sm-12">
                        <label class="control-label"><?php echo $entry_rating; ?></label>
                        &nbsp;&nbsp;&nbsp; <?php echo $entry_bad; ?>&nbsp;
                        <input type="radio" name="rating" value="1" />
                        &nbsp;
                        <input type="radio" name="rating" value="2" />
                        &nbsp;
                        <input type="radio" name="rating" value="3" />
                        &nbsp;
                        <input type="radio" name="rating" value="4" />
                        &nbsp;
                        <input type="radio" name="rating" value="5" />
                        &nbsp;<?php echo $entry_good; ?></div>
                </div>
                <?php echo $captcha; ?>
                <div class="buttons clearfix">
                    <div class="pull-right">
                        <button type="button" id="button-review" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-primary"><?php echo $button_continue; ?></button>
                    </div>
                </div>
                <?php } else { ?>
                <?php echo $text_login; ?>
                <?php } ?>
            </form>
        </div>
        <?php } ?>
        <?php if( $productConfig['enable_product_customtab'] && isset($productConfig['product_customtab_content'][$languageID]) ) { ?>
        <div id="tab-customtab" class="tab-content tab-pane custom-tab">
            <div class="inner">
                <?php echo html_entity_decode( $productConfig['product_customtab_content'][$languageID], ENT_QUOTES, 'UTF-8'); ?>
            </div>
        </div>
        <?php } ?>

    </div>
</div>
    </div>

<script>
    $('.nowhere').click(function(e) {
        e.preventDefault();
        $("body, html").animate({
            scrollTop: $( $(this).attr('href') ).offset().top - $('#momenul').height()
        }, 1200);

    });

    $(document).ready(function(){
        var divTop = parseInt($( '#div-momen' ).position().top  );

        var divBotoom = parseInt($( '#div-momen' ).position().top + $( '#div-momen' ).outerHeight(true)  );

        console.log(divTop+' - '+divBotoom)

        $(window).bind('scroll', function() {

            if($(window).scrollTop() >= divTop && $(window).scrollTop() <= divBotoom) {
                $('#momenul').addClass('fixed');
            }
            else {
                $('#momenul').removeClass('fixed');
            }
        });
    });


</script>