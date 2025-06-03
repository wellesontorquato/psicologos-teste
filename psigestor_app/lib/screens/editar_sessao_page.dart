import 'package:flutter/material.dart';

class EditarSessaoPage extends StatefulWidget {
  final Map<String, dynamic> sessao;

  const EditarSessaoPage({super.key, required this.sessao});

  @override
  State<EditarSessaoPage> createState() => _EditarSessaoPageState();
}

class _EditarSessaoPageState extends State<EditarSessaoPage> {
  final _formKey = GlobalKey<FormState>();
  late TextEditingController _horaController;
  late bool _confirmada;

  @override
  void initState() {
    super.initState();
    _horaController = TextEditingController(text: widget.sessao['hora']);
    _confirmada = widget.sessao['confirmada'] == 1;
  }

  @override
  void dispose() {
    _horaController.dispose();
    super.dispose();
  }

  void _salvarEdicao() {
    if (_formKey.currentState!.validate()) {
      final sessaoAtualizada = {
        ...widget.sessao,
        'hora': _horaController.text,
        'confirmada': _confirmada ? 1 : 0,
      };

      // TODO: Enviar PUT para a API com dados atualizados
      print('Salvar: $sessaoAtualizada');

      Navigator.pop(context, sessaoAtualizada);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Editar Sessão')),
      body: Padding(
        padding: const EdgeInsets.all(16),
        child: Form(
          key: _formKey,
          child: Column(
            children: [
              Text('Paciente: ${widget.sessao['paciente']['nome']}'),
              const SizedBox(height: 12),
              TextFormField(
                controller: _horaController,
                decoration: const InputDecoration(
                  labelText: 'Hora (HH:mm)',
                  border: OutlineInputBorder(),
                ),
                validator: (value) =>
                    value == null || value.isEmpty ? 'Informe a hora' : null,
              ),
              const SizedBox(height: 12),
              SwitchListTile(
                value: _confirmada,
                title: const Text('Confirmada'),
                onChanged: (val) => setState(() => _confirmada = val),
              ),
              const SizedBox(height: 20),
              ElevatedButton.icon(
                icon: const Icon(Icons.save),
                label: const Text('Salvar alterações'),
                onPressed: _salvarEdicao,
              ),
            ],
          ),
        ),
      ),
    );
  }
}
