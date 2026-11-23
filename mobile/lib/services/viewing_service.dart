import '../constants/api_constants.dart';
import '../models/viewing_request_model.dart';
import 'api_service.dart';

class ViewingService {
  final ApiService _api = ApiService();

  Future<List<ViewingRequestModel>> getViewingRequests() async {
    final res = await _api.get(ApiConstants.viewingRequests);
    final data = res['data'] as List? ?? [];
    return data.map((json) => ViewingRequestModel.fromJson(json)).toList();
  }

  Future<ViewingRequestModel> requestViewing({
    required int propertyId,
    required String preferredDate,
    required String preferredTime,
    required String contactPhone,
    String? renterNote,
  }) async {
    final res = await _api.post(ApiConstants.viewingRequests, {
      'property_id': propertyId,
      'preferred_date': preferredDate,
      'preferred_time': preferredTime,
      'contact_phone': contactPhone,
      'renter_note': renterNote,
    });

    return ViewingRequestModel.fromJson(res['data']);
  }
}
