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

       <?php if ($thumb || $description) { ?>
      <div class="category-info clearfix hidden-xs hidden-sm">
        <?php if ($thumb) { ?>
          <div class="image"><img src="<?php echo $thumb; ?>" alt="<?php echo $heading_title; ?>" class="img-responsive" /></div>
          <?php } ?>
          <?php if ($description) { ?>
          <div class="category-description wrapper">
            <?php echo $description; ?>
          </div>
          <?php } ?>
      </div>
      <?php } ?>

      <?php if( false &&  $categories = $helper->getCategoriesById() ){   ?>
     <div class="subcategories panel panel-default">
      <div class="panel-heading">
        <h4 class="panel-title"><?php echo $text_refine; ?> </h4>
       </div>
        <div class="panel-body">
          <?php $col=6; $i=0; $ncol = floor(12/$col); foreach( $categories as $category ){  $i++; ?>
            <?php if($i%$col==1) { ?>
            <div class="row">
            <?php } ?>
            <div class="col-lg-<?php echo $ncol; ?> col-md-3 col-sm-4"><div class="category-item">
              <?php if( $category['thumb'] ){ ?>
                   <div class="image">
                      <a href="<?php echo $category['href']; ?>">
                        <img src="<?php echo $category['thumb']; ?>" alt="<?php echo $category['name']; ?>" class="img-responsive" />
                      </a>
                  </div>
              <?php } ?>
                <a href="<?php echo $category['href']; ?>"><?php echo $category['name']; ?></a>
            </div></div>
            <?php if( $i%$col==0 || $i==count($categories) ){ ?>
            </div>
            <?php } ?>
          <?php } ?>
        </div>
        </div>
        <?php } else if ($categories) { ?>
          <div class="refine-search panel panel-center space-20">
            <div class="panel-heading hidden">
              <h4 class="panel-title"><?php echo $text_refine; ?></h4>
            </div>
            <div class="panel-body border">
              <?php if (count($categories) <= 20) { ?>
                  <ul class="list-inline">
                    <?php foreach ($categories as $category) { ?>
                    <li><a href="<?php echo $category['href']; ?>"><?php echo $category['name']; ?></a></li>
                    <?php } ?>
                  </ul>
              <?php } else { ?>
              <div class="row">
                <?php foreach (array_chunk($categories, ceil(count($categories) / 4)) as $categories) { ?>
                <div class="col-sm-3">
                  <ul class="list-inline">
                    <?php foreach ($categories as $category) { ?>
                    <li><a href="<?php echo $category['href']; ?>"><?php echo $category['name']; ?></a></li>
                    <?php } ?>
                  </ul>
                </div>
                <?php } ?>
              </div>
              <?php } ?>
            </div>
         </div>    <!-- end refine-search -->
      <?php } ?>

      <?php if ($products) { ?>
      <?php   require( ThemeControlHelper::getLayoutPath( 'product/product_filter.tpl' ) );   ?>
      <br />
        <?php require( ThemeControlHelper::getLayoutPath( 'common/product_collection.tpl' ) );  ?>
      <?php } ?>
      <?php if (!$categories && !$products) { ?>
      <p><?php echo $text_empty; ?></p>
      <div class="buttons">
        <div class="pull-right"><a href="<?php echo $continue; ?>" class="btn btn-primary"><?php echo $button_continue; ?></a></div>
      </div>
      <?php } ?>
      <?php echo $content_bottom; ?></div>
    <?php echo $column_right; ?></div>
</div>
<?php echo $footer; ?>
