@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">{{ __('Edit Transaction') }}</h2>
    <form method="post" action="{{ route('transactions.update', $transaction) }}">
        @method('PUT')
        @csrf
        <div class="form-group row">
            <label for="inputDate" class="col-md-2 col-form-label">{{ __('Date') }}</label>
            <div class="col-md-4">
                <input type="date" name="date" value="{{ old('date', $transaction->date->format('Y-m-d')) }}" class="form-control {{ $errors->has('date') ? 'is-invalid' : '' }}">
                @if ($errors->has('date'))
                    <div class="invalid-feedback">{{ $errors->first('date') }}</div>
                @endif
            </div>
        </div>
        <div class="form-group row">
            <label for="inputDescription" class="col-md-2 col-form-label">{{ __('Description') }}</label>
            <div class="col-md-4">
                <input type="text" name="description" value="{{ old('description', $transaction->description) }}" class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}">
                @if ($errors->has('description'))
                    <div class="invalid-feedback">{{ $errors->first('description') }}</div>
                @endif
            </div>
        </div>
        <div class="mt-4 mb-1">Journal Entries</div>
            <table class="table table-sm" id="entries">
                <thead>
                    <tr>
                        <th width="50%">{{ __('Account') }}</th>
                        <th width="22%">{{ __('Debit') }}</th>
                        <th width="22%">{{ __('Credit') }}</th>
                        <th width="6%"></th>
                    </tr>
                </thead>
                <tbody>
                    @if (old('entries'))
                        @foreach (old('entries') as $i => $entry)
                            <tr>
                                <td>
                                    <select name="entries[{{ $i }}][account]" class="form-control form-control-sm">
                                        <option></option>
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}" {{ $entry['account'] == $account->id ? 'selected' : '' }}>{{ $account->name }} ({{ $account->type_name }})</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="entries[{{ $i }}][debit]" value="{{ $entry['debit'] }}" class="form-control form-control-sm" step="0.01" onchange="this.value = parseFloat(this.value).toFixed(2)">
                                </td>
                                <td>
                                    <input type="number" name="entries[{{ $i }}][credit]" value="{{ $entry['credit'] }}" class="form-control form-control-sm" step="0.01" onchange="this.value = parseFloat(this.value).toFixed(2)">
                                </td>
                                <td class="text-center">
                                    <span class="oi oi-trash"></span>
                                    <input type="hidden" name="entries[{{ $i }}][id]" value="{{ $entry['id'] }}">
                                </td>
                            </tr>
                        @endforeach
                    @else
                        @foreach ($transaction->journalEntries as $i => $entry)
                            <tr>
                                <td>
                                    <select name="entries[{{ $i }}][account]" class="form-control form-control-sm">
                                        <option></option>
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}" {{ $account->id == $entry->account_id ? 'selected' : '' }}>{{ $account->name }} ({{ $account->type_name }})</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="entries[{{ $i }}][debit]" value="{{ $entry->debit_amount ? number_format($entry->debit_amount, 2, '.', '') : '' }}" class="form-control form-control-sm" step="0.01" onchange="this.value = parseFloat(this.value).toFixed(2)">
                                </td>
                                <td>
                                    <input type="number" name="entries[{{ $i }}][credit]" value="{{ $entry->credit_amount ? number_format($entry->credit_amount, 2, '.', '') : '' }}" class="form-control form-control-sm" step="0.01" onchange="this.value = parseFloat(this.value).toFixed(2)">
                                </td>
                                <td class="text-center">
                                    <span class="oi oi-trash"></span>
                                    <input type="hidden" name="entries[{{ $i }}][id]" value="{{ $entry->id }}">
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
            <div class="text-right mb-5">
                <button type="button" class="btn btn-primary" id="addEntry">{{ __('Add Entry') }}</button>
            </div>
        <div class="form-group row">
            <div class="col-sm-12">
                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
            </div>
        </div>
    </form>
</div>

<script>
    $('document').ready(function() {
        function deleteRow() {
            if ($('#entries tbody tr').length == 1) {
                return;
            }

            $(this).parent().parent().remove();
        }
        var index = {{ old('entries') ? count(old('entries')) : $transaction->journalEntries->count() }};
        $('#addEntry').click(function() {
            var newRow = $('#entries tbody tr').first().clone();
            newRow.find('input').val(null);
            newRow.find('option').removeAttr('selected');
            newRow.find('.oi-trash').click(deleteRow);
            var inputs = newRow.find('select, input');
            inputs.eq(0).prop('name', 'entries[' + index + '][account]');
            inputs.eq(1).prop('name', 'entries[' + index + '][debit]');
            inputs.eq(2).prop('name', 'entries[' + index + '][credit]');
            inputs.eq(3).prop('name', 'entries[' + index++ + '][id]');
            $('#entries tbody').append(newRow);
        });
        $('.oi-trash').click(deleteRow);
    });
</script>
@endsection
