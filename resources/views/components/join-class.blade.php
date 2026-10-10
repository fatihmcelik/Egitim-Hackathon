@props(['field' => 'code'])
@php $u = auth()->user(); @endphp

@if($u && ! $u->classroom_id && \Illuminate\Support\Facades\Route::has('student.joinClassroom'))
    <form method="POST" action="{{ route('student.joinClassroom') }}" class="space-y-3">
        @csrf
        <label class="block text-sm text-gray-300">Öğretmeninin verdiği sınıf kodunu gir</label>
        <div class="flex gap-2">
            <input type="text" name="{{ $field }}" required maxlength="12" placeholder="Örn: A1B2C3"
                   class="flex-1 min-w-0 bg-[#0e1630] border border-indigo-400/50 text-white uppercase tracking-widest px-4 py-3 rounded-xl outline-none placeholder-gray-600 focus:ring-2 focus:ring-indigo-400">
            <button type="submit" class="px-6 rounded-xl font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 transition">
                Katıl
            </button>
        </div>
    </form>
@endif