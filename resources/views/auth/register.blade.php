<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ユーザ登録 - EventEase</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
        crossorigin="anonymous">
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-center">EventEase</h4>
                    <form method="post" action="{{ route('register') }}">
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
                            <label for="name" class="form-label">ユーザ名</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter your name">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">メールアドレス</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter email">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">パスワード</label>
                            <input type="password" class="form-control" id="password" name="password"
                                placeholder="Password">
                        </div>
                        <div class="mb-3">
                            <label for="password-confirm" class="form-label">パスワード確認</label>
                            <input type="password" class="form-control" id="password-confirm"
                                name="password_confirmation" placeholder="Confirm Password">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">ユーザ登録</button>
                        <div class="text-center mt-3">
                            <a href="{{ route('login') }}">登録済の方はこちら</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.min.js"></script>
</body>

</html>