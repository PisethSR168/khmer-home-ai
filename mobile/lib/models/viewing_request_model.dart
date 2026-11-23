import 'property_model.dart';
import 'user_model.dart';

class ViewingRequestModel {
  final int id;
  final int userId;
  final int propertyId;
  final int landlordId;
  final String preferredDate;
  final String preferredTime;
  final String status;
  final String contactPhone;
  final String? renterNote;
  final String? landlordResponse;
  final PropertyModel? property;
  final UserModel? user;
  final UserModel? landlord;

  ViewingRequestModel({
    required this.id,
    required this.userId,
    required this.propertyId,
    required this.landlordId,
    required this.preferredDate,
    required this.preferredTime,
    required this.status,
    required this.contactPhone,
    this.renterNote,
    this.landlordResponse,
    this.property,
    this.user,
    this.landlord,
  });

  factory ViewingRequestModel.fromJson(Map<String, dynamic> json) {
    return ViewingRequestModel(
      id: json['id'] ?? 0,
      userId: json['user_id'] ?? 0,
      propertyId: json['property_id'] ?? 0,
      landlordId: json['landlord_id'] ?? 0,
      preferredDate: json['preferred_date'] ?? '',
      preferredTime: json['preferred_time'] ?? '',
      status: json['status'] ?? 'pending',
      contactPhone: json['contact_phone'] ?? '',
      renterNote: json['renter_note'],
      landlordResponse: json['landlord_response'],
      property: json['property'] != null ? PropertyModel.fromJson(json['property']) : null,
      user: json['user'] != null ? UserModel.fromJson(json['user']) : null,
      landlord: json['landlord'] != null ? UserModel.fromJson(json['landlord']) : null,
    );
  }
}
