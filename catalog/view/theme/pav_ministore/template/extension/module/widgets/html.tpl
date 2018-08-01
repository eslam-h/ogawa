
<div class="widget-image <?php echo $addition_cls ?>  <?php if( isset($stylecls)&&$stylecls ) { ?>box-<?php echo $stylecls;?><?php } ?>">

	<div class="custom-v1 panel-center">	
		<?php if( $show_title ) { ?>
		<div class="widget-heading panel-heading pull-left"><h3 class="panel-title align-left"><?php echo $heading_title?></h3></div>
		<?php } ?>
		<div class="pull-left" style="color: #708a87">
		 <?php echo htmlspecialchars_decode( $html ); ?>
		</div>
	</div>	 
</div>
