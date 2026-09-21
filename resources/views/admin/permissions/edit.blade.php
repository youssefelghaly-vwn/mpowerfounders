<x-admin-layout title="Edit permission">
    <form action="{{ route('admin.permissions.update', $permission) }}" method="POST">
        @include('admin.permissions._form')
    </form>
</x-admin-layout>