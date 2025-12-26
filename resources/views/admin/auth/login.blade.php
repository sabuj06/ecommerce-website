<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Login</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card login-card">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <i class="fas fa-shield-alt fa-3x text-primary"></i>
                            <h3 class="mt-3">Admin Login</h3>
                            <p class="text-muted">Enter your credentials to access admin panel</p>
                        </div>

                        <form id="login-form">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control" name="email" placeholder="admin@admin.com" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" class="form-control" name="password" placeholder="Enter password" required>
                                </div>
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">Remember Me</label>
                            </div>

                            <div id="error-message" class="alert alert-danger d-none"></div>

                            <button type="submit" class="btn btn-primary w-100" id="login-btn">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </button>
                        </form>

                        <div class="text-center mt-4">
                            <small class="text-muted">
                                Default: <strong>admin@admin.com</strong> / <strong>admin123</strong>
                            </small>
                        </div>

                        <hr>

                        <div class="text-center">
                            <a href="{{ route('shop.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Back to Website
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#login-form').submit(function(e) {
            e.preventDefault();
            
            const btn = $('#login-btn');
            const errorDiv = $('#error-message');
            
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Logging in...');
            errorDiv.addClass('d-none');

            $.post('{{ route("admin.login.post") }}', $(this).serialize())
                .done(function(response) {
                    if(response.success) {
                        window.location.href = response.redirect;
                    }
                })
                .fail(function(xhr) {
                    const message = xhr.responseJSON?.message || 'Login failed. Please try again.';
                    errorDiv.text(message).removeClass('d-none');
                    btn.prop('disabled', false).html('<i class="fas fa-sign-in-alt"></i> Login');
                });
        });
    </script>
</body>
</html>