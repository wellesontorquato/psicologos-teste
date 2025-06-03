class Sessao {
  final int id;
  final int pacienteId;
  final DateTime dataHora;
  final int duracao;
  final double valor;
  final bool foiPago;
  final String? status;
  final String? pacienteNome;

  Sessao({
    required this.id,
    required this.pacienteId,
    required this.dataHora,
    required this.duracao,
    required this.valor,
    required this.foiPago,
    this.status,
    this.pacienteNome,
  });

  factory Sessao.fromJson(Map<String, dynamic> json) {
    return Sessao(
      id: json['id'],
      pacienteId: json['paciente_id'] ?? 0,
      dataHora: DateTime.parse(json['data_hora']),
      duracao: json['duracao'],
      valor: double.tryParse(json['valor'].toString()) ?? 0.0,
      foiPago: json['foi_pago'] == true || json['foi_pago'] == 1,
      status: json['status_confirmacao'], // pode ser null
      pacienteNome: json['paciente']?['nome'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'paciente_id': pacienteId,
      'data_hora': dataHora.toIso8601String(),
      'duracao': duracao,
      'valor': valor,
      'foi_pago': foiPago,
      // não inclui 'status_confirmacao' pois não é usado na criação
    };
  }

  String get dataHoraFormatada {
    final d = dataHora;
    return '${d.day.toString().padLeft(2, '0')}/${d.month.toString().padLeft(2, '0')}/${d.year} ${d.hour.toString().padLeft(2, '0')}:${d.minute.toString().padLeft(2, '0')}';
  }

  String get statusDisplay {
    switch ((status ?? 'PENDENTE').toUpperCase()) {
      case 'CONFIRMADA':
        return '✅ Confirmada';
      case 'CANCELADA':
        return '❌ Cancelada';
      case 'REMARCAR':
        return '🔄 Remarcar';
      case 'REMARCADO':
        return '📅 Remarcado';
      default:
        return '⏳ Pendente';
    }
  }
}
