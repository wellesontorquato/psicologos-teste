import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:psigestor_app/models/paciente.dart';

class PacienteService {
  final String baseUrl = 'http://localhost:8000/api';

  Future<List<Paciente>> listar(String? token) async {
    if (token == null || token.isEmpty) {
      throw Exception('Token ausente ou inválido');
    }

    final response = await http.get(
      Uri.parse('$baseUrl/pacientes-json'),
      headers: {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
      },
    );
    
    if (response.statusCode == 200) {
      final List<dynamic> dados = json.decode(response.body);
      return dados.map((json) => Paciente.fromJson(json)).toList();
    } else {
      throw Exception('Erro ao buscar pacientes');
    }
  }

  Future<bool> criar(Paciente paciente, String token) async {
    final response = await http.post(
      Uri.parse('$baseUrl/pacientes'),
      headers: {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
      },
      body: jsonEncode(paciente.toJson()),
    );

    return response.statusCode == 201;
  }

  Future<bool> atualizar(Paciente paciente, String token) async {
    if (paciente.id == null) throw Exception('Paciente sem ID');

    final response = await http.put(
      Uri.parse('$baseUrl/pacientes/${paciente.id}'),
      headers: {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
      },
      body: jsonEncode(paciente.toJson()),
    );

    return response.statusCode == 200;
  }

  Future<bool> excluir(int id, String token) async {
    final response = await http.delete(
      Uri.parse('$baseUrl/pacientes/$id'),
      headers: {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
      },
    );
    return response.statusCode == 204;
  }
}
