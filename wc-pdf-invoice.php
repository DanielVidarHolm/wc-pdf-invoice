<?php
/**
 * Plugin Name: WooCommerce PDF Invoice Generator
 * Description: Generates PDF invoices for WooCommerce orders using Dompdf.
 * Version: 1.0
 * Author: Daniel Holm
 */

require_once __DIR__ . '/vendor/autoload.php';

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
    
    // adding the invoice number to the order object
    $order->update_meta_data('wpi_invoice_number', get_option('wpi_invoice_count'));
    $order->save();

    // Get the file path for the attachment with ID 39099.
    $logo_path = get_attached_file(39099);


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

    // dompdf settings
    $dompdf->setPaper('A4', 'portrait');

    // outputting
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
    include(plugin_dir_path(__FILE__) . 'templates/invoice-template.php');
    return ob_get_clean();
}

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
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $pdf_output = $dompdf->output();

    // Encode the PDF so we can embed it in a data URI.
    $pdf_base64 = base64_encode($pdf_output);
    $pdf_url = 'data:application/pdf;base64,' . $pdf_base64;

    // Return an anchor that opens the PDF in a new tab.
    // return '<a href="' . $pdf_url . '" target="_blank">Open Invoice PDF in New Tab</a>';
    return $html;
}

/**
 * Display invoice PDF links on the order details page.
 */
function wpi_add_invoice_links_to_order( $order ) {
    $invoice_pdf_url = $order->get_meta('wpi_invoice_pdf_url');
    if ( $invoice_pdf_url ) {
        ?>
        <div class="invoice-links" style="margin-top:20px;">
            <!-- Download Invoice Button -->
            <a href="<?php echo esc_url( $invoice_pdf_url ); ?>" download class="button">
                Download Invoice
            </a>

            <!-- Print Invoice Button -->
            <a href="<?php echo esc_url( $invoice_pdf_url ); ?>" target="_blank" class="button" id="print-invoice">
                Print Invoice
            </a>

            <!-- View Invoice Button -->
            <a href="<?php echo esc_url( $invoice_pdf_url ); ?>" target="_blank" class="button">
                View Invoice
            </a>
        </div>
        <script>
            // Attach a click event to the print button that opens the PDF and triggers printing.
            document.getElementById('print-invoice').addEventListener('click', function(e) {
                e.preventDefault();
                var win = window.open(this.href, '_blank');
                win.addEventListener('load', function(){
                    win.print();
                });
            });
        </script>
        <?php
    }
}
add_action( 'woocommerce_admin_order_data_after_order_details', 'wpi_add_invoice_links_to_order', 10, 1 );


/**
 * Attach PDF Invoice to the WooCommerce Completed Order Email.
 *
 * @param array         $attachments Existing email attachments.
 * @param string        $email_id    Email identifier.
 * @param WC_Order|bool $order       The order object.
 * @param WC_Email      $email       The email instance.
 *
 * @return array Modified attachments array.
 */

function wpi_attach_invoice_pdf_to_email( $attachments, $email_id, $order, $email ) {
    if ( 'customer_completed_order' === $email_id && is_a( $order, 'WC_Order' ) ) {
        $pdf_url = $order->get_meta( 'wpi_invoice_pdf_url' );
        if ( $pdf_url ) {
            $upload_dir   = wp_upload_dir();
            $invoice_file = str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], $pdf_url );
            if ( file_exists( $invoice_file ) ) {
                $attachments[] = $invoice_file;
            }
        }
    }
    return $attachments;
}
add_filter( 'woocommerce_email_attachments', 'wpi_attach_invoice_pdf_to_email', 10, 4 );