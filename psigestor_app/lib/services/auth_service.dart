import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:http/http.dart' as http;

class AuthService extends ChangeNotifier {
  String? _token;
  Map<String, dynamic>? _user;
  bool _initialized = false;

  String? get token => _token;
  Map<String, dynamic>? get user => _user;
  bool get isAuthenticated => _token != null && _token!.isNotEmpty;

  /// Inicializa o AuthService ao iniciar o app
  Future<void> init() async {
    if (_initialized) return;
    final prefs = await SharedPreferences.getInstance();
    _token = prefs.getString('auth_token');
    if (_token != null && _token!.isNotEmpty) {
      await fetchUser();
    }
    _initialized = true;
    notifyListeners();
  }

  /// Faz login e salva token + dados do usuário
  Future<bool> login(String email, String password) async {
    final response = await http.post(
      Uri.parse('http://localhost:8000/api/login'),
      headers: {'Content-Type': 'application/json'},
      body: jsonEncode({'email': email, 'password': password}),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      _token = data['token'];
      _user = data['user'];
      final prefs = await SharedPreferences.getInstance();
      await prefs.setString('auth_token', _token!);
      notifyListeners();
      return true;
    } else {
      return false;
    }
  }

  /// Busca os dados do usuário logado
  Future<void> fetchUser() async {
    if (_token == null) return;
    final response = await http.get(
      Uri.parse('http://localhost:8000/api/user'),
      headers: {'Authorization': 'Bearer $_token'},
    );
    if (response.statusCode == 200) {
      _user = jsonDecode(response.body);
    } else {
      _user = null;
      _token = null;
      final prefs = await SharedPreferences.getInstance();
      await prefs.remove('auth_token');
    }
  }

  /// Faz logout limpando tudo
  Future<void> logout() async {
    _token = null;
    _user = null;
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('auth_token');
    notifyListeners();
  }
}
