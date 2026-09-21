<x-admin-layout title="New permission">
    <form action="{{ route('admin.permissions.store') }}" method="POST">
        @include('admin.permissions._form')
    </form>
</x-admin-layout>