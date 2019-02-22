@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">{{ __('Transactions') }}</h2>
    @if ($transactions->count() > 0)
        <table class="table table-striped table-sm table-hover">
            <thead>
                <tr>
                    <th width="25%">{{ __('Date') }}</th>
                    <th width="75%">{{ __('Description') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transactions as $transaction)
                    <tr data-href="{{ route('transactions.show', $transaction) }}">
                        <td>{{ $transaction->date->format('d/m/Y') }}</td>
                        <td>{{ $transaction->description }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $transactions->links() }}
    @else
        {{ __('No transactions.') }}
        <a href="{{ route('transactions.create') }}">{{ __('Click here to get started with your first transaction.') }}</a>
    @endif
</div>
@endsection
