import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import 'screens/home_page.dart';
import 'screens/dashboard_page.dart';
import 'screens/paciente_list_page.dart';
import 'screens/sessoes_page.dart';
import 'screens/evolucoes_page.dart';
import 'screens/agenda_page.dart';
import 'services/auth_service.dart';

class HomeWrapper extends StatefulWidget {
  const HomeWrapper({super.key});

  @override
  State<HomeWrapper> createState() => _HomeWrapperState();
}

class _HomeWrapperState extends State<HomeWrapper> {
  int _indiceAtual = 0;

  final List<String> _titulos = [
    'Início',
    'Dashboard',
    'Pacientes',
    'Sessões',
    'Evoluções',
    'Agenda',
  ];

  final List<Widget> _telas = const [
    HomePage(),
    DashboardPage(),
    PacienteListPage(),
    SessoesPage(),
    EvolucoesPage(),
    AgendaPage(),
  ];

  void _navegarPara(int index) {
    setState(() => _indiceAtual = index);
    Navigator.pop(context); // fecha o Drawer
  }

  @override
  Widget build(BuildContext context) {
    final auth = Provider.of<AuthService>(context);
    final user = auth.user;

    return Scaffold(
      appBar: AppBar(
        title: Text(_titulos[_indiceAtual]),
        elevation: 1,
      ),
      drawer: Drawer(
        child: Column(
          children: [
            UserAccountsDrawerHeader(
              currentAccountPicture: const CircleAvatar(
                backgroundColor: Colors.white,
                child: Icon(Icons.person, color: Colors.blue, size: 36),
              ),
              accountName: Text(user?['name'] ?? 'Usuário'),
              accountEmail: Text(user?['email'] ?? ''),
              decoration: const BoxDecoration(
                color: Color(0xFF1976D2),
              ),
            ),
            ListTile(
              leading: const Icon(Icons.home),
              title: const Text('Início'),
              onTap: () => _navegarPara(0),
            ),
            ListTile(
              leading: const Icon(Icons.bar_chart),
              title: const Text('Dashboard'),
              onTap: () => _navegarPara(1),
            ),
            ListTile(
              leading: const Icon(Icons.people),
              title: const Text('Pacientes'),
              onTap: () => _navegarPara(2),
            ),
            ListTile(
              leading: const Icon(Icons.calendar_today),
              title: const Text('Sessões'),
              onTap: () => _navegarPara(3),
            ),
            ListTile(
              leading: const Icon(Icons.description),
              title: const Text('Evoluções'),
              onTap: () => _navegarPara(4),
            ),
            ListTile(
              leading: const Icon(Icons.schedule),
              title: const Text('Agenda'),
              onTap: () => _navegarPara(5),
            ),
            const Spacer(),
            const Divider(),
            ListTile(
              leading: const Icon(Icons.logout),
              title: const Text('Sair'),
              onTap: () async {
                final confirmar = await showDialog<bool>(
                  context: context,
                  builder: (ctx) => AlertDialog(
                    title: const Text('Sair'),
                    content: const Text('Deseja realmente sair?'),
                    actions: [
                      TextButton(onPressed: () => Navigator.pop(ctx, false), child: const Text('Cancelar')),
                      ElevatedButton(onPressed: () => Navigator.pop(ctx, true), child: const Text('Sair')),
                    ],
                  ),
                );

                if (confirmar == true) {
                  Provider.of<AuthService>(context, listen: false).logout();
                }
              },
            ),
          ],
        ),
      ),
      body: _telas[_indiceAtual],
    );
  }
}
