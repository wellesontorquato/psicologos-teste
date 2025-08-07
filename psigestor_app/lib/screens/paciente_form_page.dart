import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';
import 'package:psigestor_app/models/paciente.dart';
import 'package:shared_preferences/shared_preferences.dart';

class PacienteFormPage extends StatefulWidget {
  final Paciente? paciente;

  const PacienteFormPage({Key? key, this.paciente}) : super(key: key);

  @override
  State<PacienteFormPage> createState() => _PacienteFormPageState();
}

class _PacienteFormPageState extends State<PacienteFormPage> {
  final _formKey = GlobalKey<FormState>();

  final nome = TextEditingController();
  final dataNascimento = TextEditingController();
  final telefone = TextEditingController();
  final email = TextEditingController();
  final cpf = TextEditingController();
  final cep = TextEditingController();
  final rua = TextEditingController();
  final numero = TextEditingController();
  final complemento = TextEditingController();
  final bairro = TextEditingController();
  final cidade = TextEditingController();
  final uf = TextEditingController();
  final observacoes = TextEditingController();
  final nomeContato = TextEditingController();
  final telefoneContato = TextEditingController();

  String sexo = '';
  String parentesco = '';
  bool exigeNota = false;
  bool semNumero = false;

  final List<String> opcoesSexo = ['M', 'F', 'Outro'];
  final List<String> opcoesParentesco = [
    'Pai', 'Mãe', 'Cônjuge', 'Filho(a)', 'Irmão(ã)', 'Amigo(a)', 'Outro'
  ];

  @override
  void initState() {
    super.initState();
    if (widget.paciente != null) {
      final p = widget.paciente!;
      nome.text = p.nome ?? '';
      dataNascimento.text = _formatarDataParaExibicao(p.dataNascimento ?? '');
      telefone.text = p.telefone ?? '';
      email.text = p.email ?? '';
      cpf.text = p.cpf ?? '';
      cep.text = p.cep ?? '';
      rua.text = p.rua ?? '';
      numero.text = p.numero ?? '';
      complemento.text = p.complemento ?? '';
      bairro.text = p.bairro ?? '';
      cidade.text = p.cidade ?? '';
      uf.text = p.uf ?? '';
      observacoes.text = p.observacoes ?? '';
      nomeContato.text = p.nomeContatoEmergencia ?? '';
      telefoneContato.text = p.telefoneContatoEmergencia ?? '';
      sexo = opcoesSexo.contains(p.sexo) ? p.sexo! : '';
      parentesco = opcoesParentesco.contains(p.parentescoContatoEmergencia)
          ? p.parentescoContatoEmergencia!
          : '';
      exigeNota = p.exigeNotaFiscal == 1;
      semNumero = p.numero == 'S/N';
    }
  }

  String _formatarDataParaSalvar(String dataBr) {
    try {
      final partes = dataBr.split('/');
      if (partes.length == 3) {
        final dia = partes[0];
        final mes = partes[1];
        final ano = partes[2];
        return '$ano-$mes-$dia';
      }
      return dataBr;
    } catch (_) {
      return dataBr;
    }
  }

  String _formatarDataParaExibicao(String dataIso) {
    try {
      final partes = dataIso.split('-');
      if (partes.length == 3) {
        return '${partes[2]}/${partes[1]}/${partes[0]}';
      }
      return dataIso;
    } catch (_) {
      return dataIso;
    }
  }

  Future<void> _selecionarDataNascimento(BuildContext context) async {
    final dataInicial = DateTime.tryParse(
      _formatarDataParaSalvar(dataNascimento.text),
    ) ?? DateTime(2000, 1, 1);

    final DateTime? picked = await showDatePicker(
      context: context,
      initialDate: dataInicial,
      firstDate: DateTime(1900),
      lastDate: DateTime.now(),
      locale: const Locale('pt', 'BR'),
    );

    if (picked != null) {
      setState(() {
        dataNascimento.text =
            '${picked.day.toString().padLeft(2, '0')}/${picked.month.toString().padLeft(2, '0')}/${picked.year}';
      });
    }
  }

  Future<void> buscarCEP() async {
    final cepClean = cep.text.replaceAll(RegExp(r'\D'), '');
    if (cepClean.length != 8) return;

    final response = await http.get(Uri.parse('https://viacep.com.br/ws/$cepClean/json/'));
    if (response.statusCode == 200) {
      final data = json.decode(response.body);
      setState(() {
        rua.text = data['logradouro'] ?? '';
        bairro.text = data['bairro'] ?? '';
        cidade.text = data['localidade'] ?? '';
        uf.text = data['uf'] ?? '';
      });
    }
  }

