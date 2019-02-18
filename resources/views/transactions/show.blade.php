@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Transaction Details</h2>
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
            @foreach ($journalEntries as $entry)
                <tr>
                    <td>{{ $entry->account->name }}</td>
                    <td>{{ fc($entry->debit_amount) }}</td>
                    <td>{{ fc($entry->credit_amount) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $journalEntries->links() }}
</div>
@endsection
