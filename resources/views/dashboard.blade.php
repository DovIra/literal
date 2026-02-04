<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - EventEase</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
</head>
<body>
    

    <nav class="navbar navbar-expand-md bg-light" data-bs-theme="">
        <div class="container-fluid d-flex align-items-baseline">
            <a class="navbar-brand" href="ダッシュボード.html">EventEase</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="ナビゲーションの切替">
            <span class="navbar-toggler-icon"></span>
            </button>
    
          <div class="collapse navbar-collapse" id="navbar">
            <ul class="navbar-nav me-auto mb-2 mb-md-0">
              <li class="nav-item ms-3">
                <a class="nav-link" aria-current="page" href="{{ route('dashboard') }}">ホーム</a>
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
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        {{ Auth::user()->name }}さん
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="プロフィール画面.html">プロフィール</a></li>
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
      
        <h2>開催日が近いイベント</h2>
        <div class="row">
            <!-- Example Event Card -->
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <p><span class="badge bg-secondary">申込み済</span></p>
                        <h5 class="card-title">Event Title</h5>
                        <p class="card-text">Event Description...</p>
                        <a href="イベント詳細画面.html" class="btn btn-primary">詳細を見る</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <p><span class="badge bg-secondary">申込み済</span></p>
                        <h5 class="card-title">Event Title</h5>
                        <p class="card-text">Event Description...</p>
                        <a href="イベント詳細画面.html" class="btn btn-primary">詳細を見る</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <p><span class="badge bg-success">未申込み</span></p>
                        <h5 class="card-title">Event Title</h5>
                        <p class="card-text">Event Description...</p>
                        <a href="イベント詳細画面.html" class="btn btn-primary">詳細を見る</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <p><span class="badge bg-success">未申込み</span></p>
                        <h5 class="card-title">Event Title</h5>
                        <p class="card-text">Event Description...</p>
                        <a href="イベント詳細画面.html" class="btn btn-primary">詳細を見る</a>
                    </div>
                </div>
            </div>
            <!-- Repeat Event Card as needed -->
        </div>


        <h2>通知</h2>
        <ul class="list-group">
            <li class="list-group-item">
                <h5> [Event Title] が間もなく始まります。</h5>
                <p> [Event Title] が 2024年9月30日 10:00 に開催されます。お忘れなくご参加ください！</p>
                <small>開催場所: Conference Room A</small>
            </li>
            <li class="list-group-item">
                <h5>新しい[Event Title] が登録されました。</h5>
                <p>[Event Title] が 2024年09月18日 16:00 に開催されます。ぜひご参加ください！</p>
                <small>開催場所: Conference Room A</small>
            </li>
            <!-- Repeat notification items as needed -->
        </ul>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
