<div class="product-filter space-padding-tb-10 clearfix border-bottom">
  <div class="display">
    <div class="btn-group group-switch">
     <button type="button" id="grid-view" class="btn btn-switch active" data-toggle="tooltip" title="<?php echo $button_grid; ?>">
      <i class="zmdi zmdi-apps zmdi-hc-fw"></i>
      </button>
      <button type="button" id="list-view" class="btn btn-switch" data-toggle="tooltip" title="<?php echo $button_list; ?>">
      <i class="zmdi zmdi-view-list-alt zmdi-hc-fw"></i>
      </button>
      
    </div>
  </div>
  <div class="filter-left">
    <div class="product-compare pull-left"><a href="<?php echo $compare; ?>" class="btn btn-primary" id="compare-total"><?php echo $text_compare; ?></a></div>
    <div class="sort pull-left">
      <span><?php echo $text_sort; ?></span>
      <select id="input-sort" class="form-control" onchange="location = this.value;">
        <?php foreach ($sorts as $sorts) { ?>
        <?php if ($sorts['value'] == $sort . '-' . $order) { ?>
        <option value="<?php echo $sorts['href']; ?>" selected="selected"><?php echo $sorts['text']; ?></option>
        <?php } else { ?>
        <option value="<?php echo $sorts['href']; ?>"><?php echo $sorts['text']; ?></option>
        <?php } ?>
        <?php } ?>
      </select>
    </div>
    
    <div class="limit pull-left">
      <span><?php echo $text_limit; ?></span>
      <select id="input-limit" class="form-control" onchange="location = this.value;">
        <?php foreach ($limits as $limits) { ?>
        <?php if ($limits['value'] == $limit) { ?>
        <option value="<?php echo $limits['href']; ?>" selected="selected"><?php echo $limits['text']; ?></option>
        <?php } else { ?>
        <option value="<?php echo $limits['href']; ?>"><?php echo $limits['text']; ?></option>
        <?php } ?>
        <?php } ?>
      </select>
    </div>
  </div>

</div>