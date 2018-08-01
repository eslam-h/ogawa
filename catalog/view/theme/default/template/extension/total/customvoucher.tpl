<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title"><a href="#collapse-customvoucher" class="accordion-toggle" data-toggle="collapse" data-parent="#accordion"><?php echo $heading_title; ?> <i class="fa fa-caret-down"></i></a></h4>
    </div>
    <div id="collapse-customvoucher" class="panel-collapse collapse">
        <div class="panel-body">
            <div class="input-group">
                <?php echo $entry_customvoucher; ?>
                <span class="input-group-btn">
                    <input type="button" value="<?php echo $button_customvoucher; ?>" id="button-customvoucher" data-loading-text="<?php echo $text_loading; ?>"  class="btn btn-primary" />
                </span>
            </div>
            <script type="text/javascript"><!--
                $('#button-customvoucher').on('click', function() {
                    $.ajax({
                        url: 'index.php?route=extension/total/customvoucher/customvoucher',
                        type: 'post',
                        beforeSend: function() {
                            $('#button-customvoucher').button('loading');
                        },
                        complete: function() {
                            $('#button-customvoucher').button('reset');
                        },
                        success: function(json) {
                            $('.alert').remove();

                            if (json['error']) {
                                $('.breadcrumb').after('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');

                                $('html, body').animate({ scrollTop: 0 }, 'slow');
                            }

                            if (json['redirect']) {
                                location = json['redirect'];
                            }
                        }
                    });
                });
                //--></script>
        </div>
    </div>
</div>
