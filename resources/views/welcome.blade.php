<html>
<head>
    <title>Ayima Full Stack Developer - Technical Test</title>
    <meta name="csrf-token" content="{{ csrf_token()  }}">
    <link rel="stylesheet" href="{{mix('css/app.css')}}" type="text/css"/>
</head>
<body>
<div id="app"></div>
<script>
    window.testApiUrls = {
        'getScores': '{{ route('api.get_domain_scores') }}',
        'getMonths': '{{ route('api.get_domain_months') }}'
    };
</script>
<script src="{{mix('js/app.js')}}" defer></script>
</body>
</html>
