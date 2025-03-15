<?php
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