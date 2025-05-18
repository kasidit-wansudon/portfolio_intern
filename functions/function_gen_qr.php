<?php
// function_gen_qr.php
function generateQRCode($text) {
    include_once "./phpqrcode-master/qrlib.php";
    $filename = "./cache/_qr" . time() . ".png";
    QRcode::png($text, $filename, QR_ECLEVEL_L, 4);
    return $filename;
}
