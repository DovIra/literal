<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>イベント編集 - EventEase</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
</head>
<body>
    <nav class="navbar navbar-expand-md bg-light">
        <div class="container-fluid d-flex align-items-baseline">
            <a class="navbar-brand" href="{{ route('dashboard') }}">EventEase</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar"
                aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbar">
                <ul class="navbar-nav me-auto mb-2 mb-md-0">
                    <li class="nav-item ms-3">
                        <a class="nav-link" aria-current="page" href="{{ route('dashboard') }}">ホーム</a>
                    </li>
                    <li class="nav-item ms-3">
                        <a class="nav-link" href="{{ route('events.index') }}">イベントを探す</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>イベント編集</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="post" action="{{ route('events.update', $event) }}">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="category" class="form-label">カテゴリ</label>
                <input type="text" class="form-control" id="category" name="category" value="{{ old('category', $event->category) }}">
            </div>
            <div class="mb-3">
                <label for="title" class="form-label">イベント名</label>
                <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $event->title) }}">
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">詳細説明</label>
                <textarea class="form-control" id="description" name="description" rows="4">{{ old('description', $event->description) }}</textarea>
            </div>
            <div class="mb-3">
                <label for="event_date" class="form-label">開催日</label>
                <input type="date" class="form-control" id="event_date" name="event_date"
                    value="{{ old('event_date', \Illuminate\Support\Carbon::parse($event->event_date)->format('Y-m-d')) }}">
            </div>
            <div class="mb-3">
                <label for="start_time" class="form-label">開始時間</label>
                <input type="time" class="form-control" id="start_time" name="start_time"
                    value="{{ old('start_time', \Illuminate\Support\Carbon::parse($event->start_time)->format('H:i')) }}">
            </div>
            <div class="mb-3">
                <label for="location" class="form-label">場所</label>
                <input type="text" class="form-control" id="location" name="location" value="{{ old('location', $event->location) }}">
            </div>

            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-primary">保存</button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
