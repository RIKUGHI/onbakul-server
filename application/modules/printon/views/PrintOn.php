<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    table{
      border-collapse: collapse;
      font-family: 'Times New Roman', Times, serif;
      width: 400px;
    }

    th,td{
      padding: 1px 5px;
      font-size: 10px;
    }

    thead tr:first-child th,thead tr:nth-child(2) td,thead tr:nth-child(3) th{
      text-align: center;
      padding: 1px 0px;
    }

    p{
      margin: 0;
    }

    tbody tr td:first-child{
      width: 100%;
    }

    tbody tr td:nth-child(2),tbody tr td:nth-child(3),tbody tr td:nth-child(4){
      white-space: nowrap;
    }

    tbody tr td:nth-child(3){
      text-align: center;
    }

    tfoot tr td:first-child{
      text-align: right;
    }

    tfoot tr td:last-child{
      text-align: right;
      white-space: nowrap;
    }

    tfoot tr:last-child td{
      text-align: center;
    }

    .simple-info{
      display: flex;
      width: 100%;
    }

    .simple-info p:first-child{
      width: 40px;
    }

    .simple-info p:nth-child(2){
      color: blue;
      width: 10px;
    }
  </style>
  <title>Service | Print</title>
</head>
<body>

  <script src="<?= base_url('assets/js/print.js'); ?>"></script>
</body>
</html>