  Future<void> salvarPaciente() async {
    if (!_formKey.currentState!.validate()) return;

    final data = {
      'nome': nome.text,
      'data_nascimento': _formatarDataParaSalvar(dataNascimento.text),
      'sexo': sexo,
      'telefone': telefone.text,
      'email': email.text,
      'cpf': cpf.text,
      'cep': cep.text,
      'rua': rua.text,
      'numero': semNumero ? 'S/N' : numero.text,
      'sem_numero': semNumero,
      'complemento': complemento.text,
      'bairro': bairro.text,
      'cidade': cidade.text,
      'uf': uf.text,
      'observacoes': observacoes.text,
      'exige_nota_fiscal': exigeNota ? '1' : '0',
      'nome_contato_emergencia': nomeContato.text,
      'telefone_contato_emergencia': telefoneContato.text,
      'parentesco_contato_emergencia': parentesco,
    };

    final isEditing = widget.paciente != null;
    final url = isEditing
        ? 'http://localhost:8000/api/pacientes-json/${widget.paciente!.id}'
        : 'http://localhost:8000/api/pacientes-json';

    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('token') ?? '';

    final response = await http.post(
      Uri.parse(url),
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': 'Bearer $token',
      },
      body: json.encode(data),
    );

    if (response.statusCode == 200 || response.statusCode == 201) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Paciente ${isEditing ? "atualizado" : "salvo"} com sucesso!')),
      );
      Navigator.pop(context);
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Erro: ${response.body}')),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text(widget.paciente != null ? 'Editar Paciente' : 'Novo Paciente')),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Form(
          key: _formKey,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              _buildSectionTitle(Icons.person, 'Dados Pessoais'),
              _buildCard([
                _buildField('Nome', nome, validator: _obrigatorio),
                GestureDetector(
                  onTap: () => _selecionarDataNascimento(context),
                  child: AbsorbPointer(
                    child: _buildField(
                      'Data de nascimento',
                      dataNascimento,
                      validator: _obrigatorio,
                      readOnly: true,
                    ),
                  ),
                ),
                _buildDropdown('Sexo', opcoesSexo, sexo, (v) => setState(() => sexo = v ?? ''), validator: _obrigatorio),
                _buildField('Telefone', telefone, type: TextInputType.phone),
                _buildField('Email', email, type: TextInputType.emailAddress),
                _buildField('CPF', cpf),
              ]),

              _buildSectionTitle(Icons.location_on, 'Endereço'),
              _buildCard([
                _buildField('CEP', cep, onSubmit: (_) => buscarCEP()),
                _buildField('Rua', rua),
                Row(
                  children: [
                    Expanded(child: _buildField('Número', numero, readOnly: semNumero)),
                    Checkbox(value: semNumero, onChanged: (v) => setState(() => semNumero = v ?? false)),
                    const Text('S/N'),
                  ],
                ),
                _buildField('Complemento', complemento),
                _buildField('Bairro', bairro),
                _buildField('Cidade', cidade),
                _buildField('UF', uf, maxLength: 2),
              ]),

              _buildSectionTitle(Icons.health_and_safety, 'Informações Clínicas'),
              _buildCard([
                _buildField('Observações', observacoes),
                SwitchListTile(
                  title: const Text('Exige nota fiscal Receita Saúde'),
                  value: exigeNota,
                  onChanged: (v) => setState(() => exigeNota = v),
                ),
              ]),

              _buildSectionTitle(Icons.contact_phone, 'Contato de Emergência'),
              _buildCard([
                _buildField('Nome do contato', nomeContato),
                _buildField('Telefone do contato', telefoneContato),
                _buildDropdown('Parentesco', opcoesParentesco, parentesco, (v) => setState(() => parentesco = v ?? ''), validator: _obrigatorio),
              ]),

              const SizedBox(height: 24),
              ElevatedButton.icon(
                onPressed: salvarPaciente,
                icon: const Icon(Icons.save),
                label: const Text('Salvar Paciente'),
                style: ElevatedButton.styleFrom(
                  minimumSize: const Size.fromHeight(48),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  // Helpers

  String? _obrigatorio(String? value) => (value == null || value.isEmpty) ? 'Obrigatório' : null;

  Widget _buildSectionTitle(IconData icon, String title) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 8, top: 24),
      child: Row(
        children: [
          Icon(icon, color: Theme.of(context).primaryColor),
          const SizedBox(width: 8),
          Text(title, style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
        ],
      ),
    );
  }

  Widget _buildCard(List<Widget> children) {
    return Card(
      elevation: 2,
      margin: const EdgeInsets.only(bottom: 16),
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          children: children.map((e) => Padding(padding: const EdgeInsets.symmetric(vertical: 8), child: e)).toList(),
        ),
      ),
    );
  }

  Widget _buildField(String label, TextEditingController controller, {
    TextInputType type = TextInputType.text,
    String? Function(String?)? validator,
    Function(String)? onSubmit,
    bool readOnly = false,
    int? maxLength,
  }) {
    return TextFormField(
      controller: controller,
      keyboardType: type,
      validator: validator,
      readOnly: readOnly,
      maxLength: maxLength,
      decoration: InputDecoration(labelText: label, border: const OutlineInputBorder()),
      onFieldSubmitted: onSubmit,
    );
  }

  Widget _buildDropdown(String label, List<String> options, String value,
      void Function(String?) onChanged, {String? Function(String?)? validator}) {
    return DropdownButtonFormField<String>(
      value: options.contains(value) ? value : null,
      decoration: InputDecoration(labelText: label, border: const OutlineInputBorder()),
      items: options.map((e) => DropdownMenuItem(value: e, child: Text(e))).toList(),
      onChanged: onChanged,
      validator: validator,
    );
  }
}
