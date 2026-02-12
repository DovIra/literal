<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>イベント詳細 - EventEase</title>
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
        <h2>イベント詳細</h2>
        <p><span class="badge bg-success">公開中</span></p>

        <div class="card mb-3">
            <div class="card-header">
                イベント情報
            </div>
            <div class="card-body">
                <h5 class="card-title">イベント名: {{ $event->title }}</h5>
                <p class="card-text">カテゴリ: {{ $event->category }}</p>
                <p class="card-text">開催日: {{ $event->event_date }}</p>
                <p class="card-text">開始時間: {{ $event->start_time }}</p>
                <p class="card-text">場所: {{ $event->location }}</p>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                詳細説明
            </div>
            <div class="card-body">
                <p>{{ $event->description }}</p>
            </div>
        </div>
       <div class="card mb-3">                                                                                                                                                                      
      <div class="card-body">                                                                                                                                                                  
          @if ($event->participants->contains(Auth::id()))                                                                                                                                     
              <p><span class="badge bg-success">参加登録済み</span></p>                                                                                                                        
              <form method="post" action="{{ route('events.leave', $event->id) }}" class="d-inline">                                                                                           
                  @csrf                                                                                                                                                                        
                  @method('DELETE')                                                                                                                                                            
                  <button type="submit" class="btn btn-warning">参加をキャンセル</button>                                                                                                      
              </form>                                                                                                                                                                          
          @else                                                                                                                                                                                
              <form method="post" action="{{ route('events.join', $event->id) }}" class="d-inline">                                                                                            
                  @csrf                                                                                                                                                                        
                  <button type="submit" class="btn btn-success">参加する</button>                                                                                                              
              </form>                                                                                                                                                                          
          @endif                                                                                                                                                                               
                                                                                                                                                                                               
          <a href="{{ route('events.participants', $event->id) }}" class="btn btn-secondary ms-2">参加者管理</a>                                                                               
      </div>                                                                                                                                                                                   
  </div>
            
        <div class="d-flex justify-content-end mt-4">
            <a href="{{ route('events.edit', $event->id) }}" class="btn btn-primary me-2">編集</a>
            <form method="post" action="{{ route('events.destroy', $event->id) }}" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">削除</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
