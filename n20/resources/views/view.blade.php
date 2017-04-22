<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>file upload</title>
</head>
<body>
    @if($errors->has('message'))
        <h1>{{ $errors->first('message') }}</h1>
    @endif
    <form action="{{ route('viewUpload') }}" method="post" enctype="multipart/form-data">
        {{ csrf_field() }}
        <input type="file" name="image" accept="image/jpeg,image/png,image/webp" required>
        <input type="submit" value="submit">
    </form>
</body>
</html>