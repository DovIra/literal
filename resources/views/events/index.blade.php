<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>イベント一覧 - EventEase</title>
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
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item ms-3 dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            {{ Auth::user()->name }}さん
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="{{ route('profile') }}">プロフィール</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="post" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">ログアウト</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mt-4">
            <h2>イベント一覧</h2>
            <a class="btn btn-primary" href="{{ route('events.create') }}">イベント作成</a>
        </div>

        @if (session('success'))
            <div class="alert alert-success mt-3">{{ session('success') }}</div>
        @endif

        @if ($events->isEmpty())
            <div class="alert alert-info mt-3">イベントがありません。</div>
        @else
            <div class="table-responsive mt-3">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>タイトル</th>
                            <th>カテゴリー</th>
                            <th>開催日</th>
                            <th>開始時間</th>
                            <th>場所</th>
                            <th class="text-end">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($events as $event)
                            <tr>
                                <td>{{ $event->title }}</td>
                                <td>{{ $event->category }}</td>
                                <td>{{ $event->event_date }}</td>
                                <td>{{ $event->start_time }}</td>
                                <td>{{ $event->location }}</td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('events.show', $event->id) }}">詳細</a>
                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('events.edit', $event->id) }}">編集</a>
                                    <form method="post" action="{{ route('events.destroy', $event->id) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">削除</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
