<?php  echo $header; ?>

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

<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDd9QTt9S_f_9FMJSNh9MeV7gttcvyVM1A"></script>
<script type="text/javascript" src="catalog/view/javascript/gmap/gmap3.min.js"></script>
<script type="text/javascript" src="catalog/view/javascript/gmap/gmap3.infobox.js"></script>

<div class="container">

    <br>

    <?php $javascriptLocations = array();  $id = 0;  foreach($locations as $old_type => $arr) { ?>
    <?php foreach($arr as $location) { ?>

    <?php
        $javascriptLocations[] = array(
            'shop_name' => $location['name'],
            'address'   => $location['address'],
            'lat'       => $location['latitude'],
            'lng'       => $location['longitude']
        );
    ?>
    <div class="row">
        <div class="col-lg-2 col-md-2 col-sm-2"></div>
        <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                <h1 style="color:#5fb937; display: inline; font-weight:bold; font-size: 24px;" > <?php echo($location['name']); ?> </h1>
            </div>

            <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
                <h4 style="color:#000000; display: inline; font-weight:300; font-size: 20px;"><?php echo($location['address']); ?> </h4>
            </div>

        </div>

        <?php if(isset($location['open']) && strLen($location['open']) > 0 ) { ?>
        <div class="col-lg-2 col-md-2 col-sm-2"></div>
        <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                <h1 style="color:#5fb937; display: inline; font-weight:bold; font-size: 24px;" > <?php echo($text_open); ?> </h1>
            </div>

            <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
                <h4 style="color:#000000; display: inline; font-weight:100; font-size: 20px;" > <?php echo(htmlspecialchars_decode($location['open'])); ?> </h4>
            </div>
        </div>
        <?php } ?>

        <div class="col-lg-2 col-md-2 col-sm-2"></div>
        <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                <h1 style="color:#5fb937; font-weight:bold; font-size: 24px; display: inline;" > <?php echo($text_tele); ?> </h1>
            </div>

            <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
                <h4 style="color:#000000; font-weight:100; font-size: 20px; direction:ltr; display: inline-flex;"><?php echo($location['telephone']); ?> </h4>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-lg-3 col-md-2 col-sm-1"></div>
        <div id="<?php echo $id++; ?>" class="col-lg-6 col-md-8 col-sm-10 col-xs-12" style="height: 350px;"></div>
    </div>

    <br>

    <?php } ?>
    <?php } ?>

</div>

<script type="text/javascript">
    <?php echo ("var locations = " . json_encode($javascriptLocations)); ?>

    var i;

    <?php for($i = 0; $i < $id; $i++) { ?>

        i = <?php echo $i; ?>;

        var lat = ( locations[<?php echo $i; ?>]['lat'] );
        var lng = ( locations[<?php echo $i; ?>]['lng'] );

        var myLatlng = new google.maps.LatLng(parseFloat(lat),parseFloat(lng));

        var map = new google.maps.Map(document.getElementById('<?php echo $i; ?>'), {
            zoom: 10,
            center:myLatlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });

        var infowindow = new google.maps.InfoWindow();

        var marker;

        marker = new google.maps.Marker({
            position: myLatlng,
            map: map
            });

        google.maps.event.addListener(marker, 'click', (function(marker, i) {
            return function() {
                infowindow.setContent("<br>"+"<strong>"+locations[<?php echo $i; ?>]['shop_name']+"</strong><br>"+locations[<?php echo $i; ?>]['address'] );
                infowindow.open(map, marker);
            }
        })(marker, i));


        <?php } ?>

    console.log(locations);
</script>

<?php echo $footer; ?>