<?php

use Picqer\Barcode\BarcodeGeneratorPNG;
use Dompdf\Dompdf;

// hooking into woocommerce complete order hook
add_action('woocommerce_order_status_completed', 'wpi_generate_invoice_pdf',10 , 1);
function wpi_generate_invoice_pdf($order_id){
    // get the order with wc get order method
    $order = wc_get_order($order_id);

    if(!$order){
        return;
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

    // Get the file path for the attachment with ID 39099.
    $logo_path = get_attached_file(24643);


    // Read the image file contents.
    $logo_data = file_get_contents($logo_path);
    $logo_base64 = base64_encode($logo_data);

    $barcodeGenerator = new BarcodeGeneratorPNG();
    $barcodeData = $barcodeGenerator->getBarcode($order->get_meta('wpi_invoice_number'), $barcodeGenerator::TYPE_CODE_128);
    $barcodeBase64 = base64_encode($barcodeData);



    // using function to generate the html
    $html = generate_invoice_html($order, $barcodeBase64, $logo_base64);

    // creatomg a new instance of Dompdf
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $pdf_output = $dompdf->output();
    $upload_dir = wp_upload_dir();
    $invoice_dir = $upload_dir['basedir'] . '/invoices';

    if (!file_exists($invoice_dir)){
        wp_mkdir_p($invoice_dir);
    }

    // Creating and storing the file in the upload directory
    $pdf_file = $invoice_dir . '/invoice-' . $order_id . '.pdf';
    file_put_contents($pdf_file, $pdf_output);

    // get the file url to store with the order
    $pdf_url = $upload_dir['baseurl'] . '/invoices/invoice-' . $order_id . '.pdf';

    // save the url with the order in the database

    $order->update_meta_data('wpi_invoice_pdf_url', esc_url_raw($pdf_url));
    $order->save();


}

function generate_invoice_html($order, $barcodeBase64, $logoBase64) {
    ob_start();
    // Make the barcode variable available in the template
    $barcode = $barcodeBase64;
    $logo = $logoBase64;
    include(PLUGIN_ROOT . '\templates\invoice-template.php');
    return ob_get_clean();
}