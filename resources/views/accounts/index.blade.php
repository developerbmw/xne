@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">{{ __('Accounts') }}</h2>
    @if ($accounts->count() > 0)
        <table class="table table-striped table-sm table-hover">
            <thead>
                <tr>
                    <th width="50%">{{ __('Name') }}</th>
                    <th width="25%">{{ __('Type') }}</th>
                    <th width="25%">{{ __('Balance') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($accounts as $account)
                    <tr data-href="{{ route('accounts.show', $account) }}">
                        <td>{{ $account->name }}</td>
                        <td>{{ $account->type_name }}</td>
                        <td>{{ fc($account->balance) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{ $accounts->links() }}
    @else
        {{ __('No accounts.') }}
        <a href="{{ route('accounts.create') }}">{{ __('Click here to create your first account.') }}</a>
    @endif
</div>
@endsection
