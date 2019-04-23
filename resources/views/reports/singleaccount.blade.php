@extends('layout')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <h2>{{ __('Single Account Report') }}</h2>
            <h6 class="mb-4">For the period {{ $start->format('d/m/Y') }} - {{ $end->format('d/m/Y') }}</h6>
            {{ __('Opening Balance:') }} {{ fc($openingBalance) }}<br>
            {{ __('Closing Balance:') }} {{ fc($closingBalance) }}<br>
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
        </div>
    </div>
</div>
@endsection
