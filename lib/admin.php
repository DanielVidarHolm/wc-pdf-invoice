<?php



function wpi_add_invoice_links_to_order( $order ) {
    $invoice_pdf_url = $order->get_meta('wpi_invoice_pdf_url');
    if ( $invoice_pdf_url ) {
        ?>
        <div class="invoice-links" style="margin-top:20px;">
            <!-- Download Invoice Button -->
            <a href="<?php echo esc_url( $invoice_pdf_url ); ?>" download class="button" style="margin-top:20px;">
                Download Invoice
            </a>

            <!-- Print Invoice Button -->
            <a href="<?php echo esc_url( $invoice_pdf_url ); ?>" target="_blank" class="button" id="print-invoice" style="margin-top:20px;">
                Print Invoice
            </a>

            <!-- View Invoice Button -->
            <a href="<?php echo esc_url( $invoice_pdf_url ); ?>" target="_blank" class="button" style="margin-top:20px;">
                View Invoice
            </a>

            <?php if($order->status === 'completed'){ ?>
                <form method="POST" action="/">
                    <input type="submit" class="button" name="send_email" value="Send email" style="margin-top:20px;">
                </form>
            <?php } ?>

            <?php

            if($_SERVER['REQUEST_METHOD'] === 'POST') {
                if(isset($_POST["send_email"])){
                    $email_oc = new WC_Email_Customer_Completed_Order();
                    $email_oc->trigger($order->id);
                }
            }

            ?>

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