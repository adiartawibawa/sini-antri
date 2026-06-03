<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login Operator</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body {
    font-family: 'Segoe UI', system-ui, sans-serif;
    background: linear-gradient(135deg, #1e3a8a 0%, #1a56db 100%);
    min-height: 100vh;
    display: flex; align-items: center; justify-content: center;
    padding: 1rem;
  }
  .card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0,0,0,.25);
    padding: 2.5rem 2rem;
    width: 100%; max-width: 380px;
  }
  .logo { text-align:center; margin-bottom:2rem; }
  .logo-icon {
    width: 72px; height: 72px;
    background: #1a56db;
    border-radius: 20px;
    display: flex; align-items: center; justify-content: center;
    font-size: 32px;
    margin: 0 auto 1rem;
  }
  h1 { font-size: 1.4rem; font-weight: 700; color: #1e293b; }
  p  { color: #64748b; font-size: .875rem; margin-top:.25rem; }

  .alert {
    background: #fef2f2; border: 1px solid #fecaca;
    color: #991b1b; border-radius: 10px;
    padding: .75rem 1rem; font-size: .875rem;
    margin-bottom: 1.25rem;
  }
  label {
    display: block; font-size: .875rem; font-weight: 600;
    color: #374151; margin-bottom: .4rem;
  }
  input[type=email], input[type=password] {
    width: 100%; padding: .75rem 1rem;
    border: 2px solid #e5e7eb; border-radius: 10px;
    font-size: 1rem; color: #1e293b;
    transition: border-color .2s;
    margin-bottom: 1.25rem; font-family: inherit;
  }
  input:focus { outline: none; border-color: #1a56db; box-shadow: 0 0 0 3px rgba(26,86,219,.1); }
  .remember {
    display: flex; align-items: center; gap: .5rem;
    margin-bottom: 1.5rem; font-size: .875rem; color: #374151;
  }
  .btn {
    width: 100%; padding: .875rem;
    background: #1a56db; color: white;
    border: none; border-radius: 12px;
    font-size: 1rem; font-weight: 700; cursor: pointer;
    transition: background .2s;
  }
  .btn:hover { background: #1343b3; }
</style>
</head>
<body>
<div class="card">
  <div class="logo">
    <div class="logo-icon">🖥️</div>
    <h1>Login Operator</h1>
    <p>Masuk ke dashboard kelola antrian</p>
  </div>

  @if($errors->any())
  <div class="alert">{{ $errors->first() }}</div>
  @endif

  <form method="POST" action="{{ route('login.post') }}">
    @csrf
    <label>Email</label>
    <input type="email" name="email" value="{{ old('email') }}"
           placeholder="loket1@antrian.test" required autofocus>

    <label>Password</label>
    <input type="password" name="password" placeholder="••••••••" required>

    <div class="remember">
      <input type="checkbox" name="remember" id="remember">
      <label for="remember" style="margin:0">Ingat saya</label>
    </div>

    <button type="submit" class="btn">Masuk ke Dashboard</button>
  </form>
</div>
</body>
</html>
