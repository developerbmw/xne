@extends('layout')

@section('content')
<div class="container">
    <h2 class="mb-4">{{ __('Run Report') }}</h2>
    <form method="get" action="{{ route('reports.run') }}">
        <div class="form-group row">
            <label for="selectReport" class="col-sm-2 col-form-label">{{ __('Report') }}</label>
            <div class="col-md-4">
                <select id="selectReport" name="report" class="form-control {{ $errors->has('report') ? 'is-invalid' : '' }}">
                    @foreach ($reports as $name)
                        <option {{ old('report') == $name ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
                @if ($errors->has('report'))
                    <div class="invalid-feedback">{{ $errors->first('report') }}</div>
                @endif
            </div>
        </div>
        <div id="selectAccountDiv" class="form-group row">
            <label for="selectAccount" class="col-sm-2 col-form-label">{{ __('Account') }}</label>
            <div class="col-md-4">
                <select id="selectAccount" name="account" class="form-control {{ $errors->has('account') ? 'is-invalid' : '' }}">
                    @foreach ($accounts as $account)
                        <option value="{{ $account->id }}" {{ old('account') == $account->id ? 'selected' : '' }}>{{ $account->name }}</option>
                    @endforeach
                </select>
                @if ($errors->has('account'))
                    <div class="invalid-feedback">{{ $errors->first('account') }}</div>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="inputStartDate" class="col-md-2 col-form-label">{{ __('Start Date') }}</label>
            <div class="col-md-4">
                <input type="date" name="start_date" value="{{ old('start_date') }}" class="form-control {{ $errors->has('start_date') ? 'is-invalid' : '' }}">
                @if ($errors->has('start_date'))
                    <div class="invalid-feedback">{{ $errors->first('start_date') }}</div>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="inputEndDate" class="col-md-2 col-form-label">{{ __('End Date') }}</label>
            <div class="col-md-4">
                <input type="date" name="end_date" value="{{ old('end_date') }}" class="form-control {{ $errors->has('end_date') ? 'is-invalid' : '' }}">
                @if ($errors->has('end_date'))
                    <div class="invalid-feedback">{{ $errors->first('end_date') }}</div>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <div class="col-sm-12">
                <button type="submit" class="btn btn-primary">{{ __('Run') }}</button>
            </div>
        </div>
    </form>
</div>

<script>
    $(document).ready(function() {
        function updateForm() {
            if ($('#selectReport').val() == 'Single Account') {
                $('#selectAccountDiv').show();
            } else {
                $('#selectAccountDiv').hide();
            }
        }

        $('#selectReport').change(updateForm);
        updateForm();
    });
</script>
@endsection
