<form id="wompiForm" action="https://checkout.wompi.co/p/" method="GET">
    <input type="hidden" name="public-key" value="<?= $public_key ?>" />
    <input type="hidden" name="currency" value="<?= $currency ?>" />
    <input type="hidden" name="amount-in-cents" value="<?= $amount_in_cents ?>" />
    <input type="hidden" name="reference" value="<?= $reference ?>" />
    <input type="hidden" name="signature:integrity" value="<?= $signature ?>" />

    <!-- Datos opcionales -->
    <input type="hidden" name="redirect-url" value="<?= $redirect_url ?>" />
    <input type="hidden" name="customer-data:email" value="<?= $email ?>" />
    <input type="hidden" name="customer-data:full-name" value="<?= $nombre ?>" />
    <input type="hidden" name="customer-data:phone-number" value="<?= $telefono ?>" />
    <input type="hidden" name="customer-data:legal-id" value="<?= $documento ?>" />
    <input type="hidden" name="customer-data:legal-id-type" value="CC" />
</form>

<script>
    document.getElementById("wompiForm").submit();
</script>
