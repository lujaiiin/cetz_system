<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>نظام الكلية</title>
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')

</head>
<body class="bg-gray-50 text-gray-800 font-sans">
  <div class="min-h-screen flex">
    @include('ui.partials.sidebar')

    <div class="flex-1">
      @include('ui.partials.header')
      <main class="p-6">
        @yield('content')
      </main>
    </div>
  </div>
</body>
</html>
