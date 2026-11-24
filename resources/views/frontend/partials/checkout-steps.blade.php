<ol class="checkout-steps" aria-label="Checkout progress">
    <li class="{{ $step >= 1 ? 'is-done' : '' }} {{ $step === 1 ? 'is-current' : '' }}"><span>1</span> Cart</li>
    <li class="{{ $step >= 2 ? 'is-done' : '' }} {{ $step === 2 ? 'is-current' : '' }}"><span>2</span> Checkout</li>
    <li class="{{ $step >= 3 ? 'is-done' : '' }} {{ $step === 3 ? 'is-current' : '' }}"><span>3</span> Confirmation</li>
</ol>
