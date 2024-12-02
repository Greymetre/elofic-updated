<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate of Authorization</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #FFF;
        }
    </style>
</head>

<body>

    <table style="width: 100%; border:2px solid #000;padding:0px;">
        <tr>
            <td><img width="310" style="height: 690px;" src="{{ $backImage64 }}" alt="Logo"></td>
            <td style="text-align: center;display:block;margin-top:20px;">
                <div class="header">
                    <!-- Logo -->
                    <img width="600" src="{{ $logoBase64 }}" alt="Logo">
                </div>
                <h1 style="font-size: 50px;color:#071f2b;font-weight:bold;margin-bottom:0;line-height:35px;">CERTIFICATE <br><span style="font-weight:700;font-size: 20px;">OF AUTHORISATION</span></h1>
                <div class="content">
                    <h6 style="color: #0f3e55;font-style:italic;font-family:cursive;font-size:20px;font-weight:600;margin-bottom: 0;line-height:30px">We are pleased to certify that <br> <span style="font-weight: bold;font-style:normal;font-family:Arial, sans-serif;">{{ $dealerName }} </span> <br> is our authorized {{$customer_type}} for {{$brand}} brand in <br> <span style="font-weight: 900;">{{ $division }}</span> <br> in {{ $region }} region for the period of <br> <span style="font-weight: 900;">{{ $financialYear }}</span>. </h6>
                </div>
                <div class="footer">
                    <table style="width: 100%;margin-top:20px;">
                        <tr style="text-align: center;">
                            <td style="font-size: 14px;color:#0f3e55;font-weight:200;margin-bottom:0;line-height:25px;">Issued Date : {{ date('d/m/Y',strtotime($issue_date)) }} <br> Place : Rajkot </td>
                            <td><img width="120" src="{{ $footerLogoImage64 }}" alt="Logo"></td>
                            <td><img width="200" src="{{ $signImage64 }}" alt="Logo"></td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

</body>

</html>