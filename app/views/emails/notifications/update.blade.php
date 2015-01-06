<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<h2>RSS Feed Update for: {{ $feed['name'] }}</h2>
		<p style="font-style: italic;">{{ $feed['description'] }}</p>
		<div>
			@foreach ($feed['items'] as $item)
				<p>
					<strong>{{ date('d/M/Y H:i', $item['timestamp']) }} {{ $item['title'] }}</strong><br />
					{{ $item['content'] }}<br />
					{{ $item['link'] }}
				</p>
				<hr />
			@endforeach
		</div>
	</body>
</html>
