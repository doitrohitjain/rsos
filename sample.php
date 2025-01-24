<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<?php

function encryptText($text, $key, $iv) {
    $cipher = "aes-256-cbc";
    $options = 0;

    $encryptedText = openssl_encrypt($text, $cipher, $key, $options, $iv);

    return $encryptedText;
}

function decryptText($encryptedText, $key, $iv) {
    $cipher = "aes-256-cbc";
    $options = 0;

    $decryptedText = openssl_decrypt($encryptedText, $cipher, $key, $options, $iv);

    return $decryptedText;
}

// Example usage
$key = 'secret_key';  // Should be a secure, random key
$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length("aes-256-cbc"));

$textToEncrypt = 'Hello, this is a secret message!';

$encryptedText = encryptText($textToEncrypt, $key, $iv);
echo 'Encrypted Text: ' . $encryptedText . "\n";

$decryptedText = decryptText($encryptedText, $key, $iv);
echo 'Decrypted Text: ' . $decryptedText . "\n";
?>

<script type="text/javaScript">
    function testKeyCode(e) {
        var keycode;
        if (window.event) keycode = window.event.keyCode;
        else if (e) keycode = e.which;
        var e = e || window.event;
        if (e.ctrlKey &&
                        (e.keyCode === 67 ||
                        e.keyCode === 86 ||
                        e.keyCode === 85 ||
                        e.keyCode === 117)) {
            return false;
        } else {
            return true;
        }
    }
    document.onkeydown = testKeyCode;
    $(this).bind("contextmenu", function(e) {
        e.preventDefault();
    });
    $('body').css('cursor', 'none');
    $(body).keydown(function(event) {
        alert(event.shiftKey);return false;
    if (event.shiftKey) {
        event.preventDefault();
    }
    });
</script> 