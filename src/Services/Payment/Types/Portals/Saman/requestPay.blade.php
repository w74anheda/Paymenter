<body onload='document.forms["form"].submit()'>
    <h3>redirect to saman gateway</h3>

    <form  style="display: none"  name="form" action="{{ $requestURL }}" method="POST">

        <input name="TerminalId" value="{{ $terminalId }}">
        <input name="RedirectUrl" value="{{ $callBackUrl }}">
        <input name="ResNum" value="{{ $paymentTransaction->resNum }}">
        <input name="Amount" value="{{ $paymentTransaction->amount }}">
        {{--  <input name="CellNumber" value="">  --}}

    </form>
</body>
