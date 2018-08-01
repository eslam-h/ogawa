<!DOCTYPE html>
<html dir = "<?php echo $direction; ?>" lang = "<?php echo $lang; ?>" xmlns = "http://www.w3.org/1999/html" >
<head>
<meta charset="UTF-8" />
<title><?php echo $title; ?></title>
<base href="<?php echo $base; ?>" />
<link href="view/javascript/bootstrap/css/bootstrap.css" rel="stylesheet" media="all" />
<script type="text/javascript" src="view/javascript/jquery/jquery-2.1.1.min.js"></script>
<script type="text/javascript" src="view/javascript/bootstrap/js/bootstrap.min.js"></script>
<link href="view/javascript/font-awesome/css/font-awesome.min.css" type="text/css" rel="stylesheet" />
<link type="text/css" href="view/stylesheet/stylesheet.css" rel="stylesheet" media="all" />
</head>
<body>
<div class="container">
  <?php foreach ($orders as $order) { ?>
  <div style="page-break-after: always;">
    <div class="pull-left"><img src="view/image/inner-logo.png" alt="<?php echo $heading_title; ?>" title="<?php echo $heading_title; ?>" /></div>
    <h1 class="pull-right"><?php echo $text_invoice; ?> #<?php echo $order['order_id']; ?></h1>
    <table class="table table-bordered">
      <thead>
        <tr>
          <td colspan="2"><?php echo $text_order_detail; ?></td>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td style="width: 50%;"><address>
            <strong><?php echo $order['store_name']; ?></strong><br />
            <?php echo $order['store_address']; ?>
            </address>
            <b><?php echo $text_telephone; ?></b> <?php echo $order['store_telephone']; ?><br />
            <?php if ($order['store_fax']) { ?>
            <b><?php echo $text_fax; ?></b> <?php echo $order['store_fax']; ?><br />
            <?php } ?>
            <b><?php echo $text_email; ?></b> <?php echo $order['store_email']; ?><br />
            <b><?php echo $text_website; ?></b> <a href="<?php echo $order['store_url']; ?>"><?php echo $order['store_url']; ?></a></td>
          <td style="width: 50%;"><b><?php echo $text_date_added; ?></b> <?php echo $order['date_added']; ?><br />
            <?php if ($order['invoice_no']) { ?>
            <b><?php echo $text_invoice_no; ?></b> <?php echo $order['invoice_no']; ?><br />
            <?php } ?>
            <b><?php echo $text_order_id; ?></b> <?php echo $order['order_id']; ?><br />
            <b><?php echo $text_payment_method; ?></b> <?php echo $order['payment_method']; ?><br />
            <?php if ($order['shipping_method']) { ?>
            <b><?php echo $text_shipping_method; ?></b> <?php echo $order['shipping_method']; ?><br />
            <?php } ?></td>
        </tr>
      </tbody>
    </table>
    <table class="table table-bordered">
      <thead>
        <tr>
          <td style="width: 50%;"><b><?php echo $text_payment_address; ?></b></td>
          <td style="width: 50%;"><b><?php echo $text_shipping_address; ?></b></td>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><address>
            <?php echo $order['payment_address']; ?>
            </address></td>
          <td><address>
            <?php echo $order['shipping_address']; ?>
            </address></td>
        </tr>
      </tbody>
    </table>
    <table class="table table-bordered">
      <thead>
        <tr>
          <td><b><?php echo $column_product; ?></b></td>
          <td><b><?php echo $column_model; ?></b></td>
          <td class="text-right"><b><?php echo $column_quantity; ?></b></td>
          <td class="text-right"><b><?php echo $column_price; ?></b></td>
          <td class="text-right"><b><?php echo $column_total; ?></b></td>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($order['product'] as $product) { ?>
        <tr>
          <td><?php echo $product['name']; ?>
            <?php foreach ($product['option'] as $option) { ?>
            <br />
            &nbsp;<small> - <?php echo $option['name']; ?>: <?php echo $option['value']; ?></small>
            <?php } ?></td>
          <td><?php echo $product['model']; ?></td>
          <td class="text-right"><?php echo $product['quantity']; ?></td>
          <td class="text-right"><?php echo $product['price']; ?></td>
          <td class="text-right"><?php echo $product['total']; ?></td>
        </tr>
        <?php } ?>
        <?php foreach ($order['voucher'] as $voucher) { ?>
        <tr>
          <td><?php echo $voucher['description']; ?></td>
          <td></td>
          <td class="text-right">1</td>
          <td class="text-right"><?php echo $voucher['amount']; ?></td>
          <td class="text-right"><?php echo $voucher['amount']; ?></td>
        </tr>
        <?php } ?>
        <?php foreach ($order['total'] as $total) { ?>
        <tr>
          <td class="text-right" colspan="4"><b><?php echo $total['title']; ?></b></td>
          <td class="text-right"><?php echo $total['text']; ?></td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
    <?php if ($order['comment']) { ?>
    <table class="table table-bordered">
      <thead>
        <tr>
          <td><b><?php echo $text_comment; ?></b></td>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><?php echo $order['comment']; ?></td>
        </tr>
      </tbody>
    </table>
    <?php } ?>


    <div class="row" style="font-size: 14px">


      <div class="col-lg-6">
        <p>
          <strong>Replacement and Retrieval policy for online order</strong>
          <br>
          Replacement and return policy within 7 days from the date of delivery with the original invoice, if products are in their original condition with original packaging.
          <br>
          Customer pays the shipping fees in case of the absence of manufacture defect of the return products.
          <br>
          In the case of replacement, the product must has an industry defect, replacing it with new product or another product at the same price.
          <br>
          The product is guaranteed for one year from the date of delivery against defects.
          <br>
          Products purchased using gift certificate, voucher, or credit card cannot be refunded in cash.
          <br>
          For any inquires or questions, contact us
          <br>
          Customer service No.: +974 6660 9755
        </p>
      </div>

      <div style="direction: rtl" class="col-lg-6">
        <p>
<strong>      سياسة الاستبدال والاسترجاع للمشتريات عن طريق الموقع الالكترونى </strong>
          <br>
          سياسة الاسترجاع والاستبدال خلال سبعة (7) أيام من تاريخ الاستلام مع احضار أصل الفاتورة، على ان يكون المنتج بحالته الأصلية عند الشراء ومغلفا بالغلاف الأصلي.
          <br>
          يتحمل العميل مصاريف الشحن فى حالة عدم وجود عيب صناعة فى المنتج.
          <br>
          الجهاز مضمون لمدة عام واحد من تاريخ الاستلام ضد عيوب.
          <br>
          المشتريات المدفوعة بقسائم شرائية أو بطاقات الإئتمان لا يمكن إرجاعها نقدا.
          <br>
          إذا كان لديك أي استفسارات أو أسئلة يرجى الاتصال بنا
          <br>
          رقم خدمة العملاء:9755 6660 974+

        </p>
      </div>


    </div>

    <br><br>



  </div>
  <?php } ?>
</div>
</body>
</html>