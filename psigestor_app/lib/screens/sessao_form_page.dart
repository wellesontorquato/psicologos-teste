import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:psigestor_app/models/paciente.dart';
import 'package:psigestor_app/models/sessao.dart';
import 'package:psigestor_app/services/sessao_service.dart';

class SessaoFormPage extends StatefulWidget {
  final Sessao? sessao;

  const SessaoFormPage({super.key, this.sessao});

  @override
  State<SessaoFormPage> createState() => _SessaoFormPageState();
}

class _SessaoFormPageState extends State<SessaoFormPage> {
  final _formKey = GlobalKey<FormState>();
  final _dataHoraController = TextEditingController();

  DateTime? dataHora;
  int duracao = 50;
  double? valor;
  bool foiPago = false;
  Paciente? pacienteSelecionado;

  List<Paciente> pacientes = [];

  @override
  void initState() {
    super.initState();
    carregarPacientes();
    if (widget.sessao != null) {
      final s = widget.sessao!;
      dataHora = s.dataHora;
      duracao = s.duracao;
      valor = s.valor;
      foiPago = s.foiPago;
      _dataHoraController.text = DateFormat('dd/MM/yyyy HH:mm').format(s.dataHora);
    }
  }

  Future<void> carregarPacientes() async {
    final lista = await SessaoService(context).listarPacientes();
    setState(() {
      pacientes = lista;
      if (widget.sessao != null) {
        pacienteSelecionado = lista.firstWhere(
          (p) => p.id == widget.sessao!.pacienteId,
          orElse: () => lista.first,
        );
      } else {
        pacienteSelecionado = lista.isNotEmpty ? lista.first : null;
      }
    });
  }

  Future<void> salvar() async {
    if (!_formKey.currentState!.validate() || pacienteSelecionado == null || dataHora == null) return;

    final novaSessao = Sessao(
      id: widget.sessao?.id ?? 0,
      pacienteId: pacienteSelecionado!.id,
      pacienteNome: pacienteSelecionado!.nome,
      dataHora: dataHora!,
      duracao: duracao,
      valor: valor ?? 0,
      foiPago: foiPago,
      status: widget.sessao?.status ?? 'PENDENTE',
    );

    try {
      final service = SessaoService(context);
      final sucesso = widget.sessao == null
          ? await service.criar(novaSessao)
          : await service.atualizar(novaSessao);

      if (sucesso) {
        Navigator.pop(context, true);
      } else {
        mostrarErroDialog('Erro desconhecido ao salvar sessão.');
      }
        } catch (e) {
        String mensagem = 'Erro ao salvar sessão.';
        try {
          // Verifica se a mensagem de erro tem um JSON válido com "message"
          final response = e.toString();
          final start = response.indexOf('{');
          final end = response.lastIndexOf('}');
          if (start != -1 && end != -1 && end > start) {
            final jsonStr = response.substring(start, end + 1);
            final jsonMap = json.decode(jsonStr);
            if (jsonMap is Map && jsonMap.containsKey('message')) {
              mensagem = jsonMap['message'];
            }
          }
        } catch (_) {
          // Não altera a mensagem padrão
        }

        // Se for erro 409 (conflito de horário), captura também
        if (e.toString().contains('409')) {
          mensagem = 'Já existe uma sessão nesse horário.';
        }

        mostrarErroDialog(mensagem);
      }
  }

  void mostrarErroDialog(String mensagem) {
    showDialog(
      context: context,
      builder: (_) => AlertDialog(
        title: const Row(
          children: [
            Icon(Icons.error_outline, color: Colors.red),
            SizedBox(width: 8),
            Text('Erro'),
          ],
        ),
        content: Text(mensagem),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context),
            child: const Text('OK'),
          ),
        ],
      ),
    );
  }

  @override
  void dispose() {
    _dataHoraController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text(widget.sessao == null ? 'Nova Sessão' : 'Editar Sessão')),
      body: pacientes.isEmpty
          ? const Center(child: CircularProgressIndicator())
          : Padding(
              padding: const EdgeInsets.all(16),
              child: Form(
                key: _formKey,
                child: ListView(
                  children: [
                    DropdownButtonFormField<Paciente>(
                      value: pacienteSelecionado,
                      decoration: const InputDecoration(labelText: 'Paciente'),
                      items: pacientes
                          .map((p) => DropdownMenuItem(value: p, child: Text(p.nome)))
                          .toList(),
                      onChanged: (p) => setState(() => pacienteSelecionado = p),
                      validator: (value) => value == null ? 'Selecione um paciente' : null,
                    ),
                    const SizedBox(height: 12),
                    TextFormField(
                      controller: _dataHoraController,
                      decoration: const InputDecoration(
                        labelText: 'Data e Hora',
                        suffixIcon: Icon(Icons.calendar_today),
                      ),
                      readOnly: true,
                      onTap: () async {
                        final date = await showDatePicker(
                          context: context,
                          initialDate: dataHora ?? DateTime.now(),
                          firstDate: DateTime.now().subtract(const Duration(days: 365)),
                          lastDate: DateTime.now().add(const Duration(days: 365)),
                        );
                        if (date != null) {
                          final time = await showTimePicker(
                            context: context,
                            initialTime: TimeOfDay.fromDateTime(dataHora ?? DateTime.now()),
                          );
                          if (time != null) {
                            final selecionada = DateTime(date.year, date.month, date.day, time.hour, time.minute);
                            setState(() {
                              dataHora = selecionada;
                              _dataHoraController.text = DateFormat('dd/MM/yyyy HH:mm').format(selecionada);
                            });
                          }
                        }
                      },
                      validator: (value) => dataHora == null ? 'Selecione data e hora' : null,
                    ),
                    const SizedBox(height: 12),
                    TextFormField(
                      initialValue: duracao.toString(),
                      decoration: const InputDecoration(labelText: 'Duração (minutos)'),
                      keyboardType: TextInputType.number,
                      onChanged: (v) => duracao = int.tryParse(v) ?? 50,
                      validator: (v) => (v == null || v.isEmpty) ? 'Informe a duração' : null,
                    ),
                    const SizedBox(height: 12),
                    TextFormField(
                      initialValue: (valor ?? widget.sessao?.valor)?.toStringAsFixed(2) ?? '',
                      decoration: const InputDecoration(labelText: 'Valor (R\$)'),
                      keyboardType: const TextInputType.numberWithOptions(decimal: true),
                      onChanged: (v) => valor = double.tryParse(v),
                    ),
                    const SizedBox(height: 12),
                    CheckboxListTile(
                      title: const Text('Foi Pago?'),
                      value: foiPago,
                      onChanged: (v) => setState(() => foiPago = v ?? false),
                    ),
                    const SizedBox(height: 16),
                    Row(
                      children: [
                        ElevatedButton(
                          onPressed: salvar,
                          style: ElevatedButton.styleFrom(backgroundColor: Colors.green),
                          child: const Text('Salvar'),
                        ),
                        const SizedBox(width: 8),
                        OutlinedButton(
                          onPressed: () => Navigator.pop(context),
                          child: const Text('Cancelar'),
                        ),
                      ],
                    )
                  ],
                ),
              ),
            ),
    );
  }
}
