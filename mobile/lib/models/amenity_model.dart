class AmenityModel {
  final int id;
  final String name;
  final String slug;
  final String icon;
  final String category;

  AmenityModel({
    required this.id,
    required this.name,
    required this.slug,
    required this.icon,
    required this.category,
  });

  factory AmenityModel.fromJson(Map<String, dynamic> json) {
    return AmenityModel(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      slug: json['slug'] ?? '',
      icon: json['icon'] ?? 'check',
      category: json['category'] ?? 'facility',
    );
  }
}
