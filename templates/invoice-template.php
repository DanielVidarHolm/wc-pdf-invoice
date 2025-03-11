<?php
/**
 * Invoice Template
 */


// Retrieve store details.
$store_address      = get_option('woocommerce_store_address');
$store_city         = get_option('woocommerce_store_city');
$store_postcode     = get_option('woocommerce_store_postcode');
$store_name         = get_bloginfo('name');
$store_email        = get_option('woocommerce_email_from_address'); 

// Retrieve invoice and order details.
$invoice_number     = $order->get_meta('wpi_invoice_number');
$invoice_date       = $order->get_meta('wpi_invoice_date');
$order_number       = $order->get_order_number();
$order_date_temp    = new DateTime($order->get_date_paid());
$order_date         = $order_date_temp->format('j M, Y');

// Retrieve billing details.
$billing_fname      = $order->get_billing_first_name();
$billing_lname      = $order->get_billing_last_name();      
$billing_full_name  = $billing_fname . ' ' . $billing_lname;
$billing_address    = $order->get_billing_address_1();
$billing_address2   = $order->get_billing_address_2();
$billing_postcode   = $order->get_billing_postcode();
$billing_city       = $order->get_billing_city();
$billing_phone      = $order->get_billing_phone();
$billing_email      = $order->get_billing_email();
$billing_company    = $order->get_billing_company();

// Retrieve shipping details.
$shipping_fname      = $order->get_shipping_first_name();
$shipping_lname      = $order->get_shipping_last_name();      
$shipping_full_name  = $shipping_fname . ' ' . $shipping_lname;
$shipping_address    = $order->get_shipping_address_1();
$shipping_address2   = $order->get_shipping_address_2();
$shipping_postcode   = $order->get_shipping_postcode();
$shipping_city       = $order->get_shipping_city();
$shipping_phone      = $order->get_shipping_phone();
$shipping_company    = $order->get_shipping_company();

