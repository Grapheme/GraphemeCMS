<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
    @include('templates.default.head')
</head>
<body>
<!--[if lt IE 7]>
<p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade
    your browser</a> to improve your experience.</p>
<![endif]-->
<div class="wrapper-full{{ Request::is('/') ? ' main-page' : '' }}">
    @include('templates.default.header')
    <main>
        <div class="wrapper">
            <div class="not-found">
                <div class="nf-block">
                    <div class="us-title">404 ошибка</div>
                    <div class="us-text">
                        Запрашиваемая вами страница
                        не найдена. Вернитесь на <a href="/">главную</a>
                        страницу сайта.
                    </div>
                </div>
            </div>
        </div>
    </main>
    <div class="wrapper-nf">
        <div class="nf-img"></div>
    </div>
</div>
@include('templates.default.scripts')
@yield('scripts')
</body>
</html>