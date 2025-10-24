@extends('layouts.auth')

@section('content')
<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card shadow">
        <div class="card-header bg-primary text-white text-center">
          <h4>Iniciar sesión</h4>
        </div>
        <div class="card-body">
          <form method="POST" action="{{ route('login.post') }}">
            @csrf
            <div class="mb-3">
              <label class="form-label">Usuario</label>
              <input type="text" class="form-control" name="username" required autofocus>
            </div>
            <div class="mb-3">
              <label class="form-label">Contraseña</label>
              <input type="password" class="form-control" name="password" required>
            </div>
            <button class="btn btn-primary w-100">Ingresar</button>
          </form>

          @if ($errors->any())
            <div class="alert alert-danger mt-3">
              {{ $errors->first() }}
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
