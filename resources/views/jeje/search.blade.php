@extends('layouts.app')
@section('title', 'Cari JJ Kamu')

@section('content')
    <div class="row">
        <div class="col text-center">
            <img src="{{ asset(config('meta.logo')) }}" alt="logo" width="150">
            <h1 class="responsive-title fw-bold">Mahakarya Agency</h1>
            <p class="fst-italic">
                ~ Pencipta pertama aplikasi bubblephoto ~
            </p>
        </div>
    </div>

    <div class="row">
        <div class="col text-center">
            <b class="text-primary">CS: 0852-8153-1230</b>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-lg-6 text-center mx-auto">
            <h6 class="opacity-75">
                Silahkan cari JJ kamu disini berdasarkan username Tiktok kamu.
            </h6>
            <div class="my-4">
                <form action="{{ route('jeje.index') }}" method="GET">
                    <div class="input-group">
                        <input type="text" class="form-control" name="username" placeholder="Cari username...">
                        <button class="btn btn-primary" type="submit">Cari</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-center">
                <a href="{{ route('jeje.create') }}" class="btn btn-primary">
                    <i class='fe fe-upload'></i>
                    UPLOAD JJ DISINI
                </a>
            </div>
        </div>
    </div>

    @if ($results && $results->count())
        <div class="mt-4">
            <div class="row">
                @foreach ($results as $item)
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                        <div class="card custom-card">
                            <video class="card-img-top" controls>
                                <source src="{{ asset("storage/jj/{$item->filename}") }}" type="video/mp4">
                            </video>
                            <div class="card-body">
                                <h6 class="card-title text-truncate" title="{{ $item->display_type_label }}">
                                    {{ $item->display_type_label }}
                                </h6>
                                <p class="card-text">
                                    Username 1: {{ $item->username_1 }} <br>
                                    Username 2: {{ $item->username_2 ?? '-' }}
                                </p>
                            </div>
                            <div class="card-footer">
                                <a href="{{ route('jeje.edit', $item->id) }}" class="btn btn-secondary spa-link">
                                    <i class='fe fe-edit'></i> Ganti
                                </a>
                                <button type="button" class="btn btn-danger btn-delete-jj" data-url="{{ route('jeje.destroy', $item->id) }}">
                                    <i class='fe fe-trash'></i> Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-3">
                {{ $results->links() }}
            </div>
        </div>
    @elseif(request('username'))
        <div class="mt-4">
            <p class="text-danger text-center">Tidak ada hasil untuk "{{ request('username') }}"</p>
        </div>
    @endif
@endsection

@section('scripts')
    <script data-partial="1">
        $(".btn-delete-jj").on("click", function(e) {
            e.preventDefault();
            const url = $(this).data('url');

            ajaxRequest({
                url: url,
                method: "DELETE",
                confirm: {
                    title: "Hapus Video JJ?",
                    text: "Video JJ ini akan dihapus permanen!",
                    confirmButtonText: "Ya, hapus!",
                },
            });
        });
    </script>
@endsection
