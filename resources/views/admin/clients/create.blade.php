<x-admin-layout title="Add client">
    <form action="{{ route('admin.clients.store') }}" method="POST">
        @include('admin.clients._form')
    </form>
</x-admin-layout>
