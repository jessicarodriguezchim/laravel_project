@extends('admin.layouts.app')

@section('title', 'Crear usuario')

@section('content')
<h1 class="h3 mb-3">Crear usuario</h1>

<form action="{{ route('admin.users.store') }}" method="POST">
    @csrf

    <div class="mb-3">
        <label class="form-label">Nombre</label>
        <input type="text" name="name"
               class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name') }}">
        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Correo</label>
        <input type="email" name="email"
               class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email') }}">
        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Si manejas password aquí, agrégalo igual que en roles o como lo requiera tu práctica --}}

    <button type="submit" class="btn btn-primary">
        Guardar
    </button>
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
        Cancelar
    </a>
</form>
@endsection
