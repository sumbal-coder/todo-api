<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
        <style>
            body {
                font-family: poppins;
                margin-left: auto;
                margin-right: auto;
                display: block;
                width: 50%; }
        </style>
    </head>
    <body>
            <div class="row">
                <div class="col">
                    <h3>Verification Code</h3>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <p>Your Account is not created yet. Please verify first by entering verfification code.</p>
                    Verification Code: <strong>
                       
                            {{$str}}
                        
                    </strong>
               
                </div>
            </div>
    </body>
</html>
