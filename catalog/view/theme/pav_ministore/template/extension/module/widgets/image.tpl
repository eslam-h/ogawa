<?php if ( isset($thumbnailurl) )  { ?>
<div class="widget-images panel  <?php if( isset($stylecls)&&$stylecls ) { ?>box-<?php echo $stylecls;?><?php } ?>">
	<?php if( $show_title ) { ?>
	<div class="widget-heading"><h3 class="panel-title"><?php echo $heading_title?></h3></div>
	<?php } ?>
	<div class="widget-inner img-adv box-content clearfix">
		 <div class="image-item">
			 <?php if(strLen($link) > 0){ ?>
				 <a href="<?php echo $link;?>">
					<div class="<?php echo $addition_cls ?>">
						<img class="img-responsive" alt=" " src="<?php echo $thumbnailurl; ?>"/>
					</div>
				</a>
			 <?php } else { ?>
				 <div class="<?php echo $addition_cls ?>">
					 <img class="img-responsive" alt=" " src="<?php echo $thumbnailurl; ?>"/>
				 </div>
			 <?php } ?>
		 	<?php if( $ispopup ){ ?>
		 		<a href="<?php echo $imageurl; ?>" class="pts-popup fancybox" title="<?php echo $this->language->get('Large Image');?>"><span class="icon icon-expand"></span></a>
		 	<?php } ?>
		 </div>
	</div>
</div>
<?php } ?>