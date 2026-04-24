<?php
function wpi_attach_invoice_pdf_to_email( $attachments, $email_id, $order, $email ) {

    if ( 'customer_completed_order' !== $email_id || ! $order instanceof WC_Order ) {
        return $attachments;
    }

    $pdf_url = $order->get_meta( 'wpi_invoice_pdf_url' );
    if ( ! $pdf_url ) {
        return $attachments;
    }
    $url_path = wp_parse_url( $pdf_url, PHP_URL_PATH );
    if ( ! $url_path ) {
        return $attachments;
    }

    $upload_dir   = wp_upload_dir();
    $uploads_path = wp_parse_url( $upload_dir['baseurl'], PHP_URL_PATH );

    if ( 0 === strpos( $url_path, $uploads_path ) ) {
        $relative = substr( $url_path, strlen( $uploads_path ) );
        $invoice_file = $upload_dir['basedir'] . $relative;
        if ( file_exists( $invoice_file ) ) {
            $attachments[] = $invoice_file;
        }
    }

    return $attachments;
}
add_filter( 'woocommerce_email_attachments', 'wpi_attach_invoice_pdf_to_email', 10, 4 );