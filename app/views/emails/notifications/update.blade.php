<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<h2>RSS Feed Update</h2>

		<h3>{{ $feed['name'] }}</h3>
		<p>{{ $feed['description'] }}</p>
		<div>
			<p>
			@foreach ($feed['items'] as $item)
				<strong>{{ $item['timestamp'] }} {{ $item['title'] }}</strong><br />
				{{ $item['content'] }}<br />
				{{ $item['link'] }}
			@endforeach
			</p>
		</div>
	</body>
</html>
