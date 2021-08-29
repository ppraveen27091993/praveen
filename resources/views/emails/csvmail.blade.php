<!DOCTYPE html>
<html>
<head>
    <title>CSV Upload</title>
</head>
<body>
    <h1>CSV Upload Result</h1>
    @if(is_array($details))
       <h2>Error List</h2>
            @foreach($details as $key)
                <p> {{ $key }}</p>
            @endforeach
    @else
       <p> {{ $details }}</p>
    @endif

    <p>Thank you</p>
</body>
</html>
