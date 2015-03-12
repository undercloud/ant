<!DOCTYPE html>
<html>
<head>
	<title>{{ $title }} {{ $body.second }} {{ $.server.DOCUMENT_ROOT }}</title>
</head>
<body>
	{@section(main)}
		Content must be replaced
	{@end}

	{@section(ovarah)}
		Ovarahalla
	{@end}
</body>
</html>