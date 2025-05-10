<!doctype html>
<html class="h-full" data-theme="true" data-theme-mode="dark" lang="pt-br">

<head>
    <meta charset="utf-8">
    <title>@yield('title') - ATMOS</title>
    <base href="/">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="icon" type="image/png" href="{{ asset('assets/media/app/mini-logo.png') }}">

    @vite('resources/css/app.scss')

    @stack('head') 

    <meta name="robots" content="noindex, nofollow">
</head>

<body class="flex h-full demo1 sidebar-fixed header-fixed bg-[#fefefe] dark:bg-coal-500">
    <!--begin::Theme mode setup on page load-->
    <script>
       const defaultThemeMode = 'dark';
       let themeMode;

       if (document.documentElement) {
        if (localStorage.getItem('theme')) {
            themeMode = localStorage.getItem('theme');
        } else if (document.documentElement.hasAttribute('data-theme-mode')) {
            themeMode = document.documentElement.getAttribute('data-theme-mode');
        } else {
            themeMode = defaultThemeMode;
        }

        if (themeMode === 'system') {
            themeMode = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'dark';
        }

        document.documentElement.classList.add(themeMode);
    }

</script>
<!--end::Theme mode setup on page load-->

@yield('main')

@vite('resources/js/app.js')

</body>

</html>
