<!DOCTYPE html>
<html>
<head>
    <script src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="{{ config('midtrans.client_key') }}"></script>
</head>
<body>

<script>
    window.onload = function () {
        snap.pay('{{ $snapToken }}', {
            onSuccess: function(result){
                window.location.href = "/payment/result";
            },
            onPending: function(result){
                console.log(result);
            },
            onError: function(result){
                console.log(result);
            }
        });
    };
</script>

</body>
</html>
