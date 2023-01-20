@extends('nova-multitenancy::layout')
@section('title', __('Selecione uma conta'))

@section('content')
    <h1 class="text-4xl font-bold tracking-tight sm:text-center sm:text-6xl">
        Selecione uma conta
    </h1>
    <p class="mt-6 text-lg leading-8 text-gray-600 sm:text-center">
        Você possui mais de uma conta associada ao seu usuário. Favor selecionar uma conta para continuar.
    </p>
    <div class="mt-8 flex flex-wrap justify-evenly">
        @foreach($tenants as $id => $name)
            <div class="mt-3 sm:ml-3">
                <form method="POST">
                    <form method="POST">
                        <input type="hidden" name="tenant" value="{{$id}}">
                        @csrf
                        <button type="submit"
                                class=" mt-8w-full px-3 md:px-5 py-1 border border-transparent text-base
                                    leading-6 font-medium rounded-md text-white bg-red-500 hover:bg-red-400 focus:outline-none
                                    focus:border-red-600 focus:shadow-outline-red transition duration-150
                                    ease-in-out md:text-lg  transition duration-150 ease-in-out rounded-sm shadow">
                            {{ $name }}
                        </button>
                    </form>
            </div>
        @endforeach
    </div>
    </div>
@endsection
