<?php
/**
 * Invoice Template
 */
if(empty($order)){
    return;
}
if(empty($logo)){
    return;
}
if(empty($barcode)){
    return;
}

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
$customer_number    = $order->get_customer_id();
$currency_code      = $order->get_currency();
$payment_method     = $order->get_payment_method_title();
$total_with_tax     = $order->get_total();
$total_tax          = $order->get_total_tax();
$total_without_tax  = $order->get_total() - $total_tax;

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

$shipping_address    = $order->get_shipping_address_1();
$shipping_address2   = $order->get_shipping_address_2();
$shipping_postcode   = $order->get_shipping_postcode();
$shipping_city       = $order->get_shipping_city();
$shipping_company    = $order->get_shipping_company();

$shipping_method     = $order->get_shipping_method();

// Retrieve delivery details if available.
if ($shipping_method === "PostNord MyPack Collect") {
    $delivery_data   = $order->get_meta('shipmondo_pickup_point');
    $shipping_name   = $delivery_data['name'];
    $shipping_address= $delivery_data['address_1'];
    $shipping_city   = $delivery_data['city'];
    $shipping_postcode = $delivery_data['postcode'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #<?= esc_html($order_number); ?></title>
    <style>
        <?php echo file_get_contents(__DIR__ . './../assets/styles.css'); ?>
    </style>
</head>
<body>
    <!-- Header Section -->
    <table class="header-table">
        <tr>
            <!-- Invoice details -->
            <td class="header-left">
                <div class="logo-container">
                    <img src="data:image/png;base64,<?= $logo; ?>" alt="Logo" style="">
    
                    <div class="store-info">
                        
                        <span><?= esc_html($store_name); ?> - </span>
                        <span>+45 70 77 77 87 - </span>
                        <span>CVR: 41956976</span>
                    
                    </div>
                </div>
              
                
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
    <table class="info-table">
        <tbody>

            <tr>
                <!-- Billing Address -->
                <td class="address-section">

                    <div class="billing">
                        <h2>Billing</h2>
                        <table class="billing-table">
                            <tr>
                                <td class="key-cell">Navn:</td>
                                <td class="value-cell"><?= esc_html($billing_full_name); ?></td>
                            </tr>
                            <tr>
                                <td class="key-cell">Address:</td>
                                <td class="value-cell"><?= esc_html($billing_address); ?></td>
                            </tr>
                            <tr>
                                <td class="key-cell">By:</td>
                                <td class="value-cell"><?= esc_html($billing_postcode . ' ' . $billing_city); ?></td>
                            </tr>
                            <tr>
                                <td class="key-cell">Telefon:</td>
                                <td class="value-cell"><?= esc_html($billing_phone); ?></td>
                            </tr>
                            <tr>
                                <td class="key-cell">E-mail:</td>
                                <td class="value-cell"><?= esc_html($billing_email); ?></td>
                            </tr>
                        </table>
                    </div>

                    <!-- Shipping -->
                    <div class="billing">
                        <h2>Levering</h2>
                        <table class="billing-table">
                            <tr>
                                <td class="key-cell">Metode:</td>
                                <td class="value-cell"><?= esc_html($order->get_shipping_method()); ?></td>
                            </tr>
                            <?php if ($shipping_method === "PostNord MyPack Collect") { ?>
                            <tr>
                                <td class="key-cell">Navn:</td>
                                <td class="value-cell"><?= esc_html($shipping_name); ?></td>
                            </tr>
                            <?php } ?>
                            <tr>
                                <td class="key-cell">Address:</td>
                                <td class="value-cell"><?= esc_html($shipping_address); ?></td>
                            </tr>
                            <tr>
                                <td class="key-cell">By:</td>
                                <td class="value-cell"><?= esc_html($shipping_postcode . ' ' . $shipping_city); ?></td>
                            </tr>

                        </table>
                    </div>

                </td>

                <td class="faktura-section">
                    <div class="billing">
                        <h2>Faktura</h2>
                        <table class="billing-table">
                            <?php  ?>
                            <tr>
                                <td class="key-cell">Kunde nr:</td>
                                <td class="value-cell"><?= esc_html($customer_number); ?></td>
                            </tr>

                            <tr>
                                <td class="key-cell">Faktura nr:</td>
                                <td class="value-cell"><?= esc_html($invoice_number); ?></td>
                            </tr>

                            <tr>
                                <td class="key-cell">Faktura dato:</td>
                                <td class="value-cell"><?= esc_html($invoice_date); ?></td>
                            </tr>
                            <tr>
                                <td class="key-cell">Ordre nr:</td>
                                <td class="value-cell"><?= esc_html($order_number); ?></td>
                            </tr>
                            <tr>
                                <td class="key-cell">Ordre dato:</td>
                                <td class="value-cell"><?= esc_html($order_date); ?></td>
                            </tr>
                            <tr>
                                <td class="key-cell">Betalingsmetode:</td>
                                <td class="value-cell"><?= esc_html($payment_method); ?></td>
                            </tr>
                            <tr>
                                <td class="key-cell">Valuta:</td>
                                <td class="value-cell"><?= esc_html($currency_code); ?></td>
                            </tr>

                        </table>
                            
                        
                    </div>

                    
                </td>
            </tr>

        </tbody>
        
    </table>

    <!-- Items Table -->
    <table class="item-table">

        <thead>
            <tr class="item-table-header-row">
                <th align="left" class="item-table-header1">Varenummer</th>
                <th align="left" class="item-table-header2">Produkt</th>
                <th align="right" class="item-table-header3">Antal</th>
                <th align="right" class="item-table-header4">Pris</th>
                <th align="right" class="item-table-header5">Total pris</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ( $order->get_items() as $item ) {
                $product = $item->get_product();
                if ( $product ) { ?>
                  
                    <tr class="item-table-row">
                        <td align="left" class="item-table-cell1"><?= esc_html($product->get_sku()); ?></td>
                        <td align="left" class="item-table-cell2"><?= esc_html($item->get_name()); ?></td>
                        <td align="right" class="item-table-cell3"><?= esc_html($item->get_quantity()); ?></td>
                        <td align="right" class="item-table-cell4"><?= wc_price($product->get_price()); ?></td>
                        <td align="right" class="item-table-cell5"><?= wc_price($item->get_total() + $item->get_subtotal_tax()); ?></td>
                    </tr>

            <?php }} ?>
                    <tr class="item-table-row">
                        <td align="left" class="item-table-cell1">- - - - - - - - - -</td>
                        <td align="left" class="item-table-cell2"><?= esc_html($shipping_method); ?></td>
                        <td align="right" class="item-table-cell3"><?= esc_html('1'); ?></td>
                        <td align="right" class="item-table-cell4"><?= wc_price($order->get_shipping_total() + $order->get_shipping_tax()); ?></td>
                        <td align="right" class="item-table-cell5"><?= wc_price($order->get_shipping_total() + $order->get_shipping_tax()); ?></td>

                    </tr>

        </tbody>
    </table>
    <table>
        <tbody>
            <tr class="total-section">
                <td></td>
                <td class="total-table-container">
                    <table class="total-table">
                        <tr class="total-row">
                            <td align="left" class="total-key-cell">Total (uden moms)</td>
                            <td align="right" class="total-value-cell"><?= wc_price(esc_html($total_without_tax)); ?></td>
                        </tr>
                        <tr class="total-row">
                            <td align="left" class="total-key-cell">Total moms (25%)</td>
                            <td align="right" class="total-value-cell"><?= wc_price(esc_html($total_tax)); ?></td>
                        </tr>
                        <tr class="total-row last-row">
                            <td align="left" class="total-key-cell">Total (med moms)</td>
                            <td align="right" class="total-value-cell"><?= wc_price(esc_html($total_with_tax)); ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
    <!-- Footer Section -->
    <div class="footer">
        
        <p>Fakturaen gælder som købsbevis. Købsbeviset skal fremlægges i forbindelse med anvendelse af reklamationsretten.</p>
        <p><?= esc_html($store_name); ?> | <?= esc_html($store_address); ?>, <?= esc_html($store_postcode . ' ' . $store_city); ?> | Tlf.: +45 70 77 77 87 | Email: info@teledele.dk</p>
    </div>
</body>
</html>
