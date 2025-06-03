import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:provider/provider.dart';
import '../services/auth_service.dart';

class NovaEvolucaoPage extends StatefulWidget {
  const NovaEvolucaoPage({super.key});

  @override
  State<NovaEvolucaoPage> createState() => _NovaEvolucaoPageState();
}

class _NovaEvolucaoPageState extends State<NovaEvolucaoPage> {
  final _formKey = GlobalKey<FormState>();
  String? _pacienteId;
  String _texto = '';
  bool _carregando = true;
  List<dynamic> _pacientes = [];

  @override
  void initState() {
    super.initState();
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

  Future<void> salvarEvolucao() async {
    if (!_formKey.currentState!.validate() || _pacienteId == null) return;
    _formKey.currentState!.save();

    final auth = Provider.of<AuthService>(context, listen: false);
    final response = await http.post(
      Uri.parse('http://localhost:8000/api/evolucoes'),
      headers: {
        'Authorization': 'Bearer ${auth.token}',
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
      body: jsonEncode({
        'paciente_id': _pacienteId,
        'texto': _texto,
      }),
    );

    if (response.statusCode == 201) {
      Navigator.pop(context);
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Evolução criada com sucesso')),
      );
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Erro ao criar evolução')),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Nova Evolução')),
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
                      value: _pacienteId,
                      items:
                          _pacientes.map<DropdownMenuItem<String>>((paciente) {
                        return DropdownMenuItem<String>(
                          value: paciente['id'].toString(),
                          child: Text(paciente['nome']),
                        );
                      }).toList(),
                      onChanged: (value) => setState(() => _pacienteId = value),
                      validator: (value) =>
                          value == null ? 'Selecione um paciente' : null,
                    ),
                    const SizedBox(height: 16),
                    TextFormField(
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
                      label: const Text('Salvar Evolução'),
                      onPressed: salvarEvolucao,
                    ),
                  ],
                ),
              ),
            ),
    );
  }
}
