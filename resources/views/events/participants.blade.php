<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>参加者管理 - EventEase</title>
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
                        <a class="nav-link" href="{{ route('dashboard') }}">ホーム</a>
                    </li>
                    <li class="nav-item ms-3">
                        <a class="nav-link" href="{{ route('events.create') }}">イベント作成</a>
                    </li>
                    <li class="nav-item ms-3">
                        <a class="nav-link" href="{{ route('events.index') }}">イベントを探す</a>
                    </li>
                    <li class="nav-item ms-3">
                        <a class="nav-link" href="{{ route('calendar') }}">カレンダー</a>
                    </li>
                    @can('admin')
                        <li class="nav-item ms-3">
                            <a class="nav-link" href="{{ route('admin.users.index') }}">ユーザー管理</a>
                        </li>
                    @endcan
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

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2>参加者管理</h2>
            <a href="{{ route('events.show', $event->id) }}" class="btn btn-outline-secondary">イベントに戻る</a>
        </div>

        @if (session('success'))
            <div class="alert alert-success mt-3">{{ session('success') }}</div>
        @endif

        <!-- Event Information -->
        <div class="card mt-3">
            <div class="card-header">イベント情報</div>
            <div class="card-body">
                <h5 class="card-title">{{ $event->title }}</h5>
                <p class="card-text"><strong>開催日:</strong> {{ $event->event_date }}</p>
                <p class="card-text"><strong>場所:</strong> {{ $event->location }}</p>
            </div>
        </div>

        <!-- Registered Participants -->
        <div class="card mt-3">
            <div class="card-header">登録済み参加者 ({{ $participants->count() }}名)</div>
            <div class="card-body">
                @if ($participants->isEmpty())
                    <div class="alert alert-info">参加者がまだいません。</div>
                @else
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>名前</th>
                                <th>メール</th>
                                <th>ステータス</th>
                                <th class="text-end">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($participants as $index => $participant)
                                <tr>
                                    <th scope="row">{{ $index + 1 }}</th>
                                    <td>{{ $participant->name }}</td>
                                    <td>{{ $participant->email }}</td>
                                    <td>
                                        <span class="badge bg-success">参加中</span>
                                    </td>
                                    <td class="text-end">
                                        <form method="post" action="{{ route('events.participants.remove', [$event->id, $participant->id]) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">参加取消</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
