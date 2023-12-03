<html lang="ru" style="background: #f5f5f5;">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="referrer" content="nowhere">
        <link rel="stylesheet" href="/css/bootstrap.css" />
        <style>
            ::-webkit-scrollbar {
                width: 14px;
                height: 14px;
            }
            ::-webkit-scrollbar-thumb {
                background-color: #a6a6ab;
                border-radius: 10px;
                border: 3px solid transparent;
                background-clip: padding-box;
            }
        </style>
    </head>

    <body>
        <div class="container">
            <div style="display: inline-block;">
                <a href="{{env('APP_URL')}}webhook/blanks?{{http_build_query($request)}}"
                   class="btn btn-warning text-center"
                   target="_blank"
                   style="color: #212529;
                background-color: #ffc107;
                border-color: #ffc107;
                border: 1px solid transparent;
                padding: 0.375rem 0.75rem;
                font-size: 1rem;
                line-height: 1.5;
                cursor: pointer;
                border-radius: 10px;
                width: 100px;"
                >Открыть</a>
            </div>
        </div>
    </body>
</html>
