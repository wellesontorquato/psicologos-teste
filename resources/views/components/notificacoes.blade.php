<div x-data="{ aberto: false }" class="relative">
    <button @click="aberto = !aberto" class="relative p-2 text-gray-700 hover:text-blue-600 focus:outline-none">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 
                     6.002 0 00-4-5.659V4a2 2 0 10-4 0v1.341C7.67 
                     6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 
                     1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        @if($naoLidas > 0)
            <span class="absolute top-0 right-0 bg-red-600 text-white text-xs px-1.5 py-0.5 rounded-full">
                {{ $naoLidas }}
            </span>
        @endif
    </button>

    <div x-show="aberto" @click.away="aberto = false"
         class="absolute left-0 mt-2 w-80 bg-white border border-gray-200 rounded-xl shadow-lg z-50">
        <div class="p-3 border-b font-semibold text-gray-700">Notificações</div>

        <ul class="max-h-96 overflow-y-auto divide-y">
            @forelse($notificacoes as $notificacao)
                <li class="p-3 hover:bg-gray-100 text-sm">
                    <a href="{{ route('notificacoes.acao', ['id' => $notificacao->id]) }}"
                    class="notificacao-link flex items-start gap-2"
                    data-tipo="{{ $notificacao->tipo }}"
                    data-id="{{ $notificacao->id }}">
                        <div>
                            <span class="font-medium">{{ $notificacao->titulo }}</span><br>
                            <span class="text-gray-500 text-xs">{{ $notificacao->created_at->diffForHumans() }}</span>
                        </div>
                    </a>
                </li>
            @empty
                <li class="p-3 text-sm text-gray-500">Sem novas notificações.</li>
            @endforelse
        </ul>
    </div>
</div>
