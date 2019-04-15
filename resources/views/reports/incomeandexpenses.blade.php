@extends('layout')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <h2>{{ __('Income and Expenses Report') }}</h2>
            <h6 class="mb-4">For the period {{ $start->format('d/m/Y') }} - {{ $end->format('d/m/Y') }}</h6>
            <hr>
            <h4>{{ __('Income') }}</h4>
            @foreach ($income as $account)
                {{ $account->name }}: {{ fc($account->total) }}<br>
            @endforeach
            <span class="font-weight-bold">Total: {{ fc($totalIncome) }}</span><br>
            <hr>
            <h4>{{ __('Expenses') }}</h4>
            @foreach ($expenses as $account)
                {{ $account->name }}: {{ fc($account->total) }}<br>
            @endforeach
            <span class="font-weight-bold">Total: {{ fc($totalExpenses) }}</span><br>
            <hr>
            <h4 class="text-right text-{{ $totalIncome >= $totalExpenses ? 'success' : 'danger' }}">{{ $totalIncome >= $totalExpenses ? 'Profit' : 'Loss' }}: {{ fc(abs($totalIncome - $totalExpenses)) }}</h4>
        </div>
    </div>
</div>
@endsection
