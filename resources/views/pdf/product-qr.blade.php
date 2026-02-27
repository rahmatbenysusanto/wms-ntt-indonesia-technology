<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Product QR Code</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 10px;
            text-align: center;
        }

        .qr-container {
            margin-top: 10px;
        }

        .label-details {
            margin-top: 15px;
            text-align: left;
            font-size: 12px;
            line-height: 1.4;
        }

        .label-details table {
            width: 100%;
            border-collapse: collapse;
        }

        .label-details td {
            padding: 2px 0;
        }

        .label-details td.label {
            width: 40%;
            font-weight: bold;
        }

        .label-details td.value {
            width: 60%;
        }

        .footer {
            margin-top: 10px;
            font-size: 10px;
            color: #666;
            border-top: 1px dashed #ccc;
            padding-top: 5px;
        }
    </style>
</head>

<body>
    <div class="qr-container">
        <img src="data:image/png;base64,{{ $qrcode }}" alt="QR Code" width="150">
    </div>

    <div class="label-details">
        <table>
            <tr>
                <td class="label">Purc Doc:</td>
                <td class="value">{{ $item->purc_doc }}</td>
            </tr>
            <tr>
                <td class="label">Sales Doc:</td>
                <td class="value">{{ $item->sales_doc }}</td>
            </tr>
            <tr>
                <td class="label">Material:</td>
                <td class="value">{{ $item->material }}</td>
            </tr>
            <tr>
                <td class="label">Client:</td>
                <td class="value">{{ $item->client_name ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Generated on {{ date('Y-m-d H:i:s') }}
    </div>
</body>

</html>
