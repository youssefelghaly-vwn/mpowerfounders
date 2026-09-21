<x-admin-layout :title="'Edit — ' . $client->name">
    <form action="{{ route('admin.clients.update', $client) }}" method="POST">
        @include('admin.clients._form')
    </form>
</x-admin-layout>
