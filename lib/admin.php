<?php

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