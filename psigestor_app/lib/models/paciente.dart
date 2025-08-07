class Paciente {
  final int id;
  final String nome;
  final String dataNascimento;
  final String sexo;
  final String telefone;
  final String email;
  final String cpf;
  final bool exigeNotaFiscal;

  final String? nomeContatoEmergencia;
  final String? telefoneContatoEmergencia;
  final String? parentescoContatoEmergencia;

  final String? cep;
  final String? rua;
  final String? numero;
  final bool semNumero;
  final String? complemento;
  final String? bairro;
  final String? cidade;
  final String? uf;

  final String? observacoes;

  Paciente({
    required this.id,
    required this.nome,
    required this.dataNascimento,
    required this.sexo,
    required this.telefone,
    required this.email,
    required this.cpf,
    required this.exigeNotaFiscal,
    this.nomeContatoEmergencia,
    this.telefoneContatoEmergencia,
    this.parentescoContatoEmergencia,
    this.cep,
    this.rua,
    this.numero,
    this.semNumero = false,
    this.complemento,
    this.bairro,
    this.cidade,
    this.uf,
    this.observacoes,
  });

  factory Paciente.fromJson(Map<String, dynamic> json) {
    return Paciente(
      id: json['id'],
      nome: json['nome'] ?? '',
      dataNascimento: json['data_nascimento'] ?? '',
      sexo: json['sexo'] ?? '',
      telefone: json['telefone'] ?? '',
      email: json['email'] ?? '',
      cpf: json['cpf'] ?? '',
      exigeNotaFiscal:
          json['exige_nota_fiscal'] == true || json['exige_nota_fiscal'] == 1,

      nomeContatoEmergencia: json['nome_contato_emergencia'],
      telefoneContatoEmergencia: json['telefone_contato_emergencia'],
      parentescoContatoEmergencia: json['parentesco_contato_emergencia'],

      cep: json['cep'],
      rua: json['rua'],
      numero: json['numero'],
      semNumero: (json['numero']?.toString().toUpperCase() == 'S/N'),
      complemento: json['complemento'],
      bairro: json['bairro'],
      cidade: json['cidade'],
      uf: json['uf'],

      observacoes: json['observacoes'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'nome': nome,
      'data_nascimento': dataNascimento,
      'sexo': sexo,
      'telefone': telefone,
      'email': email,
      'cpf': cpf,
      'exige_nota_fiscal': exigeNotaFiscal ? 1 : 0,

      'nome_contato_emergencia': nomeContatoEmergencia,
      'telefone_contato_emergencia': telefoneContatoEmergencia,
      'parentesco_contato_emergencia': parentescoContatoEmergencia,

      'cep': cep,
      'rua': rua,
      'numero': semNumero ? 'S/N' : numero,
      'complemento': complemento,
      'bairro': bairro,
      'cidade': cidade,
      'uf': uf,

      'observacoes': observacoes,
    };
  }
}
