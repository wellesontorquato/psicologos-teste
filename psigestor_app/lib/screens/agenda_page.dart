import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:http/http.dart' as http;
import 'package:provider/provider.dart';
import 'package:table_calendar/table_calendar.dart';
import 'dart:convert';

import '../services/auth_service.dart';

class AgendaPage extends StatefulWidget {
  const AgendaPage({super.key});

  @override
  State<AgendaPage> createState() => _AgendaPageState();
}

class _AgendaPageState extends State<AgendaPage> {
  DateTime _diaSelecionado = DateTime.now();
  List<dynamic> sessoes = [];
  bool carregando = true;

  @override
  void initState() {
    super.initState();
    carregarSessoes();
  }

  Future<void> carregarSessoes() async {
    setState(() => carregando = true);
    final auth = Provider.of<AuthService>(context, listen: false);
    final response = await http.get(
      Uri.parse('http://localhost:8000/api/sessoes-json'),
      headers: {
        'Authorization': 'Bearer ${auth.token}',
        'Accept': 'application/json',
      },
    );

    if (response.statusCode == 200) {
      setState(() {
        sessoes = jsonDecode(response.body);
        carregando = false;
      });
    } else {
      setState(() => carregando = false);
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Erro ao carregar sessões')),
      );
    }
  }

  List<dynamic> get sessoesDoDia {
    return sessoes.where((s) {
      final dataStr = s['data_hora'];
      if (dataStr == null) return false;
      try {
        final dataSessao = DateTime.parse(dataStr);
        return DateFormat('yyyy-MM-dd').format(dataSessao) ==
            DateFormat('yyyy-MM-dd').format(_diaSelecionado);
      } catch (e) {
        return false;
      }
    }).toList();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: carregando
          ? const Center(child: CircularProgressIndicator())
          : Padding(
              padding: const EdgeInsets.symmetric(horizontal: 12.0),
              child: Column(
                children: [
                  const SizedBox(height: 24),
                  TableCalendar(
                    locale: 'pt_BR',
                    firstDay: DateTime.utc(2020, 1, 1),
                    lastDay: DateTime.utc(2030, 12, 31),
                    focusedDay: _diaSelecionado,
                    selectedDayPredicate: (day) =>
                        isSameDay(_diaSelecionado, day),
                    onDaySelected: (selectedDay, focusedDay) {
                      setState(() => _diaSelecionado = selectedDay);
                    },
                    calendarStyle: const CalendarStyle(
                      todayDecoration: BoxDecoration(
                          color: Colors.indigo, shape: BoxShape.circle),
                      selectedDecoration: BoxDecoration(
                          color: Colors.deepPurple, shape: BoxShape.circle),
                    ),
                    daysOfWeekStyle: const DaysOfWeekStyle(
                      weekdayStyle: TextStyle(
                        fontSize: 13,
                        fontWeight: FontWeight.w600,
                      ),
                      weekendStyle: TextStyle(
                        fontSize: 13,
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                    headerStyle: const HeaderStyle(
                      formatButtonVisible: false,
                      titleCentered: true,
                      titleTextStyle: TextStyle(
                        fontSize: 18,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    rowHeight: 48, // 🔹 Altura ajustada
                  ),
                  const SizedBox(height: 16),
                  Expanded(
                    child: sessoesDoDia.isEmpty
                        ? const Center(child: Text('Nenhuma sessão neste dia.'))
                        : ListView.builder(
                            itemCount: sessoesDoDia.length,
                            itemBuilder: (context, index) {
                              final s = sessoesDoDia[index];
                              final horario = DateFormat.Hm('pt_BR')
                                  .format(DateTime.parse(s['data_hora']));
                              return Card(
                                shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.circular(12)),
                                elevation: 4,
                                margin: const EdgeInsets.symmetric(
                                    horizontal: 8, vertical: 6),
                                child: ListTile(
                                  contentPadding: const EdgeInsets.symmetric(
                                      horizontal: 16, vertical: 12),
                                  leading: CircleAvatar(
                                    backgroundColor: Colors.indigo[100],
                                    child: const Icon(Icons.schedule,
                                        color: Colors.indigo),
                                  ),
                                  title: Text(
                                    '${s['paciente']['nome']} • $horario',
                                    style: const TextStyle(
                                        fontWeight: FontWeight.w600),
                                  ),
                                  subtitle: Text(
                                    s['status_confirmacao'] == 'CONFIRMADA'
                                        ? 'Confirmada'
                                        : s['status_confirmacao'] == 'CANCELADA'
                                            ? 'Cancelada'
                                            : s['status_confirmacao'] ==
                                                    'REMARCAR'
                                                ? 'Remarcar'
                                                : 'Pendente',
                                    style: TextStyle(
                                      color: s['status_confirmacao'] ==
                                              'CONFIRMADA'
                                          ? Colors.green
                                          : s['status_confirmacao'] ==
                                                  'CANCELADA'
                                              ? Colors.red
                                              : s['status_confirmacao'] ==
                                                      'REMARCAR'
                                                  ? Colors.blueGrey
                                                  : Colors.orange,
                                    ),
                                  ),
                                ),
                              );
                            },
                          ),
                  ),
                ],
              ),
            ),
    );
  }
}
