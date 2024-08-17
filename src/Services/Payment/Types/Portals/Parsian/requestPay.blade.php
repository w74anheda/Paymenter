<body onload='document.forms["form"].submit()'>
    <h3>redirect to parsian gateway</h3>

    <form  style="display: none"  name="form" action="{{ $formRequestUrl }}" method="POST">
        <input type="hidden" name="token" value="{{ $token }}">
    </form>
</body>
