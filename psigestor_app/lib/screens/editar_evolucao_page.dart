import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:provider/provider.dart';
import '../services/auth_service.dart';

class EditarEvolucaoPage extends StatefulWidget {
  final int evolucaoId;
  final int pacienteId;
  final String texto;

  const EditarEvolucaoPage({
    super.key,
    required this.evolucaoId,
    required this.pacienteId,
    required this.texto,
  });

  @override
  State<EditarEvolucaoPage> createState() => _EditarEvolucaoPageState();
}

class _EditarEvolucaoPageState extends State<EditarEvolucaoPage> {
  final _formKey = GlobalKey<FormState>();
  String? _pacienteIdSelecionado;
  String _texto = '';
  bool _carregando = true;
  List<dynamic> _pacientes = [];

  @override
  void initState() {
    super.initState();
    _texto = widget.texto;
    _pacienteIdSelecionado = widget.pacienteId.toString();
    carregarPacientes();
  }

  Future<void> carregarPacientes() async {
    final auth = Provider.of<AuthService>(context, listen: false);
    final response = await http.get(
      Uri.parse('http://localhost:8000/api/pacientes'),
      headers: {
        'Authorization': 'Bearer ${auth.token}',
        'Accept': 'application/json',
      },
    );

    if (response.statusCode == 200) {
      setState(() {
        _pacientes = jsonDecode(response.body);
        _carregando = false;
      });
    } else {
      setState(() => _carregando = false);
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Erro ao carregar pacientes')),
      );
    }
  }

  Future<void> atualizarEvolucao() async {
    if (!_formKey.currentState!.validate()) return;
    _formKey.currentState!.save();

    final auth = Provider.of<AuthService>(context, listen: false);
    final response = await http.put(
      Uri.parse('http://localhost:8000/api/evolucoes/${widget.evolucaoId}'),
      headers: {
        'Authorization': 'Bearer ${auth.token}',
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
      body: jsonEncode({
        'paciente_id': _pacienteIdSelecionado,
        'texto': _texto,
      }),
    );

    if (response.statusCode == 200) {
      Navigator.pop(context);
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Evolução atualizada com sucesso')),
      );
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Erro ao atualizar evolução')),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Editar Evolução')),
      body: _carregando
          ? const Center(child: CircularProgressIndicator())
          : Padding(
              padding: const EdgeInsets.all(16),
              child: Form(
                key: _formKey,
                child: ListView(
                  children: [
                    DropdownButtonFormField<String>(
                      decoration: const InputDecoration(labelText: 'Paciente'),
                      value: _pacienteIdSelecionado,
                      items:
                          _pacientes.map<DropdownMenuItem<String>>((paciente) {
                        return DropdownMenuItem<String>(
                          value: paciente['id'].toString(),
                          child: Text(paciente['nome']),
                        );
                      }).toList(),
                      onChanged: (value) =>
                          setState(() => _pacienteIdSelecionado = value),
                      validator: (value) =>
                          value == null ? 'Selecione um paciente' : null,
                    ),
                    const SizedBox(height: 16),
                    TextFormField(
                      initialValue: _texto,
                      decoration: const InputDecoration(
                        labelText: 'Texto da Evolução',
                        border: OutlineInputBorder(),
                      ),
                      maxLines: 6,
                      onSaved: (value) => _texto = value!.trim(),
                      validator: (value) => value == null || value.isEmpty
                          ? 'Digite o texto'
                          : null,
                    ),
                    const SizedBox(height: 20),
                    ElevatedButton.icon(
                      icon: const Icon(Icons.save),
                      label: const Text('Salvar Alterações'),
                      onPressed: atualizarEvolucao,
                    ),
                  ],
                ),
              ),
            ),
    );
  }
}
