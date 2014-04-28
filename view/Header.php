<!DOCTYPE HTML>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
        <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
        <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.3.5/bootstrap-select.min.css">
        <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.3.5/bootstrap-select.min.js"></script>
        <link rel="stylesheet" href="assets/style/style.css">
        <link href='http://fonts.googleapis.com/css?family=Open+Sans:300' rel='stylesheet' type='text/css'>
        <title><?php echo $pageName ?></title>
        <script type="text/javascript">
            window.onload = function() {
                $('select').selectpicker();
            };
        </script>
    </head>
    <body>
        <?php include_once 'Menu.php'; ?>
        <div class="imageContener">
            <p class="headerText">
                DuoQ
            </p>
        </div>
        <div id="wrapper">
