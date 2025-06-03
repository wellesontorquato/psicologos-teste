import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';
import '../services/auth_service.dart';
import 'editar_evolucao_page.dart';
import 'nova_evolucao_page.dart';

class EvolucoesPage extends StatefulWidget {
  const EvolucoesPage({super.key});

  @override
  State<EvolucoesPage> createState() => _EvolucoesPageState();
}

class _EvolucoesPageState extends State<EvolucoesPage> {
  List<dynamic> evolucoes = [];
  bool carregando = true;

  @override
  void initState() {
    super.initState();
    carregarEvolucoes();
  }

  Future<void> carregarEvolucoes() async {
    setState(() => carregando = true);
    final auth = Provider.of<AuthService>(context, listen: false);
    final response = await http.get(
      Uri.parse('http://localhost:8000/api/evolucoes-json'),
      headers: {
        'Authorization': 'Bearer ${auth.token}',
        'Accept': 'application/json',
      },
    );

    if (response.statusCode == 200) {
      setState(() {
        evolucoes = jsonDecode(response.body);
        carregando = false;
      });
    } else {
      setState(() => carregando = false);
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Erro ao carregar evoluções')),
      );
    }
  }

  Future<void> excluirEvolucao(int id) async {
    final auth = Provider.of<AuthService>(context, listen: false);
    final confirm = await showDialog<bool>(
      context: context,
      builder: (_) => AlertDialog(
        title: const Text('Confirmar exclusão'),
        content: const Text('Tem certeza que deseja excluir esta evolução?'),
        actions: [
          TextButton(
              onPressed: () => Navigator.pop(context, false),
              child: const Text('Cancelar')),
          TextButton(
              onPressed: () => Navigator.pop(context, true),
              child: const Text('Excluir')),
        ],
      ),
    );

    if (confirm == true) {
      final response = await http.delete(
        Uri.parse('http://localhost:8000/api/evolucoes/$id'),
        headers: {
          'Authorization': 'Bearer ${auth.token}',
          'Accept': 'application/json',
        },
      );

      if (response.statusCode == 200) {
        carregarEvolucoes();
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Evolução excluída com sucesso')),
        );
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Erro ao excluir evolução')),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return carregando
        ? const Center(child: CircularProgressIndicator())
        : Column(
            children: [
              Padding(
                padding: const EdgeInsets.all(16),
                child: ElevatedButton.icon(
                  icon: const Icon(Icons.add),
                  label: const Text('Nova Evolução'),
                  onPressed: () async {
                    await Navigator.push(
                      context,
                      MaterialPageRoute(
                          builder: (_) => const NovaEvolucaoPage()),
                    );
                    carregarEvolucoes();
                  },
                ),
              ),
              Expanded(
                child: evolucoes.isEmpty
                    ? const Center(child: Text('Nenhuma evolução registrada.'))
                    : ListView.builder(
                        itemCount: evolucoes.length,
                        itemBuilder: (context, index) {
                          final e = evolucoes[index];
                          return Card(
                            margin: const EdgeInsets.symmetric(
                                horizontal: 16, vertical: 8),
                            child: ListTile(
                              leading: const Icon(Icons.note),
                              title: Text(e['paciente']['nome']),
                              subtitle: Text(
                                DateFormat('dd/MM/yyyy')
                                    .format(DateTime.parse(e['data'])),
                              ),
                              onTap: () async {
                                await Navigator.push(
                                  context,
                                  MaterialPageRoute(
                                    builder: (_) => EditarEvolucaoPage(
                                      evolucaoId: e['id'],
                                      pacienteId: e['paciente']['id'],
                                      texto: e['texto'],
                                    ),
                                  ),
                                );
                                carregarEvolucoes();
                              },
                              trailing: IconButton(
                                icon:
                                    const Icon(Icons.delete, color: Colors.red),
                                onPressed: () => excluirEvolucao(e['id']),
                              ),
                            ),
                          );
                        },
                      ),
              ),
            ],
          );
  }
}
