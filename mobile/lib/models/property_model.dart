import 'amenity_model.dart';
import 'user_model.dart';

class PropertyModel {
  final int id;
  final int landlordId;
  final String title;
  final String slug;
  final String description;
  final String propertyType;
  final double pricePerMonth;
  final double? depositAmount;
  final int bedrooms;
  final int bathrooms;
  final double? sizeSqm;
  final String city;
  final String khanDistrict;
  final String? sangkatCommune;
  final String streetAddress;
  final double? electricityCostPerKwh;
  final double? waterCostPerM3;
  final bool wifiIncluded;
  final bool parkingAvailable;
  final bool petFriendly;
  final String furnishedStatus;
  final String status;
  final bool isFeatured;
  final int viewsCount;
  final int aiCompletenessScore;
  final String? aiSummary;
  final String primaryImageUrl;
  final List<PropertyImageModel> images;
  final List<AmenityModel> amenities;
  final UserModel? landlord;
  bool isFavorited;

  PropertyModel({
    required this.id,
    required this.landlordId,
    required this.title,
    required this.slug,
    required this.description,
    required this.propertyType,
    required this.pricePerMonth,
    this.depositAmount,
    required this.bedrooms,
    required this.bathrooms,
    this.sizeSqm,
    required this.city,
    required this.khanDistrict,
    this.sangkatCommune,
    required this.streetAddress,
    this.electricityCostPerKwh,
    this.waterCostPerM3,
    required this.wifiIncluded,
    required this.parkingAvailable,
    required this.petFriendly,
    required this.furnishedStatus,
    required this.status,
    required this.isFeatured,
    required this.viewsCount,
    required this.aiCompletenessScore,
    this.aiSummary,
    required this.primaryImageUrl,
    required this.images,
    required this.amenities,
    this.landlord,
    this.isFavorited = false,
  });

  factory PropertyModel.fromJson(Map<String, dynamic> json) {
    var rawImages = json['images'] as List? ?? [];
    List<PropertyImageModel> parsedImages =
        rawImages.map((i) => PropertyImageModel.fromJson(i)).toList();

    var rawAmenities = json['amenities'] as List? ?? [];
    List<AmenityModel> parsedAmenities =
        rawAmenities.map((a) => AmenityModel.fromJson(a)).toList();

    String primaryUrl = json['primary_image_url'] ??
        (parsedImages.isNotEmpty ? parsedImages.first.imageUrl : '');
    if (primaryUrl.isEmpty) {
      primaryUrl =
          'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=800&q=80';
    }

    return PropertyModel(
      id: json['id'] ?? 0,
      landlordId: json['landlord_id'] ?? 0,
      title: json['title'] ?? '',
      slug: json['slug'] ?? '',
      description: json['description'] ?? '',
      propertyType: json['property_type'] ?? 'room',
      pricePerMonth: (json['price_per_month'] is num)
          ? (json['price_per_month'] as num).toDouble()
          : double.tryParse(json['price_per_month']?.toString() ?? '0') ?? 0.0,
      depositAmount: json['deposit_amount'] != null
          ? double.tryParse(json['deposit_amount'].toString())
          : null,
      bedrooms: json['bedrooms'] ?? 1,
      bathrooms: json['bathrooms'] ?? 1,
      sizeSqm: json['size_sqm'] != null
          ? double.tryParse(json['size_sqm'].toString())
          : null,
      city: json['city'] ?? 'Phnom Penh',
      khanDistrict: json['khan_district'] ?? 'Phnom Penh',
      sangkatCommune: json['sangkat_commune'],
      streetAddress: json['street_address'] ?? '',
      electricityCostPerKwh: json['electricity_cost_per_kwh'] != null
          ? double.tryParse(json['electricity_cost_per_kwh'].toString())
          : 0.25,
      waterCostPerM3: json['water_cost_per_m3'] != null
          ? double.tryParse(json['water_cost_per_m3'].toString())
          : 0.40,
      wifiIncluded: json['wifi_included'] == true || json['wifi_included'] == 1,
      parkingAvailable: json['parking_available'] == true || json['parking_available'] == 1,
      petFriendly: json['pet_friendly'] == true || json['pet_friendly'] == 1,
      furnishedStatus: json['furnished_status'] ?? 'semi_furnished',
      status: json['status'] ?? 'available',
      isFeatured: json['is_featured'] == true || json['is_featured'] == 1,
      viewsCount: json['views_count'] ?? 0,
      aiCompletenessScore: json['ai_completeness_score'] ?? 85,
      aiSummary: json['ai_summary'],
      primaryImageUrl: primaryUrl,
      images: parsedImages,
      amenities: parsedAmenities,
      landlord: json['landlord'] != null ? UserModel.fromJson(json['landlord']) : null,
      isFavorited: json['is_favorited'] == true,
    );
  }
}

class PropertyImageModel {
  final int id;
  final String imageUrl;
  final bool isPrimary;
  final String? caption;

  PropertyImageModel({
    required this.id,
    required this.imageUrl,
    required this.isPrimary,
    this.caption,
  });

  factory PropertyImageModel.fromJson(Map<String, dynamic> json) {
    return PropertyImageModel(
      id: json['id'] ?? 0,
      imageUrl: json['url'] ?? json['image_path'] ?? '',
      isPrimary: json['is_primary'] == true || json['is_primary'] == 1,
      caption: json['caption'],
    );
  }
}
