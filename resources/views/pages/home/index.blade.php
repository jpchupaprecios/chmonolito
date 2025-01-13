@extends('layouts.app')

@section('content')
    @include('pages.home.components.banner')
    @include('pages.home.components.featured')
    @include('pages.home.components.categories')
    @include('pages.home.components.offers')
    <script>
        // Verifica si el client_session_id existe en localStorage, si no, lo crea
        let clientSessionId = null;
        if (!localStorage.getItem('client_session_id')) {
            clientSessionId = generateUniqueId();
            localStorage.setItem('client_session_id', clientSessionId);

        }else{
            clientSessionId = localStorage.getItem('client_session_id');
        }

        function generateUniqueId() {
            return `${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
        }

        // Realiza la llamada AJAX
        sendClientSessionId(clientSessionId);

        function sendClientSessionId(clientSessionId) {
            fetch(`/bh/${clientSessionId}/`, {
                method: 'GET', // Usa GET ya que tu ruta es una GET
                headers: {
                    'X-Requested-With': 'XMLHttpRequest', // Indica que es una llamada AJAX
                },
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la solicitud');
                    }
                    return response.json(); // Procesa la respuesta como JSON si es necesario
                })
                .then(data => {
                    console.log('Respuesta del servidor:', data);
                })
                .catch(error => {
                    console.error('Error en la llamada AJAX:', error);
                });
        }
    </script>
@endsection
