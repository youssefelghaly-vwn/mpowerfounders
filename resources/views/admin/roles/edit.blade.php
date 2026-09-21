<x-admin-layout title="Edit role">
    <form action="{{ route('admin.roles.update', $role) }}" method="POST">
        @include('admin.roles._form')
    </form>
</x-admin-layout>