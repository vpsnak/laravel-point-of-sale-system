<!DOCTYPE html>
@php
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;

$barcode = new BarcodeGenerator();
if ($product->barcode){
$barcode->setText($product->barcode);
}
else {
$barcode->setText($product->sku);
}
$barcode->setType(BarcodeGenerator::Code128);
$barcode->setScale(2);
$barcode->setThickness(25);
$barcode->setFontSize(12);
$code = $barcode->generate();

@endphp

<html lang="en">
​
<head>
    <meta charset="UTF-8">
    <title>Product Barcode</title>
</head>
<style>
    body {
        margin: 0;
        max-height: 1in;
        max-width: 2in;
        width: 100%;
        height: 100%;
    }
    .barcode {
        position: absolute;
        top:0;
        left:0;
        right: 0;
        bottom: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    img {
        width: 100%;
        height: auto;
        max-width: 100px;
    }
    .product_price{
        font-size: 20px;
    }
</style>

@if (config('app.env') === 'local')

<body>
@else

<body onload="window.print()" onafterprint="window.close()">
@endif
    <div class="barcode">
        <span>{{ $product->name }}</span>
        <span class="product_price">${{ round($product->final_price, 2) }}</span>
        <img src="data:image/png;base64,{{ $code }}">
    </div>
</body>
</html>
