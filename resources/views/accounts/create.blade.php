@extends('layout')

@section('content')
<div class="container">
    <h2 class="mb-4">{{ __('Create Account') }}</h2>
    <div class="row">
        <div class="col-md-6">
            <form method="post" action="{{ route('accounts.store') }}">
                @csrf
                <div class="form-group row">
                    <label for="inputName" class="col-sm-2 col-form-label">{{ __('Name') }}</label>
                    <div class="col-sm-10">
                        <input type="text" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}" value="{{ old('name') }}">
                        @if ($errors->has('name'))
                            <div class="invalid-feedback">{{ $errors->first('name') }}</div>
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <label for="selectType" class="col-sm-2 col-form-label">{{ __('Type') }}</label>
                    <div class="col-sm-10">
                        <select name="type" class="form-control {{ $errors->has('type') ? 'is-invalid' : '' }}">
                            <option></option>
                            @foreach ($accountTypes as $code => $name)
                                <option value="{{ $code }}" {{ old('type') == $code ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('type'))
                            <div class="invalid-feedback">{{ $errors->first('type') }}</div>
                        @endif
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-12">
                        <button type="submit" class="btn btn-primary">{{ __('Create') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
