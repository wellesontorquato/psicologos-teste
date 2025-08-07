import 'package:flutter/material.dart';
import 'package:psigestor_app/models/sessao.dart';

class SessaoDetalhesPage extends StatelessWidget {
  final Sessao sessao;

  const SessaoDetalhesPage({super.key, required this.sessao});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('Sessão de ${sessao.pacienteNome}')),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: ListView(
          children: [
            ListTile(
              title: const Text('Paciente'),
              subtitle: Text(sessao.pacienteNome ?? ''),
            ),
            ListTile(
              title: const Text('Data e hora'),
              subtitle: Text(sessao.dataHoraFormatada),
            ),
            ListTile(
              title: const Text('Duração'),
              subtitle: Text('${sessao.duracao} minutos'),
            ),
            ListTile(
              title: const Text('Valor'),
              subtitle: Text('R\$ ${sessao.valor.toStringAsFixed(2).replaceAll('.', ',')}'),
            ),
            ListTile(
              title: const Text('Pago'),
              subtitle: Text(sessao.foiPago ? 'Sim' : 'Não'),
            ),
            ListTile(
              title: const Text('Status'),
              subtitle: Text(sessao.statusDisplay),
            ),
            const SizedBox(height: 20),
            ElevatedButton.icon(
              onPressed: () => Navigator.pop(context),
              icon: const Icon(Icons.arrow_back),
              label: const Text('Voltar'),
            )
          ],
        ),
      ),
    );
  }
}
