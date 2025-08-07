class News {
  final int id;
  final String title;
  final String? subtitle;
  final String? content;
  final String imageUrl;

  News({
    required this.id,
    required this.title,
    this.subtitle,
    this.content,
    required this.imageUrl,
  });

  factory News.fromJson(Map<String, dynamic> json) {
    return News(
      id: json['id'] is int ? json['id'] : int.tryParse(json['id'].toString()) ?? 0,
      title: json['title'] ?? 'Sem título',
      subtitle: json['subtitle'],
      content: json['content'],
      imageUrl: json['image_url'] ?? '',
    );
  }
}
