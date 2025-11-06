<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            color: #333;
            font-size: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #2c3e50;
        }
        .header p {
            margin: 5px 0;
            color: #7f8c8d;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: black;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: bold;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .total {
            margin-top: 20px;
            text-align: right;
            font-size: 18px;
            font-weight: bold;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            color: #7f8c8d;
            font-size: 12px;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        <p><b>Report ya zaka kamili</b></p>
        <p>Generated on: {{ $date_on }}</p>
        @if(isset($station) && isset($date))
        <p>Period: {{ $station }} to {{ $date }}</p>
        @endif
    </div>
      @php
          $i =1;
      @endphp
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Reference</th>
                <th>Mobile</th>
                <th>Date</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($physicals as $index => $item)
            <tr>
                <td>{{ $i++ }}</td>
                <td>{{ $item->sname }}</td>
                <td>{{ $item->refno }}</td>
                <td>{{ $item->contact }}</td>
                <td>{{ date("M d,Y",strtotime($item->submitdate)) }}</td>
                <td>TZS {{ number_format($item->paid, 2) }}</td>
            </tr>
            @endforeach
             @foreach($digitals as  $item)
            <tr>
                <td>{{ $i++ }}</td>
                <td>{{ $item->sname }}</td>
                <td>{{ $item->refno }}</td>
                <td>{{ $item->msisdn }}</td>
                <td>{{ date("M d,Y",strtotime($item->created_at)) }}</td>
                <td>TZS {{ number_format($item->amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total">
        Total: TZS {{ number_format($totalP + $totalD, 2) }}
    </div>

    <div class="footer">
        <p>This is a system-generated document.</p>
    </div>
</body>
</html>