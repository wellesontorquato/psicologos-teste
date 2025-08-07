import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'package:psigestor_app/models/sessao.dart';
import 'package:psigestor_app/models/paciente.dart';
import 'package:psigestor_app/services/auth_service.dart';

class SessaoService {
  final BuildContext context;

  SessaoService(this.context);

  Future<String?> get token async {
    final auth = context.read<AuthService>();
    return auth.token;
  }

  Future<List<Sessao>> listar() async {
    final t = await token;
    if (t == null) throw Exception('Token de autenticação não encontrado.');

    final response = await http.get(
      Uri.parse('http://localhost:8000/api/sessoes-json'),
      headers: {
        'Authorization': 'Bearer $t',
        'Accept': 'application/json',
      },
    );

    if (response.statusCode == 200) {
      final List jsonList = json.decode(response.body);
      return jsonList.map((j) => Sessao.fromJson(j)).toList();
    } else {
      throw Exception(response.body);
    }
  }

  Future<List<Paciente>> listarPacientes() async {
    final t = await token;
    if (t == null) throw Exception('Token de autenticação não encontrado.');

    final response = await http.get(
      Uri.parse('http://localhost:8000/api/pacientes-json'),
      headers: {
        'Authorization': 'Bearer $t',
        'Accept': 'application/json',
      },
    );

    if (response.statusCode == 200) {
      final List jsonList = json.decode(response.body);
      return jsonList.map((j) => Paciente.fromJson(j)).toList();
    } else {
      throw Exception(response.body);
    }
  }

  Future<bool> criar(Sessao sessao) async {
    final t = await token;
    if (t == null) throw Exception('Token de autenticação não encontrado.');

    final response = await http.post(
      Uri.parse('http://localhost:8000/api/sessoes-json'),
      headers: {
        'Authorization': 'Bearer $t',
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
      body: json.encode({
        'paciente_id': sessao.pacienteId,
        'data_hora': sessao.dataHora.toIso8601String(),
        'duracao': sessao.duracao,
        'valor': sessao.valor,
        'foi_pago': sessao.foiPago,
      }),
    );

    if (response.statusCode == 201 || response.statusCode == 200) {
      return true;
    }

    throw Exception(response.body);
  }

  Future<bool> atualizar(Sessao sessao) async {
    final t = await token;
    if (t == null) throw Exception('Token de autenticação não encontrado.');

    final response = await http.put(
      Uri.parse('http://localhost:8000/api/sessoes-json/${sessao.id}'),
      headers: {
        'Authorization': 'Bearer $t',
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
      body: json.encode({
        'paciente_id': sessao.pacienteId,
        'data_hora': sessao.dataHora.toIso8601String(),
        'duracao': sessao.duracao,
        'valor': sessao.valor,
        'foi_pago': sessao.foiPago,
      }),
    );

    if (response.statusCode == 200) {
      return true;
    }

    throw Exception(response.body);
  }

  Future<void> excluir(int id) async {
    final t = await token;
    if (t == null) throw Exception('Token de autenticação não encontrado.');

    final response = await http.delete(
      Uri.parse('http://localhost:8000/api/sessoes-json/$id'),
      headers: {
        'Authorization': 'Bearer $t',
        'Accept': 'application/json',
      },
    );

    if (response.statusCode != 200) {
      throw Exception(response.body);
    }
  }

  Future<void> gerarRecorrencias(int sessaoId, int semanas, {bool foiPago = false}) async {
    final t = await token;
    if (t == null) throw Exception('Token de autenticação não encontrado.');

    final response = await http.post(
      Uri.parse('http://localhost:8000/api/sessoes-json/recorrencias'),
      headers: {
        'Authorization': 'Bearer $t',
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
      body: json.encode({
        'sessao_id': sessaoId,
        'semanas': semanas,
        'foi_pago': foiPago,
      }),
    );

    if (response.statusCode != 200) {
      throw Exception(response.body);
    }
  }
}
