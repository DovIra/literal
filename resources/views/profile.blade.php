<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - EventEase</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
        crossorigin="anonymous">
</head>

<body>
    <nav class="navbar navbar-expand-md bg-light" data-bs-theme="">
        <div class="container-fluid d-flex align-items-baseline">
            <a class="navbar-brand" href="ダッシュボード.html">EventEase</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar"
                aria-controls="navbar" aria-expanded="false" aria-label="ナビゲーションの切替">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbar">
                <ul class="navbar-nav me-auto mb-2 mb-md-0">
                    <li class="nav-item ms-3">
                        <a class="nav-link" aria-current="page" href="ダッシュボード.html">ホーム</a>
                    </li>
                    <li class="nav-item ms-3">
                        <a class="nav-link" href="イベント作成画面.html">イベント作成</a>
                    </li>
                    <li class="nav-item ms-3"></li>
                    <a class="nav-link" href="イベント一覧画面.html">イベントを探す</a>
                    </li>
                    <li class="nav-item ms-3"></li>
                    <a class="nav-link" href="カレンダー表示画面.html">カレンダー</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">

                    <li class="nav-item ms-3 dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            {{ Auth::user()->name }}さん
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="プロフィール画面.html">プロフィール</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="ログイン.html">ログアウト</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        <h2>プロフィール</h2>
        <form method="post" action="{{ route('profile.update') }}">
            @csrf
            @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
            <div class="mb-3">
                <label for="profileName" class="form-label">ユーザ名</label>
                <input type="text" class="form-control" id="profileName" name="name" value="{{ Auth::user()->name }}">
            </div>
            <div class="mb-3">
                <label for="profileEmail" class="form-label">メールアドレス</label>
                <input type="email" class="form-control" id="profileEmail" value="{{ Auth::user()->email }}">
            </div>
            <div class="mb-3">
                <label for="profilePassword" class="form-label">パスワード</label>
                <input type="password" class="form-control" id="profilePassword" name="password" placeholder="New password">
            </div>
            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-primary">更新</button>
            </div>

        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>