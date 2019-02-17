@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">{{ __('Edit Profile') }}</h2>
    <div class="row">
        <div class="col-md-6">
            <form method="post" action="{{ route('profile.update') }}">
                @method('PUT')
                @csrf
                <div class="form-group row">
                    <label for="inputName" class="col-sm-4 col-form-label">{{ __('Name') }}</label>
                    <div class="col-sm-8">
                        <input type="text" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" id="inputName" value="{{ old('name', Auth::user()->name) }}" required>
                        @if($errors->has('name'))
                            <div class="invalid-feedback">{{ $errors->first('name') }}</div>
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputEmail" class="col-sm-4 col-form-label">{{ __('Email') }}</label>
                    <div class="col-sm-8">
                        <input type="email" name="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" id="inputEmail" value="{{ old('email', Auth::user()->email) }}" autocomplete="off">
                        @if($errors->has('email'))
                            <div class="invalid-feedback">{{ $errors->first('email') }}</div>
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputPassword" class="col-sm-4 col-form-label">{{ __('Password') }}</label>
                    <div class="col-sm-8">
                        <input type="password" name="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}" id="inputPassword" autocomplete="off">
                        @if($errors->has('password'))
                            <div class="invalid-feedback">{{ $errors->first('password') }}</div>
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <label for="inputPasswordConfirmation" class="col-sm-4 col-form-label">{{ __('Confirm Password') }}</label>
                    <div class="col-sm-8">
                        <input type="password" name="password_confirmation" class="form-control {{ $errors->has('password_confirmation') ? 'is-invalid' : '' }}" id="inputPasswordConfirmation" autocomplete="off">
                        @if($errors->has('password_confirmation'))
                            <div class="invalid-feedback">{{ $errors->first('password_confirmation') }}</div>
                        @endif
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-4">{{ __('Save') }}</button>
            </form>
        </div>
    </div>
</div>
@endsection
