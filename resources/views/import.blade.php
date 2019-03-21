@extends('layout')

@section('content')
<div class="container">
    <h2 class="mb-4">{{ __('Import Data') }}</h2>
    <div class="row">
        <div class="col-md-6">
            <p>CSV must have columns: date, account, description, debit, and credit.</p>
            <p>Each row with a description will start a new transaction. All rows for a transaction must have the description blank except for the first row.</p>
            <br>
            <form method="post" action="{{ route('import.run') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group row">
                    <label for="inputFile" class="col-sm-4 col-form-label">{{ __('File') }}</label>
                    <div class="col-sm-8">
                        <div class="custom-file">
                          <input name="file" type="file" class="custom-file-input" id="inputFile" required accept=".csv">
                          <label class="custom-file-label" for="inputFile" id="inputFileLabel">Choose file</label>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-4">{{ __('Run Import') }}</button>
            </form>
        </div>
    </div>
</div>

<script>
    $('#inputFile').change(function(e) {
        if (e.target.files.length > 0) {
            $('#inputFileLabel').text(e.target.files[0].name);
        } else {
            $('#inputFileLabel').text('Choose file');
        }
    });
</script>
@endsection
