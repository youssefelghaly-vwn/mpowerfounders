<x-admin-layout title="New role">
    <form action="{{ route('admin.roles.store') }}" method="POST">
        @include('admin.roles._form')
    </form>
</x-admin-layout>