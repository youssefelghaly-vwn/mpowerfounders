<x-admin-layout :title="'Edit stage — ' . $stage->name">
    <form action="{{ route('admin.pipeline.update', $stage) }}" method="POST">
        @include('admin.pipeline._form')
    </form>
</x-admin-layout>
