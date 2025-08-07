import 'package:flutter/material.dart';
import 'package:psigestor_app/models/sessao.dart';
import 'package:psigestor_app/services/sessao_service.dart';
import 'package:psigestor_app/screens/sessao_detalhes_page.dart';
import 'package:psigestor_app/screens/sessao_form_page.dart';

class SessoesPage extends StatefulWidget {
  const SessoesPage({super.key});

  @override
  State<SessoesPage> createState() => _SessoesPageState();
}

class _SessoesPageState extends State<SessoesPage> {
  List<Sessao> sessoes = [];
  bool carregando = true;

  String filtroPago = 'Todos';
  String filtroStatus = 'Todos';
  String filtroPeriodo = 'Todos';
  String filtroOrdenar = 'Mais recente';
  String busca = '';

  @override
  void initState() {
    super.initState();
    carregarSessoes();
  }

  Future<void> carregarSessoes() async {
    setState(() => carregando = true);
    try {
      final dados = await SessaoService(context).listar();
      if (!mounted) return;
      setState(() {
        sessoes = dados;
        carregando = false;
      });
    } catch (e) {
      if (!mounted) return;
      setState(() => carregando = false);
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Erro ao carregar sessões: $e')),
      );
    }
  }

  Future<void> confirmarExclusao(Sessao sessao) async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (_) => AlertDialog(
        title: const Text('Confirmar exclusão'),
        content: Text(
            'Deseja realmente excluir a sessão de ${sessao.pacienteNome}?'),
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
      try {
        await SessaoService(context).excluir(sessao.id);
        if (!mounted) return;
        carregarSessoes();
      } catch (e) {
        if (!mounted) return;
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Erro ao excluir: $e')),
        );
      }
    }
  }

  Future<void> mostrarRecorrenciaDialog(Sessao sessao) async {
    final semanasController = TextEditingController(text: '4');
    bool foiPago = false;

    final confirm = await showDialog<bool>(
      context: context,
      builder: (_) => StatefulBuilder(
        builder: (context, setModalState) => AlertDialog(
          title: const Text('Gerar Recorrências'),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              const Text('Quantas semanas deseja repetir esta sessão?'),
              const SizedBox(height: 8),
              TextField(
                controller: semanasController,
                keyboardType: TextInputType.number,
                decoration: const InputDecoration(labelText: 'Semanas'),
              ),
              const SizedBox(height: 12),
              CheckboxListTile(
                value: foiPago,
                onChanged: (v) => setModalState(() => foiPago = v ?? false),
                title: const Text('Foi Pago?'),
                controlAffinity: ListTileControlAffinity.leading,
                contentPadding: EdgeInsets.zero,
              ),
            ],
          ),
          actions: [
            TextButton(
                onPressed: () => Navigator.pop(context, false),
                child: const Text('Cancelar')),
            TextButton(
                onPressed: () => Navigator.pop(context, true),
                child: const Text('Confirmar')),
          ],
        ),
      ),
    );

    if (confirm == true) {
      final semanas = int.tryParse(semanasController.text) ?? 0;
      if (semanas > 0) {
        try {
          await SessaoService(context)
              .gerarRecorrencias(sessao.id, semanas, foiPago: foiPago);
          if (!mounted) return;
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(content: Text('Recorrências geradas com sucesso!')),
          );
          carregarSessoes();
        } catch (e) {
          if (!mounted) return;
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(content: Text('Erro ao gerar recorrências: $e')),
          );
        }
      }
    }
  }

  List<Sessao> getSessoesFiltradas() {
    return sessoes.where((s) {
      if (busca.isNotEmpty &&
          !(s.pacienteNome ?? '').toLowerCase().contains(busca.toLowerCase()))
        return false;
      if (filtroPago == 'Sim' && !s.foiPago) return false;
      if (filtroPago == 'Não' && s.foiPago) return false;
      if (filtroStatus != 'Todos' && (s.status ?? 'PENDENTE') != filtroStatus)
        return false;

      final agora = DateTime.now();
      final data = s.dataHora;
      if (filtroPeriodo == 'Hoje' &&
          !(data.year == agora.year &&
              data.month == agora.month &&
              data.day == agora.day)) return false;
      if (filtroPeriodo == 'Semana') {
        final inicio = agora.subtract(Duration(days: agora.weekday - 1));
        final fim = inicio.add(const Duration(days: 6));
        if (data.isBefore(inicio) || data.isAfter(fim)) return false;
      }
      if (filtroPeriodo == 'Próxima') {
        final fimSemana = agora.add(Duration(days: 7 - agora.weekday));
        final inicio = fimSemana.add(const Duration(days: 1));
        final fim = inicio.add(const Duration(days: 6));
        if (data.isBefore(inicio) || data.isAfter(fim)) return false;
      }
      return true;
    }).toList()
      ..sort((a, b) => filtroOrdenar == 'Mais recente'
          ? b.dataHora.compareTo(a.dataHora)
          : a.dataHora.compareTo(b.dataHora));
  }

  @override
  Widget build(BuildContext context) {
    final sessoesFiltradas = getSessoesFiltradas();

    return Scaffold(
      body: carregando
          ? const Center(child: CircularProgressIndicator())
          : SingleChildScrollView(
              child: Column(
                children: [
                  buildCabecalhoFiltros(sessoesFiltradas),
                  const Divider(),
                  LayoutBuilder(
                    builder: (context, constraints) {
                      return constraints.maxWidth < 600
                          ? buildListaSessoes(sessoesFiltradas)
                          : buildTabelaSessoes(sessoesFiltradas);
                    },
                  ),
                ],
              ),
            ),
      floatingActionButton: FloatingActionButton(
        onPressed: () async {
          final resultado = await Navigator.push(
            context,
            MaterialPageRoute(builder: (_) => const SessaoFormPage()),
          );
          if (!mounted) return;
          if (resultado == true) carregarSessoes();
        },
        child: const Icon(Icons.add),
      ),
    );
  }

  Widget buildCabecalhoFiltros(List<Sessao> sessoesFiltradas) => Padding(
        padding: const EdgeInsets.all(12),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Wrap(
              spacing: 12,
              runSpacing: 12,
              children: [
                buildDropdown('Pago?', filtroPago, ['Todos', 'Sim', 'Não'],
                    (v) => setState(() => filtroPago = v!)),
                buildDropdown(
                    'Status',
                    filtroStatus,
                    [
                      'Todos',
                      'PENDENTE',
                      'CONFIRMADA',
                      'CANCELADA',
                      'REMARCAR',
                      'REMARCADO'
                    ],
                    (v) => setState(() => filtroStatus = v!)),
                buildDropdown(
                    'Período',
                    filtroPeriodo,
                    ['Todos', 'Hoje', 'Semana', 'Próxima'],
                    (v) => setState(() => filtroPeriodo = v!)),
                buildDropdown(
                    'Ordenar por',
                    filtroOrdenar,
                    ['Mais recente', 'Mais antigo'],
                    (v) => setState(() => filtroOrdenar = v!)),
              ],
            ),
            const SizedBox(height: 12),
            Row(
              children: [
                Expanded(
                  child: TextField(
                    decoration: const InputDecoration(labelText: 'Buscar'),
                    onChanged: (v) => setState(() => busca = v),
                  ),
                ),
                const SizedBox(width: 8),
                OutlinedButton.icon(
                  onPressed: () => setState(() {
                    filtroPago = 'Todos';
                    filtroStatus = 'Todos';
                    filtroPeriodo = 'Todos';
                    filtroOrdenar = 'Mais recente';
                    busca = '';
                  }),
                  icon: const Icon(Icons.clear),
                  label: const Text('Limpar'),
                ),
              ],
            ),
            const SizedBox(height: 8),
            Text(
              'Exibindo ${sessoesFiltradas.length} de ${sessoes.length} sessões',
              style: TextStyle(
                  color: Colors.grey[700], fontStyle: FontStyle.italic),
            ),
          ],
        ),
      );

  Widget buildDropdown(String label, String valor, List<String> opcoes,
          void Function(String?) onChanged) =>
      SizedBox(
        width: 180,
        child: DropdownButtonFormField<String>(
          value: valor,
          decoration: InputDecoration(labelText: label, isDense: true),
          items: opcoes
              .map((v) => DropdownMenuItem(value: v, child: Text(v)))
              .toList(),
          onChanged: onChanged,
        ),
      );

  Widget buildTabelaSessoes(List<Sessao> sessoesFiltradas) =>
      SingleChildScrollView(
        scrollDirection: Axis.horizontal,
        child: DataTable(
          columnSpacing: 16,
          columns: const [
            DataColumn(label: Text('Paciente')),
            DataColumn(label: Text('Data')),
            DataColumn(label: Text('Duração')),
            DataColumn(label: Text('Valor')),
            DataColumn(label: Text('Pago?')),
            DataColumn(label: Text('Status')),
            DataColumn(label: Text('Ações')),
          ],
          rows: sessoesFiltradas.map((s) {
            return DataRow(cells: [
              DataCell(Text(s.pacienteNome ?? '')),
              DataCell(Text(s.dataHoraFormatada)),
              DataCell(Text('${s.duracao} min')),
              DataCell(Text(
                  'R\$ ${s.valor.toStringAsFixed(2).replaceAll('.', ',')}')),
              DataCell(Container(
                padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                decoration: BoxDecoration(
                  color: s.foiPago ? Colors.green : Colors.grey,
                  borderRadius: BorderRadius.circular(6),
                ),
                child: Text(s.foiPago ? 'Sim' : 'Não',
                    style: const TextStyle(color: Colors.white)),
              )),
              DataCell(Text(s.status ?? 'PENDENTE')),
              DataCell(Row(
                children: [
                  IconButton(
                    icon: const Icon(Icons.edit, color: Colors.orange),
                    onPressed: () async {
                      final result = await Navigator.push(
                        context,
                        MaterialPageRoute(
                            builder: (_) => SessaoFormPage(sessao: s)),
                      );
                      if (!mounted) return;
                      if (result == true) carregarSessoes();
                    },
                  ),
                  IconButton(
                    icon: const Icon(Icons.delete, color: Colors.red),
                    onPressed: () => confirmarExclusao(s),
                  ),
                  TextButton(
                    onPressed: () => mostrarRecorrenciaDialog(s),
                    child: const Text('Recorrências'),
                  ),
                ],
              )),
            ]);
          }).toList(),
        ),
      );

  Widget buildListaSessoes(List<Sessao> sessoesFiltradas) => Padding(
        padding: const EdgeInsets.only(
            bottom: 90), // 👈 espaço para o botão flutuante
        child: ListView.builder(
          shrinkWrap: true,
          physics: const NeverScrollableScrollPhysics(),
          itemCount: sessoesFiltradas.length,
          itemBuilder: (context, index) {
            final s = sessoesFiltradas[index];
            return Card(
              margin: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
              child: ListTile(
                contentPadding:
                    const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                title: Text(s.pacienteNome ?? '',
                    style: const TextStyle(fontWeight: FontWeight.bold)),
                subtitle: Text(s.dataHoraFormatada,
                    style: const TextStyle(fontSize: 14)),
                trailing: Builder(
                  builder: (ctx) => PopupMenuButton<String>(
                    icon: const Icon(Icons.more_vert),
                    onSelected: (value) async {
                      if (value == 'editar') {
                        final result = await Navigator.push(
                          ctx,
                          MaterialPageRoute(
                              builder: (_) => SessaoFormPage(sessao: s)),
                        );
                        if (!mounted) return;
                        if (result == true) carregarSessoes();
                      } else if (value == 'excluir') {
                        confirmarExclusao(s);
                      } else if (value == 'recorrencia') {
                        mostrarRecorrenciaDialog(s);
                      }
                    },
                    itemBuilder: (_) => const [
                      PopupMenuItem(value: 'editar', child: Text('Editar')),
                      PopupMenuItem(value: 'excluir', child: Text('Excluir')),
                      PopupMenuItem(
                          value: 'recorrencia', child: Text('Recorrências')),
                    ],
                  ),
                ),
              ),
            );
          },
        ),
      );
}