// Retrieve delivery details if available.
if ( $order->get_meta('shipmondo_pickup_point') ) {
    $delivery_data   = $order->get_meta('shipmondo_pickup_point');
    $delivery_name   = $delivery_data['name'];
    $delivery_address= $delivery_data['address_1'];
    $delivery_city   = $delivery_data['city'];
    $delivery_postcode = $delivery_data['postcode'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #<?= esc_html($order_number); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        h1 {
            font-size: 28px;
            line-height:100%;
            vertical-align:top;
        }
        h2 {
            font-size: 16px;
            margin: 0 0 10px 0;
        }
        p {
            margin: 2px 0;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        .header-table, .address-table, .item-table, .footer-table {
            margin-bottom: 20px;
        }
        /* Header styling */
        .header-table td {
            vertical-align: top;
        }
        .header-left {

        }
        .header-right {

            text-align: right;
        }
        /* Store & invoice details */
        .store-info ul, .address-section ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .store-info li, .address-section li {
            margin-bottom: 0px
        }
        /* Items table */
        .item-table th, .item-table td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .item-table th {
            background-color: #f2f2f2;
        }
        .item-table td {
            text-align: left;
        }
        .item-table td.numeric {
            text-align: right;
        }
        /* Footer */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            border-top: 1px solid rgb(221, 221, 221);
            padding-top: 5px;
        }
        .small-text {
            font-size: 10px;
        }
        .barcode > img{
            width:200px;
            height:auto;
        }
        .layout-row > * {
            display: inline-block;
        }
        .layout-row > *:nth-child(n + 2){
            margin-left:20px;
        }
        .layout-col > * {
            display: block;
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <table class="header-table">
        <tr>
            <!-- Invoice details -->
            <td class="header-left">
                <div class="layout-row">
                    <h1>Faktura</h1>
                    <div class="layout-row">
                        <div class="layout-col">
                            <p>Faktura nr: <?= esc_html($invoice_number); ?></p>
                            <p>Faktura dato: <?= esc_html($invoice_date); ?></p>
                        </div>

                        <div class="layout-col">
                            <p>Ordre nr: <?= esc_html($order_number); ?></p>
                            <p>Ordre dato: <?= esc_html($order_date); ?></p>
                        </div>


                    </div>
            
                </div>
                <img src="data:image/png;base64,<?= $logo; ?>" alt="Logo" style="margin-left:-25px;">
                
            </td>

            <!-- Logo and Barcode -->
            <td class="header-right">
           
                <div class="barcode">
                    <img src="data:image/png;base64,<?= $barcode; ?>" alt="Barcode">
                </div>
            </td>
        </tr>
    </table>

    <!-- Store Information -->
    <table class="header-table">
        <tr>
            <td>
                <h2>Firmaoplysninger:</h2>
                <div class="store-info">
                    <ul>
                        <li><?= esc_html($store_name); ?></li>
                        <li><?= esc_html($store_address); ?></li>
                        <li><?= esc_html($store_postcode . ' ' . $store_city); ?></li>
                        <li>+45 70 77 77 87</li>
                        <li>CVR: 41956976</li>
                    </ul>
                </div>
            </td>
        </tr>
    </table>

    <!-- Addresses Section -->
    <table class="address-table">
        <tr>
            <!-- Billing Address -->
            <td class="address-section" style="width: 33%; vertical-align: top;">
                <h2>Betalingsadresse:</h2>
                <ul>
                    <li><?= esc_html($billing_full_name); ?></li>
                    <?php if ($billing_company) { ?><li><?= esc_html($billing_company); ?></li><?php } ?>
                    <li><?= esc_html($billing_address); ?></li>
                    <?php if ($billing_address2) { ?><li><?= esc_html($billing_address2); ?></li><?php } ?>
                    <li><?= esc_html($billing_postcode . ' ' . $billing_city); ?></li>
                    <li><?= esc_html($billing_phone); ?></li>
                    <li><?= esc_html($billing_email); ?></li>
                </ul>
            </td>
            <!-- Shipping Address -->
            <td class="address-section" style="width: 33%; vertical-align: top;">
                <h2>Leveringsadresse:</h2>
                <ul>
                    <li><?= esc_html($shipping_full_name); ?></li>
                    <?php if ($shipping_company) { ?><li><?= esc_html($shipping_company); ?></li><?php } ?>
                    <li><?= esc_html($shipping_address); ?></li>
                    <?php if ($shipping_address2) { ?><li><?= esc_html($shipping_address2); ?></li><?php } ?>
                    <li><?= esc_html($shipping_postcode . ' ' . $shipping_city); ?></li>
                    <li><?= esc_html($shipping_phone); ?></li>
                </ul>
            </td>
            <!-- Delivery Address (if available) -->
            <?php if ( isset($delivery_name) && $delivery_name ) { ?>
            <td class="address-section" style="width: 34%; vertical-align: top;">
                <h2>Udleveringssted:</h2>
                <ul>
                    <li><?= esc_html($delivery_name); ?></li>
                    <li><?= esc_html($delivery_address); ?></li>
                    <li><?= esc_html($delivery_postcode . ' ' . $delivery_city); ?></li>
                </ul>
            </td>
            <?php } ?>
        </tr>
    </table>

    <!-- Items Table -->
    <table class="item-table">

        <thead>
            <tr>
                <th>Varenummer</th>
                <th>Produkt</th>
                <th style="text-align: right; width:10%;">Antal</th>
                <th style="text-align: right; width:10%;">Pris</th>
                <th style="text-align: right; width:20%;">Total pris</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ( $order->get_items() as $item ) {
                $product = $item->get_product();
                if ( $product ) { ?>
                  
                    <tr>
                        <td><?= esc_html($product->get_sku()); ?></td>
                        <td><?= esc_html($item->get_name()); ?></td>
                        <td class="numeric"><?= esc_html($item->get_quantity()); ?></td>
                        <td class="numeric"><?= wc_price($product->get_price()); ?></td>
                        <td class="numeric"><?= wc_price($item->get_total() + $item->get_subtotal_tax()); ?></td>
                    </tr>
               
                  

            <?php }} ?>
            <tr style="border-top:2px solid rgb(196, 196, 196);">
                <td style="border:none;" colspan="3"></td>
                <th style="text-align: right;">SUBTOTAL</th>
                <td class="numeric"><?= $order->get_subtotal_to_display(); ?> <span class="small-text">(inkl moms)</span></td>
            </tr>
            <tr>
                <td style="border:none;" colspan="3"></td>
                <th style="text-align: right;">FORSENDELSE</th>
                <td class="numeric">
                    <?= wc_price($order->get_shipping_total() + $order->get_shipping_tax()); ?>
                    <br>
                    <span class="small-text"><?= esc_html($order->get_shipping_method()); ?></span>
                </td>
            </tr>
            <tr style="border-top:3px solid rgb(138, 138, 138);">
                <td style="border:none;" colspan="3"></td>
                <th style="text-align: right;">TOTAL</th>
                <td class="numeric">
                    <?= wc_price($order->get_total()); ?>
                    <br>
                    <span class="small-text">(inkl moms <?= wc_price($order->get_total_tax()); ?>)</span>
                </td>
            </tr>
        </tbody>
    </table>
    <p>Betalingsmetode: <strong><?= esc_html($order->get_payment_method_title()); ?></strong></p>
    <!-- Footer Section -->
    <div class="footer">
        
        <p>Fakturaen gælder som købsbevis. Købsbeviset skal fremlægges i forbindelse med anvendelse af reklamationsretten.</p>
        <p><?= esc_html($store_name); ?> | <?= esc_html($store_address); ?>, <?= esc_html($store_postcode . ' ' . $store_city); ?> | Tlf.: +45 70 77 77 87 | Email: info@teledele.dk</p>
    </div>
</body>
</html>
