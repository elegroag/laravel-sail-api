{{-- SDK ePayco. Smart Checkout v2 requiere contexto seguro (HTTPS). --}}
@if (($EPAYCO_CHECKOUT_VERSION ?? config('app.epayco.checkout_version', '1')) === '2')
<script src="https://checkout.epayco.co/checkout-v2.js"></script>
@else
<script src="https://checkout.epayco.co/checkout.js"></script>
@endif
