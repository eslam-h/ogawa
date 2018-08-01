<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="form-faqcategory" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-gallerygategory" class="form-horizontal">
                    <div class="tab-content">
                        <div class="tab-pane active in" id="tab-general">

                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-sort-order"><?php echo $entry_sort_order; ?></label>
                                <div class="col-sm-10">
                                    <input type="text" name="sort_order" value="<?php echo $sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
                                <div class="col-sm-10">
                                    <select name="status" id="input-status" class="form-control">
                                        <?php if ($status) { ?>
                                        <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                        <option value="0"><?php echo $text_disabled; ?></option>
                                        <?php } else { ?>
                                        <option value="1"><?php echo $text_enabled; ?></option>
                                        <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <ul class="nav nav-tabs" id="language">
                                <?php foreach ($languages as $language) { ?>
                                <li><a href="#language<?php echo $language['language_id']; ?>" data-toggle="tab"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /> <?php echo $language['name']; ?></a></li>
                                <?php } ?>
                            </ul>
                            <div class="tab-content">
                                <?php foreach ($languages as $language) { ?>
                                <div class="tab-pane" id="language<?php echo $language['language_id']; ?>">
                                    <div class="form-group required">
                                        <label class="col-sm-2 control-label" for="input-name<?php echo $language['language_id']; ?>"><?php echo $entry_title; ?></label>
                                        <div class="col-sm-10">
                                            <input type="text" name="gallery_category_description[<?php echo $language['language_id']; ?>][title]" value="<?php echo isset($gallery_category_description[$language['language_id']]) ? $gallery_category_description[$language['language_id']]['title'] : ''; ?>" placeholder="<?php echo $entry_title; ?>" id="input-title<?php echo $language['language_id']; ?>" class="form-control" />
                                            <?php if (isset($error_title[$language['language_id']])) { ?>
                                            <div class="text-danger"><?php echo $error_title[$language['language_id']]; ?></div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script type="text/javascript"><!--
        <?php foreach ($languages as $language) { ?>
            $('#input-description<?php echo $language['language_id']; ?>').summernote({
                height: 300
            });
        <?php } ?>
        //--></script>
    <script type="text/javascript"><!--
        $('input[name=\'path\']').autocomplete({
            'source': function(request, response) {
                $.ajax({
                    url: 'index.php?route=catalog/faqcategory/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
                    dataType: 'json',
                    success: function(json) {
                        json.unshift({
                            faqcategory_id: 0,
                            name: '<?php echo $text_none; ?>'
                        });

                        response($.map(json, function(item) {
                            return {
                                label: item['name'],
                                value: item['faqcategory_id']
                            }
                        }));
                    }
                });
            },
            'select': function(item) {
                $('input[name=\'path\']').val(item['label']);
                $('input[name=\'parent_id\']').val(item['value']);
            }
        });
        //--></script>
    <script type="text/javascript"><!--
        $('input[name=\'filter\']').autocomplete({
            'source': function(request, response) {
                $.ajax({
                    url: 'index.php?route=catalog/filter/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
                    dataType: 'json',
                    success: function(json) {
                        response($.map(json, function(item) {
                            return {
                                label: item['name'],
                                value: item['filter_id']
                            }
                        }));
                    }
                });
            },
            'select': function(item) {
                $('input[name=\'filter\']').val('');

                $('#faqcategory-filter' + item['value']).remove();

                $('#faqcategory-filter').append('<div id="faqcategory-filter' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="faqcategory_filter[]" value="' + item['value'] + '" /></div>');
            }
        });

        $('#faqcategory-filter').delegate('.fa-minus-circle', 'click', function() {
            $(this).parent().remove();
        });
        //--></script>
    <script type="text/javascript"><!--
        $('#language a:first').tab('show');
        //--></script></div>
<?php echo $footer; ?>
