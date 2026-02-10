<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ユーザー管理 - EventEase</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
</head>

<body>
    <nav class="navbar navbar-expand-md bg-light" data-bs-theme="">
        <div class="container-fluid d-flex align-items-baseline">
            <a class="navbar-brand" href="{{ route('dashboard') }}">EventEase</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="ナビゲーションの切替">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbar">
                <ul class="navbar-nav me-auto mb-2 mb-md-0">
                    <li class="nav-item ms-3">
                        <a class="nav-link" aria-current="page" href="{{ route('dashboard') }}">ホーム</a>
                    </li>
                    <li class="nav-item ms-3">
                        <a class="nav-link" href="{{ route('events.create') }}">イベント作成</a>
                    </li>
                    <li class="nav-item ms-3"></li>
                    <a class="nav-link" href="{{ route('events.index') }}">イベントを探す</a>
                    </li>
                    <li class="nav-item ms-3"></li>
                    <a class="nav-link" href="{{ route('calendar') }}">カレンダー</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">

                    <li class="nav-item ms-3 dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ Auth::user()->name }}さん
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="{{ route('profile') }}">プロフィール</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
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
        <h2>ユーザー管理</h2>
        @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <div class="mb-3">
            <input type="text" class="form-control" id="searchUsers" placeholder="Search users">
        </div>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">名前</th>
                    <th scope="col">メール</th>
                    <th scope="col">役割</th>
                    <th scope="col">アクション</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                <tr>
                    <th scope="row">{{ $user->id }}</th>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role == 'admin' ? '管理者' : 'ユーザー' }}</td>
                    <td>
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary btn-sm">編集</a>
                        <form method="post" action="{{ route('admin.users.destroy', $user->id) }}" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">削除</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.min.js"></script>
</body>

</html>