class UserModel {
  final int id;
  final String name;
  final String email;
  final String? phone;
  final String role;
  final String? avatar;
  final String? bio;
  final LandlordProfileModel? landlordProfile;

  UserModel({
    required this.id,
    required this.name,
    required this.email,
    this.phone,
    required this.role,
    this.avatar,
    this.bio,
    this.landlordProfile,
  });

  factory UserModel.fromJson(Map<String, dynamic> json) {
    return UserModel(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      email: json['email'] ?? '',
      phone: json['phone'],
      role: json['role'] ?? 'renter',
      avatar: json['avatar'],
      bio: json['bio'],
      landlordProfile: json['landlord_profile'] != null
          ? LandlordProfileModel.fromJson(json['landlord_profile'])
          : null,
    );
  }

  bool get isLandlord => role == 'landlord';
  bool get isAdmin => role == 'admin';
}

class LandlordProfileModel {
  final int id;
  final String? businessName;
  final String? telegramUsername;
  final String verificationStatus;
  final double rating;
  final int totalReviews;

  LandlordProfileModel({
    required this.id,
    this.businessName,
    this.telegramUsername,
    required this.verificationStatus,
    required this.rating,
    required this.totalReviews,
  });

  factory LandlordProfileModel.fromJson(Map<String, dynamic> json) {
    return LandlordProfileModel(
      id: json['id'] ?? 0,
      businessName: json['business_name'],
      telegramUsername: json['telegram_username'],
      verificationStatus: json['verification_status'] ?? 'pending',
      rating: (json['rating'] is num) ? (json['rating'] as num).toDouble() : 5.0,
      totalReviews: json['total_reviews'] ?? 0,
    );
  }
}
