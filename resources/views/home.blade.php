<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Factorio Mod Checker</title>

    <!-- Styles -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
          integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <!-- Custom fonts for this template -->
    <link href="fontawesome/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic,700italic" rel="stylesheet" type="text/css">

    <link rel="stylesheet" href="/css/landing-page.css">
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<!-- Masthead -->
<header class="masthead text-white text-center">
    <div class="overlay"></div>
    <div class="container">
        <div class="row">
            <div class="col-xl-9 mx-auto">
                <h1 class="mb-5 glow">Factorio Mod Info</h1>
            </div>
            <div class="col-md-10 col-lg-8 col-xl-7 mx-auto">
                <h3 class="text-left glow">Upload your mod-list.json</h3>
                <form method="post" action="{{ route('upload-mod-list') }}" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="form-row">
                        <div class="col-12 col-md-9 mb-2 mb-md-0">
                            <input type="file" name="mod-list" class="form-control form-control-lg">
                        </div>
                        <div class="col-12 col-md-3">
                            <button type="submit" class="btn btn-block btn-lg btn-warning">Upload</button>
                        </div>
                        @if ($errors->isNotEmpty())
                            <div class="alert alert-danger">{{ implode(',', $errors->get('mod-list')) }}</div>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</header>

@if ( ! empty($mods))
<div class="container">
    <div class="row">
        <div class="col card">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Mod</th>
                        <th>Author</th>
                        <th>Mod Version</th>
                        <th>Factorio Version</th>
                        <th>Last Update</th>
                        <th>Enabled</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($mods as $mod)
                    <tr>
                        <td>{{ $mod->title }}</td>
                        <td>{{ $mod->author }}</td>
                        <td>{{ $mod->version }}</td>
                        <td>@if ($mod->factorio_version >= '1.1') <i class="fas fa-check text-success"></i> @else <i class="fas fa-times text-danger"></i> @endif {{ $mod->factorio_version }}</td>
                        <td>{{ $mod->releasedAt() }}</td>
                        <td>{{ $mod->enabled ? 'Enabled' : '' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@else
<div class="container">
    <div class="row">
        <div class="col-lg-9 mx-auto card">
            <p>
                Upload your local mod-list.json to see which mods are already updated to the latest Factorio version.<br>
                You can find this file:
            </p>

            <span><i class="fab fa-windows"></i> Windows:</span><br>
            <pre>C:\Users\[Username]\AppData\Roaming\factorio\mods</pre>

            <span><i class="fab fa-apple"></i> Mac:</span><br>
            <pre>/Users/[Username]/Library/Application Support/factorio/mods</pre>

        </div>
    </div>
</div>
@endif

<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo"
        crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6"
        crossorigin="anonymous"></script>
</body>
</html>
