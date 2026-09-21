<x-admin-layout title="New pipeline stage">
    <form action="{{ route('admin.pipeline.store') }}" method="POST">
        @include('admin.pipeline._form')
    </form>
</x-admin-layout>
