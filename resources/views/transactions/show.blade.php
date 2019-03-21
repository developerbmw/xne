@extends('layout')

@section('content')
<div class="container">
    <div class="row mb-4">
        <h2 class="col-sm-6">{{ __('Transaction Details') }}</h2>
        <div class="col-sm-6 text-right">
            <a href="{{ route('transactions.edit', $transaction) }}" class="btn btn-primary">{{ __('Edit') }}</a>
            <form method="post" action="{{ route('transactions.destroy', $transaction) }}" style="display: inline-block">
                @method('DELETE')
                @csrf
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
        </div>
    </div>
    <strong>{{ __('Description:') }}</strong> {{ $transaction->description }}<br>
    <strong>{{ __('Date:') }}</strong> {{ $transaction->date->format('d/m/Y') }}<br>
    <div class="mt-4 mb-1">{{ __('Journal Entries') }}</div>
    <table class="table table-striped table-sm table-hover">
        <thead>
            <tr>
                <th width="50%">{{ __('Account') }}</th>
                <th width="25%">{{ __('Debit') }}</th>
                <th width="25%">{{ __('Credit') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transaction->journalEntries as $entry)
                <tr>
                    <td>{{ $entry->account->name }}</td>
                    <td>{{ $entry->debit_amount ? fc($entry->debit_amount) : '' }}</td>
                    <td>{{ $entry->credit_amount ? fc($entry->credit_amount) : '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
