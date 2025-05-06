<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>فاتورة طلب #{{ $order->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; }
        .table th { background-color: #f2f2f2; }
        .text-right { text-align: right; }
    </style>
<div class="header">
    <h1>فاتورة طلب #{{ $order->id }}</h1>
    <p>متجر: {{ $order->store->name }}</p>
    <p>تاريخ: {{ $date }}</p>
</div>

<table class="table">
    <thead>
    <tr>
        <th>#</th>
        <th>المنتج</th>
        <th>الكمية</th>
        <th>السعر</th>
        <th>المجموع</th>
    </tr>
    </thead>
    <tbody>
    @foreach($order->products as $product)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $product->name }}</td>
            <td>{{ $product->pivot->quantity }}</td>
            <td>{{ number_format($product->pivot->price, 2) }}</td>
            <td>{{ number_format($product->pivot->price * $product->pivot->quantity, 2) }}</td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr>
        <td colspan="4" class="text-right"><strong>الإجمالي:</strong></td>
        <td>{{ number_format($order->total, 2) }}</td>
    </tr>
    </tfoot>
</table>
</head>
<body>

</body>
</html>
