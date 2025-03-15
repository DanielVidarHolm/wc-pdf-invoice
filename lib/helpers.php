<?php

use Picqer\Barcode\BarcodeGeneratorPNG;
use Dompdf\Dompdf;

add_shortcode('wpi_test_invoice', 'wpi_test_invoice_shortcode');
function wpi_test_invoice_shortcode() {

    // valid order ID
    $order_id =  40929;
    $order = wc_get_order($order_id);
    if (!$order) {
        return 'Order not found. Please use a valid order ID.';
    }

    // setting up a option to store the current sum of invoices
    add_option('wpi_invoice_count', 3000);


    // updating the sum of invoices
    $currentInvoiceCount = get_option('wpi_invoice_count');
    $newInvoiceCount = $currentInvoiceCount + 1;
    update_option('wpi_invoice_count', $newInvoiceCount);



    // adding the invoice number and date to the order object
    $order->update_meta_data('wpi_invoice_number', get_option('wpi_invoice_count'));
    $order->save();
    $order->update_meta_data('wpi_invoice_date',wp_date('j M, Y'));
    $order->save();

    // logo id
    $logo_path = get_attached_file(24643);


    // Read the image file contents.
    $logo_data = file_get_contents($logo_path);
    $logo_base64 = base64_encode($logo_data);


    echo $order->get_meta('wpi_invoice_number');
    $barcodeGenerator = new BarcodeGeneratorPNG();
    $barcodeData = $barcodeGenerator->getBarcode($order->get_meta('wpi_invoice_number'), $barcodeGenerator::TYPE_CODE_128);
    $barcodeBase64 = base64_encode($barcodeData);

    // using function to generate the html
    $html = generate_invoice_html($order, $barcodeBase64, $logo_base64);

    // Return the HTML so it renders on the page.
    $dompdf = new Dompdf([ 'chroot' => 'C:\laragon\www\teledele\wp-content\plugins\wc-pdf-invoice' ]);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');

    $dompdf->render();
    $pdf_output = $dompdf->output();

    // Encode the PDF so we can embed it in a data URI.
    $pdf_base64 = base64_encode($pdf_output);
    $pdf_url = 'data:application/pdf;base64,' . $pdf_base64;

    // Return an anchor that opens the PDF in a new tab.
    echo '<a href="' . $pdf_url . '" target="_blank">Open Invoice PDF in New Tab</a>';
    return $html;
}