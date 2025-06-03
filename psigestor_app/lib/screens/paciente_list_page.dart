import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:flutter/scheduler.dart';
import 'package:provider/provider.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:http/http.dart' as http;

import 'package:psigestor_app/models/paciente.dart';
import 'package:psigestor_app/services/paciente_service.dart';
import 'package:psigestor_app/services/auth_service.dart';
import 'paciente_form_page.dart';
import 'package:psigestor_app/loading_overlay.dart';

class PacienteListPage extends StatefulWidget {
  const PacienteListPage({super.key});

  @override
  State<PacienteListPage> createState() => _PacienteListPageState();
}

class _PacienteListPageState extends State<PacienteListPage> {
  List<Paciente> pacientes = [];
  bool carregando = true;
  bool _carregado = false;

  @override
  void didChangeDependencies() {
    super.didChangeDependencies();
    if (!_carregado) {
      carregarPacientes();
      _carregado = true;
    }
  }

  Future<void> carregarPacientes() async {
    try {
      final token = Provider.of<AuthService>(context, listen: false).token!;
      final resultado = await PacienteService().listar(token);
      setState(() {
        pacientes = resultado;
        carregando = false;
      });
    } catch (e) {
      setState(() => carregando = false);
      SchedulerBinding.instance.addPostFrameCallback((_) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Erro ao carregar pacientes: $e')),
        );
      });
    }
  }

  void confirmarExclusao(Paciente paciente) async {
    final confirmar = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        title: const Text('Confirmação'),
        content: Text('Deseja excluir o paciente "${paciente.nome}"?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx, false),
            child: const Text('Cancelar'),
          ),
          ElevatedButton(
            onPressed: () => Navigator.pop(ctx, true),
            child: const Text('Excluir'),
          ),
        ],
      ),
    );

    if (confirmar == true) {
      try {
        final token = Provider.of<AuthService>(context, listen: false).token;
        
        LoadingOverlay.show(context);
        await PacienteService().excluir(paciente.id!, token!);
        LoadingOverlay.hide();

        carregarPacientes();
      } catch (e) {
        SchedulerBinding.instance.addPostFrameCallback((_) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(content: Text('Erro ao excluir paciente: $e')),
          );
        });
      }
    }
  }

  void _editarPaciente(int pacienteId) async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('token') ?? '';
    LoadingOverlay.show(context);    
    final response = await http.get(
      Uri.parse('http://localhost:8000/api/pacientes-json/$pacienteId'),
      headers: {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
      },
    );

    LoadingOverlay.hide();

    if (response.statusCode == 200) {
      final pacienteJson = json.decode(response.body);
      final paciente = Paciente.fromJson(pacienteJson);

      final atualizado = await Navigator.push(
        context,
        MaterialPageRoute(
          builder: (_) => PacienteFormPage(paciente: paciente),
        ),
      );
      if (atualizado == true) carregarPacientes();
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Erro ao carregar paciente completo')),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: carregando
          ? const Center(child: CircularProgressIndicator())
          : pacientes.isEmpty
              ? const Center(child: Text('Nenhum paciente cadastrado.'))
              : ListView.builder(
                  itemCount: pacientes.length,
                  itemBuilder: (context, index) {
                    final p = pacientes[index];
                    return Card(
                      margin: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                      child: ListTile(
                        title: Text(p.nome),
                        subtitle: Text('Telefone: ${p.telefone}'),
                        trailing: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            IconButton(
                              icon: const Icon(Icons.edit),
                              onPressed: () => _editarPaciente(p.id!),
                            ),
                            IconButton(
                              icon: const Icon(Icons.delete, color: Colors.red),
                              onPressed: () => confirmarExclusao(p),
                            ),
                          ],
                        ),
                      ),
                    );
                  },
                ),
      floatingActionButton: FloatingActionButton(
        onPressed: () async {
          final criado = await Navigator.push(
            context,
            MaterialPageRoute(builder: (_) => const PacienteFormPage()),
          );
          if (criado == true) carregarPacientes();
        },
        child: const Icon(Icons.add),
      ),
    );
  }
}
