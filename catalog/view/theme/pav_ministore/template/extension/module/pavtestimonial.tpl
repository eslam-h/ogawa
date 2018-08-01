<?php if( count($testimonials) ) { ?>
	<?php $id = rand(1,10)+rand();?>
   <div id="pavtestimonial<?php echo $id;?>" class="carousel slide pavtestimonial">
		<div class="carousel-inner">
			 <?php foreach ($testimonials as $i => $testimonial) {  ?>
				<div class="item <?php if($i==0) {?>active<?php } ?>">
	 				<div class="testimonial-item text-center">
						<?php if(  $testimonial['description'] ) { ?>
						<div class="testimonial">
							<div><?php echo $testimonial['description']; ?></div>
						</div>
						<?php } ?>
						<?php if(  $testimonial['profile'] ) { ?>
						<div class="profile space-padding-lr-10">
							<div><?php echo $testimonial['profile']; ?></div>
						</div>
						<?php } ?>
						<?php if( $testimonial['video_link']) { ?>
						<a class="colorbox-t" href="<?php echo $testimonial['video_link'];?>"><?php echo $this->language->get('text_watch_video_testimonial');?></a>
						<?php } ?>
					</div>
				</div>
			<?php } ?>
		</div>
	 		
		<?php if( count($testimonials) > 1 ){ ?>
			<div class="carousel-controls-v1">	
				<a class="carousel-control carousel-sm left" href="#pavtestimonial<?php echo $id;?>" data-slide="prev"><i class="zmdi zmdi-chevron-left zmdi-hc-fw"></i></a>
				<a class="carousel-control carousel-sm right" href="#pavtestimonial<?php echo $id;?>" data-slide="next"><i class="zmdi zmdi-chevron-right zmdi-hc-fw"></i></a>
			</div>
		<?php } ?>
    </div>
	<?php if( count($testimonials) > 1 ){ ?>
	<script type="text/javascript">
	<!--
		$('#pavtestimonial<?php echo $id;?>').carousel({interval:<?php echo ( $auto_play_mode?$interval:'false') ;?>,auto:<?php echo $auto_play;?>,pause:'hover'});
	-->
	</script>
	<?php } ?>

<?php } ?>
