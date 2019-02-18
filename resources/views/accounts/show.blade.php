@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <h2 class="col-sm-6">{{ __('Account Details') }}</h2>
        <div class="col-sm-6 text-right">
            <a href="{{ route('accounts.edit', $account) }}" class="btn btn-primary">{{ __('Edit') }}</a>
        </div>
    </div>
    <strong>{{ __('Name:') }}</strong> {{ $account->name }}<br>
    <strong>{{ __('Type:') }}</strong> {{ $account->type_name }}<br>
    <strong>{{ __('Balance:') }}</strong> {{ fc($account->balance) }}<br>
    <div class="mt-4 mb-1">{{ __('Journal Entries') }}</div>
    <table class="table table-striped table-sm table-hover">
        <thead>
            <tr>
                <th width="25%">{{ __('Date') }}</th>
                <th width="50%">{{ __('Transaction Description') }}</th>
                <th width="25%">{{ __('Amount') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($journalEntries as $entry)
                <tr data-href="{{ route('transactions.show', $entry->transaction_id) }}">
                    <td>{{ $entry->date->format('d/m/Y') }}</td>
                    <td>{{ $entry->description }}</td>
                    <td>{{ fc($entry->amount) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $journalEntries->links() }}
</div>
@endsection
