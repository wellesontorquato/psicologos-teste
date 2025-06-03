import 'dart:async';
import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import '../models/news.dart';
import 'news_detail_page.dart';

class HomePage extends StatefulWidget {
  const HomePage({super.key});

  @override
  State<HomePage> createState() => _HomePageState();
}

class _HomePageState extends State<HomePage> {
  late Future<List<News>> noticias;
  final PageController _controller = PageController(viewportFraction: 0.9);
  int _currentPage = 0;
  Timer? _timer;

  final List<String> frasesMotivacionais = [
    'A saúde mental importa todos os dias.',
    'Cuidar de você é um ato de amor.',
    'Respire. Você está indo bem.',
    'Tudo bem desacelerar.',
    'Cada passo conta.',
    'Você merece cuidado.',
    'Falar é um ato de coragem.',
    'Pedir ajuda é força, não fraqueza.',
    'Você é suficiente.',
    'Se acolha com carinho.',
    'Não se compare: seu tempo é único.',
    'Cuide de si como cuida dos outros.',
    'A mente também precisa de descanso.',
    'Você é mais forte do que pensa.',
    'Uma pausa hoje evita um colapso amanhã.',
    'Tudo bem não estar bem.',
    'Ouça sua mente com empatia.',
    'Atenção plena: aqui e agora.',
    'Ame-se com gentileza.',
    'Saúde emocional é prioridade.',
    'Seja seu maior aliado.',
    'Pequenos cuidados, grandes mudanças.',
    'Cuide do corpo e da mente.',
    'Você está fazendo o seu melhor.',
    'Não carregue o mundo sozinho.',
    'Permita-se descansar.',
    'Valorize suas conquistas.',
    'O equilíbrio começa no interior.',
    'A empatia começa por você.',
    'Saúde mental é liberdade de ser.'
  ];

  String fraseAtual = '';

  @override
  void initState() {
    super.initState();
    noticias = buscarNoticias();
    fraseAtual = _sortearFrase();
    _timer = Timer.periodic(const Duration(seconds: 5), (Timer timer) {
      if (_controller.hasClients) {
        if (_currentPage < 4) {
          _currentPage++;
        } else {
          _currentPage = 0;
        }
        _controller.animateToPage(
          _currentPage,
          duration: const Duration(milliseconds: 800),
          curve: Curves.easeInOut,
        );
      }
    });
  }

  String _sortearFrase() {
    frasesMotivacionais.shuffle();
    return frasesMotivacionais.first;
  }

  @override
  void dispose() {
    _controller.dispose();
    _timer?.cancel();
    super.dispose();
  }

  Future<List<News>> buscarNoticias() async {
    final response = await http.get(Uri.parse('http://localhost:8000/api/blog-json'));
    if (response.statusCode == 200) {
      final List<dynamic> data = json.decode(response.body);
      return data.map((n) => News.fromJson(n)).toList();
    } else {
      throw Exception('Erro ao carregar notícias');
    }
  }

  Widget _buildCarrossel(List<News> noticias) {
    return SizedBox(
      height: 250,
      child: PageView.builder(
        itemCount: noticias.length,
        controller: _controller,
        onPageChanged: (index) => _currentPage = index,
        itemBuilder: (_, index) {
          final noticia = noticias[index];
          return GestureDetector(
            onTap: () {
              Navigator.push(
                context,
                MaterialPageRoute(builder: (_) => NewsDetailPage(noticia: noticia)),
              );
            },
            child: Card(
              margin: const EdgeInsets.symmetric(horizontal: 10, vertical: 8),
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
              clipBehavior: Clip.antiAlias,
              child: Stack(
                fit: StackFit.expand,
                children: [
                  Image.network(
                    noticia.imageUrl,
                    fit: BoxFit.cover,
                    errorBuilder: (_, __, ___) => Container(color: Colors.grey[300]),
                  ),
                  Container(
                    decoration: BoxDecoration(
                      gradient: LinearGradient(
                        colors: [Colors.transparent, Colors.black.withOpacity(0.8)],
                        begin: Alignment.topCenter,
                        end: Alignment.bottomCenter,
                      ),
                    ),
                  ),
                  Positioned(
                    bottom: 16,
                    left: 16,
                    right: 16,
                    child: Text(
                      noticia.title,
                      style: const TextStyle(
                        color: Colors.white,
                        fontSize: 18,
                        fontWeight: FontWeight.bold,
                        shadows: [Shadow(color: Colors.black54, blurRadius: 4)],
                      ),
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                    ),
                  ),
                ],
              ),
            ),
          );
        },
      ),
    );
  }

  Widget _buildFraseMotivacional() {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 20),
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: Colors.blue[50],
          borderRadius: BorderRadius.circular(12),
        ),
        child: Row(
          children: [
            const Icon(Icons.format_quote, color: Colors.blueAccent),
            const SizedBox(width: 12),
            Expanded(
              child: Text(
                '"$fraseAtual"',
                style: const TextStyle(fontStyle: FontStyle.italic, fontSize: 16),
              ),
            ),
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.grey[100],
      body: FutureBuilder<List<News>>(
        future: noticias,
        builder: (context, snapshot) {
          if (snapshot.connectionState == ConnectionState.waiting) {
            return const Center(child: CircularProgressIndicator());
          } else if (snapshot.hasError) {
            return const Center(child: Text('Erro ao carregar notícias'));
          } else if (snapshot.hasData && snapshot.data!.isNotEmpty) {
            return ListView(
              children: [
                const Padding(
                  padding: EdgeInsets.only(top: 32, left: 16, bottom: 8),
                  child: Text(
                    'Últimas Notícias',
                    style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
                  ),
                ),
                _buildCarrossel(snapshot.data!),
                _buildFraseMotivacional(),
              ],
            );
          } else {
            return const Center(child: Text('Nenhuma notícia encontrada'));
          }
        },
      ),
    );
  }
}
