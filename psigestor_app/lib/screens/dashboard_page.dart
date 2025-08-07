import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'package:intl/intl.dart';
import 'package:provider/provider.dart';
import 'package:url_launcher/url_launcher.dart';
import '../services/auth_service.dart';

class DashboardPage extends StatefulWidget {
  const DashboardPage({super.key});

  @override
  State<DashboardPage> createState() => _DashboardPageState();
}

class _DashboardPageState extends State<DashboardPage> {
  String _filtro = '7';
  Map<String, dynamic> dados = {};
  bool carregando = true;

  final filtros = {
    '7': 'Últimos 7 dias',
    '15': 'Últimos 15 dias',
    '30': 'Últimos 30 dias',
  };

  @override
  void initState() {
    super.initState();
    carregarDashboard();
  }

  Future<void> carregarDashboard() async {
    setState(() => carregando = true);
    final auth = Provider.of<AuthService>(context, listen: false);

    final response = await http.get(
      Uri.parse('http://localhost:8000/api/dashboard?periodo=$_filtro'),
      headers: {
        'Authorization': 'Bearer ${auth.token}',
        'Accept': 'application/json',
      },
    );

    if (response.statusCode == 200) {
      setState(() {
        dados = jsonDecode(response.body);
        carregando = false;
      });
    } else {
      setState(() => carregando = false);
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Erro ao carregar dashboard')),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return carregando
        ? const Center(child: CircularProgressIndicator())
        : RefreshIndicator(
            onRefresh: carregarDashboard,
            child: ListView(
              padding: const EdgeInsets.all(16),
              children: [
                Row(
                  children: [
                    DropdownButton<String>(
                      value: _filtro,
                      items: filtros.entries
                          .map((f) => DropdownMenuItem(
                                value: f.key,
                                child: Text(f.value),
                              ))
                          .toList(),
                      onChanged: (v) {
                        setState(() => _filtro = v!);
                        carregarDashboard();
                      },
                    ),
                    const Spacer(),
                    IconButton(
                      icon: const Icon(Icons.picture_as_pdf),
                      onPressed: () => abrirExport('pdf'),
                    ),
                    IconButton(
                      icon: const Icon(Icons.table_chart),
                      onPressed: () => abrirExport('excel'),
                    ),
                  ],
                ),
                const SizedBox(height: 12),
                Card(
                  child: ListTile(
                    leading: const Icon(Icons.schedule),
                    title: const Text('Sessões no período'),
                    trailing: Text('${dados['totais']['sessoes']}'),
                  ),
                ),
                Card(
                  child: ListTile(
                    leading: const Icon(Icons.attach_money),
                    title: const Text('Total recebido'),
                    trailing: Text(
                        'R\$ ${double.tryParse(dados['valores']['total'].toString())?.toStringAsFixed(2) ?? '0.00'}'),
                  ),
                ),
                Card(
                  child: ListTile(
                    leading: const Icon(Icons.warning),
                    title: const Text('Pendências de pagamento'),
                    trailing: Text('${dados['pendencias']}'),
                  ),
                ),
                Card(
                  child: ListTile(
                    leading: const Icon(Icons.today),
                    title: const Text('Sessões de hoje'),
                    trailing: Text('${dados['sessoesHoje']}'),
                  ),
                ),
                const Divider(height: 32),
                const Text(
                  'Próximas Sessões',
                  style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
                ),
                const SizedBox(height: 10),
                ...List.generate(
                  (dados['proximasSessoes'] as List).length,
                  (index) {
                    final sessao = dados['proximasSessoes'][index];
                    final dataHora = DateTime.parse(sessao['data_hora']);
                    return ListTile(
                      leading: const Icon(Icons.event),
                      title: Text(sessao['paciente']['nome']),
                      subtitle: Text(
                          DateFormat('dd/MM/yyyy • HH:mm').format(dataHora)),
                    );
                  },
                )
              ],
            ),
          );
  }

  void abrirExport(String tipo) async {
    final auth = Provider.of<AuthService>(context, listen: false);
    final baseUrl = 'http://localhost:8000/api/dashboard/exportar';
    final url = tipo == 'pdf'
        ? '$baseUrl/pdf?periodo=$_filtro'
        : '$baseUrl/excel?periodo=$_filtro';

    final uri = Uri.parse(url);

    if (await canLaunchUrl(uri)) {
      await launchUrl(
        uri,
        mode: LaunchMode.externalApplication,
        webViewConfiguration:
            const WebViewConfiguration(enableJavaScript: true),
      );
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Não foi possível abrir o link')),
      );
    }
  }
}
