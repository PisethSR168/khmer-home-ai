import '../constants/api_constants.dart';
import '../models/property_model.dart';
import 'api_service.dart';

class AiService {
  final ApiService _api = ApiService();

  Future<Map<String, dynamic>> smartSearch(String naturalQuery) async {
    final res = await _api.post(ApiConstants.aiSmartSearch, {
      'query': naturalQuery,
    });

    final data = res['data'];
    final rawProperties = data['properties'] as List? ?? [];
    final properties = rawProperties.map((p) => PropertyModel.fromJson(p)).toList();

    return {
      'query': data['query'],
      'explanation': data['explanation'],
      'extracted_criteria': data['extracted_criteria'],
      'total_matches': data['total_matches'],
      'properties': properties,
    };
  }

  Future<Map<String, dynamic>> chatWithAssistant(String message, {List<dynamic>? history}) async {
    final res = await _api.post(ApiConstants.aiRentalAssistant, {
      'message': message,
      'history': history ?? [],
    });

    return res['data'];
  }

  Future<List<PropertyModel>> getRecommendations({int? propertyId}) async {
    final uri = Uri.parse(ApiConstants.aiRecommendations).replace(
      queryParameters: propertyId != null ? {'property_id': propertyId.toString()} : null,
    );
    final res = await _api.get(uri.toString());

    final rawRecs = res['data']['recommendations'] as List? ?? [];
    return rawRecs.map((r) => PropertyModel.fromJson(r)).toList();
  }
}
