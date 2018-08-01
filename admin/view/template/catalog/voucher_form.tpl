<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="form-voucher" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
                <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
            <h1><?php echo $heading_title; ?></h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="container-fluid">
        <?php if ($error_warning) { ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?></h3>
            </div>
            <div class="panel-body">
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-voucher" class="form-horizontal">
                    <div class="form-group required">
                        <label class="col-sm-2 control-label" for="input-total"><span data-toggle="tooltip" title="<?php echo $help_total; ?>"><?php echo $entry_total; ?></span></label>
                        <div class="col-sm-10">
                            <input type="text" name="total" value="<?php echo $total; ?>" placeholder="<?php echo $entry_total; ?>" id="input-total" class="form-control" />
                            <?php if ($error_total) { ?>
                            <div class="text-danger"><?php echo $error_total; ?></div>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="form-group required">
                        <label class="col-sm-2 control-label" for="input-value"><span data-toggle="tooltip" title="<?php echo $help_value; ?>"><?php echo $entry_value; ?></span></label>
                        <div class="col-sm-10">
                            <input type="text" name="value" value="<?php echo $value; ?>" placeholder="<?php echo $entry_value; ?>" id="input-value" class="form-control" />
                            <?php if ($error_value) { ?>
                            <div class="text-danger"><?php echo $error_value; ?></div>
                            <?php } ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php echo $footer; ?>
