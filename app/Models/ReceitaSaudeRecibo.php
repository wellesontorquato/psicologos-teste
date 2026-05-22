<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceitaSaudeRecibo extends Model
{
    use HasFactory;

    protected $table = 'receita_saude_recibos';

    protected $fillable = [
        'user_id',
        'paciente_id',
        'sessao_id',
        'data_pagamento',
        'data_atendimento',
        'codigo_rendimento',
        'codigo_ocupacao',
        'valor_pagamento',
        'valor_deducao',
        'descricao',
        'recebido_de',
        'cpf_pagador',
        'cpf_beneficiario',
        'indicador_cpf_nao_informado',
        'cnpj',
        'indicador_irrf',
        'valor_irrf',
        'indicador_recibo',
        'cpf_profissional',
        'registro_profissional',
        'numero_recibo',
        'status',
        'exportado_em',
        'emitido_em',
        'observacoes',
    ];

    protected $casts = [
        'data_pagamento' => 'date',
        'data_atendimento' => 'date',
        'valor_pagamento' => 'decimal:2',
        'valor_deducao' => 'decimal:2',
        'valor_irrf' => 'decimal:2',
        'exportado_em' => 'datetime',
        'emitido_em' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function sessao()
    {
        return $this->belongsTo(Sessao::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'rascunho' => 'Rascunho',
            'exportado' => 'Exportado',
            'emitido' => 'Emitido',
            'cancelado' => 'Cancelado',
            default => ucfirst((string) $this->status),
        };
    }
}
